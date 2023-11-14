<?php

class jqPTOView extends jqGrid
{
    protected function init()
    {
        global $core, $lang, $jqPTOViewId;
        $id = $jqPTOViewId;
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$uid_types = $this->getUidTypes();
		$org_types = $this->getOrgTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
		// $this->nav = array(
        //     #Set common excel export
        //     'excel' => true,
        //     'exceltext' => 'Excel',
        // );		
        $this->query = "SELECT {fields} FROM `".DB_PREFIX."_pto_view` as pv WHERE pv.pto_id = '{$id}' AND {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'pv.id',
            ),	

            'numb' => array(
				'db' => 'pv.id',
				'label' => 'ID',
				'width' => 5,
				'hidden' => false,
				'search_op' => 'like'
			),	
			
			'pnumb' => array(
			 	'db' => 'pv.numb',
			 	'label' => '№ рамы',
			 	'width' => 5,
			 	'encode' => false,
			 	'hidden' => false,
			 	'null' => 'Не указан',
			 	'search_op' => 'like'
			 ), 	
			 
			'l' => array(
			 	'db' => 'pv.l',
			 	'label' => 'L',
			 	'width' => 5,
			 	'encode' => false,
			 	'hidden' => false,
			 	'null' => 'Не указан',
			 	'search_op' => 'like'
			 ), 	
			 
			 'l1' => array(
			 	'db' => 'pv.l1',
			 	'label' => 'L1',
			 	'width' => 5,
			 	'encode' => false,
			 	'hidden' => false,
			 	'null' => 'Не указан',
			 	'search_op' => 'like'
			 ), 	
			 
			 'l2' => array(
			 	'db' => 'pv.l2',
			 	'label' => 'L2',
			 	'width' => 5,
			 	'encode' => false,
			 	'hidden' => false,
			 	'null' => 'Не указан',
			 	'search_op' => 'like'
			 ), 	
			 
			'hairpin_type' => array(
			 	'db' => 'pv.hairpin_type',
			 	'label' => 'Размер шпилек',
			 	'width' => 10,
			 	'encode' => false,
			 	'hidden' => false,
			 	'null' => 'Не указан',
			 	'search_op' => 'like'
			 ), 
			 
			'hairpin_count' => array(
			 	'db' => 'pv.hairpin_count',
			 	'label' => 'Кол-во шпилек',
			 	'width' => 10,
			 	'encode' => false,
			 	'hidden' => false,
			 	'null' => 'Не указан',
			 	'search_op' => 'like'
			 ), 
			 
			 'plates_min' => array(
			 	'db' => 'pv.plates_min',
			 	'label' => 'Мин. кол-во пластин',
			 	'width' => 15,
			 	'encode' => false,
			 	'hidden' => false,
			 	'null' => 'Не указан',
			 	'search_op' => 'like'
			 ), 
			 
			  'plates_max' => array(
			 	'db' => 'pv.plates_max',
			 	'label' => 'Макс. кол-во пластин',
			 	'width' => 15,
			 	'encode' => false,
			 	'hidden' => false,
			 	'null' => 'Не указан',
			 	'search_op' => 'like'
			 ),  
			 
			 'weight' => array(
			 	'db' => 'pv.weight',
			 	'label' => 'Вес',
			 	'width' => 15,
			 	'encode' => false,
			 	'hidden' => false,
			 	'null' => 'Не указан',
			 	'search_op' => 'like'
			 ), 
			
			'date' => array(
				'label' => 'Дата',
				'db' => 'pv.date',
                'width' => 25,
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => presetRanges(),                  
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',                       
                    ),
                ))),
                'search_op' => 'date_range',
            ),
			
			// 'org' => array(			
			// 	'label' => 'Организация',
			// 	'null' => 'Не указано',
            //     'db' => 's.org',
            //    'width' => 30,              
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'sortable' => false,
            //     'replace' => $org_types,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => new jqGrid_Data_Value($org_types, 'Все'),
            //     ),
            //     'search_op' => 'equal',
            // ),
			
			
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 'pv.uid',
                'width' => 25,              
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
			
			'status' => array(
				'label' => $lang['status'],
				'width' => 15,
				'align' => 'center',
				'db' => 'pv.status',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, 'Все'),
                ),
                'search_op' => 'equal',							
				'excel_hidden' => true,
			),
			
			'ab' => array(
				'label' => 'Выберите действие',
				'width' => 50,
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
	
	protected function getAutoTypes($select = false)
    {  
		$rows_select = array();	
		$rows_select[1] = 'Активный';

		$rows = array();	
		$rows[1] = "<span style='margin-left: -12px;' class='far fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
		
        return ($select ?  $rows_select :  $rows);
	}
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_users as u WHERE u.active = '1' AND u.group = '1' ORDER BY u.surname");
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
	
	protected function getOrgTypes()
    {
        $result = $this->DB->query("SELECT r.id, o.name FROM JMY_org as r LEFT JOIN JMY_organizations as o on(r.oid=o.id) ORDER BY r.id");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {			
            $rows[$r['id']] = $r['name'];
        }
        return $rows;
    }

    protected function parseRow($r)
    {	
		global $jqPTOViewId;
        $id = $r["id"];
		$r['title'] = empty($r['title']) ? '<font color="red">Не указано</font>' : $r['title'];
        $r['ab'] = <<<HTML
                     <a href="/administration/module/pto/view/{$jqPTOViewId}/edit/{$id}" target="_blank" class="btn btn-xs btn-system" style="color: #fff; font-size: 11px;">Изменить</a>
HTML;
        if(is_date($r["date"], "Y-m-d")) $r["date"] = date("d.m.Y", strtotime($r["date"]));
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}