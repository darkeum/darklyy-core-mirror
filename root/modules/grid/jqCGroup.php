<?php

class jqCGroup extends jqGrid
{
    protected function init()
    {
		$this->nav = array('excel' => true);
		$delivery_types = $this->getDeliveryTypes();
		$org_types = $this->getOrgTypes();
		$uid_types = $this->getUidTypes();
		$pay_types = $this->getPayTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_catalog_group as o WHERE {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'o.id',
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 10,
				'db' => 'o.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
			
			/*
			'type' => array(
				'db' => 'type',
				'label' => 'Тип контрагента',
				'width' => 30,
				'hidden' => false,
				'search_op' => 'like'
            ),
			
			'inn' => array(
				'label' => 'Наименование',
				'width' => 10,
				'hidden' => false,
				'db' => 'o.inn',
				'encode' => false,
				'search_op' => 'like'
            ),
			
			'name' => array(
				'label' => 'Наименование',
				'width' => 10,
				'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
				'search_op' => 'like'
            ),
			*/
			
			
			
			
			/*
			'numb_bank' => array(
				'db' => 'numb',
				'label' => 'Банковский номер',
				'width' => 30,
				'hidden' => false,
				'search_op' => 'like'
            ),
			
			'pay' => array(
				'label' => 'Тип',
				'width' => 30,
				'db' => 'pay_in',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $pay_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($pay_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'type' => array(
				'db' => 'type',
				'label' => 'Категория',
				'width' => 30,
				'hidden' => false,
				'search_op' => 'like'
            ),
			
			'payer' => array(
				'label' => 'Плательщик',
                'hidden' => false,
				'encode' => false,
				'db' => 'p.name',
				'width' => 60,				
            ),	
			
			'recipient' => array(
				'label' => 'Получатель',
                'hidden' => false,
				'encode' => false,
				'db' => 'r.name',
				'width' => 60,				
            ),	
			
			'date' => array(
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
			
			'sum' => array(
				'label' => 'Сумма',
				'width' => 20,
				'hidden' => false,
				'search_op' => 'like'
            ),
			
			'comment' => array(
				'label' => 'Комментарий',
				'width' => 100,
				'hidden' => false,
				'search_op' => 'like'
            ),
			
			'upload_id' => array(
				'label' => '№ выгрузки',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
            ),*/
            	
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
	
	protected function getPayTypes()
    {       
        $rows = array();
		$rows[0] = 'Списание';
		$rows[1] = 'Поступление';
        return $rows;
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
        $r['numb'] = 'ON-'.$r['id'];		
		//$r['date'] = formatdate($r['date_real'], 'd.m.Y');	
        $r['_class'] = array('c_name' => ($r['discount'] > 0.1) ? 'bold font-green' : null);
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}