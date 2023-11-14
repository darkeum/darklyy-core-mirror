<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (28.09.2023 16:24)
*/

namespace EconomicNew\UI\WEB\Grids;

use Boot\Support\Facades\DB;
use Boot\System\Grid\GridModern;
use EconomicNew\Models\ActReconciliation;
use Illuminate\Database\Query\Builder;
use Orgs\Models\Org;

class ActGrid extends GridModern
{

    /**
     * Пространство имен
     *
     * @var string
     */
    protected string $namespace = 'act_reconciliation';

    /**
     * Действие на двойной клик
     *
     * @var string
     */
    protected string $opts_type = "open";

    protected function init()
    {
        $this->title = trans('economicNew::lang.title');

        // Опции таблицы (сортировка)
        $this->options = [
            'sort' => [
                'name' => 'id',
                'order' => 'DESC',
            ]
        ];

        // Опции панели навигации
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

        $this->db = $this->getDB();
        $organization = $this->getValues('organizations', 'name', 'name');
        $users = $this->getShortUserName();

        $this->cols = [
            'id' => [
                'visible' => false,
                'db' => 'ar.id'
            ],
            'id_view' => [
                'title' => 'ID',
                'db' => 'ar.id',
                'className' => 'text-center',
                'search_op' => 'like',
                'width' => '1%',
            ],
            'date' => [
                'title' => 'Дата',
                'db' => 'ar.date',
                'type' => 'date_range',
                'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                //'width' => '10%',
                'className' => 'text-center',
                'search_op' => 'date_range',
            ],
            'numb' => [
                'title' => '№',
                'db' => 'ar.numb',
                'className' => 'text-center',
                'search_op' => 'like'
            ],
            'oid' => [
                'title' => 'Контрагент',
                'db' => 'o.name',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($organization, 'Все'),
                ],
                'className' => 'text-center',
                'render' => 'text',
                'null' => 'Отсутствует',
                'search_op' => 'like',
            ],
            'date_start' => [
                'title' => 'Дата начала',
                'db' => 'ar.date_start',
                'type' => 'date_range',
                'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                //'width' => '10%',
                'className' => 'text-center',
                'search_op' => 'date_range',
            ],
            'date_end' => [
                'title' => 'Дата окончания',
                'db' => 'ar.date_end',
                'type' => 'date_range',
                'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                //'width' => '10%',
                'className' => 'text-center',
                'search_op' => 'date_range',
            ],
            'org' => [
                'title' => 'Огранизация',
                'db' => 'ar.org',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($organization, 'Все'),
                ],
                'replace' => $this->getOrgType(),
                'className' => 'text-center',
                'render' => 'text',
                'search_op' => 'like',
            ],
            'uid' => [
                'title' => 'Ответственный',
                'db' => 'ar.uid',
                'className' => 'text-center',
                'encode' => false,
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($users, 'Все'),
                ],
                'null' => 'Отсутствует',
                'search_op' => 'equal',
            ],
            'active' => [
                'title' => 'Проведенность',
                'db' => 'ar.active',
                'className' => 'text-center',
                'replace' => $this->getAutoTypes(),
                'encode' => false,
            ],
            'action' => [
                'type' => 'actions',
                'className' => 'text-center',
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,
                'search_op' => false,
            ],
        ];

        $this->render_filter_toolbar = true;
    }

    protected function parseRow($row)
    {
        $userName = $this->getShortUserName();
        $row['uid'] = $userName[$row['uid']] ?? $row['uid'];

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'economic_new.act',
            'actions' => [
                'destroy' => true,
                'edit' => true,
            ],
        ])->render();
        return $row;
    }

    private function getDB(): Builder
    {
        return DB::table('act_reconciliation', 'ar')
            ->select('*')
            ->leftJoin('organizations as o', 'o.id', '=', 'ar.oid');
    }

    // Получаем короткое ФИО 'Пупкин В.В'
    private function getShortUserName(): array
    {
        $contactU = DB::table('users')
            ->get();

        return $this->getFilterParam($contactU)->transform(function ($user) {
            return user_short_name($user);
        })->toArray();
    }

    /**
     * Получение наименование организаций
     * @return array
     */
    private function getOrgType(): array
    {
        $result = [];
        DB::table('org', 'r')
            ->select('r.id', 'o.name')
            ->leftJoin('organizations as o', 'r.oid', '=', 'o.id')
            ->orderBy('r.id')
            ->each(function (object $object) use(&$result) {
                $result[$object->id] = $object->name;
            });

        return $result;
    }

    protected function getAutoTypes($select = false): array
    {
        $rows_select = [
            0 => 'Не проведенная',
            1 => 'Проведенная',
        ];

        $rows = [
            0 => "<span class='badge rounded-circle bg-danger'><i class='feather-check'></i></span>",
            1 => "<span class='badge rounded-circle bg-success'><i class='feather-check'></i></span>",
        ];

        return $select ?  $rows_select :  $rows;
    }
}