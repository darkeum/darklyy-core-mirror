<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Stocks\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;
use Organizations\Model\Organizations;

class StocksGrid extends GridModern
{
    protected $db_type = 'db';
    private $orgs;

    protected function init()
    {

        $this->title = __('stocks::general.index');

        $this->options = [
            'sort' => [
                'name' => 'numb',
                'order' => 'DESC'
            ],
        ];

        $this->db = DB::table('stock as s');

        $org = DB::table('org')
            ->whereIn('id', DB::table('stock')->pluck('org')->unique())
            ->get();


        $this->orgs = $this->getFilterParam($org)->transform(function ($o) {
            return Organizations::find($o->oid)->name;
        });

        $this->cols = [

            'id' => [
                'visible' => false,
                'db' => 's.id',
            ],

            'numb' => [
                'db' => 's.id',
                'title' => '№',
                'width' => '5%',
                'className' => 'text-center',
                'search_op' => 'like'
            ],

            'title' => [
                'db' => 's.name',
                'title' => __('stocks::general.stock.name'),
                'width' => "30%",
                'encode' => false,
                'replace' => $this->getStocksName(),
                'options' => [
                    'value' => $this->setValue($this->getStocksName(), __('grid.all')),
                ],
                'search_op' => 'equal',
                'type' => 'select',
                'null' => __('grid.null'),
            ],

            'org' => [
                'title' => __('stocks::general.org'),
                'null' => __('grid.null'),
                'db' => 's.org',
                'width' => "40%",
                'encode' => false,
                'sortable' => false,
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->orgs->toArray(), __('grid.all')),
                ],
                'search_op' => 'equal',
            ],

            'action' => [
                'type' => 'actions',
                'className' => 'text-center',
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

    protected function getStocksName()
    {

        $result = $this->db->get();
        foreach ($result as $res) {
            $arr[$res->name] = $res->name;
        }
        return $arr;
    }

    protected function parseRow($row)
    {
        $row['org'] = $this->orgs[$row['org']];
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'stocks',
            'actions' => [
                'show' => true,
                'edit' => true,
            ],
        ])->render();

        return $row;
    }
}
