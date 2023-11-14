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

class ScadaActionsGrid extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'scada_actions';

    protected $namespace_second = 'scada_tags';

    protected $namespace_third = 'scada';

    protected function init()
    {
        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );

        $this->title = __('scada::actions.list');

        $this->db = DB::table($this->namespace)->leftJoin($this->namespace_second, $this->namespace_second . ".id", "=", $this->namespace . ".tag_id")->leftJoin($this->namespace_third, $this->namespace_third . ".id", "=", $this->namespace_second . ".scada_id");

        $this->cols = array(

            'id' => array(
                'visible' => false,
                'db' => $this->namespace . '.id',
            ),

            'numb' => array(
                'title' => '№',
                'db' => $this->namespace . '.id',
                'width' => '1%',
                'className' => 'text-center px-1',

                'search_op' => 'like'
            ),

            'tag_id' => array(
                'title' => __('scada::actions.grid.tag'),
                'db' => $this->namespace . '.tag_id',
                'width' => '1%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            'tag_title' => array(
                'title' =>  __('scada::actions.grid.title.tag'),
                'db' => $this->namespace_second . '.title',
                'width' => '5%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            'scada_title' => array(
                'title' =>  __('scada::actions.grid.title.scada'),
                'db' => $this->namespace_third . '.title',
                'width' => '10%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            'value' => array(
                'title' =>  __('scada::actions.grid.value'),
                'db' => $this->namespace . '.value',
                'width' => '5%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ),

            'uid' => array(
                'title' => __('scada::actions.grid.uid'),
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
       
            'created_at' => [
                'title' => __('scada::actions.grid.created'),
                'db' => $this->namespace . '.created_at',
                'type' => 'date_range',
                'width' => '5%',
                'className' => 'text-center px-1',

                'search_op' => 'date_range'
            ],            

            'status' => array(
                'title' => __('scada::actions.grid.status'),
                'db' => $this->namespace . '.status',
                'width' => '1%',
                'className' => 'text-center px-1',
                'encode' => false,
                'replace' => $this->getStatus(),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getStatus(true), __('grid.all')),
                ),
                'null' => __('grid.null'),

                'search_op' => 'equal',
            ),

            //'action' => [
            //    'type' => 'actions',
            //    'width' => '1%',
            //    'className' => 'text-center px-1',
            //    'manual' => true,
            //    'encode' => false,
            //    'sortable' => false,
            //    'search' => false,
            //    'excel_hidden' => true,
            //
            //    'search_op' => false,
            //]
        );
        $this->render_filter_toolbar = true;
    }

    protected function getStatus($select = false)
    {
        $rows_select = [];
        $rows_select[0] =  __('scada::actions.grid.status.off');
        $rows_select[1] =  __('scada::actions.grid.status.on');
        $rows = [];
        $rows[0] = "<span class='badge rounded-pill badge-light-danger d-block' data-toggle='tooltip'>$rows_select[0]</span>";
        $rows[1] = "<span class='badge rounded-pill badge-light-success d-block' data-toggle='tooltip'>$rows_select[1]</span>";
        return ($select ?  $rows_select :  $rows);
    }

    protected function parseRow($row)
    {
        return $row;
    }
}
