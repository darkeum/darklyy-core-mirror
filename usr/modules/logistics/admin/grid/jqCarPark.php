<?php

use Sales\Model\Sales;

class jqCarPark extends jqGrid
{
    protected function init()
    {
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
		
        $this->query = "SELECT {fields} 
		FROM `".DB_PREFIX."_car_park` as cp 
		WHERE {where} ";
        $this->cols = array(
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'cp.id',
            ),

            'numb' => array(
				'db' => 'cp.id',
				'label' => '№',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
			),	
			
			'brand' => array(
				'db' => 'cp.brand',
				'label' => 'Марка машины',
				'width' => 35,
				'encode' => false,
				'hidden' => false,
				'null' => 'Не указан',
				'search_op' => 'like'
			), 

			'number' => array(
				'db' => 'cp.number',
				'label' => 'Номер машины',
				'width' => 35,
				'encode' => false,
				'hidden' => false,
				'null' => 'Не указан',
				'search_op' => 'like'
			), 
			
			'org' => array(			
				'label' => 'Организация',
				'null' => 'Не указано',
                'db' => 'cp.org',
               	'width' => 10,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'replace' => $this->getOrgTypes(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($this->getOrgTypes(), 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'ab' => array(
				'label' => 'Выберите действие',
				'width' => 20,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,							
				'excel_hidden' => true,
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
	
	protected function getOrgTypes()
    {
        $result = $this->DB->query("SELECT r.id, o.name, r.prefix FROM JMY_org as r LEFT JOIN JMY_organizations as o on(r.oid=o.id)");
        // $result = $this->DB->query("SELECT r.id, r.prefix FROM JMY_org as r");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {			
            $rows[$r['id']] = $r["name"];
        }
        return $rows;
    }

    protected function parseRow($r)
    {
		$r['ab'] = '<div class="btn-group">
						<button type="button" class="btn btn-danger btn-xs" onclick="delete_car('.$r["id"].')">Удалить</button>
					</div>';
		
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}