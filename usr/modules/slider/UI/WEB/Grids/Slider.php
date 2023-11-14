<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Slider\UI\WEB\Grids;

use Boot\Support\Facades\Auth;
use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;
use \Boot\Support\Facades\Translation;

class Slider extends GridModern
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
    protected $namespace = 'slider';

    /**
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        // Заголовок таблицы
        $this->title = __('slider::lang.admin.table.title');

        // Опции таблицы (в данном случае сортировка)
        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        ];

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

            // Заголовок #1
            'title_top' => [
                'title' => 'Заголовок - Строка 1',
                'db' => $this->namespace . '.title_top',
                'className' => '',
                'render' => 'text',
                'popover' => true,

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Заголовок #2
            'title_down' => [
                'title' => 'Заголовок - Строка 2',
                'db' => $this->namespace . '.title_down',
                'className' => '',
                'render' => 'text',
                'popover' => true,

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Ссылка
            'link' => [
                'title' => 'Ссылка',
                'db' => $this->namespace . '.link',
                'className' => '',
                'render' => 'text',
                'popover' => true,

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Главное изображение слайдера
            'img' => [
                'title' => 'Главное изображение',
                'db' => $this->namespace . '.img',
                'className' => 'text-center',

                'encode' => false,
                'sortable' => false,
                'search' => false,
            ],

            // Фон - Слева
            'bg_left' => [
                'title' => 'Фон - Слева',
                'db' => $this->namespace . '.bg_left',
                'className' => 'text-center',

                'encode' => false,
                'sortable' => false,
                'search' => false,
            ],

            // Фон - Справа
            'bg_right' => [
                'title' => 'Фон - Справа',
                'db' => $this->namespace . '.bg_right',
                'className' => 'text-center',

                'encode' => false,
                'sortable' => false,
                'search' => false,
            ],

            // Приоритет
            'priority' => [
                'title' => 'Приоритет',
                'db' => $this->namespace . '.priority',
                'className' => 'text-center',

                'encode' => false,
                'sortable' => false,
                'search' => false,
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

            // Изменение
            //'updated_at' => [
            //    'title' => __('grid.updated_at_cut'),
            //    'db' => $this->namespace . '.updated_at',
            //    'type' => 'date_range',
            //    //'options' => $this->dateRangeSearch(),
            //
            //    'width' => '10%',
            //    'className' => 'text-center px-1',
            //
            //    // query sql
            //    'search_op' => 'date_range'
            //],

            // Статус
            //'status' => [
            //    'title' => 'Статус',
            //    'db' => $this->namespace . '.status',
            //    'type' => 'select',
            //    'options' => [
            //        'value' => $this->setValue($this->getStatus(true), 'Все'),
            //        //'value' => $this->setValue($this->getStatus('select'), __('grid.all')), // OLD
            //    ],
            //
            //    'width' => '5%',
            //    'className' => 'text-center px-1',
            //
            //    'encode' => false,
            //    'replace' => $this->getStatus(),
            //    //'replace' => $this->getStatus('render'), // OLD
            //
            //    // query sql
            //    'search_op' => 'equal'
            //],

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

    /**
     * Подстановка и замена элементов в строке (подмена ячеек)
     *
     * @param array $row
     * @return array
     */
    protected function parseRow($row)
    {
        // Использование общего шаблона grid img
        $row['img'] = view('grid::layouts.img', [
            //'title' => 'Главное изображение',
            'src' => $row['img'],
        ])->render();

        $row['bg_left'] = view('slider::manager.grid.img', [
            //'title' => 'Фон - Слева',
            'src' => $row['bg_left'],
        ])->render();

        $row['bg_right'] = view('slider::manager.grid.img', [
            //'title' => 'Фон - Справа',
            'src' => $row['bg_right'],
        ])->render();

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => $this->namespace,
            'actions' => [

                //'custom' => ['Название 1', 'route:admin.slider.index', 'plus'],
                //'custom' => ['Название 2', 'url:/', 'minus'],

                //'show' => true,
                //'show' => false, // false фильтруется
                'edit' => true,
                //'custom' => ['Название 1', 'route:admin.slider.index', 'plus'],
                //'custom' => ['Название 2', 'url:/', 'minus'],
                'destroy' => true,

            ],
        ])->render();

        return $row;
    }
}