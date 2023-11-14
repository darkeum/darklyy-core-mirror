<?php

class jqLetter extends jqGrid
{
    protected function init()
    {
		//$this->nav = array('excel' => true);
		$delivery_types = $this->getDeliveryTypes();
		$uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM ".DB_PREFIX."_letter as l LEFT JOIN ".DB_PREFIX."_users as u on(u.id=l.uid) LEFT JOIN JMY_organizations as o_from ON(l.oid_from = o_from.id) LEFT JOIN JMY_organizations as o_for ON(l.oid_for = o_for.id) WHERE {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'l.id',
            ),			

            'numb' => array(
				'label' => '№',
				'width' => 30,
				'hidden' => false,
				'manual' => true,
            ),
			'oid_from' => array(			
				'label' => 'От кого',
                'db' => 'o_from.name',
                'width' => 50,
                'encode' => false,
                'null'=>'Не указан',
                'search_op' => 'like',
            ),
			'oid_for' => array(			
				'label' => 'Для кого',
                'db' => 'o_for.name',
                'width' => 50,
                'encode' => false,
                'null'=>'Не указан',
                'search_op' => 'like',
            ),	
			'date' => array(
				'label' => 'Дата',
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
                        'dateFormat' => 'dd.mm.yy',                       
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
	
	protected function getUidTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_command as r LEFT JOIN JMY_users as u on (r.uid=u.id)");
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
        $r['numb'] = $r['id'];	
		//$r['auto'] = '<span class="fa fa-certificate fa-md"></span>';	
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/verification/order/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">123</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a target="_blank" href="https://atri-energo.ru/administration/command/doc_create/' . $r['id'] . '">Скачать документ</a></li>  							
						</ul>
					</div>';	
		//$r['date'] = formatdate($r['date_real'], 'd.m.Y');	
       // $r['_class'] = array('c_name' => ($r['discount'] > 0.1) ? 'bold font-green' : null);

       if(is_date($r["date"], "Y-m-d")) $r["date"] = date("d.m.Y", strtotime($r["date"]));

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}