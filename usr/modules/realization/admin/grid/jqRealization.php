<?php

use Boot\Support\Facades\Auth;

class jqRealization extends jqGrid
{
    protected function init()
    {
		global $core, $lang;		
		$this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
            'del' => true,
            'deltext' => 'eee',
			'search' => true,
            'searchtext' => 'Search',
            'prmSearch' => array(
                'multipleSearch' => true,
                'multipleGroup' => true,               
            ),
        );			
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$scan_types = $this->getScanTypes();
		$scan_types_select = $this->getScanTypes(true);

		$pay_types = $this->getPayTypes();
		$pay_types_select = $this->getPayTypes(true);
		
		$uid_types = $this->getUidTypes();
		$uid_types_create = $this->getUidTypesCreate();
		// $pay_types = $this->getPayTypes();
		$org_types = $this->getOrgTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_realization as s LEFT JOIN JMY_organizations as o on(o.id=s.oid) LEFT JOIN JMY_users as u on(u.id=s.uid) LEFT JOIN JMY_object as obj on(obj.id=s.object) LEFT JOIN JMY_org as org on(org.id=s.org) LEFT JOIN JMY_sales as sal ON(sal.id = s.sales_id) WHERE {where} ";
        $this->cols = array(
			
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),
            'sales_real' => array('hidden' => true,
                'db' => 'sal.id',
            ),
			'status_real' => array('hidden' => true,
                'db' => 's.status',
            ),
			'oid_real' => array('hidden' => true,
                'db' => 's.oid',
            ),
			'active_real' => array('hidden' => true,
                'db' => 's.active',
            ),			
			'code' => array(
				'db' => 's.id',
				'label' => 'Код',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
            ),			
            'numb' => array(
				'db' => 's.numb',
				'label' => '№',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
            ),	
			
			'sales' => array(
				'label' => $lang['sales'],
                'hidden' => false,
				'db' => 'sal.numb',				
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 15,		
				'search_op' => 'like'
            ),	
			
			'oid' => array(
				'label' => $lang['realization_oid'],
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
                'null' => $lang['not_specified'],
				'width' => 35,				
			),	
			
			'inn' => array(
				'label' => 'ИНН',
                'hidden' => false,
				'db' => 'o.inn',
				'encode' => false,
                'null' => $lang['not_specified'],
				'width' => 20,				
			),	
			
			'object' => array(
				'label' => $lang['object'],
                'hidden' => false,
				'db' => 'obj.name',				
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 30,				
            ),	
			
			'date' => array(
				'label' => $lang['date'],
				'db' => 's.date',
                'width' => 30,
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' =>  presetRanges(),
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
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, $lang['all']),
                ),
                'search_op' => 'equal',
			), 
			
			'uid_create' => array(			
				'label' => 'Создатель',
                'db' => 's.uid_create',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_types_create,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types_create, $lang['all']),
                ),
                'search_op' => 'equal',
            ), 
			
			'org' => array(			
				'label' => 'Организация',
                'db' => 'org.id',
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
			
			'total' => array(
				'label' => 'Сумма',
                'hidden' => false,
				'db' => 's.total',				
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 30,	
				'excel_hidden' => true,
            ),	
			
			'total_excel' => array(
				'label' => 'Сумма',
                'hidden' => true,
				'db' => 's.total',				
				'null' => '0',
				'encode' => false,
				'width' => 30,	
				'excel_only' => true,
			),	
			
			'pay' => array(
				'label' => 'Оплата',
				'width' => 15,
				'db' => 'sal.pay',
				'align' => 'center',
				'null' => 'Не указано',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $pay_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($pay_types_select, 'Все'),
                ),
				'search_op' => 'equal',						
				'excel_hidden' => true,
            ),
			
			'scan' => array(
				'label' => 'Скан',
				'width' => 15,
				'align' => 'center',    
				'db' => 's.scan',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $scan_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($scan_types_select, $lang['all']),
                ),
                'search_op' => 'equal',
				
				'excel_hidden' => true,
				
            ),
			
			
			'scan_excel' => array(
				'label' => 'Скан',
				'width' => 15,
				'hidden' => true,
				'align' => 'center',    
				'db' => 's.scan',
				'encode' => false,
				'sortable' => false,
				'replace' => $scan_types_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($scan_types_select, $lang['all']),
                ),
                'search_op' => 'equal',				
				'excel_only' => true,
            ),	
			
			'status' => array(
				'label' => $lang['status'],
				'width' => 15,
				'align' => 'center',    
				'db' => 's.active',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, $lang['all']),
                ),
                'search_op' => 'equal',				
				'excel_hidden' => true,
				
            ),
			
			'status_excel' => array(
				'label' => $lang['status'],
				'width' => 15,
				'align' => 'center', 
				'hidden' => true,
				'db' => 's.active',
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
				'width' => 60,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
				'excel_hidden' => true,
            )            	
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
			$end = strtotime("+1 day", $end);
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
		global $db, $lang, $core;		
        $rows_select = array();	
		$rows_select[0] = 'Не проведена';
		$rows_select[1] = 'Проведена';		
		
        $rows = [];
		$rows[0] = "<span class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Не проведена'></span>";
        $rows[1] = "<span class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='Проведена'></span>"; 		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getScanTypes($select = false)
    {       	
		global $db, $lang, $core;		
        $rows_select = array();	
		$rows_select[0] = 'Скан отсутствует';
		$rows_select[1] = 'Скан приложен';		
		
        $rows = [];
		$rows[0] = "<span class='fal fa-file-check text-danger fa-md' data-toggle='tooltip' title='Скан отсутствует'></span>";
        $rows[1] = "<span class='fal fa-file-check text-success fa-md' data-toggle='tooltip' title='Скан приложен'></span>"; 		
        return ($select ?  $rows_select :  $rows);
	}
	
	protected function getPayTypes($select = false)
    {       
        $rows_select = array();	
		$rows_select[0] = 'Не оплачена';
		$rows_select[1] = 'Частично оплачена';
		$rows_select[2] = 'Оплачена';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='fa fa-rub text-danger fa-md' data-toggle='tooltip' title='Не оплачена'></span>";
		$rows[1] = "<span style='margin-left: -12px;' class='fa fa-rub text-warning fa-md' data-toggle='tooltip' title='Частично оплачена'></span>";     
        $rows[2] = "<span style='margin-left: -12px;' class='fa fa-rub text-success fa-md' data-toggle='tooltip' title='Оплачена'></span>";    
		
        return ($select ?  $rows_select :  $rows);
    }

	protected function getOrgTypes()
    {
        $result = $this->DB->query("SELECT r.id, o.name FROM JMY_org as r LEFT JOIN JMY_organizations as o on(r.oid=o.id)");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {			
            $rows[$r['id']] = $r['name'];
        }
        return $rows;
    }
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_realization as r LEFT JOIN JMY_users as u on (r.uid=u.id) WHERE r.uid <> '0' ORDER BY u.surname");
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
	protected function getUidTypesCreate()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_realization as r LEFT JOIN JMY_users as u on (r.uid_create = u.id) WHERE r.uid_create <> '0' ORDER BY u.surname");
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
		global $org_array, $lang, $core; 		
	
		$uid = Auth::id();
		$user = Auth::user();
       
		$r['sales'] = empty($r['sales']) ? 'Не указано' : '<a href="'.ADMIN.'/module/sales/order/'.$r['sales_real'].'" target="_blank">'.$r['sales'].'</a>';
		$r['oid'] = empty($r['oid_real']) ? 'Не указано' : '<a href="'.ADMIN.'/organizations/edit/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/realization/order/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Распечатать УПД</a></li>  							
							<li><a href="javascript:void(0)" onclick="$(`#syncModal-'. $r['id'].'`).modal(`show`);">Отправить на синхронизацию</a></li>
							<li class="divider"></li>
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Отметить на удаление</a></li> 
						</ul>
						
					</div>

					<div class="btn-group">
						<button type="button"
						onclick=" '.disabled_all().' open_modal('.$r["id"].', '.($r['active_real'] == 0 ? "true" : "false").')" 
						class="btn btn-xs btn-'.($r['active_real'] == 0 ? 'success' : 'warning').'"><span style="    font-size: 14px;" class="far fa-check-square" data-toggle="tooltip" title="'.($r['active_real'] == 0 ? $lang['active_is_on'] : $lang['active_is_off']).'"></span></button>						
					</div>
					<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/realization/order/' . $r['id'] . '\'" class="btn btn-xs btn-system"><span class="glyphicon glyphicon-print " data-toggle="tooltip" title="Распечатать"></span></button>						
					</div>
					<div class="btn-group">
						<button type="button" onclick="modal_o(\'#modal-doc-'. $r['id'].'\')" class="btn btn-xs btn-alert"><span class="glyphicon glyphicon-floppy-disk" data-toggle="tooltip" title="'.$lang['supply_receipt_document'].'"></span></button>						
					</div>					
					<div class="modal fade" id="syncModal-'. $r['id'].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" style="color: #000;" id="exampleModalLabel">Отправить на синхронизацию</h5>       
						  </div>
						  <div class="modal-body" style="color: #000;">
							Вы действительно хотите отправить данную реализацию на синхронизацию с 1С Бухгалтерией? 
						  </div>
						  <div class="modal-footer">
							<button type="button" style="color: #fff;" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
							<button type="button" style="color: #fff;" class="btn btn-primary syncBtn" data-id="'.$r['id'].'">Отправить</button>
						  </div>
						</div>
					  </div>
					</div>     
					    
                    <div id="modal-active-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">'.($r['active_real'] == 0 ? $lang['active_is_on'] : $lang['active_is_off']).'?</h3>
								<hr class="short alt">
								<p>'.($r['active_real'] == 0 ? $lang['active_is_on'] : $lang['active_is_off']).'?</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" class="btn btn-success universalBtn" data-id="'.$r['id'].'">Продолжить</button>
							  </div>
							</div>
						  </div>
					
					';	
        if(is_date($r["date"], "Y-m-d")) $r["date"] = date("d.m.Y", strtotime($r["date"]));
		$r['total'] = empty($r['total']) ? 'Не указано' : number_format($r['total'],2,'.',' ').' руб.';
        return $r;
    }
	
	  protected function renderNav($nav)
    {
			//print_r($nav);

        return $nav;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}