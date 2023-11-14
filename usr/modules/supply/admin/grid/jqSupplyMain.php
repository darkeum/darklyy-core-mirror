<?php

class jqSupplyMain extends jqGrid
{
    protected function init()
    {
		global $lang;
		$uid_types = $this->getUidTypes();
		$status_types = $this->getStatusTypes();
		$status_types_select = $this->getStatusTypes(true);
        $this->options = array(
            'sortname' => 'date_edit',
            'sortorder' => 'desc',
        );
        $this->nav = array(
			#Set common excel export
			'excel' => true,
			'exceltext' => 'Excel',
		);
		$this->query = "SELECT {fields} FROM JMY_supply as s LEFT JOIN JMY_sales as sl on(sl.id=s.sales_id) LEFT JOIN JMY_organizations as o on(o.id=sl.oid) LEFT JOIN JMY_users as u on(u.id=s.uid)  LEFT JOIN JMY_object as obj on(obj.id=sl.object) LEFT JOIN JMY_sales as sal ON(sal.id = s.sales_id) WHERE s.type = 1 AND {where} ";
        $this->cols = array(		
			
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),			
            'sales_real' => array('hidden' => true,
                'db' => 'sal.id',
            ),			
				
			'oid_real' => array('hidden' => true,
                'db' => 'sl.oid',
            ),
			
			'object_id' => array('hidden' => true,
                'db' => 'sl.object',
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 10,
				'db' => 's.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
								
			'oid' => array(
				'label' => $lang['oid'],
                'hidden' => false,
				'db' => 'o.name',
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 35,				
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
			
            'object' => array(
				'label' => $lang['object'],
                'hidden' => false,
				'db' => 'obj.name',
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 40,				
            ),				
		
			'uid' => array(			
				'label' => 'Постановщик',
                'db' => 's.uid',
                'width' => 25,              
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
			
			'date' => array(
				'label' => $lang['date'],
				'db' => 's.date',
                'width' => 15,
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

			'date_edit' => array(
				'label' => 'Дата изменения',
				'db' => 's.date_edit',
                'width' => 15,
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
                'null' => $lang['not_specified'],
            ),
			
			'status' => array(			
				'label' => $lang['status'],
                'db' => 's.status',
				'width' => 15,
				'align' => 'center',             
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $status_types,
                'stype' => 'select',				
                'searchoptions' => array(
					'defaultValue' => '1',
                    'value' => new jqGrid_Data_Value($status_types_select, $lang['all']),
                ),
                'search_op' => 'equal',
                'excel_hidden' => true,
            ), 		
            
            'status_excel' => array(
				'label' => $lang['status'],
				'width' => 15,
				'align' => 'center',
				'hidden' => true,
				'db' => 's.status',
				'encode' => false,
				'sortable' => false,
				'replace' => $status_types_select,
				'stype' => 'select',
				'searchoptions' => array(
					'value' => new jqGrid_Data_Value($status_types_select, $lang['all']),
				),
				'search_op' => 'equal',
				'excel_only' => true,

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
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_supply as s LEFT JOIN JMY_users as u on (s.uid=u.id) WHERE s.uid <> '0' ORDER BY u.surname");
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
		
	protected function getStatusTypes($select = false)
    {  
		global $lang;   
        $rows_select = array();	
		$rows_select[0] = 'Менеджеры';
		$rows_select[1] = 'Ожидает закупки';
		$rows_select[2] = 'Закуплено';
		
		$rows = array();	
        $rows[0] = "<span style='margin-left: -12px;' class='fa fa-clock-o text-primary fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";     
        $rows[1] = "<span style='margin-left: -12px;' class='fa fa-clock-o text-warning fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";     
		$rows[2] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$rows_select[2]."'></span>";    		
        return ($select ?  $rows_select :  $rows);
    }
	
    protected function parseRow($r)
    {
		global $lang;   
        $r['numb'] = module_perfix('supply', $r['id']);		
		$r['object'] = empty($r['object_id']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/object/view/'.$r['object_id'].'" target="_blank">'.$r['object'].'</a>';
		$r['sales'] = empty($r['sales']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/module/sales/order/'.$r['sales_real'].'" target="_blank">'.$r['sales'].'</a>';
		$r['oid'] = empty($r['oid_real']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/organizations/edit/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/supply/detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр заявки</button>						
					</div>
					<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/supply/create_excel_detail/' . $r['id'] . '\'" class="btn btn-xs btn-system"><span class="glyphicon glyphicon-print " data-toggle="tooltip" title="Распечатать"></span></button>						
					</div>';
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        if(is_date($r["date_edit"], "Y-m-d H:i:s")) $r["date_edit"] = date("d.m.Y H:i:s", strtotime($r["date_edit"]));
        return $r;
    }
	
	protected function opDialog()
    {
      echo 'r';
    }
}