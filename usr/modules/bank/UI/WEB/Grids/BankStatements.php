<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Bank\UI\WEB\Grids;

use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class BankStatements extends GridModern
{
    protected $db_type = 'db';

    protected function init()
    {
        // Заголовок таблицы
        $this->title = trans('bank::main.statements.list');

        $this->options = array(
            'sortname' => 'bu.id',
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

        $this->db = DB::table('bank_upload as bu');

        //dd($this->getUid('bank_upload'));

        $this->cols = array(

            'id' => array(
                'db' => 'bu.id',
                'visible' => false,
            ),

            'real_file' => array(
                'db' => 'bu.file',
                'visible' => false,
            ),

            'numb' => array(
                'title' => '№',
                'label' => '#',
                'db' => 'bu.id',
                'width' => '1%',
                'className' => 'text-center',

                'search_op' => 'like'
            ),

            'date' => array(
                'title' => 'Дата выписки',
                'label' => 'Дата выписки',
                'db' => 'bu.date',
                'type' => 'date_range',
                'format_date' => "d.m.Y",
                'options' => $this->dateRangeSearch(),

                'width' => '2%',
                'className' => 'text-center',

                'search_op' => 'date_range',
            ),

            'date_add' => array(
                'title' => 'Дата загрузки',
                'label' => 'Дата загрузки',
                'db' => 'bu.date_add',
                'type' => 'date_range',
                'format_date' => "d.m.Y",
                'options' => $this->dateRangeSearch(),
                'width' => '2%',
                'className' => 'text-center',

                'search_op' => 'date_range',
            ),

            'balance_in' => array(
                'title' => 'Начальный остаток',
                'label' => 'Начальный остаток',
                'db' => 'bu.balance_in',
                'width' => '2%',
                'className' => 'text-center',

                'search_op' => 'like'
            ),

            'balance_out' => array(
                'title' => 'Конечный остаток',
                'label' => 'Конечный остаток',
                'db' => 'bu.balance_out',
                'width' => '2%',
                'className' => 'text-center',

                'search_op' => 'like'
            ),

            'uid' => array(
                'title' => 'Ответственный',
                'label' => 'Ответственный',
                'db' => 'bu.uid',
                'width' => '1%',
                'className' => 'text-center',

                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid('bank_upload'),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getUid('bank_upload'), 'Все'),
                ),

                'search_op' => 'equal',
            ),

            'org' => array(
                'title' => 'Организация',
                'label' => 'Организация',
                'db' => 'bu.org',
                'width' => '1%',
                'className' => 'text-center',

                'encode' => false,
                'replace' => $this->getOrg("bank_upload"),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getOrg("bank_upload"), 'Все'),
                ),

                'search_op' => 'equal',
            ),

            'action' => array(
                'type' => 'actions',
                'width' => '0%',
                'className' => 'text-center px-1',

                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,

                'search_op' => false,
            ),
        );
        $this->render_filter_toolbar = true;
    }

    protected function getStatus($type = 'select')
    {
        $rows = [];
        if ($type == 'select') {

            $rows[0] = "Отключена";
            $rows[1] = "Опубликована";

        } elseif ($type == 'render') {

            $rows[0] = "<span class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Отключена'></span>";
            $rows[1] = "<span class='far fa-check-circle text-success fa-md' data-toggle='tooltip' title='Опубликована'></span>";

        }
        return $rows;
    }

    protected function getPayIn()
    {
        $rows = [];
        $rows[0] = 'Списание';
        $rows[1] = 'Поступление';
        return $rows;
    }


    protected function parseRow($row)
    {
        $row['numb'] = 'BU-' . $row['id'];

        $row['action'] = (string)view('bank::manager.grid.download', [
            'file' => url($row['real_file']),
            'statement' => route('admin.bank.statements.download', $row['id']),
        ]);

        return $row;
    }
}
