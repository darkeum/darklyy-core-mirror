<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Certificates\UI\WEB\Grids;

use \Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class IndexGrid extends GridModern
{
    protected $db_type = 'db';
    protected $namespace = 'certificates';

    protected function init()
    {
        // Заголовок таблицы
        $this->title = 'Сертификаты';

        // Опции таблицы (в данном случае сортировка)
        $this->options = [
            'sort' => [
                'name' => 'numb',
                'order' => 'DESC'
            ],
        ];

         // Опции панели навигации
        $this->nav = [
            'excel' => true,
            'exceltext' => __('grid.export.excel'),
            'trash' => true,
            'trashtype' => 'null',
            'trashtext' => __('grid.trash.show'),
            'trashfield' => $this->namespace . '.deleted_at'
        ];

        $this->db = DB::table($this->namespace)->where('deleted_at', null);

        $this->cols = array(

            'id' => array(
                'visible' => false,
                'db' =>  $this->namespace.'.id',
            ),

            'numb' => array(
                'db' => $this->namespace.'.id',
                'title' => '№',
                'width' => '2%',
                
                'search_op' => 'like'
            ),
            'numb_cert' => array(
                'db' => $this->namespace.'.numb',
                'title' => "Номер сертификата",
                'width' => "2%",
                'render' => 'text',
                'search_op' => 'like'
            ),

            'title' => array(
                'db' => $this->namespace.'.title',
                'title' => "Заголовок",
                'width' => "8%",
                'render' => 'text',
                'search_op' => 'like'
            ),


            'uid' => array(
                'title' => "Ответственный",
                'db' => $this->namespace.'.uid',
                'width' => "5%",
                
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid($this->namespace),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getUid($this->namespace), "Все"),
                ),
                'search_op' => 'equal',
            ),

            'created_at' => array(
                'title' => "Дата создания",
                'db' => $this->namespace.'.created_at',
                'type' => 'date_range',
                'width' => '5%',
                'className' => 'text-center px-1',

                // query sql
                'search_op' => 'date_range'
            ),

            'updated_at' => array(
                'title' => "Дата обновления",
                'db' => $this->namespace.'.updated_at',
                'type' => 'date_range',
                'width' => '5%',
                'className' => 'text-center px-1',

                // query sql
                'search_op' => 'date_range',
            ),

            'views' => array(
                'db' => $this->namespace.'.views',
                'title' => "Просмотры",
                'width' => "1%",
                'className' => 'text-center',
                'encode' => false,
                'search_op' => 'like'
            ),

            'status' => array(
                'title' => "Статус",
                'db' => $this->namespace.'.status',
                'width' => "2%",
                'className' => 'text-center',
                
                'encode' => false,
                'replace' => $this->getStatus('render'),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getStatus('select'), "Все"),
                ),
                'search_op' => 'equal',
            ),

            // Действия
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

    protected function getStatus($type = 'select')
    {
        $rows = [];
        if ($type == 'select') {
            $rows[0] = "Отключен";
            $rows[1] = "Опубликован";
        } elseif ($type == 'render') {
            $rows[0] = "<span class='fal fa-circle text-danger fa-md' data-toggle='tooltip' title='Отключен'></span>";
            $rows[1] = "<span class='fal fa-check-circle text-success fa-md' data-toggle='tooltip' title='Опубликован'></span>";
        }
        return $rows;
    }
  
    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];       
        $r['action'] = view('grid::layouts.actions', [
            'row' => $r,
            //'module' => $this->namespace,
            'module' => "certificates",
            'actions' => [
                // 'show' => route('certificates.show',['']),
                'edit' => true,
                'destroy' => true,
            ],
        ])->render();
        return $r;
    }
}
