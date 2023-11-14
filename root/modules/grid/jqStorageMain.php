<?php

class jqStorageMain extends jqGrid
{
    protected function init()
    {
		$uid_types = $this->getUidTypes();
		$status_types = $this->getStatusTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
			//'postData' => "&rows=30&page=1&sidx=id&sord=desc&status=1&status=1",
        );
		$this->do_search = true;
			$this->multipleSearch = true;
			if (isset($_GET['status'])) 
			{	
				$where_now = "where {where}";
				if ($_GET['status'] == 'all')
				{
					$where_now = "where s.status = '100' AND {where}";
				}
			              
			}
			else
			{
				$where_now = "where s.status='1' AND {where}";
			} 	
$where_now = "where {where}";			
		$this->query = "SELECT {fields} FROM JMY_storage as s LEFT JOIN JMY_sales as sl on(sl.id=s.sales_id) LEFT JOIN JMY_organizations as o on(o.id=sl.oid) LEFT JOIN JMY_users as u on(u.id=s.uid)  ".$where_now." ";
        $this->cols = array(		
			
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 15,
				'db' => 's.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
				
				
			'oid' => array(
				'label' => 'Контрагент',
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
				'width' => 60,				
				'null' => 'Не указано',				
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
                    'value' => new jqGrid_Data_Value($uid_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),  			
			
			'date' => array(
				'label' => 'Дата добавления',
				'db' => 's.date',
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
			
			'status' => array(			
				'label' => 'Статус',
                'db' => 's.status',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'replace' => $status_types,
                'stype' => 'select',
                'searchoptions' => array(
					'defaultValue' => '1',
                    'value' => new jqGrid_Data_Value($status_types, 'Все'),				
                ),
                'search_op' => 'equal',
            ), 			
			
			'ab' => array(
				'label' => 'Выберите действие',
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
		
        $result = $this->DB->query("SELECT * FROM JMY_supply as s LEFT JOIN JMY_users as u on (s.uid=u.id) WHERE s.uid <> '0'");
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
	
	protected function getStatusTypes()
    {       
        $rows = array();
		$rows[0] = 'Отменен';
		$rows[1] = 'Ожидает подтверждения';
		$rows[2] = 'Подтвержден';
        return $rows;
    }
	


    protected function parseRow($r)
    {
		global $org_array;   
        $r['numb'] = 'SN-'.$r['id'];
			$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/storage/detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр заявки</button>						
					</div>';	
                    
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}