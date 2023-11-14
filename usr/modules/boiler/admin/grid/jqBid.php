<?php

class jqBid extends jqGrid
{
    protected function init()
    {
        global $core, $lang;
        $this->nav = array(
            #Set common excel export
            'excel' => true,
            'exceltext' => 'Excel',
            'trash' => true,
            'trashtext' => 'Показывать удаленные',
            'trashfield' => 'bb.archive',
        );
		$auto_types = $this->getAutoTypes();
		$auto_types_select = $this->getAutoTypes(true);
		$auto_types_archive = $this->getAutoTypesArchive();
		$auto_types_archive_select = $this->getAutoTypesArchive(true);
		$uid_types = $this->getUidTypes();
        $uid_types2 = [];
        $users = \DB::table("boiler_bid_users as bbu")->leftJoin("users as u", "u.id", "=", "bbu.uid")->select("u.*")->orderBy("u.surname")->groupBy("bbu.uid")->get();
        $this->uid_types = [];
        foreach($users as $u){
            $this->uid_types[$u->id] = $uid_types2[",{$u->id},"] = self::fio_short($u);
        }
        
		$pay_types = $this->getPayTypes();
		$pay_types_select = $this->getPayTypes(true);
		$org_types = $this->getOrgTypes();
        $this->options = array(
            'sortname' => 'id',
            'sortorder' => 'desc',
        );	
		$this->query = "SELECT {fields} 
        FROM JMY_boiler_bid as bb 
        LEFT JOIN (
            SELECT bid_id, GROUP_CONCAT(CONCAT(',', uid, ',') SEPARATOR '') as uid FROM JMY_boiler_bid_users GROUP BY bid_id
        ) as bbu ON(bbu.bid_id = bb.id)
        LEFT JOIN JMY_organizations as o on(o.id=bb.oid) 
        LEFT JOIN JMY_object as obj on(obj.id=bb.object) 
        LEFT JOIN JMY_org as org on(org.id=bb.org) 
        
        WHERE {where} ";
		
        $this->cols = array(
		
			#Real id is hidden
            'id' => array('hidden' => true,
                'db' => 'bb.id',
            ),
			'status_real' => array('hidden' => true,
                'db' => 'bb.status',
            ),
			'archive_real' => array('hidden' => true,
                'db' => 'bb.archive',
            ),
			
			'oid_real' => array('hidden' => true,
                'db' => 'bb.oid',
            ),
			
			'object_id' => array('hidden' => true,
                'db' => 'bb.object',
            ),
			 
			'code' => array(
				'db' => 'bb.id',
				'label' => 'ID',
				'width' => 10,
				'hidden' => false,
				'search_op' => 'like'
            ),	

            'numb' => array(
				'db' => 'bb.numb',
				'label' => '№',
				'width' => 15,
				'hidden' => false,
				'search_op' => 'like'
            ),	
            
			'title' => array(
				'db' => 'bb.title',
				'label' => 'Наименование',
				'width' => 35,
				'encode' => false,
				'hidden' => false,
				'null' => 'Не указан',
				'search_op' => 'like'
            ),
            
            'first_type' => array(
				'label' => 'Тип',
				'width' => 25,
				'hidden' => false,
				'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
				'title' => false,							
				'excel_hidden' => true,
            ),
            
			'oid' => array(
				'label' => 'Контрагент',
                'hidden' => false,
				'db' => 'o.name',
				'encode' => false,
                'null' => 'Не указан',
				'width' => 35,				
            ),	
			
			'object' => array(
				'label' => 'Объект',
                'hidden' => false,
				'db' => 'obj.name',
				'null' => 'Не указано',
				'encode' => false,
				'width' => 40,				
            ),		
			
			'date_create' => array(
				'label' => 'Дата создания',
				'db' => 'bb.date_create',
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
			
			'org' => array(			
				'label' => 'Организация',
				'null' => 'Не указано',
                'db' => 'bb.org',
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
                'db' => 'bb.uid',
                'width' => 25,              
				'hidden' => false,
                'encode' => false,
                'null' => 'Не указано',
				'sortable' => false,
				'replace' => $uid_types,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types, 'Все'),
                ),
                'search_op' => 'equal',
            ),  

			// 'uid2' => array(
			// 	'db' => 'bbu.uid',
			// 	'label' => 'Наличие',
			// 	'width' => 15,
			// 	'hidden' => false,
			// 	'search_op' => 'like'
            // ),	

			'participants' => array(			
				'label' => 'Участники',
                'db' => 'bbu.uid',
                'width' => 25,              
				'hidden' => false,
                'encode' => false,
                'null' => 'Не указано',
				'sortable' => false,
                'stype' => 'select',
                'searchoptions' => array(
                    'value' => new jqGrid_Data_Value($uid_types2, 'Все'),
                ),
                'search_op' => 'like',
            ),  
	
			'status' => array(
				'label' => $lang['status'],
				'width' => 15,
				'align' => 'center',
				'db' => 'bb.status',
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

			// 'archive' => array(
			// 	'label' => 'Архив',
			// 	'width' => 15,
			// 	'align' => 'center',
			// 	'db' => 'bb.archive',
			// 	'hidden' => false,
			// 	'encode' => false,
			// 	'sortable' => false,
			// 	'replace' => $auto_types_archive,
            //     'stype' => 'select',
            //     'searchoptions' => array(
            //         'defaultValue' => '0',
            //         'value' => new jqGrid_Data_Value($auto_types_archive_select, 'Все'),
            //     ),
            //     'search_op' => 'equal',							
			// 	'excel_hidden' => true,
            // ),

            'technical_solutions_engineer' => array(
				'label' => 'Инженер технических решений',
                'manual' => true,
				'encode' => false,
				'sortable' => false,			
                'search_op' => false,
				'search' => false,
                'title' => false,
                'hidden' => true,
				'excel_only' => true,
            ),	

			'ab' => array(
				'label' => 'Выберите действие',
				'width' => 25,
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
		$rows = array();	
		$rows_select[1] = 'Не выполнено';
		$rows_select[2] = 'Частично выполнено';
		$rows_select[3] = 'Выполнено';
		
		$rows[1] = "<span class='far fa-circle text-danger' title=".$rows_select[1]."></span>";
		$rows[2] = "<span class='far fa-circle text-warning' title=".$rows_select[2]."></span>";
		$rows[3] = "<span class='far fa-circle text-success' title=".$rows_select[3]."></span>";

        return ($select ?  $rows_select :  $rows);
    }
    
	protected function getAutoTypesArchive($select = false)
    {       
		$rows_select = array();	
		$rows = array();	
		$rows_select[0] = "В работе";
		$rows_select[1] = "В архиве";
		
		$rows[0] = "<span title=".$rows_select[0]." class='far fa-dot-circle text-success'></span>";
		$rows[1] = "<span title=".$rows_select[1]." class='far fa-dot-circle text-warning'></span>";

        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getPayTypes($select = false)
    {       
        $rows_select = array();	
		$rows_select[0] = 'Не оплачена';
		$rows_select[1] = 'Частично оплачена';
		$rows_select[2] = 'Оплачена';
		
		$rows = array();	
		$rows[0] = "<span style='margin-left: -12px;' class='fa fa-rub text-danger fa-md' data-toggle='tooltip' title='Не оплачена'></span>";
		$rows[1] = "<span style='margin-left: -12px;' class='fa fa-rub text-warning fa-md' data-toggle='tooltip' title='Частично оплачена'></span>";     
        $rows[2] = "<span style='margin-left: -12px;' class='fa fa-rub text-success fa-md' data-toggle='tooltip' title='Оплачена'></span>";    
		
        return ($select ?  $rows_select :  $rows);
    }
	
	protected function getUidTypes()
    {
		
        $result = $this->DB->query("SELECT u.* FROM ".DB_PREFIX."_boiler_bid as bb LEFT JOIN JMY_users as u on (bb.uid=u.id) ORDER BY u.surname");
        $rows = array();
		$rows[0] = 'Не указан';
        while($r = $this->DB->fetch($result))
        {	
            $rows[$r['id']] = self::fio_short($r);
        }
        return $rows;
    }

    static public function fio_short($r){
        $r = is_object($r) ? $r : (object)$r;
        $result_row = $r->surname.' ';
        $result_row .= mb_substr($r->name, 0, 1) . '. ';
        $result_row .= mb_substr($r->ochestvo, 0, 1) . '.';
        return $result_row;
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

    protected function parseRow($r)
    {
        global $org_array, $lang, $db; 
        
        require_once(ROOT."usr/modules/boiler/admin/sub/bid.php");
	
		$r['oid'] = empty($r['oid_real']) ? 'Не указано' : '<a href="/administration/module/organizations/edit/'.$r['oid_real'].'" target="_blank">'.$r['oid'].'</a>';
		$r['object'] = empty($r['object_id']) ? '<font color="red">Не указано</font>' : '<a href="'.ADMIN.'/object/view/'.$r['object_id'].'" target="_blank">'.$r['object'].'</a>';
		//$r['title'] = empty($r['title']) ? '<font color="red">Не указано</font>' : $r['title'];
		
		$r['ab'] = '<div class="btn-group">
						<button type="button" onclick="location.href = \'/administration/module/boiler/bid/detail/' . $r['id'] . '\'" class="btn btn-xs btn-primary">Просмотр</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">Действие</span></button>
						<ul role="menu" class="dropdown-menu pull-right">		
                            <li>
                                <a href="javascript:void(0)" onclick="archive_modal('.($r["archive_real"] == 0 ? '1' : '0').', '.$r["id"].')">
                                    '.($r["archive_real"] == 0 ? 'Перенести в архив' : 'Перенести в работу').'
                                </a>
                            </li>  
						</ul>
					</div>
					';	

        if(is_date($r["date_create"], "Y-m-d H:i:s")) $r["date_create"] = date("d.m.Y", strtotime($r["date_create"]));

        $r["technical_solutions_engineer"] = 'Не указан';
        $query = $db->query("SELECT * FROM JMY_boiler_bid_users WHERE bid_id = '".$r["id"]."' AND type = '3'");
        if($db->numRows($query) > 0){
            $res = [];
            while($p = $db->getRow($query)){
                $res[] = shortUserName($p["uid"]);
            }
            $r["technical_solutions_engineer"] = implode(", ", $res);
        }

        $r["first_type"] = "Не указано";
        $query = bid_type_query($r["id"], ["limit" => 1]);
        if($db->numRows($query) > 0){
            while($btq = $db->getRow($query)){
                $r["first_type"] = list_type_group_name($btq["section"]);
            }
        }

        if(empty($r["participants"])){
            $r["participants"] = "Не указано";
        } else {
            $participants = explode(",", $r["participants"]);
            $pp = [];
            foreach($participants as $p){
                if(!empty($p)) $pp[] = $this->uid_types[ $p ];
            } 
            $r["participants"] = implode(", ", $pp);
        }
    
        return $r;
    }
	
	 protected function opDialog()
    {
      echo 'r';
    }
}