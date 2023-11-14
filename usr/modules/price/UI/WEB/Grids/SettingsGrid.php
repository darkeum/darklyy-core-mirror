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
use Catalog_v2\Model\CatalogManufacture;

class SettingsGrid extends GridModern
{
	protected $namespace = 'catalog';

	protected $namespace_price = 'catalog_price_rival';

	protected $namespace_groups = 'catalog_groups';

	protected $namespace_manufacture = 'catalog_manufacture';

	protected $db_type = 'db';

	protected function init()
	{
		$this->options = [
			'sort' => [
				'name' => 'id',
				'order' => 'DESC'
			],
			'button' => view('price::manager.settings.actions')->render(),
		];

		$this->title = __('price::settings.catalog.list');

		$this->db = DB::table($this->namespace)
			->leftJoin($this->namespace_price, $this->namespace . ".id", "=", $this->namespace_price . ".id")
			->leftJoin($this->namespace_groups, $this->namespace . ".catalog_groups_id", "=", $this->namespace_groups . ".id")
			->leftJoin($this->namespace_manufacture, $this->namespace . ".man", "=", $this->namespace_manufacture . ".id")
			->where('hidden', 0);

		$this->cols = [

			'id' => [
				'visible' => false,
				'db' => $this->namespace . '.id',
			],

			'checkbox' => [
				'type' => 'checkbox',
				'width' => '1%',
				'className' => 'text-center px-1',
				'db' => $this->namespace . '.id',
				'encode' => false,
				'sortable' => false,
				'search' => false,
				'excel_hidden' => true,
				'search_op' => false,
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
				'width' => '7%',
				'className' => 'text-center',
				'encode' => false,
				'render' => 'text',
				'search_op' => 'like',
			],

			'type' => [
				'title' => __('price::settings.catalog.grid.type'),
				'db' => $this->namespace_price . '.type',
				'width' => '2%',
				'className' => 'text-center',
				'replace' => $this->getTypeTypes(),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getTypeTypes(), __('grid.all')),
				],
				'encode' => false,
				'search_op' => 'equal',
			],

			'manufacture' => [
				'title' => __('price::settings.catalog.grid.manufacture'),
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

			'title' => [
				'title' => __('price::settings.catalog.grid.title'),
				'db' => $this->namespace . '.title',
				'encode' => false,
				'width' => '18%',
				'render' => 'text',
				'search_op' => 'like',
			],

			'rival_link' => [
				'title' => __('price::settings.catalog.grid.rival_link'),
				'db' => $this->namespace_price . '.rival_link',
				'width' => '5%',
				'className' => 'text-center',
				'render' => 'text',
				'encode' => false,
				'null' => __('grid.null'),
				'search_op' => 'like',
			],

			'competing_links' => [
				'title' => __('price::settings.catalog.grid.competing_links'),
				'db' => $this->namespace_price . '.competing_links',
				'width' => '5%',
				'className' => 'text-center',
				'render' => 'text',
				'encode' => false,
				'null' => __('grid.null'),
				'excel_only' => true,
				'search_op' => 'like',
			],

			'catalog_groups_id' => [
				'title' => __('price::settings.catalog.grid.catalog_groups_id'),
				'db' => $this->namespace . '.catalog_groups_id',
				'width' => '5%',
				'className' => 'text-center',
				'render' => 'text',
				'encode' => false,
				'null' => __('grid.null'),
				'excel_only' => true,
				'search_op' => 'like',
			],

			'catalog_groups_name' => [
				'title' => __('price::settings.catalog.grid.catalog_groups_name'),
				'db' => $this->namespace_groups . '.name',
				'width' => '5%',
				'className' => 'text-center',
				'render' => 'text',
				'encode' => false,
				'null' => __('grid.null'),
				'search_op' => 'like',
			],

			'iteration' => array(
				'title' => __('price::settings.catalog.grid.iteration'),
				'width' => '5%',
				'className' => 'text-center',
				'db' => DB::raw('IF(' . DB::getTablePrefix() . $this->namespace_price . '.iteration IS NULL, 0, 1) AS iteration'),
				'field' => $this->namespace_price . '.iteration',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getIteration(true),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getIteration(), __('grid.all')),
				],
				'excel_only' => true,
				'search_op' => 'equal',
			),

			'iteration_count' => [
				'title' => __('price::settings.catalog.grid.iteration_count'),
				'db' => $this->namespace_price . '.iteration_count',
				'width' => '5%',
				'className' => 'text-center',
				'render' => 'text',
				'encode' => false,
				'null' => __('grid.null'),
				'excel_only' => true,
				'search_op' => 'like',
			],

			'disable' => [
				'title' => __('price::settings.catalog.grid.disable'),
				'className' => 'text-center',
				'width' => '5%',
				'db' => DB::raw('IF(' . DB::getTablePrefix() . $this->namespace_price . '.disable IS NULL, 0, 1) AS disable'),
				'field' => $this->namespace_price . '.disable',
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getDisable(true),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getDisable(), __('grid.all')),
				],
				'search_op' => 'null',
				'excel_hidden' => true,
			],

			'disable_excel' => [
				'title' => __('price::settings.catalog.grid.disable'),
				'className' => 'text-center',
				'db' => DB::raw('IF(' . DB::getTablePrefix() . $this->namespace_price . '.disable IS NULL, 0, 1) AS disable_excel'),
				'field' => $this->namespace_price . '.disable_excel',
				'replace' => $this->getDisable(),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getDisable(), __('grid.all')),
				],
				'excel_only' => true,
			],

			'our_supplier_discount' => [
				'title' => __('price::settings.catalog.supplier'),
				'db' => $this->namespace_price . '.our_supplier_discount',
				'encode' => false,
				'className' => 'text-center',
				'excel_only' => true,
				'null' => __('grid.null'),
			],

			'price_adjustments' => [
				'title' => __('price::settings.catalog.adjustments'),
				'db' => $this->namespace_price . '.price_adjustments',
				'encode' => false,
				'className' => 'text-center',
				'excel_only' => true,
				'null' => __('grid.null'),
			],

			'price_adjustments_price' => [
				'title' => __('price::settings.catalog.adjustments.price'),
				'db' => $this->namespace_price . '.price_adjustments_price',
				'encode' => false,
				'className' => 'text-center',
				'excel_only' => true,
				'null' => __('grid.null'),
			],

			'correction_factor_price' => [
				'title' => __('price::settings.catalog.correction'),
				'db' => $this->namespace_price . '.correction_factor_price',
				'encode' => false,
				'className' => 'text-center',
				'excel_only' => true,
				'null' => __('grid.null'),
			],

			'min_margin' => [
				'title' => __('price::settings.catalog.margin.min'),
				'db' => $this->namespace_price . '.min_margin',
				'encode' => false,
				'excel_only' => true,
				'null' => __('grid.null'),
			],

			'optimal_margin' => [
				'title' => __('price::settings.catalog.margin.optimal'),
				'db' => $this->namespace_price . '.optimal_margin',
				'encode' => false,
				'className' => 'text-center',
				'excel_only' => true,
				'null' => __('grid.null'),
			],

			'discount_installers_mrc' => [
				'title' => __('price::settings.catalog.discount.installers'),
				'db' => $this->namespace_price . '.discount_installers_mrc',
				'encode' => false,
				'className' => 'text-center',
				'excel_only' => true,
				'null' => __('grid.null'),
			],

			'action' => [
				'type' => 'actions',
				'excel_hidden' => true,
			]

		];

		$this->render_filter_toolbar = true;
	}

	protected function getDisable($selected = false)
	{
		$rows_select = [];
		$rows_select[0] =  __('price::settings.catalog.grid.disable.on');
		$rows_select[1] =  __('price::settings.catalog.grid.disable.off');
		$rows = [];
		$rows[0] = "<span class='far fa-circle text-success fa-md' data-bs-toggle='tooltip' data-bs-placement='top' title='$rows_select[0]'></span>";
		$rows[1] = "<span class='far fa-circle text-danger fa-md' data-bs-toggle='tooltip' data-bs-placement='top' title='$rows_select[1]'></span>";

		return ($selected ?  $rows :  $rows_select);
	}

	protected function getIteration($selected = false)
	{
		$rows_select = [];
		$rows_select[0] =  __('price::settings.catalog.grid.iteration.empty');
		$rows_select[1] =  __('price::settings.catalog.grid.iteration.has');
		$rows = [];
		$rows[0] = "<span class='far fa-circle text-primary fa-md' data-bs-toggle='tooltip' data-bs-placement='top' title='$rows_select[0]'></span>";
		$rows[1] = "<span class='far fa-circle text-warning fa-md' data-bs-toggle='tooltip' data-bs-placement='top' title='$rows_select[1]'></span>";

		return ($selected ?  $rows :  $rows_select);
	}

	protected function getTypeTypes()
	{
		return CatalogPriceRival::getType();
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
		$row['action'] = view('grid::layouts.actions', [
			'row' => $row,
			'module' => 'price.settings',
			'actions' => [
				'edit' => true,
			],
		])->render();

		return $row;
	}
}
