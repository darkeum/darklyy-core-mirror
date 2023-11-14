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
	$yt = '';
	$lnn = 'Добавить блок';
	$adminTpl->admin_head($lang['sales'].' | '.$lnn);
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>			
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/selection/save" method="post" name="cats" role="form" class="form-horizontal parsley-form">	
					<div class="form-group">
						<label for="type"  class="col-lg-3 control-label">Код блока</label>
						<div class="col-lg-4">
							<label for="yt" class="field prepend-icon">
								<input  type="text" name="code" id="code" placeholder="Введите код блока" class="gui-input" value="'.$yt.'">
								<label for="name" class="field-icon"><i class="fa fa-pencil"></i></label>
							</label>
						</div>
					</div>
					<div class="form-group" style="display: none;" id="warningCode">
					<div class="col-lg-3"></div>
					<div style="color: red; font-weight: bold;" class="col-lg-4">Такой код уже существует!</div>
					</div>
					<div id="t2" class="form-group">
						<label for="type" class="col-lg-3 control-label">Тип блока</label>
						<div class="col-lg-4">
								<label class="field select">
									<select class="form-control" name="type" id="type">';
									$array = getTypeBlock();
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
							<input type="button" class="btn btn-success btn-parsley" id="sub" value="Создать блок" />
						</div>
					</div>
				</form>
			</div>
		</div>';
		$adminTpl->footIncludes[] = '<script src="/usr/modules/selection/admin/assets/js/add.js"></script>';
	$adminTpl->admin_foot();
}

function save() {
global $adminTpl, $db, $core, $lang;

	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
	$date = date("Y-m-d H:i:s");		
	$code = $_POST["code"];		
	$type = $_POST["type"];		
	$open = $_POST["open"];
	$uid = $core->auth->user_info['id'];

	echo '<div id="content" class="animated fadeIn">';	
		if($db->query("INSERT INTO `" . DB_PREFIX . "_selection` (code, type, uid, date, status) VALUES ('".$code."', '".$type."', '".$uid."', '".$date."', '1')"))
		{	
			$orid = $db->insert_id();				
			if ($open != '1')
			{				
				$adminTpl->info('Блок успешно добавлен!', 'info', null, $lang['info'], 'Список продаж', ADMIN.'/module/sales');				
			}
			else
			{
				header('Location: /'.ADMIN.'/module/selection/detail/'.$orid);
			}
		}	
	echo '</div>';
	$adminTpl->admin_foot();
}