<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Users\UI\WEB\Grids;

use Boot\Models\Groups;
use Boot\Support\Facades\Auth;
use Boot\System\Database\DB;

//use Boot\Support\Facades\Auth;
use Boot\System\Grid\GridModern;
use \Boot\Support\Facades\Translation;
use Boot\System\Permission\Models\Role;

class Users extends GridModern
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
    protected $namespace = 'users';

    /**
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        // Заголовок таблицы
        $this->title = __($this->namespace . '::lang.admin.table.title');

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
                // 'label' => '#',
                'db' => $this->namespace . '.id',
                'width' => '5%',
                'className' => 'text-center px-1',
                //'icon' => 'featherhash',

                // query sql
                'search_op' => 'like',
            ],

            // Фамилия
            'surname' => [
                'title' => 'Фамилия',
                'db' => $this->namespace . '.surname',
                'className' => 'text-center px-1',
                'render' => 'text',
                //'popover' => 'bottom',

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Имя
            'name' => [
                'title' => 'Имя',
                'db' => $this->namespace . '.name',
                'className' => 'text-center px-1',
                'render' => 'text',
                //'popover' => 'bottom',

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Отчество
            'ochestvo' => [
                'title' => 'Отчество',
                'db' => $this->namespace . '.ochestvo',
                'className' => 'text-center px-1',
                'render' => 'text',
                // 'popover' => 'bottom',

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Логин - nick - Он же email
            'nick' => [
                'title' => 'Логин',
                'db' => $this->namespace . '.nick',
                'className' => 'text-center',
                'render' => 'text',

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Группа
            // 'group' => [
            //     'title' => 'Группа',
            //     'db' => $this->namespace . '.group',
            //     'className' => 'text-center',
            //
            //     //'encode' => false,
            //     'null' => __('grid.null'),
            //
            //     // query sql
            //     'search_op' => 'like'
            // ],

            // Роль
            'role' => [
                'title' => 'Роль',
                'db' => $this->namespace . '.group',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getActive(true), 'Все'),
                ],
                'className' => 'text-center',

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Email - Почта
            'email' => [
                'title' => 'Электронная почта',
                'db' => $this->namespace . '.email',
                'className' => 'text-center',
                'render' => 'text',
                //'popover' => 'bottom',

                //'encode' => false,
                'null' => __('grid.null'),

                // query sql
                'search_op' => 'like'
            ],

            // Активность
            'active' => [
                'title' => 'Активность',
                'db' => $this->namespace . '.active',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getActive(true), 'Все'),
                ],
                'className' => 'text-center',

                'null' => __('grid.null'),

                'encode' => false,
                'replace' => $this->getActive(),

                // query sql
                'search_op' => 'like'
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

            // Статус
            // 'status' => [
            //     'title' => 'Статус',
            //     'db' => $this->namespace . '.status',
            //     'type' => 'select',
            //     'options' => [
            //         'value' => $this->setValue($this->getStatus(true), 'Все'),
            //     ],
            //
            //     'width' => '5%',
            //     'className' => 'text-center px-1',
            //
            //     'encode' => false,
            //     'replace' => $this->getStatus(),
            //
            //     // query sql
            //     'search_op' => 'equal'
            // ],

            // Действия
            'action' => [
                'type' => 'actions',
                // 'width' => '1%',
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
     * Получение активности
     *
     * @param bool $select
     * @return void
     */
    protected function getActive($select = false)
    {
        $rows = trans('users::lang.admin.active');

        $rows_select = [
            0 => '<span class="badge rounded-pill bg-danger d-block">' . $rows[0] . '</span>',
            1 => '<span class="badge rounded-pill bg-success d-block">' . $rows[1] . '</span>',
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
        // $roles = \Boot\Models\Users::find($row['id']);
        // $roles = \Roles\Models\Users::find($row['id']);
        // $roles = Role::all();
        // $row['group'] = $roles->roles;
        // if ($roles->isNotEmpty()) {
        //     // $row['group'] = $roles->first()->name;
        //     $row['group'] = $roles;
        // } else {
        //     $row['group'] = 'Не выбрана';
        // }

        // $row['email'] = '<a href="' . $row['email'] . '" target="_blank">' . $row['email'] . '</a>';

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => $this->namespace,
            'actions' => [
                'show' => true,
                'edit' => true,
                // 'destroy' => true,
            ],
        ])->render();

        return $row;
    }
}