<?php

class jqStorageMain extends jqGrid
{
    protected function init()
    {
		global $lang, $whereSM, $typeSM;
		$uid_types = $this->getUidTypes();
		$status_types = $this->getStatusTypes();
		$types = $this->getTypes();
		$status_types_select = $this->getStatusTypes(true);
		$types_select = $this->getTypes(true);
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->nav = array(
            #Set common excel export
			'trash' => true,			
            'trashtext' => 'Показать удаленные',
            'trashtype' => 'null',				
            'trashfield' => 's.deleted_at',
        );	
		$this->do_search = true;
		$this->multipleSearch = true;
		$this->query = "SELECT {fields} FROM JMY_storage as s LEFT JOIN JMY_sales as sl on(sl.id=s.sales_id) LEFT JOIN JMY_organizations as o on(o.id=s.oid) LEFT JOIN JMY_users as u on(u.id=s.uid) where s.type NOT IN (4, 5) AND {where} ";
        $this->cols = array(		
			
            'id' => array('hidden' => true,
                'db' => 's.id',
            ),
			'oid_real' => array('hidden' => true,
                'db' => 's.oid',
            ),
			'sales_real' => array(
				'hidden' => true,
				'db' => 's.sales_id',				
            ),
					
			'numb' => array(
				'label' => '№',
				'width' => 15,
				'db' => 's.id',
				'hidden' => false,
				'search_op' => 'like'
            ),
            
            'sales' => array(
				'label' => $lang['sales'],
				'width' => 20,
				'db' => 'sl.numb',
				'hidden' => false,
				'encode' => false,
                'search_op' => 'like',
                'null' => $lang['not_specified'],	
            ),
							
		
			'uid' => array(			
				'label' => $lang['uid'],
                'db' => 's.uid',
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
            'comment' => array(			
				'label' => $lang['comment'],
                'db' => 's.comment',
                'width' => 40,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'stype' => 'select',
                'search_op' => 'equal',
            ),   			
			
			'date' => array(
				'label' => $lang['date'],
				'db' => 's.date',
                'width' => 40,
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
                'null' => $lang['not_specified'],	
            ),
			'date_end' => array(
				'label' => "Дата завершения",
				'db' => 's.date_end',
                'width' => 40,
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
                'null' => $lang['not_specified'],	
            ),

			'status' => array(			
				'label' => $lang['status'],
                'db' => 's.status',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'replace' => $status_types,
                'stype' => 'select',
                'searchoptions' => array(
					'defaultValue' => '1',
                    'value' => new jqGrid_Data_Value($status_types_select, $lang['all']),				
                ),
                'search_op' => 'equal',
            ), 			
			
			'ab' => array(
				'label' => $lang['action'],
				'width' => 40,
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
            #Stap dates if start is bigger than end
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
	
	protected function getUidTypes()
    {
		global $lang;		
        $result = $this->DB->query("SELECT * FROM JMY_storage as s LEFT JOIN JMY_users as u on (s.uid=u.id) WHERE s.uid <> '0' ORDER BY u.surname");
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
	
	protected function getStatusTypes($select = false)
    { 
		global $lang;
        $rows_select = array();	
		$rows_select[1] = $lang['storage_count_order_wait'];
		$rows_select[2] = $lang['storage_count_order_ok'];
		
        $rows = array();
		$rows[1] = "<span class='fa fa-clock-o text-warning fa-md' data-toggle='tooltip' title='".$lang['storage_count_order_wait']."'></span>";
        $rows[2] = "<span class='fas fa-check-circle text-success fa-md' data-toggle='tooltip' title='".$lang['storage_count_order_ok']."'></span>";
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getTypes($select = false)
    {  
		global $lang;
        $rows_select = array();	
        $rows = array();
        $rows_select[1] = $lang['storage_count_order_sos'];
        $rows_select[2] = $lang['storage_count_order_supply'];
        $rows_select[3] = 'Ручное добавление';

        $rows[1] = "<span class='text-danger fa-md' data-toggle='tooltip' title='".$rows_select[1]."' style=\"font-weight: bold; font-size: 15px;\">SOS</span>";
        $rows[2] = "<span class='fas fa-shopping-cart fa-md text-primary' data-toggle='tooltip' title='".$rows_select[2]."'></span>";
        $rows[3] = "<span class='fa fa-pencil text-info fa-md' data-toggle='tooltip' title='".$rows_select[3]."'></span>";
        
        return ($select ?  $rows_select :  $rows);
    }
	


    protected function parseRow($r)
    {
		global $lang; 
		$r['oid'] = empty($r['oid_real']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/organizations/edit/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';
		$r['sales'] = empty($r['sales_real']) ? '<font color="red">'.$lang['not_specified'].'</font>' : '<a href="'.ADMIN.'/module/sales/order/'.$r['sales_real'].'" target="_blank">'. $r['sales'].'</a>';
        $r['comment'] = empty($r['comment']) ? '<font color="red">'.$lang['not_specified'].'</font>' : $r['comment'];
        $r['numb'] = 'SN-'.$r['id'];
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/storage/detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр заявки</button>
                        <button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">'.$lang['action'].'</span></button>
				        <ul role="menu" class="dropdown-menu">
				        	<li><a href="javascript:void(0)" onclick="delModal('.$r["id"].');">'.$lang['delete'].'</a></li> 
				        </ul>					
					</div>';	
                    
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        if(is_date($r["date_end"], "Y-m-d H:i:s")) $r["date_end"] = date("d.m.Y H:i:s", strtotime($r["date_end"]));
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}