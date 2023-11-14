<?php

class jqSupplyLogistics extends jqGrid
{
    protected function init()
    {
		global $lang;
		$uid_types = $this->getUidTypes();  
		$arrived_types = $this->getArrivedTypes();
		$arrived_types_select = $this->getArrivedTypes(true);
		$delivery_types = $this->getDeliveryTypes();
		$delivery_types_select = $this->getDeliveryTypes(true);
		$date_types = $this->getDateTypes();
        $date_types_select = $this->getDateTypes(true);
        $org_types = $this->getOrgTypes();
        
		/*$this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );*/
		$this->query = "SELECT {fields} FROM JMY_supply_order as s LEFT JOIN JMY_organizations as o on(o.id=s.oid) LEFT JOIN JMY_users as u on(u.id=s.uid)  LEFT JOIN JMY_sales as sl on(sl.id=s.sales_id) LEFT JOIN JMY_object as obj on(obj.id=s.object) WHERE s.date_ship IS NOT NULL AND {where} ";
        $this->cols = array(		
			
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),
            'sales_real' => array('hidden' => true,
                'db' => 'sl.id',
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 20,
				'db' => 's.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
					
			'flag' => array(
				'label' => '№',
				'width' => 20,
				'db' => '(CASE WHEN s.date_end <= CURRENT_TIMESTAMP THEN 1 ELSE 0 END)',				
				'search_op' => 'like',
				'hidden' => true,
            ),
			
			'flag_n' => array(
				'label' => '№',
				'width' => 20,
				'db' => '(CASE WHEN s.date_end IS NULL THEN 1 ELSE 0 END)',
				'hidden' => true,
				'search_op' => 'like',
            ),
			
			
				
			
			'oid' => array(
				'label' => $lang['oid'],
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
				'width' => 45,				
            ),	
			
			'bill_code' => array(
				'label' => 'Номер счета',
                'hidden' => false,
				'db' => 's.bill_code',
				'encode' => false,
				'width' => 30,		
				'search_op' => 'like'
            ),	
			
			'sales' => array(
				'label' => 'Заказ',
                'hidden' => false,
				'db' => 'sl.numb',				
				'null' => 'Не указано',
				'encode' => false,
				'width' => 20,	
				'search_op' => 'like'
            ),	
			
			'object_id' => array(
                'hidden' => true,
				'db' => 's.object',				
            ),
			
			
			'object' => array(
				'label' => 'Объект',
                'hidden' => false,
				'db' => 'obj.name',
				'encode' => false,
				'width' => 30,				
            ),	
			
            'org' => array(			
				'label' => 'Организация',
				'null' => 'Не указано',
                'db' => 's.org',
               'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'replace' => $org_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($org_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
            
			'date_bill' => array(
				'label' => 'Дата счета',
                'width' => 20,
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
			'date_work' => array(
				'label' => 'Дата размещения',
                'width' => 20,
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
			'date_ship' => array(
				'label' => 'Дата отгрузки',
                'width' => 20,
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
				'label' => 'Дата поставки',
                'width' => 20,
				'db' => 's.date_end',
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
            
          
            
                       
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 's.uid',
                'width' => 25,              
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
			
			 'date_flag' => array(
				'label' => 'Рассмотрение',
				'db' => '(CASE WHEN s.date_end IS NULL THEN 1 ELSE 0 END)',
				'align' => 'center',
				'width' => 10,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $date_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($date_types_select, 'Все'),
					
                ),
                'search_op' => 'equal',
            ),
			
			 'delivery' => array(
				'label' => 'Тип доставки',
				'db' => 's.delivery',
				'align' => 'center',
				'width' => 10,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $delivery_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($delivery_types_select, 'Все'),
					
                ),
                'search_op' => 'equal',
            ),
			
			'arrived' => array(
				'label' => 'Статус',
				'db' => 's.arrived',
				'align' => 'center',
				'width' => 10,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $arrived_types,
                'stype' => 'select',
                'searchoptions' => array(				
                    'value' => new jqGrid_Data_Value($arrived_types_select, 'Все'),
					'defaultValue' => '0',
                ),
                'search_op' => 'equal',
            ),
			
			'ab' => array(
				'label' => $lang['action'],
				'width' => 40,
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
	
	protected function getArrivedTypes($select = false)
    {  
		global $lang;   
        $rows_select = array();	
		$rows_select[0] = 'Доставляется';
		$rows_select[1] = 'Доставлен';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='far fa-shipping-fast text-danger fa-md' data-toggle='tooltip' title='Доставляется'></span>";
        $rows[1] = "<span style='margin-left: -12px;' class='far fa-shipping-fast text-success fa-md' data-toggle='tooltip' title='Доставлен'></span>";   		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getDeliveryTypes($select = false)
    {  
		global $lang;   
        $rows_select = array();	
		$rows_select[1] = 'Самовывоз';
		$rows_select[2] = 'ТК';
		
		$rows = array();	
		$rows[1] = "<span style='margin-left: -12px;' class='far fa-plane text-alert fa-md' data-toggle='tooltip' title='Самовывоз'></span>";
        $rows[2] = "<span style='margin-left: -12px;' class='far fa-truck-container text-system fa-md' data-toggle='tooltip' title='ТК'></span>";   		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getDateTypes($select = false)
    {  
		global $lang;   
        $rows_select = array();	
		$rows_select[0] = 'Рассмотрено';
		$rows_select[1] = 'Ожидает рассмотрения';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='fal fa-calendar-check text-success fa-md' data-toggle='tooltip' title='Рассмотрено'></span>";
        $rows[1] = "<span style='margin-left: -12px;' class='fal fa-calendar-check text-danger fa-md' data-toggle='tooltip' title='Ожидает рассмотрения'></span>";   		
        return ($select ?  $rows_select :  $rows);
    }

    protected function getOrgTypes()
    {
        $where = "";
        if(!check_uid_org()){
            $org = get_uid_org();
            if(count($org) > 0){
                $where = " WHERE r.id IN(".implode(", ",$org).")";
            } else {
                $where = " WHERE r.id = '-1'";
            }
        }
        $result = $this->DB->query("SELECT r.id, o.name FROM JMY_org as r LEFT JOIN JMY_organizations as o on(r.oid=o.id)".$where);
        $rows = array();
        while($r = $this->DB->fetch($result))
        {			
            $rows[$r['id']] = $r['name'];
        }
        return $rows;
    }
	
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_supply_order as s LEFT JOIN JMY_users as u on (s.uid=u.id) WHERE s.uid <> '0'");
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
	
	protected function buildOrderBy($sidx, $sord)
	{
			return "ORDER BY flag DESC, flag_n ASC, date_end ASC, $sidx $sord";
		/*
		if(in_array($sidx, array('user_id', 'post_id')))
		{
		
		}
		else
		{
			return parent::buildOrderBy($sidx, $sord);
		}*/
	}
    
    protected function parseRow($r)
    {
		global $lang;   
        $r['numb'] = module_perfix('supply_logistics', $r['id']);	 
		$r['bill_code'] = empty($r['bill_code']) ? 'Не заполнено' : $r['bill_code'];
		$r['delivery'] = empty($r['delivery']) ? "<span style='margin-left: -12px;' class='far fa-question-circle text-info fa-md' data-toggle='tooltip' title='Не проставлено'></span>" : $r['delivery'];
		$r['sales'] = empty($r['sales']) ? 'Не указано' : '<a href="'.ADMIN.'/module/sales/order/'.$r['sales_real'].'" target="_blank">'.$r['sales'].'</a>';
		$r['object'] =  empty($r['object']) ? 'Не указано' : '<a href="'.ADMIN.'/object/view/'.$r['object_id'].'" target="_blank">'.$r['object'].'</a>';
        $r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/supply/detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>						
					</div>					
					<div class="btn-group">
						<button type="button" onclick="modal_o(\'#modal-active-'. $r['id'].'\')" class="btn btn-xs btn-'.($r['arrived'] == 0 ? 'success' : 'warning').'"><span style="    font-size: 14px;" class="far fa-truck" data-toggle="tooltip" title="'.($r['arrived'] == 0 ? $lang['supply_logistics_arrived_on'] : $lang['supply_logistics_arrived_off']).'"></span></button>						
					</div>
					<div id="modal-active-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="far fa-truck"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">'.str_replace('[logistics]', module_perfix('supply_logistics', $r['id']), ($r['arrived'] == 0 ? $lang['supply_logistics_arrived_on_title'] : $lang['supply_logistics_arrived_off_title'])).'</h3>
								<hr class="short alt">
								<p>'.str_replace('[logistics]', module_perfix('supply_logistics', $r['id']), ($r['arrived'] == 0 ? $lang['supply_logistics_arrived_on_text'] : $lang['supply_logistics_arrived_off_text'])).'</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" data-id="'.$r['id'].'" class="btn btn-'.($r['arrived'] == 0 ? 'success' : 'warning').' logisticsBtn">'.($r['arrived'] == 0 ? $lang['supply_logistics_arrived_on'] : $lang['supply_logistics_arrived_off']).'</button>
							  </div>
							</div>
						</div>
					<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/supply/create_excel_detail/' . $r['id'] . '\'" class="btn btn-xs btn-alert"><span class="far fa-save" data-toggle="tooltip" title="Распечатать"></span></button>						
					</div>';
        if(is_date($r["date_bill"])) $r["date_bill"] = date("d.m.Y", strtotime($r["date_bill"]));   
		if(is_date($r["date_work"])) $r["date_work"] = date("d.m.Y", strtotime($r["date_work"]));   
		if(is_date($r["date_ship"])) $r["date_ship"] = date("d.m.Y", strtotime($r["date_ship"]));   
		if(is_date($r["date_end"])) $r["date_end"] = date("d.m.Y", strtotime($r["date_end"]));
		$cols_class = null;
		if(!empty($r["date_end"])&&(strtotime($r["date_end"]) < time())&&($r['arrived'] != 1))
		{
			$cols_class = 'red';
		}
		elseif(empty($r["date_end"])) 
		{
			$cols_class = 'yellow';
		}
        $r['_class'] = $cols_class;
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}