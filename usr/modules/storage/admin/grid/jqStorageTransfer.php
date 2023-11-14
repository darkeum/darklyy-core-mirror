<?php

class jqStorageTransfer extends jqGrid
{
    protected function init()
    {
		$this->nav = array(
            #Set common excel export
            'excel' => true,
			'exceltext' => 'Excel',
			
			'action' => true,
			'actiontext' => 'Действие',
			'actions' => array(
				'sendmrg' =>  array(
					'title' => 'Отправить на комплектацию',
					'icon' => 'fa fa-star',
					'id' => 'sendmrg',
					'onclick' => 'mergingModal()',					
				),			
			),		
			'trash' => true,			
            'trashtext' => 'Показать удаленные',				
            'trashfield' => 't.del',	
        );		
        $uid_types = $this->getUidTypes();
        $auto_types = $this->getAutoTypes();
        $auto_types_select = $this->getAutoTypes(true);
        $deleted_types = $this->getDeletedTypes();
        $deleted_types_select = $this->getDeletedTypes(true);
        $scan_types = $this->getScanTypes();
		$scan_types_select = $this->getScanTypes(true);
		
		$org_types = $this->getOrgTypes();
        $this->options = array(
            'sortname' => 'id',
			'sortorder' => 'desc',
			'multiselect' => true,
        );
		$this->query = "SELECT {fields} FROM JMY_stock_transfer as t LEFT JOIN JMY_users as u on(u.id=t.uid) LEFT JOIN JMY_object as obj on(obj.id=t.object) LEFT JOIN JMY_sales as s ON(s.id = t.sales_id) WHERE {where} ";
        $this->cols = array(		
			
            'id' => array(
				'label' => 'Код',
				'width' => 10,
                'db' => 't.id',
            ),
            'sales_real' => array(
				'hidden' => true,
                'db' => 's.id',
            ),
            's_title' => array(
				'hidden' => true,
                'db' => 's.title',
            ),
            'has_null' => array(
				'hidden' => true,
                'db' => 't.has_null',
            ),
            'has_null_excel' => array(
				'label' => 'Нулевая цена',
				'hidden' => true,
				'db' => 't.has_null',
				'excel_only' => true,
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 15,
				'db' => 't.numb',
				'hidden' => false,
				'search_op' => 'like'
            ),	

			'title' => array(
				'label' => 'Наименование',
				'width' => 30,
				'db' => 't.title',
                'hidden' => false,
                'null' => 'Не указано',
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
			
			'sales' => array(
				'label' => 'Заказ',
                'hidden' => false,
				'db' => 's.numb',				
				'null' => 'Не указано',
				'encode' => false,
				'width' => 20,				
            ),		
			
			'date' => array(
				'label' => 'Дата счета',
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
			
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 't.uid',
                'width' => 20,              
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
            
            'total' => array(
				'label' => 'Итого',
				'width' => 20,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
				'excel_hidden' => true,
				
            ),
			
			'total_excel' => array(
				'label' => 'Итого',
				'width' => 20,
				'hidden' => true,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
				'excel_only' => true,
            ),

            'active_real' => array('hidden' => true,
                'db' => 't.active',
            ),

			'stock_out' => array(			
				'label' => "Склад отправления",
                'db' => 't.stock_out',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getStock(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($this->getStock(), "Все"),
                ),
                'search_op' => 'equal',
            ),  

			'stock_in' => array(			
				'label' => "Склад назначения",
                'db' => 't.stock_in',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getStock(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($this->getStock(), "Все"),
                ),
                'search_op' => 'equal',
            ),  
			
            'status' => array(
				'label' => 'Проведение',
				'width' => 20,
				'align' => 'center',
				'db' => 't.active',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
                'stype' => 'select',
                'searchoptions' => array(
					'defaultValue' => '0',
                    'value' => new jqGrid_Data_Value($auto_types_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
			),
			// 'del' => array(			
			// 	'label' => "Архив",
			// 	'db' => 't.del',
			// 	'align' => 'center',
            //     'width' => 9,              
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'replace' => $deleted_types,
            //     'stype' => 'select',
            //     'searchoptions' => array(
			// 		'defaultValue' => 0,
			// 		'value' => new jqGrid_Data_Value($deleted_types_select, 'Все'),	
            //     ),
            //     'search_op' => 'equal',
            // ), 
			 'status_excel' => array(
				'label' => 'Проведение',
				'width' => 9,
				'align' => 'center',
				'db' => 't.active',
				'hidden' => true,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_only' => true,
            ),
            'scan' => array(
				'label' => 'Скан',
				'width' => 15,
				'align' => 'center',    
				'db' => 't.scan',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $scan_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($scan_types_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
            ),
			
			 'scan_excel' => array(
				'label' => 'Скан',
				'width' => 15,
				'align' => 'center',    
				'db' => 't.scan',
				'hidden' => true,
				'encode' => false,
				'sortable' => false,
				'replace' => $scan_types_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($scan_types_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_only' => true,
            ),
			
			'ab' => array(
				'label' => 'Дейтсвия',
				'width' => 30,
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
		$rows_select[0] = 'Не проведено';
		$rows_select[1] = 'Проведено';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='far fa-check-square text-danger fa-md' data-toggle='tooltip' title='Не проведено'></span>";
        $rows[1] = "<span style='margin-left: -12px;' class='far fa-check-square text-success fa-md' data-toggle='tooltip' title='Проведено'></span>";    
		
        return ($select ?  $rows_select :  $rows);
	}
	
    protected function getDeletedTypes($select = false)
    {
		global $lang;
		$rows_select = array();	
		$rows_select[0] = 'В работе';
		$rows_select[1] = 'В архиве';
		
		
		$rows = array();
		$rows[0] = "<span style='margin-left: -12px;' class='far fas fa-archive text-danger fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";   	
		$rows[1] = "<span style='margin-left: -12px;' class='far fas fa-archive text-success fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
         
		
        return ($select ?  $rows_select :  $rows);
    }

    protected function getScanTypes($select = false)
    {       	
		global $db, $lang, $core;		
        $rows_select = array();	
		$rows_select[0] = 'Скан отсутствует';
		$rows_select[1] = 'Скан приложен';		
		
        $rows = [];
		$rows[0] = "<span class='fal fa-file-check text-danger fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";
        $rows[1] = "<span class='fal fa-file-check text-success fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>"; 		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getUidTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_stock_transfer as t LEFT JOIN JMY_users as u on (t.uid=u.id) WHERE t.uid <> '0' ORDER BY u.surname");
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

	protected function getStock(){
		$items = \Stocks\Models\Stock::all();
		$rows = [];
		foreach($items as $item){
			$rows[ $item->id ] = $item->name;
		}
		return $rows;
	}

    protected function parseRow($r)
    {
		global $lang, $core;   

		$uid = $core->auth->user_info['id'];

       // $r['numb'] = module_perfix('storage_transfer', $r['id']); 
		$r['sales'] = empty($r['sales']) ? 'Не указано' : '<a href="'.ADMIN.'/module/sales/order/'.$r['sales_real'].'" target="_blank">'.$r['sales'].' ('.(empty($r["s_title"]) ? "Не указано" : $r["s_title"]).')</a>';
        $r['ab'] = '<div class="btn-group d-flex">
						<a href="'.ADMIN.'/module/storage/transfer/detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary" target="_blank">'.$lang['view'].'</a>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">'.$lang['action'].'</span></button>
						<ul role="menu" class="dropdown-menu">	 
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
					</div>
					<div class="btn-group">
										
					</div>'; 
                    
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));

        $result = $this->DB->query("SELECT SUM(s_price * count) as total FROM JMY_stock_transfer_detail WHERE transfer_id = '".$r["id"]."' GROUP BY transfer_id");
        $total = $this->DB->fetch($result);
        $r["total"] = (isset($total["total"]) ? number_format($total["total"],2,'.',' '). ' руб.' : 'не указано');
		$r["total_excel"] = (isset($total["total"]) ? number_format($total["total"],2,',','') : 'не указано');

		$cols_class = null;
		if($r['has_null'] == 1)
		{
			$cols_class = 'red';
		}
        $r['_class'] = $cols_class;

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}