<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


function content_device() 
{
global $adminTpl, $core, $db, $admin_conf, $lang;
	$adminTpl->admin_head($lang['modules'] .' | '. $lang['static']);
	$notif_add = "notif(\'success\', \'".$lang['info']."\', \'Прибор успешно добавлен\');";
	$adminTpl->js_code[] ='	$("#add_device").submit(function() { 
										var form_data = $(this).serialize(); 
										$.ajax({
										type: "POST", 
										url: "'.ADMIN.'/module/verification/ajax/divece_add", 
										data: form_data,
										success: function() {	
												$("#modal-add").magnificPopup(\'close\');
												notif("success","'.$lang['info'].'", "Прибор успешно добавлен");
												ajaxGet("'.ADMIN.'/module/verification/ajax/divece_list", "dev_list");
												document.getElementById("name").value = "";
												document.getElementById("price").value = "";
												document.getElementById("status").value = "1";
												
										}
										});
								});';								
	$adminTpl->js_code[] = '$("#points").spinner();';
	echo '
	<script type="text/javascript" language="javascript">
		function ajax_send(name, str_url) {
			var msg   = $("#"+name).serialize();
			$.ajax({
				type: "POST",
				url: str_url,
				data: msg,
				success: function(data) {
					$("#modal-"+name).magnificPopup(\'close\');
					notif("success","'.$lang['info'].'", data);
					ajaxGet("'.ADMIN.'/module/verification/ajax/divece_list", "dev_list");						
				}          
			});
		}
	</script>
	<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список приборов:</span>
					<div class="widget-menu pull-right" >
						<div class="btn-group">';
							$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
							echo '<button onclick="ajaxGetJS(\'{MOD_LINK}/ajax/divece_list\', \'demoHighCharts.init(); '.$notif.'\', \'dev_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
						</div>
						<div class="btn-group">
							<button onclick="location.href = \'administration/module/verification/device/ulist\'" type="button" class="btn btn-xs btn-primary btn-block">Индивидуальные цены</button>
						</div>
						<div class="btn-group">		
							<button onclick="modal_o(\'#modal-add\');" type="button" class="btn btn-xs btn-success btn-block">Добавить прибор</button>
						</div> 
						<div id="modal-add" class="popup-basic admin-form mfp-with-anim mfp-hide">							
							<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Добавить прибор</span></div>
								<form id="add_device" action="javascript:void(null);">
									<div class="panel-body p25">
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="name" name="name" type="text" placeholder="Введите наименование приборов" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
											</label>
										</div>
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="price" name="price" type="text" placeholder="Укажите стоимость работ" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-rub"></i></label>
											</label>
										</div>										
										<div class="section">
											<label class="col-sm-3 control-label">Активировать прибор?</label>
											<div class="col-sm-4">
												'.radio("status", '1').'														
											</div>
										</div>
									</div>									
									<div class="panel-footer">
									  <input type="submit" class="button btn-primary" value="Добавить прибор" />
									</div>
								</form>
							</div>
						</div>						
					</div>	
              </div>
			  <script type="text/javascript">ajaxGet(\'{MOD_LINK}/ajax/divece_list\', \'dev_list\');</script>
              <div class="panel-body pn table-responsive"> 
				
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w300">Наименование прибора</th>
							<th class="w200">Стоимость</th>
							<th class="w40 text-center">' . $lang['status'] .'</th>
							<th class="w40 text-center"><span class="glyphicon glyphicon-stats fa-lg"></span></th>
							<th class="w200 text-right">' . $lang['actions'] . '</th>
							<th class="w20 text-right">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
                    <tbody id="dev_list">					
					</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="10">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act">							
								<option value="activate">Изменить цены</option>
								<option value="deActivate">Добавить индивидуальные цены</option>
								<option value="reActivate">Включить</option>	
								<option value="reActivate">Отключить</option>		
								<option value="delete">Пометить на удаление</option>
							</select>
							<input name="submit" type="submit" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" /><span class="pd-l-sm"></span>							
						 </nav>
                      </td>					 
                    </tr>
                  </tfoot> 
				</table> 
						
          </div>
        </div>
	</div>';
	$adminTpl->admin_foot();
} 

function ajax_device_list()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];	
	$query = $db->query("SELECT d.*, p.price FROM ".DB_PREFIX."_ver_device AS d LEFT JOIN ".DB_PREFIX."_ver_device_price as p on(d.id=p.vdid) LIMIT " . $cut . ", " . $admin_conf['num'] . "");
	if($db->numRows($query) > 0)
	{	
	while($content = $db->getRow($query)) 
		{
			if ($content['status'] == 1)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}		
			else
			{
				$status_icon = '<span class="fa fa-circle text-danger fa-md"></span>';
			}			
			echo '
			<tr>
				<td><span class="pd-l-sm"></span>' . $content['id'] . '</td>
				<td>' . $content['name'] . '</td>
				<td>' . $content['price']. ' рублей.</td>			
				<td class="text-center">' . $status_icon . '</td>
				<td class="text-center">' . $content['count'] . '</td>
				<td class="w200 text-right">				
					<div class="btn-group">
						<button type="button" onclick="modal_o(\'#modal-edit-'.$content['id'].'\')" class="btn btn-xs btn-primary">'.$lang['edit_short'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a target="_blank" href="' . $contentLink . $content['translate'] . '.html">Задать цену</a></li>  
							<li><a target="_blank" href="' . $contentLink . $content['translate'] . '.html">Задать индивидуальную цену</a></li> 
							<li><a href="{MOD_LINK}/reactivate/'.$content['id'].'">'. (($content['status'] != 1) ? $lang['do_activation'] : $lang['do_deactivation']).'</a></li>  													
							<li class="divider"></li>
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$content['id'].'\')">Отметить на удаление</a></li>
						</ul>
					</div>
					<div id="modal-form-'.$content['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5">' . str_replace('[content]', $content['title'], $lang['static_delete_title']) .'</h3>					
							<hr class="short alt">
							<p>' . str_replace('[content]', $content['title'], $lang['static_delete_text']) .  '</p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{MOD_LINK}/delete/'.$content['id'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						</div>
					  </div>
					  <div id="modal-edit-'.$content['id'].'" class="popup-basic admin-form mfp-with-anim mfp-hide">						
						<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Изменить прибор</span></div>
								<form id="edit-'.$content['id'].'" action="javascript:void(null);" onsubmit="ajax_send(\'edit-'.$content['id'].'\', \''.ADMIN.'/module/verification/ajax/device_edit\');">
									<div class="panel-body p25">
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="name" name="name" type="text" placeholder="Введите наименование приборов" value="'.$content['name'].'" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
											</label>
										</div>																		
										<div class="section">
											<label class="col-sm-3 control-label">Активировать прибор?</label>
											<div class="col-sm-4">
												'.radio("status", $content['status']).'														
											</div>
										</div>
									</div>									
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$content['id'].'" />
										<input type="submit" class="button btn-primary" value="Изменить прибор" />
									</div>
								</form>
							</div>					
					  </div>
				</td>
				<td class="w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['id'] . '" type="checkbox" name="checks[]" value="' . $content['id'] . '">
						<label for="checkbox' . $content['id'] . '"></label>
					</div>
				</td>
			</tr>';		
		}
		} 	
	else 
	{
		$adminTpl->info($lang['static_empty'], 'empty', null, $lang['static_list'], $lang['static_add'], ADMIN.'/module/content/add');	
	}	
}

function ajax_device_add()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['name'])&&$_POST['name']!=""))
	{
		$name = $_POST['name'];
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
		if($db->query("INSERT INTO `" . DB_PREFIX . "_ver_device` ( `id` , `name`, `status`) VALUES (NULL, '" . $db->safesql($name) . "', '" . $status . "');")) 
		{
			if((isset($_POST['price'])&&$_POST['price']!="0"))
			{
				$vdid = $db->insert_id();
				$price = $_POST['price'];
				$db->query("INSERT INTO `" . DB_PREFIX . "_ver_device_price` ( `id` , `vdid`, `uid`, `price`, `nds`,`status`) VALUES (NULL, '" . $db->safesql($vdid) . "', '0', '" . $db->safesql(intval($price)) . "', '0', '1');");
			}		
		}
	}
}

function ajax_device_edit()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['name'])&&$_POST['name']!="") && isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$name = $_POST['name'];
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
		$db->query("UPDATE `" . DB_PREFIX . "_ver_device` SET `status` = '".$status."', `name` = '".$name."' WHERE `id` = " . $id . " LIMIT 1 ;");
		echo $_POST['name'].' успешно изменён!';
	}
}


