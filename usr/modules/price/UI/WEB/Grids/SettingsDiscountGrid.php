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
use Price\Models\CatalogPriceRival;

class SettingsDiscountGrid extends GridModern
{
	protected $namespace = 'catalog';

	protected $namespace_price = 'catalog_price_rival';

	protected $namespace_discount = 'catalog_price_rival_discount';


	protected $db_type = 'db';

	protected function init()
	{
		$this->options = [
			'sort' => [
				'name' => 'id',
				'order' => 'DESC'
			],
		];

		$this->title = __('price::settings.catalog.list');

		$this->db = DB::table($this->namespace)
			->leftJoin($this->namespace_price, $this->namespace . ".id", "=", $this->namespace_price . ".id")
			->leftJoin($this->namespace_discount, $this->namespace . ".id", "=", $this->namespace_discount . ".cid")
			->where('hidden', 0);

		$this->cols = [

			'id' => [
				'visible' => false,
				'db' => $this->namespace . '.id',
			],	

			'cprd_id' => [
				'visible' => false,
				'db' => $this->namespace_discount . '.id',
			],	

			'numb' => [
				'db' => $this->namespace . '.id',
				'title' => '№',
				'width' => '5%',
				'className' => 'text-center',
				'search_op' => 'like'
			],

			'sku' => [
				'title' => __('price::settings.catalog.grid.sku'),
				'db' => $this->namespace . '.sku',
				'width' => '10%',
				'className' => 'text-center',
				'encode' => false,
				'render' => 'text',
				'search_op' => 'like',
			],

			'title' => [
				'title' => __('price::settings.catalog.grid.title'),
				'db' => $this->namespace . '.title',
				'encode' => false,
				'width' => '15%',
				'render' => 'text',
				'search_op' => 'like',
			],

			'type' => [
				'title' => __('price::settings.catalog.grid.type'),
				'db' => $this->namespace_price . '.type',
				'width' => '5%',
                'className' => 'text-center',
				'replace' => $this->getTypeTypes(),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getTypeTypes(), __('grid.all')),
				],
				'encode' => false,
				'search_op' => 'equal',
			],
			
			'ci' => [
				'title' => __('price::settings.catalog.grid.status'),
				'width' => '5%',
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,	
				'search' => false,
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

	protected function getTypeTypes()
	{
		return CatalogPriceRival::getType();
	}

	protected function parseRow($row)
	{
		$row["ci"] = empty($row["cprd_id"]) ? '<i class="far fa-circle text-warning"></i>' : '<i class="far fa-circle text-success"></i>';
		$row['action'] = view('grid::layouts.actions', [
			'row' => $row,
			'module' => 'price.settings.discount',
			'actions' => [
				'edit' => true,
			],
		])->render();

		return $row;
	}
}
