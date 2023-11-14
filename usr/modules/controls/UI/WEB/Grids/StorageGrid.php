<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (30.03.2023 5:34)
*/
namespace Controls\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;
use Controls\Models\ControlStorage;

class StorageGrid extends GridModern {

    private string $namespace = 'control_storage';

    protected function init()
    {
        $this->title = __('controls::grid.storage_list');

        $this->options = [
            'sort' => [
                'name' => 'id',
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
            ],
        ];

        $this->db = DB::table($this->namespace . ' as c');

        $this->cols = [
            'id' => [
                'visible' => false,
                'db' => 'c.id'
            ],
            'numb' => [
                'title' => __('controls::grid.numb'),
                'db' => 'c.id',
                'className' => 'text-center',
                'width' => '1%',
                'search_op' => 'like',
            ],
            #Заголовок
            'title' => [
                'title' => __('controls::grid.title'),
                'db' => 'c.title',
                'null' => 'Отсутствует',
                'render' => 'text',
                'search_op' => 'like',
            ],
            #Дата
            'date' => [
                'title' => __('controls::grid.date'),
                'db' => 'c.date',
                'type' => 'date_range',
                'format_date' => 'd.m.Y',
                'options' => $this->dateRangeSearch(),
                'width' => '10%',
                'className' => 'text-center',

                // query sql
                'search_op' => 'date_range',
            ],
            #Статус
            'status_view' => [
                'title' => __('controls::grid.status'),
                'db' => 'c.status',
                'type' => 'select',
                'options' => [
                    'value' => $this->setValue($this->getStatus(), __('grid.all')),
                ],
                'width' => '10%',
                'className' => 'text-center',

                'encode' => false,
                'sortable' => false,
                'replace' => $this->getStatus('render'),
                'excel_hidden' => true,
            ],
            #Действия
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

    /**
     * Подстановка и замена элементов в строке (подмена ячеек)
     *
     * @param array $row
     * @return array
     */
    protected function parseRow($row): array
    {
        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => 'controls.storage',
            'actions' => [
                'show' => true, // Может быть кастомной ссылкой простой string
                'print' => url('administration/module/sales/order', $row['id']),
                //'destroy' => true,
            ],
        ])->render();
        return $row;
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getStatus(string $type = 'select'): array
    {
        $rows_text = ControlStorage::getStatus();

        $rows[1] = "<span class='badge rounded-pill badge-light-warning d-block' data-toggle='tooltip'>$rows_text[1]</span>";
        $rows[2] = "<span class='badge rounded-pill badge-light-success d-block' data-toggle='tooltip'>$rows_text[3]</span>";
        $rows[3] = "<span class='badge rounded-pill badge-light-danger d-block' data-toggle='tooltip'>$rows_text[2]</span>";

        return $type == 'select'? $rows_text : $rows;
    }
}