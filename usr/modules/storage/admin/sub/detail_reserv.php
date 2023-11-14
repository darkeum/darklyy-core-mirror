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

function detail_reserv($id) 
{
global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	
	$rows_status = array();
	$rows_status[1] = 'Ожидает подтверждения';
	$rows_status[2] = 'Подтвержден';
	
	$query = $db->query("SELECT s.*, o.name as oname, o.id as oid, sl.id as slid FROM ".DB_PREFIX."_storage as s LEFT JOIN JMY_sales as sl on(sl.id=s.sales_id) LEFT JOIN ".DB_PREFIX."_organizations AS o on(sl.oid=o.id)  WHERE s.id = '" . $id . "'"); 
	$order = $db->getRow($query);		

	$adminTpl->admin_head('Резерв | Резерв №'.$id);								
	$adminTpl->js_code[] = '$("#points").spinner();
	  $(".select2-single").select2();
		$(\'#qrcode\').qrcode({width: 186,height: 186,text: "'.$config['url'].'/supply/view/'.md5(intval($order['date'])+intval($order['id'])).'"});';
	echo '
    <style>
    .cp{
        cursor: pointer;
    }
    </style>
	<section id="content" class="animated fadeIn">
		<div class="text-center">            
              <p class="lead">РЕЗЕРВ №'.$id.'</p>
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
															<td style="cursor: pointer;" onclick="location.href=\''.config('app.url').'/'.ADMIN.'/module/storage/create_excel/'.$id.'\'"><span style="top: 0px;" class="glyphicon glyphicon-list-alt mr5 va-m text-warning"></span>Распечатать заявку</td>
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
										<small>Контрагент:</small><br/>
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
										<b>'. (empty($order['date_end']) ? 'Заявка еще не завершена!' : formatdate($order['date_end'], false)).'</b><br/><br/>
										<small>Комментарий:</small><br/>
													<b><a href="javascript:void(0);" onclick="modal_o(\'#modal-comm\'); ajaxGet(\''.ADMIN.'/module/sales/ajax/comments_get/'.$order['slid'].'\', \'sale_comments_list\');">Посмотреть комментарии</a></b>
                                                    
													
													
                                                    <div id="modal-comm" class="popup-basic bg-none mfp-with-anim mfp-hide">
                                                    <form id="sale_comments" action="javascript:void(null);" onsubmit="ajax_comments_send(\'sale_comments\', \''.ADMIN.'/module/sales/commSave/'.$order['slid'].'\');">
                                                    <div class="panel">
                                                    <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарии</span></div>
                                                    <div class="panel-body">
                                                    <h3 class="mt5">Комментарии:</h3>
													<div id="sale_comments_list" style="max-height: 300px; overflow-y: scroll;">';
													
													
													
                                                    echo '</div><hr class="short alt">
													<textarea class="form-control" rows="5" name="comm" id="comm" placeholder="Комментарий..." required></textarea>
                                                    
                                                    </div>
                                                    <div class="panel-footer">
                                                    <input type="submit" value="Добавить комментарий" class="btn btn-success">
                                                    </div>
                                                    </div>
                                                    </form>
                                                    </div>
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
					ajaxGet("'.ADMIN.'/module/storage/ajax/detail_list/'.$id.'", "dev_list");					
				}          
			});
		}
		function ajax_comments_send(name, str_url) {
		var msg   = $("#"+name).serialize();
		$.ajax({
		type: "POST",
		url: str_url,
		data: msg,
		success: function(data) {
		ajaxGet("'.ADMIN.'/module/sales/ajax/comments_get/'.$order['slid'].'", "sale_comments_list");		
		$("#comm").val("");											
		}          
		});
		}
	</script>

    <script src="/usr/modules/storage/admin/asset/js/storageDetailReserv.js"></script>
	<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список приборов:</span>
					<div class="widget-menu pull-right" >
						<div class="btn-group">';
							$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
							echo '<button onclick="ajaxGetJS(\'{MOD_LINK}/reserv/detail_list/'.$id.'\', \'demoHighCharts.init(); '.$notif.'\', \'dev_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
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
			  <script type="text/javascript">ajaxGet(\'{MOD_LINK}/reserv/detail_list/'.$id.'\', \'dev_list\');</script>
              
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w50">Артикул</th>
							<th class="w200">Наименование</th>							
							<th class="w50">Ед.</th>
							<th class="w50" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
							<th class="w50">Количество</th>
							<th class="w40">Комментарий</th>		
							<th class="w40 text-center">' . $lang['status'] .'</th>						
							<th class="w150">' . $lang['actions'] . '</th>
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
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">	
								<option value="1">Резерв</option>
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" /><span class="pd-l-sm"></span>							
						 </nav>
                      </td>					 
                    </tr>
                  </tfoot> 
				</table> 
                </form>
			 </div>			
          </div>
        </div>
	</section>
	
	<div class="modal fade" id="reservModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Потвердеть резервацию</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите подтвердить резервацию?
      </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary reservBtn" data-dismiss="modal">Подтвердить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
	
<div class="modal fade" id="reservModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel">Потвердеть отмену резервации</h5>
		</div>
		<div class="modal-body">
			Вы действительно хотите подтвердить отмену резервации?
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary reservBtn" data-dismiss="modal">Подтвердить</button>
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		</div>
	</div>
</div>
</div>
	';
	$adminTpl->admin_foot();
} 

function detail_list_reserv($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];	
	$query = $db->query("SELECT s.id as st_id, s.count, s.count_now, s.status as s_status, s.comment as scomment, c.*, cu.name as s_unit, stor.type as stortype FROM ".DB_PREFIX."_storage_detail as s LEFT JOIN ".DB_PREFIX."_storage as stor ON(stor.id = s.storage_id) LEFT JOIN ".DB_PREFIX."_catalog as c on(s.cid=c.id) LEFT JOIN JMY_catalog_unit as cu ON(cu.unit = c.unit) WHERE s.storage_id = '".$id."' AND s.status != '0'");
	
	if($db->numRows($query) > 0)
	{	
	$i=0;
	$total = 0;
	while($content = $db->getRow($query)) 
		{
			$i++;
			$flag = true;
            $count_now = $content['count_now'];
			if ($content['count_now'] == "" || $content['count_now'] == null)
			{
				$count_now = 'Не указано';
			}
			if ($content['s_status'] == 1)
			{
					$status_icon = '<span class="fa fa-circle-o text-danger fa-md"></span>';
				
				$flag = false;
			}
			elseif ($content['s_status'] == 2)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}			
			elseif ($content['s_status'] == 3)
			{
				$status_icon = '<span class="fa fa-clock-o text-warning fa-md" data-toggle="tooltip" title=""></span>';
			}

			$ndr_view = get_ndr(["cid" => $content["id"], "stock_id" => "1", "module" => "sales", "pid" => null, "returnType" => "view"]);

			echo '
			<tr data-id="'.$content['st_id'].'">
				<td data-toggle="tooltip" title="' . $content['st_id'] . '"><span class="pd-l-sm"></span>' . $i . '</td>
				<td>' . $content['sku'] . '</td>
				<td>' . $content['title'] . '</td>				
				<td>' . $content['s_unit'] . '</td>		
				<td class="availability">'.$ndr_view.'</td>	
				<td>' . (empty($content['count']) ? 'Не указано' : $content['count']) . '</td>			
				<td>' . (!empty($content['scomment']) ? '<span style="cursor: pointer;" onclick="modal_o(\'#modal-comm-'.$content['st_id'].'\')" class="text-center">'.short(20, $content['scomment']).'</span>
				<div id="modal-comm-'.$content['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарий</span></div>
						  <div class="panel-body">
							<h3 class="mt5">Комментарий:</h3>					
							<hr class="short alt">
							<p>'.$content['scomment'].'</p>
						  </div>
						  <div class="panel-footer text-right">
							
						  </div>
						</div>
					  </div>' : '<span class="text-center">Нет комментария</span>').'</td>					
				<td class="text-center">' . $status_icon . '</td>
				<td>				
					<div class="btn-group" >
						';

						$dis = 'disabled';
						if(($content['stortype'] == 4 || $content['stortype'] == 5) && $content['s_status'] == 1){
							$dis = '';
						}
						switch($content['stortype']){
							default:
								echo '<button type="button" class="btn btn-xs btn-primary reserv" data-id="'.$content['st_id'].'" '.$dis.'>Резерв</button>';
							break;
							case "5":
								echo '<button type="button" class="btn btn-xs btn-primary reserv1" data-id="'.$content['st_id'].'" '.$dis.'>Резерв</button>';
							break;
						}
							
							echo		'<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
									<ul role="menu" class="dropdown-menu">';											
										echo '<li><a href="javascript:void(0);">В разработке...</a></li>
									</ul>';
						
					echo '
						
						
					</div>
					
					<div id="modal-compl-'.$content['st_id'].'" class="popup-basic admin-form mfp-with-anim mfp-hide">						
						<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Подтвердить количество</span></div>
								<form id="compl-'.$content['st_id'].'" action="javascript:void(null);" onsubmit="ajax_send(\'compl-'.$content['st_id'].'\', \''.ADMIN.'/module/storage/ajax/count_send\');">
									<div class="panel-body p25">																										
										<div class="section">
											Количество товаров: <br>
											<label for="count_now" class="field prepend-icon">
												<input id="count_now" type="text" name="count_now" value="'. $content['quantity'] .'" placeholder="Количество на складе" class="gui-input">
												<label for="count_now" class="field-icon"><i class="fa fa-link"></i></label>
											</label>
										</div>
										<div class="section">
											Комментарий: <br>
											<label for="text" class="field prepend-icon">
											    <textarea id="comment" name="comment" class="gui-textarea" placeholder="Комментарий"/></textarea>
												<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
											</label>
										</div>									
									</div>									
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$content['st_id'].'" />									
										<input type="submit" class="button btn-success" value="Подтвердить" />
									</div>
								</form>
							</div>					
					</div>
					  
					<div id="modal-form-'.$content['st_id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <form id="del-'.$content['st_id'].'" action="javascript:void(null);" onsubmit="ajax_send(\'del-'.$content['st_id'].'\', \''.ADMIN.'/module/verification/ajax/order_divece_del\');">
						  <div class="panel-body">
							<h3 class="mt5">Удалить '.$content['title'].'?</h3>					
							<hr class="short alt">
							<p>Вы действительно хотите удалить '.$content['title'].' из списка?</p>
						  </div>
						  <div class="panel-footer text-right">
							<input type="hidden" name="id" value="'.$content['st_id'].'" />
							<button type="submit" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						  </form>
						</div>
					  </div>
				</td>
				<td class="w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['st_id'] . '" type="checkbox" name="checks[]" value="' . $content['st_id'] . '" class="checks">
						<label for="checkbox' . $content['st_id'] . '"></label>
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
