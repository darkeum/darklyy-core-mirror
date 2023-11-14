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


function main() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Список заявок на подбор';
	$adminTpl->admin_head('Менеджер склада | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'form';
	$atributs['module_open'] = 'detail';
	$atributs['button'] = 'Добавить заявку';
	$atributs['button_url'] = '/administration/module/form/add';	
	load_grid('jqForm', $lnn, $atributs, ROOT.'/usr/modules/form/admin/grid/');
	$adminTpl->footIncludes[] = '<script src="usr/modules/storage/admin/asset/js/function.js?'.time().'"></script>';	
	$adminTpl->footIncludes[] = '<script src="usr/modules/storage/admin/asset/js/scriptTransferMain.js"></script>';	
	$adminTpl->footIncludes[] = '<script src="/usr/modules/storage/admin/asset/js/storageTransferGrid.js?5646654156564"></script>';
	echo '
	<div class="modal fade" id="removeTransferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Удалить перемещение</h5>
			</div>
			<div class="modal-body">
				Вы действительно хотите удалить перемещение <span class="removeTransferModalNum"></span>?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="removeTransferModalBtn">Удалить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="removeTransferModalError" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Внимание</h5>
			</div>
			<div class="modal-body">
				Прежде чем удалить перемещение <span class="removeTransferModalNum"></span>, отмените заявку	
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>
	';
	$adminTpl->admin_foot();
} 

function form_add($edit_id = null)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	if (empty($edit_id))
	{
		$date = gmdate('d.m.Y H:i'); 
		$object = false; 		
		$lnn = 'Создание новой заявки';
		$rtype = null;
	}
	$adminTpl->admin_head($lang['cats'].' | '.$lnn);
	datetimepickerInit('date');
	$adminTpl->js_code[] = '$(".select2-single").select2();';	
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/storage/transfer/save" method="post" enctype="multipart/form-data" name="cats" role="form" class="form-horizontal parsley-form">
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
					<div class="form-group">
						<label for="object"  class="col-lg-3 control-label">Объект</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">
								<label for="object" class="field prepend-icon">
									<select name="object" class="select2-single gui-input">
									<option value="0" disabled selected>Выберите объект</option>';
									$query_r = $db->query("SELECT id, name FROM ".DB_PREFIX."_object  ORDER BY id ASC");
													if($db->numRows($query_r) > 0) 
														{
															while($r_list = $db->getRow($query_r)) 
															{
																$selected = ($r_list['id'] == $rtype) ? "selected" : "";
																echo '<option value="'.$r_list['id'].'" '.$selected.'>№OB-'.$r_list['id'].' ('.$r_list['name'].')</option>';
															}
														}	
									echo '</select>								
									<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
								</label>
								<button  onclick="modal_o(\'#modal-add-org\');" type="button" class="button"><i class="fa fa-plus"></i></button>
							</div>	
						</div>	
					</div>
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Склад отправления</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">
								<label for="stock_out" class="field prepend-icon">
									<select name="stock_out" class="select2-single gui-input">
									<option value="1" selected>Оптовый</option>
									<option value="2" >Производство</option>
									<option value="5" >ТТ-Энерго</option>
											<option value="69" >Сервис</option>
									<option value="70" >Неликвид</option>
									</select>								
									<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
								</label>
							</div>	
						</div>	
					</div>	
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Склад назначения</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">
								<label for="stock_in" class="field prepend-icon">
									<select name="stock_in" class="select2-single gui-input">
									<option value="1" >Оптовый</option>
									<option value="2" selected>Производство</option>
									<option value="5" >ТТ-Энерго</option>
											<option value="69" >Сервис</option>
									<option value="70" >Неликвид</option>
									</select>						
									<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
								</label>
							</div>	
						</div>	
					</div>	
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Дата перемещения</label>
						<div class="col-lg-4">
							<label for="date" class="field prepend-picker-icon">
								<input id="date" type="text" name="date" placeholder="Введите дату " class="gui-input" value="'.$date.'">
							</label>
						</div>	
					</div>	
					<div class="form-group">
						<label class="col-sm-3 control-label">Комментарий</label>
						<div class="col-sm-4">
							<label for="comment" class="field prepend-icon">
								<textarea name="comment" id="comment" placeholder="" class="gui-textarea"></textarea>
								<label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer">Введите ваш комментарий к закупке!</span>
							</label>
							</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-success btn-parsley" id="sub" value="Создать закупку" />
						</div>
					</div>
				</form>
			</div>
		</div>';
	$adminTpl->admin_foot();
}