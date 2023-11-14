<?php

class jqFcontrolStorage extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		//$uid_types = $this->getUidTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
        
		
        );	
        $this->query = "SELECT {fields} FROM JMY_control_storage WHERE {where} ";
        $this->cols = array(	
			
            'id' => array('hidden' => true,
                'db' => 'id',
            ),	
            'numb' => array(
				'db' => 'id',
				'label' => '№',
				'width' => 10,
				'hidden' => false,
				'manual' => true,
				'search_op' => 'like'
            ),  
			 'title' => array(
				'db' => 'title',
				'label' => 'Заголовок',
				'width' => 130,
				'hidden' => false,
				'manual' => false,
				'search_op' => 'like'
            ),  
            'date' => array(
				'label' => $lang['date'],
				'db' => 'date',
                'width' => 30,
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
			'status' => array(
				'label' => $lang['status'],
				'width' => 20,
				'align' => 'center',
				'db' => 'status',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
                'stype' => 'select',
                'defaultValue' => '1',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, $lang['all']),
                ),
                'search_op' => 'equal',
            ),
			'ab' => array(
				'label' => $lang['action'],
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
		global $lang;
        $rows_select = array();	
		$rows_select[1] = $lang['fcontrol_control_status_1'];
		$rows_select[2] = $lang['fcontrol_control_status_3'];
		$rows_select[3] = $lang['fcontrol_control_status_2'];
		
		$rows = array();	
		$rows[1] = "<span style='margin-left: -12px;' class='far fa-clock text-warning fa-md' data-toggle='tooltip' title='".$lang['fcontrol_control_status_1']."'></span>";
        $rows[2] = "<span style='margin-left: -12px;' class='far fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$lang['fcontrol_control_status_3']."'></span>"; 
		$rows[3] =  "<span style='margin-left: -12px;' class='far fa-times-circle text-danger fa-md' data-toggle='tooltip' title='".$lang['fcontrol_control_status_2']."'></span>";
		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getUidTypes()
    {
		global $lang;
        $result = $this->DB->query("SELECT * FROM JMY_control as c LEFT JOIN JMY_users as u on (c.uid=u.id) WHERE c.uid <> '0'");
        $rows = array();
		$rows[0] = $lang['not_specified'];
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
		$r['numb'] = module_perfix('fcontrol_storage', $r['id']);	
        
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/fcontrol/storage_detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр заявки</button>
					</div>
					<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/order/' . $r['id'] . '\'" class="btn btn-xs btn-system"><span class="glyphicon glyphicon-print " data-toggle="tooltip" title="Распечатать"></span></button>						
					</div>';	
                    
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        return $r;
    }
	
	protected function opDialog()
    {
      echo 'r';
    }
}