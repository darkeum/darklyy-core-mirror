<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Notifications\UI\WEB\Grids;

use Boot\Support\Facades\DB;
use Boot\Support\Facades\Auth;
use Boot\System\Grid\GridModern;
use Notifications\Support\NotificationsSupport;
use function Composer\Autoload\includeFile;

class Index extends GridModern
{
    /**
     * Пространство имен
     *
     * @var string
     */
    protected $namespace = 'notifications';

    /**
     * Действие на двойной клик
     *
     * @var string
     */
    protected $opts_type = "open";

    /**
     * Получение URL на открытие записи по двойному клику
     *
     * @return string
     */
    protected function getOpenUrl()
    {
        return route("admin.notifications.redirect", ["id" => "@id"]);
    }

    /**
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        // Заголовок таблицы
        $this->title = __('notifications::admin.list');

        // Опции таблицы (в данном случае сортировка)
        $this->options = array(
            'sort' => [
                'name' => 'code',
                'order' => 'DESC'
            ],
        );

        // Опции панели навигации
        $this->nav = array(
            'export' => true,
            'export.detail' => [
                'excel' => true,
                'pdf' => true,
                'print' => true,
                'copy' => true,
                'csv' => false,
            ],
        );

        // Подключение к необходимой таблице ПЕРЕДЕЛАТЬ !!!! ДЛЯ СЕБЯ !!!
        if (Auth::id() == 1092) {
            $this->db = DB::table('notif as n')->where('uid', 1);
        } else {
            $this->db = DB::table('notif as n')->where('uid', Auth::id());
        }

        // Настройка колонок
        $this->cols = array(

            # ID
            'id' => array(
                'title' => 'Идентификатор',
                'db' => 'n.id',
                'visible' => false,
            ),

            # Реальная дата
            'date_real' => array(
                'title' => 'Дата создания',
                'db' => 'n.created_at',
                'visible' => false,
            ),

            # Код
            'code' => array(
                'title' => __('grid.code'),
                'db' => 'n.id',
                'width' => '8%',
                'className' => 'text-center',

                'priority' => '1',

                // query sql
                'search_op' => 'like',
            ),

            # Заголовок
            'title' => array(
                'db' => 'n.title',
                'title' => __('notifications::grid.title'),
                'className' => '',
                // 'width' => '15%',
                'render' => 'text',
                'priority' => '2',

                // query sql
                'search_op' => 'like'
            ),

            # Уведомление
            'text' => array(
                'db' => 'n.text',
                'title' => __('notifications::grid.text'),
                'className' => '',
                'render' => 'text',
                'priority' => '3',

                // query sql
                'search_op' => 'like'
            ),

            # Дата уведомления
            'date' => array(
                'title' => __('notifications::grid.date'),
                'label' => 'Выбрать дату',
                'db' => 'n.created_at',
                'type' => 'date_range',
                //'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                'width' => '10%',
                'className' => 'text-center',

                // query sql
                'search_op' => 'date_range',
            ),

            # Дата прочтения
            'read_date' => array(
                'title' => __('notifications::grid.date.read'),
                'db' => 'n.read_date',
                'type' => 'date_range',
                //'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                'width' => '10%',
                'className' => 'text-center',

                // query sql
                'search_op' => 'date_range',
            ),

            # Раздел
            'module' => array(
                'title' => __('notifications::grid.module'),
                'db' => 'n.module',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getTypes(true), __('grid.all')),
                ),
                'width' => '10%',
                'className' => 'text-center',

                'encode' => false,
                'sortable' => false,
                'replace' => $this->getTypes(),
                'excel_hidden' => true,

                // query sql
                'search_op' => 'equal'
            ),

            # Статус
            'status' => array(
                'title' => __('grid.status'),
                'db' => 'n.status',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getStatus(true), __('grid.all')),
                ),
                'width' => '8%',
                'className' => 'text-center',

                'encode' => false,
                'sortable' => true,
                'replace' => $this->getStatus(),
                'excel_hidden' => true,

                // query sql
                'search_op' => 'equal',
            ),

            # Действия
            'action' => array(
                //'title' => __('grid.action'),
                //'title' => 'Текст123', // Если будет указан то будет выведен текст, а не иконка
                //'tooltip' => 'Кастомный текст подсказки', // WIP
                'type' => 'actions',
                // 'width' => '1%', // Для actions всегда стоит 1% в фильтрах
                'className' => 'text-center',
                //'icon' => 'feathersettings',
                //'icon' => false,

                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,

                // query sql
                'search_op' => false,
            )

        );
        $this->render_filter_toolbar = true;
    }

    /**
     * Получение типов уведомлений
     *
     * @param bool $select
     * @return void
     */
    protected function getTypes($select = false)
    {
        // $rows_select = [];
        $rows = NotificationsSupport::getNotificationModule();
        $rows_select = NotificationsSupport::getNotificationModule();

        return ($select ? $rows : $rows_select);
    }

    /**
     * Получение статуса уведомлений
     *
     * @param bool $select
     * @return array
     */
    protected function getStatus($select = false)
    {
        $rows_select = [];
        $rows = [
            1 => __('notifications::admin.status.news'),
            2 => __('notifications::admin.status.readables'),
        ];

        $rows_select[1] = '<span class="badge rounded-pill badge-light-danger d-block">' . __('notifications::admin.status.new') . '</span>';
        $rows_select[2] = '<span class="badge rounded-pill badge-light-success d-block">' . __('notifications::admin.status.readable') . '</span>';

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
        // OLD
        $row['action'] = view('notifications::manager.grid.action', [
            //'row' => $row,
            'show' => router("admin.notifications.redirect", $row['id']),
        ])->render();

        //$row['action'] = view('grid::layouts.actions', [
        //    'row' => $row,
        //    'module' => $this->namespace,
        //    'actions' => [
        //        //'show' => true,
        //        //'edit'  => true,
        //        //'delete'  => true,
        //        //'export'  => true,
        //    ],
        //])->render();

        return $row;
    }
}
