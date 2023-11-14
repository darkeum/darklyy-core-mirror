<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Reviews Index Grid
*/

namespace Reviews\Grid;

use \Boot\System\Grid\Grid;
use Boot\System\Grid\GridModern;
use \Boot\System\Database\DB;
use \Boot\Support\Facades\Translation;

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
        
        $this->title = __('requests::general.grid');

        $this->db = DB::table($this->namespace)->where('deleted_at', null)->leftJoin('organizations', function ($join) {
            $join->on('organizations.id', '=', $this->namespace.'.oid');
        });

        $this->cols = array(

            'id' => array(
                'visible' => false,
                'db' =>  $this->namespace.'.id',
            ),

            'numb' => array(
                'db' => $this->namespace.'.id',
                'label' => '№',
                'width' => "2%",
                'search_op' => 'like'
            ),
          

            'oid_name' => array(
                'db' => 'organizations.name',
                'label' => Translation::trans('reviews.grid.oid_name'),
                'width' => "10%",
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'title' => array(
                'db' => $this->namespace.'.title',
                'label' => Translation::trans('reviews.grid.title'),
                'width' => "10%",
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),
          

            'uid' => array(
                'label' => Translation::trans('grid.uid'),
                'db' => $this->namespace.'.uid',
                'width' => "10%",
                'encode' => false,
                'replace' => $this->getUid('reviews'),
                'type' => 'select',
                'options' => array(
                    'value' => $this->setValue($this->getUid('reviews'), Translation::trans('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'created_at' => array(
                'label' => Translation::trans('grid.created_at'),
                'db' => $this->namespace.'.created_at',
                'width' => "5%",
                'options' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'updated_at' => array(
                'label' => Translation::trans('grid.updated_at'),
                'db' => $this->namespace.'.updated_at',
                'width' => "5%",
                'options' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'views' => array(
                'db' => $this->namespace.'.views',
                'label' => Translation::trans('reviews.grid.views'),
                'width' => "5%",
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'status' => array(
                'label' => Translation::trans('grid.status'),
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

            'ab' => array(
                'label' => Translation::trans('grid.action'),
                'width' => 50,
                'manual' => true,
                'encode' => false,
                'search_op' => false,
                'search' => false,
                'title' => false,
            ),
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
        $r['ab'] = '<a class="btn btn-xs btn-primary" href="'.route('admin.reviews.edit', $r['id']).'">Изменить</a>'; 
        if(is_date($r["created_at"], "Y-m-d H:i:s")) $r["created_at"] = date("d.m.Y H:i:s", strtotime($r["created_at"]));
        if(is_date($r["updated_at"], "Y-m-d H:i:s")) $r["updated_at"] = date("d.m.Y H:i:s", strtotime($r["updated_at"]));
        return $r;
    }
}
