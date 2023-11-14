<?php

class jqSelection extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$uid_types = $this->getUidTypes();
		$types = $this->getTypes();
		//$pay_types = $this->getPayTypes();
		//$pay_types_select = $this->getPayTypes(true);
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_selection as s LEFT JOIN JMY_users as u on(u.id=s.uid) WHERE {where} ";
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
			
			'code' => array(
				'db' => 's.code',
				'label' => 'Код',
				'width' => 35,
				'hidden' => false,
				'manual' => false,
				'encode' => false,
				'search_op' => 'like'
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
			'aid_scheme_real' => array('hidden' => true,
                'db' => 's.aid_scheme',
            ),
            'aid_scheme' => array(
				'db' => 's.aid_scheme',
				'label' => 'Схема',
				'width' => 10,
				'hidden' => false,
				'encode' => false,
				'manual' => true,
				'search_op' => 'like'
			),	
			'aid_size_real' => array('hidden' => true,
                'db' => 's.aid_size',
            ),
            'aid_size' => array(
				'db' => 's.aid_size',
				'label' => 'Размеры',
				'width' => 10,
				'hidden' => false,
				'encode' => false,
				'manual' => true,
				'search_op' => 'like'
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
			/*
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
			*/
            	
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
		$rows_select[1] = 'Продажа создана';
		$rows_select[2] = 'Получение сроков';
		$rows_select[3] = 'Сроки получены';
		$rows_select[4] = 'Отправлено на закупку';
		$rows_select[5] = 'Закупка обрабатывается';
		$rows_select[6] = 'Товары заказаны';
		$rows_select[7] = 'Товары прибыли';
		$rows_select[8] = 'Товары отгружены';
		$rows_select[9] = 'Продажа закрыта';
		
		$rows = array();	
		$rows[1] = "<span style='margin-left: -12px;' class='fa fa-pencil text-system fa-md' data-toggle='tooltip' title='Продажа создана'></span>";
        $rows[2] = "<span style='margin-left: -12px;' class='fa fa-clock-o text-alert fa-md' data-toggle='tooltip' title='Получение сроков'></span>";    
		$rows[3] = "<span style='margin-left: -12px;' class='fa fa-clock-o text-success fa-md' data-toggle='tooltip' title='Сроки получены'></span>";    
		$rows[4] = "<span style='margin-left: -12px;' class='fa fa-shopping-cart text-primary fa-md' data-toggle='tooltip' title='Отправлено на закупку'></span>";   
		$rows[5] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='Закупка обрабатывается'></span>";  
		$rows[6] = "<span style='margin-left: -12px;' class='fa fa-truck text-warning fa-md' data-toggle='tooltip' title='Товары заказаны'></span>";    
		$rows[7] = "<span style='margin-left: -12px;' class='fa fa-plus-circle text-success fa-md' data-toggle='tooltip' title='Товары прибыли'></span>";  
		$rows[8] = "<span style='margin-left: -12px;' class='fa fa-cog text-success fa-md' data-toggle='tooltip' title='Товары отгружены'></span>";    
		$rows[9] = "<span style='margin-left: -12px;' class='fa fa-check-circle text-success fa-md' data-toggle='tooltip' title='Продажа закрыта'></span>";  
		
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
		$rows = getTypeBlock();
        return $rows;
	}

    protected function parseRow($r)
    {
		global $org_array, $lang; 		
	
		$r['numb'] = module_perfix('selection', $r['id']);	

		$r["aid_scheme"] = empty($r["aid_scheme_real"]) ? '<i style="font-size: 25px;" class="fa fa-circle-o fa-md text-danger" aria-hidden="true"></i>' : '<i class="fa fa-check-circle fa-md text-success" style="font-size: 25px;" aria-hidden="true"></i>';

		$r["aid_size"] = empty($r["aid_size_real"]) ? '<i style="font-size: 25px;" class="fa fa-circle-o fa-md text-danger" aria-hidden="true"></i>' : '<i class="fa fa-check-circle fa-md text-success" style="font-size: 25px;" aria-hidden="true"></i>';

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