<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Price\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class ClientGrid extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'organizations';

    protected function init()
    {
        $this->options = [
            'sort' => [
                'name' => 'cpt.id',
                'order' => 'DESC'
            ],
        ];

        $this->title = __('price::rival.list');

        $this->db = DB::table($this->namespace);

        $this->cols = [

            'id' => [
                'visible' => false,
                'db' => $this->namespace . '.id',
            ],

            'numb' => [
                'title' => '№',
                'db' => $this->namespace . '.id',
                'width' => '1%',
                'className' => 'text-center',
                'search_op' => 'like'
            ],

            'name' => [
                'title' => __('price::rival.grid.name'),
                'db' => $this->namespace . '.name',
                'null' => __('grid.null'),
                'search_op' => 'like'
            ],

            'inn' => [
                'title' => __('price::rival.grid.inn'),
                'db' => $this->namespace . '.inn',
                'null' => __('grid.null'),
                'search_op' => 'like'
            ],

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
        ];
        $this->render_filter_toolbar = true;
    }

    protected function parseRow($row)
    {
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'price.rival.client',
            'actions' => [
                'edit' => true,
            ],
        ])->render();

        return $row;
    }
}
