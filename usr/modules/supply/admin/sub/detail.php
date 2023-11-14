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

function query_detail_result($id)
{
	global $db;
	$query = $db->query("SELECT s.*, o.name as oname, o.id as oid, sl.id as slid, sl.comment as slcomment, sl.numb, sl.org as sl_org, obj.name as obj_name, obj.id as obj_id 
	FROM `" . DB_PREFIX . "_supply` as s 
	LEFT JOIN `" . DB_PREFIX . "_sales` as sl on(sl.id=s.sales_id) 
	LEFT JOIN `" . DB_PREFIX . "_organizations` AS o on(sl.oid=o.id) 
	LEFT JOIN `" . DB_PREFIX . "_object` as obj ON(obj.id = sl.object) 
	WHERE s.id = '" . $id . "'");
	$order = $db->getRow($query);
	return $order;
}

function detail($id)
{
	global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	$order = query_detail_result($id);
	$adminTpl->admin_head('Закупки | Заявка на закупку №' . $id);
	$adminTpl->js_code[] = '$("#points").spinner();
		$(".select2-single").select2();
		$(".select2-single1").select2({placeholder: "Выберите контрагента"});
		$(".select2-single2").select2({placeholder: "Выберите заказ"});';

	echo '
    <style>
    .cp{
        cursor: pointer;
    }
	</style>
	<input type="hidden" value="' . $id . '" id="mainId">
	<section id="content" class="animated fadeIn">
		<div class="text-center">            
              <p class="lead">ЗАЯВКА НА ЗАКУПКУ №' . $id . '</p>
        </div>	
		<div class="row" id="admin-form">			
			<div class="panel mb25 mt5">										
				<div class="panel-body p20 pb10">									
					<div class="section row mbn admin-form">
						<div class="col-md-12 pl15">
							<div class="section row mb15" id="dev_head">';
								ajax_detail_head($id);
							echo '</div>	

							</div>							
							</div>
						</div>
					
					</div>
				</div>
	<script>
	
		function ajax_send(name, str_url) {
			var msg   = $("#"+name).serialize();
			$.ajax({
				type: "POST",
				url: str_url,
				data: msg,
				success: function(data) {
					$("#modal-"+name).magnificPopup(\'close\');
					notif("success","' . $lang['info'] . '", data);
					ajaxGet("' . ADMIN . '/module/supply/ajax/detail_list/' . $id . '", "dev_list");					
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
		ajaxGet("' . ADMIN . '/module/sales/ajax/comments_get/' . $order['slid'] . '", "sale_comments_list");		
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
	$notif = "notif(\'primary\', \'" . $lang['info'] . "\', \'" . $lang['ajax_update_compl'] . "\');";
	echo '<button onclick="ajaxGetJS(\'{MOD_LINK}/ajax/detail_list/' . $id . '\', \'demoHighCharts.init(); ' . $notif . '\', \'dev_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
						</div>	

					</div>
                </div>
              <div class="modal fade" id="removeOnPass" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="">Удалить из счета?</h5>
                          </div>
                          <div class="modal-body" id="bodyRemoveOnPass">
                            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary" id="removeOnPassConfirm">Удалить</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="modal fade" id="viewSupplyOrder" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="">Просмотр счета</h5>
                          </div>
                          <div class="modal-body" id="bodyViewSuppplyOrder">
                            
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="modal fade" id="viewWarehousePosition" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="">Складская позиция</h5>
                          </div>
                          <div class="modal-body" id="bodyWarehousePosition">
                                Вы действительно хотите отправить складскую позицию?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary" id="warehousePositionBtn">Отправить</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="modal fade" id="viewWarehousePositionDel" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="">Не складская позиция</h5>
                          </div>
                          <div class="modal-body" id="bodyWarehousePosition">
                                Вы действительно хотите убрать из складской позиции?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary" id="warehousePositionDelBtn" data-dismiss="modal">Отправить</button>
                          </div>
                        </div>
                      </div>
                    </div>
              
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w50">Артикул</th>
							<th class="w200">Наименование</th>	
                            <th class="w50">Производитель</th>							
							<th class="w100">Кол-во</th>
							<th class="w100">Кол-во отправленного</th>
							<th class="w100">Цена закупки</th>
							<th class="w50">Ед.</th>
							<th class="w50" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
							<th class="w40 text-center">Комментарий</th>		
							<th class="w40 text-center">' . $lang['status'] . '</th>						
							<th class="w200 text-right">' . $lang['actions'] . '</th>
							<th class="w20 text-right">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); check_checks(); return true;">
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
								<option value="mas_supply_order">Массовый заказ</option>											
								<option value="mas_storage">Массовая складская позиция</option>	
								<option value="mas_del">Массовое удаление</option>								
							</select>
							<input name="submit" id="sub" type="button" class="btn btn-success" style="display: inline-block;" onclick="select_help();" value="' .  $lang['doit'] . '" disabled><span class="pd-l-sm"></span>							
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

	<div class="modal fade admin-form" id="modal-compl" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<form id="compl" action="javascript:void(null);">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">Отправить в счет</h5>
		  </div>
		  
	      	<div class="panel-body p25">
					<div class="section">
						<div>Номер закупки:</div>
						<div class="smart-widget sm-right smr-50">';
						// echo '<label class="field select prepend-icon">
						// 	<select  class="select2-single2 form-control" name="order_numb" id="order_numb"></select>
						// 	<i class="arrow double"></i>
						// 	<label for="order_numb" class="field-icon"><i class="fas fa-shopping-cart"></i></label>
						// </label>';
						oSelect("jsAdminTpl", "order_numb", null, "getSupplyOrderToSelect");
						oSelect("select", "order_numb", null, "getSupplyOrderToSelect");
						oSelectCustom([
							"mod" => "jsAdminTpl",
							"name" => "order_numb",
							"ajaxUrl" => "getSupplyOrderToSelect",
							"data_send" => ["org" => empty($order["org"]) ? $order["sl_org"] : $order["org"]],
						]);
						echo '<button onclick="showhide(\'addition\')" type="button" class="button"><i class="fa fa-plus"></i></button>	
								</div>		
					</div>		
					<div class="section" id="addition" style="display: none">
						<div>Выберите контрагента:</div>';
						// echo '<label class="field select prepend-icon">
						// 	<select class="select2-single1 gui-input" name="new_order" id="new_order"></select>
						// 	<label for="new_order" class="field-icon"><i class="fas fa-users"></i></label>
						// </label>';
						oSelect("jsAdminTpl", "new_order", null, "getOrganizations");
						oSelect("select", "new_order", null, "getOrganizations");
					echo '</div>	
					<div class="section">
						Комментарий: <br>
						<label for="text" class="field prepend-icon">
								<textarea id="comment" name="comment" class="gui-textarea" placeholder="Комментарий"/></textarea>
							<label for="text" class="field-icon"><i class="fa fa-comments"></i></label>
						</label>
					</div>																		
					<div class="section" id="div_count">
						Количество товаров: <br>
						<label for="count" class="field prepend-icon">
							<input id="countModal" type="text" name="count" value="  " placeholder="Количество" class="gui-input">
							<label for="countModal" class="field-icon"><i class="fas fa-calculator"></i></label>
						</label>
					</div>
				</div>

	     	 <div class="panel-footer">
					<input type="hidden" name="id" value="  " id="id_modal">
					<input type="hidden" name="sales_id" value="  " id="sale_id_modal">
					<input type="button" class="button btn-success" value="Отправить" id="modalComplBtn" data-dismiss="modal">
					<input type="button" class="button btn-success" value="Отправить" id="modalComplBtn2" onclick="order_send_mas_ajax();" data-dismiss="modal">
				</div>

	    </div>
	  </div>
	  </form>
	</div>


	<div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="exampleModalLabel">Удалить товар</h5>
		</div>
		<div class="modal-body">
		  Вы действительно хотите удалить товар?
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal" id="delModalBtn" onclick="delTov(this);">Удалить</button>
		  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		</div>
	  </div>
	</div>
  </div>

	<div class="modal fade" id="help_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="help_modal_header"></h5>
		</div>
		<div class="modal-body" id="help_modal_body">
		  
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="select_help_modal();" id="help_modal_btn"></button>
		  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		</div>
	  </div>
	</div>
  </div>';

	print_modal_supply_detail();

	require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
	ndr_init();

	$adminTpl->footIncludes[] = '<script src="usr/modules/supply/admin/asset/js/supplyDetail.js?'.time().'"></script>';

	$adminTpl->admin_foot();
}

function ajax_detail_head($id){
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
	$rows_status = array();
	$rows_status[0] = 'Менеджеры';
	$rows_status[1] = 'Ожидает закупки';
	$rows_status[2] = 'Закуплено';

	$order = query_detail_result($id);

	$pp = $db->query("SELECT c.*, u.surname as surname, u.name as name, u.ochestvo as ochestvo FROM `" . DB_PREFIX . "_comments_admin` as c LEFT JOIN `" . DB_PREFIX . "_users` as u ON(c.uid = u.id) WHERE c.module = 'sales' AND c.module_id = '" . $order['slid'] . "'");
	$countComm = $db->numRows($pp);

?>
	<div class="col-md-4">

		<div class="element-groups-lg-custom wizard-options" id="ajaxLoadBtn">
			<div class="ph10">
				<a href="javascript:void(0);" class="holder-style p15 text-center" id="awtw"><span class="fa fa-handshake holder-icon"></span><br> Пересчитать <br> товары</a>
			</div>
			<div class="ph10">
				<a href="javascript:void(0);" onclick="print_modal(<?php echo $id; ?>);" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print holder-icon"></span><br> Распечатать <br>заявку</a>
			</div>

			<?php
			if ($order["status"] == 1) {
			?>
			<div class="ph10">
				<a href="javascript:void(0);" onclick="close_supply_modal(<?php echo $id; ?>);" class="holder-style p15 text-center holder-active"><span class="far fa-times-circle holder-icon"></span><br> Закрыть <br>заявку</a>
			</div>
			<?php
			}
			?>

			<?php
			if ($order["status"] == 0) {
			?>
				<div class="ph10">
					<a href="javascript:void(0);" onclick="end_manager_modal();" class="holder-style p15 text-center holder-active"><span class="fas fa-flag-checkered holder-icon"></span><br> Завершить <br>заявку</a>
				</div>
			<?php
			}
			?>

		</div>
	</div>
	<div class="modal fade" id="awtwModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Согласовать со складом?</h5>
				</div>
				<div class="modal-body">
					Вы действительно хотите отправить на согласование?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal" id="awtwSaveBtn" data-id="'.$id.'">Отправить</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-2">
		<small>Контрагент:</small><br />
		<b><?php echo $order['oname']; ?></b>&nbsp;&nbsp;
		<a target="_blank" href="/administration/module/organizations/edit/<?php echo $order['oid']; ?>"><span class="fa fa-external-link"></span></a>
		<br /><br />
		<small>Постановщик:</small><br />
		<b><?php echo shortUserName($order['uid']); ?></b><br /><br />
	</div>

	<div class="col-md-2">
		<small>Дата подачи заявки:</small><br />
		<b><?php echo date("d.m.Y H:i:s", strtotime($order['date'])); ?></b><br /><br />
		<small>Статус:</small><br />
		<b><?php echo $rows_status[$order['status']]; ?></b>
	</div>

	<div class="col-md-2">
		<small>Заказ:</small><br />
		<b><a href="/administration/module/sales/order/<?php echo $order['slid']; ?>">№<?php echo $order['numb']; ?></a></b><br /><br />
		<small>Объект:</small><br />
		<b><a target="_blank" href="/administration/object/view/<?php echo $order["obj_id"]; ?>"><?php echo $order['obj_name']; ?></a></b><br /><br />
	</div>

	<div class="col-md-2">
		<?php
			if(!empty($order["org"])){
				$orgInfo = getOrgInfo($order["org"]);
		?>
			<small>Организация:</small><br />
			<b><?php echo $orgInfo["organizations"]["name"]; ?></b><br /><br />
		<?php
			}
		?>
		<small>Комментарий:</small><br />
		<b><a href="javascript:void(0);" onclick="modal_o('#modal-comm'); ajaxGet('/administration/module/sales/ajax/comments_get/<?php echo $order['slid']; ?>', 'sale_comments_list');">Посмотреть комментарии</a></b> <span class="badge badge-danger"><?php echo $countComm; ?></span><br /><br />

		<div id="modal-comm" class="popup-basic bg-none mfp-with-anim mfp-hide">
			<form id="sale_comments" action="javascript:void(null);" onsubmit="ajax_comments_send('sale_comments', '/administration/module/sales/commSave/<?php echo $order['slid']; ?>');">
				<div class="panel">
					<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарии<span></div>
					<div class="panel-body">
						<h3 class="mt5">Комментарии:</h3>
						<div id="sale_comments_list" style="max-height: 300px; overflow-y: scroll;">';



							echo '</div>
						<hr class="short alt">
						<textarea class="form-control" rows="5" name="comm" id="comm" placeholder="Комментарий..." required></textarea>

					</div>
					<div class="panel-footer">
						<input type="submit" value="Добавить комментарий" class="btn btn-success">
					</div>
				</div>
			</form>
		</div>

	</div>

<?php
} 

function ajax_detail_list($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;

	$queryO  = $db->query("SELECT sales_id FROM " . DB_PREFIX . "_supply WHERE id = '" . $id . "'");
	$order = $db->getRow($queryO);

	$query = \Supply\Support\SupplyDetailSupport::detail_query(["id" => $id]);

	$status = [
				'<span class="far fa-circle text-danger fa-md" data-toggle="tooltip" title="Ожидает распределения"></span>',
				'<span class="far fa-clock text-success fa-md" data-toggle="tooltip" title="Товар заказан"></span>',
				'<span class="far fa-clock text-warning fa-md" data-toggle="tooltip" title="Товар заказан не полностью"></span>',
			];

	if ($db->numRows($query) > 0) {
		$i = 0;
		while ($content = $db->getRow($query)) {
			$i++;
			$flag = false;

			$sod_count = empty($content["sod_count"]) ? 0 : $content["sod_count"];

			switch ($content['status']) {
				default:
					$status_icon = '<span class="far fa-clock text-warning fa-md" data-toggle="tooltip" title=""></span>';
				break;
				case 1:
				case 4:
					$status_icon = $status[0];
					break;
				case 2:
					$status_icon = '<span class="far fa-clock text-warning fa-md" data-toggle="tooltip" title=""></span>';
					break;
				case 3:
					$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
					break;
				case 5:
					$status_icon = $status[1];
					break;
				case 7:
					$status_icon = '<span class="fa fa-archive text-success fa-md" data-toggle="tooltip" title="Складская позиция"></span>';
					break;
				case 8:
					$status_icon = '<span class="fa fa-archive text-danger fa-md" data-toggle="tooltip" title="Складская позиция1"></span>';
					break;
				case 9:
					$status_icon = '<span class="fa fa-archive text-warning fa-md" data-toggle="tooltip" title="Складская позиция2"></span>';
					break;
				case 10:
					$status_icon = '<span class="fas fa-balance-scale text-danger fa-md cp" data-toggle="tooltip" title="Измененная позиция" onclick="iSeeThisFuck(' . $content["id"] . ');"></span>';
					$flag = true;
					break;
				case 11:
					$status_icon = '<span class="fas fa-trash-alt text-danger fa-md cp" data-toggle="tooltip" title="Удаленная позиция" onclick="iSeeThisFuck(' . $content["id"] . ', 13);"></span>';
					$flag = true;
					break;
				case 12:
					$status_icon = '<span class="fas fa-exchange-alt text-danger fa-md cp" data-toggle="tooltip" title="Замененная позиция" onclick="iSeeThisFuck(' . $content["id"] . ', 14);"></span>';
					$flag = true;
					break;
				case 13:
					//iSeeThiFuck -> 11 
					$status_icon = '<span class="fas fa-trash-alt text-danger fa-md" data-toggle="tooltip" title="Удаленная позиция"></span>';
					break;
				case 14:
					//iSeeThiFuck -> 12
					$status_icon = '<span class="fas fa-exchange-alt text-danger fa-md" data-toggle="tooltip" title="Замененная позиция" ></span>';
					break;
				case 15:
					$status_icon = $status[2];
					break;
			}

			switch($content['status']){
				case 1:
				case 4:
				case 5:
				case 15:
					if($sod_count == 0){
						$status_icon = $status[0];
					} else if($sod_count >= $content["count"]){
						$status_icon = $status[1];
					} else {
						$status_icon = $status[2];
					}
				break;
			}

			$turn1 = $db->query("SELECT * FROM `" . DB_PREFIX . "_sales_detail` WHERE id = '" . $content['sdid'] . "'");
			$turn1Row = $db->getRow($turn1);

			$ppp = \Price::getPrice($content["cid"]);

			$datePrice = empty($ppp->price_purchase_detail->price->date) ? 'Отсутствует' : date("d.m.Y", strtotime($ppp->price_purchase_detail->price->date));

			$ndr_view = get_ndr(["cid" => $content["cid"], "stock_id" => "1", "module" => "sales", "pid" => null, "returnType" => "view"]);

			echo '
			<tr ' . ($flag ? 'style="border: 4px red dashed;"' : '') . '>
				<td data-toggle="tooltip" title="id: ' . $content['id'] . '; cid: '.$content["cid"].'"><span class="pd-l-sm"></span>' . $i . '</td>
				<td>' . $content['s_sku'] . '</td>
				<td>' . ee($content['s_name']) . '</td>
				<td>' . $content['cm_name'] . '</td>
				<td class="count">' . $content['count'] . '</td>	
				<td class="count_send">' . $sod_count . '</td>			
				<td>';
			$attachUrl = $ppp->price_purchase_detail->price->attach->url ?? null;
			if (!empty($attachUrl)) {
				echo '<b><a target="_blank" href="/download/' . $attachUrl . '">' . number_format($ppp->price_purchase_, 2, '.', ' ') . ' руб.</a></b><br>' . $datePrice;
			} else {
				echo '<b>' . number_format($ppp->price_purchase_, 2, '.', ' ') . ' руб.</b><br>' . $datePrice;
			}
			echo '</td>				
				<td>' . get_unit('data', $content['s_unit'], 'sname') . '</td>		
				<td onclick="view_ndr_modal(' . $content["cid"] . ')" class="ndr">' . $ndr_view . '</td>	
				<td>' . (!empty($turn1Row['comment']) ? '<span style="cursor: pointer;" onclick="modal_o(\'#modal-comm-' . $turn1Row['id'] . '\')" class="text-center">' . short(20, $turn1Row['comment']) . '</span>
				<div id="modal-comm-' . $turn1Row['id'] . '" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарий</span></div>
						  <div class="panel-body">
							<h3 class="mt5">Комментарий:</h3>					
							<hr class="short alt">
							<p>' . $turn1Row['comment'] . '</p>
						  </div>
						  <div class="panel-footer text-right"></div>
						</div>
					  </div>' : '<span class="text-center">Нет комментария</span>') . '</td>					
				<td class="text-center">' . $status_icon . '</td>
				<td class="w200 text-right">				
					<div class="btn-group" >';
			
			$del_btn = '';
			if($sod_count > 0){
				$str1 = '<li><a href="javascript:void(0);" onclick="viewSupplyOrderFunc('.$content['id'].');" class="viewSupplyOrderBtn">Перейти в счет</a></li>
				<li><a href="javascript:void(0)" class="removeOnPassBtn" onclick="removeOnPassFunc('.$content['id'].');">Удалить из счета</a></li>';
			} else {
				if ($content["status"] != 7 && $content["status"] != 9) {
					$str1 = '<li><a href="javascript:void(0)" class="warehousePosition" onclick="$(\'#viewWarehousePosition\').modal(\'show\'); $(\'#warehousePositionBtn\').data(\'id\', ' . $content['id'] . ');" data-id="' . $content['id'] . '">Складская позиция</a></li>';
					$del_btn = '<li><a href="javascript:void(0);" onclick="delModal(' . $content['id'] . ');">Отметить на удаление</a></li>';
				} else {
					$str1 = '';
				}
			}
			$str2 = '';
			if ($content["status"] ==  7) {
				$str2 = '<li><a href="javascript:void(0);" class="warehousePositionDel" onclick="warehousePositionDel(' . $content['id'] . ');">Не складская позиция</a></li>';
			}

			echo '<button type="button" class="btn btn-xs btn-primary makeOrder" data-id="' . $content['id'] . '" data-salesid="' . $order['sales_id'] . '">Сделать заказ</button>
			<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
			<ul role="menu" class="dropdown-menu">											
				' . $del_btn . $str1 . $str2 . '
			</ul>';

			echo '</div>';

			echo '</td>
				<td class="w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['id'] . '" type="checkbox" name="checks[]" value="' . $content['id'] . '" class="checks">
						<label for="checkbox' . $content['id'] . '"></label>
					</div>
				</td>
			</tr>';
		}
	} else {
		echo '<tr><td colspan="12">Нет товаров</td></tr>';
	}
}

function supply_order_modal_query($id){
	global $db;
	return $db->query("SELECT sod.id, sod.supply_o, o.name as o_name, sod.count, so.numb 
	FROM `".DB_PREFIX."_supply_detail` as sd 
	LEFT JOIN `".DB_PREFIX."_supply_order_detail` as sod ON(sd.id = sod.supply_d) 
	LEFT JOIN `".DB_PREFIX."_supply_order` as so ON(so.id = sod.supply_o) 
	LEFT JOIN `".DB_PREFIX."_organizations` as o ON(o.id = so.oid) 
	WHERE sd.id = {$id} AND sod.deleted_at IS NULL");
}

function supply_order_modal_view($id){
	global $db;
	$query = supply_order_modal_query($id);
	$result = [];
	if($db->numRows($query) > 0){
		while ($r = $db->getRow($query)) {
			$text = supply_order_modal_text($r);
			$result[] = <<<HTML
				<li><a target="_blank" href="administration/module/supply/order_detail/{$r['supply_o']}">{$text}</a></li>
HTML;
		}
	}
	return implode("", $result);
}

function supply_order_modal_delete($id){
	global $db;
	$query = supply_order_modal_query($id);
	$result = [];
	if($db->numRows($query) > 0){
		while ($r = $db->getRow($query)) {
			$text = supply_order_modal_text($r);
			$result[] = <<<HTML
				<li style="list-style-type: none;">
					<div class="checkbox">
						<label onselectstart="return false;">
							<input type="checkbox" value="{$r['id']}" class="removeCheck">{$text}
						</label>
					</div>
				</li>
HTML;
		}
	}
	return implode("", $result);
}

function supply_order_modal_text($r){
	return "СЧЕТ ОТ ПОСТАВЩИКА №{$r["numb"]} ({$r["o_name"]})(количество: {$r["count"]})";
}

function ajax_order_send()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if (isset($_POST['id'], $_POST['count'])) {
		$id = intval($_POST['id']);
		$count = $_POST['count'];
		$comment = isset($_POST['comment']) ? $_POST['comment'] : "";
		$order_numb = isset($_POST['order_numb']) ? intval($_POST['order_numb']) : 0;
		$new_order = isset($_POST['new_order']) ? intval($_POST['new_order']) : 0;
		orderSend($id, $count, $comment, $order_numb, $new_order);
	}
}

function ajax_order_send_mas()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if (isset($_POST['id'])) {
		$id = $_POST['id'];
		$comment = isset($_POST['comment']) ? $_POST['comment'] : "";
		$order_numb = isset($_POST['order_numb']) ? intval($_POST['order_numb']) : 0;
		$new_order = isset($_POST['new_order']) ? intval($_POST['new_order']) : 0;

		if (is_array($id) && count($id) > 0) {
			$id_str = implode(", ", $id);
			// $query = $db->query("SELECT id, count, count_send FROM " . DB_PREFIX . "_supply_detail WHERE id IN (" . $id_str . ")");
			$query = \Supply\Support\SupplyDetailSupport::detail_query(["detail_id" => $id_str]);
			if ($db->numRows($query) > 0) {
				while ($r = $db->getRow($query)) {
					$l_id = $r["id"];
					$count = $r["count"] - $r["sod_count"];
					if($count > 0){
						$result = orderSend($l_id, $count, $comment, $order_numb, $new_order);
						$order_numb = $result["supply_order"];
					}
				}
			}
		}
	}
}

function create_excel_detail($id)
{
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $dn_array;
	require_once(ROOT . 'usr/plugins/PHPExcel.php');
	require_once(ROOT . 'usr/plugins/PHPExcel/IOFactory.php');
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');

	$queryO  = $db->query("SELECT s.oid, o.sales_id, obj.name, s.numb FROM " . DB_PREFIX . "_supply as o LEFT JOIN " . DB_PREFIX . "_sales as s on(o.sales_id=s.id) LEFT JOIN " . DB_PREFIX . "_object as obj on(s.object=obj.id) WHERE o.id = '" . $id . "'");
	$order = $db->getRow($queryO);
	/*
	$obj = '';
	if(!empty($order['name']))
	{
		$obj = '_'.$order['name'];
	}
	*/
	$tpl_name = 'other';
	$storage = '';
	if ($order['oid'] == '70') {
		$tpl_name = 'wilo';
		$storage = 'Забор Со склада  Ногинска';
	}

	$combine = isset($_GET["combine"]) ? true : false;

	$objPHPExcel = $objReader->load(ROOT . '/files/tpl/supply/' . $tpl_name . '.xlsx');
	$query = $db->query("SELECT s.* FROM " . DB_PREFIX . "_supply_detail as s LEFT JOIN " . DB_PREFIX . "_catalog as c on(c.id=s.cid) WHERE s.sid = '" . $id . "' AND s.status != '0'");
	if ($db->numRows($query) > 0) {
		$baseRow = 8;
		$row_count = 0;
		$i = 0;
		$result = [];
		$key_sync = [];
		while ($content = $db->getRow($query)) {
			if ($combine && array_key_exists($content['cid'], $key_sync)) {
				$result[$key_sync[$content['cid']]]["count"] += $content['count'];
			} else {
				$result[] = [
					"s_sku" => $content['s_sku'],
					"s_name" => htmlspecialchars_decode($content['s_name'], ENT_QUOTES),
					"count" => $content['count'],
					"storage" => $storage,
					"unit" => $content['s_unit'],
				];
			}

			if (!array_key_exists($content['cid'], $key_sync)) {
				$key_sync[$content['cid']] = $i;
			}
			$i++;
		}

		foreach ($result as $val) {
			$row_count++;
			$row = $baseRow + $row_count;
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $row_count)
				->setCellValue('B' . $row, $val['s_sku'])
				->setCellValue('C' . $row, $val['s_name'])
				->setCellValue('D' . $row, $val['count'])
				->setCellValue('E' . $row, get_unit('data', $val['unit'], 'sname'))
				->setCellValue('F' . $row, $val['storage']);
		}
	}
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$dir = ROOT . 'files/supply/' . $id;
	if (!file_exists($dir)) {
		mkdir($dir, 0777);
	}
	//$file_url= 'files/supply/'.$id.'/Заказ_'.$order['sales_id'].mb_strimwidth($obj, 0, 100).'_'.time().'.xlsx';
	$file_url = 'files/supply/' . $id . '/Заказ_' . $order['numb'] . '_' . time() . '.xlsx';
	$objWriter->save(ROOT.$file_url);
	header('Location: https://atri-energo.ru/' . $file_url);
}

function supply_end_manager($id){
	global $db;
	$db->query("UPDATE `" . DB_PREFIX . "_supply` SET `status` = '1' WHERE `id` = " . $id . " LIMIT 1");
	$status = update_status_supply_detail($id);
	if($status == 1){
		$uid_array = [];
		$uid_array = getGroup(["group" => "supply"]);
		// $uid_array[] = 33;
		$attr = [];
		$attr['uid'] = $uid_array;
		$attr['title'] = 'Заявка на закупку, доделать!';
		$attr['text'] = 'Поступила заявка на закупку, нажмите чтобы посмотреть и доделать!';
		$attr['icon'] = config('app.url').'/files/push/new_order.png';
		$attr['url'] = config('app.url').'/administration/module/supply/detail/'.$id;
		$attr['pNotif'] = true;
		$attr['pPush'] = true;
		$attr['module'] = "supply";
		$attr['pid'] = $id;
		sendAllNotif($attr);
	}
}