<?php

class jqCols extends jqGrid
{
    protected function init()
    {
		//$this->nav = array('excel' => true);
		$delivery_types = $this->getDeliveryTypes();
		$org_types = $this->getOrgTypes();
		$auto_types = $this->getAutoTypes();
		$uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_reestr as r LEFT JOIN JMY_reestr_type as t on(t.id=r.type) LEFT JOIN JMY_org as o on(o.id=r.org) LEFT JOIN JMY_organizations as a on(a.id=r.oid) LEFT JOIN JMY_users as u on(u.id=r.uid) WHERE r.status = 1 AND {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'r.id',
            ),
			'numb_real' => array('hidden' => true,
				 'db' => 'r.numb',
            ),
			'type_real' => array('hidden' => true,
				 'db' => 't.short',
            ),
			'oid_real' => array('hidden' => true,
				 'db' => 'r.oid',
            ),
			'org_real' => array('hidden' => true,
				 'db' => 'r.org',
            ),
			'date_real' => array('hidden' => true,
				 'db' => 'r.date',
            ),
			
            'id_view' => array(
				'label' => 'id',
                'hidden' => false,
				'db' => 'r.id',
				'width' => 80,				
            ),
            'numb' => array(
				'label' => '№',
				'width' => 30,
				'hidden' => false,
				'manual' => true,
				'editable' => true,
            ),
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
			
			'other_numb' => array(			
				'label' => 'Особый номер',
                'db' => 'r.other_numb',
                'width' => 60,
                'hidden' => false,
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
                'width' => 30,
                'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),  
			
			'comment' => array(			
				'label' => 'Комментарий',
                'db' => 'r.comment',
                'width' => 60,
                'replace' => $delivery_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($delivery_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'auto' => array(
				'label' => 'Способ добавления',
				'width' => 30,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			'ab' => array(
				'label' => 'Выбирете действие',
				'width' => 50,
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
	
	protected function getAutoTypes()
    {       
        $rows = array();
		$rows[1] = 'Автоматический';
		$rows[0] = 'Ручной';
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
        $r['numb'] = $r['type_real'].'-'.$r['numb_real'];	
		$r['auto'] = '<span class="fa fa-certificate fa-md"></span>';	
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/verification/order/" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">123</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a target="_blank" href="">Добавить документ</a></li>  							
						</ul>
                    </div>';	
        if(is_date($r["date_view"], "Y-m-d")) $r["date_view"] = date("d.m.Y", strtotime($r["date_view"]));
		//$r['date_view'] = formatdate($r['date_view'], 'd.m.Y');	
       // $r['_class'] = array('c_name' => ($r['discount'] > 0.1) ? 'bold font-green' : null);
		
		$r['oid'] = htmlspecialchars_decode($r['oid']);
		
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}