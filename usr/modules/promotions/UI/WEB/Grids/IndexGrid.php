<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Promotions\UI\WEB\Grids;

use Boot\System\Grid\GridModern;
use \Boot\System\Database\DB;
use \Boot\Support\Facades\Translation;

class IndexGrid extends GridModern
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->table = 'promotions';
        $this->title = __('promotions::general.list');
        $this->namespace = 'promotions';

        $this->options = [
            'sort' => [
                'name' => 'numb',
                'order' => 'DESC'
            ],
        ];

        $this->db = DB::table($this->table)->where('deleted_at', null)->leftJoin('sales', function ($join) {
            $join->on('sales.id', '=', $this->table . '.sales_id');
        });


        $this->cols = array(

            'id' => array(

                'db' => $this->table . '.id',
                'visible' => false,
            ),

            'numb' => array(
                'db' => $this->table . '.id',
                'title' => '№',
                'width' => "1%",
                'search_op' => 'like'
            ),

            'sales_id' => array(
                'title' => "№ акции", //уточнить
                'db' => $this->table . '.sales_id',
                'width' => "2%",
                'className' => 'text-center',
            ),

            'title' => array(
                'db' => $this->table . '.title',
                'title' => __("promotions::general.grid.title"),
                'render' => 'text',
                'width' => '40%',
                'encode' => false,
                'null' => __("promotions::.general.grid.null"),
                'search_op' => 'like'
            ),

            'type' => array(
                'title' => __("promotions::general.grid.type"),
                'width' => "5%",
                'className' => 'text-center',
                'db' => $this->table . '.type',
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getTypes(),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getTypes(), __("promotions::general.grid.all")),
                ),
                'search_op' => 'equal',
            ),

            'sales_numb' => array(
                'db' => 'sales.numb',
                'title' => __("promotions::general.grid.sales"),
                'width' => "5%",
                'className' => 'text-center',
                'encode' => false,
                'null' => __("promotions::general.grid.null"),
                'search_op' => 'like'
            ),

            'uid' => array(
                'title' => __("promotions::general.grid.uid"),
                'db' => $this->table . '.uid',
                'width' => "5%",
                'className' => 'text-center',
                'encode' => false,
                'replace' => $this->getUid('reviews'),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getUid('reviews'), __("promotions::general.grid.all")),
                ),
                'search_op' => 'equal',
            ),

            'date_start' => array(
                'title' => __("promotions::general.grid.date_start"),
                'db' => $this->table . '.date_start',
                'width' => "3%",
                'className' => 'text-center',
                'type' => "date_range",
                'format_date' => "d.m.Y",
                'search_op' => 'date_range',
            ),

            'date_end' => array(
                'title' => __("promotions::general.grid.date_end"),
                'db' => $this->table . '.date_end',
                'width' => "3%",
                'className' => 'text-center',
                'type' => "date_range",
                'format_date' => "d.m.Y",
                'search_op' => 'date_range',
            ),

            'views' => array(
                'db' => $this->table . '.views',
                'title' => __("promotions::general.grid.views"),
                'width' => "1%",
                'render' => 'text',
                'className' => 'text-center',
                'encode' => false,
                'null' => __("promotions::general.grid.null"),
                'search_op' => 'like'
            ),

            'status' => array(
                'title' => __("promotions::general.grid.status"),
                'db' => $this->table . '.status',
                'width' => "1%",
                'className' => 'text-center',
                'encode' => false,
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getStatus()),
                ),
                'search_op' => 'equal',
            ),

            'action' => array(
                'type' => 'actions',
                // 'width' => "1%",
                // 'manual' => true,
                // 'encode' => false,
                // 'sortable' => false,
                // 'search_op' => false,
                // 'search' => false,
                // 'title' => false,
            ),
        );
        $this->render_filter_toolbar = true;
    }

    protected function getStatus()
    {
        $rows = [];

        $rows[0] = __("promotions::general.status.off");
        $rows[1] = __("promotions::general.status.on");

        return $rows;
    }

    protected function getTypes()
    {

        $rows = [];
        $rows[1] = __("promotions::general.types.sales");
        $rows[2] = __("promotions::general.types.custom");

        return $rows;
    }

    protected function parseRow($row)
    {
        $row["numb"] = $row["id"];

        if ($row['status'] == 0) {
            $row['status'] = '<i class="fa-regular fa-circle-xmark" style="color: #b41313;"></i>';
        } else {
            $row['status'] = '<i class="fa-regular fa-circle-check" style="color: #6ff104;" ></i>';
        }

        $row['action'] = (string)view('promotions::manager.grid.action', [
            'id' => $row['id'],
            'edit' => route('admin.promotions.edit', $row['id']),
            'showSale' => "/administration/module/sales/order/" . $row['sales_id'],
            'numbSale' => $row['sales_numb'] ? $row['sales_numb'] : false,
        ]);

        // $row['action'] = view('grid::layouts.actions', [
        //     'row' => $row,
        //     //'module' => $this->namespace,
        //     'module' => 'promotions',
        //     'actions' => [
        //         // 'show' => true,
        //         'edit' => route('admin.promotions.edit', $row['id']),
        //         // 'destroy' => true,
        //     ],
        // ])->render();

        return $row;
    }
}
