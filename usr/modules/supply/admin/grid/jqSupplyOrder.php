<?php

class jqSupplyOrder extends jqGrid
{
    protected function init()
    {
		global $lang, $db;
        $this->table = 'JMY_supply_order';
		$this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
            'sortorder' => 'asc',
			 'onSelectRow' => new jqGrid_Data_Raw("function(id){ 
			  if (id && id !== lastSelection) {
                    var grid = $(\"#jqCatalog\");
                    grid.jqGrid('restoreRow',lastSelection);
                    grid.jqGrid('editRow',id, {keys: true} );
                    lastSelection = id;
                }
            }"),
            'trash' => true,
            'trashtext' => 'Показать удаленные',
            'trashfield' => 's.deleted_at',
            'trashtype' => 'null',
        );		
		$uid_types = $this->getUidTypes();
		$status_control = $this->getStatusControl();
		$status_control_select = $this->getStatusControl(true);
		$status_pay = $this->getStatusPay();
		$status_pay_select = $this->getStatusPay(true);
		$status_admission = $this->getStatusAdmission();
		$status_admission_select = $this->getStatusAdmission(true);
		$org_types = $this->getOrgTypes();
		$close = $this->getClose();
		$close_select = $this->getClose(true);
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );

        $where = '';
        if(!check_uid_org()){
            $org = get_uid_org();
            if(count($org) > 0){
                $where = "s.org IN(".implode(", ", $org).") AND ";
            } else {
                $where = "s.org = '-1' AND ";
            }
        }

        $this->query = "SELECT {fields} 
        FROM JMY_supply_order as s 
        LEFT JOIN JMY_organizations as o on(o.id=s.oid) 
        LEFT JOIN JMY_users as u on(u.id=s.uid) 
        LEFT JOIN JMY_sales as sl on(sl.id=s.sales_id) 
        LEFT JOIN JMY_object as obj on(obj.id=s.object) 
        LEFT JOIN (
            SELECT * FROM (SELECT * FROM JMY_control WHERE status = 3 ORDER BY date DESC) grouped GROUP BY grouped.supply_o
        ) AS c on(c.supply_o = s.id)
        WHERE ".$where."{where} ";
		
        $this->cols = array(		
			
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),
            'sales_real' => array('hidden' => true,
                'db' => 'sl.id',
            ),
			'oid_real' => array('hidden' => true,
                'db' => 's.oid',
            ),
			'object_id' => array('hidden' => true,
                'db' => 's.object',
            ),		
			'code' => array(
				'db' => 's.id',
				'label' => 'Код',
				'width' => 20,
				'hidden' => false,
				'search_op' => 'like'
            ),	

            'numb' => array(
				'db' => 's.numb',
				'label' => '№',
				'width' => 30,
				'hidden' => false,
				'search_op' => 'like'
            ),	
				
				
			'oid' => array(
				'label' => 'Поставщик',
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
				'width' => 50,				
            ),	
			
		
			
			'sales' => array(
				'label' => 'Заказ',
                'hidden' => false,
				'db' => 'sl.numb',				
				'null' => 'Не указано',
				'encode' => false,
				'width' => 30,			
				'search_op' => 'like'
            ),	
			
			
			'object' => array(
				'label' => 'Объект',
                'hidden' => false,
				'db' => 'obj.name',
				'null' => 'Не указано',
				'encode' => false,
				'width' => 30,				
            ),	

			'total' => array(
				'label' => 'Сумма',
                'hidden' => false,
				'db' => 's.total',
				'encode' => false,
                'width' => 30,	
                'search_op' => 'like'			
            ),	

			'bill_number' => array(
				'label' => 'Номер счета',
                'hidden' => false,
				'db' => 's.bill_code',				
				'null' => 'Не указано',
				'encode' => false,
				'width' => 30,		
				'search_op' => 'book'
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
                'width' => 40,
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
                'width' => 40,
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
                'width' => 40,
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
                'width' => 40,
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
                'width' => 40,              
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
			'control' => array(			
				'label' => 'Контроль',
                'db' => 's.control',
				'align' => 'center',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $status_control,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_control_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ), 	
			
			'control_excel' => array(
				'label' => 'Оплата',
				'width' => 15,
				'align' => 'center', 
				'hidden' => true,
				'db' => 's.control',
				'encode' => false,
				'sortable' => false,
				'replace' => $status_control_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_control_select, $lang['all']),
                ),
                'search_op' => 'equal',
				'excel_only' => true,
				
            ),
			
			'pay' => array(			
				'label' => 'Оплата',
                'db' => 'c.pay',
				'align' => 'center',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $status_pay,
                'stype' => 'select',
                'null' => '0',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_pay_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ), 	
			
			'pay_excel' => array(
				'label' => 'Оплата',
				'width' => 15,
				'align' => 'center', 
				'hidden' => true,
				'db' => 'c.pay',
				'encode' => false,
				'sortable' => false,
				'replace' => $status_pay_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_pay_select, $lang['all']),
                ),
                'search_op' => 'equal',
				'excel_only' => true,
				
            ),

			'admission' => array(			
				'label' => 'Поступление',
                'db' => 's.admission',
				'align' => 'center',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $status_admission,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_admission_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ), 		
			
			'admission_excel' => array(
				'label' => 'Поступление',
				'width' => 15,
				'align' => 'center', 
				'hidden' => true,
				'db' => 's.admission',
				'encode' => false,
				'sortable' => false,
				'replace' => $status_admission_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_admission_select, $lang['all']),
                ),
                'search_op' => 'equal',
				'excel_only' => true,
            ),

			'close' => array(
				'label' => 'Закрытие',
				'width' => 30,
				'align' => 'center', 
				'db' => 's.close',
				'encode' => false,
				'sortable' => false,
				'replace' => $close,
                'stype' => 'select',
                'searchoptions' => array(
                    'defaultValue' => '0',
                    'value' => new jqGrid_Data_Value($close_select, $lang['all']),
                ),
                'search_op' => 'equal',
            ),

            'close_' => array('hidden' => true,
                'db' => 's.close',
            ),
		
			'ab' => array(
				'label' => $lang['action'],
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
	
	 #Custom search operation
    protected function searchOpBook($c, $val)
    {
		
         return $c['db']." LIKE '%".$val."%'";
    }

    protected function getClose($select = false){
        $rows_select = [];
		$rows_select[0] = 'Открыто';
		$rows_select[1] = 'Закрыто';

        $rows = [];
		$rows[0] = "<span style='margin-left: -12px;' class='fas fa-times-circle text-primary fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";
		$rows[1] = "<span style='margin-left: -12px;' class='fas fa-plus-circle text-alert fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";

        return $select ? $rows_select : $rows;
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
	
	protected function getUidTypes()
    {
        $where = "";
        if(!check_uid_org()){
            $org = get_uid_org();
            if(count($org) > 0){
                $where = " AND s.org IN(".implode(", ",$org).")";
            } else {
                $where = " AND s.org = '-1'";
            }
        } 
        $result = $this->DB->query("SELECT u.surname, u.name, u.ochestvo, u.id FROM JMY_supply_order as s LEFT JOIN JMY_users as u on (s.uid=u.id) WHERE s.uid <> '0'".$where." ORDER BY u.surname");
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
	
	protected function getStatusPay($select = false)
    {  
		global $lang;
        $rows_select = array();	
		$rows_select[0] = 'Не оплачен';
		$rows_select[1] = 'Оплачен';
		$rows_select[2] = 'Отмена оплаты';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='fa fa-rub text-danger fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";
        $rows[1] = "<span style='margin-left: -12px;' class='fa fa-rub text-success fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";    
        $rows[2] = "<span style='margin-left: -12px;' class='fa fa-rub text-primary fa-md' data-toggle='tooltip' title='".$rows_select[2]."'></span>";    
		
        return ($select ?  $rows_select :  $rows);
    }

	protected function getStatusAdmission($select = false)
    {  
		global $lang;
        $rows_select = array();	
		$rows_select[1] = 'Не поступило';
		$rows_select[2] = 'Частичное поступление';
		$rows_select[3] = 'Поступило';
		$rows_select['1,2'] = 'В работе';
		
		$rows = array();	
		$rows[1] = "<span style='margin-left: -12px;' class='fas fa-truck text-danger fa-md' data-toggle='tooltip' title='Не поступило'></span>";
        $rows[2] = "<span style='margin-left: -12px;' class='fas fa-truck text-warning fa-md' data-toggle='tooltip' title='Частичное поступление'></span>";    
        $rows[3] = "<span style='margin-left: -12px;' class='fas fa-truck text-success fa-md' data-toggle='tooltip' title='Поступило'></span>";    
        $rows['1,2'] = "<span style='margin-left: -12px;' class='fas fa-truck text-success fa-md' data-toggle='tooltip' title='В работе'></span>";    
		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getStatusControl($select = false)
    {  
		global $lang;
        $rows_select = array();	
		$rows_select[0] = 'Не отправлен';
		$rows_select[1] = 'На согласовании';
		$rows_select[2] = 'Отправлен на доработку';
		$rows_select[3] = 'Согласован';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='far fa-circle text-info fa-md' data-toggle='tooltip' title='Не отправлен'></span>";
        $rows[1] = "<span style='margin-left: -12px;' class='far fa-clock text-warning fa-md' data-toggle='tooltip' title='На согласовании'></span>";    
		$rows[2] = "<span style='margin-left: -12px;' class='far fa-times-circle text-danger fa-md' data-toggle='tooltip' title='Отправлен на доработку'></span>";    
		$rows[3] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='Согласован'></span>";    
		
        return ($select ?  $rows_select :  $rows);
    }
	


    protected function parseRow($r)
    {
		global $lang;    
        $close_text = $r["close_"] == 0 ? "Закрыть" : "Открыть";
        $r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \'administration/module/supply/order_detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">'.$lang['view'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">'.$lang['action'].'</span></button>
						<ul role="menu" class="dropdown-menu pull-right">
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Отправить на согласование</a></li> 
							<li><a href="javascript:void(0)" onclick="close_supply_order(`'.$r["id"].'`, `'.(int)!$r["close_"].'`);">'.$close_text.'</a></li> 
							<li><a href="javascript:void(0)" onclick="del_modal('.$r['id'].')">Удалить</a></li> 
						</ul>
					</div>
					<div id="modal-form-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
							<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							<div class="panel-body">
								
							</div>
							<div class="panel-footer text-right">
								<button type="button" onclick="location.href = \''.ADMIN.'/module/fcontrol/approve/'. $r['id'].'\'" class="btn btn-success">'.$lang['delete'].'</button>
							</div>
						</div>
					</div>
					<div id="modal-delete-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
							<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							<div class="panel-body">
								
							</div>
							<div class="panel-footer text-right">
								<button type="button" data-id="'. $r['id'].'" class="btn btn-danger deleteBtn">'.$lang['delete'].'</button>
							</div>
						</div>
					</div>';
		
		$r['oid'] = empty($r['oid_real']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="/administration/module/organizations/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';		
		$r['sales'] = empty($r['sales']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/module/sales/order/'.$r['sales_real'].'" target="_blank">'.$r['sales'].'</a>';	
		$r['object'] = empty($r['object_id']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/object/view/'.$r['object_id'].'" target="_blank">'.\Boot\Support\Str::limit($r['object'], 70).'</a>';
		if(is_date($r["date_bill"])) $r["date_bill"] = date("d.m.Y", strtotime($r["date_bill"]));   
		if(is_date($r["date_work"])) $r["date_work"] = date("d.m.Y", strtotime($r["date_work"]));   
		if(is_date($r["date_ship"])) $r["date_ship"] = date("d.m.Y", strtotime($r["date_ship"]));   
		if(is_date($r["date_end"])) $r["date_end"] = date("d.m.Y", strtotime($r["date_end"]));   
        
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}