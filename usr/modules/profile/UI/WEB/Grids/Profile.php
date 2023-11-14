<?php

namespace Profile\UI\WEB\Grids;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;
use Organizations\Model\Organizations;

class Profile extends Grid
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->options = array(
            'sortname' => 'p.id',
            'sortorder' => 'DESC',
        );

        // $this->nav = array(
        //     'excel' => true,
        //     'exceltext' => 'Сохранить в Excel',
        //     'trash' => true,
        //     'trashtype' => 'null',
        //     'trashtext' => 'Показывать удаленные',
        //     'trashfield' => 'r.deleted_at',
        // );

        $this->db = DB::table('users as ');

        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' => 'p.id',
            ),


            'ab' => array(
                'label' => 'Действия',
                'width' => 50,
                'hidden' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
            ),
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


    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];
        // $r['ab'] = '<div class="btn-group">
        // <a href="'.router("admin.profile.permission.edit", $r['id']).'" target="_blank" class="btn btn-sm btn-primary" role="button">Редактировать</a>
        // <a href="'.router("admin.profile.permission.destroy", $r['id']).'" target="_blank" class="btn btn-sm btn-danger" role="button">Удалить</a>
        // </div>';
        return $r;
    }
}
