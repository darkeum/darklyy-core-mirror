<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Order Index Grid
*/

namespace Order\Grid;

use \Boot\System\Grid\Grid;
use \Boot\System\Database\DB;
use \Boot\Support\Facades\Translation;

class IndexGrid extends Grid
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->table = 'order';

        $this->options = array(
            'sortname' => $this->table.'.id',
            'sortorder' => 'DESC',
        );

        $this->db = DB::table($this->table);

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

           

            'surname' => array(
                'db' => $this->table.'.surname',
                'label' => Translation::trans('order.grid.surname'),
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'name' => array(
                'db' => $this->table.'.name',
                'label' => Translation::trans('order.grid.name'),
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'ochestvo' => array(
                'db' => $this->table.'.ochestvo',
                'label' => Translation::trans('order.grid.ochestvo'),
                'width' => 30,
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'email' => array(
                'db' => $this->table.'.email',
                'label' => Translation::trans('order.grid.email'),
                'width' => 40,
                'hidden' => false,
                'encode' => false,
                'null' => Translation::trans('grid.null'),
                'search_op' => 'like'
            ),

            'phone' => array(
                'db' => $this->table.'.phone',
                'label' => Translation::trans('order.grid.phone'),
                'width' => 30,
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
                'replace' => $this->getUid('order'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getUid('order'), Translation::trans('grid.all')),
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
        
            'type' => array(
				'label' => Translation::trans('order.grid.type'),
				'width' => 30,
				'db' => $this->table.'.type',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getTypes(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' =>  $this->setValue($this->getTypes(), Translation::trans('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'read_status' => array(
                'label' => Translation::trans('order.grid.read_status'),
                'db' => $this->table.'.read_status',
                'width' => 15,
                'align' => 'center',
                'hidden' => false,
                'encode' => false,
                'replace' => $this->getReadStatus('render'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getReadStatus('select'), Translation::trans('grid.all')),
                ),
                'search_op' => 'equal',
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
            $rows[0] = Translation::trans('order.status.archive');
            $rows[1] = Translation::trans('order.status.wait');
            $rows[2] = Translation::trans('order.status.work');
            $rows[3] = Translation::trans('order.status.complete');
        } elseif ($type == 'render') {
            $rows[0] = "<span class='fal fa-circle text-danger fa-md' data-toggle='tooltip' title='". Translation::trans('order.status.archive')."'></span>";
            $rows[1] = "<span class='fal fa-circle text-warning fa-md' data-toggle='tooltip' title='". Translation::trans('order.status.wait')."'></span>";
            $rows[2] = "<span class='fal fa-check-time text-info fa-md' data-toggle='tooltip' title='". Translation::trans('order.status.work')."'></span>";
            $rows[3] = "<span class='fal fa-check-circle text-success fa-md' data-toggle='tooltip' title='". Translation::trans('order.status.complete')."'></span>";
        }
        return $rows;
    }

    protected function getReadStatus($type = 'select')
    {
        $rows = [];
        if ($type == 'select') {
            $rows[0] = Translation::trans('order.grid.unread');
            $rows[1] = Translation::trans('order.grid.read');
        } elseif ($type == 'render') {
            $rows[0] = "<span class='fal fa-eye text-danger fa-md' data-toggle='tooltip' title='". Translation::trans('order.grid.unread')."'></span>";
            $rows[1] = "<span class='fal fa-eye text-success fa-md' data-toggle='tooltip' title='". Translation::trans('order.grid.read')."'></span>";
        }
        return $rows;
    }

    protected function getTypes() {
       
        $rows = [];
        $rows['feedback'] = Translation::trans('order.type.feedback');
        $rows['job'] = Translation::trans('order.type.job');
        $rows['heat'] = Translation::trans('order.type.heat');
        $rows['ver'] = Translation::trans('order.type.ver');
        $rows['partner'] = Translation::trans('order.type.partner');
        $rows['pump'] = Translation::trans('order.type.pump');
      
        return $rows;
    }

    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];
        $r['surname'] = empty($r['surname']) ? '<font color="red">'. Translation::trans('grid.null').'</font>' :  $r['surname'];
        $r['name'] = empty($r['name']) ? '<font color="red">'. Translation::trans('grid.null').'</font>' :  $r['name'];
        $r['ochestvo'] = empty($r['ochestvo']) ? '<font color="red">'. Translation::trans('grid.null').'</font>' :  $r['ochestvo'];
        $r['phone'] = empty($r['phone']) ? '<font color="red">'. Translation::trans('grid.null').'</font>' :  $r['phone'];
        $r['email'] = empty($r['email']) ? '<font color="red">'. Translation::trans('grid.null').'</font>' :  $r['email'];
        $r['uid'] = empty($r['uid']) ? '<font color="red">'. Translation::trans('grid.null').'</font>' :  $r['uid'];
        $r['ab'] = '<a class="btn btn-xs btn-primary" href="'.router('admin.order.show', $r['id']).'">Просмотр</a>'; 
        if(is_date($r["created_at"], "Y-m-d H:i:s")) $r["created_at"] = date("d.m.Y H:i:s", strtotime($r["created_at"]));
        if(is_date($r["updated_at"], "Y-m-d H:i:s")) $r["updated_at"] = date("d.m.Y H:i:s", strtotime($r["updated_at"]));
        return $r;
    }
}
