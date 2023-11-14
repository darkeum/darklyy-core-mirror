<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Inventory\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;
use Inventory\Models\Inventory;

class InventoryGrid extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'inventory';

    protected $opts_type = "open";

    protected function init()
    {
        $this->title = __('inventory::general.list');

        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        ];

        $this->db = DB::table('inventory')->where("status", '<>', 0);

        $this->cols = [

            'id' => [
                'db' => 'id',
                'visible' => false
            ],

            'numb' => [
                'title' => '№',
                'width' => '1%',
                'db' => 'id',
                'search_op' => 'like'
            ],

            'serial_number' => [
                'title' => __('inventory::general.serial'),
                'width' => '3%',
                'render' => 'text',
                'db' => 'serial_number',
                'null' => __('grid.null'),
                'search_op' => 'like'
            ],

            'category' => [
                'title' => __('inventory::general.category'),
                'db' => 'category',
                'width' => '5%',
                'render' => 'text',
                'options' => [
                    'value' => $this->setValue($this->getCat(),  __('grid.all')),
                ],
                'replace' => $this->getCat(),
                'encode' => false,
                'type' => 'select',
                'null' => __('grid.null'),
                'search_op' => 'equal',
            ],

            'name' => [
                'title' => __('inventory::general.name'),
                'width' => '5%',
                'render' => 'text',
                'db' => 'name',
                'null' => __('grid.null'),
                'search_op' => 'like'
            ],

            'org' => [
                'title' => __('inventory::general.org'),
                'db' => 'org',
                'width' => '8%',
                'encode' => false,
                'null' => __('grid.null'),
                'options' => [
                    'value' => $this->setValue($this->Org(),  __('grid.all')),
                ],
                'replace' => $this->Org(),
                'type' => 'select',
                'search_op' => 'equal',
            ],

            'location' => [
                'title' => __('inventory::general.location'),
                'db' => 'location',
                'width' => '10%',
                'encode' => false,
                'options' => [
                    'value' => $this->setValue($this->getLoc(), __('grid.all')),
                ],
                'replace' => $this->getLoc(),
                'type' => 'select',
                'search_op' => 'equal',
            ],

            'wid' => [
                'title' => __('inventory::general.wid'),
                'db' => 'wid',
                'width' => '10%',
                'encode' => false,
                'options' => [
                    'value' => $this->setValue($this->getWic(),  __('grid.all')),
                ],
                'replace' => $this->getWic(),
                'type' => 'select',
                'search_op' => 'equal',
            ],

            'date' => [
                'title' => __('inventory::general.date'),
                'db' => 'date',
                'type' => 'date_range',
                'format_date' => "d.m.Y",
                'width' => '1%',
                'search_op' => 'date_range',
            ],

            'ab' => [
                'type' => 'actions',
                'width' => '2%',
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

    protected function Org()
    {
        $rows = [];
        $result = Inventory::with('organizations')->get();
        foreach ($result as $org) {
            $rows[$org->organizations->id] = $org->organizations->name;
        }
        return $rows;
    }


    protected function getCat()
    {
        $rows = [];
        $result = Inventory::with('categories')->get();
        foreach ($result as $cat) {
            $rows[$cat->categories->id] = $cat->categories->name;
        }
        return $rows;
    }

    protected function getLoc()
    {
        $rows = [];
        $result = Inventory::with('locations')->get();
        foreach ($result as $loc) {
            $rows[$loc->locations->id] = $loc->locations->name;
        }
        return $rows;
    }

    protected function getWic()
    {
        $rows = [];
        $result = Inventory::with('users')->get();
        foreach ($result as $user) {
            if(!empty($user->users)){
                $rows[$user->users->id] = shortUserName($user->users->id);
            }           
        }
        return $rows;
    }

    protected function parseRow($r)
    {
        $r['numb'] = $r['id'];
        $r['ab'] = (string)view('inventory::manager.grid.action', [
            'id' => $r['id'],
            'edit' => route('admin.inventory.edit', $r['id']),
        ]);
        return $r;
    }
}
