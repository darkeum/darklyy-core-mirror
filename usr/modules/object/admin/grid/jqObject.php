<?php

class jqObject extends jqGrid
{
    protected function init()
    {
		//$this->nav = array('excel' => true);	
		$auto_types = $this->getAutoTypes();
		$types = $this->getTypes();
		$uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'date',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_object as s LEFT JOIN JMY_users as u on(u.id=s.uid) WHERE {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),
			'status_real' => array('hidden' => true,
                'db' => 's.status',
            ),

            'numb' => array(
				'db' => 's.id',
				'label' => '№',
				'width' => 10,
				'hidden' => false,
				'manual' => true,
            ),	
			
			'name' => array(
				'db' => 's.name',
				'label' => 'Название',
				'width' => 40,
				'hidden' => false,
				'null' => 'Не указано',
            ),	
			'address' => array(
				'db' => 's.address',
				'label' => 'Адрес',
				'width' => 60,
				'hidden' => false,
				'null' => 'Не указано',
            ),	
			
			/*
			'oid' => array(
				'label' => 'Контрагент',
                'hidden' => false,
				'db' => 'o.name',
				'width' => 80,				
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
            ),*/
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
			'types' => array(
				'label' => 'Тип объекта',
				'width' => 30,
				'db' => 's.cat',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			'status' => array(
				'label' => 'Статус объекта',
				'width' => 30,
				'db' => 's.status',
				'hidden' => false,
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
	
	protected function getAutoTypes()
    {       
        $rows = array();
		$rows[0] = 'На удалении';
		$rows[1] = 'Подтвержден';
		$rows[3] = 'Ожидает подтверждения';
        return $rows;
    }
	
	protected function getTypes()
    {       
        $rows = array();
		$rows[0] = 'Продажа';
		$rows[1] = 'Проектирование';
		$rows[2] = 'Монтаж';
        return $rows;
    }
	
	protected function getUidTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_object as r LEFT JOIN JMY_users as u on (r.uid=u.id)");
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
	
        $r['numb'] = 'OB-'.$r['id'];	
		
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/object/view/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a target="_blank" href="'.ADMIN.'/object/edit/' . $r['id'] . '">Редактировать объект</a></li>  							
						</ul>
					</div>';	
      //  $r['_class'] = array('c_name' => ($r['discount'] > 0.1) ? 'bold font-green' : null);

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}