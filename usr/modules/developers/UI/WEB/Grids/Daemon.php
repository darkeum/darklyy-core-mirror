<?php

namespace Developers\UI\WEB\Grids;


use Developers\Support\DaemonProcess;
use Boot\System\Grid\Grid;
use Boot\System\Database\DB;
use Boot\System\Grid\GridModern;

class Daemon extends GridModern
{

    /**
     * Тип подключение к базе данных
     *
     * @var string
     */
    protected $db_type = 'db';

    /**
     * Пространство имен
     *
     * @var string
     */
    protected $namespace = 'daemon';

    /**
     * Действие на двойной клик
     *
     * @var string
     */
    // protected $opts_type = "open";

    /**
     * Получение URL на открытие записи по двойному клику
     *
     * @return void
     */
    // protected function getOpenUrl()
    // {
    //     return route("admin.developers.daemons.show", ["id" => "@id"]);
    // }

    /**
     * Инициализация grid
     *
     * @return void
     */
    protected function init()
    {
        $status = $this->getStatus();
        $uid_types = $this->getUidTypes();
        //Заголовок таблицы
        $this->title = __('developers::admin.daemons');

        //Опции таблицы (в данном случае сортировка)
        $this->options = array(
            'sortname' => 'd.id',
            'sortorder' => 'DESC',
        );

        //Опции панели навигации
        $this->nav = array(
            'excel' => true,
            'exceltext' => __('grid.export.excel'),
            'trash' => true,
            'trashtype' => 'null',
            'trashtext' => __('grid.trash.show'),
            'trashfield' => 'd.deleted_at',
        );

        //Подключение к необходимой таблице
        $this->db = DB::table($this->namespace . ' as d')->whereNull('deleted_at');

        $this->cols = array(

            #Real id is hidden
            'id' => array(
                'title' => '#',
                'visible' => false,
                'db' => 'd.id',
            ),

            'pid' => array(
                'label' => 'PID процесса',
                'title' => 'PID процесса',
                'db' => 'd.pid',
                'width' => "5%",
                'null' => '',
                'search_op' => 'like'
            ),

            'title' => array(
                'title' => 'Название',
                'label' => 'Название',
                'db' => 'd.title',
                'width' => "5%",
                'encode' => false,
                'hidden' => false,
                'null' => 'Не указан',
                'search_op' => 'like'
            ),

            'iswork' => array(
                'title' => 'Статус',
                'label' => 'Статус',
                'width' => "10%",
                'className' => 'text-center',
                'hidden' => false,
                'manual' => true,
                'encode' => false,
                'sortable' => true,
                'search_op' => false,
                'search' => false,
                'excel_hidden' => true,
            ),

            'uid' => array(		
                'title' => 'Ответственный',	
				'label' => 'Ответственный',
                'db' => 'd.uid',
                'width' => "15%",              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_types,
                'stype' => 'select',
                'search_op' => 'equal',
            ),
    

            'command' => array(
                'title' => 'Команда',
                'db' => 'd.command',
                'width' => '20%',
                'render' => 'text',
                'encode' => false,
                'sortable' => false,
                'hidden' => false,
                'null' => 'Не указана',
                'search' => false,
                'edittype' => "text",
                
                'search_op' => false
            ),

            'comment' => array(
                'title' => 'Комментарий',
                'db' => 'd.comment',
                'width' => "15%",
                'encode' => false,
                'sortable' => false,
                'hidden' => false,
                'null' => 'Не указан',
                'search' => false,
                'edittype' => "text",
                
                'search_op' => false
            ),

            'autostart' => array(
                'title' => 'Автозапуск',
                'label' => 'Автозапуск',
                'db' => 'd.autostart',
                'width' =>"5%",
                'className' => 'text-center',
                'search_op' => 'equal',
                'excel_hidden' => true,
            ),

            'status' => array(
                'title' => 'Режим работы',
                'label' => 'Режим работы',
                'width' => "5%",
                'db' => 'd.status',
                'replace' => $status,
                'className' => 'text-center',
                'encode' => false,
              
        
                'search_op' => 'equal',
                'excel_hidden' => true,
            ),

            'action' => array(
                'type' => 'actions',
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


    protected function getStatus($select = false)
    {
        $rows_select = array();	
		$rows_select[0] = 'Остановлено вручную';
		$rows_select[1] = 'Нормальная работа';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -3px;' class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Остановлено вручную'></span>";
        $rows[1] = "<span style='margin-left: -3px;' class='far fa-circle text-success fa-md' data-toggle='tooltip' title='Нормальная работа'></span>";    
		
        return ($select ?  $rows_select :  $rows);
    }

    protected function getUidTypes()
    {
		
        $result = DB::table("users as u")->where('u.active',1)->where('u.group',1)->orderBy('u.surname')->get();
        
        $rows = array();
		$rows[0] = 'Не указан';
        foreach($result as $r)
        {	
			$result_row = $r->surname .' ';
			$result_row .= mb_substr($r->name, 0, 1) . '. ';
			$result_row .= mb_substr($r->ochestvo, 0, 1) . '.';
            $rows[$r->id] = $result_row;
        }
        return $rows;
    }

    protected function parseRow($r)
    {
        $isTrashTabOpen = isset($_COOKIE[$this->grid_id . 'Trash']) && $_COOKIE[$this->grid_id . 'Trash'] == '1';
        $process = new DaemonProcess();
        $process->setPid($r['pid']);
        $status = $process->status(); // возвращает true или false
        $deleteBtn = '';
        $processOnImg = "<span style='margin-left: -3px;' class='far fa-circle text-success fa-md' data-toggle='tooltip' title='Запущен'></span>";
        $processOffImg = "<span style='margin-left: -3px;' class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Не запущен'></span>";
        $r['iswork'] = ($status ? $processOnImg : $processOffImg);

        // btns
        $btnStopDaemon = '<li><a href="javascript:void(0);" class="dropdown-item" onclick="stopDaemon(' . $r['id'] . ');">Остановить</a></li>';
        $btnStartDaemon = '<li><a href="javascript:void(0);" class="dropdown-item" onclick="startDaemon(' . $r['id'] . ');">Запустить</a></li>';

        if ($isTrashTabOpen) {
            $btnStartStop = '';
            $btnRestore = '';
            $deleteBtn = '<li><a href="javascript:void(0);" class="dropdown-item" onclick="restoreModal(' . $r['id'] . ');">Восстановить</a></li>';
        } else {
            $btnStartStop = $status ? $btnStopDaemon : $btnStartDaemon;
            $btnReload = $status ? '<li><a href="javascript:void(0);" class="dropdown-item"  onclick="reloadDaemon(' . $r['id'] . ');">Перезапустить</a></li>' : "";
            $deleteBtn = '<li><a href="javascript:void(0);" class="dropdown-item" onclick="removeModal(' . $r['id'] . ');">Удалить</a></li> ';
        }


        $r['action'] =(string)view('developers::manager.grid.action', [
            'btnStartStop' => $btnStartStop,
            'btnReload' => $btnReload,
            'deleteBtn' => $deleteBtn,

            'status' => $status,
            'play' => $r['id'],
            'stop' => $r['id'],
            'reload' => $r['id'],
            'delete' => $r['id'],

        ]);
        // if (is_date($r["created_at"], "Y-m-d H:i:s")) $r["created_at"] = date("d.m.Y H:i:s", strtotime($r["created_at"]));
        return $r;
    }


}
