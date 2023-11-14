<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Reviews\UI\WEB\Grids;

use Boot\System\Grid\GridModern;
use \Boot\System\Database\DB;
use \Boot\Support\Facades\Translation;
use \Boot\Models\Categories;

class IndexGrid extends GridModern
{
    protected $db_type = 'db';
    protected $namespace = 'reviews';

    protected function init()
    {

        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC'
            ],
        );

        $this->nav = array(
            'excel' => true,
            'exceltext' => __('grid.export.excel'),
            'trash' => true,
            'trashtype' => 'null',
            'trashtext' => __('grid.trash.show'),
            'trashfield' => $this->namespace . '.deleted_at',
        );
        
        $this->title = "Таблица отзывов";

        $this->db = DB::table($this->namespace)->where('deleted_at', null)
        ->leftJoin('organizations', function ($join) {
            $join->on('organizations.id', '=', $this->namespace.'.oid');
        });

        $this->cols = array(

            'id' => array(
                'visible' => false,
                'db' =>  $this->namespace.'.id',
            ),

            'slug' => array(
                'visible' => false,
                'db' =>  $this->namespace.'.slug',
            ),

            'category' => array(
                'visible' => false,
                'db' =>  $this->namespace.'.category',
            ),

            'numb' => array(
                'db' => $this->namespace.'.id',
                'title' => '№',
                'width' => "2%",
                'search_op' => 'like',
                'className' => 'text-center',
            ),
          
            'oid_name' => array(
                'db' => 'organizations.name',
                'title' => "Контрагент",
                'width' => "10%",
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'title' => array(
                'db' => $this->namespace.'.title',
                'title' => "Заголовок",
                'width' => "10%",
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'uid' => array(
                'title' => "Ответственный",
                'db' => $this->namespace.'.uid',
                'width' => "10%",
                'encode' => false,
                'replace' => $this->getUid('reviews'),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getUid('reviews'), __('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'created_at' => array(
                'title' => "Дата создания",
                'db' => $this->namespace.'.created_at',
                'width' => "5%",
                'type' => 'date_range',
                'format_date' => "d.m.Y",
                'className' => 'text-center',

                'search_op' => 'date_range',
            ),

            'updated_at' => array(
                'title' => "Дата обновления",
                'db' => $this->namespace.'.updated_at',
                'width' => "5%",
                'type' => 'date_range',
                'format_date' => "d.m.Y",
                'className' => 'text-center',

                'search_op' => 'date_range',
            ),

            'views' => array(
                'db' => $this->namespace.'.views',
                'title' => "Просмотры",
                'width' => "1%",
                'encode' => false,
                'className' => 'text-center',
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'status' => array(
                'title' => "Статус",
                'db' => $this->namespace.'.status',
                'width' => "5%",
                'className' => 'text-center',
                'encode' => false,
                'replace' => $this->getStatus('render'),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getStatus('select'), Translation::trans('grid.all')),
                ),
                'search_op' => 'equal',
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

    protected function getStatus($type = 'select')
    {
        $rows = [];
        if ($type == 'select') {
            $rows[0] = Translation::trans('reviews.status.off');
            $rows[1] = Translation::trans('reviews.status.on');
        } elseif ($type == 'render') {
            $rows[0] = "<span class='fal fa-circle text-danger fa-md' data-toggle='tooltip' title='". Translation::trans('order.status.off')."'></span>";
            $rows[1] = "<span class='fal fa-check-circle text-success fa-md' data-toggle='tooltip' title='". Translation::trans('order.status.on')."'></span>";
        }
        return $rows;
    }
  
    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];       
        $r['action'] = view('grid::layouts.actions', [
            'row' => $r,
            //'module' => $this->namespace,
            'module' => $this->namespace,
            'actions' => [
                // 'show' => ($row['type_real'] != 3) ? true : '/administration/module/request/view/'. $row['id'],
                'show' => route("reviews.show",['categories' => Categories::find($r['category'])->altname,'post' =>$r['slug']]),
                'edit' => true,
                'destroy' => true,
              
            ],
        ])->render();
        return $r;
    }
}
