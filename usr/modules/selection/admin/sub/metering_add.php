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

function metering_add($cat = null)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;		
	$lnn = 'Добавить расход узла учета';
	$adminTpl->admin_head($lang['sales'].' | '.$lnn);
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>			
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/selection/metering_save" method="post" name="cats" role="form" class="form-horizontal parsley-form">
					<div class="form-group">
						<label for="scheme" class="col-lg-3 control-label">Схема </label>
						<div class="col-lg-4">
								<label class="field select">
								<select class="form-control" name="scheme" id="type">';
									$array = getTypeMetringScheme();
									foreach($array as $key => $val){
										echo '<option value="'.$key.'">'.$val.'</option>';
									}
									echo '</select><i class="arrow double"></i>
									
								</label>
						</div>
					</div>					
					<div class="form-group" style="display: none;">
						<label for="type"  class="col-lg-3 control-label">Минимальный расход</label>
						<div class="col-lg-4">
							<label for="yt" class="field prepend-icon">
								<input  type="text" name="r_min" id="r_min" placeholder="Введите минимальный расход" class="gui-input" value="0">
								<label for="name" class="field-icon"><i class="fa fa-pencil"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group" style="display: none;">
						<label for="type"  class="col-lg-3 control-label">Максимальный расход</label>
						<div class="col-lg-4">
							<label for="yt" class="field prepend-icon">
								<input  type="text" name="r_max" id="r_max" placeholder="Введите максимальный расход" class="gui-input" value="0">
								<label for="name" class="field-icon"><i class="fa fa-pencil"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group" style="display: none;" id="warningCode">
					<div class="col-lg-3"></div>
					<div style="color: red; font-weight: bold;" class="col-lg-4">Такой диапазон существует существует!</div>
					</div>
					<div id="t2" class="form-group">
						<label for="type" class="col-lg-3 control-label">Тип </label>
						<div class="col-lg-4">
								<label class="field select">
									<select class="form-control" name="type" id="type">';
									$array = getTypeMetring();
									foreach($array as $key => $val){
										echo '<option value="'.$key.'">'.$val.'</option>';
									}
									echo '</select><i class="arrow double"></i>
								</label>
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
							<input type="submit" class="btn btn-success btn-parsley" id="sub" value="Создать расход" />
						</div>
					</div>
				</form>
			</div>
		</div>';
		//$adminTpl->footIncludes[] = '<script src="/usr/modules/selection/admin/assets/js/metering_add.js"></script>';
	$adminTpl->admin_foot();
}

function metering_save() {
global $adminTpl, $db, $core, $lang;

	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	$date = date("Y-m-d H:i:s");		
	$r_min = $_POST["r_min"];		
	$r_max = $_POST["r_max"];		
	$type = $_POST["type"];		
	$scheme = $_POST["scheme"];		
	$open = $_POST["open"];
	$uid = $core->auth->user_info['id'];

	echo '<div id="content" class="animated fadeIn">';	
		if($db->query("INSERT INTO `" . DB_PREFIX . "_metering` (r_min, r_max, type, scheme, uid, date, status) VALUES ('".$r_min."', '".$r_max."', '".$type."', '".$scheme."', '".$uid."', '".$date."', '1')"))
		{	
			$orid = $db->insert_id();				
			if ($open != '1')
			{				
				$adminTpl->info('Расход успешно добавлен!', 'info', null, $lang['info'], 'Список расходов', ADMIN.'/module/metering');				
			}
			else
			{
				header('Location: /'.ADMIN.'/module/selection/metering_detail/'.$orid);
			}
		}	
	echo '</div>';
	$adminTpl->admin_foot();
}