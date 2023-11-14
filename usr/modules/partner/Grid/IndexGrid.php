<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description News Index Grid
*/

// namespace Certificates\Grid;
namespace Partner\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;
use \Boot\Support\Facades\Translation;

class IndexGrid extends Grid
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->table = 'certificates';
        $this->lang = 'certificates';
        $this->module = 'certificates';

        $this->options = array(
            'sortname' => $this->table.'.id',
            'sortorder' => 'DESC',
        );

        $this->db = DB::table($this->table)->where('deleted_at', null);

        $this->cols = array(

            'id' => array(
                'hidden' => true,
                'db' =>  $this->table.'.id',
            ),

            'numb' => array(
                'db' => $this->table.'.id',
                'label' => '№',
                'width' => 8,
                'hidden' => false,
                'search_op' => 'like'
            ),

            'title' => array(
                'db' => $this->table.'.title',
                'label' => Translation::trans($this->lang.'.grid.title'),
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'numb_cert' => array(
                'db' => $this->table.'.numb',
                'label' => Translation::trans($this->lang.'.grid.numb'),
                'width' => 15,
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),
          

            'uid' => array(
                'label' => Translation::trans('grid.uid'),
                'db' => $this->table.'.uid',
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid($this->module),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getUid($this->module), Translation::trans('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'created_at' => array(
                'label' => Translation::trans('grid.created_at'),
                'db' => $this->table.'.created_at',
                'width' => 25,
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'updated_at' => array(
                'label' => Translation::trans('grid.updated_at'),
                'db' => $this->table.'.updated_at',
                'width' => 25,
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'views' => array(
                'db' => $this->table.'.views',
                'label' => Translation::trans($this->lang.'.grid.views'),
                'width' => 15,
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'status' => array(
                'label' => Translation::trans('grid.status'),
                'db' => $this->table.'.status',
                'width' => 15,
                'align' => 'center',
                'hidden' => false,
                'encode' => false,
                'replace' => $this->getStatus('render'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getStatus('select'), Translation::trans('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'ab' => array(
                'label' => Translation::trans('grid.action'),
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

    protected function getStatus($type = 'select')
    {
        $rows = [];
        if ($type == 'select') {
            $rows[0] = Translation::trans($this->lang.'.status.off');
            $rows[1] = Translation::trans($this->lang.'.status.on');
        } elseif ($type == 'render') {
            $rows[0] = "<span class='fal fa-circle text-danger fa-md' data-toggle='tooltip' title='". Translation::trans($this->lang.'.status.off')."'></span>";
            $rows[1] = "<span class='fal fa-check-circle text-success fa-md' data-toggle='tooltip' title='". Translation::trans($this->lang.'.status.on')."'></span>";
        }
        return $rows;
    }
  
    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];       
        $r['ab'] = '<a class="btn btn-xs btn-primary" href="'.router('admin.'.$this->module.'.edit', $r['id']).'">Изменить</a>'; 
        if(is_date($r["created_at"], "Y-m-d H:i:s")) $r["created_at"] = date("d.m.Y H:i:s", strtotime($r["created_at"]));
        if(is_date($r["updated_at"], "Y-m-d H:i:s")) $r["updated_at"] = date("d.m.Y H:i:s", strtotime($r["updated_at"]));
        return $r;
    }
}
