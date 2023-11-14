<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}


$dir = ROOT.'/usr/modules/verification/admin/sub/';  
includeSub($dir);

   
$dn_array= array();
$dn_array[0] = 'Нет';
$dn_array[1] = 'Ду 15';
$dn_array[2] = 'Ду 20';
$dn_array[3] = 'Ду 25';
$dn_array[4] = 'Ду 32';
$dn_array[5] = 'Ду 40';
$dn_array[6] = 'Ду 50';
$dn_array[7] = 'Ду 65';
$dn_array[8] = 'Ду 80';
$dn_array[9] = 'Ду 100';
$dn_array[10] = 'Ду 125';
$dn_array[11] = 'Ду 150';
$dn_array[12] = 'Ду 200';
$dn_array[13] = 'Ду 10';


function add($cat = null)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$date = gmdate('d.m.Y H:i'); 
	$dateend = gmdate('d.m.Y H:i'); 
	$task = false;
	$toid = false;
	$endsw = false;		
	$lnn = 'Создание новой заявки';
	$select_setting = [
		"mod" => "jsAdminTpl",
        "name" => "oid",
        "ajaxUrl" => "getOrganizations",
        "return" => true,
	];
	oSelectCustom($select_setting);
	$select_setting["mod"] = "select";
	$select = oSelectCustom($select_setting);
	$adminTpl->admin_head($lang['cats'].' | '.$lnn);
	$validation_array = array(
		'date' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		),
		'oid' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		)
	);
	validationInit($validation_array);
	datetimepickerInit('date');
	echo  '
	<script>
function updateVerDoc(mod, id)
{
	AJAXEngine.setPostVar(\'orglist\', mod);
	AJAXEngine.sendRequest(\'ajax.php?do=getVerDoc\', id);
}
$(function(){
	$("#oid").on("change", function(){
		updateVerDoc(this.value, "doc");
	})
})

</script>';
	echo ajaxorgadd();
	$adminTpl->js_code[] = '$(".select2-single").select2();';	
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/verification/save" method="post" name="cats" role="form" class="form-horizontal parsley-form">				
					<div class="form-group">
						<label for="toid"  class="col-lg-3 control-label">Контрагент</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">';
								// echo '<label for="toid" class="field prepend-icon">
								// 	<select name="oid" class="select2-single gui-input" id="orglist" onchange="updateVerDoc(this.value, \'doc\');">
								// 	<option value="0" disabled selected>Выберите контрагента</option>
								// 	</select>								
								// 	<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
								// </label>';
								echo $select;
								echo '<button  onclick="modal_o(\'#modal-add-org\');" type="button" class="button"><i class="fa fa-plus"></i></button>
							</div>	
						</div>	
					</div>		
					<div class="form-group">
						<label for="type"  class="col-lg-3 control-label">Выберите договор (если есть)</label>
						<div class="col-lg-4">
								<label class="field select">
									<select class="form-control" name="doc" id="doc" >
										<option value="0" selected>Договор не выбран</option>	
								</select><i class="arrow double"></i>
								</label>
						</div>
					</div>	
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Дата заявки</label>
						<div class="col-lg-4">
							<label for="date" class="field prepend-picker-icon">
								<input id="date" type="text" name="date" placeholder="Введите дату" class="gui-input" value="'.$date.'">
							</label>
						</div>	
					</div>	
					<div class="form-group">
						<label class="col-sm-3 control-label">Индивидуальные цены (если есть)?</label>
						<div class="col-sm-4">
							'.radio("iprice", '1').'														
						</div>	
					</div>		
					<div class="form-group">
						<label class="col-sm-3 control-label">Открыть заявку после сохранения?</label>
						<div class="col-sm-4">
							'.radio("open",  '1').'														
						</div>	
					</div>		
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-success btn-parsley" id="sub" value="Создать заявку" />
						</div>
					</div>
				</form>
			</div>
		</div>';
	$adminTpl->admin_foot();
}

function add_doc($cat = null)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$date = gmdate('d.m.Y H:i'); 
	$dateend = gmdate('d.m.Y H:i'); 
	$task = false;
	$toid = false;
	$endsw = false;		
	$lnn = 'Создание нового договора';
	$adminTpl->admin_head($lang['cats'].' | '.$lnn);
	$validation_array = array(
		'date' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		),
		'oid' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		)
	);
	validationInit($validation_array);
	datetimepickerInit('date');
	echo ajaxorgadd();
	$adminTpl->js_code[] = '$(".select2-single").select2();';	
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/verification/doc_save" method="post" name="cats" role="form" class="form-horizontal parsley-form">				
					<div class="form-group">
						<label for="toid"  class="col-lg-3 control-label">Контрагент</label>
						<div class="col-lg-3">
							<div class="smart-widget sm-right smr-50">
								<label for="toid" class="field prepend-icon">
									<select name="oid" class="select2-single gui-input" id="orglist">
									<option value="0" disabled selected>Выберите контрагента</option>
									</select>								
									<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
								</label>
								<button  onclick="modal_o(\'#modal-add-org\');" type="button" class="button"><i class="fa fa-plus"></i></button>
							</div>	
						</div>	
					</div>						
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Дата заключения договора</label>
						<div class="col-lg-3">
							<label for="date" class="field prepend-picker-icon">
								<input id="date" type="text" name="date" placeholder="Введите дату" class="gui-input" value="'.$date.'">
							</label>
						</div>	
					</div>						
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-success btn-parsley" id="sub" value="Создать договор" />
						</div>
					</div>
				</form>
			</div>
		</div>';
	$adminTpl->admin_foot();
}

function save() {
global $adminTpl, $db, $core, $lang;

	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	$ttime = time();
	$date = !empty($_POST['date']) ? filter($_POST['date']) : $ttime;	
	$oid= isset($_POST['oid']) ? intval($_POST['oid']) : false;		
	$doc= isset($_POST['doc']) ? intval($_POST['doc']) : '';	
	$iprice= isset($_POST['iprice']) ? $_POST['iprice'] : '0';	
	$open= isset($_POST['open']) ? $_POST['open'] : false;	
	$uid = $core->auth->user_info['id'];
	// if($date != $ttime)
	// {
	// 	$parseDate = explode(' ', $date);
	// 	$subDate = explode('.', $parseDate[0]);
	// 	if(isset($parseDate[1]))
	// 	{
	// 		$subTime = explode(':', $parseDate[1]);
	// 	}
	// 	else
	// 	{
	// 		$subTime[0] = 12;
	// 		$subTime[1] = 0;
	// 	}
	// 	$date = gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
	// }
	$add = [
		"uid" => $uid,
		"date" => $date,
		"oid" => $oid,
		"doc" => $doc,
		"iprice" => $iprice,		
	];
	
	echo '<div id="content" class="animated fadeIn">';	
	if(!empty($date)&&!empty($oid))
	{
		$Ver = new \Verification\Ver;
		$orid = $Ver->add($add);
		// if($db->query("INSERT INTO `" . DB_PREFIX . "_ver` ( `id` , `uid` , `oid` ,  `iprice` , `date` , `date_end`, `status`) VALUES (NULL, '" . $uid . "', '" . $oid . "', '" . $iprice . "', '" . $date . "', '0', '1');"))
		// {	
			// $orid = $db->insert_id();	
			
			// $secret_key	= md5($date+$orid);
			// $db->query("UPDATE `" . DB_PREFIX . "_ver` SET `secret_key` = '".$db->safesql($secret_key)."' WHERE `id` = " . $orid . " LIMIT 1 ;");
			// if (!empty($doc))
			// {
			// 	$query_n = $db->query("SELECT id, oid, date FROM ".DB_PREFIX."_reestr WHERE org = '2' AND id='".$db->safesql($doc)."' LIMIT 1");
			// 	$rn = $db->getRow($query_n);
			// 	if (($rn['oid'] == $oid)&&($rn['date']<$date))
			// 	{
			// 		$db->query("INSERT INTO `" . DB_PREFIX . "_relations` ( `id` , `module_out` , `pid_out` ,  `module_in` , `pid_in` , `date` , `uid` , `status`) VALUES (NULL, 'verification', '" . $orid . "', 'reestr', '" . $db->safesql($doc) . "', '". time() ."', '" . $uid . "', '1');");
			// 	}
			// }
			if ($open != '1')
			{				
				$adminTpl->info('Заявка успешно добавлена!', 'info', null, $lang['info'], 'Список заявок', ADMIN.'/module/verification');				
			}
			else
			{
				header('Location: /'.ADMIN.'/module/verification/order/'.$orid);
			}
		// }	
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}

function doc_save() {
global $adminTpl, $db, $core, $lang;

	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	$ttime = time();
	$date = !empty($_POST['date']) ? filter($_POST['date']) : $ttime;	
	$oid= isset($_POST['oid']) ? intval($_POST['oid']) : false;			
	$uid = $core->auth->user_info['id'];
	if($date != $ttime)
	{
		$parseDate = explode(' ', $date);
		$subDate = explode('.', $parseDate[0]);
		if(isset($parseDate[1]))
		{
			$subTime = explode(':', $parseDate[1]);
		}
		else
		{
			$subTime[0] = 12;
			$subTime[1] = 0;
		}
		$date = gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
	}
	
	$adminTpl->admin_foot();
}

function report2(){
    global $adminTpl, $db, $core, $lang;
    $adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
    if(!isset($_POST['turn']))
    {
    $date_end = date('d.m.Y H:i');
	$date_start =  date('d.m.Y H:i', strtotime('-1 month'));
    $adminTpl->js_code[] = ' // Init Boostrap Multiselects
      
        $(".select2-single").select2();'; 
    echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">Отчёты и аналитика</span>					
			</div>
			<div class="panel-body admin-form">		
			<form id="admin-form" action="" method="post" role="form" class="form-horizontal">
			<div data-animate="1000" class="row mb20">
              <div class="col-md-4">              
				<label for="date_start" class="field prepend-picker-icon">
					<input id="date_start" type="text" name="date_start" placeholder="" class="gui-input" value="'.$date_start.'">
				</label>			  
              </div>
              <div class="col-md-4">               
			    <label for="date_end" class="field prepend-picker-icon">
					<input id="date_end" type="text" name="date_end" placeholder="" class="gui-input" value="'.$date_end.'">
				</label>			   
              </div>
              <div class="col-md-4">               
					<label for="section" class="field" id="cc">
							<select name="section[]" class="select2-single form-control" multiple="multiple" >';
											$query_section_list = $db->query("SELECT * FROM ".DB_PREFIX."_ver as d LEFT JOIN ".DB_PREFIX."_organizations AS o on(d.oid = o.id)");
                                            $orgName = [];
														if($db->numRows($query_section_list) > 0) 
														{
															while($section_list = $db->getRow($query_section_list)) 
															{
															if($section_array) $selected = in_array($section_list['id'], $section_array) ? "selected" : "";
                                                            if(empty($orgName[$section_list['name']])){
                                                                echo '<option value="'.$section_list['id'].'" '.$selected.'>'.$section_list['name'].'</option>';
                                                                 $orgName[$section_list['name']] = $section_list['name']; 
                                                            }
																
															}
														}                                                      
                      echo     ' </select>			   
              </div>
            </div>
			  <center>   
			  <input  name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="Составить отчёт" />
              <input  name="turn" type="text" value="next" hidden>
			  </center>
			</form>
		</div>	
	</div>';
    datetimepickerInit('date_start');
    datetimepickerInit('date_end');
    } else 
    {	echo '<div id="content" class="animated fadeIn">';
        $querySec = '';
        if(isset($_POST['section'])){
            $sec = $_POST['section'];
            $sec = implode(',', $sec);
            $querySec = ' AND oid IN ('.$sec.')';
        }
        $query = $db->query("SELECT total, status FROM ".DB_PREFIX."_ver WHERE `date` BETWEEN ".datetostamp($_POST['date_start'])." AND ".datetostamp($_POST['date_end']).$querySec." AND status <> 0");
        $allSum = 0;
        $allSum1 = 0;
        while($catalog = $db->getRow($query))
        {
            $allSum += $catalog['total'];
            if($catalog['status'] == 3) $allSum1 += $catalog['total'];
        }
        
        $query = $db->query("SELECT d.status FROM ".DB_PREFIX."_ver_detail as d LEFT JOIN ".DB_PREFIX."_ver AS o on(d.vid = o.id) WHERE `date` BETWEEN ".datetostamp($_POST['date_start'])." AND ".datetostamp($_POST['date_end']).$querySec." AND d.status <> 0");
        $allCount = 0;
        $allCount1 = 0;
        while($catalog = $db->getRow($query))
        {
            $allCount++;
            if($catalog['status'] == 2) $allCount1++;
        }
        echo '
        <div class="row">
              <div class="col-sm-4 col-xl-3">
                <div class="panel panel-tile text-center br-a">
                  <div class="panel-body">
                    <h1 class="fs30 mt5 mbn">'.$allSum.' р.</h1>
                    <h6 class="text-system" style="text-transform: uppercase;">Сумма заказов</h6>
                  </div>   
				<div class="panel-footer br-t p12" style="text-transform: uppercase;"><span class="fs11">За период от '.$_POST['date_start'].' по '.$_POST['date_end'].'</span></div>				  
                </div>
              </div>
              <div class="col-sm-4 col-xl-3">
                <div class="panel panel-tile text-center br-a">
                  <div class="panel-body">
                    <h1 class="fs30 mt5 mbn">'.$allSum1.' р.</h1>
                    <h6 class="text-success" style="text-transform: uppercase;">Сумма выполеннных заказов</h6>
                  </div>
                  <div class="panel-footer br-t p12" style="text-transform: uppercase;"><span class="fs11">За период от '.$_POST['date_start'].' по '.$_POST['date_end'].'</span></div>
                </div>
              </div>
              <div class="col-sm-4 col-xl-3">
                <div class="panel panel-tile text-center br-a">
                  <div class="panel-body">
                    <h1 class="fs30 mt5 mbn">'.$allCount.' шт.</h1>
                    <h6 class="text-warning" style="text-transform: uppercase;">Количество приборов</h6>
                  </div>
                  <div class="panel-footer br-t p12" style="text-transform: uppercase;"><span class="fs11">За период от '.$_POST['date_start'].' по '.$_POST['date_end'].'</span></div>
                </div>
              </div> 
              <div class="col-sm-4 col-xl-3">
                <div class="panel panel-tile text-center br-a">
                  <div class="panel-body">
                    <h1 class="fs30 mt5 mbn">'.$allCount1.' шт.</h1>
                    <h6 class="text-info" style="text-transform: uppercase;">Количество проверенных приборов</h6>
                  </div>
                  <div class="panel-footer br-t p12" style="text-transform: uppercase;"><span class="fs11">За период от '.$_POST['date_start'].' по '.$_POST['date_end'].'</span></div>
                </div>
              </div>  
            </div>
        <div class="row">
            <div class="col-md-12">
                <div id="sum-pie"></div>
            </div>
        </div>
        <br/>
        <div class="row">
                <div class="col-md-6">
                    <div id="sum-pie1" style="height: 300px"></div>
                </div>
                <div class="col-md-6">
                    <div id="sum-pie2" style="height: 300px"></div>
                </div>
        </div>
        ';
        
        $masName = [];
        $masName1 = [];
        $masName2 = [];
               
        $HighCharts_array = array(	
        'Mas' => array(
            'id' =>  'sum-pie',
			'type' => 'pies',
			'name' =>  'Сумма',
            'title' => 'Контрагенты',
			'data' => array()
        ),
        'Mas1' => array(
            'id' =>  'sum-pie1',
			'type' => 'pies',
			'name' =>  'Сумма2',
            'title' => 'Приборы (по сумме)',
			'data' => array()
        ),
        'Mas2' => array(
            'id' =>  'sum-pie2',
			'type' => 'pies',
			'name' =>  'Сумма3',
            'title' => 'Приборы (по количеству)',
			'data' => array()
        ));
        $query = $db->query("SELECT d.*, o.name as oname  FROM ".DB_PREFIX."_ver as d LEFT JOIN ".DB_PREFIX."_organizations AS o on(d.oid=o.id) WHERE `date` BETWEEN ".datetostamp($_POST['date_start'])." AND ".datetostamp($_POST['date_end']).$querySec." AND d.status <> 0");
        while($catalog = $db->getRow($query))
        {
            if(empty($masName[$catalog['oname']])){
                $masName[$catalog['oname']] = $catalog['total'];
            } else {
                $masName[$catalog['oname']] += $catalog['total'];
            }
            
        }
        foreach($masName as $key => $value){
            $HighCharts_array['Mas']['data'][] = [htmlspecialchars_decode($key).' ('.$value.' р.)', $value, 'true', 'true'];
        } 
        
        $query = $db->query("SELECT d.sum, d.stable_name, d.stable_price, d.status FROM ".DB_PREFIX."_ver_detail as d LEFT JOIN ".DB_PREFIX."_ver AS o on(d.vid = o.id) WHERE `date` BETWEEN ".datetostamp($_POST['date_start'])." AND ".datetostamp($_POST['date_end']).$querySec." AND d.status <> 0");
        while($catalog = $db->getRow($query))
        {
            if(empty($masName1[$catalog['stable_name']])){
                $masName1[$catalog['stable_name']] = $catalog['stable_price'] * $catalog['sum'];
            } else {
                $masName1[$catalog['stable_name']] += $catalog['stable_price'] * $catalog['sum'];
            }
        }
        foreach($masName1 as $key => $value){
            $HighCharts_array['Mas1']['data'][] = [htmlspecialchars_decode($key).' ('.$value.' р.)', $value, 'true', 'true'];
        }
        
        $query = $db->query("SELECT d.sum, d.stable_name, d.stable_price, d.status FROM ".DB_PREFIX."_ver_detail as d LEFT JOIN ".DB_PREFIX."_ver AS o on(d.vid = o.id) WHERE `date` BETWEEN ".datetostamp($_POST['date_start'])." AND ".datetostamp($_POST['date_end']).$querySec." AND d.status <> 0");
        while($catalog = $db->getRow($query))
        {
            if(empty($masName2[$catalog['stable_name']])){
                $masName2[$catalog['stable_name']] = $catalog['sum'];
            } else {
                $masName2[$catalog['stable_name']] += $catalog['sum'];
            }
        }
        foreach($masName2 as $key => $value){
            $HighCharts_array['Mas2']['data'][] = [htmlspecialchars_decode($key).' ('.$value.' шт.)', $value, 'true', 'true'];
        }
        /*
		'Чаша' => array(
			'id' =>  'high-pie',
			'type' => 'pies',
			'name' =>  'Предпочтение по чашам',
			'data' =>  array(array('Фруктовая чаша ('.$fruct.')', $fruct,'false','false'), array('Глиняная чаша ('.$glina.')',$glina,'true','true'))
		),		
		'Табак обшее' => array(
			'id' =>  'high-tabac',
			'type' => 'pies',
			'name' =>  'Предпочтение по табаку',
			'data' =>  array(array('Soft ('.($glina_tabac[1]+$fruct_tabac[1]).')', ($glina_tabac[1]+$fruct_tabac[1]),'false','false'), array('Medium ('.($glina_tabac[2]+$fruct_tabac[2]).')',($glina_tabac[2]+$fruct_tabac[2]),'true','true'),array('Premium ('.($glina_tabac[3]+$fruct_tabac[3]).')', ($glina_tabac[3]+$fruct_tabac[3]),'false','false'),array('Без табака ('.($glina_tabac[4]+$fruct_tabac[4]).')', ($glina_tabac[4]+$fruct_tabac[4]),'false','false'))
		),	
		'Круги' => array(
			'id' =>  'high-pie',
			'type' => 'circulars'
		)
    */
    

	HighChartsInit($HighCharts_array);
    }
    echo '</div>';
    $adminTpl->admin_foot();
}


switch(isset($url[3]) ? $url[3] : null) {
	default:
		content_main();
	break;
	
	case "add":
		add();
	break;
	
	case "add_doc":
		add_doc();
	break;
	
	case "order":		
		$id = intval($url[4]);
		order($id);
	break;

	case "order2":		
		$id = intval($url[4]);
		order2($id);
	break;
	
	case "device":
		content_device();
	break;
	
	case "save":
		save();
	break;
    
    case "report":
        report2();
    break;
    
    case "search_device":
        search_device();
    break;
    
    case "searchList":
        if(isset($_POST["search"])){
            $search = $_POST["search"];
            if(mb_strlen( $search) > 0){
                $searchMas = explode(" ", $search);
                $str = "";
                foreach($searchMas as $val){
                    $str .= "number LIKE '%".$val."%' AND ";
                }
                $query = $db->query("SELECT * FROM JMY_ver_detail WHERE ".mb_substr($str, 0, -4)." LIMIT 50");
                if($db->numRows($query) > 0){
                    echo '<ul>';
                    while($r = $db->getRow($query)){
                        echo '<li><a href="/administration/module/verification/order/'.$r["vid"].'" target="_blank">'.$r["stable_name"].' (Заявка ПР-'.$r["vid"].')</a></li>';
                        }
                    echo '</ul>';
                } else echo "Ничего не найдено!";
            } else echo "";
        }
    break;
	
	case "edit":
		$id = intval($url[4]);
		content_add($id);
	break;
	
	case "delete":
		$id = intval($url[4]);
		delete($id);
		header('Location: /'.ADMIN.'/module/content');
	break;
	
	case "activate":
		$id = intval($url[4]);
		activate($id);
		header('Location: /'.ADMIN.'/module/content');
	break;	
	
	case "doc_create":
		$id = intval($url[4]);
		doc_create($id);		
	break;	
	
	case "nz_create":
		$id = intval($url[4]);
		nz_create($id);		
	break;	

	case "action":
		$type = $_POST['act'];
		if(is_array($_POST['checks'])) {
			switch($type) {
				case "activate":
					foreach($_POST['checks'] as $id) {
						activate(intval($id));
					}
					break;			
				
				case "deActivate":
					foreach($_POST['checks'] as $id) {
						$db->query("UPDATE `" . DB_PREFIX . "_content` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
					}
					break;			
					
				case "reActivate":
					foreach($_POST['checks'] as $id) {
						$db->query("UPDATE `" . DB_PREFIX . "_content` SET `active` = NOT `active` WHERE `id` = " . $id . " LIMIT 1 ;");
					}
					break;
				
				case "delete":
					foreach($_POST['checks'] as $id) {
						delete(intval($id));
					}
					break;
			}
		}
		header('Location: /'.ADMIN.'/module/verification');
	break;
	
	case 'ajax':
		global $adminTpl, $db, $config;
			ajaxInit();
			$type = $url[4];			
			switch($type) 
			{				
				case "order_list":
					ajax_order_list();
				break;	
				case "order_divece_add":					
					ajax_order_device_add();
				break;	
				case "order_divece_list":
					$vid = intval($url[5]);
					ajax_order_device_list($vid);
				break;	
				case "order_divece_compl":					
					ajax_order_device_compl();
				break;	
				case "order_divece_del":					
					ajax_order_device_del();
				break;	
				case "divece_list":
					ajax_device_list();
				break;
				case "divece_add":
					ajax_device_add();
				break;
				case "device_edit":
					ajax_device_edit();
				break;
                
                case "order_device_edit":
					ajax_order_device_edit();
				break;
                case "order_device_check":
					ajax_device_check();
				break;
                case "order_del":
                $vid = intval($url[5]);
					ajax_order_del($vid);
				break;
			}
	break;	
}
