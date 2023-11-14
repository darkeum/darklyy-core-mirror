<?php

class jqFcontrolPay extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'plan_pay_date',
            'sortorder' => 'desc',
			
        );
		$this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
        
			'search' => true,
            'searchtext' => 'Search',
            'prmSearch' => array(
                'multipleSearch' => true,
                'multipleGroup' => true,               
            ),
        );		

        // dd($con);  
        // dd($this->test); 
        $w = isset($_COOKIE["plan_pay_date_all"]) ? '' : "AND c.plan_pay_date <= '".date("Y-m-d")."'";
        // dd($w);
        // $w = "";

        $this->query = "SELECT {fields} FROM JMY_control as c LEFT JOIN JMY_supply_order as so ON(so.id = c.supply_o) LEFT JOIN JMY_organizations as o on(o.id=so.oid) LEFT JOIN JMY_users as u on(u.id=c.uid) LEFT JOIN JMY_object as obj on(obj.id=so.object) LEFT JOIN JMY_sales as s ON(s.id = c.sales_id) WHERE c.status = 3 ".$w." AND {where} ";
        $this->cols = array(	
			
            'id' => array('hidden' => true,
                'db' => 'c.id',
            ),	
            'sales_real' => array('hidden' => true,
                'db' => 's.id',
            ),	
            'supply_real' => array('hidden' => true,
                'db' => 'so.id',
            ),	
			'oid_real' => array('hidden' => true,
                'db' => 'c.oid',
            ),
			'object_id' => array('hidden' => true,
                'db' => 'c.object',
            ),
			'real_status' => array('hidden' => true,
                'db' => 'c.status',
            ),
            'numb' => array(
				'db' => 'c.id',
				'label' => '№',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
            ),
			'oid' => array(
				'label' => $lang['oid'],
                'hidden' => false,
				'db' => 'o.name',
                'encode' => false,
                'null' => $lang['not_specified'],
				'width' => 40,	
            ),	
           
			'object' => array(
				'label' => $lang['object'],
                'hidden' => false,
				'db' => 'obj.name',
				'null' =>  $lang['not_specified'],
				'encode' => false,
				'width' => 40,				
            ),	
			'supply_o' => array(
				'db' => 'so.numb',
				'label' => $lang['fcontrol_supply_order'],
				'width' => 15,
				'hidden' => false,
                'encode' => false,
				'search_op' => 'like'
            ),		
			'sales' => array(
				'db' => 's.numb',
				'label' => $lang['sales'],
				'width' => 15,
				'hidden' => false,
                'encode' => false,
				'search_op' => 'like'
            ),
			'uid' => array(			
				'label' => $lang['uid'],
                'db' => 'c.uid',
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
            'date' => array(
				'label' => $lang['date'],
				'db' => 'c.date',
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
            'plan_pay_date' => array(
				'label' => "Дата оплаты",
				'db' => 'c.plan_pay_date',
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
            'total' => array(
				'db' => 'c.total',
				'label' => $lang['total'],
				'width' => 25,
				'hidden' => false,
				'encode' => false,
				'search_op' => 'like'
            ),		
			'pay' => array(
				'label' => "Оплата",
				'width' => 20,
				'align' => 'center',
				'db' => 'c.pay',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
                'stype' => 'select',
                'defaultValue' => '1',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, $lang['all']),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ),
			
			'pay_excel' => array(
				'label' => "Оплата",
				'width' => 15,
				'align' => 'center', 
				'hidden' => true,
				'db' => 'c.pay',
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, $lang['all']),
                ),
                'search_op' => 'equal',
				'excel_only' => true,
				
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
	
	protected function getAutoTypes($select = false)
    {  
		global $lang;
        $rows_select = array();	
		$rows_select[0] = "Ожидает оплаты";
		$rows_select[1] = "Оплачено";
		$rows_select[2] = "Отмена оплаты";
		$rows_select[3] = "Частичная оплаты";
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='fa fa-clock-o text-warning fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";  
		$rows[1] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";    
		$rows[2] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-primary fa-md' data-toggle='tooltip' title='".$rows_select[2]."'></span>";    
		$rows[3] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-warning-dark fa-md' data-toggle='tooltip' title='".$rows_select[3]."'></span>";    
		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getUidTypes()
    {
		global $lang;
        $result = $this->DB->query("SELECT * FROM JMY_control as c LEFT JOIN JMY_users as u on (c.uid=u.id) WHERE c.uid <> '0' ORDER BY u.surname");
        $rows = array();
		$rows[0] = $lang['not_specified'];
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
		global $lang;
		$r['numb'] = module_perfix('fcontrol', $r['id']);	
		$r['sales'] = empty($r['sales']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/module/sales/order/'.$r['sales_real'].'" target="_blank">'.$r['sales'].'</a>';
		$r['supply_o'] = empty($r['supply_real']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/module/supply/order_detail/'.$r['supply_real'].'" target="_blank">'.$r['supply_o'].'</a>';
		$r['oid'] = empty($r['oid_real']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/module/organizations/edit/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';
		$r['object'] = empty($r['object_id']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/object/view/'.$r['object_id'].'" target="_blank">'.$r['object'].'</a>';
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/fcontrol/pay_detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">'.$lang['view'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">'.$lang['action'].'</span></button>
						<ul role="menu" class="dropdown-menu">
							'.($r['real_status'] == 1 ? '<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">'.$lang['fcontrol_fast_agree'].'</a></li>  
							<li class="divider"></li>' : '').'							
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-delete-'. $r['id'].'\')">'.$lang['delete'].'</a></li> 
						</ul>
					</div>
					<div id="modal-form-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
							<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							<div class="panel-body">
								<h3 class="mt5">'.str_replace('[order]', $r['supply_o'], $lang['fcontrol_approve_title']).'</h3>
								<hr class="short alt">
								<p>'.str_replace('[order]', $r['supply_o'], $lang['fcontrol_approve_text']).'</p>
							</div>
							<div class="panel-footer text-right">
								<button type="button" onclick="location.href = \''.ADMIN.'/module/fcontrol/approve/'. $r['id'].'\'" class="btn btn-success">'.$lang['fcontrol_approve'].'</button>
							</div>
						</div>
					</div>
					<div id="modal-delete-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
							<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							<div class="panel-body">
								<h3 class="mt5">'.str_replace('[control]', $r['numb'], $lang['fcontrol_delete_title']).'</h3>
								<hr class="short alt">
								<p>'.str_replace('[control]', $r['numb'], $lang['fcontrol_delete_text']).'</p>
							</div>
							<div class="panel-footer text-right">
								<button type="button" onclick="location.href = \''.ADMIN.'/module/fcontrol/delete/'. $r['id'].'\'" class="btn btn-danger">'.$lang['delete'].'</button>
							</div>
						</div>
					</div>';	
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        if(is_date($r["plan_pay_date"], "Y-m-d")) $r["plan_pay_date"] = date("d.m.Y", strtotime($r["plan_pay_date"]));
		$r['total'] = empty($r['total']) ? '<font color="red">'.$lang['not_specified'].'</font>' : number_format($r['total'],2,'.',' ') .' руб.';
        return $r;
    }
	
	protected function opDialog()
    {
      echo 'r';
    }
}