<?php

class jqSupplyOrder extends jqGrid
{
    protected function init()
    {
		$uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
		$this->query = "SELECT {fields} FROM JMY_supply_order as s LEFT JOIN JMY_organizations as o on(o.id=s.oid) LEFT JOIN JMY_users as u on(u.id=s.uid) LEFT JOIN JMY_attach as a on (a.pid = s.id AND a.module = 'supply') LEFT JOIN JMY_sales as sl on(sl.id=s.sales_id) LEFT JOIN JMY_object as obj on(obj.id=sl.object) WHERE {where} ";
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
				'label' => 'Поставщик',
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
				'width' => 80,				
            ),	
			
			'sales' => array(
				'label' => 'Заказ',
                'hidden' => false,
				'db' => 's.sales_id',				
				'null' => 'Не указано',
				'encode' => false,
				'width' => 30,				
            ),	
			
			
			'object' => array(
				'label' => 'Объект',
                'hidden' => false,
				'db' => 'obj.name',
				
				'null' => 'Не указано',
				'encode' => false,
				'width' => 30,				
            ),	
			
			'object' => array(
				'label' => 'Номер счета',
                'hidden' => false,
				'db' => 's.bill_code',				
				'null' => 'Не указано',
				'encode' => false,
				'width' => 30,				
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
			
			'attach' => array(
				'label' => 'Документ',
				'width' => 15,
				'db' => 'a.url',
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
            ),
			
			'attach_hidden' => array(
				'hidden' => true,
                'db' => 'a.url',
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
		
        $result = $this->DB->query("SELECT * FROM JMY_supply_order as s LEFT JOIN JMY_users as u on (s.uid=u.id) WHERE s.uid <> '0'");
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
		global $org_array;   
        $r['numb'] = module_perfix('supply_order', $r['id']);
		$r['attach'] = (empty($r['attach_hidden']) ? '' : '<a href="'.$r['attach_hidden'].'" target="_blank">Скачать</a>');
        
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