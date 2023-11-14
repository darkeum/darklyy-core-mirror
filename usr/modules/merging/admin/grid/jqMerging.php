<?php

class jqMerging extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$uid_types = $this->getUidTypes();
		$org_types = $this->getOrgTypes();
		$stock_types = $this->getStockTypes();
		$merging_types = $this->getMergingTypes();
		$merging_types_select = $this->getMergingTypes(true);
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );	
		$this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',	
			'trash' => true,			
            'trashtext' => 'Показать удаленные',				
            'trashfield' => 'm.del',
			
        );			
        $this->query = "SELECT {fields} FROM `".DB_PREFIX."_merging` as m 
        LEFT JOIN `".DB_PREFIX."_object` as obj on(obj.id=m.object) 
        LEFT JOIN `".DB_PREFIX."_merging_additional` as ma ON(ma.merging_id = m.id) 
        WHERE {where}";
        $this->cols = array(		
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'm.id',
            ),	
			'del' => array('hidden' => true,
                'db' => 'm.del',
            ),

            'id_view' => array(
				'db' => 'm.id',
				'label' => 'ID',
				'width' => 5,
				'hidden' => false,
				'search_op' => 'like'
            ),	
            
            'numb' => array(
				'db' => 'm.numb',
				'label' => '№',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
			),	
			
			'title' => array(
				'db' => 'm.name',
				'label' => 'Наименование',
				'width' => 40,
				'encode' => false,
				'hidden' => false,
				'null' => 'Не указан',
				'search_op' => 'like'
			), 	
			
			'count' => array(
				'db' => 'm.count',
				'label' => 'Кол-во',
				'width' => 7,
				'encode' => false,
				'hidden' => false,
				'null' => 'Не указан',
				'search_op' => 'like'
			), 	
			
			'object' => array(
				'label' => $lang['object'],
                'hidden' => false,
				'db' => 'obj.name',				
				'null' => $lang['not_specified'],
				'encode' => false,
				'width' => 30,				
            ),	
			
			'stock' => array(
				'db' => 'm.stock_id',
				'label' => 'Склад',
				'width' => 15,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'replace' => $stock_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($stock_types, 'Все'),
                ),
                'search_op' => 'equal',
			), 	
			
			'type' => array(
				'db' => 'm.type',
				'label' => 'Тип',
				'width' => 10,
				'align' => 'center',             
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'replace' => $merging_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($merging_types_select, 'Все'),
                ),
                'search_op' => 'equal',
				'excel_hidden' => true,
			),
			
			'type_excel' => array(
				'label' => 'Активность',
				'width' => 15,
				'align' => 'center', 
				'hidden' => true,
				'db' => 'm.type',
				'encode' => false,
				'sortable' => false,
				'replace' => $merging_types_select,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($merging_types_select, $lang['all']),
                ),
                'search_op' => 'equal',
				'excel_only' => true,
				
            ),
			
			'org' => array(			
				'label' => 'Организация',
                'db' => 'm.org',
				'width' => 20,              
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
			
			'date' => array(
				'label' => 'Дата',
				'db' => 'm.date',
                'width' => 20,
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
                'db' => 'm.uid',
                'width' => 15,              
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
				'label' => 'Активность',
				'width' => 10,
				'align' => 'center',
				'db' => 'm.active',
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
			
			'status_excel' => array(
				'label' => 'Активность',
				'width' => 15,
				'align' => 'center', 
				'hidden' => true,
				'db' => 'm.active',
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

            'additional' => array(
				'label' => 'Доукомплектация',
				'width' => 10,
				'align' => 'center',
				'db' => 'IF(ma.id IS NULL, 0, 1)',
				'hidden' => false,
                'null' => 0,
				'encode' => false,
				'sortable' => false,
				'replace' => $this->additional(),
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($this->additional(true), 'Все'),
                ),
                'search_op' => 'equal',							
				'excel_hidden' => true,
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
	
	protected function getAutoTypes($select = false)
    {  
		$rows_select = array();	
		$rows_select[0] = 'Не активирована';
		$rows_select[1] = 'Активирована';

		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='far fa-circle fa-md text-danger' data-toggle='tooltip' title='".$rows_select[0]."'></span>";
		$rows[1] = "<span style='margin-left: -12px;' class='far fa-circle fa-md text-success' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
		
        return ($select ?  $rows_select :  $rows);
	}

	protected function additional($select = false)
    {  
		$rows_select = array();	
		$rows_select[0] = 'Нет';
		$rows_select[1] = 'Есть';

		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='far fa-circle fa-md text-danger' data-toggle='tooltip' title='".$rows_select[0]."'></span>";
		$rows[1] = "<span style='margin-left: -12px;' class='far fa-circle fa-md text-success' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
		
        return ($select ?  $rows_select :  $rows);
	}
	
	protected function getMergingTypes($select = false)
    {  
		$rows_select = array();	
		$rows_select[1] = 'Комплектация';
		$rows_select[2] = 'Разкомплектация';

		$rows = array();	
		$rows[1] = "<span style='margin-left: -12px;' class='far fa-box fa-md text-info' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
		$rows[2] = "<span style='margin-left: -12px;' class='far fa-box-open fa-md text-alert' data-toggle='tooltip' title='".$rows_select[2]."'></span>";
		
        return ($select ?  $rows_select :  $rows);
	}
	
	protected function getUidTypes()
    {
		global $db;
        $result = $db->query("SELECT * FROM JMY_merging as m LEFT JOIN JMY_users as u ON(u.id = m.uid) WHERE u.active = '1' AND u.group = '1' ORDER BY u.surname");
        $rows = array();
		$rows[0] = 'Не указан';
        while($r = $db->getRow($result))
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
        $result = $this->DB->query("SELECT r.id, o.name FROM JMY_org as r LEFT JOIN JMY_organizations as o on(r.oid=o.id)");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {			
            $rows[$r['id']] = $r['name'];
        }
        return $rows;
    }
	
	protected function getStockTypes()
    {
        $result = $this->DB->query("SELECT r.id, r.name FROM JMY_stock as r");
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
		$r['object'] = empty($r['object']) ? '<font color="red">Не указано</font>' : $r['object'];
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/merging/detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">		
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Распечатать</a></li>  
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Отправить на синхронизацию</a></li>  
							<li class="divider"></li>
							'.($r['del'] == '0' ? '<li><a href="javascript:void(0)" onclick="modalRemove('.$r['id'].')" data-status="" data-id="'.$r['id'].'">Удалить</a></li> ' : '<li><a href="javascript:void(0)" onclick="modalRestore('.$r['id'].')" data-status="" data-id="'.$r['id'].'">Восстановить</a></li>').'
						</ul>
					</div>
					';	
        if(is_date($r["date"], "Y-m-d")) $r["date"] = date("d.m.Y", strtotime($r["date"]));
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}