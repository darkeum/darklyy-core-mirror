<?php

use Boot\System\Office\OfficeSupport;
use Boot\System\Office\Spreadsheet\SpreadsheetSupport;
use Sales\Models\SalesDetail;

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


function order()
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Счета поставщиков';
	$adminTpl->admin_head('Менеджер закупок | ' . $lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'supply';
	$atributs['module_open'] = 'order_detail';
	$atributs['js'] = 'setTimeout(function () {$("#jqSupplyOrder").jqGrid(\'setGridParam\',{postData:{close: \'0\'}}); $(\'#jqSupplyOrder\').trigger(\'reloadGrid\');}, 2000);';
	loadGrid('jqSupplyOrder', $lnn, $atributs, ROOT . '/usr/modules/supply/admin/grid/');
	echo '<script src="usr/modules/supply/admin/asset/js/scriptGrid.js?' . time() . '"></script>';
	$adminTpl->admin_foot();
}

function get_query_result_so($id)
{
	global $db;
	$control = \Supply\Support\SupplyOrderSupport::control_sql();
	$query = $db->query("SELECT s.*, o.name as oname, sl.id as slid, c.pay as c_pay, c.reason as c_reason, c.id as c_id, obj.name as obj_name
	FROM `" . DB_PREFIX . "_supply_order` as s
	LEFT JOIN `" . DB_PREFIX . "_organizations` AS o on(s.oid=o.id)
	LEFT JOIN `" . DB_PREFIX . "_sales` AS sl on(s.sales_id=sl.id)
	LEFT JOIN `" . DB_PREFIX . "_object` AS obj on(s.object=obj.id)
	LEFT JOIN (
		{$control}
	) AS c on(c.supply_o = s.id)
	WHERE s.id = '" . $id . "'");
	$order = $db->getRow($query);
	return $order;
}


function order_detail($id, $logistic = false)
{
	global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;

	$uid = $core->auth->user_info['id'];

	$order = get_query_result_so($id);

	if (!check_uid_org($order["org"])) {
		header("Location: " . config('app.url') . "/administration/module/supply/prohibited");
	}

	// dd($order);

	$adminTpl->js_code[] = '

                                     $(document).on("click", "#confirmRes", function(){
                                        if($("#numDoc").val() != ""){
                                            let msg = $("#tablesForm").serialize() +"&"+ $("#formMyModal").serialize();
                                            $.ajax({
                                                type: "POST",
                                                url: "' . ADMIN . '/module/supply/confirm_receipt/' . $id . '",
                                                data: msg,
                                                success: function(echoStr) {
                                                    //notif("success", "Данные отправлены!", "Проведенные товары: "+echoStr+"");
                                                    $("#myModal").modal("hide");
                                                    $("#numDoc").val("");
                                                    //console.log(echoStr);
                                                    window.open("' . config('app.url') . '/administration/module/supply/receipts_detail/"+echoStr);
                                                },
                                            });
                                        }
                                    }); ';
	$adminTpl->admin_head('Счет поставщика | Закупка №' . $order["numb"]);
	echo '<style>
        .elemList{
            cursor: pointer;
            padding-bottom: 10px;
        }
        .elemList:hover{
            background-color: #dadada;
        }
        .cp{
            cursor: pointer;
        }
    </style>
    <input type="hidden" value=0 id="counter">
    <input type="hidden" value="" id="saveId">
    <input type="hidden" value="" id="saveData">
    <input type="hidden" value="' . $logistic . '" id="logistic">
    ';
	$attribute = [];
	$attribute["name"] = "";
	$attribute["script"] = true;
	$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
	$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
	echo uploaderMaster("css", $attribute);
	echo '
	<section id="content" class="animated fadeIn">
		<div class="text-center">
              <p class="lead">СЧЕТ ОТ ПОСТАВЩИКА #' . $order["numb"] . '</p>
              <input type="hidden" value="' . $id . '" id="mainId">
        </div>

        <div class="modal" id="myModal" role="dialog">
		<form onsubmit="return false;" id="formMyModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Введите номер документа</h5>
                    </div>
                    <div class="modal-body">
					<div class="form-group">
					<label for="numDoc">
					Номер документа
					</label>
                        <input type="text" class="form-control" id="numDoc" name="numDoc" placeholder="Введите номер документа">
					</div>
					<div class="form-group">
					<label for="datePost">
					Дата поступления
					</label>
						<input type="date" class="form-control" id="datePost" name="datePost" placeholder="Введите дату поступление">
					</div>
                    <div class="form-group">
					<label for="">
					Выберите организацию
					</label>
						<select class="form-control" id="org" name="org" onchange="show_stock();">';
	$org = $db->query("SELECT o.*, org.id as orgid FROM JMY_org as org LEFT JOIN JMY_organizations as o ON(org.oid = o.id)");
	while ($r = $db->getRow($org)) {
		$select = '';
		if ($r["orgid"] == $order["org"]) $select = 'selected';
		echo '<option value="' . $r["orgid"] . '" ' . $select . '>' . $r["name"] . '</option>';
	}
	echo '</select>
					</div>
                    <div class="form-group">
					<label for="">
					Выберите склад
					</label>
						<select class="form-control" id="stock" name="stock">';
	$stock = $db->query("SELECT * FROM JMY_stock WHERE name IS NOT NULL");
	$first = true;
	while ($r = $db->getRow($stock)) {
		$select = '';
		$org_select = $order["org"];
		if ($first && $r["org"] == $org_select) {
			$first = false;
			$select = 'selected';
		}
		$hide = "";
		if ($r["org"] != $org_select) $hide = 'style="display:none;"';
		if (!empty($r["name"])) echo '<option value="' . $r["id"] . '" ' . $select . ' class="org_opt org_' . $r["org"] . '" ' . $hide . '>' . $r["name"] . '</option>';
	}
	echo '</select>
                        </select>
					</div>
					<div class="form-group">';
	echo uploaderMaster("upload", $attribute);
	echo uploaderMaster("view", $attribute);
	echo '</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="confirmRes" ondblclick="return false;">Продолжить</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
            </form>
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
					notif("success","' . $lang['info'] . '", data);
					ajaxGet("' . ADMIN . '/module/supply/ajax/order_detail_list/' . $id . '", "tov_list");
				}
			});
		}



	</script>

		<div class="row" id="admin-form">
			' . order_detail_head($id, $logistic) . '
		</div>

		<div class="modal fade" id="addFileModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title" id="exampleModalLabel">Загрузить файл</h5>
			</div>
			<form id="addFileModalForm">
			<div class="modal-body">';
	$attribute = [];
	$attribute["name"] = "3";
	$attribute["script"] = true;
	$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
	$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
	echo uploaderMaster("css", $attribute);
	echo uploaderMaster("upload", $attribute);
	echo uploaderMaster("view", $attribute);
	echo '</div>
			</form>
			<div class="modal-footer">
			  <button type="button" class="btn btn-primary" data-dismiss="modal" id="addFileModalBtn" onclick="addFileModalBtn();">Загрузить</button>
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		  </div>
		</div>
	  </div>

		<div class="modal fade" id="delFileSupply" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Удалить файл?</h5>
			  </div>
			  <div class="modal-body">
				Вы точно хотите удалить <span id="nameFile"></span>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="delFileBtn">Удалить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			  </div>
			</div>
		  </div>
		</div>

		<div class="modal fade" id="controlModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title" id="exampleModalLabel">Лист согласования</h5>
			</div>
			<div class="modal-body">';

	$sales_rel_d = sales_rel_data($id);
	$salesFlag = $sales_rel_d["salesFlag"];
	$salesControl = $sales_rel_d["salesControl"];
	$salesControlArray = $sales_rel_d["salesControlArray"];

	if ($salesFlag) {
		echo '<b>Согласование у менеджеров</b><br>';
		echo $salesControl;
		echo '<hr>';
	}
	echo '
			  <b>Согласование у руководителя</b><br>';
	if (!empty($salesControlArray)) {
		echo 'Ожидается согласования у менеджеров';
	} else {
		$queryControl = $db->query("SELECT * FROM `" . DB_PREFIX . "_control` WHERE `supply_o` = '" . $id . "'");
		if ($db->numRows($queryControl) > 0) {
			$rControl = $db->getRow($queryControl);

			//   if($core->auth->allowAccessById($uid, '8'))
			//   {
			// 	  echo '<a href="'.ADMIN.'/module/fcontrol/detail/'.$rControl["id"].'">Заявка на согласование CTR-'.$rControl["id"].'</a> --- ';
			//   }
			//   else
			//   {
			echo 'Заявка на согласование CTR-' . $rControl["id"] . ' --- ';
			//   }
			switch ($rControl["status"]) {
				case 0:
					echo '<font  color="red">Ожидает отправки на проверку</font>';
					break;
				case 1:
					echo '<font style="cursor: pointer;" color="#8925f7" data-toggle="modal" data-target="#controlModal">Ожидает согласования</font>';
					break;
				case 2:
					echo '<font style="cursor: pointer;" color="yellow" data-toggle="modal" data-target="#controlModal">Отправлен на доработку</font>';
					break;
				case 3:
					echo '<font style="cursor: pointer;" color="green" data-toggle="modal" data-target="#controlModal">Согласован</font>';
					break;
			}
		}
	}
	echo '
			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		  </div>
		</div>
	  </div>

		<div class="modal fade" id="viewReservModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Список резервов</h5>
				</div>
				<div class="modal-body" id="viewReservModalBody">

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="reconcileInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
			  <div class="modal-content">
			  <form id="reconcileInvoiceModalForm" onsubmit="return false;">
				<div class="modal-header">
				  <h5 class="modal-title" id="exampleModalLabel">Планируемая дата оплаты</h5>
				</div>
				<div class="modal-body">
					<label>Дата оплаты</label>
					<input type="date" value="' . date("Y-m-d") . '" name="date" id="fcontrol_date" class="form-control">
					<label>Комментарий</label>
					<textarea class="form-control" name="comment" placeholder="Введите комментарий"></textarea>
					<input type="hidden" value="' . $id . '" name="id">
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-primary" value="Отправить" data-dismiss="modal" onclick="send_fcontrol();">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				</div>
				</form>
			  </div>
			</div>
		</div>

		<div class="modal fade" id="receipts_view_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Поступления</h5>
		      </div>
			  <div class="modal-body" id="receipts_view_modal_body">

		      </div>
			  <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="mas_date_change_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Изменить дату</h5>
		      </div>
			  <div class="modal-body" id="receipts_view_modal_body">
			  	<input type="date" class="form-control" id="mas_date_change_modal_date">
		      </div>
			  <div class="modal-footer">
			  	<input type="button" class="btn btn-primary" value="Отправить" id="mas_date_change_modal_btn">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div class="modal fade" id="change_type_nds_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Изменить ставку НДС</h5>
		      </div>
			  <div class="modal-body">
			  	<select id="change_type_nds_modal_select" class="form-control">';
	foreach (\Price::getNds("all") as $key => $value) {
		echo "<option value='{$key}'>{$value}</option>";
	}
	echo '</select>
		      </div>
			  <div class="modal-footer">
		        <button type="button" class="btn btn-primary" id="change_type_nds_modal_button">Изменить</button>
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		      </div>
		    </div>
		  </div>
		</div>

		<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список товаров на закупку:</span>
					<div class="widget-menu pull-right" >
						<div class="btn-group">';
	$notif = "notif('primary', '" . $lang['info'] . "', '" . $lang['ajax_update_compl'] . "');";
	$victoria = new \Victoria;
	$victoria->link = "/administration/module/supply/victoria_add/{$id}";
	$bb = $victoria->button();

	$btns = $bb . '<button onclick="update_body(); ' . $notif . '" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>
							<button type="button" class="btn btn-xs btn-success" id="saveSupply">Сохранить</button>';
	echo $btns;
	echo '</div>
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
							<th class="w100">Кол-во</th>
							<th class="w50">Кол-во поступления</th>
							<th class="w50">Ед.изм.</th>
							<th class="w100 text-center" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
							<th class="w100" style="display: none;">Цена продажи</th>
							<th class="w100">Цена закупки</th>
							<th class="w100">Сумма без НДС</th>
							<th class="w100">Сумма с НДС</th>
							<th class="w100">Ставка НДС</th>
							<th class="w100">НДС</th>
							<th class="w100">Дата отгрузки</th>
							<th class="w100">Дата поставки</th>
							<th class="w50">Комментарий</th>
							<th class="w50">Заказ</th>
							<th class="w50 text-right">' . $lang['actions'] . '</th>
							<th class="w20 text-right">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" class="checks" onclick="setCheckboxes(\'tablesForm\', true); return true;">
									<label for="all"></label>
								</div>
							</th>
						</tr>
                    </thead>
                    <tbody id="tov_list">
					</tbody>
					<tfoot class="footer-menu">
                    <tr align="right">
					  <td align="right" colspan="17">
                        <nav align="right" class="text-right">
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">
								<option value="del">Удалить выбранное</option>
								<option value="mas_date_ship">Массовая установка даты отгрузки</option>
								<option value="mas_date_end">Массовая установка даты поставки</option>
								<option value="change_type_nds">Смена ставки НДС</option>
							</select>
							<input onclick="universl_sub();" name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" disabled><span class="pd-l-sm"></span>
						 </nav>
                      </td>
                    </tr>
                  </tfoot>
				</table>
                </form>
			 </div>
          </div>
        </div>
	</section>';

	$ReestrModule = new \Reestr\ReestrModule;
	$ReestrModule->model_out = \Supply\Model\SupplyOrder::class;
	$ReestrModule->module_out = "supply_order";
	$ReestrModule->pid_out = $id;
	$ReestrModule->local_script = <<<HTML
		reestr_module.update_head = function(){
			update_head();
		};
HTML;
	$ReestrModule->init();

	xeditableInit();
	if ($core->auth->user_info['id'] == 33 || $core->auth->user_info['id'] == 1) {
		//fastBar(["module" => 'supply_order_detail', "id" => $id, "update" => "tov_list", "update_url" => "/administration/module/supply/ajax/order_detail_list/".$id, "status" => 1]);
	}
	$adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/function.js?' . time() . '"></script>';
	$adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/price.js?' . time() . '"></script>';
	$adminTpl->footIncludes[] = '<script src="usr/modules/supply/admin/asset/js/script.js?' . time() . '"></script>';
	$victoria_min = new \Victoria;
	$victoria_min->link = '/administration/module/supply/victoria_add_min/${id}';
	$vm = $victoria_min->open_func();
	$adminTpl->footIncludes[] = <<<HTML
		<script>
			function change_goods_open(id){
				{$vm}
			}
		</script>
HTML;

	print_modal_supply_order();
	require_once(ROOT . "usr/modules/sales/admin/sub/ndr.php");
	ndr_init();

	$attr = [
		"module" => 'supply_order_detail',
		"id" => $id,
		"update" => "tov_list",
		"status" => 1,
		"hotkeys" => $btns,
		"update_func" => "update_body",
	];


	fastBar($attr);

	$adminTpl->admin_foot();
}

function sales_rel_data($id)
{
	global $db;
	$salesControlArray = [];
	$queryControlSales = $db->query("SELECT * FROM `" . DB_PREFIX . "_sales` WHERE `confirm_supply_order` LIKE '%\"" . $id . "\"%'");
	if ($db->numRows($queryControlSales) > 0) {
		while ($rControlSales = $db->getRow($queryControlSales)) {
			$salesControlArray[$rControlSales['id']] = 'Wait';
		}
	}
	$query_sales = $db->query("SELECT DISTINCT sl.id, sl.date, sl.object, sl.numb, o.name
	FROM `" . DB_PREFIX . "_supply_order_detail` AS sod
	LEFT JOIN `" . DB_PREFIX . "_sales_detail` AS s on(sod.sale_detail_id=s.id)
	LEFT JOIN `" . DB_PREFIX . "_sales` AS sl on(s.sales_id=sl.id)
	LEFT JOIN `" . DB_PREFIX . "_object` AS o on(o.id=sl.object)
	WHERE sod.supply_o = '" . $id . "' AND sod.deleted_at IS NULL");
	$salesRelation = '';
	$salesControl = '';
	$salesFlag = false;
	if ($db->numRows($query_sales) > 0) {
		while ($qs = $db->getRow($query_sales)) {
			if (!empty($qs['id'])) {
				$salesFlag = true;
				$qs_object = '';
				if (!empty($qs['name'])) {
					$qs_object = ' - <a href="' . ADMIN . '/object/view/' . $qs['object'] . '">' . $qs['name'] . '</a>';
				}
				$salesRelation .= '<a href="' . ADMIN . '/module/sales/order/' . $qs['id'] . '">' . $qs["numb"] . '</a> ' . $qs_object . '<br>';
				$salesControl .= '<a href="' . ADMIN . '/module/sales/order/' . $qs['id'] . '">' . $qs["numb"] . '</a> ' . $qs_object . ' --- ' . (isset($salesControlArray[$qs['id']]) ? '<font color="#8925f7">Ожидает согласования</font>' : '<font color="green">Согласован</font>') . '<br>';
			}
		}
	}
	$salesRelationFlag = true;
	if (empty($salesRelation)) {
		$salesRelation =  'Продажи отсутствуют';
		$salesRelationFlag = false;
	}

	return [
		"salesFlag" => $salesFlag,
		"salesControl" => $salesControl,
		"salesControlArray" => $salesControlArray,
		"salesRelation" => $salesRelation,
		"salesRelationFlag" => $salesRelationFlag,
	];
}

function order_detail_head($id, $logistic = false)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;

	require_once(ROOT . "usr/modules/fcontrol/admin/sub/function.php");

	$order = get_query_result_so($id);

	$edit_href = $logistic ? ADMIN . '/module/supply/editLogistics/' . $id : ADMIN . '/module/supply/edit/' . $id;

	$remove_control_send_btn = "";
	if (!empty($order["control_send"])) {
		$remove_control_send_btn = <<<HTML
		<div class="ph10">
			<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" onclick="remove_control_send_modal();">
				<span class="fas fa-bell-slash holder-icon"></span><br>Удалить<br>уведомление
			</a>
		</div>
HTML;
	}

	$is_close_order = $order["close"] == 0;
	$close_onclick = $is_close_order ? "closeOrder();" : "restoreOrder()";
	$close_order_btn_text = $is_close_order ? "Закрыть" : "Открыть";
	$close_order_icon = $is_close_order ? "fas fa-times-circle" : "fas fa-plus-circle";
	$close_order_btn = <<<HTML
		<div class="ph10">
			<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" onclick="{$close_onclick}">
				<span class="{$close_order_icon} holder-icon"></span><br>{$close_order_btn_text}
			</a>
		</div>
HTML;

	$rows_status = [];
	$rows_status[0] = 'Отменен';
	$rows_status[1] = 'Ожидает закупки';
	$rows_status[2] = 'Ожидает отправки';
	$rows_status[3] = 'Закуплено';

	$object_name = empty($order["obj_name"]) ? "Не указано" : $order["obj_name"];

	$oid_href = ADMIN . '/module/organizations/' . $order['oid'];

	$uid_name = shortUserName($order["uid"]);

	$date_create = empty($order['date_create']) ? 'Не указано!' : date("d.m.Y", strtotime($order['date_create']));
	$bill_code = empty($order['bill_code']) ? '' : $order['bill_code'];

	$org_info = getOrgInfo($order["org"]);
	$stock_name = get_stock_name($org_info["org"]["stock_id_default"]);
	$stock_data = json_encode(get_stock_data(), JSON_UNESCAPED_UNICODE);

	$date_bill = empty($order['date_bill']) ? 'Дата счета не указана!' : date("d.m.Y", strtotime($order['date_bill']));
	$date_work = empty($order['date_work']) ? 'Дата размещения не указана!' : date("d.m.Y", strtotime($order['date_work']));
	$date_ship = empty($order['date_ship']) ? 'Дата отгрузки не указана!' : date("d.m.Y", strtotime($order['date_ship']));
	$date_end = empty($order['date_end']) ? 'Дата поставки не указана!' : date("d.m.Y", strtotime($order['date_end']));

	$includesNds = ($order["nds_includes"] == 1 || $order["nds_includes"] == null) ? "checked" : "";

	$price_active = "";
	$query_price = $db->query("SELECT * FROM `" . DB_PREFIX . "_catalog_price` WHERE `module` = 'supply_order' AND `pid` = '" . $id . "' LIMIT 1");
	if ($db->numRows($query_price) > 0) {
		$pl = $db->getRow($query_price);
		$price_active_checked = $pl["active"] === '1' ? "" : "checked";

		$price_active = <<<HTML
		<label class="form-check-label" for="price_active" onselectstart="return false;" style="cursor: help;" title="При включении, цены не будут включены в общий реестр цен">
			<input class="form-check-input" type="checkbox" value="1" onchange="price_active_change({$pl['id']});" id="price_active" {$price_active_checked}> Спец. цена
		</label>
HTML;
	}

	$control_status_text = \Control\Model\Control::getStatus();

	switch ($order["control"]) {
		case 0:
			$control = '<font  color="red">' . $control_status_text[$order["control"]] . '</font>';
			break;
		case 1:
			$control = '<font style="cursor: pointer;" color="#8925f7" data-toggle="modal" data-target="#controlModal">' . $control_status_text[$order["control"]] . '</font>';
			break;
		case 2:
			$control = '<font style="cursor: pointer;" color="yellow" data-toggle="modal" data-target="#controlModal">' . $control_status_text[$order["control"]] . '</font>';
			break;
		case 3:
			$control = '<font style="cursor: pointer;" color="green" data-toggle="modal" data-target="#controlModal">' . $control_status_text[$order["control"]] . '</font>';
			break;
	}

	$pay_status_v = pay_status_view($order["c_pay"], $order["c_reason"]);

	$conToFile = order_detail_load_file($id, ["return" => true]);

	$sales_rel_d = sales_rel_data($id);
	$salesRelation = $sales_rel_d["salesRelation"];
	$salesRelationFlag = $sales_rel_d["salesRelationFlag"];
	if ($salesRelationFlag) {
		$viewReserv = "viewReserv();";
	} else {
		$viewReserv = "swal('Внимание!', 'Продажи не привязаны', 'warning');";
	}

	$comm_contr = [];
	$flag = false;
	$comment_control = json_decode($order["comment_control"], true);
	if (is_array($comment_control) && count($comment_control) > 0) {
		foreach ($comment_control as $val) {
			$u_n = shortUserName($val["uid"]);
			$date = date("d.m.Y H:i:s", strtotime($val["date"]));
			$comm_contr[] = <<<HTML
			<div class="mb20" style="background-color: #e4e4e4; border-radius: 5px; min-height: 40px; padding: 10px;">
				<b>{$u_n} ({$date}):</b>{$val["comment"]}</div>
HTML;
			$flag = true;
		}
	}
	$comm_contr_btn = '<b>' . ($flag ? "<a onclick=$('#commModal').modal('show'); href=javascript:void(0); id=comm>Комментарий</a>" : "Отсутствует") . '</b><br/><br/>';

	$comm_contr = implode("", $comm_contr);

	$relations = new \Relations;
	$data = $relations->find(["module_in" => "reestr", "module_out" => "supply_order", "pid_out" => $id]);
	$dogovor = count($data) > 0 ? \Reestr\ReestrModule::pact_template($data[0]["pid_in"]) : "Договор не выбран!";

	return <<<HTML

	<div class="panel mb25 mt5">
		<div class="panel-body p20 pb10">
			<div class="section row mbn admin-form">
				<div class="col-md-12 pl15">
					<div class="section row mb15">
						<div class="col-md-3">
							<div class="element-groups-lg-custom wizard-options">
								<div class="ph10">
									<a href="{$edit_href}" class="holder-style p15 text-center holder-active">
										<span class="fa fa-edit holder-icon"></span>
										<br> Редактировать<br>заказ
									</a>
								</div>
								<div class="ph10">
									<a class="holder-style p15 text-center" id="myCheck">
										<span class="fa fa-check-circle holder-icon"></span>
										<br> Подтвердить<br>поступление
									</a>
								</div>
								<div class="ph10">
									<a href="javascript:void(0);" onclick="print_modal({$id});" class="holder-style p15 text-center holder-active">
										<span class="fa fa-save holder-icon"></span>
										<br> Сохранить<br>заказ
									</a>
								</div>
								<div class="ph10">
									<a href="javascript:void(0);" id="reconcileInvoice" class="holder-style p15 text-center holder-active">
										<span class="glyphicon glyphicon-fire holder-icon"></span>
										<br>Согласовать<br>счет
									</a>
								</div>

								{$close_order_btn}
								{$remove_control_send_btn}

							</div>
						</div>

						<div class="col-md-2">
							<small>Поставщик:</small><br/>
							<b>{$order['oname']}</b>&nbsp;&nbsp;
							<a target="_blank" href="{$oid_href}"><span class="fa fa-external-link"></span></a>
							<br/><br/>
							<small>Объект:</small><br/>
							<b>{$object_name}</b><br/><br/>
							<small>Ответственный:</small><br/>
							<b>{$uid_name}</b><br/><br/>
							<small>Дата создания счёта:</small><br/>
							<b>{$date_create}</b><br/><br/>
							<small>Номер счета:</small><br/>
							<b id="bill_code" class="cp">{$bill_code}</b><br/><br/>
							<small>Склад отображения:</small><br/>
							<b id="stock_ndr" data-value="{$org_info['org']['stock_id_default']}">{$stock_name}</b><br/><br/>
							<input type="hidden" value='{$stock_data}' id="stock_ndr_data">
							<b><a href="javascript:void(0);" onclick="{$viewReserv}">Посмотреть резервы</a></b>
						</div>

						<div class="col-md-2">
							<small>Дата счета:</small><br/>
							<b id="date_bill" class="cp">{$date_bill}</b><br/><br/>
							<small>Дата размещения:</small><br/>
							<b id="date_work" class="cp">{$date_work}</b><br/><br/>
							<small>Дата отгрузки:</small><br/>
							<b id="date_ship" class="cp">{$date_ship}</b><br/><br/>
							<small>Дата поставки:</small><br/>
							<b id="date_end" class="cp">{$date_end}</b><br/><br/>
							<small>Организация:</small><br/>
							<b>{$org_info["organizations"]["name"]}</b><br/><br/>
                            <div class="form-check">
							  <label class="form-check-label" for="includesNds" onselectstart="return false;">
							  	<input class="form-check-input" type="checkbox" value="1" id="includesNds" {$includesNds}>Цена включает НДС
							  </label><br/><br/>

							{$price_active}

                            </div>
						</div>
						<div class="col-md-2">
                            <small>Контроль закупки:</small><br/>
							<b>{$control}</b><br/><br/>
                            <small>Оплата:</small><br/>
							<b>{$pay_status_v}</b><br/><br/>
							<small>Комментарии контроля закупок:</small><br/>
							{$comm_contr_btn}

							<div class="modal fade" id="commModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        					  <div class="modal-dialog" role="document">
        					    <div class="modal-content">
        					      <div class="modal-header">
        					        <h5 class="modal-title" id="exampleModalLabel">Комментарии</h5>
        					      </div>
        					      	<div class="modal-body">
								  		{$comm_contr}
									</div>
        					      <div class="modal-footer">
        					        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        					      </div>
        					    </div>
        					  </div>
							</div>
							<small>Договор:<i class="fas fa-plus text-success cp" onclick="reestr_module.show();"></i></small><br/>
							<b>{$dogovor}</b><br/><br/>
						</div>
						<div class="col-md-2">
							<small>Статус:</small><br/>
							<b>{$rows_status[$order['status']]}</b><br/><br/>
							<small>Документы по счету <i class="fas fa-file-upload text-success cp fs20 pl20" title="Загрузить файл" onclick="addFile();"></i></small><br/>
							<div id="conToFile">
								{$conToFile}
							</div>
							<small>Привязанные продажи:</small><br/>
							<b>{$salesRelation}</b><br/><br/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

HTML;
}

function order_detail_load_file($id, $request = [])
{
	global $db;
	$attach = "Отсутствуют<br><br>";
	$data = \Supply\Support\SupplyOrderSupport::get_attach($id);
	if (count($data) > 0) {
		$attach = '<ul class="mt10">';
		foreach ($data as $r) {
			$attach .= '<li id="li_' . $r["id"] . '">
				<a target="_blank" href="' . config('app.url') . '/download/' . $r["id"] . '">' . $r["name"] . '</a>
				<a href="javascrip:void(0);" class="ml20 removeAttach" data-id="' . $r["id"] . '" data-name="' . $r["name"] . '" style="color: red;">
					<i class="fa fa-times" aria-hidden="true"></i>
				</a>
			</li>';
		}
		$attach .= '</ul>';
	}
	if (isset($request["return"])) {
		return $attach;
	}
	echo $attach;
}

function supply_order_detail_query($id)
{
	return \Supply\Support\SupplyOrderDetailSupport::detail_query($id);
}

function ajax_order_detail_list($id, $one = false, $array = ["id" => "", "num" => ""])
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;

	$queryTest = supply_order_detail_query($id);
	$order = get_query_result_so($id);

	if ($db->numRows($queryTest) > 0) {
		$i = 0;
		$total = 0;
		$nds = 0;
		while ($content = $db->getRow($queryTest)) {
			$i++;
			$content["i_pos"] = $i;
			$content["org"] = $order["org"];
			$content["stock_ndr"] = empty($array["stock_ndr"]) ? '' : $array["stock_ndr"];
			$date_ship = empty($content["date_ship"]) ? $order["date_ship"] : $content["date_ship"];
			$content["date_ship"] = empty($date_ship) ? "" : $date_ship;
			$date_end = empty($content["date_end"]) ? $order["date_end"] : $content["date_end"];
			$content["date_end"] = empty($date_end) ? "" : $date_end;
			$content["nds_includes"] = $order["nds_includes"];
			template_order($content);

			$total += $content["sototal"];
			$nds += $content["nds"];
		}

		$total = round($total, 2);
		$nds = round($nds, 2);

		echo '<tr>
            <td colspan="9" class="noSort text-right"><b>Итого:</b></td>
            <td colspan="8" class="noSort allTotal text-left"><b>' . number_format($total, 2, '.', ' ') . ' руб.</b></td>
		</tr>
        <tr>
            <td colspan="9" class="noSort text-right"><b>В том числе НДС:</b></td>
            <td colspan="8" class="noSort allNds text-left"><b>' . number_format($nds, 2, '.', ' ') . ' руб.</b></td>
		</tr>';

		if (!$one) {
			$db->query("UPDATE JMY_supply_order SET total = '" . $total . "',  nds = '" . $nds . "' WHERE id = " . $id . " LIMIT 1");
		}
	} else {
		echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';
	}
}

function template_order($content)
{
	global $db, $lang;
	if (empty($content["sale_detail_id"])) {
		$price = 0;
		$sales_link = '';
	} else {
		$sales = SalesDetail::with('sales')->with('sales.object_')->where('id', $content["sale_detail_id"])->first();
		if (!empty($sales)) {
			$price = $sales->s_price;
			$sales_link = '<a href="'.route('admin.sales.show', $sales->sales->id).'" target="_blank">' . $sales->sales->numb . ' (' .( $sales->sales->object_->name ?? '') . ')</a>';
		}
	}

	$unit = get_unit('data', $content['so_unit'], 'sname');

	if (empty($content["stock_ndr"])) {
		$org_info = getOrgInfo($content["org"]);
		$stock_id_default = $org_info["org"]["stock_id_default"];
	} else {
		$stock_id_default = $content["stock_ndr"];
	}

	$ndr_view = get_ndr(["cid" => $content["id"], "stock_id" => $stock_id_default, "module" => "sales", "pid" => null, "returnType" => "view"]);
	// $ndr_view = "";

	$name = ee($content['s_name']);

	$count_receipts = empty($content["count_receipts"]) ? 0 : $content["count_receipts"];

	$date_ship = empty($content["date_ship"]) ? "" : date("d.m.Y", strtotime($content["date_ship"]));
	$date_end = empty($content["date_end"]) ? "" : date("d.m.Y", strtotime($content["date_end"]));

	$price_now = $content['price_now'];
	$count = $content['count'];
	$nds_type = $content["type_nds"] === null ? 3 : $content["type_nds"];
	$nds_includes = $content["nds_includes"];

	$pd = \Price::get_all([
		"price" => $price_now,
		"count" => $count,
		"nds_type" => $nds_type,
		"nds_includes" => $nds_includes,
	]);

	$total_no_nds = $pd["total"] - $pd["nds"];

	echo '
	<tr id="tr_' . $content['soid'] . '" class="tov_item" data-id="' . $content['soid'] . '" data-cid="' . $content["id"] . '" data-supply_d="' . $content['supply_d'] . '">
		<td data-toggle="tooltip" title="id: ' . $content['soid'] . '; cid: ' . $content["id"] . '" class="num">' . $content["i_pos"] . '</td>
		<td>' . $content['s_sku'] . '</td>
		<td class="cp change_goods" id="cg_' . $content['soid'] . '" data-id="' . $content['soid'] . '">' . $name . '</td>
		<td class="count cp">' . $count . '</td>
		<td onclick="view_count_receipts(' . $content['soid'] . ');" class="cp" style="font-weight:bold; color: ' . ($count_receipts == "0" ? "red" : ($count_receipts == $content["count"] ? "green" : "#adad01")) . '">' . $count_receipts . '</td>
		<td>' . $unit . '</td>
		<td class="text-center ndr" onclick="view_ndr_modal(' . $content["id"] . ')">' . $ndr_view . '</td>
		<td style="display: none;">' . number_format($price, 2, '.', ' ') . ' руб.</td>
		<td class="edit cp">' . number_format($price_now, 2, '.', ' ') . ' руб.</td>
		<td class="total_no_nds cp" data-id="' . $content['soid'] . '">' . number_format($total_no_nds, 2, '.', ' ') . ' руб.</td>
		<td class="total cp" data-id="' . $content['soid'] . '">' . number_format($content['sototal'], 2, '.', ' ') . ' руб.</td>
		<td class="typeNds cp">' . $pd["nds_proc"] . '%</td>
		<td class="nds cp">' . number_format($content["nds"], 2, '.', ' ') . ' руб.</td>

		<td><span class="date_ship cp" data-pk="' . $content["soid"] . '">' . $date_ship . '</span></td>
		<td ><span class="date_end cp" data-pk="' . $content["soid"] . '">' . $date_end . '</span></td>

		<td>' . (!empty($content['comment']) ? '<span style="cursor: pointer;" onclick="modal_o(\'#modal-comm-' . $content['soid'] . '\')" class="fa fa fa-edit text-info fa-md"></span>
		<div id="modal-comm-' . $content['soid'] . '" class="popup-basic bg-none mfp-with-anim mfp-hide">
				<div class="panel">
				  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарий</span></div>
				  <div class="panel-body">
					<h3 class="mt5">Комментарий:</h3>
					<hr class="short alt">
					<p>' . $content['comment'] . '</p>
				  </div>
				  <div class="panel-footer text-right">

				  </div>
				</div>
			  </div>' : '<span class="fa fa fa-edit fa-md"></span>') . '</td>
		<td>' . $sales_link . '</td>
		<td class="w200 text-right">
			<div class="btn-group " >';
?>

	<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only"><?php echo $lang['action']; ?></span></button>
	<ul role="menu" class="dropdown-menu pull-right">
		<li><a href="javascript:void(0);" onclick="autoPriceModal(<?php echo $content['soid']; ?>);">Магия</a></li>
		<?php if (!empty($content['sale_detail_id'])) { ?>
			<li><a href="javascript:void(0);" onclick="detachFromSale(this, <?php echo $content['soid']; ?>);">Отвязать от продажи</a></li>
		<?php } ?>
		<li class="divider"></li>
		<li><a href="javascript:void(0);" onclick="deleteSubModal(<?php echo $content['soid']; ?>);">Удалить</a></li>
	</ul>

<?php

	echo '

			</div>

				<div class="modal fade text-left" id="modal-form-' . $content['soid'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title">' . $lang['confirm'] . '</h5>
					  </div>
					  <form id="del-' . $content['soid'] . '" action="" method="POST">
						<div class="modal-body">
							<h3 class="mt5">Удалить ' . $content['title'] . '?</h3>
							<hr class="short alt">
							<p>Вы действительно хотите удалить ' . $content['title'] . ' из списка?</p>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-danger deleteSub" data-id="' . $content['soid'] . '" data-supply="' . $content['supply_d'] . '" data-dismiss="modal" onclick="deleteSub(this);">' . $lang['delete'] . '</button>

							<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>

							<input type="hidden" name="id" value="' . $content['soid'] . '" />
							<input type="hidden" id="supply_d_' . $content['soid'] . '" name="supply_d" value="' . $content['supply_d'] . '" />
						  </div>
					  </form>
					</div>
				  </div>
				</div>
		</td>
		<td class="w20 text-right">
			<div class="checkbox-custom mb15">
				<input id="checkbox' . $content['soid'] . '" type="checkbox" name="checks[]" class="checks" value="' . $content['soid'] . '">
				<label for="checkbox' . $content['soid'] . '"></label>
			</div>
		</td>
	</tr>';
}

function ajax_order_tovar_add()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if ((isset($_POST['cid']))) {
		$supply_o = intval($_POST['id']);
		$mcid = $_POST['cid'];

		$request = [
			"supply_o" => $supply_o,
			"items" => [],
		];

		foreach ($mcid as $cid) {
			$count = isset($_POST['count_' . $cid]) ? $_POST['count_' . $cid] : 1;
			$request["items"][] = [
				"cid" => $cid,
				"count" => $count,
			];
		}
		supply_order_tovar_add($request);

		echo '1';
	} else {
		echo '2';
	}
}

function supply_order_tovar_add($request)
{
	//["supply_o" => "", "items" => ["cid" => "", "count" => ""], ]
	$supply_o = $request["supply_o"];
	$so = \Supply\Model\SupplyOrder::find($supply_o)->toArray();
	$nds_type = 3;
	$items = $request["items"];
	foreach ($items as $item) {
		$cid = $item["cid"];
		$ts = \Catalog_v2\Model\Catalog::findOrFail($cid)->toArray();
		$count = isset($item["count"]) ? $item["count"] : 1;
		$unit = $ts["unit"];
		$s_name = $ts["title"];
		$s_sku = $ts["sku"];
		$priceList = \Price::getPrice($cid);
		$s_price = $priceList->price_purchase_;
		$data = [
			"nds_includes" => $so["nds_includes"],
			"price" => $s_price,
			"count" => $count,
			"nds_type" => $nds_type,
		];
		$result = get_all_price($data);
		$s_total = $result["total"];
		$s_nds = $result["nds"];
		\Supply\Model\SupplyOrderDetail::create(["cid" => $cid, "supply_o" => $supply_o, "count" => $count, "status" => 1, "unit" => $unit, "s_name" => $s_name, "s_sku" => $s_sku, "price_now" => $s_price, "total" => $s_total, "type_nds" => $nds_type, "nds" => $s_nds]);
	}
	auto_supply_o_total($supply_o);
}

function get_all_price($data)
{
	return \Price::get_all($data);
}

function ajax_order_device_del()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if (isset($_POST['id'], $_POST['supply_d'])) {
		$id = intval($_POST['id']);
		$supply_detail_id = intval($_POST['supply_d']);
		$db->query("DELETE FROM `" . DB_PREFIX . "_supply_order_detail` WHERE `id` = '" . $id . "' LIMIT 1 ;");

		$query = $db->query("SELECT * FROM JMY_supply_order_detail WHERE id = " . $id);

		$count = 0;
		if ($db->numRows($query) > 0) {
			$r = $db->getRow($query);
			$query = $db->query("SELECT * FROM JMY_supply_order_detail WHERE supply_o = " . $r["supply_o"]);
			while ($r = $db->getRow($query)) {
				$count += $r["count"];
			}
		}
		if (!empty($supply_detail_id)) {
			$db->query("UPDATE " . DB_PREFIX . "_supply_detail SET `status` = 1, count_send = '" . $count . "' WHERE `id` = '" . $db->safesql($supply_detail_id) . "' LIMIT 1");

			$query = $db->query("SELECT sid FROM " . DB_PREFIX . "_supply_detail WHERE id = '" . $supply_detail_id . "' LIMIT 1");
			if ($db->numRows($query) > 0) {
				$sd = $db->getRow($query);
				update_status_supply_detail($sd["sid"]);
			}
		}

		echo 'Прибор успешно удален!';
	} else echo ("Не хватает данных");
}

function create_excel_data($id, $combine)
{
	global $db;
	$data = [];
	$save_key = [];
	$query = supply_order_detail_query($id);
	$i = 0;
	$i0 = 0;
	if ($db->numRows($query) > 0) {
		while ($content = $db->getRow($query)) {
			$sku = $content['s_sku'];
			$title = ee(ee($content['s_name']));
			$count = $content['count'];
			$unit = get_unit('data', $content['unit'], 'sname');
			if (empty($content["sale_detail_id"])) {
				$sales_link = '';
			} else {
				$sales = SalesDetail::with('sales')->with('sales.object_')->where('id', $content["sale_detail_id"])->first();
				if (!empty($sales)) {
					$sales_link = $sales->sales->numb . ' (' . ($sales->sales->object_->name ?? '') . ')';
				}
			}
			if ($combine && array_key_exists($content["cid"], $save_key)) {
				$data[$save_key[$content["cid"]]]["count"] += $count;
			} else {
				$i0++;
				$data[] = [
					"i" => $i0,
					"sku" => $sku,
					"title" => $title,
					"count" => $count,
					"unit" => $unit,
					"price" => $content["price_now"],
					"total" => $content["sototal"],
					"sales_link" => $sales_link,
				];
			}


			if (!array_key_exists($content["cid"], $save_key)) {
				$save_key[$content["cid"]] = $i;
				$i++;
			}
		}
	}
	return $data;
}

function create_excel_init()
{
	require_once(ROOT . 'usr/plugins/PHPExcel.php');
	require_once(ROOT . 'usr/plugins/PHPExcel/IOFactory.php');
}

function create_excel_order($id)
{
	global $db;
	$queryO  = $db->query("SELECT o.oid, o.sales_id, obj.name, s.numb
	FROM `" . DB_PREFIX . "_supply_order` as o
	LEFT JOIN `" . DB_PREFIX . "_sales` as s on(o.sales_id=s.id)
	LEFT JOIN `" . DB_PREFIX . "_object` as obj on(s.object=obj.id) WHERE o.id = '" . $id . "'");
	return $db->getRow($queryO);
}

function create_excel_combine()
{
	return isset($_GET["combine"]) ?? false;
}

function create_excel($id)
{
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $dn_array;
	create_excel_init();
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');

	$order = create_excel_order($id);
	$obj = '';
	if (!empty($order['name'])) {
		$obj = '_' . $order['name'];
	}
	$tpl_name = 'other';
	$storage = '';
	if ($order['oid'] == '70') {
		$tpl_name = 'wilo';
		$storage = 'Забор Со склада  Ногинска';
	}

	$combine = create_excel_combine();

	// $objPHPExcel = $objReader->load(ROOT.'/files/tpl/supply/'.$tpl_name.'.xlsx');
	$objPHPExcel = $objReader->load(ROOT . 'usr/modules/supply/resources/templates/' . $tpl_name . '.xlsx');
	$baseRow = 8;
	$row_count = 0;
	// $query = $db->query("SELECT so.* FROM ".DB_PREFIX."_supply_order_detail as so WHERE so.supply_o = '".$id."' AND so.status != '0'");
	$data = create_excel_data($id, $combine);

	$baseRow = 8;
	$row_count = 0;
	foreach ($data as $val) {
		$row_count++;
		$row = $baseRow + $row_count;
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $row_count)
			->setCellValue('B' . $row, $val['sku'])
			->setCellValue('C' . $row, $val['title'])
			->setCellValue('D' . $row, $val['count'])
			->setCellValue('E' . $row, $val["unit"])
			->setCellValue('F' . $row, $storage)
			->setCellValue('G' . $row, $val['sales_link']);
	}

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$dir = ROOT . 'files/supply/' . $id;
	if (!file_exists($dir)) {
		mkdir($dir, 0777, true);
	}
	$file_url = 'files/supply/' . $id . '/Заказ_' . $order['numb'] . '_' . time() . '.xlsx';
	$objWriter->save(ROOT . $file_url);
	// header('Location: /'.$file_url);
	OfficeSupport::get_file(ROOT . $file_url);
}

function create_excel2($id)
{
	$combine = create_excel_combine();
	$data = [
		"head" => ["№", "Артикул", "Наименование", "Количество", "Единицы измерения", "Цена", "Сумма", 'Заказ'],
		"data" => create_excel_data($id, $combine)
	];
	SpreadsheetSupport::data_to_excel($data);
}

// function get_sod($id){
// 	global $db;
// 	$query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_order_detail` WHERE id = '".$id."' LIMIT 1");
// 	$sod = $db->getRow($query);
// 	return $sod;
// }

// function s_auto_price($id){
// 	global $db;
// 	$count_change = 0;
// 	if($id){
// 		$sod = get_sod($id);

// 		$query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_order_detail` WHERE supply_o = '".$sod["supply_o"]."' AND cid = '".$sod["cid"]."'");
// 		$count_change = $db->numRows($query);
// 		if($count_change > 0){
// 			while($r = $db->getRow($query)){

// 				$db->query("UPDATE `".DB_PREFIX."_supply_order_detail` SET price_now = '".$sod[""]."' WHERE id = '".$r["id"]."' LIMIT 1");
// 			}
// 			auto_supply_o_total($sod["supply_o"]);
// 		}
// 	}
// 	return $count_change;
// }


function confirm_receipt($id, $request)
{

	$module = new \Module;

	$data = $module->supply_to_receipts($id, [
		"doc_number" => $request["numDoc"],
		"date_receipts" => $request["datePost"],
		"org" => $request["org"],
		"stock_id" => $request["stock"],
		"supply_detail_id" => $request["checks"],
	]);

	$rid = $data["receipts_id"];

	$file = isset($request['file']) ? $request['file'] : false;
	if ($file) {
		$attribute = [];
		$attribute["name"] = "";
		$attribute["file"] = $file;
		$attribute["module"] = 'supply';
		$attribute["mod"] = 'receipts';
		$attribute["id"] = $rid;
		$attribute["comm_id"] = $rid;
		$attribute["ftp"] = "StorageFTP";
		uploaderMaster("download", $attribute);
	}

	return $rid;

	// global $db, $core;
	// $query = $db->query("SELECT so.sales_id, so.total, so.oid, o.name, o.inn, so.nds_includes, so.nds, so.object
	// FROM JMY_supply_order as so
	// LEFT JOIN JMY_organizations as o ON(so.oid = o.id)
	// WHERE so.id = '".$id."' LIMIT 1");
	// $res = $db->getRow($query);

	// $sales_id = isset($res["sales_id"]) ? "'".$res["sales_id"]."'" : 'null';
	// $uid = $core->auth->user_info['id'];
	// $numDoc = $request["numDoc"];
	// $datePost = $request["datePost"];
	// $org = $request["org"];
	// $stock_id = $request["stock"];
	// $nds_includes =  is_null($res["nds_includes"]) ? 1 : $res["nds_includes"];
	// $object = empty($res["object"]) ? "null" : "'".$res["object"]."'";

	// $numb_array = getNumbCounter('receipts', $org);
	// $numb = $numb_array['numb'];
	// $numb_counter = $numb_array['numb_counter'];
	// $db->query("INSERT INTO JMY_receipts (numb_counter, numb, sales_id, supply_id, uid, date, active, status, oid, oid_name, inn, doc_number, date_receipts, org, stock_id, nds_includes, object) VALUES('".$numb_counter."', '".$numb."', ".$sales_id.", '".$id."', '".$uid."', '".date("Y-m-d H:i:s")."', '0', '1', '".$res["oid"]."', '".$res["name"]."', '".$res["inn"]."', '".$numDoc."', '".$datePost."', '".$org."', '".$stock_id."', '".$nds_includes."', ".$object.")");
	// $rid = $db->insert_id();
	// relationsSave('supply_order', $id, 'receipts', $rid);

	// $file = isset($request['file']) ? $request['file'] : false;
	// if($file){
	// 	$attribute = [];
	// 	$attribute["name"] = "";
	// 	$attribute["file"] = $file;
	// 	$attribute["module"] = 'supply';
	// 	$attribute["mod"] = 'receipts';
	// 	$attribute["id"] = $rid;
	// 	$attribute["comm_id"] = $rid;
	// 	uploaderMaster("download", $attribute);
	// }

	// $idMas = $request["checks"];

	// $str = '';
	// $echoStr = '';
	// $total = 0;
	// $totalAll = 0;
	// $nds = 0;
	// $ndsAll = 0;
	// $price = new Price;
	// foreach($idMas as $val)
	// {
	// 	$query = $db->query("SELECT so.nds_includes, sod.cid, sod.count, sod.count_receipts, sod.price_now, sod.sale_detail_id as sale_detail_id, sod.total, sod.type_nds, sod.nds, sod.unit, sod.s_name, sod.s_sku FROM JMY_supply_order as so
	// 	LEFT JOIN JMY_supply_order_detail as sod ON(so.id = sod.supply_o)
	// 	WHERE sod.id = '".$val."' LIMIT 1");
	// 	$res = $db->getRow($query);

	// 	$count = $res["count"] - $res["count_receipts"];

	// 	$data = $price->get_all([
	// 		"price" => $res["price_now"],
	// 		"count" => $count,
	// 		"nds_includes" => $nds_includes,
	// 		"nds_type" => $res["type_nds"]
	// 	]);

	// 	$total = $data["total"];
	// 	$ndsCalc =  $data["nds"];

	// 	if($uid == 1){
	// 		$count = $res["count"];
	// 		$total = $res["total"];
	// 		$ndsCalc =  $res["nds"];
	// 	}


	// 	$sales_detail_id = isset($res["sale_detail_id"]) ? "'".$res["sale_detail_id"]."'" : 'null';
	// 	$str .= "('".$rid."', '".$res["cid"]."', ".$sales_detail_id.", '".$val."', '".htmlspecialchars($res["s_name"], ENT_QUOTES)."', '".$res["s_sku"]."', '".$res["unit"]."', '".$count."', '".$res["price_now"]."', '1', ".$total.", ".$res["type_nds"].", ".$ndsCalc."), ";
	// 	$totalAll += $total;
	// 	$ndsAll += $ndsCalc;

	// 	$echoStr .= $val.'; ';
	// }

	// $totalAll = round($totalAll, 2);
	// $ndsAll = round($ndsAll, 2);

	// $db->query("UPDATE JMY_receipts SET total = '".$totalAll."', nds = '".$ndsAll."' WHERE id = '".$rid."' LIMIT 1");
	// $db->query("INSERT INTO JMY_receipts_detail (rid, cid, sales_detail_id, supply_detail_id, name, sku, unit, count, price, status, total, type_nds, nds) VALUES ".mb_substr($str, 0, -2)."");

	// return $rid;
}

function checkGoodsSupply($request)
{
	global $db;
	$idMas = $request["checks"];
	$flag = true;
	if (is_array($idMas) && count($idMas) > 0) {
		$idMasStr = implode(", ", $idMas);
		$idFalse = '';
		$query = $db->query("SELECT sod.count, rd.count as count_receipts, sod.id
		FROM `" . DB_PREFIX . "_supply_order_detail` as sod
		LEFT JOIN (
			SELECT rd.supply_detail_id, SUM(count) as count
			FROM `" . DB_PREFIX . "_receipts_detail` as rd
			LEFT JOIN `" . DB_PREFIX . "_receipts` as r ON(r.id = rd.rid)
			WHERE r.active = '1'
			GROUP BY rd.supply_detail_id
		) as rd ON(rd.supply_detail_id = sod.id)
		WHERE sod.id IN(" . $idMasStr . ")");

		if ($db->numRows($query) > 0) {
			while ($r = $db->getRow($query)) {
				$count = $r["count"];
				$count_receipts = empty($r["count_receipts"]) ? 0 : $r["count_receipts"];
				if ($count - $count_receipts <= 0) {
					$flag = false;
					$idFalse .= $r["id"] . '; ';
				}
			}
		}
	}

	if ($flag) {
		return 'good';
	} else {
		return $idFalse;
	}
}
