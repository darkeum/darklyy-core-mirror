<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Orgs\UI\WEB\Grids;

use Boot\Models\UsersSign;
use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class SignsGrid extends GridModern
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        ];

        $this->title = __('orgs::general.list.sign');

        $this->db = DB::table('users_sign as us')->whereNull("us.deleted_at");

        $this->cols = [

            'id' => [
                'visible' => false,
                'db' => 'us.id',
            ],

            'numb' => [
                'db' => 'us.id',
                'title' => '№',
                'width' => "1%",
                'search_op' => 'like',
                'className' => 'text-center',
            ],

            'uid' => [
                'db' => 'us.uid',
                'title' => 'Ответственный',
                'width' => "10%",
                'encode' => false,
                'replace' => $this->getUid("users_sign"),
                'options' => [
                    'value' => $this->setValue($this->getUid('users_sign'), 'Все'),
                ],

                'search_op' => 'equal',
                'type' => 'select',
            ],

            'type' => [
                'title' => 'Тип',
                'db' => 'us.type',
                'width' => "8%",
                'render' => 'text',
                'encode' => false,
                'replace' => $this->getType(),
                'options' => [
                    'value' => $this->setValue($this->getType(), 'Все'),
                ],
                'search_op' => 'equal',
                'type' => 'select',
            ],

            'base' => [
                'title' => 'На основании',
                'db' => 'us.base',
                'width' => "5%",
                'encode' => false,
                'replace' => $this->getBase(),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getBase(), 'Все'),
                ],
                'search_op' => 'equal',
                'className' => 'text-center',
            ],

            'base_numb' => [
                'db' => 'us.base_numb',
                'title' => '№ приказа',
                'width' => "1%",
                'search_op' => 'like',
                'className' => 'text-center',
            ],

            'base_date' => [
                'title' => 'Дата регистрации',
                'db' => 'us.base_date',
                'null' => 'Не указано',
                'width' => "3%",
                'format_date' => "d.m.Y",
                'options' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
                'type' => 'date_range',

                'className' => 'text-center',
            ],

            'date_start' => [
                'title' => 'Период действия с',
                'db' => 'us.date_start',
                'null' => 'Не указано',
                'width' => "3%",
                'format_date' => "d.m.Y",
                'options' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
                'type' => 'date_range',
                'className' => 'text-center',
            ],

            'date_end' => [
                'title' => 'Период действия до',
                'db' => 'us.date_end',
                'null' => 'Не указано',
                'width' => "3%",
                'format_date' => "d.m.Y",
                'options' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
                'type' => 'date_range',
                'className' => 'text-center',
            ],

            'org' => [
                'title' => 'Организация',
                'null' => 'Не указано',
                'db' => 'us.org',
                'width' => "10%",
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getOrg("users_sign"),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getOrg("users_sign"), 'Все'),
                ],
                'search_op' => 'equal',
                'className' => 'text-center',
            ],

            'type_post' => [
                'title' => 'Тип должности',
                'db' => 'us.type_post',
                'width' => "5%",
                'encode' => false,
                'replace' => $this->getTypePost(),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getTypePost(), 'Все'),
                ],
                'search_op' => 'equal',
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
                'search_op' => false,
            ]
        ];
        $this->render_filter_toolbar = true;
    }

    protected function getTypePost()
    {
        return getTypePost();
    }

    protected function getBase()
    {
        return getTypeBase();
    }

    protected function getType()
    {
        return UsersSign::getType();
    }

    protected function parseRow($row)
    {
        $row["numb"] = $row["id"];
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'orgs.sign',
            'actions' => [
                'edit' => true,
            ],
        ])->render();

        return $row;
    }
}
