<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Job\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;
use Boot\System\Categories\Categories;

class IndexGrid extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'job';

    protected function init()
    {
        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        ];

        $this->title = __('job::main.list');

        $this->db = DB::table($this->namespace)->where('deleted_at',NULL);

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

            'title' => [
                'title' => __('job::main.grid.title'),
                'db' => $this->namespace . '.title',
                'width' => '15%',
                //'className' => 'text-center px-1',
                'render' => 'text',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ],

            'description' => [
                'title' =>  __('job::main.grid.description'),
                'db' => $this->namespace . '.description',
                'width' => '15%',
                //'className' => 'text-center px-1',
                'render' => 'text',
                'null' => __('grid.null'),

                'search_op' => 'like'
            ],

            'uid' => [
                'title' => __('job::main.grid.uid'),
                'db' => $this->namespace . '.uid',
                'width' => "10%",
                'className' => 'text-center px-1',
                'replace' => $this->getUid('job'),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getUid('job'), __('grid.all')),
                ],

                'encode' => false,
                'sortable' => false,
                'search_op' => 'equal',
            ],

            'created_at' => [
                'title' => __('job::main.grid.created_at'),
                'db' => $this->namespace . '.created_at',
                'width' => '10%',
                'className' => 'text-center',
                'type' => 'date_range',

                'search_op' => 'date_range'
            ],

            'updated_at' => [
                'title' => __('job::main.grid.updated_at'),
                'db' => $this->namespace . '.updated_at',
                'width' => '10%',
                'className' => 'text-center',
                'type' => 'date_range',

                'search_op' => 'date_range'
            ],

            'category' => [
                'title' => __('job::main.grid.category'),
                'db' => $this->namespace . '.category',
                'width' => '5%',
                'className' => 'text-center',
                'replace' => $this->getCategoryTypes(),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getCategoryTypes(), __('grid.all')),
                ],

                'encode' => false,
                'search_op' => 'equal',
            ],

            'status' => [
                'title' => __('job::main.grid.status'),
                'db' => $this->namespace . '.status',
                'width' => '5%',
                'className' => 'text-center px-1',
                'replace' => $this->getStatus(),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getStatus(true), __('grid.all')),
                ],

                'encode' => false,
                'search_op' => 'equal',
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

    protected function getCategoryTypes()
    {
        $categories = new Categories;
        $rows = [];
        foreach ($categories->getModuleCategories('job') as $value) {
            $rows[$value['id']] = $value['name'];
        }
        return $rows;
    }

    protected function getStatus($select = false)
    {
        $rows_select = [];
        $rows_select[0] =  __('job::main.grid.status.off');
        $rows_select[1] =  __('job::main.grid.status.on');
        $rows = [];
        $rows[0] = "<span class='badge rounded-pill badge-light-danger d-block' data-toggle='tooltip'>$rows_select[0]</span>";
        $rows[1] = "<span class='badge rounded-pill badge-light-success d-block' data-toggle='tooltip'>$rows_select[1]</span>";
        return ($select ?  $rows_select :  $rows);
    }

    protected function parseRow($row)
    {
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'job',
            'actions' => [
                'show' => true,
                'edit' => true,
                'destroy' => true,
            ],
        ])->render();
        return $row;
    }
}
