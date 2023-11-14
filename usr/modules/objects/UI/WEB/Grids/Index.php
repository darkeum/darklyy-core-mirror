<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Objects\UI\WEB\Grids;

//use Boot\System\Grid\Grid;
use Boot\Models\Users;
use Boot\Support\Facades\Auth;

//use Boot\System\Database\DB;
use Illuminate\Support\Facades\DB;

//use Boot\Support\Facades\Auth;
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
    protected $namespace = 'object';

    /**
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        // Заголовок таблицы
        $this->title = __('objects::lang.table.title');

        // Опции таблицы (в данном случае сортировка)
        $this->options = [
            'sort' => [
                'name' => 'numb',
                'order' => 'DESC'
            ],
        ];

        // Действия мыши по строкам (Не обязательный)
        //$this->mouse = config($this->namespace . '-config.grid.mouse');

        // Опции панели навигации
        $this->nav = [
            'excel' => true,
            'exceltext' => __('grid.export.excel'),
            'trash' => true,
            'trashtype' => 'null',
            'trashtext' => __('grid.trash.show'),
            'trashfield' => $this->namespace . '.deleted_at'
        ];

        // хз
        $this->render_filter_toolbar = true;

        //$obj = \Objects\Models\Objects::where('status' , 3)->get();
        //foreach ($obj as $o) {
        //    $rr = $o;
        //    $rr->status = 1;
        //    $rr->save();
        //}

        // Подключение к необходимой таблице
        $this->db = DB::table($this->namespace);

        $users = getUsers('id', DB::table('object')->pluck('uid')); // Через новый хелпер
        //$users = $this->getIDs('users', 'id', DB::table('object')->pluck('uid'), 'surname');

        // Получаем массив ФИО `Пупкин В.В` пользователей
        $this->users = $this->getFilterParam(
        //$users->sortBy('surname') // Можно отсортировать и тут
            $users
        )->transform(function ($user) {
            return user_short_name($user);
        })->toArray();

        //dd($this->users);

        // Настройка колонок
        $this->cols = [
            // ID
            'id' => [
                'db' => $this->namespace . '.id',
                'visible' => false
            ],

            // №
            'numb' => [
                'title' => '№',
                'label' => '#',
                'db' => $this->namespace . '.id',
                'width' => '5%',
                'className' => 'text-center px-1',
                //'icon' => 'featherhash',

                'prefix' => 'ОВ-',

                // query sql
                'search_op' => 'like',
            ],

            // Название
            'title' => [
                'title' => 'Название',
                'db' => $this->namespace . '.name',
                'className' => '',
                'render' => 'text',
                'popover' => true,
                //'popover' => 'bottom',
                //'popover' => 'top',

                //'null' => "Отсутствует",

                //'other' => [
                //    'attach' => true,
                //],

                // query sql
                'search_op' => 'like'
            ],

            // Адрес
            'address' => [
                'title' => 'Адрес',
                'db' => $this->namespace . '.address',
                'className' => '',
                'render' => 'text',
                'popover' => true,
                'null' => "Отсутствует",

                // query sql
                'search_op' => 'like'
            ],

            // Ответственный
            'uid' => [
                'title' => __('grid.uid'),
                'db' => $this->namespace . '.uid',
                'type' => 'select',
                'options' => [
                    //'value' => $this->setValue($this->users, __('grid.all')),
                    'value' => $this->setValue($this->users, true),
                ],
                'width' => '10%',
                'className' => 'text-center px-1',

                'encode' => false,
                //'null' => __('grid.not_specified'),
                'sortable' => false,
                //'replace' => $usersNames,

                // query sql
                'search_op' => 'equal',
            ],

            // Тип
            //'types' => [
            //    'title' => 'Тип',
            //    'db' => $this->namespace . '.types',
            //    'type' => 'select',
            //    'options' => array(
            //        'value' => $this->setValue($this->getTypes(true), 'Все'),
            //    ),
            //    'width' => '5%',
            //    'className' => 'text-center px-1',
            //
            //    'encode' => false,
            //    //'replace' => $this->getTypes(),
            //
            //    // query sql
            //    'search_op' => 'equal'
            //],

            // Статус
            'status' => [
                'title' => 'Статус',
                'db' => $this->namespace . '.status',
                'type' => 'select',
                'options' => array(
                    //'value' => $this->setValue($this->getStatus(true), 'Все'),
                    'value' => $this->setValue($this->getStatus(true), true),
                ),
                'width' => '5%',
                'className' => 'text-center px-1',

                'encode' => false,
                'replace' => $this->getStatus(),

                // query sql
                'search_op' => 'equal'
            ],

            // Создание
            'create_at' => [
                'title' => 'Создание',
                'db' => $this->namespace . '.created_at',
                'type' => 'date_range',
                // 'options' => $this->dateRangeSearch(),
                'width' => '10%',
                'className' => 'text-center px-1',

                // query sql
                'search_op' => 'date_range'
            ],

            // Действия
            'action' => [
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
            ]
        ];

    }

    /**
     * Получение типов
     *
     * @param bool $select
     * @return void
     */
    protected function getTypes($select = false)
    {
        $rows = config('objects-config.types');

        $rows_select = [
            //0 => '<span class="badge rounded-pill badge-light-danger d-block">' . $rows[0] . '</span>',
            //1 => '<span class="badge rounded-pill badge-light-danger d-block">' . $rows[1] . '</span>',
            //2 => '<span class="badge rounded-pill badge-light-danger d-block">' . $rows[2] . '</span>',
            //3 => '<span class="badge rounded-pill badge-light-danger d-block">' . $rows[3] . '</span>',
        ];

        return ($select ? $rows : $rows_select);
    }

    /**
     * Получение статуса
     *
     * @param bool $select
     * @return void
     */
    protected function getStatus($select = false)
    {
        //$rows_select = [];
        $rows = [
            1 => __('Ожидает'),
            2 => __('Подтвержден'),
            3 => __('На удалении'),
        ];
        //$rows = config('objects-config.status');

        $rows_select = [
            1 => '<span class="badge rounded-pill badge-light-warning d-block">' . $rows[1] . '</span>',
            2 => '<span class="badge rounded-pill badge-light-success d-block">' . $rows[2] . '</span>',
            3 => '<span class="badge rounded-pill badge-light-danger d-block">' . $rows[3] . '</span>'
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
        if (array_key_exists($row['uid'], $this->users)) {
            $row['uid'] = $this->users[$row['uid']];
        }

        //$row['type'] = config('objects-config.types')[$row['type'] ?? 0];

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            //'module' => $this->namespace,
            'module' => 'objects',
            'actions' => [
                'show' => true,
                'edit' => true,
                // 'destroy' => true,
                '<a class="obj-bind" data-url="'. route('admin.objects.users', $row['id']) .'"><i class="" data-feather="user-plus"></i> Привязать</a>',
            ],
        ])->render();

        return $row;
    }
}