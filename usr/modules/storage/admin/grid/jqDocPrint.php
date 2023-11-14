<?php

class jqDocPrint extends jqGrid
{
    protected function init()
    {
		global $lang;
		$uid_types = $this->getUidTypes();
		$status_types = $this->getStatusTypes();
		$types = $this->getTypes();
		$status_types_select = $this->getStatusTypes(true);
		$types_select = $this->getTypes(true);
        $this->options = array(
            'sortname' => 'date',
            'sortorder' => 'desc',
        );
		$this->do_search = true;
		$this->multipleSearch = true;
		$this->query = "SELECT {fields} FROM JMY_sales as s LEFT JOIN JMY_organizations as o ON(s.oid = o.id) LEFT JOIN JMY_object as obj ON(obj.id = s.object) WHERE s.allow_shipment = '1' AND {where} ";
        $this->cols = array(		
			
            'id' => array(
                'hidden' => true,
                'db' => 's.id',
            ),
			'oid_real' => array(
                'hidden' => true,
                'db' => 's.oid',
            ),
			'object_real' => array(
                'hidden' => true,
                'db' => 'obj.id',
            ),
					
			'sales_id' => array(
				'label' => 'ID',
				'width' => 15,
				'db' => 's.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
			'numb' => array(
				'label' => '№',
				'width' => 15,
				'db' => 's.numb',
				'hidden' => false,
				'search_op' => 'like'
            ),
				
			'oid' => array(
				'label' => $lang['oid'],
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
				'width' => 40,				
				'null' => $lang['not_specified'],				
            ),		

			'object' => array(
				'label' => 'Объект',
                'hidden' => false,
				'db' => 'obj.name',
				'encode' => false,
				'width' => 40,				
				'null' => $lang['not_specified'],				
            ),				
		
			'uid' => array(			
				'label' => $lang['uid'],
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
			
			'date' => array(
				'label' => $lang['date'],
				'db' => 's.date',
                'width' => 40,
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
			
			// 'status' => array(			
			// 	'label' => $lang['status'],
            //     'db' => 'sa.status',
            //     'width' => 30,              
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'replace' => $status_types,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'defaultValue' => '1',
            //         'value' => new jqGrid_Data_Value($status_types_select, $lang['all']),				
            //     ),
            //     'search_op' => 'equal',
            // ), 			
			
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
	
	protected function getUidTypes()
    {
		global $lang;		
        $result = $this->DB->query("SELECT u.surname, u.name, u.ochestvo, u.id FROM JMY_storage_assembly AS sa LEFT JOIN JMY_users AS u ON (sa.uid=u.id)");
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
	
	protected function getStatusTypes($select = false)
    { 
		global $lang;
        $rows_select = array();	
		$rows_select[1] = "Ожидает";
		$rows_select[2] = "Выполнено";
		
        $rows = array();
		$rows[1] = "<span class='fa fa-clock-o text-warning fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
        $rows[2] = "<span class='fas fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$rows_select[2]."'></span>";
        return ($select ?  $rows_select :  $rows);
    }
    
	protected function getTypes($select = false)
    {  
		global $lang;
        $rows_select = array();	
        $rows = array();
        $rows_select[1] = 'Сборка товаров';
        $rows_select[2] = 'Отмена сборки';

        $rows[1] = "<span class='fas fa-people-carry text-success fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
        $rows[2] = "<span class='fas fa-people-carry text-danger fa-md' data-toggle='tooltip' title='".$rows_select[2]."'></span>";
        
        return ($select ?  $rows_select :  $rows);
    }


    protected function parseRow($r)
    {
		global $lang; 
		$r['oid'] = empty($r['oid_real']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/organizations/edit/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';
		$r['object'] = empty($r['object_real']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="/administration//object/view/'.$r['object_real'].'" target="_blank">'.$r['object'].'</a>';
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="close_request('.$r["id"].');" class="btn btn-xs btn-primary">Закрыть заявку</button>						
					</div>';	
                    
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y", strtotime($r["date"]));

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}