<?php

class jqSupplyStock extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$uid_types = $this->getUidTypes();
		$status_types = $this->getStatusTypes();
		$status_types_select = $this->getStatusTypes(true);
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
		$this->query = "SELECT {fields} FROM JMY_supply_stock as ss WHERE {where} ";
        $this->cols = array(		
			
            'id' => array('hidden' => true,
                'db' => 'ss.id',
            ),			
					
			'numb' => array(
				'label' => '№',
				'width' => 10,
				'db' => 'ss.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
			
			'date' => array(
				'label' => 'Дата начала',
				'db' => 'ss.date',
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
			
			'status' => array(			
				'label' => $lang['status'],
                'db' => 'ss.status',
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
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_supply as s LEFT JOIN JMY_users as u on (s.uid=u.id) WHERE s.uid <> '0' ORDER BY surname");
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
		$rows_select[1] = 'Ожидает';
		$rows_select[2] = 'Завершена';
		
		$rows = array();	
        $rows[1] = "<span style='margin-left: -12px;' class='fa fa-clock-o text-warning fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";     
		$rows[2] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$rows_select[2]."'></span>";    		
        return ($select ?  $rows_select :  $rows);
    }
	
    protected function parseRow($r)
    {
		global $lang;   
        $r['numb'] = $r['id'];		
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/supply/supply_stock_detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр заявки</button>						
					</div>';
        if(is_date($r["date"], "Y-m-d")) $r["date"] = date("d.m.Y", strtotime($r["date"]));
        return $r;
    }
	
	protected function opDialog()
    {
      echo 'r';
    }
}