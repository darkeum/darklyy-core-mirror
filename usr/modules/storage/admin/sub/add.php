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
	$yt = '';
	$lnn = 'Добавление новой заявки';
	$adminTpl->admin_head($lang['sales'].' | '.$lnn);
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
	$adminTpl->js_code[] = '$(".select2-single").select2();';	
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>			
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/storage/save" method="post" name="cats" role="form" class="form-horizontal parsley-form">	
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Склад назначения</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">
								<label for="stock_in" class="field prepend-icon">
									<select name="stock_in" class="select2-single gui-input">';
										foreach(\Stocks\Models\Stock::all() as $stock){
											$selected = $stock->id == 1 ? "selected" : "";
											echo "<option value=\"{$stock->id}\" {$selected}>{$stock->name}</option>";
										}
									echo '</select>						
									<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
								</label>
							</div>	
						</div>	
					</div>						
					<div class="form-group">
						<label class="col-sm-3 control-label">Открыть после создания?</label>
						<div class="col-sm-4">
							'.radio("open",  '1').'														
						</div>	
					</div>		
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-success btn-parsley" id="sub" value="Добавить заявку" />
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
	$uid = $core->auth->user_info['id'];

	$open= isset($_POST['open']) ? $_POST['open'] : false;	
	$stock_in= isset($_POST['stock_in']) ? $_POST['stock_in'] : false;	
	$date_add = date('Y-m-d H:i:s', time());
	echo '<div id="content" class="animated fadeIn">';	
	if(true)
	{
		if($db->query("INSERT INTO `" . DB_PREFIX . "_storage` ( `id` , `uid` , `stock_id` , `type` ,`date` , `status`) VALUES (NULL, '" . $uid . "', '".$stock_in."', '3', '" . $date_add . "', '1');"))
		{	
			
			$orid = $db->insert_id();				
			if ($open != '1')
			{				
				$adminTpl->info('Заявка успешно добавлена!', 'info', null, $lang['info'], 'Список заявок', ADMIN.'/module/storage');				
			}
			else
			{
				header('Location: /'.ADMIN.'/module/storage/detail/'.$orid);
			}
		}	
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
}