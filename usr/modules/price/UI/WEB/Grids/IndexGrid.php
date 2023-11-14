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
use Price\Models\CatalogPriceType;

class IndexGrid extends GridModern
{
    protected $namespace = 'catalog_price';

    protected $db_type = 'db';

    protected function init()
    {
        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        ];

        $this->title = __('price::main.list');

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
                'className' => 'text-center px-1',
                'search_op' => 'like',
            ],

            'uid' => [
                'title' => __('grid.uid'),
                'db' => $this->namespace . '.uid',
                'width' => "5%",
                'className' => 'text-center px-1',
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid('catalog_price'),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getUid('catalog_price'), __('grid.all')),
                ],
                'search_op' => 'equal',
            ],

            'date' => [
                'title' => __('price::main.grid.date'),
                'db' => $this->namespace . '.date',
                'width' => "5%",
                'className' => 'text-center px-1',
                'type' => 'date_range',
                'search_op' => 'date_range',
            ],

            'price_type' => [
                'title' => __('price::main.grid.type'),
                'db' => $this->namespace . '.price_type',
                'width' => "5%",
                'className' => 'text-center px-1',
                'encode' => false,
                'replace' => $this->getType(),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getType(), __('grid.all')),
                ],
                'search_op' => 'equal',
            ],

            'comment' => [
                'title' => __('price::main.grid.comment'),
                'db' => $this->namespace . '.comment',
                'width' => '15%',
				'encode' => false,
				'render' => 'text',
				'search_op' => 'like',
            ],

            'active' => [
                'title' => __('price::main.grid.active'),
                'db' => $this->namespace . '.active',
                'encode' => false,
                'replace' => $this->getActive(),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getActive(true), __('grid.all')),
                ],
                'search_op' => 'equal',
                'width' => '5%',
                'className' => 'text-center px-1',
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

    protected function getActive($select = false)
    {
        $rows_select = [];
        $rows_select[0] =  __('price::main.grid.active.off');
        $rows_select[1] =  __('price::main.grid.active.on');
        $rows = [];
        $rows[0] = "<span class='badge rounded-pill badge-light-danger d-block' data-toggle='tooltip'>$rows_select[0]</span>";
        $rows[1] = "<span class='badge rounded-pill badge-light-success d-block' data-toggle='tooltip'>$rows_select[1]</span>";
        return ($select ?  $rows_select :  $rows);
    }

    protected function getType()
    {
        $rows_select = [];
        $price_types = CatalogPriceType::all();
        foreach ($price_types as $value) {
            $rows_select[$value->id] = $value->title;
        }
        return $rows_select;
    }

    protected function parseRow($row)
    {
        $row["numb"] = $row["id"];
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'price',
            'actions' => [
                'show' => true,
            ],
        ])->render();

        return $row;
    }
}
