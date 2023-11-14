<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Decrees\UI\WEB\Grids;

use Boot\System\Database\DB;
use Decrees\Tasks\TypesTask;
use Boot\System\Grid\GridModern;

class Index extends GridModern
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
	protected $namespace = 'decrees';

	/**
	 * Инициализация grid
	 *
	 * @return void
	 */
	protected function init()
	{
		//Заголовок таблицы
		$this->title = __('decrees::admin.list');

		//Опции таблицы (в данном случае сортировка)
		$this->options = [
			'sort' => [
				'name' => 'id',
				'order' => 'DESC'
			],
		];

		//Опции панели навигации
		$this->nav = [
			'export' => true,
			'export.detail' => [
				'excel' => true,
				'pdf' => true,
				'print' => true,
				'copy' => true,
				'csv' => false,
			],
		];

		//Подключение к необходимой таблице
		$this->db = DB::table($this->namespace . ' as d')
			->leftJoin("org as o", "o.id", "=", "d.org")
			->leftJoin("users as u", "u.id", "=", "d.uid")
			->where('d.deleted_at', null);

		//Настройка колонок
		$this->cols = [
			'id' => [
				'visible' => false,
				'db' => 'd.id',
			],

			'org_real' => [
				'visible' => false,
				'db' => 'd.org',
			],

			'date_real' => [
				'visible' => false,
				'db' => 'd.created_at',
			],

			'numb_real' => [
				'visible' => false,
				'db' => 'd.numb',
			],

			'code' => [
				'db' => 'd.id',
				'title' => __('grid.numb'),
				'width' => "1%",
				'search_op' => 'like'
			],

			'numb' => [
				'db' => 'd.numb',
				'title' => __('grid.code'),
				'width' => "1%",
				'search_op' => 'like',
				'className' => 'text-center',
			],

			'org' => [
				'title' => __('grid.org'),
				'null' => __('grid.not_specified'),
				'db' => 'd.org',
				'width' => "10%",
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getOrg($this->namespace),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getOrg($this->namespace), __('grid.all')),
				],
				'search_op' => 'equal',
				'className' => 'text-center',
			],

			'date' => [
				'title' => __('grid.created_at'),
				'db' => 'd.created_at',
				'width' => "1%",
				'format_date' => "d.m.Y",
				'type' => 'date_range',
				'search_op' => 'date_range',
				'className' => 'text-center',
			],

			'uid' => [
				'title' => __('grid.uid'),
				'db' => 'd.uid',
				'width' => "10%",
				'className' => 'text-center',
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getUid($this->namespace),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getUid($this->namespace), __('grid.all')),
				],
				'search_op' => 'equal',
			],

			'type' => [
				'title' => __('decrees::admin.type'),
				'width' => "2%",
				'className' => 'text-center',
				'db' => 'd.type',
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getTypes(),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getTypes(true), __('grid.all')),
				],
				'search_op' => 'equal',
				'excel_hidden' => true,
			],

			'started_at' => [
				'title' => __('decrees::admin.started_at'),
				'db' => 'd.started_at',
				'width' => "1%",
				'format' => "d.m.Y",
				'type' => 'date_range',
				'search_op' => 'date_range',
				'className' => 'text-center',
			],

			'ended_at' => [
				'title' => __('decrees::admin.ended_at'),
				'db' => 'd.ended_at',
				'width' => "1%",
				'format' => "d.m.Y",
				'type' => 'date_range',
				'search_op' => 'date_range',
				'className' => 'text-center',
			],

			'comment' => [
				'db' => 'd.comment',
				'title' => __('grid.comment'),
				'width' => '15%',
				'render' => 'text',
				'search_op' => 'like'
			],

			'status' => [
				'title' => __('grid.status'),
				'width' => '1%',
				'align' => 'center',
				'db' => 'd.status',
				'className' => 'text-center',
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getStatus(),
				'type' => 'select',
				'options' => [
					'value' => $this->setValue($this->getStatus(true), __('grid.all')),
				],
				'search_op' => 'equal',
				'excel_hidden' => true,
			],

			'action' => [
				'width' => "1%",
				'type' => 'actions',
				'className' => 'text-center',
				'manual' => true,
				'encode' => false,
				'sortable' => false,
				'search_op' => false,
				'search' => false,
				'title' => false,
				'excel_hidden' => true,
			]

		];
		$this->render_filter_toolbar = true;
	}

	/**
	 * Получение типов приказов
	 *
	 * @param  bool $select
	 * @return void
	 */
	protected function getTypes($select = false)
	{
		$rows_select = [];
		$rows = app(TypesTask::class)->run(null, 'default');
		$rows_select = app(TypesTask::class)->run(null, 'icon');
		return ($select ? $rows : $rows_select);
	}

	/**
	 * Получение статуса приказов
	 *
	 * @param  bool $select
	 * @return void
	 */
	protected function getStatus($select = false)
	{
		$rows_select = [];
		$rows = [
			__('decrees::admin.status.end'),
			__('decrees::admin.status.active'),
		];

		$rows_select[0] = '<i class="fa-regular fa-circle" data-bs-toggle="tooltip" data-bs-placement="top" style="color: #fb0909;" title="' . $rows[0] . '"></i>';

		$rows_select[1] = '<i class="fa-regular fa-circle" data-bs-toggle="tooltip" data-bs-placement="top" style="color: #5ed100;" title="' . $rows[1] . '"></i>';

		return ($select ? $rows : $rows_select);
	}

	/**
	 * Подстановка и замена элементов в строке (подмена ячеек)
	 *
	 * @param  array $row
	 * @return void
	 */
	protected function parseRow($row)
	{

		$row['comment'] = strip_tags($row['comment']);

		$row['action'] = view('grid::layouts.actions', [
			'row' => $row,
			'module' => $this->namespace,
			'actions' => [
				'edit'  => true,
				'show' => true,
                'edit'  => true,
				'destroy' => true
			],
		])->render();

		return $row;
	}
}
