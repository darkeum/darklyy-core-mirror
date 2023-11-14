<?php

class jqRequest extends jqGrid
{
    protected function init()
    {
        global $taskAttr, $lang;
		//$this->nav = array('excel' => true);	
		//$auto_types = $this->getAutoTypes();
		$uid_dir = $this->getUidTypes('uid_dir');
		$uid_exec = $this->getUidTypes('uid_exec');
		$type = $this->getType();
        //$toid = $this->getUidTypes('toid');
        $status_type = $this->getStatusType();
        $status_value = $this->getStatus();
		//$pay_types = $this->getPayTypes();
        $this->options = array(
            'sortname' => 'date',
            'sortorder' => 'DESC',
        );
        $this->query = "SELECT {fields} FROM JMY_request as r WHERE {where} ";
        $this->cols = array(
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'r.id',
            ),

            'numb' => array(
				'db' => 'r.id',
				'label' => '№',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
            ),
            'title' => array(
				'db' => 'r.title',
				'label' => 'Заголовок',
				'width' => 30,
                'hidden' => false,
                'null' => "Отсутствует",
				'search_op' => 'like'
            ),
            'type' => array(
				'label' => 'Тип',
                'db' => 'r.type',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $type,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($type, 'Все'),
                ),
                'search_op' => 'equal',				
            ),
            'uid_dir' => array(
				'label' => 'Постановщик',
                'db' => 'r.uid_dir',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_dir,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_dir, 'Все'),
                ),
                'search_op' => 'equal',				
            ),
            'uid_exec' => array(
				'label' => 'Исполнитель',
                'db' => 'r.uid_exec',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_exec,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_exec, 'Все'),
                ),
                'search_op' => 'equal',				
            ),
            'date' => array(
				'label' => 'Дата',
				'db' => 'r.date',
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
            'status' => array(
				'label' => 'Статус',
                'db' => 'r.status',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'replace' => $status_type,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_value, 'Все'),
                ),
                'search_op' => 'equal',	
            ),
            /*
            'type' => array(
				'label' => '<span class="octicon octicon-alignment-unalign text-dark octicon-md" data-toggle="tooltip" title="'.$lang['task_type'].'"></span>',
				'width' => 10,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
            ),		
			
			'title' => array(
				'db' => 't.title',
				'label' => 'Заголовок',
				'width' => 100,
				'hidden' => false,
				'manual' => false,
				'encode' => true,
				'sortable' => true,			
                'search_op' => 'like',
				'search' => true,
				'title' => true,
				'null'=>'Не указан',
            ),		

			'fromid' => array(
				'label' => 'Отправитель',
                'db' => 't.fromid',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $fromid,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($fromid, 'Все'),
                ),
                'search_op' => 'equal',				
            ),	
            
            'toid' => array(
				'label' => 'Получатель',
                'db' => 't.toid',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $toid,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($toid, 'Все'),
                ),
                'search_op' => 'equal',				
            ),
			
				
			'object' => array(
				'label' => 'Объект',
                'hidden' => false,
				'db' => 'obj.name',
				
				'null' => 'Не указано',
				'encode' => false,
				'width' => 40,				
            ),	
			
			'date' => array(
				'label' => 'Начало',
                'width' => 40,
                'db' => '(SELECT FROM_UNIXTIME(t.date))',
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => array(
                        array('text' => 'За 2016 год', 'dateStart' => '2016-01-01', 'dateEnd' => '2016-12-31'),
                        array('text' => 'За 2017 год', 'dateStart' => '2017-01-01', 'dateEnd' => '2017-12-31'),
						array('text' => 'За 2018 год', 'dateStart' => '2018-01-01', 'dateEnd' => '2018-12-31'),
                    ),
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',                       
                    ),
                ))),
                'search_op' => 'date_range',
            ),

            'dateend' => array(
				'label' => 'Окончание',
                'width' => 40,
                'db' => '(SELECT (CASE WHEN (t.endsw = 0) THEN \'Без учета времени\' ELSE FROM_UNIXTIME(t.dateend) END))',
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => array(
                        array('text' => 'За 2016 год', 'dateStart' => '2016-01-01', 'dateEnd' => '2016-12-31'),
                        array('text' => 'За 2017 год', 'dateStart' => '2017-01-01', 'dateEnd' => '2017-12-31'),
						array('text' => 'За 2018 год', 'dateStart' => '2018-01-01', 'dateEnd' => '2018-12-31'),
                    ),
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',                       
                    ),
                ))),
                'search_op' => 'date_range',
            ),
            
            'status' => array(
				'label' => 'Статус',
                'db' => 't.status',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'replace' => $status_type,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_value, 'Все'),
                ),
                'search_op' => 'equal',	
            ),
			'ab' => array(
				'label' => 'Действия',
				'width' => 50,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
            ),
			*/
            	
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
	
	protected function getUidTypes($type)
    {
    global $core, $db;
    
        switch($type){
            case "uid_dir":
                $result = $db->query("SELECT DISTINCT r.uid_dir, u.* FROM JMY_request as r LEFT JOIN JMY_users as u on (r.uid_dir=u.id)"); 
            break;
            case "uid_exec":
                $result = $db->query("SELECT DISTINCT r.uid_exec, u.* FROM JMY_request as r LEFT JOIN JMY_users as u on (r.uid_exec=u.id)"); 
            break;
        }
   
        $rows = array();
        $rows["0"] = "Гость";
        while($r = $db->getRow($result))
        {	
            $rows[$r['id']] = shortUserName($r['id']);
        }
        return $rows;
    }
    
    protected function getStatusType()
    {
    global $db, $lang, $core;
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
        return $rows;
    }

    protected function parseRow($r)
    {
		global $org_array, $lang, $db; 		
    global $adminTpl, $core, $admin_conf, $lang;
    $r["numb"] = $r["id"];
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
	
	 protected function opDialog()
    {
      echo 'r';
    }
}