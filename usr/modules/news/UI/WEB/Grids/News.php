<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace News\UI\WEB\Grids;

use Boot\Support\Facades\Auth;
use Boot\System\Database\DB;

//use Boot\Support\Facades\Auth;
use Boot\System\Grid\GridModern;
use \Boot\Support\Facades\Translation;

class News extends GridModern
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
    protected $namespace = 'news';

    /**
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        // Заголовок таблицы
        $this->title = __('news::lang.admin.table.title');

        // Опции таблицы (в данном случае сортировка)
        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        ];

        // Действия мыши по строкам (Не обязательный)
        //$this->mouse = config($this->namespace . '-config.grid.mouse');

        // Опции панели навигации
        $this->nav = [
            //'excel' => true,
            //'exceltext' => __('grid.export.excel'),
            //'trash' => true,
            //'trashtype' => 'null',
            //'trashtext' => __('grid.trash.show'),
            //'trashfield' => $this->namespace . '.deleted_at'
        ];

        //
        $this->render_filter_toolbar = true;

        // Подключение к необходимой таблице
        $this->db = DB::table($this->namespace);

        // Настройка колонок
        $this->cols = [
            // ID
            'id' => [
                'db' => $this->namespace . '.id',
                'visible' => false
            ],

            // №
            'numb' => [
                'title' => __('grid.numb'),
                'label' => '#',
                'db' => $this->namespace . '.id',
                'width' => '5%',
                'className' => 'text-center px-1',
                //'icon' => 'featherhash',

                // query sql
                'search_op' => 'like',
            ],

            // Заголовок
            'title' => [
                'title' => __('grid.heading'),
                'db' => $this->namespace . '.title',
                'className' => '',
                'render' => 'text',
                'popover' => true,
                //'popover' => 'bottom',

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Ответственный
            'uid' => [
                'title' => __('grid.uid'),
                'db' => $this->namespace . '.uid',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getUid($this->namespace), __('grid.all')),
                    //'value' => $this->setValue($this->getUid('reviews'), __('grid.all')), // OLD
                ],
                'width' => '10%',
                'className' => 'text-center px-1',

                'encode' => false,
                'null' => __('grid.not_specified'),
                'sortable' => false,
                'replace' => $this->getUid($this->namespace),
                //'replace' => $this->getUid('reviews'), // OLD

                // query sql
                'search_op' => 'equal',
            ],

            // Создание
            'create_at' => [
                'title' => __('grid.created_at_cut'),
                'db' => $this->namespace . '.created_at',
                'type' => 'date_range',
                //'options' => $this->dateRangeSearch(),

                'width' => '10%',
                'className' => 'text-center px-1',

                // query sql
                'search_op' => 'date_range'
            ],

            // Создание
            'updated_at' => [
                'title' => __('grid.updated_at_cut'),
                'db' => $this->namespace . '.updated_at',
                'type' => 'date_range',
                //'options' => $this->dateRangeSearch(),

                'width' => '10%',
                'className' => 'text-center px-1',

                // query sql
                'search_op' => 'date_range'
            ],

            // Просмотры
            'views' => [
                'title' => 'Просмотры',
                'db' => $this->namespace . '.views',
                'width' => '5%',
                'className' => 'text-center px-1',

                //'hidden' => false,
                //'encode' => false,
                //'null' => Translation::trans('grid.null'),

                'search_op' => 'like'
            ],

            // Статус
            'status' => [
                'title' => 'Статус',
                'db' => $this->namespace . '.status',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getStatus(true), 'Все'),
                    //'value' => $this->setValue($this->getStatus('select'), __('grid.all')), // OLD
                ],

                'width' => '5%',
                'className' => 'text-center px-1',

                'encode' => false,
                'replace' => $this->getStatus(),

                // query sql
                'search_op' => 'equal'
            ],

            // Действия
            'action' => [
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
            ]
        ];

    }

    /**
     * Получение статуса
     *
     * @param bool $select
     * @return void
     */
    protected function getStatus($select = false)
    {
        $rows = trans('news::lang.admin.status');

        $rows_select = [
            0 => '<span class="badge rounded-pill badge-light-warning d-block">' . $rows[0] . '</span>',
            1 => '<span class="badge rounded-pill badge-light-success d-block">' . $rows[1] . '</span>',
            2 => '<span class="badge rounded-pill badge-light-danger d-block">' . $rows[2] . '</span>',
        ];

        return ($select ? $rows : $rows_select);
    }

    //protected function getStatus($type = 'select')
    //{
    //    $rows = [];
    //    if ($type == 'select') {
    //        $rows[0] = Translation::trans('reviews.status.off');
    //        $rows[1] = Translation::trans('reviews.status.on');
    //    } elseif ($type == 'render') {
    //        $rows[0] = "<span class='fal fa-circle text-danger fa-md' data-toggle='tooltip' title='". Translation::trans('order.status.off')."'></span>";
    //        $rows[1] = "<span class='fal fa-check-circle text-success fa-md' data-toggle='tooltip' title='". Translation::trans('order.status.on')."'></span>";
    //    }
    //    return $rows;
    //}


    /**
     * Подстановка и замена элементов в строке (подмена ячеек)
     *
     * @param array $row
     * @return array
     */
    protected function parseRow($row)
    {
        //$row['action'] = view('grid::layouts.Templates.actions_template', [
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => $this->namespace,
            'actions' => [
                'show' => true,
                'edit' => true,
            ],
        ])->render();

        return $row;
    }
}