<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class IndexGrid extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'scada';

    protected function init()
    {
        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );
        $this->title = __('scada::main.list');

        $this->db = DB::table($this->namespace);

        $this->cols = array(

            'id' => array(
                'db' => $this->namespace . '.id',
                'visible' => false,
            ),

            'numb' => array(
                'title' => '№',
                'db' => $this->namespace . '.id',
                'width' => '3%',
                'search_op' => 'like'
            ),

            'name' => array(
                'title' => __('scada::main.grid.title'),
                'db' => $this->namespace . '.title',
                'width' => '15%',
                'render' => 'text',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            'address' => array(
                'title' =>  __('scada::main.grid.address'),
                'db' => $this->namespace . '.address',
                'width' => '15%',
                'render' => 'text',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            # IP Адрес
            'ip' => array(
                'title' =>  __('scada::main.grid.ip'),
                'db' => $this->namespace . '.ip',
                'width' => '10%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            # Порт
            'port' => array(
                'title' =>  __('scada::main.grid.port'),
                'db' => $this->namespace . '.port',
                'width' => '10%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            'uid' => array(
                'title' => __('scada::main.grid.uid'),
                'db' => $this->namespace . '.uid',
                'width' => "5%",
                'className' => 'text-center px-1',
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid('catalog_price'),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getUid('catalog_price'), __('grid.all')),
                ),

                'search_op' => 'equal',
            ),

            'polling_at' => [
                'title' => __('scada::main.grid.polling'),
                'db' => $this->namespace . '.polling_at',
                'type' => 'date_range',
                'width' => '10%',
                'className' => 'text-center px-1',

                'search_op' => 'date_range'
            ],

            'active' => array(
                'title' => __('scada::main.grid.status'),
                'db' => $this->namespace . '.status',
                'width' => '5%',
                'className' => 'text-center px-1',
                'encode' => false,
                'replace' => $this->getStatus(),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getStatus(true), __('grid.all')),
                ),

                'search_op' => 'equal',
            ),

            'action' => array(
                'type' => 'actions',
                'width' => '1%',
                'className' => 'text-center px-1',

                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,

                // query sql
                'search_op' => false,
            ),
        );
        $this->render_filter_toolbar = true;
    }

    protected function getStatus($select = false)
    {
        $rows_select = [];
        $rows_select[0] =  __('scada::main.grid.status.off');
        $rows_select[1] =  __('scada::main.grid.status.on');
        $rows = [];
        $rows[0] = "<span class='badge rounded-pill badge-light-danger d-block' data-toggle='tooltip'>$rows_select[0]</span>";
        $rows[1] = "<span class='badge rounded-pill badge-light-success d-block' data-toggle='tooltip'>$rows_select[1]</span>";
        return ($select ?  $rows_select :  $rows);
    }

    protected function parseRow($row)
    {
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'scada',
            'actions' => [
                'edit' => true,
                'show' => true,
            ],
        ])->render();
        return $row;
    }
}
