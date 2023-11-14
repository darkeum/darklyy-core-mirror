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

class Cashbox extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'cashbox';

    protected function init()
    {
        $this->title = __('cashbox::main.list');

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
                'title' => __('cashbox::main.grid.numb'),
                'label' => '#',
                'width' => '5%',
                'className' => 'text-center px-1',
                'search_op' => 'like'
            ),

            'title' => array(
                'db' => $this->namespace . '.title',
                'title' => __('cashbox::main.grid.title'),
                'label' => __('cashbox::main.grid.title'),
                'width' => 30,
                'hidden' => false,
                'null' => __('grid.not_specified'),
                'search_op' => 'like'
            ),

            'address' => array(
                'db' => $this->namespace . '.address',
                'title' => __('cashbox::main.grid.address'),
                'label' => __('cashbox::main.grid.address'),
                'width' => 30,
                'hidden' => false,
                'null' => __('grid.not_specified'),
                'search_op' => 'like'
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

    protected function parseRow($row)
    {
        $row["numb"] = $row["id"];
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => $this->namespace,
            'actions' => [
                'show' => true,
            ],
        ])->render();
        return $row;
    }
}
