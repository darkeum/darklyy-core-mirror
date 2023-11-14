<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @info_ru     Место под ваши собственные функции
* @info_en     Place for your own functions
*/


function ajaxorgadd($soid = 0)
{
	global $lang;
	$return = '
	<script type="text/javascript" language="javascript">
		function get_org(id){
			$.post("'.ADMIN.'/module/organizations/ajax/org_list/'.$soid.'", function(data){
				$("#"+id).html(data);
			})
		}
		get_org("orglist");
		function ajax_send_org() {
			var msg = $("#add-org").serialize();
			$.ajax({
				type: "POST",
				url: "'.ADMIN.'/module/organizations/ajax/org_add",
				data: msg,
				success: function(data) {
					$("#modal-add-org").magnificPopup(\'close\');
					if (data == "1")
					{
						notif("success","'.$lang['info'].'", "Организация успешно добавлена!");	
					}
					if (data == "2")
					{
						notif("warning","'.$lang['info'].'", "Организация уже есть в базе данных!");	
					}
					if (data == "3")
					{
						notif("danger","'.$lang['info'].'", "Что-то пошло не так, возможно заполнены не все поля!");	
					}
					if (data == "4")
					{
						notif("danger","'.$lang['info'].'", "Такая организация не существует");	
					}
					get_org("orglist");
				}  					
			});
		}
	</script>	
	<div id="modal-add-org" class="popup-basic admin-form mfp-with-anim mfp-hide">							
							<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-pencil-square"></i>Добавить организацию</span></div>
								<form id="add-org" action="javascript:void(null);" onsubmit="ajax_send_org()">
									<div class="panel-body p25">
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="inn" name="inn" type="text" placeholder="Введите ИНН" class="gui-input" />
												<label for="text" class="field-icon"><i class="fas fa-money-check"></i></label>
											</label>
										</div>
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="bik" name="bik" type="text" placeholder="БИК" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-bank"></i></label>
											</label>
										</div>
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="rs" name="rs" type="text" placeholder="Расчетный счет" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="phone" name="phone" type="text" placeholder="Контактный телефон" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-phone"></i></label>
											</label>
										</div>
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="email" name="email" type="text" placeholder="Контактный E-mail" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-envelope"></i></label>
											</label>
										</div>
										
									</div>									
									<div class="panel-footer">
									  <input type="submit" class="button btn-primary" value="Добавить организацию" />
									</div>
								</form>
							</div>
						</div>
					';
	return $return;
}

function ajaxObjectAdd()
{
	global $lang;
	$return = '
	<script type="text/javascript" language="javascript">
		function ajax_send_obj() {
			var msg = $("#add-obj").serializeArray();
			let data = {};
			for(let val of msg){
				data[val.name] = val.value;
			}
			$.ajax({
				type: "POST",
				url: "/administration/module/object/add",
				data: {
					data: data,
				},
				success: function(data) {
					$("#modal-add-object").magnificPopup(\'close\');
					if (data == "error")
					{
						notif("danger", "'.$lang['info'].'", "Что-то пошло не так, возможно заполнены не все поля!");	
					} else {
						notif("success", "'.$lang['info'].'", "Объект успешно добавлена!");	
					}
				}  					
			});
		}
	</script>	
	<div id="modal-add-object" class="popup-basic admin-form mfp-with-anim mfp-hide">							
							<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-pencil-square"></i>Добавить объект</span></div>
								<form id="add-obj" action="javascript:void(null);" onsubmit="ajax_send_obj()">
									<div class="panel-body p25">
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="name" name="name" type="text" placeholder="Введите имя объекта" class="gui-input" />
												<label for="text" class="field-icon"><i class="fas fa-university"></i></label>
											</label>
										</div>
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="address" name="address" type="text" placeholder="Введите адрес объекта" class="gui-input" />
												<label for="text" class="field-icon"><i class="fas fa-map-marker"></i></label>
											</label>
										</div>
									</div>									
									<div class="panel-footer">
									  <input type="submit" class="button btn-primary" value="Добавить объект" />
									</div>
								</form>
							</div>
						</div>
					';
	return $return;
}

function ajax_org_list($soid = 0)
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	$query_users_list = $db->query("SELECT id, name, inn FROM ".DB_PREFIX."_organizations ORDER BY name ASC");
	echo '<option value="0" disabled selected>Выберите контрагента</option>';
	if($db->numRows($query_users_list) > 0) 
	{
		while($users_list = $db->getRow($query_users_list)) 
		{
			$selected = ($users_list['id'] == $soid) ? "selected" : "";
			echo '<option value="'.$users_list['id'].'" '.$selected.'>'.$users_list['name'].' ('.$users_list['inn'].')</option>';
		}
	}		
}