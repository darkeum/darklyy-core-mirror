<?php

class jqLogisticsDeliveryArchive extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		// $auto_types = $this->getAutoTypes();
		// $auto_types_select = $this->getAutoTypes(true);
		$uid_types = $this->getUidTypes();
		// $org_types = $this->getOrgTypes();
        $module_type = [
            "realization" => "Реализация",
            "stock_transfer" => "Перемещение",
        ];
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );



		$this->nav = array(
            'excel' => true,
            'exceltext' => 'Excel', 
        );		
        $this->query = "SELECT {fields} FROM `".DB_PREFIX."_logistics_delivery` as ld 
        LEFT JOIN `".DB_PREFIX."_realization` as r ON(r.id = ld.pid)
        LEFT JOIN `".DB_PREFIX."_stock_transfer` as st ON(st.id = ld.pid)
        WHERE ld.status = 3 AND {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'ld.id',
            ),	
            'module_' => array('hidden' => true,
                'db' => 'ld.module',
            ),	
            'pid_' => array('hidden' => true,
                'db' => 'ld.pid',
            ),	

            'numb' => array(
				'db' => 'ld.id',
				'label' => '№',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
			),	

			'module_type' => array(			
				'label' => 'Модуль',
				'null' => 'Не указано',
                'db' => 'ld.module',
                'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'replace' => $module_type,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($module_type, 'Все'),
                ),
                'search_op' => 'equal',
            ),

            'numb_module' => array(
				'db' => 'case when ld.module = "realization" then r.numb when ld.module = "stock_transfer" then st.numb end',
				'label' => 'Модуль номер',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
			),	

            // 'oid' => array(
			// 	'db' => 'case when ld.module = "realization" then r.oid when ld.module = "stock_transfer" then st.oid end',
			// 	'label' => 'Контрагент',
			// 	'width' => 15,
			// 	'hidden' => false,
			// 	'search_op' => 'like'
			// ),	
			
			// 'title' => array(
			// 	'db' => 'l.title',
			// 	'label' => 'Наименование',
			// 	'width' => 35,
			// 	'encode' => false,
			// 	'hidden' => false,
			// 	'null' => 'Не указан',
			// 	'search_op' => 'like'
			// ), 	
			
			'date' => array(
				'label' => 'Дата создания',
				'db' => 'ld.created_at',
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
			
			
			'driver' => array(			
				'label' => 'Водитель',
                'db' => 'ld.driver',
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

			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 'ld.uid',
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
			
			// 'status' => array(
			// 	'label' => $lang['status'],
			// 	'width' => 15,
			// 	'align' => 'center',
			// 	'db' => 's.status',
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'sortable' => false,
			// 	'replace' => $auto_types,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'value' => new jqGrid_Data_Value($auto_types_select, 'Все'),
            //     ),
            //     'search_op' => 'equal',							
			// 	'excel_hidden' => true,
			// ),
            'price' => array(
				'db' => 'ld.delivery_price',
				'label' => 'Цена доставки',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like',
                'encode' => false,
                'excel_hidden' => true,
                'title' => false,	
                
			),	
            'price_excel' => array(
				'db' => 'ld.delivery_price',
				'label' => 'Цена доставки',
				'width' => 10,
				'excel_only' => true,
				'search_op' => 'like',
                'encode' => false,
                'hidden' => true,
                
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
		$rows_select[1] = '';

		$rows = array();	
		$rows[1] = "<span style='margin-left: -12px;' class='fa fa-pencil text-system fa-md' data-toggle='tooltip' title='".$rows_select[1]."'></span>";
		
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
	
		// $r['title'] = empty($r['title']) ? '<font color="red">Не указано</font>' : $r['title'];
		// $r['ab'] = '<div class="btn-group">
		// 				<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
		// 				<ul role="menu" class="dropdown-menu">		
		// 					<li><a href="javascript:void(0)" onclick="">***</a></li>  
		// 				</ul>
		// 			</div>
		// 			';	
        // $r['price']='	
        // <button type="button" class="link-secondary" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
        // '.$r['price'].' руб
        // </button>
        // ';

        $r['price'] = '
        <a href="javascript:void(0)" title="'.$r['price'].'" onclick="$(`#syncModal-'. $r['id'].'`).modal(`show`);">'.$r['price'].' руб</a>
				
        <div class="modal fade" id="syncModal-'. $r['id'].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="" style="color: #000;" id="exampleModalLabel">Изменить цену</h5>       
                    </div>
                    <form id="postForm"  method="POST">
                        <div class="modal-body" style="color: #000;">                   
                            <input type="hidden" name="id" id="id" value='.$r['id'].'>
                            <input type="number" name="price"  id="price" step="0.01" class="form-control" value='.$r['price'].' placeholder="Введите новую цену">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="submit" class="btn btn-primary">Изменить</button>
                        </div>
                    </form>
                </div>
            </div>
      </div>     
    ';

        if(is_date($r["date"], "Y-m-d H:i:s")) $r["date"] = date("d.m.Y H:i:s", strtotime($r["date"]));
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}