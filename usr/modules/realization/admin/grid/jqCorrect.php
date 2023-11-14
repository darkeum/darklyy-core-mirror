<?php

class jqCorrect extends jqGrid
{
    protected function init()
    {
		global $core, $lang;		
		$this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
        );			
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$scan_types = $this->getScanTypes();
		$scan_types_select = $this->getScanTypes(true);
		
		$uid_types = $this->getUidTypes();
		$uid_types_create = $this->getUidTypesCreate();
		$pay_types = $this->getPayTypes();
        $org_types = $this->getOrgTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_correct as c LEFT JOIN JMY_realization as s ON(s.id = c.realization_id) LEFT JOIN JMY_organizations as o on(o.id=s.oid) LEFT JOIN JMY_users as u on(u.id=s.uid) LEFT JOIN JMY_object as obj on(obj.id=s.object) LEFT JOIN JMY_org as org on(org.id=c.org) LEFT JOIN JMY_sales as sal ON(sal.id = s.sales_id) WHERE {where} ";
        $this->cols = array(
			
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'c.id',
            ),
            'sales_real' => array('hidden' => true,
                'db' => 'sal.id',
            ),
            'realization_real' => array('hidden' => true,
                'db' => 's.id',
            ),
			// 'status_real' => array('hidden' => true,
            //     'db' => 's.status',
            // ),
			'oid_real' => array('hidden' => true,
                'db' => 's.oid',
            ),
			'active_real' => array('hidden' => true,
                'db' => 'c.active',
            ),			
			'code' => array(
				'db' => 'c.id',
				'label' => 'Код',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
            ),			
            'numb' => array(
				'db' => 'c.numb',
				'label' => '№',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
            ),	
			
			'sales' => array(
				'label' => $lang['sales'],
                'hidden' => false,
				'db' => 'sal.numb',				
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 20,		
				'search_op' => 'like'
			),	
			
			'realization' => array(
				'label' => "Реализация",
                'hidden' => false,
				'db' => 's.numb',				
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 20,		
				'search_op' => 'like'
            ),	
			
			'oid' => array(
				'label' => $lang['realization_oid'],
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
                'null' => $lang['not_specified'],
				'width' => 35,				
			),	
			
			'inn' => array(
				'label' => 'ИНН',
                'hidden' => false,
				'db' => 'o.inn',
				'encode' => false,
                'null' => $lang['not_specified'],
				'width' => 20,				
			),	
			
			'object' => array(
				'label' => $lang['object'],
                'hidden' => false,
				'db' => 'obj.name',				
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 30,				
            ),	
			
			'date' => array(
				'label' => $lang['date'],
				'db' => 's.date',
                'width' => 30,
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' =>  presetRanges(),
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',                       
                    ),
                ))),
                'search_op' => 'date_range',
            ),
			
			'date_create' => array(
				'label' => 'Дата корректировки',
				'db' => 'c.date_create',
                'width' => 30,
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' =>  presetRanges(),
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
                'db' => 'c.uid',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, $lang['all']),
                ),
                'search_op' => 'equal',
			), 
			
			'org' => array(			
				'label' => 'Организация',
                'db' => 'org.id',
               'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'replace' => $org_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($org_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'total' => array(
				'label' => 'Сумма',
                'hidden' => false,
				'db' => 's.total',				
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 30,	
				'excel_hidden' => true,
            ),	
			
			'total_excel' => array(
				'label' => 'Сумма',
                'hidden' => true,
				'db' => 's.total',				
				'null' => '0',
				'encode' => false,
				'width' => 30,	
				'excel_only' => true,
            ),	
			
			// 'scan' => array(
			// 	'label' => 'Скан',
			// 	'width' => 15,
			// 	'align' => 'center',    
			// 	'db' => 's.scan',
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'sortable' => false,
			// 	'replace' => $scan_types,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => new jqGrid_Data_Value($scan_types_select, $lang['all']),
            //     ),
            //     'search_op' => 'equal',
				
			// 	'excel_hidden' => true,
				
            // ),
			
			
			'scan_excel' => array(
				'label' => 'Скан',
				'width' => 15,
				'hidden' => true,
				'align' => 'center',    
				'db' => 's.scan',
				'encode' => false,
				'sortable' => false,
				'replace' => $scan_types_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($scan_types_select, $lang['all']),
                ),
                'search_op' => 'equal',				
				'excel_only' => true,
            ),	
			
			'active' => array(
				'label' => "Активность",
				'width' => 15,
				'align' => 'center',    
				'db' => 'c.active',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, $lang['all']),
                ),
                'search_op' => 'equal',				
				'excel_hidden' => true,
            ),
			
			'active_excel' => array(
				'label' => "Активность",
				'width' => 15,
				'align' => 'center', 
				'hidden' => true,
				'db' => 'c.active',
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, $lang['all']),
                ),
                'search_op' => 'equal',
				'excel_only' => true,
				
            ),
			
			// 'ab' => array(
			// 	'label' => $lang['action'],
			// 	'width' => 50,
			// 	'hidden' => false,
			// 	'manual' => true,
			// 	'encode' => false,
			// 	'sortable' => false,			
            //     'search_op' => false,
			// 	'search' => false,
			// 	'title' => false,
			// 	'excel_hidden' => true,
            // )            	
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

            if($start > $end)
            {
                list($start, $end) = array($end, $start);
            }

			$start = date('Y-m-d', $start);
			$end = strtotime("+1 day", $end);
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
	
	
	protected function getAutoTypes($select = false)
    {       	
		global $db, $lang, $core;		
        $rows_select = array();	
		$rows_select[0] = 'Не проведена';
		$rows_select[1] = 'Проведена';		
		
        $rows = [];
		$rows[0] = "<span class='far fa-circle text-danger fa-md' data-toggle='tooltip' title='Не проведена'></span>";
        $rows[1] = "<span class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='Проведена'></span>"; 		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getScanTypes($select = false)
    {       	
		global $db, $lang, $core;		
        $rows_select = array();	
		$rows_select[0] = 'Скан отсутствует';
		$rows_select[1] = 'Скан приложен';		
		
        $rows = [];
		$rows[0] = "<span class='fal fa-file-check text-danger fa-md' data-toggle='tooltip' title='Скан отсутствует'></span>";
        $rows[1] = "<span class='fal fa-file-check text-success fa-md' data-toggle='tooltip' title='Скан приложен'></span>"; 		
        return ($select ?  $rows_select :  $rows);
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
	
	protected function getPayTypes()
    {       
        $rows = array();
		$rows[0] = 'Не оплачен';
		$rows[1] = 'Оплачен';
		$rows[2] = 'Частично оплачен';
        return $rows;
    }
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_correct as r LEFT JOIN JMY_users as u on (r.uid=u.id) WHERE r.uid <> '0' ORDER BY u.surname");
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
	protected function getUidTypesCreate()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_realization as r LEFT JOIN JMY_users as u on (r.uid_create = u.id) WHERE r.uid_create <> '0' ORDER BY u.surname");
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
		global $org_array, $lang, $core; 		
	
		$uid = $core->auth->user_info['id'];
       
		$r['sales'] = empty($r['sales']) ? 'Не указано' : '<a href="'.ADMIN.'/module/sales/order/'.$r['sales_real'].'" target="_blank">'.$r['sales'].'</a>';
		$r['realization'] = empty($r['realization']) ? 'Не указано' : '<a href="'.ADMIN.'/module/realization/order/'.$r['realization_real'].'" target="_blank">'.$r['realization'].'</a>';
		$r['oid'] = empty($r['oid_real']) ? 'Не указано' : '<a href="'.ADMIN.'/organizations/edit/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';
		// $r['ab'] = '';	
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        if(is_date($r["date_create"], "Y-m-d")) $r["date_create"] = date("d.m.Y", strtotime($r["date_create"]));
		$r['total'] = empty($r['total']) ? 'Не указано' : number_format($r['total'],2,'.',' ').' руб.';
        return $r;
    }
	
	  protected function renderNav($nav)
    {
			//print_r($nav);

        return $nav;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}