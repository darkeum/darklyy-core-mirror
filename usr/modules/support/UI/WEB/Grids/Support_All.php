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

//use Boot\System\Grid\Grid;
use Boot\System\Database\DB;
use Support\Tasks\TypesTask;
use Support\Tasks\StatusTask;
use Support\Tasks\MarksTask;
//use Boot\Support\Facades\Auth;
use Boot\System\Grid\GridModern;

class Support_All extends GridModern
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
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        // Заголовок таблицы
        $this->title = __('support::lang.grid.all');

        // Опции таблицы (в данном случае сортировка)
        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );

        // Действия мыши по строкам (Не обязательный)
        //$this->mouse = config($this->namespace . '-config.grid.mouse');

        // Опции панели навигации
        $this->nav = array(
            'excel' => true,
            'exceltext' => __('grid.export.excel'),
            'trash' => true,
            'trashtype' => 'null',
            'trashtext' => __('grid.trash.show'),
            'trashfield' => $this->namespace . '.deleted_at',
        );

        // Подключение к необходимой таблице
        $this->db = DB::table($this->namespace);

        // Настройка колонок
        $this->cols = array(
            'id' => array(
                'db' => $this->namespace . '.id',
                'visible' => false
            ),

            'numb' => array(
                'title' => '№',
                'label' => '#',
                'db' => $this->namespace . '.id',
                'width' => '5%',
                'className' => 'text-center px-1',
                //'icon' => 'featherhash',

                // query sql
                'search_op' => 'like',
            ),

            'title' => array(
                'title' => 'Заголовок',
                'db' => $this->namespace . '.title',
                'className' => '',
                'render' => 'text',
                'popover' => true,
                //'null' => "Отсутствует",

                //'other' => [
                //    'attach' => true,
                //],

                // query sql
                'search_op' => 'like'
            ),

            'uid' => array(
                'title' => __('grid.uid'),
                'db' => $this->namespace . '.uid',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getUid($this->namespace), __('grid.all')),
                ),
                'width' => '10%',
                'className' => 'text-center px-1',

                'encode' => false,
                //'null' => __('grid.not_specified'),
                'sortable' => false,
                'replace' => $this->getUid($this->namespace),

                // query sql
                'search_op' => 'equal',
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
                'title' => 'Создание',
                'db' => 'support.created_at',
                'type' => 'date_range',
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
                'options' => $this->dateRangeSearch(),
                'width' => '12%',
                'className' => 'text-center px-1', // text-nowrap

                // query sql
                'search_op' => 'date_range'
            ),

            'completed_at' => array(
                'title' => 'Завершено',
                'db' => 'support.completed_at',
                'type' => 'date_range',
                //'format_date' => 'd.Y',
                'options' => $this->dateRangeSearch(),
                'width' => '10%',
                'className' => 'text-center px-1',

                // query sql
                'search_op' => 'date_range',
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
                'db' => $this->namespace . '.status',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getStatus(true), 'Все'),
                ),
                'width' => '5%',
                'className' => 'text-center px-1',

                'encode' => false,
                'replace' => $this->getStatus(),

                // query sql
                'search_op' => 'equal'
            ),


            'action' => array(
                //'title' =>  __('grid.action'),
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
     * Получение типов заявок
     *
     * @param bool $select
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
     * Получение статуса заявок
     *
     * @param bool $select
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
     * @param array $row
     * @return array
     */
    protected function parseRow($row)
    {
        //$row['action'] = (string)view('support::manager.grid.action', [
        //    'row' => $row,
        //    'show' => router("admin.support.show", $row['id']),
        //]);

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => $this->namespace,
            'actions' => [
                'show' => true,
            ],
        ])->render();


        // if (is_date($row["completed_at"], "Y-m-d H:i:s")) $row["completed_at"] = date("d.m.Y", strtotime($row["completed_at"]));
        if (is_date($row["create_at"], "Y-m-d H:i:s")) $row["create_at"] = date("d.m.Y", strtotime($row["create_at"]));

        return $row;
    }
}