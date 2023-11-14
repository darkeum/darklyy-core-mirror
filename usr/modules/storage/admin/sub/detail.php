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

function get_order_data($id){
	global $db;
	$query = $db->query("SELECT s.*, o.name as oname, o.id as oid, sl.id as slid, sl.numb as sl_numb, st.name as st_name
	FROM `".DB_PREFIX."_storage` as s 
	LEFT JOIN `".DB_PREFIX."_sales` as sl on(sl.id=s.sales_id) 
	LEFT JOIN `".DB_PREFIX."_organizations` AS o on(sl.oid=o.id) 
	LEFT JOIN `".DB_PREFIX."_stock` AS st on(st.id=s.stock_id) 
	WHERE s.id = '" . $id . "'"); 
	$order = $db->getRow($query);
	return $order;
}

function detail($id) 
{
global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	$uid = $core->auth->user_info['id'];
	  
	$order = get_order_data($id);
	$adminTpl->admin_head('Склад | Заявка на пересчёт №'.$id);								
	$adminTpl->js_code[] = '$("#points").spinner();
	  $(".select2-single").select2();';
	echo '

	<input type="hidden" id="mainId" value="'.$id.'">

    <style>
    .cp{
        cursor: pointer;
	}
	.dis{
		text-decoration: line-through;
	}
    </style>
	<section id="content" class="animated fadeIn">
		<div class="text-center">            
              <p class="lead">ЗАЯВКА НА ПЕРЕСЧЁТ №'.$id.'</p>
        </div>	
		<div class="row" id="admin-form">';
			echo detail_head($id);
		echo '</div>
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
    
	<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список товаров:</span>
					<div class="widget-menu pull-right" >
						<div class="btn-group">';
							$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
							// $btn = '<button onclick="ajaxGetJS(\'{MOD_LINK}/ajax/detail_list/'.$id.'\', \'demoHighCharts.init(); '.$notif.'\', \'dev_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>'.($order['status'] === "1" ? callAddProduct($id, "https://atri-energo.ru/administration/module/storage/addProduct/", true) : '');
							$victoria = new \Victoria;
							$victoria->link = "/administration/module/storage/victoria_add/{$id}";
							$bb = $victoria->button();
							$btn = '<button onclick="update_body();" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>'.($order['status'] === "1" ? $bb : "");

						echo $btn; 
						echo '</div>';
					echo '</div>	
              </div>
              
              <div class="modal fade" id="confirmModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="">Подтвердить количество?</h5>
                  </div>
                  <div class="modal-body">
                    Подтвердить количество?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="confirmBtn">Выполнить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="modal fade" id="removeModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="">Подтвердить количество?</h5>
                  </div>
                  <div class="modal-body">
                    Подтвердить отсутствие?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="removeBtn">Выполнить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
			</div>
			
            <div class="modal fade" id="removeElemModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="">Удалить позицию</h5>
                  </div>
                  <div class="modal-body">
                    Вы точно хотите удалить эту позицию?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="removeElemModalBtn">Удалить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
			</div>
			
            <div class="modal fade" id="delAllModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="">Удалить позиции</h5>
                  </div>
                  <div class="modal-body">
                    Вы точно хотите удалить эти позиции?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="delAllModalBtn">Удалить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
			</div>
			
            <div class="modal fade" id="sendNotifModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="">Отправка заявки</h5>
                  </div>
                  <div class="modal-body">
                    Вы точно хотите отправить уведомление на склад?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="sendNotifBtn">Отправить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
			</div>
			
            <div class="modal fade" id="confirmStorageModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="">Отправка на подтверждение</h5>
                  </div>
                  <div class="modal-body">
                    Вы точно хотите отправить заявку на подтверждение?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="confirmStorage">Отправить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
			</div>
			
            <div class="modal fade" id="modal_print" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="">Печать</h5>
                  </div>
                  <div class="modal-body">
										
				  <table class="table list-table" style="padding: 0px;">
				  <tbody>
					<tr>
					  <td onclick="window.open(\'/administration/module/storage/print_detail/'.$id.'&format=pdf&type=bill\', \'_blank\'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать заказ</td>
					  <td class="text-right">
						  <a href="/administration/module/storage/print_detail/'.$id.'&format=pdf&type=bill" target="_blank">PDF</a> 
						  <a href="/administration/module/storage/print_detail/'.$id.'&format=docx&type=bill" target="_blank">DOC</a> 
					  </td>
					</tr>	
					<tr>
					  <td onclick="window.open(\'/administration/module/storage/print_detail/'.$id.'&format=pdf&type=bill&combine=true\', \'_blank\'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать заказ объединяя позиции</td>
					  <td class="text-right">
						  <a href="/administration/module/storage/print_detail/'.$id.'&format=pdf&type=bill&combine=true" target="_blank">PDF</a> 
						  <a href="/administration/module/storage/print_detail/'.$id.'&format=docx&type=bill&combine=true" target="_blank">DOC</a> 
					  </td>
					</tr>	
				  </tbody>
				</table>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
			</div>
			
			<div class="modal fade" id="load_excel_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog" role="document">
				<div class="modal-content">
					<form method="POST" action="/" id="load_excel_modal_form">
			      		<div class="modal-header">
			      		  <h5 class="modal-title" id="exampleModalLabel">Загрузить с excel</h5>
			      		</div>
				  		<div class="modal-body">
							<input type="file" class="form-control" name="file" required>
			      		</div>
				  		<div class="modal-footer">
							<input type="submit" class="btn btn-primary" value="Добавить">
			      		  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				  		</div>
				  	</form>
			    </div>
			  </div>
			</div>

			<div class="modal fade" id="loadFile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			  <div class="modal-dialog" role="document">
				<div class="modal-content">
					<form id="loadFileForm">
			      		<div class="modal-header">
			      		  <h5 class="modal-title" id="exampleModalLabel">Загрузить файл</h5>
			      		</div>
				  		<div class="modal-body">';
						  $attribute = [];
						  $attribute["name"] = "";
						  $attribute["script"] = true;
						  $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
						  $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
						  echo uploaderMaster("css", $attribute);
						  echo uploaderMaster("upload", $attribute);
						  echo uploaderMaster("view", $attribute);
			      		echo '</div>
				  		<div class="modal-footer">
						  	<button type="button" class="btn btn-primary" onclick="loadFile();">Загрузить</button>
			      		  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				  		</div>
				  	</form>
			    </div>
			  </div>
			</div>
              
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w50">Артикул</th>
							<th class="w200">Наименование</th>							
							<th class="w200">Производитель</th>							
							<th class="w50">Ед.</th>
							<th class="w50" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
							<th class="w50">Количество</th>
							<th class="w50">Разница</th>
							<th class="w40 text-center">Комментарий</th>		
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
                    <tbody id="dev_list">';		
						ajax_detail_list($id);		
					echo '</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="12">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">	
								<option value="1">Подтвердить количество</option>
								<option value="2">Отсутствуют на складе</option>	
								<option value="3">Удалить</option>																	
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" '.((int)$order["status"] === 1 ? "" : "disabled").'>
							<span class="pd-l-sm"></span>							
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
	';
	xeditableInit();
	$adminTpl->footIncludes[] = '<script src="/usr/modules/storage/admin/asset/js/storageDetail.js?'.time().'"></script>';
	require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
	ndr_init();

	union_modal_init([
		"base_name" => DB_PREFIX."_storage_detail", 
		"parent_name" => "storage_id", 
		"parent_id" => $id, 
		"fields" => ["ID" => ["field" => "id"], "cid" => ["field" => "cid"], "Количество" => ["field" => "count_now"]],
		"url_send" => "/administration/module/storage/detail_union_tov/".$id,
		"send_success" => "update_body();",
	]);

	$attr = [];
	$attr = [
		"module" => 'storage_detail', 
		"id" => $id, 
		"update" => "dev_list", 
		"update_url" => "/administration/module/storage/ajax/detail_list/".$id, 
		"status" => 1,
		"hotkeys" => $btn,
		"container" => "#dev_list",
		"copy_disabled" => $order['status'] == 1 ? false : true,
	];

	fastBar($attr);

	$adminTpl->admin_foot();
} 

function detail_head($id){
	global $db, $core, $lang;
	$uid = $core->auth->user_info['id'];
	$rows_status = array();
	$rows_status[1] = 'Ожидает подтверждения';
	$rows_status[2] = 'Подтвержден';
	
	$order = get_order_data($id);

	$comment = $order["comment"];

	echo '<div class="panel mb25 mt5">										
	<div class="panel-body p20 pb10">									
		<div class="section row mbn admin-form">
			<div class="col-md-12 pl15">
				<div class="section row mb15">
						<div class="col-md-6">
									<div class="element-groups-lg-custom wizard-options" id="ajaxLoadBtn">';
											if($order["status"] === "1"){
												echo '<div class="ph10" id="confirmStorageModal_div">
													<a href="javascript:void(0);" onclick="confirmStorageModal();" class="holder-style p15 text-center holder-active"><span class="fa fa-check-circle holder-icon"></span><br> Отправить на <br>подтверждение</a>
												</div>';
											}
											if(is_null($order["send_notif_uid"]) && (int)$order["status"] === 1){
												echo '<div class="ph10">
													<a href="javascript:void(0);" class="sendNotif holder-style p15 text-center holder-active"><span class="fas fa-user-secret holder-icon"></span><br> Отправить на <br>пересчет</a>
												</div>';
											}
											echo '<div class="ph10">
													<a href="javascript:void(0);" onclick="modal_print();" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print holder-icon"></span><br> Распечатать <br>заявку</a>
												</div>

											<div class="ph10">
												<a href="javascript:void(0);" onclick="union_s();" class="holder-style p15 text-center holder-active"><span class="fas fa-umbrella holder-icon"></span><br>Объединение<br>товаров</a>
											</div>
											<div class="ph10">
												<a href="javascript:void(0);" onclick="load_excel.open();" class="holder-style p15 text-center holder-active"><span class="fas fa-file-excel holder-icon"></span><br>Загрузить с<br>excel</a>
											</div>
									</div>
						</div>
						<div class="col-md-2">
							<small>Контрагент:</small><br/>
							<b>'.(empty($order['oname']) ? "Не указан" : $order['oname']).'</b>&nbsp;&nbsp;';	
							if(!empty($order['oid']))	{
								echo '<a target="_blank" href="'.ADMIN.'/module/organizations/edit/'.$order['oid'].'"><span class="fa fa-external-link"></span></a>';
							}		

							$rows_select = [];
							$rows_select[1] = $lang['storage_count_order_sos'];
							$rows_select[2] = $lang['storage_count_order_supply'];
							$rows_select[3] = 'Ручное добавление';
							

							
							echo '<br/><br/>
							<small>Ответственный:</small><br/>
							<b>'. shortUserName($order['uid']).'</b><br/><br/>
							<small>Тип заявки:</small><br/>
							<b>'. $rows_select[$order["type"]] .'</b><br/><br/>';
							echo <<<HTML
								<small>Комментарий:</small><br/>
								<b><span id="comment" data-value="{$comment}">$comment</span></b><br/><br/>
HTML;
							echo '<small>Сортировка:</small><br/>
							<div>';
							$sort_type = ["s.id"=> "ID", "c.title"=> "Имя", "c.sku"=> "Артикул"];
							$sort_checked_type = isset($_COOKIE["storage_detail_sort"]) ? $_COOKIE["storage_detail_sort"] : "s.id";

							foreach($sort_type as $key => $val){
								$sort_id = "sort_".$key;
								$sort_checked = $sort_checked_type == $key ? "checked" : "";
								?>
									<label for="<?php echo $sort_id;?>"><input type="radio" name="sort" value="<?php echo $key;?>" id="<?php echo $sort_id;?>" <?php echo $sort_checked;?> onchange="storage_detail_sort('<?php echo $key;?>');"> 
										<?php echo $val;?>
									</label>
								<?php
							}
						
							echo '</div>

						</div>
						<div class="col-md-2">
							<small>Дата подачи заявки:</small><br/>
							<b>'. date("d.m.Y H:i:s", strtotime($order['date'])) .'</b><br/><br/>
							<small>Дата завершения заявки:</small><br/>
							<b>'. (empty($order['date_end']) ? 'Заявка еще не завершена!' : date("d.m.Y H:i:s", strtotime($order['date_end']))).'</b><br/><br/>
							<small>Продажа:</small><br/>
							<b>'. (empty($order["sl_numb"]) ? "Не указана" : '<a href="/administration/module/sales/order/'.$order["slid"].'">'.$order["sl_numb"].'</a>') .'</b><br/><br/>
							<small>Склад:</small><br/>
							<b>'. (empty($order["st_name"]) ? "Не указана" : $order["st_name"]) .'</b><br/><br/>
						</div>
						<div class="col-md-2">
							<small>Статус:</small><br/>
							<b>'.  $rows_status[$order['status']] .'</b><br><br>
								<small>Закупка:</small><br/>
								<b>'. (empty($order["supply_id"]) ? "Не указана" : '<a href="/administration/module/supply/detail/'.$order["supply_id"].'">'.module_perfix("supply", $order["supply_id"]).'</a>') .'</b><br/><br/>
								<small>Файлы:</small><i class="fas fa-plus text-success cp ml10" data-toggle="modal" data-target="#loadFile"></i><br/>';
								$query = $db->query("SELECT * FROM `".DB_PREFIX."_attach` WHERE `module` = 'storage_detail' AND pid = '{$id}'");
								if($db->numRows($query) > 0){
									while($r = $db->getRow($query)){
										echo "<div>
											<a href='/download/{$r['id']}'>{$r['name']}</a><i class=\"fas fa-times cp ml10 text-danger\" onclick=\"delete_file('{$r['id']}');\"></i>
										</div>";
									}
								} else {
									echo '<b>Файлы не приложены!</b>';
								}
								echo '<br/><br/>';
						echo '</div>
					</div>											
				</div>	

				</div>
			</div>
		</div>';
}

function ajax_detail_list($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
	//$page = init_page();
	//$cut = ($page-1)*$admin_conf['num'];
	$storage_detail_sort = isset($_COOKIE["storage_detail_sort"]) ? $_COOKIE["storage_detail_sort"] : "s.position";	
	$query = $db->query("SELECT s.id as st_id, s.count, s.count_now, s.status as s_status, s.comment as scomment, c.*, cm.name as cnmane, stor.type as stortype, stor.status as stor_status, stor.stock_id 
	FROM `".DB_PREFIX."_storage_detail` as s 
	LEFT JOIN `".DB_PREFIX."_storage` as stor ON(stor.id = s.storage_id) 
	LEFT JOIN `".DB_PREFIX."_catalog` as c on(s.cid=c.id) 
	LEFT JOIN `".DB_PREFIX."_catalog_manufacture` as cm on(c.man=cm.id) 
	WHERE s.storage_id = '".$id."' AND s.status != '0' 
	ORDER BY ".$storage_detail_sort);

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
					$status_icon = '<span class="far fa-circle text-danger fa-md status_icon"></span>';
				
				$flag = false;
			}
			elseif ($content['s_status'] == 2)
			{
				$status_icon = '<span class="fas fa-check-circle text-success fa-md status_icon"></span>';
			}			
			elseif ($content['s_status'] == 3)
			{
				$status_icon = '<span class="far fa-clock text-warning fa-md" data-toggle="tooltip" title=""></span>';
			}

			$ndr_view = '';
			$ndr_view = get_ndr(["cid" => $content["id"], "stock_id" => $content["stock_id"], "module" => "sales", "pid" => null, "returnType" => "view"]);

			$unit = get_unit('data', $content['unit'] ,'sname');

			echo '
			<tr data-id="'.$content['st_id'].'">
				<td data-toggle="tooltip" title="id: ' . $content['st_id'] . '; cid: '.$content["id"].'" class="position" data-id="'.$content['st_id'].'"><span class="pd-l-sm"></span>' . $i . '</td>
				<td>'.$content['id'].'</td>
				<td>' . $content['sku'] . '</td>
				<td class="title">' . $content['title'] . '</td>				
				<td class="title">' . $content['cnmane'] . '</td>				
				<td>' . $unit . '</td>		
				<td class="availability ndr" onclick="view_ndr_modal('.$content["id"].')">'.$ndr_view.'</td>	
				<td>' . (empty($content['count']) ? 'Не указано' : $content['count']) . '</td>		
				<td class="'.($content["stor_status"] === "2" ? '' : 'cp count_now').'">' . $count_now . '</td>	
				<td><span class="comment" data-value="'.$content['scomment'].'" data-pk="'.$content['st_id'].'">'.$content['scomment'].'</span></td>
				<td class="text-center status">' . $status_icon . '</td>
				<td>				
					<div class="btn-group" >
						<button type="button" class="btn btn-xs btn-danger fas fa-times" onclick="removeElem('.$content['st_id'].')" '.($content["stor_status"] === "1" ? "" : "disabled").'></button>
					</div>';

					echo '  
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

function ajax_detail_tovar_add()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['cid'])))
	{
		$id = intval($_POST['id']);
		$mcid = $_POST['cid'];
		
		$sd = \Storage\Model\Storage::find($id);
        
        foreach($mcid as $cid){
			$count_now = isset($_POST['count_'.$cid]) ? $_POST['count_'.$cid] : null;
			$count_stable = get_quantity_stock_transaction($cid, $sd->stock_id);

			\Storage\Model\StorageDetail::create(["cid" => $cid, "storage_id" => $id, "count_stable" => $count_stable, "status" => 1, "count_now" => $count_now]);
        }
		echo '1';
	}
	else
	{
		echo '2';
	}
}


function create_excel($id)
{
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $dn_array;	
	require_once(ROOT.'usr/plugins/PHPExcel.php');
	require_once(ROOT.'usr/plugins/PHPExcel/IOFactory.php');
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	
	$queryO  = $db->query("SELECT s.*, o.name FROM ".DB_PREFIX."_storage as s LEFT JOIN ".DB_PREFIX."_sales as sl on (s.sales_id=sl.id) LEFT JOIN ".DB_PREFIX."_organizations as o on (sl.oid=o.id) WHERE s.id = '".$id."'");	
	$order = $db->getRow($queryO);	
	$tpl_name='other';
	$storage = '';
	if ($order['oid'] == '70')
	{
		$tpl_name='wilo';
		$storage = 'Забор Со склада  Ногинска';
	}
	
	$objPHPExcel = $objReader->load(ROOT.'/files/tpl/storage/list.xlsx');
	$query = $db->query("SELECT s.*, c.* FROM ".DB_PREFIX."_storage_detail as s LEFT JOIN ".DB_PREFIX."_catalog as c on(s.cid=c.id) WHERE s.storage_id = '".$id."' AND s.status != '0'");	
	if($db->numRows($query) > 0)
	{	
		$baseRow = 8;
		$row_count = 0;
		while($content = $db->getRow($query)) 
		{
			$row_count++;
			$row = $baseRow + $row_count;
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $row_count)
	                              ->setCellValue('B'.$row, $content['id'])
	                              ->setCellValue('C'.$row, $content['sku'])
	                              ->setCellValue('D'.$row, html_entity_decode($content['title']))
	                              ->setCellValue('E'.$row, $content['quantity'])
								  ->setCellValue('F'.$row, $storage );
		}
	}
			




$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$dir = ROOT.'files/supply/'.$id;
					if (!file_exists($dir))
					{
						mkdir($dir, 0777);						
					}
					$file_url= 'files/supply/'.$id.'/'.time().'.xlsx';
					
					
					
					$objWriter->save($file_url); 
						header('Location: https://atri-energo.ru/'.$file_url);	


}



function ajax_count_send()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
	
		$id = intval($_POST['id']);
		
		$query_s = $db->query("SELECT storage_id, supply_detail_id, cid FROM ".DB_PREFIX."_storage_detail WHERE id = '".$id."' LIMIT 1");
        
			$sd = $db->getRow($query_s);
			$count_now = isset($_POST['count_now']) ? (double)$_POST['count_now'] : null;
			$count_stable_data = get_ndr(["cid" => $sd["cid"], "quantity" => "",  "stock_id" => 1, "module" => "", "pid" => "", "returnType" => "array"]);
			$count_stable = $count_stable_data["n"];
			//$count_stable = isset($_POST['count_stable']) ? intval($_POST['count_stable']) : 'null';
			$storage_id = $sd['storage_id'];
			$db->query("UPDATE `" . DB_PREFIX . "_storage_detail` SET `count_now` = '".$count_now."', count_stable = ".$count_stable.", status='2' WHERE `id` = " . $id . " LIMIT 1 ;");
            
            
            if(isset($_POST["type"])){
                if($_POST["type"] == 2){
					$status = $count_now == 0 ? 8 : 9;
                    $db->query("UPDATE JMY_supply_detail SET status = ".$status." WHERE id = '".$sd["supply_detail_id"]."' LIMIT 1");
                }
			}
			echo 'Значение успешно отправлено!';
		
		
	}
}

function check_storage_status($storage_id){
	global $adminTpl, $core, $db, $admin_conf, $lang;
	$query_st = $db->query("SELECT storage_id FROM ".DB_PREFIX."_storage_detail WHERE storage_id = '".$storage_id."' AND status = 1"); 
	if($db->numRows($query_st) > 0){
		echo 2;
	} else {
		echo 1;
	}
}

function storage_confirm_status($storage_id){
	$storage = \Storage\Model\Storage::find($storage_id);
	$storage->status = 2;
	$storage->date_end = date("Y-m-d H:i:s");
	$storage->save();	
	
	if(!empty($storage->sales_id)){
		$attr = [];
		$attr['uid'] = $storage->uid;
		$attr['title'] = 'Остатки актуализированы!';
		$attr['text'] = 'Остатки по заказу №'.$storage->sales_id.' актуализированы!';
		$attr['icon'] = config('app.url').'/files/push/new_storage.png';
		$attr['url'] = config('app.url').'/administration/module/sales/order/'.$storage->sales_id;
		$attr['pNotif'] = true;
		$attr['pPush'] = true;			
		$attr['module'] = "sales";
    	$attr['pid'] = $storage->sales_id;
		$notif_id = sendAllNotif($attr);
		//relationsSave('storage', $storage_id, 'notif', $notif_id);
	}
    
	$details = $storage->storage_detail;
    if($details->count() > 0){
		$control_storage = \Fcontrol\Model\ControlStorage::create(["title" => 'Расхождение остатков за сутки от '.date("d.m.Y")]);
		$insert_id = $control_storage->id;

		foreach($details as $detail){
			\Fcontrol\Model\ControlStorageDetail::create(["control_storage_id" => $insert_id, "storage_id" => $storage->id, "storage_detail_id" => $detail->id]);
			// storage_update_quantity($storage_id, $r["stock_id"], $rr['cid'], $rr['count_now']);
		}
		
		$attr = [];
		$attr['uid'] = 3;
		$attr['title'] = 'Новая заявка на подтверждение остатков №'.$insert_id;
		$attr['text'] = 'Новая заявка на подтверждение остатков №'.$insert_id;
		$attr['icon'] = config('app.url').'/files/push/new_storage.png';
		$attr['url'] = config('app.url').'/administration/module/fcontrol/storage_detail/'.$insert_id;
		$attr['pNotif'] = true;
		$attr['pPush'] = true;			
		$attr['module'] = "fcontrol_storage";
    	$attr['pid'] = $insert_id;
		sendAllNotif($attr);
    }
}

function remove_element($id){
	global $adminTpl, $core, $db, $admin_conf, $lang;
	$db->query("DELETE FROM `JMY_storage_detail` WHERE id = '".$id."' LIMIT 1");
}

function detail_union_tov($data){
	global $db;
    if($data){
        foreach($data as $key => $val){
            if(!empty($val["union"])){
                $count = 0;
                if(count($val["union_list"]) > 0){
                    $id_str = implode(", ", $val["union_list"]);
					$query = $db->query("SELECT SUM(count_now) as sum_count FROM ".DB_PREFIX."_storage_detail WHERE id IN(".$id_str.")");
					if($db->numRows($query) > 0){
                        $r = $db->getRow($query);
                        $count = $r["sum_count"];
                        if(($key = array_search($val["union"],$val["union_list"])) !== FALSE){
                            unset($val["union_list"][$key]);
						}

						$db->query("UPDATE ".DB_PREFIX."_storage_detail SET count_now = '".$count."' WHERE id = '".$val["union"]."' LIMIT 1");

                        $id_str = implode(", ", $val["union_list"]);
                        $db->query("DELETE FROM ".DB_PREFIX."_storage_detail WHERE id IN(".$id_str.")");
					}
				}
			}
		}
	}
}	