<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (18.03.2023 7:18)
*/
namespace Controls\UI\WEB\Grids;

use Boot\System\Grid\GridModern;
use Controls\Models\Control;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PayGrid extends GridModern {

    protected string $namespace = 'control';
    private array $users = [];

    protected function init()
    {
        // Заголовок таблицы
        $this->title = __('controls::grid.pay');

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

        // Подключение к необходимой таблице
        $this->db = $this->getDB();
        $organization = $this->getValues('organizations', 'name', 'name');
        $supply_o = $this->getValues('supply_order');
        $sales = $this->getValues('sales');
        $object = $this->getValues('object', 'name');
        $this->users = $this->getShortUserName();

        $this->cols = [

            'id' => [
                'visible' => false,
                'db' => 'c.id'
            ],
            'sales_real' => [
                'visible' => false,
                'db' => 's.id',
            ],
            'supply_real' => [
                'visible' => false,
                'db' => 'so.id',
            ],
            'oid_real' => [
                'visible' => false,
                'db' => 'c.oid',
            ],
            'object_id' => [
                'visible' => false,
                'db' => 'c.object',
            ],
            'real_status' => [
                'visible' => false,
                'db' => 'c.status',
            ],

            # №
            'numb' => [
                'title' => __('controls::grid.numb'),
                'db' => 'c.id',
                'className' => 'text-center',
                'search_op' => 'like',
                'width' => '1%',
            ],

            # Контрагент
            'oid' => [
                'title' => __('controls::grid.old_name'),
                'db' => 'o.name',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($organization, 'Все'),
                ],
                'className' => 'text-center',
                'render' => 'text',
                //'width' => '10%',
                'null' => trans('controls::grid.null'),
                'search_op' => 'like',
                'encode' => false,
            ],

            # Объект
            'object' => [
                'title' => 'Объект',
                'db' => 'obj.name',
                'null' => trans('controls::grid.null'),
                'className' => 'text-center',
                'render' => 'text',
                'encode' => false,
                'search_op' => 'like',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($object, 'Все'),
                ],
                //'width' => '10%',
            ],

            # Счет
            'supply_o' => [
                'title' => 'Счет',
                'db' => 'so.numb',
                'null' => trans('controls::grid.null'),
                'className' => 'text-center',
                'encode' => false,
                'search_op' => 'like',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($supply_o, 'Все'),
                ],
                //'width' => '10%',
            ],

            # Продажа
            'sales' => [
                'title' => 'Продажа',
                'db' => 's.numb',
                'encode' => false,
                //'width' => '10%',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($sales, 'Все'),
                ],
                'className' => 'text-center',
                'null' => trans('controls::grid.null'),
            ],

            # Ответственный
            'uid' => [
                'title' => __('controls::grid.uid'),
                'db' => 'c.uid',
                'className' => 'text-center',
                'encode' => false,
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->users, 'Все'),
                ],
                'null' => trans('controls::grid.null'),
                'search_op' => 'equal',
            ],

            # Дата
            'date' => [
                'title' => __('controls::grid.date'),
                'db' => 'c.date',
                'type' => 'date_range',
                'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                //'width' => '10%',
                'className' => 'text-center',

                // query sql
                'search_op' => 'date_range',
            ],

            # Дата оплаты
            'plan_pay_date' => [
                'title' => __('controls::grid.plan_pay_date'),
                'db' => 'c.plan_pay_date',
                'type' => 'date_range',
                'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                //'width' => '10%',
                'className' => 'text-center',

                // query sql
                'search_op' => 'date_range',
            ],

            # Сумма
            'total' => [
                'title' => __('controls::grid.total'),
                'db' => 'c.total',
                'className' => 'text-center',
                'null' => trans('controls::grid.null'),
                'search_op' => 'like',
            ],

            # Оплата
            'pay' => [
                'title' => 'Оплата',
                'db' => 'c.pay',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getStatusPay(), __('grid.all')),
                ],
                'className' => 'text-center',
                'encode' => false,
                'replace' => $this->getStatusPay('render'),
            ],

            # Действия
            'action' => [
                'type' => 'actions',
                'className' => 'text-center px-1',
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'search_op' => false,
                'width' => '1%',
            ],
        ];

        $this->render_filter_toolbar = true;
    }

    /**
     * Подстановка и замена элементов в строке (подмена ячеек)
     *
     * @param array $row
     * @return array
     */
    protected function parseRow($row): array
    {
        $row['oid'] = view('controls::manager.grid.link', [
            'text' => $row['oid'],
            'url' => url('administration/module/organizations', $row['oid_real']),
        ])->render();

        $row['object'] = view('controls::manager.grid.link', [
            'text' => $row['object'],
            'url' => url('administration/object/view', $row['object_id']),
        ])->render();

        $row['supply_o'] = view('controls::manager.grid.badge', [
            'title' => 'Открыть счёт',
            'text' => $row['supply_o'],
            'url' => url('administration/module/supply/order_detail', $row['supply_real'])
        ])->render();

        $row['sales'] = view('controls::manager.grid.badge', [
            'text' => $row['sales'],
            'url' => url('administration/module/supply/order_detail', $row['supply_real'])
        ])->render();

        $row['total'] = number_format($row['total'],2,'.',' ') .' руб.';

        $row['uid'] = $this->users[$row['uid']];

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'controls.pay',
            'actions' => [
                'show' => true, // Может быть кастомной ссылкой простой string
                'destroy' => true,
                //'destroy' => true,

            ],
        ])->render();

        return $row;
    }

    /**
     * @return Builder
     */
    private function getDB(): Builder
    {
        return  DB::table($this->namespace, 'c')
            ->leftJoin('supply_order as so', 'so.id', '=', 'c.supply_o')
            ->leftJoin('organizations as o', 'o.id', '=', 'c.oid')
            ->leftJoin('users as u', 'u.id', '=', 'c.uid')
            ->leftJoin('object as obj', 'obj.id', '=', 'so.object')
            ->leftJoin('sales as s', 's.id', '=', 'c.sales_id')
            ->where('c.status', 3);
    }

    /**
     * @param string $type
     * @return string[]
     */
    protected function getStatusPay(string $type = 'select'): array
    {
        $rows_text = Control::getStatusPay();
        $rows = [
            '0' => "<span class='badge rounded-pill bg-warning d-block' data-toggle='tooltip'>$rows_text[0]</span>",
            '1' => "<span class='badge rounded-pill bg-success d-block' data-toggle='tooltip'>$rows_text[1]</span>",
            '2' => "<span class='badge rounded-pill bg-danger d-block' data-toggle='tooltip'>$rows_text[2]</span>",
            '3' => "<span class='badge rounded-pill bg-warning d-block' data-toggle='tooltip'>$rows_text[3]</span>",
        ];
        return $type == "select" ? $rows_text : $rows;
    }


    // Получаем короткое ФИО 'Пупкин В.В'
    private function getShortUserName(): array {
        $contactU = DB::table('users')
            ->whereIn('id',DB::table('control')->distinct()->pluck('uid'))
            ->get();

        return $this->getFilterParam($contactU)->transform(function ($user) {
            return user_short_name($user);
        })->toArray();
    }
}