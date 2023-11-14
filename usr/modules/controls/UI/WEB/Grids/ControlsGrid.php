<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (15.03.2023 6:21)
*/
namespace Controls\UI\WEB\Grids;

use Boot\Support\Facades\DB;
use Boot\System\Grid\GridModern;
use Control\Models\Control;
use Organizations\Model\Org;

class ControlsGrid extends GridModern {

    /**
     * Пространство имен
     *
     * @var string
     */
    protected string $namespace = 'control';
    private array $users = [];
    private array $organization = [];
    private array $supply_o = [];
    private array $objName = [];

    /**
     * Действие на двойной клик
     *
     * @var string
     */
    protected string $opts_type = "open";

    protected function init()
    {
        // Заголовок таблицы
        $this->title = __('controls::grid.list');

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
        $this->users = $this->getShortUserName();
        $this->organization = $this->getValues('organizations', 'name', 'name');
        $this->supply_o  = $this->getValues('supply_order');
        $this->objName = $this->getValues('object', 'name');

        // Настройка колонок
        $this->cols = [
            'id' => [
                'visible' => false,
                'db' => 'c.id'
            ],
            'supply_o' => [
                'visible' => false,
                'db' => 'c.supply_o',
            ],

            'numb' => [
                'title' => __('controls::grid.numb'),
                'db' => 'c.id',
                'className' => 'text-center',
                'search_op' => 'like',
                'width' => '1%',
            ],

            #Контрагент
            'old_name' => [
                'title' => __('controls::grid.old_name'),
                'db' => 'o.name',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->organization, 'Все'),
                ],
                'className' => 'text-center',
                'render' => 'text',
                'null' => trans('controls::grid.null'),
                'search_op' => 'like',
            ],

            #Объект
            'object_name' => [
                'title' => __('controls::grid.object_name'),
                'db' => 'obj.name',
                'width' => '30%',
                'null' => trans('controls::grid.null'),
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->objName, 'Все'),
                ],
                //'className' => 'text-center',
                'render' => 'text',
                'encode' => false,
                'search_op' => 'like',
            ],

            #Счёт поставщика
            'so_numb' => [
                'title' => __('controls::grid.so_numb'),
                'db' => 'so.numb',
                'null' => trans('controls::grid.null'),
                'className' => 'text-center',
                'encode' => false,
                'search_op' => 'like',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->supply_o, 'Все'),
                ],
            ],

            #Ответственный
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

            #Дата
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

            #Дата оплаты
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

            #Сумма
            'total' => [
                'title' => __('controls::grid.total'),
                'db' => 'c.total',
                //'render' => 'text',
                'className' => 'text-center',
                'null' => trans('controls::grid.null'),
                'search_op' => 'like',
            ],

            #Статус
            'status_view' => [
                'title' => __('grid.status'),
                'db' => 'c.status',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getStatus(), __('grid.all')),
                ],
                //'render' => 'text',
                'className' => 'text-center',
                'encode' => false,
                'replace' => $this->getStatus('render'),
            ],

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

    private function getDB() {
        $org = Org::where("director", auth()->id())->first();
        return DB::table('control as c')
            ->leftJoin("organizations as o", "o.id", "=", "c.oid")
            ->leftJoin("supply_order as so", "so.id", "=", "c.supply_o")
            ->leftJoin("object as obj", "obj.id", "=", "c.object")
            ->where('so.org', $org->id ?? -1);
    }

    /**
     * Подстановка и замена элементов в строке (подмена ячеек)
     *
     * @param array $row
     * @return array
     */
    protected function parseRow($row): array
    {
        $row['uid'] = $this->users[$row['uid']];

        $row['total'] = $row['total'] . ' руб.';

        $customLink =  '<a
                    onclick="confirmWithoutLooking(\''. route('admin.controls.controls_end', $row['id']) .'\')"
                    data-bs-original-title="'. trans('controls::grid.status_confirm') .'"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                ><i class="feather-check"></i></a>';

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'controls',
            'actions' => [
                'show' => true, // Может быть кастомной ссылкой простой string
                $customLink,
                //'destroy' => true,

            ],
        ])->render();

        $row['so_numb'] = view('controls::manager.main.grid.soNumb', [
            'text' => $row['so_numb'],
            'url' => url('administration/supply/order_detail', $row['supply_o']),
        ])->render();

        return $row;
    }

    protected function getStatus($type = 'select')
    {
        $rows_text = Control::getStatus();
        unset($rows_text[0]);

        $rows[1] = "<span class='d-block badge bg-warning rounded-pill' data-toggle='tooltip'>$rows_text[1]</span>";
        $rows[2] = "<span class='d-block badge bg-danger rounded-pill' data-toggle='tooltip'>$rows_text[2]</span>";
        $rows[3] = "<span class='d-block badge bg-success rounded-pill' data-toggle='tooltip'>$rows_text[3]</span>";

        return $type == "select" ? $rows_text : $rows;
    }

    // Получаем короткое ФИО 'Пупкин В.В'
    private function getShortUserName(): array {
        $contactU = \Illuminate\Support\Facades\DB::table('users')
            ->whereIn('id',DB::table('control')->distinct()->pluck('uid'))
            ->get();

        return $this->getFilterParam($contactU)->transform(function ($user) {
            return user_short_name($user);
        })->toArray();
    }
}