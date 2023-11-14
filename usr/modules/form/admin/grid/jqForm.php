<?php

class jqForm extends jqGrid
{
    protected function init()
    {
		$this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
        );		
        $uid_dir_types = $this->getUidDirTypes();
        $uid_exec_types = $this->getUidExecTypes();
        $auto_types = $this->getAutoTypes();
        $auto_types_select = $this->getAutoTypes(true);
        $types = $this->getTypes();
		$types_select = $this->getTypes(true);		
		$org_types = $this->getOrgTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
		$this->query = "SELECT {fields} FROM JMY_request as t LEFT JOIN JMY_users as u on(u.id=t.uid_dir) LEFT JOIN JMY_object as obj on(obj.id=t.object) WHERE {where} ";
        $this->cols = array(		
			
            'id' => array(
				'label' => 'Код',
				'width' => 5,
                'db' => 't.id',
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
           
			
			'title' => array(
				'label' => 'Наименование',
				'width' => 30,
				'db' => 't.title',
                'hidden' => false,
                'null' => 'Не указано',
				'search_op' => 'like'
            ),		
			
			'uid_dir' => array(			
				'label' => 'Постановщик',
                'db' => 't.uid_dir',
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
			
			'uid_exec' => array(			
				'label' => 'Исполнитель',
                'db' => 't.uid_dir',
                'width' => 20,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_exec_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_exec_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),	
			
			'org' => array(			
				'label' => 'Организация',
                'db' => 't.org',
               'width' => 25,              
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
            
           'date' => array(
				'label' => 'Дата заявки',
                'width' => 30,
				'db' => 't.date',
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
				'width' => 9,
				'align' => 'center',
				'db' => 't.status',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ),
			
			'ab' => array(
				'label' => 'Действия',
				'width' => 40,
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

    protected function getAutoTypes($select = false)
    {
		global $lang;
        $rows_select = array();	
		$rows_select[0] = 'Неизвестно';
		$rows_select[1] = 'Заявка создана';
		$rows_select[2] = 'Заявка выполняется';
		$rows_select[3] = 'Заявка завершена';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='fal fa-question-circle text-danger fa-md' data-toggle='tooltip' title='Неизвестно'></span>";
        $rows[1] = "<span style='margin-left: -12px;' class='fal fa-plus-circle text-info fa-md' data-toggle='tooltip' title='Заявка создана'></span>";    
        $rows[2] = "<span style='margin-left: -12px;' class='fal fa-clock text-warning fa-md' data-toggle='tooltip' title='Проведено'></span>";    
        $rows[3] = "<span style='margin-left: -12px;' class='fal fa-check-circle text-success fa-md' data-toggle='tooltip' title='Проведено'></span>";    
		
        return ($select ?  $rows_select :  $rows);
    }

    protected function getTypes($select = false)
    {       	
		global $db, $lang, $core;		
        $rows_select = array();	
		$rows_select[0] = 'Неизвестно';
		$rows_select[1] = 'Атри-блок';				
		$rows_select[3] = 'Теплообменное оборудование';		
		$rows_select[4] = 'Насосы';		
		$rows_select[5] = 'Насосные станции';		
		
        $rows = [];
		$rows[0] = "<span style='margin-left: -12px;' class='fal fa-question-circle text-danger fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";
        $rows[1] = '<span style="margin-left: -12px;"  data-toggle="tooltip" title="'.$rows_select[1].'"><img width="32" src="/usr/modules/form/admin/asset/images/atri.png" ></span>'; 		
        $rows[3] = '<span style="margin-left: -12px;" data-toggle="tooltip" title="'.$rows_select[3].'"><img width="32" src="/usr/modules/form/admin/asset/images/heat.png" ></span>'; 		
        $rows[4] = '<span style="margin-left: -12px;" data-toggle="tooltip" title="'.$rows_select[4].'"><img width="32" src="/usr/modules/form/admin/asset/images/pump.png" ></span>'; 		
        $rows[5] = '<span style="margin-left: -12px;" data-toggle="tooltip" title="'.$rows_select[5].'"><img width="32" src="/usr/modules/form/admin/asset/images/pump_station.png" ></span>'; 				
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getUidDirTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_request as t LEFT JOIN JMY_users as u on (t.uid_dir=u.id) WHERE t.uid_dir <> '0' ORDER BY u.surname");
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

    protected function parseRow($r)
    {
		global $lang;   
       // $r['numb'] = module_perfix('storage_transfer', $r['id']); 
		//$r['sales'] = empty($r['sales']) ? 'Не указано' : '<a href="'.ADMIN.'/module/sales/order/'.$r['sales_real'].'" target="_blank">'.$r['sales'].'</a>';
       /* $r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/storage/transfer/detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">'.$lang['view'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">'.$lang['action'].'</span></button>
						<ul role="menu" class="dropdown-menu">	 
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-print-'. $r['id'].'\')">Распечатать поступление</a></li>
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-doc-'. $r['id'].'\')">'.$lang['storage_transfer_document'].'</a></li>  
							<li><a href="javascript:void(0)" onclick="$(`#syncModal-'. $r['id'].'`).modal(`show`);">Отправить на синхронизацию</a></li> 
							<li class="divider"></li>
							<li><a href="javascript:void(0)" onclick="removeTransfer(this)" data-status="'.$r["status"].'" data-id="'.$r['id'].'" data-view-id="'.module_perfix('storage_transfer', $r['id']).'">'.$lang['delete'].'</a></li> 
						</ul>
						<div id="modal-active-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">'.str_replace('[transfer]', module_perfix('storage_transfer', $r['id']), ($r['active_real'] == 0 ? $lang['storage_transfer_active_on_title'] : $lang['storage_transfer_active_off_title'])).'</h3>
								<hr class="short alt">
								<p>'.str_replace('[transfer]', module_perfix('storage_transfer', $r['id']), ($r['active_real'] == 0 ? $lang['storage_transfer_active_on_text'] : $lang['storage_transfer_active_off_text'])).'</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" data-id="'.$r['id'].'" class="btn btn-'.($r['active_real'] == 0 ? 'success' : 'warning').' transferBtn">'.($r['active_real'] == 0 ? $lang['active_is_on'] : $lang['active_is_off']).'</button>
							  </div>
							</div>
						</div>
						<div class="modal fade" id="syncModal-'. $r['id'].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						  <div class="modal-dialog" role="document">
							<div class="modal-content">
							  <div class="modal-header">
								<h5 class="modal-title" style="color: #000;" id="exampleModalLabel">Отправить на синхронизацию</h5>       
							  </div>
							  <div class="modal-body" style="color: #000;">
								Вы действительно хотите отправить данное перемещение на синхронизацию с 1С Бухгалтерией? 
							  </div>
							  <div class="modal-footer">
								<button type="button" style="color: #fff;" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
								<button type="button" style="color: #fff;" class="btn btn-primary syncBtn" data-id="'.$r['id'].'">Отправить</button>
							  </div>
							</div>
						  </div>
						</div>       
						<div id="modal-print-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">'.$lang['storage_transfer_document'].'</h3>
								<hr class="short alt">
								<p></p>
							  </div>							  
							</div>
						</div>
					</div>
					<div class="btn-group">
						<button type="button" onclick="'.($r['active_real'] == 0 ? 'endTransfer('.$r['id'].');' : 'modal_o(\'#modal-active-'.$r["id"].'\');').'" class="btn btn-xs btn-'.($r['active_real'] == 0 ? 'success' : 'warning').'"><span style="    font-size: 14px;" class="far fa-check-square" data-toggle="tooltip" title="'.($r['active_real'] == 0 ? $lang['active_is_on'] : $lang['active_is_off']).'"></span></button>						
					</div>
					<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/order/' . $r['id'] . '\'" class="btn btn-xs btn-system"><span class="glyphicon glyphicon-print " data-toggle="tooltip" title="Распечатать"></span></button>						
					</div>
					<div class="btn-group">
						<button type="button" onclick="modal_o(\'#modal-doc-'. $r['id'].'\')" class="btn btn-xs btn-alert"><span class="glyphicon glyphicon-floppy-disk" data-toggle="tooltip" title="'.$lang['storage_transfer_document'].'"></span></button>						
                    </div>'; 
                    
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));

        $result = $this->DB->query("SELECT SUM(s_price * count) as total FROM JMY_stock_transfer_detail WHERE transfer_id = '".$r["id"]."' GROUP BY transfer_id");
        $total = $this->DB->fetch($result);
        $r["total"] = number_format($total["total"],2,'.',' '). ' руб.';
		$r["total_excel"] = number_format($total["total"],2,',','');*/
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}