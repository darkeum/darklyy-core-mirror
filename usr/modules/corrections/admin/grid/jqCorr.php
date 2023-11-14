<?php

class jqCorr extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$uid = $core->auth->user_info['id'];		
		$this->table = 'JMY_corrections';
		$this->nav = array(
            'excel' => true,
            'exceltext' => 'Сохранить в Excel',			
            'fastedit' => ($core->auth->allowAccessById($uid, '13') ? true : false),			
            'trash' => true,			
            'trashtext' => 'Показывать удаленные',				
            'trashfield' => 't.del',				
        );		
        $uid_dir_types = $this->getUidDirTypes();
        $module_types = $this->getModuleTypes();
        $uid_exec_types = $this->getUidExecTypes();
        $status = $this->getStatus();
        $status_select = $this->getStatus(true);
        $types = $this->getTypes();
		$types_select = $this->getTypes(true);	
		$mark = $this->getMark();
		$mark_select = $this->getMark(true);	
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
		$this->query = "SELECT {fields} FROM JMY_corrections as t WHERE {where} ";
        $this->cols = array(		
			
            'id' => array(
				'label' => 'Код',
				'width' => 8,
				'align' => 'center',				
                'db' => 't.id',
            ),
			
			'mark' => array(
				'label' => 'Важность',
				'width' => 18,
				'align' => 'center',
				'db' => 't.mark',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $mark,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($mark_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ),	
			
			'type' => array(
				'label' => 'Тип',
				'width' => 6,
				'align' => 'center',
				'db' => 't.type',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($types_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ),	
			
			
			'text' => array(
				'label' => 'Описание проблемы',
				'width' => 50,
				'db' => 't.text',
                'hidden' => false,
                'null' => 'Не указано',
				'search_op' => 'like',
				'editable' => ($core->auth->allowAccessById($uid, '13') ? true : false),
				'edittype' => "text",
            ),		
			
			'url' => array(
				'label' => 'Ссылка',
				'width' => 8,
				'db' => 't.url',
                'hidden' => false,
				'encode' => false,
                'null' => 'Не указано',
				'align' => 'center',
				'search_op' => 'like',
				'editable' =>false,
            ),	
			
			'modules' => array(			
				'label' => 'Модуль',
                'db' => 't.modules',
                'width' => 20,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $module_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($module_types, 'Все'),
                ),
                'search_op' => 'equal',
				'editable' => ($core->auth->allowAccessById($uid, '13') ? true : false),
                'edittype' => 'select',
                'editoptions' => array(
                    'value' => new jqGrid_Data_Value($module_types, 'Все'),
                ),
            ),	
			
			'uid' => array(			
				'label' => 'Постановщик',
                'db' => 't.uid',
                'width' => 20,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_dir_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_dir_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),	
			            
           'date' => array(
				'label' => 'Дата заявки',
                'width' => 20,
				'db' => 't.date_add',
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
			
			'date_plan' => array(
				'label' => 'Планируемая дата',
                'width' => 20,
				'db' => 't.date_plan',
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
				'editable' => ($core->auth->allowAccessById($uid, '13') ? true : false),
				'edittype' => 'text',
				'editoptions' => array(
                    'dataInit' => new jqGrid_Data_Raw('function (element) {const dk_date = $(element).val(); const new_dk_date = dk_date.split("."); $(element).val(new_dk_date[2]+"-"+new_dk_date[1]+"-"+new_dk_date[0]); $(element).datepicker({id: \'date_plan_datePicker\', dateFormat: \'yy-mm-dd\', showOn: \'focus\'});} '),
                ),
               
				
            ),	
			
			'date_end' => array(
				'label' => 'Дата завершения',
                'width' => 20,
				'db' => 't.date_end',
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
				'width' => 8,
				'align' => 'center',
				'db' => 't.status',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $status,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ),
			
			'ab' => array(
				'label' => 'Действия',
				'width' => 20,
				'hidden' => ($core->auth->allowAccessById($uid, '13') ? false : true),
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

    protected function getStatus($select = false)
    {
		global $lang;
        $rows_select = array();	
		$rows_select[0] = 'Неизвестно';
		$rows_select[1] = 'Заявка создана';
		$rows_select[2] = 'В процессе';
		$rows_select[3] = 'Выполнено';
		$rows_select[4] = 'Отклонено';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -3px;' class='fal fa-question-circle text-danger fa-md' data-toggle='tooltip' title='Неизвестно'></span>";
        $rows[1] = "<span style='margin-left: -3px;' class='fal fa-plus-circle text-info fa-md' data-toggle='tooltip' title='Заявка создана'></span>";    
        $rows[2] = "<span style='margin-left: -3px;' class='fal fa-clock text-warning fa-md' data-toggle='tooltip' title='В процессе'></span>";    
        $rows[3] = "<span style='margin-left: -3px;' class='fal fa-check-circle text-success fa-md' data-toggle='tooltip' title='Выполнено'></span>";    
        $rows[4] = "<span style='margin-left: -3px;' class='fal fa-circle text-danger fa-md' data-toggle='tooltip' title='Отклонено'></span>";    
		
        return ($select ?  $rows_select :  $rows);
    }

    protected function getTypes($select = false)
    {       	
		global $db, $lang, $core;		
        $rows_select = array();	
		$rows_select[0] = 'Не указано';
		$rows_select[1] = 'Корпоративный портал';
		$rows_select[2] = 'Сайт';		
		$rows_select[3] = 'Администрирование компьютеров';		
		$rows_select[4] = 'Администрирование оргтехники';		
		
        $rows = [];
		$rows[0] = "<span style='margin-left: -3px;' class='fal fa-question text-danger fa-md' data-toggle='tooltip' title='Неизвестно'></span>";
        $rows[1] = "<span style='margin-left: -3px;' class='fal fa-briefcase text-info fa-md' data-toggle='tooltip' title='Корпоративный портал'></span>";    
        $rows[2] = "<span style='margin-left: -3px;' class='fal fa-globe text-success fa-md' data-toggle='tooltip' title='Сайт'></span>";     				
        $rows[3] = "<span style='margin-left: -3px;' class='fal fa-desktop text-warning fa-md' data-toggle='tooltip' title='Администрирование компьютеров'></span>";     				
        $rows[4] = "<span style='margin-left: -3px;' class='fal fa-print text-alert fa-md' data-toggle='tooltip' title='Администрирование оргтехники'></span>";     				
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getMark($select = false)
    {       	
		global $db, $lang, $core;		
        $rows_select = array();	
		$rows_select[0] = 'Не указано';
		$rows_select[1] = 'Корпоративный портал';
		$rows_select[2] = 'Сайт';		
		
        $rows = [];
		$rows[0] = '<span class="bg-alert card-label mod-selectable">Без приоритета</span>';   			
		$rows[1] = '<span class="bg-info card-label mod-selectable">Обычный</span>';  				
		$rows[2] = '<span class="bg-system card-label mod-selectable">Выше среднего</span>';  				
		$rows[3] = '<span class="bg-warning card-label mod-selectable">Важный</span>';  				
		$rows[4] = '<span class="bg-danger card-label mod-selectable">Критический</span>';   				
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getUidDirTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_corrections as c LEFT JOIN JMY_users as u on (c.uid=u.id) ORDER BY u.surname");
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
	
	protected function getModuleTypes()
    {
		global $db, $lang, $core;		
        $result = $this->DB->query("SELECT * FROM ".DB_PREFIX."_plugins ORDER BY content ASC");
        $rows = array();
		$rows[0] = 'Не указано';
        while($r = $this->DB->fetch($result))
        {	
            $rows[$r['title']] = $r['content'];
        }
        return $rows;
    }
	
	protected function getUidExecTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_request as t LEFT JOIN JMY_users as u on (t.uid_exec=u.id) WHERE t.uid_exec <> '0' ORDER BY u.surname");
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

    protected function parseRow($r)
    {
		global $core, $lang;   
		$uid = $core->auth->user_info['id'];
		if($core->auth->allowAccessById($uid, '13'))
		{
		   $r['ab'] = '<div class="btn-group">
							<button type="button" onclick="modalCompl('.$r['id'].');" class="btn btn-xs btn-primary">Завершить</button>
							<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">'.$lang['action'].'</span></button>
							<ul role="menu" class="dropdown-menu">	 							
								<li><a href="javascript:void(0)" onclick="modalRemove('.$r['id'].')" data-status="" data-id="'.$r['id'].'" data-view-id="г">Удалить</a></li> 
							</ul>						
						</div>'; 
		}    
		if(!empty($r["url"]))
		{
			$r["url"] = "<span data-url='".$r["url"]."' onclick=\"window.open('".$r["url"]."')\" style='cursor: pointer; margin-left: -3px;     font-size: 14px;' class='fal fa-external-link text-info fa-md' data-toggle='tooltip' title='Перейти'></span>";
		}
		else
		{
			$r["url"] = "<span data-url='' style='margin-left: -3px;     font-size: 14px;' class='fal fa-external-link text-secondary' data-toggle='tooltip' title='Отклонено'></span>";
		}
		
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        if(is_date($r["date_end"], "Y-m-d H:i:s")) $r["date_end"] = date("d.m.Y H:i:s", strtotime($r["date_end"]));
        if(is_date($r["date_plan"], "Y-m-d")) $r["date_plan"] = date("d.m.Y", strtotime($r["date_plan"]));
		
        return $r;
    }
	
	protected function opDialog()
    {
      echo 'r';
    }
}