<?php

class jqOrder extends jqGrid
{
    protected function init()
    {
		$this->nav = array('excel' => true);
		$delivery_types = $this->getDeliveryTypes();
		$org_types = $this->getOrgTypes();
		$uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'date',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_order as c WHERE {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'c.id',
            ),
		
			'numb' => array(
				'label' => '№',
				'width' => 30,
				'hidden' => false,
				'manual' => true,
				'search_op' => 'like'
            ),
			'sku' => array(
				'label' => 'Артикул',
				'db' => 'c.sku',
				'width' => 40,
				'hidden' => false,
				'search_op' => 'like'
				
            ),
			'title' => array(
				'label' => 'Наименование',
				'db' => 'c.title',
				'width' => 100,
				'hidden' => false,
				'search_op' => 'like'
				
            ),
			'barcode' => array(
				'label' => 'Штрихкод',
				'db' => 'c.barcode',
				'width' => 30,
				'hidden' => false,
				'search_op' => 'like'
				
            ),/*
			'title' => array(			
				'label' => 'Тип договора',
                'db' => 't.id',
                'width' => 60,
                'replace' => $delivery_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($delivery_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			'org' => array(			
				'label' => 'Организация',
                'db' => 'o.id',
                'width' => 50,
                'replace' => $org_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($org_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			'oid' => array(
				'label' => 'Контрагент',
                'hidden' => false,
				'db' => 'a.name',
				'width' => 80,				
            ),			
			'date_view' => array(
				'label' => 'Дата',
                'width' => 40,
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'yy-mm-dd',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => array(
                        array('text' => 'За 2016 год', 'dateStart' => '2016-01-01', 'dateEnd' => '2016-12-31'),
                        array('text' => 'За 2017 год', 'dateStart' => '2017-01-01', 'dateEnd' => '2017-12-31'),
						array('text' => 'За 2018 год', 'dateStart' => '2018-01-01', 'dateEnd' => '2018-12-31'),
                    ),
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'dateFormat' => 'yy-mm-dd',                       
                    ),
                ))),
                'search_op' => 'date_range',
            ),
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 'u.id',
                'width' => 50,
                'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),  
			*/
            	
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
	
	protected function getDeliveryTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_reestr_type");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {
            $rows[$r['id']] = $r['short'].' - '.$r['name'];
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
	
	protected function getUidTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_reestr as r LEFT JOIN JMY_users as u on (r.uid=u.id)");
        $rows = array();
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
        $r['numb'] = 'N'.$r['id'];	
		//$r['date'] = formatdate($r['date_real'], 'd.m.Y');	
        $r['_class'] = array('c_name' => ($r['discount'] > 0.1) ? 'bold font-green' : null);

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}