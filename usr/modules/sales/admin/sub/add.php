<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function add($cat = null)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$date = gmdate('d.m.Y H:i'); 	
	$lnn = $lang['sales_adding'];
	$adminTpl->admin_head($lang['sales'].' | '.$lnn);
	$org = 1;
	$validation_array = array(
		'date' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		),
		'oid' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		),
		'type' => array(
			'required' =>  array('true', $lang['cats_name_err'])
		)
	);
	validationInit($validation_array);
	datetimepickerInit('date');	
	echo ajaxorgadd();	
	echo ajaxObjectAdd();	
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>			
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/sales/save" method="post" name="cats" role="form" class="form-horizontal parsley-form">	
					<div class="form-group">
						<label for="type"  class="col-lg-3 control-label">'.$lang['org'].'</label>
						<div class="col-lg-4">
								<label class="field select">
									<select class="form-control" name="org" id="org">';
										$query_o = $db->query("SELECT n.*, o.name FROM ".DB_PREFIX."_org as n LEFT JOIN ".DB_PREFIX."_organizations as o on(o.id=n.oid) ORDER BY id ASC");
										if($db->numRows($query_o) > 0) 
										{
											while($o_list = $db->getRow($query_o)) 
											{
												$selected = ($o_list['id'] == $org) ? "selected" : "";
												echo '<option value="'.$o_list['id'].'" '.$selected.'>'.$o_list['name'].'</option>';
											}
										}		
								echo '	</select><i class="arrow double"></i>
								</label>
						</div>
					</div>
					<div id="t2" class="form-group">
						<label for="sales_type"  class="col-lg-3 control-label">'.$lang['sales_type'].'</label>
						<div class="col-lg-4">
								<label class="field select">
									<select class="form-control" name="sales_type" id="sales_type" onchange="toggle_cat(this);">';
										$lists = \Sales\Support\SalesSupport::sales_type_list_view();
										foreach($lists as $list){
											echo "<option value='{$list["value"]}'>{$list["text"]}</option>";
										}
									echo '</select><i class="arrow double"></i>
								</label>
						</div>
					</div>
					<div class="form-group">
						<label for="toid"  class="col-lg-3 control-label">'.$lang['oid'].'</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">';
								oSelect("jsAdminTpl", "oid", null, "getOrganizations");
								oSelect("select", "oid", null, "getOrganizations");
								echo'<button  onclick="modal_o(\'#modal-add-org\');" type="button" class="button"><i class="fa fa-plus"></i></button>
							</div>	
						</div>	
					</div>	
					<div class="form-group" id="sales_cat-div">
						<label for="sales_cat"  class="col-lg-3 control-label">'.$lang['sales_cat'].'</label>
						<div class="col-lg-4">
								<label class="field select">
									<select class="form-control" name="sales_cat" id="sales_cat">
										<option value="1">'.$lang['sales_cat_std'].'</option>
										<option value="2">'.$lang['sales_cat_new'].'</option>
										<option value="3">'.$lang['sales_cat_spr'].'</option>
									</select><i class="arrow double"></i>
								</label>
						</div>
					</div>
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">'.$lang['date'].'</label>
						<div class="col-lg-4">
							<label for="date" class="field prepend-picker-icon">
								<input id="date" type="text" name="date" placeholder="'.$lang['date'].'" class="gui-input" value="'.$date.'">
							</label>
						</div>	
					</div>			
                    <div class="form-group">
						<label for="object"  class="col-lg-3 control-label">'.$lang['object'].'</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">';
								oSelect("jsAdminTpl", "object", null, "getObjectToSelect");
								oSelect("select", "object", null, "getObjectToSelect");
								echo'<button  onclick="modal_o(\'#modal-add-object\');" type="button" class="button"><i class="fa fa-plus"></i></button>
							</div>	
						</div>	
					</div>
                    <div class="form-group">
						<label for=""  class="col-lg-3 control-label">Источник обращения</label>
						<div class="col-lg-4">
								<select name="source_appeal" class="form-control">';
									echo "<option value='null'>Не указано</option>";
									foreach(\Sales\Model\Sales::getSourceAppeal() as $key => $item){
										echo "<option value='{$key}'>{$item}</option>";
									}
							echo '</select>
						</div>	
					</div>
                    <div class="form-group">
						<label for=""  class="col-lg-3 control-label">Сегмент рынка</label>
						<div class="col-lg-4">
								<select name="market_segment" class="form-control">';
									echo "<option value='null'>Не указано</option>";
									foreach(\Sales\Model\Sales::getMarketSegment() as $key => $item){
										echo "<option value='{$key}'>{$item}</option>";
									}
							echo '</select>
						</div>	
					</div>
                    <div class="form-group">
						<label for=""  class="col-lg-3 control-label">По сфере деятельности</label>
						<div class="col-lg-4">
								<select name="by_field_activity" class="form-control">';
									echo "<option value='null'>Не указано</option>";
									foreach(\Sales\Model\Sales::getByFieldActivity() as $key => $item){
										echo "<option value='{$key}'>{$item}</option>";
									}
							echo '</select>
						</div>	
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="clone_active">Заказ с Атри-энерго</label>
						<div class="col-sm-1">
							<input type="checkbox" name="clone_active" id="clone_active" value="1" class="form-control" style="width: 50px; cursor: pointer;" disabled>						
						</div>	
					</div>		
					<div class="form-group">
						<label class="col-sm-3 control-label">'.$lang['sales_open'].'</label>
						<div class="col-sm-4">
							'.radio("open",  '1').'														
						</div>	
					</div>		
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-success btn-parsley" id="sub" value="'.$lang['sales_add'].'" />
						</div>
					</div>
				</form>
			</div>
		</div>';
		$adminTpl->footIncludes[] = '<script src="/usr/modules/sales/admin/assets/js/add.js?'.time().'"></script>';
	$adminTpl->admin_foot();
}

function save() {
global $adminTpl, $lang;

	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	$date = !empty($_POST['date']) ? filter($_POST['date']) : time();		
	$oid = isset($_POST['oid']) ? intval($_POST['oid']) : false;		
	$open = isset($_POST['open']) ? $_POST['open'] : false;	
	echo '<div id="content" class="animated fadeIn">';	
	if(!empty($date)&&!empty($oid))
	{
			$request = $_POST;
			$request["uid"] = \Boot\Support\Facades\Auth::id();
			$request["source_appeal"] = $request["source_appeal"] == "null" ? null : $request["source_appeal"];
			$request["market_segment"] = $request["market_segment"] == "null" ? null : $request["market_segment"];
			$request["by_field_activity"] = $request["by_field_activity"] == "null" ? null : $request["by_field_activity"];
			$sales = \Sales\Model\Sales::create($request);
			$org_main = 1;
			if(!empty($request["clone_active"]) && $request["org"] != $org_main){
				$org = getOrgInfo($request["org"]);
				$request["org"] = $org_main;
				$request["clone_active"] = 0;
				$request["oid"] = $org["org"]["oid"];
				$request["is_clone"] = 1;
				$clone_sales = \Sales\Model\Sales::create($request);
				$sales->update(["clone" => $clone_sales->id]);
				setcookie("stock_id_view", 1, 0, "/");
			}
			if ($open != '1')
			{				
				$adminTpl->info('Продажа успешно добавлена!', 'info', null, $lang['info'], 'Список продаж', ADMIN.'/module/sales');				
			}
			else
			{
				header('Location: /'.ADMIN.'/module/sales/order/'.$sales->id);
			}	
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}