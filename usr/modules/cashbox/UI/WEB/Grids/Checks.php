<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Cashbox\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class Checks extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'cashbox_checks';

    protected $namespace_join = 'realization';

    protected function init()
    {
        $this->title = __('cashbox::checks.list');

        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        ];

        $this->db = DB::table($this->namespace)->leftJoin($this->namespace_join, $this->namespace_join . ".id", "=", $this->namespace . ".realization_id");

        $this->cols = array(

            'id' => array(
                'visible' => false,
                'db' => $this->namespace . '.id',
            ),

            'realization_id' => array(
                'visible' => false,
                'db' => $this->namespace . '.realization_id',
            ),

            'numb' => array(
                'db' => $this->namespace . '.id',
                'title' => __('cashbox::checks.grid.numb'),
                'label' => '#',
                'width' => '5%',
                'className' => 'text-center px-1',
                'search_op' => 'like'
            ),

            'created_at' => array(
                'db' => $this->namespace . '.created_at',
                'title' => __('cashbox::checks.grid.created_at'),
                'type' => 'date_range',
                'width' => '5%',
                'className' => 'text-center px-1',
                'search_op' => 'date_range'
            ),

            'r_numb' => array(
                'db' => $this->namespace_join . '.numb',
                'title' => __('cashbox::checks.grid.realization'),
                'label' => __('cashbox::checks.grid.realization'),
                'width' =>  '5%',
                'encode' => false,
                'null' => __('grid.not_specified'),
                'search_op' => 'like'
            ),

            'data' => array(
                'db' => $this->namespace . '.data',
                'title' => __('cashbox::checks.grid.data'),
                'width' => '15%',
                'render' => 'text',
                'search_op' => 'like'
            ),

            'uid' => array(
                'title' => __('grid.uid'),
                'db' => $this->namespace . '.uid',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getUid($this->namespace), __('grid.all')),
                ],
                'width' => '10%',
                'className' => 'text-center px-1',
                'encode' => false,
                'null' => __('grid.not_specified'),
                'sortable' => false,
                'replace' => $this->getUid($this->namespace),
                'search_op' => 'equal',
            ),

            'TypeCheck' => array(
                'title' => __('cashbox::checks.grid.type'),
                'db' => $this->namespace . '.TypeCheck',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getTypeCheck(),  __('grid.all')),
                ),
                'width' => '10%',
                'className' => 'text-center px-1',
                'encode' => false,
                'null' => __('grid.not_specified'),
                'sortable' => false,
                'replace' => $this->getTypeCheck(),
                'search_op' => 'equal',
            ),

            'action' => [
                'type' => 'actions',
                'width' => '1%',
                'className' => 'text-center px-1',
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,
                'search_op' => false,
            ]
        );
        $this->render_filter_toolbar = true;
    }

    protected function getTypeCheck()
    {
        $rows = [];
        $rows[0] = __('cashbox::checks.grid.type.sales');
        $rows[1] = __('cashbox::checks.grid.type.return');
        return $rows;
    }

    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];
        $r["r_numb"] = "<a href='/administration/module/realization/order/{$r["realization_id"]}' target='_blank'>{$r["r_numb"]}</a>";
        return $r;
    }
}
