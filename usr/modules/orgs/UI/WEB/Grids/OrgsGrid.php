<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Orgs\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;
use Organizations\Model\Organizations;

class OrgsGrid extends GridModern
{
    protected $db_type = 'db';

    private $orgs;

    protected function init()
    {
        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        ];

        $this->title = __('orgs::general.list');

        $this->db = DB::table('org');

        $this->orgs = $this->getFilterParam($this->db->get())->transform(function ($o) {
            return Organizations::find($o->oid)->name;
        });

        $this->cols = [

            'id' => [
                'visible' => false,
                'db' => 'org.id',
            ],

            'numb' => [
                'title' => '№',
                'width' => "2%",
                'db' => 'org.id',
                'type' => 'input',
                'className' => 'text-center',
                'search_op' => 'like',
            ],

            'prefix' => [
                'db' => 'org.prefix',
                'title' => __('orgs::general.prefix'),
                'width' => "10%",
                'null' => __('grid.null'),
                'search_op' => 'like',
                'className' => 'text-center',
            ],

            'org' => [
                'title' => __('orgs::general.title'),
                'null' => __('grid.null'),
                'db' => 'org.id',
                'width' => "30%",
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

    protected function parseRow($row)
    {
        $row["numb"] = $row["id"];
        $row['org'] = $this->orgs[$row['org']];

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'orgs',
            'actions' => [
                'edit' => true,
                'show' => true
            ],
        ])->render();

        return $row;
    }
}
