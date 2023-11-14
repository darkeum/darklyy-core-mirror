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

function metering_scheme_add($cat = null)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;		
	$lnn = 'Добавить схему узла учета';
	$adminTpl->admin_head($lang['sales'].' | '.$lnn);
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>			
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/selection/metering_scheme_save" method="post" name="cats" role="form" class="form-horizontal parsley-form">
					<div class="form-group">
						<label for="type"  class="col-lg-3 control-label">Наименование</label>
						<div class="col-lg-4">
							<label for="yt" class="field prepend-icon">
								<input  type="text" name="title" id="title" placeholder="Введите наименование" class="gui-input" value="">
								<label for="name" class="field-icon"><i class="fa fa-pencil"></i></label>
							</label>
						</div>
					</div>					
					<div class="form-group" style="display: none;" id="warningCode">
					<div class="col-lg-3"></div>
					<div style="color: red; font-weight: bold;" class="col-lg-4">Такой диапазон существует существует!</div>
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
							<input type="submit" class="btn btn-success btn-parsley" id="sub" value="Создать схему" />
						</div>
					</div>
				</form>
			</div>
		</div>';
		//$adminTpl->footIncludes[] = '<script src="/usr/modules/selection/admin/assets/js/metering_add.js"></script>';
	$adminTpl->admin_foot();
}

function metering_scheme_save() {
global $adminTpl, $db, $core, $lang;

	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	$date = date("Y-m-d H:i:s");		
	$title = $_POST["title"];	
	$open = $_POST["open"];
	$uid = $core->auth->user_info['id'];

	echo '<div id="content" class="animated fadeIn">';	
		if($db->query("INSERT INTO `" . DB_PREFIX . "_metering_scheme` (title, uid, date, status) VALUES ('".$title."', '".$uid."', '".$date."', '1')"))
		{	
			$orid = $db->insert_id();				
			if ($open != '1')
			{				
				$adminTpl->info('Схема успешно добавлена!', 'info', null, $lang['info'], 'Список схем', ADMIN.'/module/metering_scheme');				
			}
			else
			{
				header('Location: /'.ADMIN.'/module/selection/metering_detail_scheme/'.$orid);
			}
		}	
	echo '</div>';
	$adminTpl->admin_foot();
}