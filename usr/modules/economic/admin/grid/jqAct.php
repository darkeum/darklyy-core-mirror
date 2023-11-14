<?php

class jqAct extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
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
        $this->query = "SELECT {fields} 
        FROM JMY_act_reconciliation as ar
        LEFT JOIN JMY_organizations as o on(o.id=ar.oid) 
        WHERE {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'ar.id',
            ),	

            'id_view' => array(
				'db' => 'ar.id',
				'label' => 'ID',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
			),	

            'date' => array(
				'label' => 'Дата',
				'db' => 'ar.date',
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

            'numb' => array(
				'db' => 'ar.numb',
				'label' => '№',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
			),	

            'oid' => array(
				'label' => 'Контрагент',
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
                'null' => 'Не указан',
				'width' => 35,				
            ),
            
            'date_start' => array(
				'label' => 'Дата',
				'db' => 'ar.date_start',
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

            'date_end' => array(
				'label' => 'Дата',
				'db' => 'ar.date_end',
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
			
			// 'title' => array(
			// 	'db' => 's.title',
			// 	'label' => 'Наименование',
			// 	'width' => 35,
			// 	'encode' => false,
			// 	'hidden' => false,
			// 	'null' => 'Не указан',
			// 	'search_op' => 'like'
			// ), 	
			
			'org' => array(			
				'label' => 'Организация',
				'null' => 'Не указано',
                'db' => 'ar.org',
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
			
			
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 'ar.uid',
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
			
			'active' => array(
				'label' => "Проведенность",
				'width' => 15,
				'align' => 'center',
				'db' => 'ar.active',
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
			
			// 'ab' => array(
			// 	'label' => 'Выберите действие',
			// 	'width' => 50,
			// 	'hidden' => false,
			// 	'manual' => true,
			// 	'encode' => false,
			// 	'sortable' => false,			
            //     'search_op' => false,
			// 	'search' => false,
			// 	'title' => false,							
			// 	'excel_hidden' => true,
            // ),
			
            	
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
		$rows_select[0] = 'Не проведенная';
		$rows_select[1] = 'Проведенная';

		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='fas fa-check-circle text-danger fa-md' data-toggle='tooltip' title='".$rows_select[0]."'></span>";
		$rows[1] = "<span style='margin-left: -12px;' class='fas fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
		
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
	
		$r['title'] = empty($r['title']) ? '<font color="red">Не указано</font>' : $r['title'];
		$r['ab'] = '<div class="btn-group">
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">		
							<li><a href="javascript:void(0)" onclick="">***</a></li>  
						</ul>
					</div>
					';	
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        if(is_date($r["date_start"], "Y-m-d")) $r["date_start"] = date("d.m.Y", strtotime($r["date_start"]));
        if(is_date($r["date_end"], "Y-m-d")) $r["date_end"] = date("d.m.Y", strtotime($r["date_end"]));
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}