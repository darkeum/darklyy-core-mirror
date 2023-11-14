<?php

class jqVerification extends jqGrid
{
    protected function init()
    {
		//$this->nav = array('excel' => true);	
		$auto_types = $this->getAutoTypes();
		$uid_types = $this->getUidTypes();
		$status_types = $this->getStatus();
		$statusSelect_types = $this->getStatusSelect();
		//$pay_types = $this->getPayTypes();
        $this->options = array(
            'sortname' => 'date',
            'sortorder' => 'DESC',
        );
        $this->query = "SELECT {fields} FROM JMY_ver as v LEFT JOIN JMY_organizations as o on(o.id=v.oid) LEFT JOIN JMY_users as u on(u.id=v.uid) WHERE v.status <> 0  AND {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'v.id',
            ),
			'status_real' => array('hidden' => true,
                'db' => 'v.status',
            ),

            'numb' => array(
				'db' => 'v.id',
				'label' => '№',
				'width' => 10,
				'hidden' => false,
				'manual' => true,
				'search_op' => 'like'
            ),		

			'oid' => array(
				'label' => 'Контрагент',
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
				'width' => 80,				
            ),	
			
			'date' => array(
				'label' => 'Дата',
                'width' => 40,
                'db' => '(SELECT FROM_UNIXTIME(v.date))',
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

            'date_end' => array(
				'label' => 'Дата окончания',
                'width' => 40,
                'db' => '(SELECT (CASE WHEN (v.date_end = 0) THEN \'Не завершена\' ELSE FROM_UNIXTIME(v.date_end) END))',
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
            
            'total' => array(
				'db' => 'v.total',
				'label' => 'Сумма',
				'width' => 20,
				'hidden' => false,
				'null' => 'Неизвестно',
				'search_op' => 'like'
            ),
            
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 'v.uid',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),  
			'status' => array(
				'label' => 'Статус заказа',
                'db' => 'v.status',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'replace' => $status_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($statusSelect_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
            /*
            'status' => array(
				'label' => 'Статус заказа',
				'width' => 30,
				'hidden' => false,
				'manual' => true,
				'encode' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
            ),
            */
            /*
			'pay' => array(
				'label' => 'Оплата',
				'width' => 30,
				'db' => 'v.pay',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $pay_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($pay_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
            */
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
	
	protected function getAutoTypes()
    {       
        $rows = array();
		$rows[0] = 'Заполнен';
		$rows[1] = 'В наличии на складе';
		$rows[2] = 'Заказ ожидает оплаты';
		$rows[3] = 'Заказ ожидает поступления';
		$rows[4] = 'Ожидает оплаты';
		$rows[5] = 'Отгружен';
        return $rows;
    }
	/*
	protected function getPayTypes()
    {       
        $rows = array();
		$rows[0] = 'Не оплачен';
		$rows[1] = 'Оплачен';
		$rows[2] = 'Частично оплачен';
        return $rows;
    }
	*/
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_ver as r LEFT JOIN JMY_users as u on (r.uid=u.id) WHERE r.uid <> '0'");
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
    
    protected function getStatus()
    {
        $rows[2] = "<span class='fa fa-clock-o text-warning fa-md'></span>";
        $rows[3] = "<span class='fa fa-check-circle text-success fa-md'></span>";
        $rows[1] = "<span class='fa fa-clock-o text-info fa-md'></span>";
        return $rows;
    }
    
    protected function getStatusSelect()
    {
        global $db;
        $rows[2] = 'Выполняется';
        $rows[3] = 'Выполненно';
        $rows[1] = 'Не заполнено';
        $query = $db->query("SELECT d.*, o.name as oname  FROM ".DB_PREFIX."_ver as d LEFT JOIN ".DB_PREFIX."_organizations AS o on(d.oid=o.id)");
        
        return $rows;
    }

    protected function parseRow($r)
    {
		global $org_array, $lang, $db; 		
	global $adminTpl, $core, $admin_conf, $lang;
        $r['numb'] = 'ПР-'.$r['id'];	
		
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/verification/order/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a target="_blank" href="#">Распечатать акт</a></li> 
                            <li class="divider"></li>
                            <li><a href="javascript:void(0);" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Удалить</a></li> 
						</ul>
						<div id="modal-form-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">Удаление</h3>
								<p>Вы действительно хотите удалить?</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" onclick="location.href = \config('app.url').'/administration/module/verification/ajax/order_del/'.$r['id'].'\'" class="btn btn-danger">Удалить</button>
							  </div>
							</div>
						  </div>
					</div>';
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        if(is_date($r["date_end"], "Y-m-d H:i:s")) $r["date_end"] = date("d.m.Y H:i:s", strtotime($r["date_end"]));

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}