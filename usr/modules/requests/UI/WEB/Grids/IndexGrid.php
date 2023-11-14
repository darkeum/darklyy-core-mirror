<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Requests\UI\WEB\Grids;


use Boot\System\Grid\GridModern;
use \Boot\System\Database\DB;
use \Boot\Support\Facades\Translation;
use Objects\Models\Objects;
use Request\Model\Request;

class IndexGrid extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'request';

    protected function init()
    {
        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );
        //$uid_dir = $this->getUidTypes('uid_dir');
        //$uid_exec = $this->getUidTypes('uid_exec');
        //$type = $this->getType();

        //$toid = $this->getUidTypes('toid');
        $status_type = $this->getStatusType();
        //$status_value = $this->getStatus();

        // Опции панели навигации
        $this->nav = array(
            'excel' => true,
            'exceltext' => __('grid.export.excel'),
            'trash' => true,
            'trashtype' => 'null',
            'trashtext' => __('grid.trash.show'),
            'trashfield' => $this->namespace . '.deleted_at',
        );

        $this->title = __('requests::general.grid');

        // Подключение к необходимой таблице
        $this->db = DB::table($this->namespace)->where('deleted_at',null);
        $this->users = DB::table('users')->where('active', true)->get();

        $this->cols = array(

            'id' => array(
                'db' =>  $this->namespace . '.id',
                'visible' => false
            ),

            'type_real' => array(
                'db' =>  $this->namespace . '.type',
                'visible' => false
            ),

            'numb' => array(
                'db' => $this->namespace . '.id',
                'label' => '№',
                'title' => '№',
                'width' => "5%",

                'className' => 'text-center',
                'search_op' => 'like'
            ),

            'title' => array(
                'db' => $this->namespace . '.title',
                'label' => 'Заголовок',
                'title' => 'Заголовок',
                'width' => "15%",
                'render' => 'text',
                'popover' => true,

                'null' => "Отсутствует",
                'search_op' => 'like'
            ),

            'obj' => array(
                'title' => 'Объект',
                'db' => $this->namespace . '.object',
                'width' => "10%",
                'render' => 'text',
                'options' => array(
                    'value' => $this->setValue($this->getObj(), 'Все'),
                ),

                'encode' => false,
                'replace' => $this->getObj(),
                'type' => 'select',

                'className' => 'text-center',

                'search_op' => 'equal',
            ),

            'type' => array(
                'title' => 'Тип',
                'db' => $this->namespace . '.type',
                'width' => "10%",

                'options' => array(
                    'value' => $this->setValue($this->searchType(), 'Все'),
                ),

                'encode' => false,
                //'replace' => $type,
                'type' => 'select',

                'className' => 'text-center',

                'search_op' => 'equal',
            ),

            'uid_dir' => array(
                'title' => 'Постановщик',
                'db' => $this->namespace . '.uid_dir',
                'width' => "1%",

                'options' => array(
                   'value' => $this->setValue($this->searchDir(), 'Все'),
                ),

                'encode' => false,
                'sortable' => false,
                'type' => 'select',
                'className' => 'text-center',

                'search_op' => 'equal',
            ),

            'uid_exec' => array(
                'title' => 'Исполнитель',
                'db' => $this->namespace . '.uid_exec',
                'width' => "1%",

                'options' => array(
                    'value' => $this->setValue($this->searchExecutor(), 'Все'),
                 ),

                'encode' => false,
                'sortable' => false,
                'type' => 'select',
                'null' => "Отсутсвует",
                'className' => 'text-center',

                'search_op' => 'equal',
            ),

            'date' => array(
                'title' => 'Дата',
                'label' => 'Дата',
                'db' => $this->namespace . '.date',
                'type' => 'date_range',
                'format_date' => "d.m.Y",
                'width' => '1%',
                'className' => 'text-center',

                'search_op' => 'date_range',
            ),

            'status' => array(
                'title' => 'Статус',
                'db' => $this->namespace . '.status',


                'options' => array(
                    'value' => $this->setValue($this->searchStatus(), 'Все'),
                ),

                'width' => "5%",
                'encode' => false,
                'replace' => $status_type,
                'type' => 'select',
                'search_op' => 'equal',
                'className' => 'text-center',
            ),

            'action' => array(
                'type' => 'actions',
                //'width' => '1%',
                'className' => 'text-center',

                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search' => false,
                'excel_hidden' => true,

                'search_op' => false,
            )

        );

        $this->render_filter_toolbar = true;
    }

    //protected function getUidTypes($type)
    //{
    //    switch ($type) {
    //        case "uid_dir":
    //            // $result = $db->query("SELECT DISTINCT r.uid_dir, u.* FROM JMY_request as r LEFT JOIN JMY_users as u on (r.uid_dir=u.id)");
    //            $result = Request::where('deleted_at')->leftjoin("users", "request.uid_dir", "=", "users.id")->get();
    //            // $result = DB::table($this->namespace)->
    //            // leftJoin("users","request.uid_dir", "=", "users.id");
    //            break;
    //        case "uid_exec":
    //            // $result = $db->query("SELECT DISTINCT r.uid_exec, u.* FROM JMY_request as r LEFT JOIN JMY_users as u on (r.uid_exec=u.id)");
    //            $result = Request::where('deleted_at')->leftjoin("users", "request.uid_exec", "=", "users.id")->get();
    //            // $result = DB::table($this->namespace . "as r")->
    //            // leftJoin("users as u", "r.uid_exec", "=", "u.id")->
    //            // select("r.uid_exec, u.*")->distinct();
    //            break;
    //    }
    //    $rows = [];
    //    $rows["0"] = "Гость";
    //    foreach ($result as $r) {
    //        if ($r->id != 0) {
    //            $str = $r->surname;
    //            if ($r->name != "")
    //                $str = $str . " " . mb_substr($r->name, 0, 1, 'utf-8');
    //            if ($r->ochestvo != "")
    //                $str = $str . ". " . mb_substr($r->ochestvo, 0, 1, 'utf-8') . ".";
    //            $rows[$r->id] = $str;
    //        }
    //    }
    //    return $rows;
    //}

    protected function getStatusType()
    {
        $rows = [];
        $rows[1] = "<span class='badge rounded-pill badge-light-warning d-block' title='Ожидает исполнителя'>Ожидает исполнителя</span>";
        $rows[2] = "<span class='badge rounded-pill badge-light-info d-block' title='Ожидает выполнения заявки'>Ожидает выполнения заявки</span>";
        $rows[3] = "<span class='badge rounded-pill badge-light-success d-block' title='Заявка выполнена'>Заявка выполнена</span>";
        return $rows;
    }

    protected function getStatus()
    {
        $rows[1] = "Ожидает исполнителя";
        $rows[2] = "Ожидает выполнения заявки";
        $rows[3] = "Заявка выполнена";
        return $rows;
    }

    protected function searchType()
    {
        return [
            1 => 'БТП свободная заявка',
            2 => 'Расчет БТП',
            3 => 'Расчет теплообменников',
            4 => 'Расчет узла учета',
            5 => 'Расчет насосного оборудования',
            6 => 'Расчет насосной станции',
        ];
    }

    protected function searchStatus()
    {
        return [
            1 => 'Ожидает исполнителя',
            2 => 'Ожидает выполнения заявки',
            3 => 'Заявка выполнена'
        ];
    }

    protected function searchExecutor()
    {
        $result = DB::table('users')->whereIn('id',Request::pluck('uid_exec'))->get();
        $rows[] = "Отсутсвует";
        foreach ($result as $r) {
            if ($r->id != 0) {
                $str = $r->surname;
                if ($r->name != "")
                    $str = $str . " " . mb_substr($r->name, 0, 1, 'utf-8');
                if ($r->ochestvo != "")
                    $str = $str . ". " . mb_substr($r->ochestvo, 0, 1, 'utf-8') . ".";
                $rows[$r->id] = $str;
            }
         }
        return $rows;
    }

    protected function getObj()
    {
        $result = Objects::whereIn('id',Request::pluck('object'))->get()
        ;
        $rows[] = "Отсутствует";
        foreach ($result as $r) {
            $rows[$r->id] = $r->name;
        }
        return $rows;
    }

    protected function searchDir()
    {
        $result = DB::table('users')->whereIn('id',Request::pluck('uid_dir'))->get();
        $rows[] = "Отсутсвует";
        foreach ($result as $r) {
            if ($r->id != 0) {
                $str = $r->surname;
                if ($r->name != "")
                    $str = $str . " " . mb_substr($r->name, 0, 1, 'utf-8');
                if ($r->ochestvo != "")
                    $str = $str . ". " . mb_substr($r->ochestvo, 0, 1, 'utf-8') . ".";
                $rows[$r->id] = $str;
            }
         }
        return $rows;
    }

    //protected function getType()
    //{
    //    $types = config('requests-config.types.img');
    //    return $types;
    //}

    // Получение пользователей CUT
    protected function getCutNames($id)
    {
        $result = $this->users->filter(function ($item) use ($id) {
            return $item->id == $id;
        });

        if ($result->first()) {
            $r = $result->first();
            return $r->surname . '. ' . mb_substr($r->name, 0, 1, 'utf-8') . '. ' . mb_substr($r->ochestvo, 0, 1, 'utf-8') . '.';
        }
    }

    protected function parseRow($row)
    {
        $types = config('requests-config.types');
        if ($row['type']) {
            $row['type'] = view('grid::layouts.img', [
                'title' => $types['text'][$row['type'] ?? 0],
                'src' => $types['path'] . $types['img'][$row['type'] ?? 0],
            ])->render();
        } else {
            $row['type'] = 'Нету';
        }

        $row['action'] = view('grid::layouts.actions', [
            'row' => $row,
            //'module' => $this->namespace,
            'module' => 'requests',
            'actions' => [
                // 'show' => ($row['type_real'] != 3) ? true : '/administration/module/request/view/'. $row['id'],
                'show' => true,
                'edit' => true,
                'destroy' => true,
            ],
        ])->render();

        // Вариант с фильтрацией
        //$uid_dir = $row['uid_dir'];
        //$uid_dir = $this->users->filter(function ($item) use ($uid_dir) {
        //    return $item->id == $uid_dir;
        //});
        //$row['uid_dir'] = $uid_dir->first()->surname ?? null;
        //dd($row['uid_dir']);

        // Вариант с поиском ID
        //$uid_dir = $this->users->search(function($item, $key) use (&$uid_dir) {
        //    return ($item->id == $uid_dir) ? $item->id : null;
        //});
        //dd($this->users[$uid_dir]->surname);

        // Вариант с фильтрацией
        //$uid_exec = $row['uid_exec'];
        //$uid_exec = $this->users->filter(function ($item) use ($uid_exec) {
        //    return $item->id == $uid_exec;
        //});
        //$row['uid_exec'] = $uid_exec->first()->surname ?? null;
        //dd($row['uid_exec']);

        $row['uid_dir'] = $this->getCutNames($row['uid_dir']);      
        $row['uid_exec'] = $this->getCutNames($row['uid_exec']);

        if (is_date($row["date"], "Y-m-d H:i:s")) $row["date"] = date("d.m.Y H:i:s", strtotime($row["date"]));

        return $row;
    }
}
