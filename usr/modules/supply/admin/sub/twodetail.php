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

function get_query_order($id){
	global $db;
	$query = $db->query("SELECT s.*, o.name as oname, o.id as oid, sl.id as slid, sl.numb as sl_numb, obj.name as obj_name, obj.id as obj_id FROM ".DB_PREFIX."_supply as s LEFT JOIN JMY_sales as sl on(sl.id=s.sales_id) LEFT JOIN ".DB_PREFIX."_organizations AS o on(sl.oid=o.id) LEFT JOIN ".DB_PREFIX."_object as obj ON(obj.id = sl.object) WHERE s.id = '" . $id . "'"); 
	$order = $db->getRow($query);
	return $order;
}

function twodetail($id) 
{
global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	
	  	
		  
	$order = get_query_order($id);

	$adminTpl->admin_head('Заявка на SOS №'.module_perfix('supply', $id));								
	$adminTpl->js_code[] = '$("#points").spinner();
	  $(".select2-single").select2();';
	echo '
	<style>
		.cp{
			cursor: pointer;
		}
	</style>
	<input type="hidden" value="'.$id.'" id="mainId">
	<section id="content" class="animated fadeIn">
		<div class="text-center">            
              <p class="lead">ЗАЯВКА НА SOS №'.module_perfix('supply', $id).'</p>
        </div>	
		<div class="row" id="admin-form">			
			<div class="panel mb25 mt5">										
				<div class="panel-body p20 pb10">									
					<div class="section row mbn admin-form">
						<div class="col-md-12 pl15">
							<div class="section row mb15" id="dev_head">';
									two_ajax_detail_head($id);
								echo '</div>												
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
					ajaxGet("'.ADMIN.'/module/supply/two_ajax/detail_list/'.$id.'", "dev_list");					
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
							echo '<button type="button" class="btn btn-xs btn-system refresh"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
						</div>

					</div>	
              </div>
              <div class="panel-body pn table-responsive"> 
				<form method="POST" onsubmit="return false;" id="tablesForm">  
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w50">Артикул</th>
							<th class="w200">Наименование</th>	
                            <th class="w50">Производитель</th>							
							<th class="w50">Кол-во</th>
							<th class="w50">Ед.</th>
							<th class="w100">Закупочная цена</th>
							<th class="w100" title="Наличие / Доступно / Резерв">Н/Д/Р</th>	
							<th class="w50">Поставщик</th>	
							<th class="w40">Счет</th>	
							<th class="w50">Дата отгрузки</th>					
                            <th class="w50">Дата поставки на склад</th>
							<th class="w100 text-center">Цена (С НДС)</th>
							<th class="w50 text-center">Сумма</th>
							<th class="w50 text-center">' . $lang['status'] .'</th>
							<th class="w50 text-center">Действия</th>
							<th class="w20 text-right">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); dis_btn(); return true;">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
					<tbody id="dev_list">';
					
					two_ajax_detail_list($id);

					echo '</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="15">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">
								<option value="mas_date">Массовая дата</option>
								<option value="mas_oid">Поставщик</option>
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" onclick="act_doit();" id="sub" value="' .  $lang['doit'] . '" disabled><span class="pd-l-sm"></span>							
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
	
	
	<div class="modal fade" id="billModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Товары без счетов</h5>
			</div>
			<div class="modal-body">
			<form onsubmit="return false;" id="billModalForm">
				<div id="billModalList"></div>
				<div>';
					$attribute = [];
					$attribute["name"] = "1";
					$attribute["script"] = true;
					$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
					$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
					echo uploaderMaster("css", $attribute);
					echo uploaderMaster("upload", $attribute);
					echo uploaderMaster("view", $attribute);
				echo '</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="billModalAdd" data-dismiss="modal" disabled>Прикрепить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="removeBillAllModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Удалить счет</h5>
			</div>
			<div class="modal-body">
				Вы действительно хотите удалить счет <span id="removeBillAllModalText"></span>? 
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="removeBillAllModalBtn">Удалить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="removeBillModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Редактирование счета</h5>
			</div>
			<form onsubmit="return false;" id="removeBillModalForm">
				<div class="modal-body" id="removeBillModalBody">

				</div>
			</form>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="removeBillModalBtn" disabled>Удалить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="confirmExecutionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Завершить заявку</h5>
			</div>
			<div class="modal-body">
				Вы действительно хотите завершить заявку? 
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="" onclick="confirmExecution();">Завершить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="print_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Печать</h5>
			</div>
			<div class="modal-body">
				<table class="table list-table">
				<tbody>
				  <tr>
					<td onclick="window.open(\'/administration/module/supply/create_excel_detail/'.$id.'\',\'_blank\'); return false;"  style="cursor: pointer;"><span class="icon 	text-alert far fa-receipt"></span>Распечатать заказ</td>
					<td class="text-right">
						<a href="/administration/module/supply/create_excel_detail/'.$id.'" target="_blank">XLS</a> 
					</td>
				  </tr>
				  <tr>
					<td onclick="window.open(\'/administration/module/supply/create_excel_detail/'.$id.'&combine=true\',\'_blank\'); return false;"  style="cursor: pointer;"><span class="icon 	text-alert far fa-receipt"></span>Распечатать заказ с объединением позиций</td>
					<td class="text-right">
						<a href="/administration/module/supply/create_excel_detail/'.$id.'&combine=true" target="_blank">XLS</a> 
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

	<div class="modal fade" id="mas_date_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Массовая дата</h5>
			</div>
			<div class="modal-body">
				<div>Выберите дату</div>
				<input type="date" class="form-control" id="mas_date_inp">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="" onclick="set_mas_date();">Установить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>

	<div class="modal fade admin-form" id="oid_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Установка поставщика</h5>
			</div>
			<div class="modal-body">
				<div>Выберите поставщика</div>';
				oSelect("jsAdminTpl", "oid", null, "getOrganizations");
				oSelect("select", "oid", null, "getOrganizations");
			echo '</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="" onclick="change_oid();">Установить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>
	
	';

	require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");

	ndr_init();
    
	$adminTpl->footIncludes[] = js_all_func();
	$adminTpl->footIncludes[] = '<script src="/usr/modules/supply/admin/asset/js/twodetail.js?'.time().'"></script>';
    
	$adminTpl->admin_foot();
} 

function two_ajax_detail_head($id){
	global $adminTpl, $core, $db;
	$order = get_query_order($id);
	$rows_status = [];
	$rows_status[1] = 'Ожидает';
	$rows_status[2] = 'Завершена';

	echo '<div class="col-md-3">
	<div class="element-groups-lg-custom wizard-options" id="">';
	if($order["status"] === "1"){
		echo '<div class="ph10">
			<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" id="confirmExecution" onclick="confirmExecutionModal(); "><span class="fas fa-check-circle holder-icon"></span><br> Подтвердить  <br>выполнение</a>
		</div>';
	}
		echo '<div class="ph10">
			<a onclick="print_modal();" href="javascript:void(0);" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print holder-icon"></span><br> Распечатать  <br>заявку</a>
		</div>
		<div class="ph10">
			<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" onclick="'.($order["status"] == 1 ? "error_modal_end()" : "auto_create_supply_modal();").'"><span class="fas fa-magic holder-icon"></span><br> Создать<br> счёт</a>
		</div>
	</div>
</div>

<div class="col-md-2">
	<small>Контрагент:</small><br/>
	<b>'.$order['oname'].'</b>&nbsp;&nbsp;				
	<a target="_blank" href="'.ADMIN.'/module/organizations/edit/'.$order['oid'].'"><span class="fa fa-external-link"></span></a>
	<br/><br/>
	<small>Ответственный:</small><br/>
	<b>'. shortUserName($order['uid']).'</b><br/><br/>
</div>
<div class="col-md-2">
	<small>Дата подачи заявки:</small><br/>
	<b>'. date("d.m.Y H:i:s", strtotime($order['date'])) .'</b><br/><br/>
	<small>Дата завершения заявки:</small><br/>
	<b>'. (empty($order['date_end']) ? 'Заявка еще не завершена!' : date("d.m.Y H:i:s", strtotime($order['date_end']))).'</b><br/><br/>								
</div>
<div class="col-md-2">
	<small>Заказ:</small><br/>
	<b><a href="/administration/module/sales/order/'.$order["slid"].'">№'.  $order['sl_numb']  .'</a></b><br/><br/>
	<small>Объект:</small><br/>
	<b><a href="/administration/object/view/'.$order["obj_id"].'">'.  $order['obj_name']  .'</a></b><br/><br/>
</div>
<div class="col-md-2">
	<small>Статус:</small><br/>
	<b>'.  $rows_status[$order['status']] .'</b><br/><br/>
</div>
<div class="col-md-2">
	<small>Счет поставщика:</small><br/>
	<button class="btn btn-link btn-sm addBill p0"><i class="fas fa-plus-square mr5"></i>Добавить</button>
	<div id="billList"></div>
</div>';
}

function two_ajax_detail_list($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
	//$page = init_page();
	//$cut = ($page-1)*$admin_conf['num'];	
	$query = $db->query("SELECT s.*, m.name, sales_id, sup.status as sup_status, o.name as o_name 
	FROM `".DB_PREFIX."_supply_detail` as s 
	LEFT JOIN `JMY_supply` as sup on(s.sid=sup.id) 
	LEFT JOIN `".DB_PREFIX."_catalog` as c on(c.id=s.cid) 
	LEFT JOIN `".DB_PREFIX."_catalog_manufacture` as m on(c.man=m.id) 
	LEFT JOIN `".DB_PREFIX."_organizations` as o on(o.id=s.oid) 
	WHERE s.sid = '".$id."' AND s.status != '0' AND s.deleted_at IS NULL");
	
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

			$ndr_view = get_ndr(["cid" => $content["cid"], "stock_id" => "1", "module" => "sales", "pid" => null, "returnType" => "view"]);

			$priceFunc = \Price::getPrice($content["cid"]);
			$price = $priceFunc->price_purchase_;

			$unit = get_unit('data', $content['s_unit'] ,'sname');

			$oid = empty($content["oid"]) ? '<span class="text-danger oid" data-oid="">Не указан</span>' : '<span class="oid" data-oid="'.$content["oid"].'">'.$content["o_name"].'</span>';

			echo '
			<tr id="tr_'.$content['id'].'" data-id="'.$content['id'].'" class="tr_elem">
				<td data-toggle="tooltip" title="' . $content['id'] . '"><span class="pd-l-sm"></span>' . $i . '</td>
				<td>' . $content['s_sku'] . '</td>
				<td>' . ee($content['s_name']) . '</td>
				<td>' . $content['name'] . '</td>
				<td class="count">' . $content['count'] . '</td>				
				<td>' . $unit . '</td>	
				<td>'.number_format($price,2,'.',' ').' руб.</td>	
				<td class="ndr" onclick="view_ndr_modal('.$content["cid"].');">'.$ndr_view.'</td>					
				<td class="">'.$oid.'</td>					
				<td class="bill"><span class="far fa-circle fa-md '.(is_null($content["sos_bill_aid"]) ? 'text-danger' : 'text-success').'"></span></td>
				<td class="'.($content["sup_status"] === "1" ? 'date_ship cp' : '').'" data-field="date_ship">
				'.(empty($content["date_ship"]) ? 'Отсутствует' : date("d.m.Y", strtotime($content["date_ship"]))).'
				</td>
				<td class="'.($content["sup_status"] === "1" ? 'date_post cp' : '').'" data-field="date_post">
				'.(empty($content["date_post"]) ? 'Отсутствует' : date("d.m.Y", strtotime($content["date_post"]))).'
				</td>
				<td class="'.($content["sup_status"] === "1" ? 'sos_price cp' : '').' text-center">'.(is_null($content["sos_price"]) ? 'Отсутствует' : number_format($content["sos_price"],2,'.',' '). ' руб.').'</td>
				<td class="'.($content["sup_status"] === "1" ? 'sos_total cp' : '').' text-center" onclick="'.($content["sup_status"] === "1" ? 'sos_total_inp(this, '.$content['id'].');' : '').'">'.(is_null($content["sos_price"]) ? 'Отсутствует' : number_format(($content["sos_price"] * $content['count']),2,'.',' '). ' руб.').'</td>
				<td class="text-center status"><span class="far fa-circle fa-md '.($content["status"] === "1" ? "text-danger" : ($content["status"] === "2" ? "text-warning" : "text-success")).'"></span></td>
				<td>
					<div class="btn-group">
						<button type="button" class="btn btn-danger btn-xs" onclick="delete_item('.$content['id'].');">Удалить</button>
					</div>
				</td>
				<td class="w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['id'] . '" type="checkbox" name="checks[]" value="' . $content['id'] . '" class="checks">
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

function two_ajax_order_data($id){
    
}

function two_ajax_order_device_add()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['vdid'])&&$_POST['vdid']!="0"))
	{
		$id = intval($_POST['id']);
		$vdid = intval($_POST['vdid']);
		$query_ver = $db->query("SELECT id, iprice, oid FROM ".DB_PREFIX."_ver WHERE id = '". $id."' LIMIT 1");
		$ver = $db->getRow($query_ver);
		$oid = $ver['oid'];		
		$query_stable = $db->query("SELECT id, name FROM ".DB_PREFIX."_ver_device WHERE id = '". $vdid ."' LIMIT 1");
		$device_stable = $db->getRow($query_stable);	
		$query_price = $db->query("SELECT price FROM ".DB_PREFIX."_ver_device_price WHERE vdid = '".$device_stable['id']."' AND oid = '".$oid."' LIMIT 1");
		if(($db->numRows($query_price) > 0)&&($ver['iprice'])) 
		{
			$rprice = $db->getRow($query_price);
			$stable_price = $rprice['price'];
		}
		else
		{
			$query_stable_pr = $db->query("SELECT price FROM ".DB_PREFIX."_ver_device_price WHERE vdid = '".$device_stable['id']."' AND oid = '0' LIMIT 1");
			$stable_pr = $db->getRow($query_stable_pr);	
			$stable_price = $stable_pr['price'];			
		}		
		$sum = intval($_POST['sum']);
		$stable_name = $device_stable['name'];
		$model = $_POST['model'];
		$number = $_POST['number'];
		$dn = intval($_POST['dn']);
		if($db->query("INSERT INTO `" . DB_PREFIX . "_ver_detail` ( `id` , `vid`, `vdid`, `stable_name`, `stable_price`, `sum`, `model`, `number`, `dn`, `status`) VALUES (NULL, '" . $db->safesql($id) . "', '" . $db->safesql($vdid) . "', '" . $db->safesql($stable_name) . "', '" . $db->safesql($stable_price) . "', '" . $db->safesql($sum) . "', '" . $db->safesql($model) . "', '" . $db->safesql($number) . "', '" . $db->safesql($dn) . "','1');")) 
		{
			echo '1';
		}
		else
		{
			echo '3';
		}
	}
	else
	{
		echo '2';
	}
}

function two_ajax_order_send()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$sales_id = intval($_POST['sales_id']);
		$query_s = $db->query("SELECT status FROM ".DB_PREFIX."_supply_detail WHERE id = '".$id."' LIMIT 1");
		if($db->numRows($query_s) > 0)
		{
			$sd = $db->getRow($query_s);
			if ($sd['status'] == '1')
			{
				$comment = $_POST['comment'];
				$count = isset($_POST['count']) ? intval($_POST['count']) : 0;
				$order_numb = isset($_POST['order_numb']) ? intval($_POST['order_numb']) : 0;	
				$new_order = isset($_POST['new_order']) ? intval($_POST['new_order']) : 0;	
				if ($order_numb == '0'&&$new_order != '0')
				{
					$uid = $core->auth->user_info['id'];
					$db->query("INSERT INTO `" . DB_PREFIX . "_supply_order` ( `id` , `oid` ,   `sales_id` , `uid` , `status`) VALUES (NULL, '" . $new_order . "', '" . $sales_id . "', '" . $uid . "', '1');");
					$order_numb = $db->insert_id();
				}
				$status = '2';
				$db->query("INSERT INTO `" . DB_PREFIX . "_supply_order_detail` ( `id` , `supply_o`, `supply_d`, `comment`, `count`, `status`) VALUES (NULL, '" . $db->safesql($order_numb) . "', '" . $db->safesql($id) . "', '" . $db->safesql($comment) . "', '" . $db->safesql($count) . "', '1');");
				
				$db->query("UPDATE `" . DB_PREFIX . "_supply_detail` SET `status` = '2' WHERE `id` = " . $id . " LIMIT 1 ;");
				echo 'Информация успешно сохранена!';	
			}
		}			
	}
}

function two_ajax_devicedd_edit()
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

function two_ajax_order_device_compl()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$comment = $_POST['comment'];
		$result = isset($_POST['result']) ? $_POST['result'] : 0;
		$status = ($result == 0) ? '3' : '2';
		$db->query("UPDATE `" . DB_PREFIX . "_ver_detail` SET `status` = '".$status."', `comment` = '". $db->safesql($comment) ."' WHERE `id` = " . $id . " LIMIT 1 ;");
		echo 'Информация успешно сохранена!';		
		
	}
}

function two_ajax_order_device_del()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$comment = $_POST['comment'];
		$result = isset($_POST['result']) ? $_POST['result'] : 0;
		$status = '0';
		$db->query("UPDATE `" . DB_PREFIX . "_ver_detail` SET `status` = '".$status."' WHERE `id` = " . $id . " LIMIT 1 ;");
		echo 'Прибор успешно удален!';		
		
	}
}

function twodetail_update_field_detail($id, $field, $value){
	global $db;
	if($id){
		if(is_array($id)){
			$id_ = implode(", ", $id);
		} else {
			$id_ = $id;
		}
	
		$db->query("UPDATE `".DB_PREFIX."_supply_detail` SET ".$field." = '".$value."' WHERE id IN(".$id_.")");
	}
}

function auto_create_supply($data){
	global $db;
	$id = $data["id"];
	$query = $db->query("SELECT * FROM `".DB_PREFIX."_supply` WHERE id = '".$id."' LIMIT 1");
	$supply = $db->getRow($query);
	$query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_detail` WHERE sid = '".$id."' AND oid IS NOT NULL");
	$checks = [];
	$sales_attach = [];
	$sales_rel = [];
	if($db->numRows($query) > 0){
		while($r = $db->getRow($query)){
			//id добавления первой фазы(supply_detail)
			$checks[] = $r["sdid"];
			//id прикрепленного счёта, связь по id поставщику
			$sales_attach[ $r["oid"] ] = $r["sos_bill_aid"];
			//все данные sos, связь по id продажи
			$sales_rel[ $r["sdid"] ] = $r;
		}
	}
	require_once("usr/modules/sales/admin/sub/send.php");
	//костыль для первой фазы
	$_POST["checks"] = $checks;
	//создание первой фазы
	$sid = send_order2($supply["sales_id"]);

	$query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_detail` WHERE sid = '".$sid."'");
	$supply_id = [];
	if($db->numRows($query) > 0){
		while($r = $db->getRow($query)){
			//все данные первой фазы, связь по id поставщику
			$supply_id[ $sales_rel[ $r["sdid"] ]["oid"] ][] = $r;
		}
	}

	$type_nds = 3;
	// orderSend.php
	foreach($supply_id as $key => $val){
		$order_numb = 0;
		$date_ship = "";
		foreach($val as $k => $v){
			//создание второй фазы(supply_order)
			$supply_data = orderSend($v["id"], $v["count"], '', $order_numb, $key);
			if($order_numb == 0){
				$query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_order` WHERE id = '".$supply_data["supply_order"]."' LIMIT 1");
				$sor = $db->getRow($query);
			}
			//id второй фазы
			$order_numb = $supply_data["supply_order"];
			//id detail второй фазы
			$supply_order_detail = $supply_data["supply_order_detail"];
			$price = $sales_rel[ $v["sdid"] ]["sos_price"];
			$price_data = [
				"nds_includes" => $sor["nds_includes"],
				"price" => $price,
				"count" => $sales_rel[ $v["sdid"] ]["count"],
				"nds_type" => $type_nds,
			];
			$date_ship_sos = $sales_rel[ $v["sdid"] ]["date_ship"];
			//получение total и nds
			$result = get_all_price($price_data);
			$db->query("UPDATE `".DB_PREFIX."_supply_order_detail` SET price_now = '".$price."', total = '".$result["total"]."', nds = '".$result["nds"]."', type_nds = '".$type_nds."' WHERE id = '".$supply_order_detail."' LIMIT 1");
			if(empty($date_ship) || $date_ship < $date_ship_sos){
				$date_ship = $date_ship_sos;
			}
		}
		//получение счёта из sos
		$file = attach_data_file($sales_attach[ $key ]);
		$attach_data = [
			"name" => $file["name"],
            "url" => $file["url"],
            "pub_id" => $order_numb,
            "mod" => "supply",
		];
		//добавление счёта в вторую фазу
		attach_insert_file($attach_data);
		//установка даты отгрузки
		// $db->query("UPDATE `".DB_PREFIX."_supply_order` SET date_ship = '".$date_ship."' WHERE id = '".$order_numb."' LIMIT 1");
		//автоматический подсчет общего total и nds 
		auto_supply_o_total($order_numb);
	}
}

function twodetail_auto_create_supply_check($data){
	global $db;
	$id = $data["id"];
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_supply_detail WHERE sid = '".$id."'");
	if($db->numRows($query) > 0){
		while($r = $db->getRow($query)){
			$name = $r["s_name"].'. (id: '.$r["id"].')';
			if(empty($r["oid"])){
				return 'Не указан поставщик у товара: '.$name;
			}
			if(empty($r["sos_bill_aid"])){
				return 'Не приложен счёт у товара: '.$name;
			}
			if(empty($r["date_post"])){
				return 'Не указана дата поставки у товара: '.$name;
			}
			if(empty($r["date_ship"])){
				return 'Не указана дата отгрузки у товара: '.$name;
			}
			if(empty($r["sos_price"])){
				return 'Не указана цена у товара: '.$name;
			}
		}
	} else {
		return 'Нет товаров для отправки!';
	}
}