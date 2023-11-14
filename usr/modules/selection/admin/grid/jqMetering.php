<?php

class jqMetering extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$uid_types = $this->getUidTypes();
		$types = $this->getTypes();
		$scheme = $this->getScheme();
		//$pay_types = $this->getPayTypes();
		//$pay_types_select = $this->getPayTypes(true);
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_metering as s LEFT JOIN JMY_users as u on(u.id=s.uid) WHERE {where} ";
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
				'search_op' => 'like'
			),	
			
			'd1' => array(
				'db' => 's.d1',
				'label' => 'DN1',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
			),	
			
			'd2' => array(
				'db' => 's.d2',
				'label' => 'DN2',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
			),	
			
			'ddt' => array(
				'db' => 's.ddt',
				'label' => 'DDT',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
			),	
			
			'ldt' => array(
				'db' => 's.ldt',
				'label' => 'D расходомера',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
			),	
					
			'scheme' => array(
				'db' => 's.scheme',
				'label' => 'Схема',
				'width' => 25,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
				'replace' => $scheme,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($scheme, 'Все'),
                ),
                'search_op' => 'equal',
            ),
			
			'type' => array(
				'db' => 's.type',
				'label' => 'Тип',
				'width' => 25,              
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

			'date' => array(
				'label' => 'Дата',
				'db' => 's.date',
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
	
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT * FROM JMY_selection as r LEFT JOIN JMY_users as u on (r.uid=u.id) WHERE r.uid <> '0'");
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
		$rows = getTypeMetring();
        return $rows;
	}
	
	protected function getScheme()
    {
		$rows = getTypeMetringScheme();
        return $rows;
	}

    protected function parseRow($r)
    {
		global $org_array, $lang; 		
	
		$r['numb'] = module_perfix('selection', $r['id']);	

		//$r["aid_scheme"] = empty($r["aid_scheme_real"]) ? '<i style="font-size: 25px;" class="fa fa-circle-o fa-md text-danger" aria-hidden="true"></i>' : '<i class="fa fa-check-circle fa-md text-success" style="font-size: 25px;" aria-hidden="true"></i>';

		//$r["aid_size"] = empty($r["aid_size_real"]) ? '<i style="font-size: 25px;" class="fa fa-circle-o fa-md text-danger" aria-hidden="true"></i>' : '<i class="fa fa-check-circle fa-md text-success" style="font-size: 25px;" aria-hidden="true"></i>';

		/*
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/order/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Разместить заказ</a></li>  	
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Распечатать продажу</a></li>  
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Получить спецификацию</a></li>
							<li class="divider"></li>
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Отметить на удаление</a></li> 
						</ul>
						<div id="modal-form-'. $r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">Разместить заказ №' . $r['id'] . '?</h3>
								<hr class="short alt">
								<p>Вы действительно хотите разместить заказ №' . $r['id'] . '?</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/send_order/'. $r['id'].'\'" class="btn btn-success">Отправить</button>
							  </div>
							</div>
						  </div>
					</div>
					<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/order/' . $r['id'] . '\'" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-flash" data-toggle="tooltip" title="Распечатать"></span></button>
						
					</div>
					<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/order/' . $r['id'] . '\'" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-print " data-toggle="tooltip" title="Распечатать"></span></button>
						
					</div>
					<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/order/' . $r['id'] . '\'" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-floppy-disk" data-toggle="tooltip" title="Сохранить"></span></button>
						
					</div>
					';	
					*/
        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}