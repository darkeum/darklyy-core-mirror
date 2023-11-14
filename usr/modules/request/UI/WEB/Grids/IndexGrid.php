<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Gusev Kirill
*/

namespace Request\UI\WEB\Grids;


use Boot\System\Grid\GridModern;
use \Boot\System\Database\DB;
use \Boot\Support\Facades\Translation;
use Request\Model\Request;

class IndexGrid extends GridModern
{
    protected $db_type = 'db';

    protected $namespace = 'request';
    


    protected function init()
    {
        $this->options = array(
            'sort_name' => 'numb',
            'sort_order' => 'DESC',
        );
        $uid_dir = $this->getUidTypes('uid_dir');
		$uid_exec = $this->getUidTypes('uid_exec');
		$type = $this->getType();
        //$toid = $this->getUidTypes('toid');
        $status_type = $this->getStatusType();
        $status_value = $this->getStatus();
        // Опции панели навигации
        $this->nav = array(
            'excel' => true,
            'exceltext' => __('grid.export.excel'),
            'trash' => true,
            'trashtype' => 'null',
            'trashtext' => __('grid.trash.show'),
            'trashfield' => $this->namespace . '.deleted_at',
        );

        $this->title = __('request::general.grid');
        
        // Подключение к необходимой таблице
        $this->db = DB::table($this->namespace);
        

        $this->cols = array(

            'id' => array(
                'db' =>  $this->namespace . '.id',
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
            'type' => array(
                'label' => 'Тип',
                'title' => 'Тип',
                'db' => $this->namespace . '.type',
                'width' => "15%",

                'encode' => false,
                'sortable' => false,
                'replace' => $type,
                'type' => 'select',
                'search_op' => 'equal',				
            ),
            'uid_dir' => array(
                'label' => 'Постановщик',
                'title' => 'Постановщик',
                'db' => $this->namespace . '.uid_dir',
                'width' => "15%",

                'encode' => false,
                'sortable' => false,
                'replace' => $uid_dir,
                'type' => 'select', 

                'search_op' => 'equal',				
            ),
            'uid_exec' => array(
                'label' => 'Исполнитель',
                'title' => 'Исполнитель',
                'db' => $this->namespace . '.uid_exec',
                'width' => "15%",           

                'encode' => false,
                'sortable' => false,
                'replace' => $uid_exec,
                'type' => 'select',
                'null' => "Отсутсвует",
                
                'search_op' => 'equal',				
            ),
            'date' => array(
                'label' => 'Дата',
                'title' => 'Дата',
                'db' => $this->namespace . '.date',
                'width' => "10%",
                'search_op' => 'date_range',
            ),
            'status' => array(
                // 'label' => 'Статус',
                'title' => 'Статус',
                'db' => $this->namespace . '.status',
                'width' => "5%",              
                'encode' => false,
                'replace' => $status_type,
                'type' => 'select',
                'search_op' => 'equal',	
                'className' => 'text-center px-1',
            ),
            'action' => array(
                'type' => 'actions',
                'width' => '1%',
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



    protected function getUidTypes($type)
    {
 
        switch($type){
            case "uid_dir":
                // $result = $db->query("SELECT DISTINCT r.uid_dir, u.* FROM JMY_request as r LEFT JOIN JMY_users as u on (r.uid_dir=u.id)"); 
                $result = Request::where('deleted_at')->leftjoin("users","request.uid_dir", "=", "users.id")->get();
                // $result = DB::table($this->namespace)->
                // leftJoin("users","request.uid_dir", "=", "users.id");
            break;
            case "uid_exec":
                // $result = $db->query("SELECT DISTINCT r.uid_exec, u.* FROM JMY_request as r LEFT JOIN JMY_users as u on (r.uid_exec=u.id)");
                $result = Request::where('deleted_at')->leftjoin("users","request.uid_exec", "=", "users.id")->get();
                // $result = DB::table($this->namespace . "as r")->
                // leftJoin("users as u", "r.uid_exec", "=", "u.id")->
                // select("r.uid_exec, u.*")->distinct(); 
            break;
        }

        $rows = [];
        $rows["0"] = "Гость";
        foreach($result as $r)
        {   
            if($r->id!=0)
            {   
                $str=$r->surname;
                if($r->name!="")
                    $str=$str . " " . mb_substr($r->name,0,1, 'utf-8');
                if($r->ochestvo!="")
                    $str=$str . ". " . mb_substr($r->ochestvo,0,1, 'utf-8') . ".";
                $rows[$r->id] = $str ;
            }                
        }
        return $rows;
    }

    protected function getStatusType()
    {
        $rows = [];
        $rows[1] = "<span class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Ожидает исполнителя'></span>";
        $rows[2] = "<span class='far fa-check-circle text-warning fa-md' data-toggle='tooltip' title='Ожидает выполнения заявки'></span>";
        $rows[3] = "<span class='far fa-check-circle text-success fa-md' data-toggle='tooltip' title='Заявка выполнена'></span>";
        return $rows;
    }

    protected function getStatus()
    {
        $rows[1] = "Ожидает исполнителя";
        $rows[2] = "Ожидает выполнения заявки";
        $rows[3] = "Заявка выполнена";
        return $rows;
    }

    protected function getType()
    {
        $rows[1] = "Атри-проект свободная";
        $rows[2] = "Атри-проект";
        $rows[3] = "Расчет теплообменников";
        // $rows[5] = "Насосные станции";
        return $rows;
    }

    protected function parseRow($r)
    {
       
        
        $r['action'] = (string)view('request::manager.grid.action', [
            'id' => $r['id'],
            'view' => router("admin.request.view", $r['id']),
        ]);
    /*		
        $r['ab'] = '<div class="btn-group">
                                <button type="button" onclick="location.href = \'/administration/module/task/view/'.$r['id'].'\'" class="btn btn-xs btn-primary">Открыть</button>
                                <button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
                                <ul role="menu" class="dropdown-menu">
                                    <li><a href="javascript:void(0);" onclick="modal_o(\'#modal-ok-'.$r['id'].'\')">' . $lang['task_comment_ok'] .'</a></li>
                                    <li><a href="javascript:void(0);" onclick="modal_o(\'#modal-comment-'.$r['id'].'\')">' . $lang['task_comment_add'] .'</a></li>
                                    <li class="divider"></li>
                                    
                                    <li><a href="javascript:void(0);" onclick="modal_o(\'#modal-archive-'.$r['id'].'\')">' . $lang['task_inarchive'] .'</a></li>
                                </ul>
                            </div>';//modalConstract($r['id'], $r['fromid'], $r['toid'], $r['status'], true, true, true);
                        $r['ab'] .= modalConstract($r['id'], $r['fromid'], $r['toid'], 1, $r['status']);
                        $r['ab'] .= modalConstract($r['id'], $r['fromid'], $r['toid'], 2, $r['status']);
                        $r['ab'] .= modalConstract($r['id'], $r['fromid'], $r['toid'], 3, $r['status']);
                        */
        
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        return $r;
    }
    
}
