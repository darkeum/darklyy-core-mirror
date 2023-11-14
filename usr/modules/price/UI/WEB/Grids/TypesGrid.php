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

class TypesGrid extends GridModern
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->options = [
            'sort' => [
                'name' => 'cpt.id',
                'order' => 'DESC'
            ],
        ];

        $this->title = __('price::types.list');

        $this->db = DB::table('catalog_price_type as cpt')->leftJoin("catalog_price_type as cpt2", "cpt.link_id", "=", "cpt2.id");

        $this->cols = [

            'id' => [
                'visible' => false,
                'db' => 'cpt.id',
            ],

            'numb' => [
                'title' => '№',
                'db' => 'cpt.id',
                'width' => '1%',
                'className' => 'text-center',
                'search_op' => 'like'
            ],

            'name' => [
                'title' => __('price::types.grid.title'),
                'db' => 'cpt.title',
                'null' => __('grid.null'),
                'search_op' => 'like'
            ],

            'link' => [
                'title' => __('price::types.grid.relation'),
                'db' => 'cpt2.title',
                'width' => '5%',
                'null' => __('grid.null'),
                'search_op' => 'like'
            ],

            'currency' => [
                'title' => __('price::types.grid.currency'),
                'db' => 'cpt.currency',
                'width' => "1%",
                'className' => 'text-center px-1',
                'null' => __('grid.null'),
                'search_op' => 'like'
            ],

            'course' => [
                'title' => __('price::types.grid.course'),
                'db' => 'cpt.course',
                'width' => '1%',
                'className' => 'text-center px-1',
                'null' => __('grid.null'),
                'search_op' => 'like'
            ],

            'course_auto' => [
                'title' => __('price::types.grid.course.auto'),
                'db' => 'cpt.course_auto',
                'width' => '5%',
                'className' => 'text-center px-1',
                'encode' => false,
                'replace' => $this->getCourseAuto(),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getCourseAuto(), __('grid.all')),
                ],
                'search_op' => 'equal',
            ],

            'type' => [
                'title' => __('price::types.grid.type'),
                'db' => 'cpt.type',
                'width' => '10%',
                'className' => 'text-center px-1',
                'encode' => false,
                'replace' => $this->getType(),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getType(), __('grid.all')),
                ],
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

    protected function getCourseAuto($select = false)
    {

        $rows_select = [];
        $rows_select[0] =  __('price::types.grid.course.auto.off');
        $rows_select[1] =  __('price::types.grid.course.auto.on');
        $rows = [];
        $rows[0] = "<span class='badge rounded-pill badge-light-danger d-block' data-toggle='tooltip'>$rows_select[0]</span>";
        $rows[1] = "<span class='badge rounded-pill badge-light-success d-block' data-toggle='tooltip'>$rows_select[1]</span>";
        return ($select ?  $rows_select :  $rows);
    }

    protected function getType()
    {
        return CatalogPriceType::getType();
    }

    protected function parseRow($row)
    {
        $row["numb"] = $row["id"];
        if ($row["currency"] != '643') {
            $row['action'] = view('grid::layouts.actions', [
                'row' => $row,
                'module' => 'price.types',
                'actions' => [
                    'edit' => true,
                ],
            ])->render();
        } else {
            $row['action'] = '';
        }
        return $row;
    }
}
