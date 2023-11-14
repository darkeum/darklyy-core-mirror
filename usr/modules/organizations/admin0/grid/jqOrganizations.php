<?php

class jqOrganizations extends jqGrid
{
    protected function init()
    {
		global $lang;
		$this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
        );			
		$uid_types = $this->getUidTypes();
		$types = $this->getTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_organizations as o WHERE {where} ";
        $this->cols = array(
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'o.id',
            ),	
			
			'code' => array(
				'label' => 'Код',
				'width' => 5,
				'db' => 'o.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 7,
				'db' => 'o.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
			
			'name' => array(
				'label' => 'Наименование',
				'width' => 25,
				'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
				'search_op' => 'like'
            ),
			
			'type' => array(
				'label' => 'Тип контрагента',
				'width' => 20,
				'db' => 'type',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $types,
				'null' => "Не указано",
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'inn' => array(
				'label' => 'ИНН',
				'width' => 10,
				'hidden' => false,
				'db' => 'o.inn',
				'encode' => false,
				'search_op' => 'like'
            ),
			
			'kpp' => array(
				'label' => 'КПП',
				'width' => 10,
				'hidden' => false,
				'db' => 'o.kpp',
				'encode' => false,
				'search_op' => 'like'
            ),
			
			'yaddress' => array(
				'label' => 'Юридический адрес',
				'width' => 25,
				'hidden' => false,
				'db' => 'o.yaddress',
				'encode' => false,
				'search_op' => 'like'
            ),
			
			'ab' => array(
				'label' => $lang['action'],
				'width' => 20,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,
				'excel_hidden' => true,
            ) 
            	
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
	
	protected function getTypes()
    {       
        $rows = array();
		$rows['LEGAL'] = 'Юридическое лицо';
		$rows['INDIVIDUAL'] = 'Индивидуальный предприниматель';
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
		global $lang;   
        $r['numb'] = 'ON-'.$r['id'];		
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/organizations/edit/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" onclick="location.href = \'/administration/module/organizations/print/'.$r['id'].'\'" class="btn btn-xs btn-system"><span class="glyphicon glyphicon-print " data-toggle="tooltip" title="Распечатать"></span></button>
					</div>';	
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}