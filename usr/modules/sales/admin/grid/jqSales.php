<?php

use Sales\Model\Sales;

class jqSales extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$status_client = $this->getStatusClient();
		$status_client_select = $this->getStatusClient(true);
		$auto_types_end = $this->getAutoTypesEnd();
		$auto_types_end_select = $this->getAutoTypesEnd(true);
		$uid_types = $this->getUidTypes();
		$del = $this->getDel();
		$del_select = $this->getDel(true);
		$pay_types = $this->getPayTypes();
		$pay_types_select = $this->getPayTypes(true);
		$org_types = $this->getOrgTypes();
		$org_types_opt = $this->getOrgTypes(true);
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
		$this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
        );		
		$ss_where = $this->ss_where ?? "TRUE";
        $this->query = "SELECT {fields} 
		FROM JMY_sales as s 
		LEFT JOIN JMY_organizations as o on(o.id=s.oid) 
		LEFT JOIN JMY_object as obj on(obj.id=s.object) 
		WHERE {$ss_where} AND s.del = 0 AND s.is_clone = 0 AND {where} ";
        $this->cols = array(
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),
            'hidden_site' => array('hidden' => true,
                'db' => 's.hidden_site',
            ),
			'status_real' => array('hidden' => true,
                'db' => 's.status',
            ),
			'confirm_supply_order' => array('hidden' => true,
                'db' => 's.confirm_supply_order',
            ),
			
			'oid_real' => array('hidden' => true,
                'db' => 's.oid',
            ),
			
			'object_id' => array('hidden' => true,
                'db' => 's.object',
            ),
			'end_sales' => array('hidden' => true,
                'db' => 's.end',
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
			
			'title' => array(
				'db' => 's.title',
				'label' => 'Наименование',
				'width' => 35,
				'encode' => false,
				'hidden' => false,
				'null' => 'Не указан',
				'search_op' => 'like'
			), 
			
			'oid' => array(
				'label' => 'Контрагент',
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
                'null' => 'Не указан',
				'width' => 35,				
            ),	
			
			'object' => array(
				'label' => 'Объект',
                'hidden' => false,
				'db' => 'obj.name',
				'null' => 'Не указано',
				'encode' => false,
				'width' => 40,				
            ),	
			
			'total' => array(
				'label' => 'Сумма',
                'hidden' => false,
				'db' => 'total',				
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 25,						
				'excel_hidden' => true,
            ),

			'total_re' => array(
				'label' => 'Оставшееся сумма реализаций',
				'width' => 25,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,							
				'excel_hidden' => true,
            ),

			'total_re_excel' => array(
				'label' => 'Оставшееся сумма реализаций',
                'hidden' => true,	
				'manual' => true,
				'encode' => false,
				'width' => 30,	
				'excel_only' => true,
            ),
			
			'date' => array(
				'label' => 'Дата',
				'db' => 's.date',
                'width' => 15,
				'encode' => false,
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
			
			'org' => array(			
				'label' => 'Организация',
				'null' => 'Не указано',
                'db' => 's.org',
               	'width' => 10,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'replace' => $org_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($org_types_opt, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 's.uid',
                'width' => 25,              
				'hidden' => false,
				'encode' => false,
				'null' => 'Не указан',
				'sortable' => false,
				'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),  
			
			'total_excel' => array(
				'label' => 'Сумма',
                'hidden' => true,
				'db' => 'total',				
				'null' => '0',
				'encode' => false,
				'width' => 30,	
				'excel_only' => true,
            ),
			
			
			// 'status' => array(
			// 	'label' => $lang['status'],
			// 	'width' => 15,
			// 	'align' => 'center',
			// 	'db' => 's.status',
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'sortable' => false,
			// 	'replace' => $auto_types,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => new jqGrid_Data_Value($auto_types_select, 'Все'),
            //     ),
            //     'search_op' => 'equal',							
			// 	'excel_hidden' => true,
			// ),

			'status_client' => array(
				'label' => "Статус клиента",
				'width' => 15,
				'align' => 'center',
				'db' => 's.status_client',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $status_client,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($status_client_select, 'Все'),
					'dataInit' => $this->initMultiselect(),
					
					'multiple'=>true,
                ),
                'search_op' => 'auto',							
				'excel_hidden' => true,
			),
			
			'end' => array(
				'label' => "Закрытие",
				'width' => 15,
				'align' => 'center',
				'db' => 's.end',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types_end,
                'stype' => 'select',
                'searchoptions' => array(
					// 'defaultValue' => '0',
                    'value' => new jqGrid_Data_Value($auto_types_end_select, 'Все'),
                ),
                'search_op' => 'equal',							
				'excel_hidden' => true,
            ),

			'sales_type' => array(
				'label' => "Тип",
				'width' => 15,
				'align' => 'center',
				'db' => 's.sales_type',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->getType(),
                'stype' => 'select',
                'searchoptions' => array(
					// 'defaultValue' => '0',
                    'value' => new jqGrid_Data_Value($this->getType(true), 'Все'),
                ),
                'search_op' => 'equal',							
				'excel_hidden' => true,
            ),

			'sales_type_excel' => array(
				'label' => 'Тип',
                'hidden' => true,
				'db' => 's.sales_type',
				'encode' => false,
				'width' => 30,	
				'excel_only' => true,
            ),

			'end_excel' => array(
				'label' => "Закрытие",
				'width' => 15,
				'align' => 'center', 
				'hidden' => true,
				'db' => 's.end',
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types_end_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_end_select, $lang['all']),
                ),
                'search_op' => 'equal',
				'excel_only' => true,
				
            ),


			'end_date' => array(
				'label' => 'Дата закрытия',
				'width' => 15,
				'db' => 's.end_date',
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
				'hidden' => true,
				'excel_only' => true,
            ),
			
			
			'pay' => array(
				'label' => 'Оплата',
				'width' => 15,
				'db' => 's.pay',
				'align' => 'center',
				'null' => 'Не указано',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				// 'search' => false,
				'replace' => $pay_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($pay_types_select, 'Все'),
                ),
				'search_op' => 'equal',	
				// 'search_op' => false,						
				'excel_hidden' => true,
            ),
			
			'pay_excel' => array(
				'label' => 'Оплата',
				'width' => 15,
				'db' => 's.pay',
				'align' => 'center',
				'null' => 'Не указано',
				'hidden' => true,
				'encode' => false,
				'sortable' => false,
				'replace' => $pay_types_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($pay_types_select, 'Все'),
                ),
                'search_op' => 'equal',	
				'excel_only' => true,
            ),
			
			// 'del' => array(			
			// 	'label' => "Архив",
            //     'db' => 's.del',
            //     'width' => 9,              
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'replace' => $del,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'defaultValue' => '0',
            //         'value' => new jqGrid_Data_Value($del_select, $lang['all']),				
            //     ),
            //     'search_op' => 'equal',
            // ), 	

			'task_id' => array(
				'hidden' => true,
                'db' => 's.task_id',
            ),

			'task_availability' => array(
				'hidden' => true,
                'db' => 's.task_availability',
            ),

			'task_title' => array(
				'label' => 'Текст задачи',
                'hidden' => false,
				'db' => 's.task_title',
				'encode' => false,
				'width' => 40,				
            ),

			'task_date_end' => array(
				'label' => 'Дата окончания задачи',
				'width' => 15,
				'db' => 's.task_date_end',
				'width' => 25,
				'null' => 'Не указано',
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

			'source_appeal' => array(
				'label' => 'Источник обращения',
                'hidden' => true,
				'db' => 's.source_appeal',
				'encode' => false,
				'width' => 30,	
				'excel_only' => true,
            ),
			'market_segment' => array(
				'label' => 'Сегмент рынка',
                'hidden' => true,
				'db' => 's.market_segment',
				'encode' => false,
				'width' => 30,	
				'excel_only' => true,
            ),
			'comment_end' => array(
				'label' => 'Комментарий завершения заказа',
                'hidden' => true,
				'db' => 's.comment_end',
				'encode' => false,
				'width' => 30,	
				'excel_only' => true,
            ),
			
			'ab' => array(
				'label' => 'Выберите действие',
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
	
	protected function getAutoTypesEnd($select = false)
    {       
		$rows_select = array();	
		$rows = array();	
		
		
		$rows[0] = 'К исполнению';
		// $rows[3] = 'В работе';
		$rows[3] = 'К отгрузке';
		$rows[1] = 'Завершена успешно';
		$rows[2] = 'Завершена не успешно';
		

		$rows_select[0] = "<span class='far fa-circle text-warning' title='".$rows[0]."'></span>";
		$rows_select[3] = "<span class='far fa-circle text-primary' title='".$rows[3]."'></span>";
		$rows_select[1] = "<span class='far fa-circle text-success' title='".$rows[1]."'></span>";
		$rows_select[2] = "<span class='far fa-circle text-danger' title='".$rows[2]."'></span>";
		
        return ($select ? $rows : $rows_select);
	}
	
	protected function getDel($select = false)
    {  
		global $lang;
        $rows_select = array();	
        $rows = array();
        $rows_select[0] = 'В работе';
        $rows_select[1] = 'В архиве';

        $rows[0] = "<span class='fas fas fa-archive text-danger fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";
        $rows[1] = "<span class='fas fas fa-archive text-success fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
        
        return ($select ?  $rows_select :  $rows);
    }

	protected function getType($select = false)
    {  
        $rows_select = \Sales\Model\Sales::getSalesType();
        $rows = array();

        $rows[0] = "<span class='fas fa-text text-secondary fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";
        $rows[1] = "<span class='fas fa-shopping-basket text-primary fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
        $rows[2] = "<span class='fas fa-cogs text-alert fa-md' data-toggle='tooltip' title='".$rows_select[2]."'></span>";
        $rows[3] = "<span class='fas fa-money-check-alt text-warning fa-md' data-toggle='tooltip' title='".$rows_select[3]."'></span>";
        $rows[4] = "<span class='fas fa-cubes text-primary fa-md' data-toggle='tooltip' title='".$rows_select[4]."'></span>";
        $rows[5] = "<span class='fas fa-search-dollar text-warning fa-md' data-toggle='tooltip' title='".$rows_select[5]."'></span>";
        
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

	protected function getStatusClient($select = false)
    {       
        $rows_select = \Sales\Model\Sales::getStatusClient();
		
		$rows = array();	
		$rows[1] = "<span class='fas fa-user-plus text-primary fa-md' data-toggle='tooltip' title='{$rows_select[1]}'></span>";
		$rows[2] = "<span class='fas fa-user-tie text-warning fa-md' data-toggle='tooltip' title='{$rows_select[2]}'></span>";     
        $rows[3] = "<span class='fas fa-user-tie text-success fa-md' data-toggle='tooltip' title='{$rows_select[3]}'></span>";    
        $rows[4] = "<span class='fas fa-user-check text-success fa-md' data-toggle='tooltip' title='{$rows_select[4]}'></span>";   
        $rows[5] = "<span class='fas fa-truck text-warning text-success fa-md' data-toggle='tooltip' title='{$rows_select[5]}'></span>";   
        $rows[6] = "<span class='fas fa-truck text-success text-success fa-md' data-toggle='tooltip' title='{$rows_select[6]}'></span>";   
        $rows[7] = "<span class='fas fa-check-circle text-success fa-md' data-toggle='tooltip' title='{$rows_select[7]}'></span>";   
		
		// for($i=5;$i<8;$i++){
		// 	$rows_select[$i] = "Размещён ({$i})";
		// 	$rows[$i] = "<span style='margin-left: -12px;' class='fas fa-user-check text-success fa-md' data-toggle='tooltip' title='{$rows_select[$i]}'></span>";   
		// }
		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_sales as r LEFT JOIN JMY_users as u on (r.uid=u.id) WHERE r.uid <> '0' ORDER BY u.surname");
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
	
	protected function getOrgTypes($select = false)
    {
        $result = $this->DB->query("SELECT r.id, o.name, r.prefix FROM JMY_org as r LEFT JOIN JMY_organizations as o on(r.oid=o.id)");
        // $result = $this->DB->query("SELECT r.id, r.prefix FROM JMY_org as r");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {			
            $rows[$r['id']] = $select ? $r["name"] : $r['prefix'];
        }
        return $rows;
    }

    protected function parseRow($r)
    {
		global $org_array, $lang; 		
		$r['yt'] = empty($r['yt']) ? 'Не указано' : $r['yt'];
		$r['oid'] = empty($r['oid_real']) ? 'Не указано' : '<a href="/administration/module/organizations/edit/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';
		//$r['org'] = empty($r['org_real']) ? 'Не указано' : $r['org'];
		$r['object'] = empty($r['object_id']) ? '<font color="red">Не указано</font>' : '<a href="'.ADMIN.'/object/view/'.$r['object_id'].'" target="_blank">'.$r['object'].'</a>';
		$r['title'] = empty($r['title']) ? '<font color="red">Не указано</font>' : $r['title'];
		$hidden_site_text = $r["hidden_site"] == 0 ? 'Скрыть для покупателя' : 'Открыть для покупателя';
		// <li><a href="javascript:void(0)" onclick="copySalesModal(this);" data-id="'.$r['id'].'">Скопировать</a></li>
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/order/' . $r['id'] . '\'" class="btn btn-xs btn-primary" title="Просмотр"><i class="fas fa-eye"></i></button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu pull-right">		
							<li><a href="javascript:void(0)" onclick="modal_print('.$r["id"].');">Распечатать продажу</a></li>  
							<li><a href="javascript:void(0)" onclick="window.open(\'/catalog_v2/specification/sales/'.$r["id"].'\',\'_blank\'); return false;">Получить спецификацию</a></li>
							<li><a href="javascript:void(0)" onclick="$(`#syncModal-'. $r['id'].'`).modal(`show`);">Отправить на синхронизацию</a></li> 
							<li style="display:none;"><a href="javascript:void(0)" onclick="hidden_site_sales({id:'. $r['id'].', status:'.($r["hidden_site"] == 0 ? 1 : 0).', status_text:\''.$hidden_site_text.'\'});">'.($hidden_site_text).'</a></li> 

							<li><a href="javascript:void(0)" onclick="showFastTaskModal('. $r['id'].');">Быстрая задача</a></li> 

							<li class="divider"></li>
							<li><a href="javascript:void(0)" onclick="del_sales('. $r['id'].');">Удалить</a></li> 
						</ul>
						  
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
						  
					</div>';
					// echo '<div class="btn-group">
					// 	'.( ($r["end_sales"] == 0 || $r["end_sales"] == 3) ? '<button type="button" onclick="end_sales_modal('.$r["id"].')" class="btn btn-xs btn-success"><span class="fas fa-check" data-toggle="tooltip" title="Завершить продажу"></span></button>' : '<button type="button" onclick="start_sales_modal({id:'.$r["id"].', mod: \'grid\'});" class="btn btn-xs btn-danger"><span class="fas fa-reply" data-toggle="tooltip" title="Возродить продажу"></span></button>').'
					// 	<button type="button" class="btn btn-xs btn-primary" onclick="showFastTaskModal('.$r["id"].');"><i class="far fa-hand-pointer"></i></button>
					// </div>';	

        if(is_date($r["date"], "Y-m-d")) $r["date"] = "<div>".date("d.m.Y", strtotime($r["date"]))."</div>";
        if(is_date($r["task_date_end"], "Y-m-d H:i:s")) $r["task_date_end"] = date("d.m.Y H:i:s", strtotime($r["task_date_end"]));
		$total = $r['total'];
		$r['total'] = empty($total) ? 'Не указано' : number_format($total,2,'.',' ') .' руб.';

		$cols_class = "";
		if($r["confirm_supply_order"] !== '[]'){
			$cols_class = "red";
		}
		$r['_class'] = $cols_class;

		$total_re_name = "sales_total_re_{$r['id']}";
		if(\Boot\Support\Cache::has($total_re_name)){
			$total_re = \Boot\Support\Cache::get($total_re_name);
		} else {
			$total_re = \Realization\Model\Realization::where("sales_id", $r["id"])->where("active", 1)->sum("total");
			\Boot\Support\Cache::set($total_re_name, $total_re, 10800);
		}
		$total_re_ = $total - $total_re;
		$r["total_re"] = number_format($total_re_,2,'.',' ') .' руб.';
		$r["total_re_excel"] = $total_re_;

		$task_title = empty($r["task_id"]) ? ($r["task_availability"] == 0 ? "Не указано" : "Все задачи выполнены") : $r["task_title"];
		$r["task_title"] = "<div onclick='showViewTaskModal({$r['id']})' style='cursor:pointer;'>{$task_title}</div>";

		$sales_type_list = \Sales\Model\Sales::getSalesType();
		$r["sales_type_excel"] = $sales_type_list[ $r["sales_type_excel"] ] ?? "";

		$source_appeal = Sales::getSourceAppeal();
		$r["source_appeal"] = $source_appeal[ $r["source_appeal"] ] ?? "Не указано";
		$market_segment = Sales::getMarketSegment();
		$r["market_segment"] = $market_segment[ $r["market_segment"] ] ?? "Не указано";
		
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}