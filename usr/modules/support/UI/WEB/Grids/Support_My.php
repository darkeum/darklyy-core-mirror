<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\UI\WEB\Grids;

use Boot\System\Database\DB;
use Support\Tasks\TypesTask;
use Support\Tasks\StatusTask;
use Support\Tasks\MarksTask;
use Boot\Support\Facades\Auth;
use Boot\System\Grid\GridModern;

class Support_My extends GridModern
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
	protected $namespace = 'support';

	/**
	 * Действие на двойной клик
	 *
	 * @var string
	 */
	protected $opts_type = "open";

	/**
	 * Получение URL на открытие записи по двойному клику
	 *
	 * @return void
	 */
	protected function getOpenUrl()
	{
		return route("admin.support.show", ["id" => "@id"]);
	}

	/**
	 * Инициализация grid
	 *
	 * @return void
	 */
	protected function init()
	{
		//Заголовок таблицы
		$this->title = __('support::lang.grid.my');

		//Опции таблицы (в данном случае сортировка)
		$this->options = array(
            'sort' => [
                'name' => 'id',
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
		$this->db = DB::table($this->namespace)->where('uid', Auth::id());

		//Настройка колонок
		$this->cols = array(
			'id' => array(
				'db' => $this->namespace . '.id',
                'visible' => false,
			),

			'numb' => array(
				'title' => '№',
                'label' => '#',
                'db' => $this->namespace . '.id',
                'width' => '5%',
                'className' => 'text-center px-1',

                // query sql
				'search_op' => 'like'

			),

			'title' => array(
				'title' => 'Заголовок',
                'db' => $this->namespace . '.title',
				//'null' => "Отсутствует",

                // query sql
				'search_op' => 'like',
				'priority' => 1
			),

			'type' => array(
				'title' => 'Тип',
				'db' => $this->namespace.'.type',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getTypes(true), 'Все'),
                ),
                'width' => '8%',
                'className' => 'text-center px-1',

                'encode' => false,
                'replace' => $this->getTypes(),

                // query sql
				'search_op' => 'equal',
			),

			'create_at' => array(
			    'title' => 'Дата создания',
                'db' => 'support.created_at',
                'type' => 'date_range',
                //'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                'width' => '10%',
                'className' => 'text-center px-1',

                // query sql
			    'search_op' => 'date_range'
			),

			'planned_at' => array(
			    'title' => 'Планируемая дата',
			    'db' => 'support.planned_at',
                'type' => 'date_range',
                //'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                'width' => '10%',
                'className' => 'text-center px-1',

                // query sql
			    'search_op' => 'date_range'
			),

			'completed_at' => array(
			    'title' => 'Дата завершения',
			    'db' => 'support.completed_at',
                'type' => 'date_range',
                //'format_date' => 'd.Y',
                'options' => $this->dateRangeSearch(),
                'width' => '10%',
                'className' => 'text-center px-1',

                // query sql
                'search_op' => 'date_range'
			),

			'mark' => array(
				'title' => 'Приоритет',
				'db' => $this->namespace.'.mark',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getMarks(true), 'Все'),
                ),
                'width' => '8%',
                'className' => 'text-center px-1',

                'encode' => false,
                'replace' => $this->getMarks(),

                // query sql
				'search_op' => 'equal',
			),

			'status' => array(
				'title' => 'Статус',
				'db' => $this->namespace.'.status',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getStatus(true), 'Все'),
                ),
                'width' => '8%',
                'className' => 'text-center px-1',

                'encode' => false,
                'replace' => $this->getStatus(),

                // query sql
				'search_op' => 'equal',
			),

			'action' => array(
                'type' => 'actions',
                'width' => '1%',
                'className' => 'text-center px-1',

				'manual' => true,
				'encode' => false,
				'sortable' => false,
				'search' => false,
				'excel_hidden' => true,

                // query sql
                'search_op' => false,
			),

		);
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
		$rows = app(StatusTask::class)->run(null, 'default');
		$rows_select = app(StatusTask::class)->run(null, 'badge');
		return ($select ? $rows : $rows_select);
	}

	protected function getMarks($select = false)
	{
		$rows_select = [];
		$rows = app(MarksTask::class)->run(null, 'default');
		$rows_select = app(MarksTask::class)->run(null, 'badge');
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
		//$row['action'] = (string)view('support::manager.grid.action', [
		//    'row' => $row,
		//    'show' => router("admin.support.show",  $row['id']),
		//]);

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => $this->namespace,
            'actions' => [
                'show' => true,
            ],
        ])->render();

		// if (is_date($row["date"], "Y-m-d")) $row["date"] = date("d.m.Y", strtotime($row["date"]));
		return $row;
	}
}
