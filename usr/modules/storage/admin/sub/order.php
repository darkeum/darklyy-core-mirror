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


function order() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Закупки';
	$adminTpl->admin_head('Менеджер закупок | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'supply';
	$atributs['module_open'] = 'order_detail';
	load_grid('jqSupplyOrder', $lnn, $atributs);
	$adminTpl->admin_foot();
} 


function order_detail($id) 
{
global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	
	  $rows_status = array();
		$rows_status[0] = 'Отменен';
		$rows_status[1] = 'Ожидает закупки';
      	$rows_status[2] = 'Ожидает отправки';
		$rows_status[3] = 'Закуплено';
	$query = $db->query("SELECT s.*, o.name as oname, o.id as oid FROM ".DB_PREFIX."_supply_order as s LEFT JOIN ".DB_PREFIX."_organizations AS o on(s.oid=o.id) WHERE s.id = '" . $id . "'"); 
	$order = $db->getRow($query);		
	$adminTpl->admin_head('Поверка приборов | Закупка №SN-'.$id);								
	$adminTpl->js_code[] = '$("#points").spinner();
	  $(".select2-single").select2();
		$(\'#qrcode\').qrcode({width: 186,height: 186,text: "'.$config['url'].'/supply/view/'.md5(intval($order['date'])+intval($order['id'])).'"});';
	echo '
	<section id="content" class="animated fadeIn">
		<div class="text-center">            
              <p class="lead">ЗАКУПКА №SN-'.$id.'</p>
        </div>	
		<div class="row" id="admin-form">			
			<div class="panel mb25 mt5">										
				<div class="panel-body p20 pb10">									
					<div class="section row mbn admin-form">
						<div class="col-md-9 pl15">
							<div class="section row mb15">
									<div class="col-md-4">
										<div class="panel br-a">
											<div class="panel-heading" style="padding: 7px; line-height: 29px;"><span class="panel-title" style="font-size: 18px;">Меню</span></div>
											<div class="panel-body panel-scroller scroller-xs scroller-pn pn">
												<table class="table mbn tc-med-1 table-hover">
													<thead>
													  <tr class="hidden">
														<th>#</th>														
													  </tr>
													</thead>
													<tbody>
														<tr>
															<td  onclick="location.href=\''.config('app.url').'/'.ADMIN.'/module/supply/create_excel/'.$id.'\'" style="cursor: pointer;" ><span style="top: 0px;" class="glyphicon glyphicon-list-alt mr5 va-m text-warning"></span>Распечатать заявку</td>
														</tr>
														<tr>
															<td style="cursor: pointer;" ><span style="font-size: 16px;" class="fa fa-file-text-o mr5 va-m text-success"></span>Зарезервировать по наличию</td>														
														</tr>
													  <tr>
														<td><span style="font-size: 16px;" class="fa fa-check-square-o mr5 va-m text-info"></span>Создать акт</td>
													
													  </tr>         
													  <tr>
														<td><span class="flag-xs flag-fr mr5 va-b"></span>Отменить заявку</td>
													
													  </tr>  
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<small>Поставщик:</small><br/>
										<b>'.$order['oname'].'</b>&nbsp;&nbsp;				
										<a target="_blank" href="'.ADMIN.'/module/organizations/edit/'.$order['oid'].'"><span class="fa fa-external-link"></span></a>
										<br/><br/>
										<small>Ответственный:</small><br/>
										<b>'. shortUserName($order['uid']).'</b><br/><br/>
										<small>Статус:</small><br/>
										<b>'.  $rows_status[$order['status']] .'</b>
									</div>
									<div class="col-md-4">
										<small>Дата подачи заявки:</small><br/>
										<b>'. $order['date'] .'</b><br/><br/>
										<small>Дата завершения заявки:</small><br/>
										<b>'. (empty($order['date_end']) ? 'Заявка еще не завершена!' : formatdate($order['date_end'], false)).'</b>													
									</div>
							</div>												
							</div>
							<div class="col-md-3 text-right">
								<a href="'.$config['url'].'/supply/view/'.md5(intval($order['date'])+intval($order['id'])).'" target="_blank"><div id="qrcode"></div></a>
							</div>								
							</div>
						</div>
					
					</div>
				</div>
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
					ajaxGet("'.ADMIN.'/module/supply/ajax/order_detail_list/'.$id.'", "dev_list");					
				}          
			});
		}
	</script>
	<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список приборов:</span>
					<div class="widget-menu pull-right" >
						<div class="btn-group">';
							$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
							echo '<button onclick="ajaxGetJS(\'{MOD_LINK}/ajax/order_detail_list/'.$id.'\', \'demoHighCharts.init(); '.$notif.'\', \'dev_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
						</div>							
						<div id="modal-add" class="popup-basic admin-form mfp-with-anim mfp-hide">							
							<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Добавить прибор</span></div>
								<form id="add_device" action="javascript:void(null);">
									<div class="panel-body p25">
										<div class="section">
											<label for="toid" class="field prepend-icon">
												<select name="vdid" class="select2-single form-control">
												<option value="0" disabled selected>Выберите тип прибора</option>';
												$query_users_list = $db->query("SELECT * FROM ".DB_PREFIX."_ver_device ORDER BY name ASC");
												if($db->numRows($query_users_list) > 0) 
												{
													while($users_list = $db->getRow($query_users_list)) 
													{
														$selected = ($users_list['id'] == $toid) ? "selected" : "";
														echo '<option value="'.$users_list['id'].'" '.$selected.'>'.$users_list['name'].'</option>';
													}
												}																
												echo'	</select>	
												<label for="translit" class="field-icon"><i class="fa fa-tasks"></i></label>												
											</label>
										</div>
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="sum" name="sum" type="text" placeholder="Укажите количество" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-pencil"></i></label>
											</label>
										</div>	
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="model" name="model" type="text" placeholder="Модель прибора" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>		
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="number" name="number" type="text" placeholder="Заводской номер" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-shield"></i></label>
											</label>
										</div>	
										<div class="section">
											<label class="field prepend-icon select">											
									<select class="form-control" name="dn" id="dn">
									<option value="0" disabled selected>Выберите диаметр</option>';
										foreach ($dn_array as $key => $value) {
							
												echo '<option value="'.$key.'" '.($key == $types ? 'selected' : '').'>'.$value.'</option>';				
										}		
								echo '</select><i class="arrow double"></i>
								
								</label>
										</div>									
									</div>									
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$id.'" />
									  <input type="submit" class="button btn-primary" value="Добавить прибор" />
									</div>
								</form>
							</div>
						</div>						
					</div>	
              </div>
			  <script type="text/javascript">ajaxGet(\'{MOD_LINK}/ajax/order_detail_list/'.$id.'\', \'dev_list\');</script>
              <div class="panel-body pn table-responsive"> 
				
                  <table class="table table-striped" id="tablesForm">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w50">Артикул</th>
							<th class="w200">Наименование</th>							
							<th class="w150">Кол-во</th>
							<th class="w50">Ед.</th>
							<th class="w50">Наличие</th>
							<th class="w50">Доступно</th>
							<th class="w40 text-center"><span class="fa fa-pencil fa-lg"></span></th>		
							<th class="w40 text-center">' . $lang['status'] .'</th>						
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
					  <td align="right" colspan="12">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act">							
								<option value="activate">Приборы прошли проверку</option>
								<option value="deActivate">Приборы не прошли проверку</option>								
							</select>
							<input name="submit" type="submit" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" /><span class="pd-l-sm"></span>							
						 </nav>
                      </td>					 
                    </tr>
                  </tfoot> 
				</table> 
			 </div>			
          </div>
        </div>
	</section>';
	$adminTpl->admin_foot();
} 

function ajax_order_detail_list($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];	
	$query = $db->query("SELECT so.supply_o, s.*, c.quantity FROM ".DB_PREFIX."_supply_order_detail as so LEFT JOIN ".DB_PREFIX."_supply_detail as s on(so.supply_d=s.id) LEFT JOIN ".DB_PREFIX."_catalog as c on(c.id=s.cid) WHERE so.supply_o = '".$id."' AND s.status != '0'");
	
	echo '	<div id="modal-no" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Внимание!</span></div>						  
						  <div class="panel-body">
							<h3 class="mt5">Действие наивозможно!</h3>					
							<hr class="short alt">
							<p>По данной заявки уже был сформирована наряд-заказ дальнейшее изменение невозможно, обратитесь к администратору!</p>
						  </div>					 
						</div>
					  </div>';
	if($db->numRows($query) > 0)
	{	
	$i=0;
	$total = 0;
	while($content = $db->getRow($query)) 
		{
			$i++;
			$flag = true;
			
			if ($content['status'] == 1)
			{
					$status_icon = '<span class="fa fa-circle-o text-danger fa-md"></span>';
				
				$flag = false;
			}
			elseif ($content['status'] == 3)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}			
			elseif ($content['status'] == 2)
			{
				$status_icon = '<span class="fa fa-clock-o text-warning fa-md" data-toggle="tooltip" title=""></span>';
			}
			echo '
			<tr>
				<td data-toggle="tooltip" title="' . $content['id'] . '"><span class="pd-l-sm"></span>' . $i . '</td>
				<td>' . $content['s_sku'] . '</td>
				<td>' . $content['s_name'] . '</td>
				<td>' . $content['count'] . '</td>				
				<td>' . $content['s_unit'] . '</td>		
				<td>' . $content['quantity'] . '</td>	
				<td>' . $content['quantity'] . '</td>	
				<td>'.(!empty($content['comment']) ? '<span style="cursor: pointer;" onclick="modal_o(\'#modal-comm-'.$content['id'].'\')" class="fa fa fa-edit text-info fa-md"></span>
				<div id="modal-comm-'.$content['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарий</span></div>
						  <div class="panel-body">
							<h3 class="mt5">Комментарий:</h3>					
							<hr class="short alt">
							<p>'.$content['comment'].'</p>
						  </div>
						  <div class="panel-footer text-right">
							
						  </div>
						</div>
					  </div>' : '<span class="fa fa fa-edit fa-md"></span>').'</td>					
				<td class="text-center">' . $status_icon . '</td>
				<td class="w200 text-right">				
					<div class="btn-group" >
						';
						
						if (false)
						{
							echo   '<button type="button" onclick="modal_o(\'#modal-compl-'.$content['id'].'\')" class="btn btn-xs btn-primary">Зарезервировать</button>
									<button type="button" '.($content['status'] != 1 ? 'disabled' : '').' data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
									<ul role="menu" class="dropdown-menu">	
										<li><a disabled onclick="modal_o(\'#modal-no\')" href="javascript:void(0);">Сделать заказать</a></li>
										<li class="divider"></li>
										<li><a href="javascript:void(0);" onclick="modal_o(\'#modal-no\')">Отметить на удаление</a></li>
									</ul>';
						}
						else
						{
							echo   '<button type="button" onclick="modal_o(\'#modal-compl-'.$content['id'].'\')" class="btn btn-xs btn-primary">Сделать заказать</button>
									<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
									<ul role="menu" class="dropdown-menu">											
										<li><a href="javascript:void(0);" onclick="modal_o(\'#modal-no\')">Отметить на удаление</a></li>
									</ul>';
						}
					echo '
						
						
					</div>
					
					<div id="modal-compl-'.$content['id'].'" class="popup-basic admin-form mfp-with-anim mfp-hide">						
						<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Отправить в счет</span></div>
								<form id="compl-'.$content['id'].'" action="javascript:void(null);" onsubmit="ajax_send(\'compl-'.$content['id'].'\', \''.ADMIN.'/module/supply/ajax/order_send\');">
									<div class="panel-body p25">
										<div class="section">
											Номер закупки: <br>
											<label class="field select">
												<select class="form-control" name="order_numb" id="order_numb">';
													$query_r = $db->query("SELECT s.id, o.name FROM ".DB_PREFIX."_supply_order as s LEFT JOIN JMY_organizations as o on(o.id=s.oid) WHERE s.status = '1' ORDER BY id ASC");
													if($db->numRows($query_r) > 0) 
														{
															while($r_list = $db->getRow($query_r)) 
															{
																$selected = ($r_list['id'] == $rtype) ? "selected" : "";
																echo '<option value="'.$r_list['id'].'" '.$selected.'>Заказ №SN-'.$r_list['id'].' ('.$r_list['name'].')</option>';
															}
														}		
													echo '	</select><i class="arrow double"></i>
												</label>											
										</div>						
										<div class="section">
											Комментарий: <br>
											<label for="text" class="field prepend-icon">
											    <textarea id="comment" name="comment" class="gui-textarea" placeholder="Комментарий"/></textarea>
												<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
											</label>
										</div>																		
										<div class="section">
											Количество товаров: <br>
											<label for="count" class="field prepend-icon">
												<input id="count" type="text" name="count" value="'. $content['count'] .'" placeholder="Количество" class="gui-input">
												<label for="count" class="field-icon"><i class="fa fa-link"></i></label>
											</label>
										</div>
									</div>									
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$content['id'].'" />
										
										<input type="submit" class="button btn-success" value="Отправить" />
									</div>
								</form>
							</div>					
					</div>
					  
					<div id="modal-form-'.$content['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <form id="del-'.$content['id'].'" action="javascript:void(null);" onsubmit="ajax_send(\'del-'.$content['id'].'\', \''.ADMIN.'/module/verification/ajax/order_divece_del\');">
						  <div class="panel-body">
							<h3 class="mt5">Удалить '.$content['s_name'].'?</h3>					
							<hr class="short alt">
							<p>Вы действительно хотите удалить '.$content['s_name'].' из списка?</p>
						  </div>
						  <div class="panel-footer text-right">
							<input type="hidden" name="id" value="'.$content['id'].'" />
							<button type="submit" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						  </form>
						</div>
					  </div>
					  <div id="modal-edit-'.$content['id'].'" class="popup-basic admin-form mfp-with-anim mfp-hide">						
						<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Изменить прибор</span></div>
								<form id="edit-'.$content['id'].'" action="javascript:void(null);" onsubmit="ajax_send(\'edit-'.$content['id'].'\', \''.ADMIN.'/module/verification/ajax/device_edit\');">
									<div class="panel-body p25">
										<div class="section">
											<label for="text" class="field prepend-icon">
											    <input id="name" name="name" type="text" placeholder="Введите наименование приборов" value="" class="gui-input" />
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
		echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';	
	}	
}


