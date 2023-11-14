<?php

class jqCert extends jqGrid
{
    protected function init()
    {
	//	$this->nav = array('excel' => true);
		$delivery_types = $this->getDeliveryTypes();
		$org_types = $this->getOrgTypes();
		$uid_types = $this->getUidTypes();
		$cert_types = $this->getCertTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );
        $this->query = "SELECT {fields} FROM JMY_cert as c WHERE {where} ";
        $this->cols = array(
		
			
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'c.id',
            ),

			 'aid' => array('hidden' => true,
                'db' => 'c.aid',
            ),
			
            'numb' => array(
                'label' => '№',
                'db' => 'c.id',
				'width' => 10,
				'search_op' => 'like'
            ),	
			'date_start' => array(
				'label' => 'Дата начала',
                'width' => 20,
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => array(
                        array('text' => 'За 2016 год', 'dateStart' => '2016-01-01', 'dateEnd' => '2016-12-31'),
                        array('text' => 'За 2017 год', 'dateStart' => '2017-01-01', 'dateEnd' => '2017-12-31'),
						array('text' => 'За 2018 год', 'dateStart' => '2018-01-01', 'dateEnd' => '2018-12-31'),
                    ),
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',                       
                    ),
                ))),
                'search_op' => 'date_range',
            ),
			'date_end' => array(
				'label' => 'Дата окончания',
                'width' => 20,
                'searchoptions' => array('dataInit' => $this->initDateRangePicker(array(                   
                    'dateFormat' => 'dd.mm.yy',
                    'onChange' => new jqGrid_Data_Raw('dateRangePicker_onChange'),
                    'presetRanges' => array(
                        array('text' => 'За 2016 год', 'dateStart' => '2016-01-01', 'dateEnd' => '2016-12-31'),
                        array('text' => 'За 2017 год', 'dateStart' => '2017-01-01', 'dateEnd' => '2017-12-31'),
						array('text' => 'За 2018 год', 'dateStart' => '2018-01-01', 'dateEnd' => '2018-12-31'),
                    ),
                    'datepickerOptions' => array(
                        'changeMonth' => true,
                        'changeYear' => true,
                        'dateFormat' => 'dd.mm.yy',                       
                    ),
                ))),
                'search_op' => 'date_range',
            ),
			'cert_types' => array(			
				'label' => 'Тип сертификата',
                'db' => 'c.type',
                'width' => 30,
                'replace' => $cert_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($cert_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),  
			'numb_cert' => array(
				'label' => 'Номер',
				 'db' => 'c.numb',
				'width' => 50,
				'hidden' => false,
				'search_op' => 'like'
            ),
			'issue_cert' => array(
				'label' => 'Выдан',
				 'db' => 'c.issue',
				'width' => 50,
				'hidden' => false,
				'search_op' => 'like'
            ),
			'comment' => array(
				'label' => 'Комментарий',
				'db' => 'c.comment',
				'width' => 50,
				'hidden' => false,
				'search_op' => 'like'
            ),
			'uid' => array(			
				'label' => 'Ответственный',
                'db' => 'c.uid',
                'width' => 30,
                'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),  
			'ab' => array(
				'label' => 'Действия',
				'width' => 30,
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
	
	protected function getDeliveryTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_reestr_type");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {
            $rows[$r['id']] = $r['short'].' - '.$r['name'];
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
	
	protected function getUidTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_cert as r LEFT JOIN JMY_users as u on (r.uid=u.id)");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {	
			$result_row = $r['surname'].' ';
			$result_row .= mb_substr($r['name'], 0, 1) . '. ';
			$result_row .= mb_substr($r['ochestvo'], 0, 1) . '.';
            $rows[$r['id']] = $result_row;
        }
        return $rows;
    }

	protected function getCertTypes()
    {
        $result = $this->DB->query("SELECT * FROM JMY_cert_type");
        $rows = array();
        while($r = $this->DB->fetch($result))
        {				
            $rows[$r['id']] = $r['title'];
        }
        return $rows;
    }
	
    protected function parseRow($r)
    {
		global $org_array, $lang;   
        // $r['numb'] = 'CR-'.$r['id'];	
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/catalog_v2/cert/edit/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Изменить</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a target="_blank" href="'.ADMIN.'/module/catalog_v2/cert/list/' . $r['id'] . '">Прикрепить товары</a></li> 
							<li><a target="_blank" href="'.ADMIN.'/module/catalog_v2/cert/view/' . $r['id'] . '">Просмотреть прикрепленные</a></li> 
							<li><a target="_blank" href="/download/' . $r['aid'] . '">Скачать сертификат</a></li> 
							<li><a href="javascript:void(0);"  onclick="modal_o(\'#modal-form-'.$r['id'].'\')">Удалить сертификат</a></li> 
						</ul>
					</div>
					<div id="modal-form-'.$r['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">Удалить сертификат CR-'.$r['id'].'?</h3>					
							<hr class="short alt">
							<p>Вы уверены что хотите удалить сертификат (все связи и файлы будут безвозвратно удалены)?</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \''.ADMIN.'/module/catalog_v2/cert/delete_certificates/' . $r['id'] . '\'" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						</div>
                      </div>';
        $r["issue_cert"] = ee($r["issue_cert"]);
        if(is_date($r["date_start"], "Y-m-d")) $r["date_start"] = date("d.m.Y", strtotime($r["date_start"]));
        if(is_date($r["date_end"], "Y-m-d")) $r["date_end"] = date("d.m.Y", strtotime($r["date_end"]));
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}