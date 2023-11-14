<?php

class jqDaemon extends jqGrid
{
    protected function init()
    {
        global $core;
        $uid = $core->auth->user_info['id'];
        $this->table = 'JMY_daemon';
		$uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $status = $this->getStatus();
        $status_select = $this->getStatus(true);
        //$autostart = $this->getAutostart();
        $autostart_select = $this->getAutostart(true);
        $this->nav = array(
            'excel' => true,
            'exceltext' => 'Сохранить в Excel',
            'fastedit' => ($core->auth->allowAccessById($uid, '13') ? true : false),
            'trash' => true,
            'trashtype' => 'null',
            'trashtext' => 'Показывать удаленные',
            'trashfield' => 's.deleted_at',
        );
        $this->query = "SELECT {fields} FROM JMY_daemon AS s WHERE {where} ";
        $this->cols = array(
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),
			
			'pid' => array(
                'label' => 'PID процесса',
				'db' => 's.pid',				
				'width' => 25,
				'encode' => false,
				'hidden' => false,
				'null' => '',
				'search_op' => 'like'
            ), 	

            'title' => array(
                'label' => 'Название',
				'db' => 's.title',				
				'width' => 25,
				'encode' => false,
				'hidden' => false,
				'null' => 'Не указан',
				'search_op' => 'like'
            ), 

            'iswork' => array(
                'label' => 'Статус',                
                'width' => 25,
                'align' => 'center',
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => true,
                'search_op' => false,
				'search' => true,
				'excel_hidden' => true,
            ),
            
            'uid' => array(			
				'label' => 'Ответственный',
                'db' => 's.uid',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'created_at' => array(
				'label' => 'Дата запуска',
				'db' => 's.created_at',
                'width' => 25,
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => presetRanges(),                  
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',                       
                    ),
                ))),
                'search_op' => 'date_range',
            ),

            'command' => array(
                'label' => 'Команда',
				'db' => 's.command',
				'width' => 60,
                'encode' => false,
                'sortable' => false,
				'hidden' => false,
                'null' => 'Не указана',
                'editable' => ($core->auth->allowAccessById($uid, '13') ? true : false),
				'edittype' => "text",
				'search' => false,
				'search_op' => false
            ),

            'comment' => array(
                'label' => 'Комментарий',
				'db' => 's.comment',
				'width' => 40,
                'encode' => false,
                'sortable' => false,
				'hidden' => false,
                'null' => 'Не указан',
                'editable' => ($core->auth->allowAccessById($uid, '13') ? true : false),
				'edittype' => "text",
                'search' => false,
				'search_op' => false
            ),

            'autostart' => array(
                'label' => 'Автозапуск',
				'db' => 's.autostart',
                'width' => 40,
                'align' => 'center',
                'encode' => false,
                'sortable' => false,
				'hidden' => false,
                'editable' => ($core->auth->allowAccessById($uid, '13') ? true : false),
                'edittype' => "text",                   
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($autostart_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ),
			
			'status' => array(
                'label' => 'Режим работы',
				'width' => 30,
				'align' => 'center',
				'db' => 's.status',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $status, //замена значений на иконки
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ),
			
			'ab' => array(
				'label' => 'Выберите действие',
				'width' => 50,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,							
				'excel_hidden' => true,
            ),
        );

        $this->render_filter_toolbar = true;
    }
	
	protected function searchOpDateRange($c, $val)
    {
        //--------------
        // Date range
        //--------------

        if(strpos($val, ' - ') !== false)
        {
            list($start, $end) = explode(' - ', $val, 2);

            $start = strtotime(trim($start));
            $end = strtotime(trim($end));

            if(!$start or !$end)
            {
                throw new jqGrid_Exception('Invalid date format');
            }

            #Stap dates if start is bigger than end
            if($start > $end)
            {
                list($start, $end) = array($end, $start);
            }

            $start = date('Y-m-d', $start);
            $end = date('Y-m-d', $end);

            return $c['db'] . " BETWEEN '$start' AND '$end'";
        }

        //------------
        // Single date
        //------------

        $val = strtotime(trim($val));

        if(!$val)
        {
            throw new jqGrid_Exception('Invalid date format');
        }

        $val = date('Y-m-d', $val);

        return "DATE({$c['db']}) = '$val'";
    }

    protected function initDatepicker($options = null)
    {
        $options = is_array($options) ? $options : array();

        return new jqGrid_Data_Raw('function(el){$(el).datepicker(' . jqGrid_Utils::jsonEncode($options) . ');}');
    }

    protected function initDateRangePicker($options = null)
    {
        $options = is_array($options) ? $options : array();

        return new jqGrid_Data_Raw('function(el){$(el).daterangepicker(' . jqGrid_Utils::jsonEncode($options) . ');}');
    }
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_users as u WHERE u.active = '1' AND u.group = '1' ORDER BY u.surname");
        $rows = array();
		$rows[0] = 'Не указан';
        while($r = $this->DB->fetch($result))
        {	
			$result_row = $r['surname'].' ';
			$result_row .= mb_substr($r['name'], 0, 1) . '. ';
			$result_row .= mb_substr($r['ochestvo'], 0, 1) . '.';
            $rows[$r['id']] = $result_row;
        }
        return $rows;
    }

    protected function parseRow($r) {
        $isTrashTabOpen = isset($_COOKIE[$this->grid_id.'Trash']) && $_COOKIE[$this->grid_id.'Trash'] == '1';
		$process = new \Daemon\DaemonProcess();
		$process->setPid($r['pid']);
        $status = $process->status(); // возвращает true или false
        $deleteBtn = '';
        $processOnImg = "<span style='margin-left: -3px;' class='far fa-circle text-success fa-md' data-toggle='tooltip' title='Запущен'></span>";
        $processOffImg = "<span style='margin-left: -3px;' class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Не запущен'></span>";       
        $r['iswork'] = ($status ? $processOnImg : $processOffImg);
        $btnStopDaemon = '<li><a href="javascript:void(0);" onclick="stopDaemon('. $r['id'].');">Остановить</a></li>';
        $btnStartDaemon = '<li><a href="javascript:void(0);" onclick="startDaemon(' .$r['id']. ');">Запустить</a></li>';
        if ($isTrashTabOpen) {
            $btnStartStop = '';
            $btnRestore = '';
            $deleteBtn = '<li><a href="javascript:void(0);" onclick="restoreModal('. $r['id'].');">Восстановить</a></li>';

        } else {
           $btnStartStop = $status ? $btnStopDaemon : $btnStartDaemon;
           $btnReload = $status ? '<li><a href="javascript:void(0);" onclick="reloadDaemon('. $r['id'].');">Перезапустить</a></li>' : "";
           $deleteBtn = '<li><a href="javascript:void(0);" onclick="removeModal('. $r['id'].');">Удалить</a></li> ';
        }

        $r['ab'] = <<<HTML
            <div class="btn-group">
                <button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
                <ul role="menu" class="dropdown-menu">
                    {$btnStartStop}
                    {$btnReload}
                    {$deleteBtn}
                </ul>
            </div>
HTML;

        if (is_date($r["created_at"], "Y-m-d H:i:s")) $r["created_at"] = date("d.m.Y H:i:s", strtotime($r["created_at"]));
        return $r;
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

    protected function getAutostart($isAutostartOn = false) {
        $rows_select = array();
		$rows_select[0] = 'Выключен';
		$rows_select[1] = 'Включен';
		
		$rows = array();
		$rows[0] = "<span style='margin-left: -3px;' class='far fa-question-circle text-danger fa-md' data-toggle='tooltip' title='Выключен'></span>";
        $rows[1] = "<span style='margin-left: -3px;' class='far fa-plus-circle text-info fa-md' data-toggle='tooltip' title='Включен'></span>";
		
        return ($isAutostartOn ?  $rows_select :  $rows);
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}