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

use Boot\Models\Categories;
use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;
use Boot\Support\Facades\Request;
use Organizations\Model\Organizations;
use Catalog_v2\Model\CatalogManufacture;
use Price\UI\CLI\GenerateTypes\PriceGenerateType1;

class ClientEditGrid extends GridModern
{

    protected $db_type = 'db';

    protected $namespace = 'catalog';
    protected $namespace_manufacture = 'catalog_manufacture';
    protected $namespace_discount = 'catalog_price_discount_organizations';

    protected function init()
    {
        $this->options = [
            'sort' => [
                'name' => 'cpt.id',
                'order' => 'DESC'
            ],
        ];

       

        $oid = Request::session()->get('price.rival.client.edit');
        $organization = Organizations::find($oid);

        $this->title = __('price::rival.list.catalog', ['title' => $organization->name]);

        $this->db = DB::table($this->namespace)
            ->leftJoin($this->namespace_manufacture, $this->namespace . ".man", "=", $this->namespace_manufacture . ".id")
            ->leftJoin($this->namespace_discount, function ($join) use ($oid) {
                $join->on($this->namespace . ".id", "=", $this->namespace_discount . ".cid")->where($this->namespace_discount . ".oid", '=', $oid);
            })
            ->where($this->namespace . '.hidden', '0');


        $this->cols = [

            'id' => [
                'visible' => false,
                'db' => $this->namespace . '.id',
            ],

            'type_id' => [
                'visible' => false,
                'db' => $this->namespace_discount . '.id',
            ],

            'numb' => [
                'title' => '№',
                'db' => $this->namespace . '.id',
                'width' => '1%',
                'className' => 'text-center',
                'search_op' => 'like'
            ],

            'sku' => [
                'title' => __('price::rival.catalog.grid.sku'),
                'db' => $this->namespace . '.sku',
                'width' => '15%',
				'render' => 'text',
                'search_op' => 'like'
            ],

            'title' => [
                'title' => __('price::rival.catalog.grid.title'),
                'db' => $this->namespace . '.title',
                'width' => '15%',
				'render' => 'text',
                'search_op' => 'like'
            ],

            'manufacture' => [
				'title' => __('price::rival.catalog.grid.manufacture'),
				'db' => $this->namespace . '.man',
				'width' => '5%',
				'className' => 'text-center',
				'replace' => $this->getManufactureTypes(),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getManufactureTypes(), __('grid.all')),
				],
				'encode' => false,
				'search_op' => 'equal',
			],
					
			'category' => [
				'title' => __('price::rival.catalog.grid.category'),
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
	
			'oid_type' => [
				'title' => __('price::rival.catalog.grid.type'),
				'db' => $this->namespace_discount . '.type',
				'width' => '5%',
				'className' => 'text-center',
				'replace' => $this->getOidTypes(),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getOidTypes(), __('grid.all')),
				],
				'encode' => false,
				'search_op' => 'equal',
			],

			'auto' => [
				'title' => __('price::rival.catalog.grid.auto'),
				'db' => $this->namespace_discount . '.auto',
				'width' => '5%',
				'className' => 'text-center',
				'replace' => $this->getAutoTypes(),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getAutoTypes(), __('grid.all')),
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
        $result = Categories::where('module', 'catalog_v2')->orderBy('name', 'ASC')->get();
        $rows = [];
        $rows[''] = 'Без категории';

        foreach ($result as $r) {
            $rows[',' . $r->id . ','] = $r->name;
        }
        return $rows;
    }

    protected function getOidTypes()
    {
        $p = new PriceGenerateType1;
        $list = $p->get_discount_name_list_rus();
        $new_list = [];
        foreach ($list as $key => $value) {
            $new_list[$key] = "{$value} ({$key})";
        }
        return $new_list;
    }

    protected function getAutoTypes()
    {
        $new_list = [
            0 => __('price::rival.catalog.grid.auto.hand'),
            1 => __('price::rival.catalog.grid.auto.auto'),
        ];
        return $new_list;
    }

    protected function getManufactureTypes()
	{
		$result = CatalogManufacture::all();
		$rows[''] = __('grid.null');
		foreach ($result as $r) {
			$rows[$r->id] = $r->name;
		}
		return $rows;
	}

    protected function parseRow($row)
    {
        $row['action'] = view('price::manager.rival.actions', [
            'row' => $row
        ])->render();

        return $row;
    }
}
