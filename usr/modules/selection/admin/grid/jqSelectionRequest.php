<?php

class jqSelectionRequest extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$uid_types = $this->getUidTypes();
		$types = $this->getTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_selection_request as s LEFT JOIN JMY_organizations as o on(o.id=s.oid) LEFT JOIN JMY_users as u on(u.id=s.uid)  LEFT JOIN JMY_object as obj on(obj.id=s.object) WHERE {where} ";
        $this->cols = array(
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),
			'status_real' => array('hidden' => true,
                'db' => 's.status',
            ),
			
			'oid_real' => array('hidden' => true,
                'db' => 's.oid',
            ),
			
			'object_id' => array('hidden' => true,
                'db' => 's.object',
            ),

            'numb' => array(
				'db' => 's.id',
				'label' => '№',
				'width' => 10,
				'hidden' => false,
				'manual' => true,
				'search_op' => 'like'
			),	
			
			'type' => array(
				'label' => 'Тип ',
                'hidden' => false,
				'db' => 's.type',
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 30,				
            ),	
			
			'oid' => array(
				'label' => $lang['oid'],
                'hidden' => false,
				'db' => 'o.name',
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 30,				
            ),	
			
			'client' => array(			
				'label' => 'Заказчик',
                'db' => 's.uid',
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
                'width' => 15,
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
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 's.uid',
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
				'db' => 's.status',
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $auto_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($auto_types_select, 'Все'),
                ),
                'search_op' => 'equal',
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
        $rows_select[0] = 'Расчет выполнен';
		$rows_select[1] = 'Ожидает заполнения';
		
		
        $rows = array();	
        $rows[0] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-danger fa-md' data-toggle='tooltip' title='Расчет выполнен'></span>";
		$rows[1] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='Ожидает заполнения'></span>";
		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getPayTypes($select = false)
    {       
        $rows_select = array();	
		$rows_select[0] = 'Не оплачена';
		$rows_select[1] = 'Оплачена';
		$rows_select[3] = 'Частично оплачена';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='fa fa-rub text-danger fa-md' data-toggle='tooltip' title='Не оплачена'></span>";
        $rows[1] = "<span style='margin-left: -12px;' class='fa fa-rub text-success fa-md' data-toggle='tooltip' title='Оплачена'></span>";    
		$rows[2] = "<span style='margin-left: -12px;' class='fa fa-rub text-warning fa-md' data-toggle='tooltip' title='Частично оплачена'></span>";     
		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_selection_request as r LEFT JOIN JMY_users as u on (r.uid=u.id) WHERE r.uid <> '0'");
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

	protected function getTypes()
    {
		$rows = getTypeBlock();
        return $rows;
	}

    protected function parseRow($r)
    {
		global $org_array, $lang; 		
	
		$r['numb'] = module_perfix('selection', $r['id']);	
		$r['object'] = empty($r['object_id']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/object/view/'.$r['object_id'].'" target="_blank">'.$r['object'].'</a>';
		$r['oid'] = empty($r['oid_real']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/organizations/edit/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';

		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \'/administration/module/selection/view_request/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр заявки</button>						
					</div>';
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}