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

use Bank\Models\BankDetail;
use \Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class BankIndex extends GridModern
{
    protected string $db_type = 'db';
    protected string $namespace = 'bank';

    protected function init()
    {

        // Заголовок таблицы
        $this->title = trans('bank::main.operations.list');

        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );

        // $this->nav = array(
        //     'excel' => true,
        //     'exceltext' => 'Сохранить в Excel',
        //     'trash' => true,
        //     'trashtype' => 'null',
        //     'trashtext' => 'Показывать удаленные',
        //     'trashfield' => 'r.deleted_at',
        // );

        $this->db = DB::table('bank_detail as bd')->
        leftJoin("organizations_bank as ob", "ob.id", "=", "bd.organizations_bank_id")->
        leftJoin("organizations as o1", "o1.id", "=", "bd.payer")->
        leftJoin("organizations as o2", "o2.id", "=", "bd.recipient");

        $this->cols = array(

            'id' => array(
                'title' => '№',
                'label' => '#',
                'db' => 'bd.id',
                'width' => '1%',
                'className' => 'text-center',
                //'visible' => false,

                'null' => "Отсутствует",
                'search_op' => 'like'
            ),
            'organizations_bank_id' => array(
                'db' => 'bd.organizations_bank_id',
                'visible' => false
            ),

            'numb' => array(
                'title' => 'Банк. номер',
                'label' => 'Банковский номер',
                'db' => 'bd.numb',
                'width' => '1%',
                'className' => 'text-center',

                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'pay_in' => array(
                'title' => 'Операция',
                'label' => 'Тип',
                'db' => 'bd.pay_in',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getPayIn(), 'Все'),
                ),
                'width' => '1%',
                'className' => 'text-center',
                'render' => 'text',
                'popover' => true,

                'encode' => false,
                'replace' => $this->getPayIn(),
                'search_op' => 'equal',
            ),

            'org' => array(
                'title' => 'Организация',
                'label' => 'Организация',
                'db' => 'bd.org',
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getOrg("bank_detail"), 'Все'),
                ),
                'width' => '1%',
                'className' => 'text-center',
                'render' => 'text',
                'popover' => 'left',

                'encode' => false,
                'replace' => $this->getOrg("bank_detail"),
                'search_op' => 'equal',
            ),

            'organizations_bank' => array(
                'title' => 'Банк',
                'label' => 'Банк',
                'db' => 'ob.name_short',
                'width' => '5%',
                'className' => 'text-center',
                'render' => 'text',
                //'popover' => 'bottom',

                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            // 'type' => array(
            //     'title' => 'Категория',
            //     'label' => 'Категория',
            //     'db' => 'bd.type',
            //     'width' => '1%',
            //     'className' => 'text-center',
            //     'render' => 'text',

            //     'null' => "Отсутствует",
            //     'search_op' => 'like'
            // ),

            'payer' => array(
                'title' => 'Плательщик',
                'label' => 'Плательщик',
                'db' => 'o1.name',
                'width' => '5%',
                'className' => 'text-center',
                'render' => 'text',

                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'payer_inn' => array(
                'title' => 'ИНН плательщика',
                'label' => 'ИНН плательщика',
                'db' => 'o1.inn',
                'width' => '5%',
                'className' => 'text-center',
                'render' => 'text',

                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'recipient' => array(
                'title' => 'Получатель',
                'label' => 'Получатель',
                'db' => 'o2.name',
                'width' => '5%',
                'className' => 'text-center',
                'render' => 'text',

                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'recipient_inn' => array(
                'title' => 'ИНН получателя',
                'label' => 'ИНН получателя',
                'db' => 'o2.inn',
                'width' => '5%',
                'className' => 'text-center',
                'render' => 'text',

                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'date' => array(
                'title' => 'Дата',
                'label' => 'Дата',
                'db' => 'bd.date',
                'type' => 'date_range',
                'format_date' => "d.m.Y",
                'options' => $this->dateRangeSearch(),
                'width' => '1%',
                'className' => 'text-center',

                'search_op' => 'date_range',
            ),

            'sum' => array(
                'title' => 'Сумма',
                'label' => 'Сумма',
                'db' => 'bd.sum',
                'width' => '1%',
                'className' => 'text-center',

                'search_op' => 'like'
            ),

            'comment' => array(
                'title' => 'Комментарий',
                'label' => 'Комментарий',
                'db' => 'bd.comment',
                'width' => '5%',
                'render' => 'text',

                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            // 'upload_id' => array(
            //     'title' => '№ выгрузки',
            //     'label' => '№ выгрузки',
            //     'db' => 'bd.upload_id',
            //     'width' => '1%',
            //     'className' => 'text-center',

            //     'null' => "Отсутствует",
            //     'search_op' => 'like'
            // ),

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

            'action' => array(
                'type' => 'actions',
                // 'width' => '1%', // Для actions всегда стоит 1% в фильтрах
                'className' => 'text-center px-1',

                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,

                // query sql
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
        return BankDetail::getPayIn();
    }

    protected function parseRow($row)
    {
        // $row["payer"] = $row["payer"] . '('.$row["payer"].')';
        // if(is_date($r["create_at"], "Y-m-d H:i:s")) $r["create_at"] = date("d.m.Y H:i:s", strtotime($r["create_at"]));
        // if(is_date($r["published_at"], "Y-m-d H:i:s")) $r["published_at"] = date("d.m.Y H:i:s", strtotime($r["published_at"]));

        // OLD
        //$row['action'] = (string)view('bank::manager.grid.action', [
        //    'edit' => router("admin.bank.edit", $row['id']),
        //]);

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            'module' => $this->namespace,
            'actions' => [
                'edit'  => true,
            ],
        ])->render();

        return $row;
    }

}
