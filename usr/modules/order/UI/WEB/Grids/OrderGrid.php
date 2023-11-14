<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Order\UI\WEB\Grids;

use \Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class OrderGrid extends GridModern
{
    protected $db_type = 'db';

    protected function init()
    {
        $this->table = 'order';

        $this->title = __('order::general.grid.title');

        $this->options = array(
            'sort' => [
                'name' => 'id',
                'order' => 'DESC',
            ]           
        );

        $this->db = DB::table($this->table);

        $this->cols = array(

            'id' => array(
                'visible' => false,
                'db' =>  $this->table.'.id',
            ),

            'numb' => array(
                'db' => $this->table.'.id',
                'title' => '№',
                'label' => '№',
                'width' => "2%",
                'search_op' => 'like'
            ),



            'surname' => array(
                'db' => $this->table.'.surname',
                'title' => __('order::general.grid.surname'),
                'label' => __('order::general.grid.surname'),
                'width' => "10%",
                'encode' => false,
                'null' => __('grid.null'),
                'search_op' => 'like'
            ),

            'name' => array(
                'db' => $this->table.'.name',
                'title' => __('order::general.grid.name'),
                'label' => __('order::general.grid.name'),
                'width' => "10%",
                'render' => "text",
                'hidden' => false,
                'encode' => false,
                'null' => __('grid.null'),
                'search_op' => 'like'
            ),

            'email' => array(
                'db' => $this->table.'.email',
                'title' => __('order::general.grid.email'),
                'label' => __('order::general.grid.email'),
                'width' => "12%",
                'render' => "text",
                'hidden' => false,
                'encode' => false,
                'null' => __('grid.null'),
                'search_op' => 'like'
            ),

            'phone' => array(
                'db' => $this->table.'.phone',
                'title' => __('order::general.grid.phone'),
                'label' => __('order::general.grid.phone'),
                'width' => "15%",
                'hidden' => false,
                'encode' => false,
                'null' => __('grid.null'),
                'search_op' => 'like'
            ),


            'uid' => array(
                'title' => __('grid.uid'),
                'label' => __('grid.uid'),
                'db' => $this->table.'.uid',
                'width' => "10%",
                'hidden' => false,
                'encode' => false,
                'sortable' => false,
                'replace' => $this->getUid('order'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getUid('order'), __('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'created_at' => array(
                'title' => __('grid.created_at'),
                'label' => __('grid.created_at'),
                'db' => $this->table.'.created_at',
                'width' => "8%",
                'searchoptions' => $this->dateRangeSearch(),
                'search_op' => 'date_range',
            ),

            'type' => array(
                'title' => __('order::general.grid.type'),
                'label' => __('order::general.grid.type'),
                'width' => "5%",
                'db' => $this->table.'.type',

                'encode' => false,
                'sortable' => false,
                'replace' => $this->getTypes(),
                'type' => 'select',
                'className' => 'text-center',

                'options' => array(
                    'value' => $this->setValue($this->getTypes(), 'Все'),
                ),
                'search_op' => 'equal',
            ),


            'read_status' => array(
                'title' => __('order::general.grid.read_status'),
                'label' => __('order::general.grid.read_status'),
                'db' => $this->table.'.read_status',
                'width' => "5%",
                'className' => 'text-center px-1',
                'hidden' => false,
                'encode' => false,
                'replace' => $this->getReadStatus('render'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getReadStatus('select'), __('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'status' => array(
                'title' => __('grid.status'),
                'label' => __('grid.status'),
                'db' => $this->table.'.status',
                'width' => "5%",
                'align' => 'center',
                'hidden' => false,
                'encode' => false,
                'replace' => $this->getStatus('render'),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => $this->setValue($this->getStatus('select'), __('grid.all')),
                ),
                'search_op' => 'equal',
            ),

            'ab' => array(
                'title' => __('grid.action'),
                'label' => __('grid.action'),
                'width' => "5%",
                'hidden' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => false,
                'search_op' => false,
                'search' => false,
            ),
        );
        $this->render_filter_toolbar = true;
    }

    protected function getStatus($type = 'select')
    {
        $rows = [];
        if ($type == 'select') {
            $rows[0] = __('order::general.status.archive');
            $rows[1] = __('order::general.status.wait');
            $rows[2] = __('order::general.status.work');
            $rows[3] = __('order::general.status.complete');
        } elseif ($type == 'render') {
            $rows[0] = "<span class='fal fa-circle text-danger fa-md' data-toggle='tooltip' title='". __('order::general.status.archive')."'></span>";
            $rows[1] = "<span class='fal fa-circle text-warning fa-md' data-toggle='tooltip' title='". __('order::general.status.wait')."'></span>";
            $rows[2] = "<span class='fal fa-check-time text-info fa-md' data-toggle='tooltip' title='". __('order::general.status.work')."'></span>";
            $rows[3] = "<span class='fal fa-check-circle text-success fa-md' data-toggle='tooltip' title='". __('order::general.status.complete')."'></span>";
        }
        return $rows;
    }

    protected function getReadStatus($type = 'select')
    {
        $rows = [];
        if ($type == 'select') {
            $rows[0] = __('order::general.grid.unread');
            $rows[1] = __('order::general.grid.read');
        } elseif ($type == 'render') {
            $rows[0] = "<span class='fal fa-eye text-danger fa-md' data-toggle='tooltip' title='". __('order::general.grid.unread')."'></span>";
            $rows[1] = "<span class='fal fa-eye text-success fa-md' data-toggle='tooltip' title='". __('order::general.grid.read')."'></span>";
        }
        return $rows;
    }

    protected function getTypes() {

        $rows = [];
        $rows['feedback'] = __('order::general.type.feedback');
        $rows['job'] = __('order::general.type.job');
        $rows['heat'] = __('order::general.type.heat');
        $rows['ver'] = __('order::general.type.ver');
        $rows['partner'] = __('order::general.type.partner');
        $rows['pump'] = __('order::general.type.pump');
        $rows['block'] = __('order::general.type.block');
        $rows['pump_station'] = __('order::general.type.pump_station');
        $rows['metering_station'] = __('order::general.type.metering_station');


        return $rows;
    }


    protected function parseRow($r)
    {
        $r["numb"] = $r["id"];
        $r['surname'] = empty($r['surname']) ? '<font color="red">'. __('grid.null').'</font>' :  $r['surname'];
        $r['name'] = empty($r['name']) ? '<font color="red">'. __('grid.null').'</font>' :  $r['name'];
        $r['phone'] = empty($r['phone']) ? '<font color="red">'. __('grid.null').'</font>' :  $r['phone'];
        $r['email'] = empty($r['email']) ? '<font color="red">'. __('grid.null').'</font>' :  $r['email'];
        $r['uid'] = empty($r['uid']) ? '<font color="red">'. __('grid.null').'</font>' :  $r['uid'];
        $r['ab'] = '<a href="'.router('admin.order.show', $r['id']).'" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i data-feather="eye"></i>
        </a>';
        if(is_date($r["created_at"], "Y-m-d H:i:s")) $r["created_at"] = date("d.m.Y", strtotime($r["created_at"]));
        return $r;
    }
}
