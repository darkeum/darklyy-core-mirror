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

class Cashier extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'cashier_shifts';

    protected function init()
    {
        $this->title = __('cashbox::cashier.list');

        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        ];

        $this->db = DB::table($this->namespace);

        $this->cols = array(

            'id' => array(
                'visible' => false,
                'db' => $this->namespace . '.id',
            ),

            'numb' => array(
                'db' => $this->namespace . '.id',
                'title' => __('cashbox::cashier.grid.numb'),
                'label' => '#',
                'width' => '5%',
                'className' => 'text-center px-1',
                'search_op' => 'like'
            ),

            'data_start' => array(
                'db' => $this->namespace . '.data_start',
                'title' => __('cashbox::cashier.grid.data.start'),
                'width' => '15%',
                'render' => 'text',
                'search_op' => 'like'
            ),
            'data_end' => array(
                'db' => $this->namespace . '.data_end',
                'title' => __('cashbox::cashier.grid.data.end'),
                'width' => '15%',
                'render' => 'text',
                'search_op' => 'like'
            ),

            'uid_start' => array(
                'title' => __('cashbox::cashier.grid.uid.start'),
                'db' => $this->namespace . '.uid_start',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getUid($this->namespace, 'uid_start'), __('grid.all')),
                ],
                'width' => '10%',
                'className' => 'text-center px-1',
                'encode' => false,
                'null' => __('grid.not_specified'),
                'sortable' => false,
                'replace' => $this->getUid($this->namespace, 'uid_start'),
                'search_op' => 'equal',
            ),
            'uid_end' => array(
                'title' => __('cashbox::cashier.grid.uid.end'),
                'db' => $this->namespace . '.uid_end',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getUid($this->namespace, 'uid_end'), __('grid.all')),
                ],
                'width' => '10%',
                'className' => 'text-center px-1',
                'encode' => false,
                'null' => __('grid.not_specified'),
                'sortable' => false,
                'replace' => $this->getUid($this->namespace, 'uid_end'),
                'search_op' => 'equal',
            ),

            'shift_number' => array(
                'db' => $this->namespace . '.shift_number',
                'label' => __('cashbox::cashier.grid.shift'),
                'title' => __('cashbox::cashier.grid.shift'),
                'width' => '10%',
                'null' => __('grid.not_specified'),
                'search_op' => 'like'
            ),

            'number_checks' => array(
                'db' => $this->namespace . '.number_checks',
                'label' => __('cashbox::cashier.grid.checks'),
                'title' => __('cashbox::cashier.grid.checks'),
                'width' => '10%',
                'null' => __('grid.not_specified'),
                'search_op' => 'like'
            ),

            'status' => array(
                'title' => __('cashbox::cashier.grid.status'),
                'db' => $this->namespace . '.status',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getStatus(),  __('grid.all')),
                ),
                'width' => '10%',
                'className' => 'text-center px-1',
                'encode' => false,
                'null' => __('grid.not_specified'),
                'sortable' => false,
                'replace' => $this->getStatus(),
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

    protected function getStatus()
    {
        $rows = [];
        $rows[1] = __('cashbox::cashier.grid.status.open');
        $rows[2] = __('cashbox::cashier.grid.status.close');
        return $rows;
    }


    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];
        return $r;
    }
}
