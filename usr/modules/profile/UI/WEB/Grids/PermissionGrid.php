<?php

namespace Profile\UI\WEB\Grids;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;
use Organizations\Model\Organizations;

class PermissionGrid extends Grid
{
    protected $db_type = 'db';

    protected function init()
    {
        // Заголовок таблицы
        $this->title = __('objects::lang.table.title');

        // Опции таблицы (в данном случае сортировка)
        $this->options = [
            'sort' => [
                'name' => 'p.id',
                'order' => 'DESC'
            ],
        ];

        // $this->nav = array(
        //     'excel' => true,
        //     'exceltext' => 'Сохранить в Excel',
        //     'trash' => true,
        //     'trashtype' => 'null',
        //     'trashtext' => 'Показывать удаленные',
        //     'trashfield' => 'r.deleted_at',
        // );

        $this->db = DB::table('permissions as p');

        $this->cols = array(

            'id' => array(
                'visible' => false,
                'db' => 'p.id',
            ),

            'numb' => array(
                'db' => 'p.id',
                'title' => '№',
                // 'label' => '№',
                'width' => '10%',
                'search_op' => 'like'
            ),

            'name' => array(
                'db' => 'p.name',
                'title' => 'Наименование',
                // 'label' => 'Наименование',
                'width' => '30%',
                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            // 'uid' => array(
            //     'label' => 'Ответственный',
            //     'db' => 'support.uid',
            //     'width' => 30,
            //     'hidden' => false,
            //     'encode' => false,
            //     'sortable' => false,
            //     'replace' => $this->getUid('support'),
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => $this->setValue($this->getUid('support'), 'Все'),
            //     ),
            //     'search_op' => 'equal',
            // ),

            // 'type' => array(
            //     'label' => 'Вид контрагента',
            //     'db' => 'o.type',
            //     'width' => 30,
            //     'hidden' => false,
            //     'encode' => false,
            //     'replace' => $this->getType(),
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => $this->setValue($this->getType(), 'Все'),
            //     ),
            //     'search_op' => 'equal',
            // ),

            // 'date' => array(
            //     'label' => 'Дата',
            //     'db' => 'bd.date',
            //     'width' => 25,
            //     'format_date' => "d.m.Y",
            //     'searchoptions' => $this->dateRangeSearch(),
            //     'search_op' => 'date_range',
            // ),


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
        );

        $this->render_filter_toolbar = true;
    }

    // protected function getStatus($type = 'select')
    // {
    //     $rows = [];
    //     if ($type == 'select') {
    //         $rows[0] = "Отключена";
    //         $rows[1] = "Опубликована";
    //     } elseif ($type == 'render') {
    //         $rows[0] = "<span class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Отключена'></span>";
    //         $rows[1] = "<span class='far fa-check-circle text-success fa-md' data-toggle='tooltip' title='Опубликована'></span>";
    //     }
    //     return $rows;
    // }

    // protected function getClientType()
    // {
    //     $rows = Organizations::getClientType();
    //     return $rows;
    // }


    protected function parseRow($row)
    {
        $row["numb"] = $row["id"];

        // $row['action'] = '<div class="btn-group">
        //     <a href="' . router("admin.profile.permission.edit", $row['id']) . '" target="_blank" class="btn btn-sm btn-primary" role="button">Редактировать</a>
        //     <a href="' . router("admin.profile.permission.destroy", $row['id']) . '" target="_blank" class="btn btn-sm btn-danger" role="button">Удалить</a>
		// </div>';

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
