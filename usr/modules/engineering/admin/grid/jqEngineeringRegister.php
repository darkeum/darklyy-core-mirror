<?php

class jqEngineeringRegister extends jqGrid
{
    protected function init()
    {
		global $core, $lang;
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$section_types = $this->getSectionTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );	
        $this->query = "SELECT {fields} FROM JMY_engineering_register as er LEFT JOIN JMY_object as obj on(obj.id = er.object) WHERE {where} ";
        $this->cols = array(
		
	
			'id' => array('hidden' => true,
                'db' => 'er.id',
            ),
			'status_real' => array('hidden' => true,
                'db' => 'er.status',
            ),
			
			'object_id' => array('hidden' => true,
                'db' => 'er.object',
            ),

            'id_v' => array(
				'db' => 'er.id',
				'label' => '№',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
			),	
            'numb' => array(
				'db' => 'er.numb',
				'label' => 'Шифр',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
			),	
			
			'section' => array(			
				'label' => 'Раздел',
				'null' => 'Не указано',
                'db' => 'er.section',
               	'width' => 30,              
				'hidden' => false,
				'encode' => false,
				'sortable' => false,
                'search_op' => 'like',
            ), 
			
			'object' => array(
				'label' => 'Объект',
                'hidden' => false,
				'db' => 'obj.name',
				'null' => 'Не указано',
				'encode' => false,
				'width' => 40,				
            ),	
			
			'date' => array(
				'label' => 'Дата',
				'db' => 'er.date',
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
				'width' => 25,
				'db' => 'er.uid',
				'hidden' => false,
                'encode' => false,
				'sortable' => false,			
                'search_op' => 'like',							
            ),
			
			'status' => array(
				'label' => $lang['status'],
				'width' => 15,
				'align' => 'center',
				'db' => 'er.status',
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
		require_once("usr/modules/engineering/admin/sub/functions.php");

		$rows_select = get_status();	
		$rows = get_status();
		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getSectionTypes()
    {
		require_once("usr/modules/engineering/admin/sub/functions.php");
		$rows = section_type();
        return $rows;
    }

    protected function parseRow($r)
    {
		global $org_array, $lang; 		
	
        $r['object'] = empty($r['object_id']) ? '<font color="red">Не указано</font>' : '<a href="'.ADMIN.'/object/view/'.$r['object_id'].'" target="_blank">'.$r['object'].'</a>';
        $uid = json_decode($r["uid"], true);
        $elems = [];
        if(count($uid) > 0){
            foreach($uid as $val){
                $elems[] = shortUserName($val);
            }
            $r["uid"] = implode(" / ", $elems);
        }

        require_once("usr/modules/engineering/admin/sub/functions.php");
        $section = json_decode($r["section"], true);
        $section_type = section_type();
        $elems = [];
        if(count($section) > 0){
            foreach($section as $val){
                $elems[] = $section_type[$val];
            }
            $r["section"] = implode(" / ", $elems);
        }
		/*
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/order/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">		
							<li><a href="javascript:void(0)" onclick="modal_print('.$r["id"].');">Распечатать продажу</a></li>  
							<li><a href="javascript:void(0)" onclick="window.open(\'/catalog_v2/specification/sales/'.$r["id"].'\',\'_blank\'); return false;">Получить спецификацию</a></li>
							<li><a href="javascript:void(0)" onclick="copySalesModal(this);" data-id="'.$r['id'].'">Скопировать</a></li>
							<li><a href="javascript:void(0)" onclick="$(`#syncModal-'. $r['id'].'`).modal(`show`);">Отправить на синхронизацию</a></li> 
							<li class="divider"></li>
							<li><a href="javascript:void(0)" onclick="modal_o(\'#modal-form-'. $r['id'].'\')">Отметить на удаление</a></li> 
						</ul>
						  
						  <div class="modal fade" id="syncModal-'. $r['id'].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							  <div class="modal-dialog" role="document">
								<div class="modal-content">
								  <div class="modal-header">
									<h5 class="modal-title" style="color: #000;" id="exampleModalLabel">Отправить на синхронизацию</h5>       
								  </div>
								  <div class="modal-body" style="color: #000;">
									Вы действительно хотите отправить данную реализацию на синхронизацию с 1С Бухгалтерией? 
								  </div>
								  <div class="modal-footer">
									<button type="button" style="color: #fff;" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
									<button type="button" style="color: #fff;" class="btn btn-primary syncBtn" data-id="'.$r['id'].'">Отправить</button>
								  </div>
								</div>
							  </div>
							</div>    
						  
					</div>
					<div class="btn-group">
						<button type="button" onclick="modal_print('.$r["id"].');" class="btn btn-xs btn-system"><span class="glyphicon glyphicon-print " data-toggle="tooltip" title="Распечатать"></span></button>
					</div>
					
					';	
					*/
        if(is_date($r["date"], "Y-m-d")) $r["date"] = date("d.m.Y", strtotime($r["date"]));

        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}