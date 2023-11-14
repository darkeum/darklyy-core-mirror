<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander
*/

namespace Calls\UI\WEB\Grids;

use Boot\System\Grid\GridModern;
use Illuminate\Support\Facades\DB;

class Contacts extends GridModern
{

    /**
     * Пространство имен
     *
     * @var string
     */
    protected $namespace = 'users_phoneid';

    /**
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        $this->title = __('calls::lang.grid.title');

        $this->options = [
            'sort' => [
                'name' => 'code',
                'order' => 'DESC',
            ]
        ];

        $this->nav = [
            'export' => true,
            'export.detail' => [
                'excel' => true,
                'pdf' => true,
                'print' => true,
                'copy' => true,
                'csv' => false,
            ]
        ];

        $this->db = DB::table('users_phoneid as upID');

        // Получаем список пользователей для таблицы users_phoneid
        $contactU = DB::table('users')
            ->whereIn('id',DB::table('users_phoneid')->pluck('uid'))
            ->get();

        // Получаем короткое ФИО `Пупкин В.В`
        $userName = $this->getFilterParam($contactU)->transform(function ($user) {
            return '#' . $user->id . ' - ' . user_short_name($user);
        })->toArray();

        $userSignature = $this->getFilterParam($contactU, 'id', 'signature')->toArray();

        $this->cols = [

            'id' => [
                'title' => '#',
                'db' => 'upID.id',
                'width' => '1%',
                'className' => 'text-center',
            ],

            'uid' => [
                'title' => __('calls::lang.grid.uid'),
                'db' => 'upID.uid',
                'className' => 'text-center',
                'replace' => $userName,
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($userName, 'Все'),
                ],
            ],

            'phoneid' => [
                'title' => __('calls::lang.grid.phoneid'),
                'db' => 'upID.phoneid',
                'className' => 'text-center',
            ],

            'signature' => [
                'title' => trans('calls::lang.grid.signature'),
                'db' => 'upID.uid',
                'replace' => $userSignature,
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($userSignature, 'Все'),
                ],
                'className' => 'text-center',
            ],

            'action' => [
                'type' => 'actions',
                'width' => '1%',
                'className' => 'text-center',
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,

                // query sql
                'search_op' => false,
            ],
        ];
        $this->render_filter_toolbar = true;
    }

    protected function parseRow($row)
    {
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'contacts',
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
