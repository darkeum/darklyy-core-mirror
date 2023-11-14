<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Integration\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class IntegrationCatalog extends GridModern
{
	/**
	 * Тип подключение к базе данных
	 *
	 * @var string
	 */
	protected $db_type = 'db';

	/**
	 * Пространство имен
	 *
	 * @var string
	 */
	protected $namespace = 'catalog';

	/**
	 * Действие на двойной клик
	 *
	 * @var string
	 */
	protected $opts_type = "open";

	/**
	 * Инициализация grid
	 *
	 * @return void
	 */
	protected function init()
	{
		//Заголовок таблицы
		$this->title = __('integration::general.index.list');

		//Опции таблицы (в данном случае сортировка)
		$this->options = array(
            'sort' => [
                'name' => 'numb',
                'order' => 'DESC'
            ],
		);

		//Опции панели навигации
		$this->nav = array(
			'excel' => true,
			'exceltext' => __('grid.export.excel'),
			'trash' => true,
			'trashtype' => 'null',
			'trashtext' => __('grid.trash.show'),
			'trashfield' => $this->namespace . '.deleted_at',
		);

		//Подключение к необходимой таблице
		$this->db = DB::table($this->namespace)->where('use_integration', 1);

		$this->cols = array(

			'id' => array(
				'visible' => false,
				'db' => $this->namespace . '.id',
			),

			'numb' => array(
                'title' => '№',
                'label' => '#',
				'db' => $this->namespace . '.id',
				'width' => '5%',
                'className' => 'text-center',
				'search_op' => 'like'
			),

			'title' => array(
				'title' => 'Название товара',
                'db' => $this->namespace . '.title',
				'width' => '30%',
                'className' => '',
                'render' => 'expand',
				'priority' => 1,
                'search_op' => 'like',
			),

			'sku' => array(
				'title' => 'Артикул товара',
                'db' => $this->namespace . '.sku',
				'width' => '30%',
                'className' => '',
                'render' => 'expand',
				'priority' => 1,
                'search_op' => 'like',
			),

			'action' => array(
                'type' => 'actions',
				'className' => 'text-center px-1',
                'width' => '1%',
				'hidden' => false,

				'manual' => true,
				'encode' => false,
				'sortable' => false,
				'search_op' => false,
				'search' => false,
				'title' => false,
			),
		);
		$this->render_filter_toolbar = true;
	}

	/**
	 * Подстановка и замена элементов в строке (подмена ячеек)
	 *
	 * @param  array $row
	 * @return void
	 */
	protected function parseRow($row)
	{
		$row['action'] = (string)view('away::manager.grid.action', [
		    'show' => router("admin.catalog.edit",  $row['id']),
		    'edit' => router("admin.integration.edit",  $row['id']),
		]);
		return $row;
	}
}
