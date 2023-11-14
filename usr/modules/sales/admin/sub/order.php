<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

use Sales\CloneSales;

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function check_prop_cookie($prop, $id){
	if(isset($_COOKIE[$prop.$id])){
		return true;
	} else {
		return false;
	}
}

function order_query_result_s($id){
	global $db;
	$query = $db->query("SELECT s.*, o.name as oname
	FROM `".DB_PREFIX."_sales` as s
	LEFT JOIN `".DB_PREFIX."_organizations` AS o on(s.oid=o.id)
	WHERE s.id = '" . $id . "'");
	$order = $db->getRow($query);
	return $order;
}

//функция вывода верхнего меню навигации
function sales_head_nav($id, $active)
{
	global $adminTpl, $lang;
	$active_array = array();
	$active_array['order'] = '';
	$active_array['task'] = '';
	$active_array['relations'] = '';
	$active_array[$active] = 'active';
	$adminTpl->subNavIncludes[] =  '<div class="mt-30 hidden-xs"><a href="/'.ADMIN.'/module/sales/order/'.$id.'" class="btn btn-default '.$active_array['order'].' btn-sm fw600 ml10"><span class="fa fa-plus pr5"></span> '.$lang['sales_nav_info'].'</a><a href="/'.ADMIN.'/module/sales/task/'.$id.'" class="btn btn-default  '.$active_array['task'].' btn-sm fw600 ml10"><span class="glyphicon glyphicon-ok-sign pr5"></span> '.$lang['sales_nav_task'].'</a><a href="/'.ADMIN.'/module/sales/relations/'.$id.'" class="btn btn-default  '.$active_array['relations'].' btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> '.$lang['sales_nav_relations'].'</a></div>';
	return false;
}

function order($id)
{
	global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	$order = order_query_result_s($id);

	$uid = (int)$core->auth->user_info['id'];

	if(!check_uid_org($order["org"])){
		header("Location: /administration/module/sales/prohibited");
	}

	$adminTpl->admin_head($lang['sales'] .' | '. $lang['sales_order_numb'].$order['numb']);

	echo '<link rel="stylesheet" href="/usr/modules/sales/admin/assets/css/order.css?'.time().'">';

	$clone_sales = new CloneSales(["parent_id" => $id]);
	$ccs = $clone_sales->check_children();
	if($ccs){
		echo '<div id="sales_block" class="sales_block">
			<div class="sales_block__text">
				<h3>Продажа является клоном</h3>
				<a href="/administration/module/sales/order/'.$ccs["id"].'" target="_blank">Оригинал</a>
			</div>
			<i class="fas fa-lock-alt sales_block__lock"><div class="sales_block__lock-disable"></div></i>
		</div>';
	}


	sales_head_nav($id, 'order');
	$step_required = true;
	$step_sos = true;
	$step_supply = true;
	$required_list = array();
	//проверка на наличие ответственного
	if(empty($order['uid']))
	{
		if (!isset($_COOKIE['sales_uid_'.$id]))
		{
			echo '<div id="modal-uid" class="popup-basic bg-none mfp-with-anim mfp-hide">
					<div class="panel">
						<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						<div class="panel-body">
							<h3 class="mt5">'.$lang['sales_responsible_title'].'</h3>
							<hr class="short alt">
							<p>'.$lang['sales_responsible_text'].'</p>
						</div>
						<div class="panel-footer text-right">
							<button type="button" onclick="setCookie(\'sales_uid_'.$id.'\', \'no\', \'expires: 86400\'); $(\'#modal-uid\').magnificPopup(\'close\');" class="btn btn-danger">Отказаться</button>
							<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/order_uid_set/'. $id.'\'" class="btn btn-success">'.$lang['sales_confirm_ok'].'</button>
						</div>
					</div>
				</div>';
			$adminTpl->js_code[] = 'modal_o(\'#modal-uid\');';
		}
		$required_list['uid'] = 'Ответственный';
		$step_required = false;
	}
	//проверка на договор
	$d_flag = false;
	if(!empty($order['rester']))
	{
		$d_flag = true;
	}
	//проверка на объект
	if(empty($order['object']))
	{
		$step_required = false;
		$required_list['object'] = 'Объект';

	}
	//проверка на контрагента
	if(empty($order['oid']))
	{
		$step_required = false;
		$required_list['oid'] = 'Контрагент';
	}
    //проверка на md5
    if($order['md5'] == null){
        $db->query("UPDATE `" . DB_PREFIX . "_sales` SET `md5` = '".md5(intval($order['date'])+intval($order['id']))."' WHERE `id` = " . $id . " LIMIT 1 ;");
        $query = $db->query("SELECT s.*, o.name as oname  FROM ".DB_PREFIX."_sales as s LEFT JOIN ".DB_PREFIX."_organizations AS o on(s.oid=o.id)  WHERE s.id = '" . $id . "'");
        $order = $db->getRow($query);
    }
	//проверка на SOS
	if(empty($order['sos'])||$order['sos'] == 0)
	{
		$step_sos = false;
	}
	//проверка на закупку
	if(empty($order['supply'])||$order['supply'] == 0)
	{
		$step_supply = false;
	}
	//проверка на закрепленность
	if(empty($order['status'])||$order['status'] == 0)
	{
		$step_supply = false;
	}
	$step = 0;
	$steps = array();
	$steps['required'] = '1';
	$steps['sos'] = '2';
	$steps['supply'] = '3';
	$step_error = array();
	$step_count = 0;
	if ($step_required)
	{
		$step = 1;
		$step_count++;
	}
	if ($step_sos)
	{
		$step = $steps['sos'];
		$step_count++;
		if ($step_count != $step)
		{
			$step_error[] = $step-1;
			$step_count++;
		}
	}
	if ($step_supply)
	{
		$step = $steps['supply'];
		$step_count++;
		if ($step_count != $step)
		{
			$step_error[] = $step-1;
			$step_count++;
		}
	}

    //модальное окно ошибки
    echo '<div id="modal-error" class="popup-basic bg-none mfp-with-anim mfp-hide">
			<div class="panel">
				<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Ошибка</span></div>
					<div class="panel-body">
						<h3 class="mt5">Заполнены не все поля!</h3>
						<hr class="short alt">
						<p>
                           <div class="">
                                <p>У данной продажи заполнены не все обязательные поля, а именно:<br><br><ul>';
                                foreach ($required_list as $key => $value)
                                            {
                                                echo '<li>'.$value.'</li>';
                                            }

                                  echo '</ul><br>Для продолжения работы с системой необходимо заполнить все обязательные поля!</p><br>
                            </div>
						</p>
					</div>
					<div class="panel-footer text-right">
						<button type="button" onclick="$(\'#modal-error\').magnificPopup(\'close\');" class="btn btn-danger">Закрыть</button>
					</div>
				</div>
			</div>';

	//модальное окно размещения заказа
	echo '<div id="modal-ss" class="popup-basic bg-none mfp-with-anim mfp-hide">
			<div class="panel">
				<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Выберите дальнейшее действие</span></div>
					<div class="panel-body">
						<h3 class="mt5">Какими образом вы хотите разместить заказ?</h3>
						<hr class="short alt">
						<p>
                           <div class="">
                              <table class="table list-table">
                                <tbody>
                                  <tr>
                                    <td style="cursor: pointer;" onclick="modal_o(\''.($step_required == true ? '#modal-act' : '#modal-error').'\', \'#modal-ss\');"><span class="icon text-alert fa fa-ambulance"></span>Актуализировать данные (SOS)</td>
                                    <td class="text-right"></td>
								  </tr>
								  <tr>
									<td class="cp" onclick="sendActualizeSupply();"><span class="icon text-alert fas fa-first-aid"></span>Отправить на актуализацию срок и цен</td>
									<td class="text-right"></td>
								  </tr>
                                  <tr>
                                    <td style="cursor: pointer;" onclick="modal_o(\''.($step_required == true ? '#modal-send' : '#modal-error').'\', \'#modal-ss\');"><span class="icon text-info fa fa-shopping-cart"></span>Отправить на закупку</td>
                                    <td class="text-right"></td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
						</p>
					</div>
					<div class="panel-footer text-right">
						<button type="button" onclick="$(\'#modal-ss\').magnificPopup(\'close\');" class="btn btn-danger">Закрыть</button>
					</div>
				</div>
			</div>';

	echo '<div class="modal fade" id="placeOrderModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="exampleModalLabel">Какими образом вы хотите разместить заказ?</h5>
		</div>
		<div class="modal-body">
			<div class="placeOrderModal_main">
				<table class="table list-table">
					<tbody>
					  <tr>
						<td class="cp" onclick="actualizeSupplyNoNotifModal();"><span class="icon text-alert fas fa-first-aid"></span>Отправить на актуализацию сроков и цен</td>
						<td class="text-right"></td>
					  </tr>
					  <tr>
						<td class="cp" onclick="actualizeSupplyModal();"><span class="icon text-alert fas fa-first-aid"></span>Отправить на актуализацию сроков и цен (с уведомлением)</td>
						<td class="text-right"></td>
					  </tr>
					  <tr>
						<td style="cursor: pointer;" onclick="sendPurchaseModal();"><span class="icon text-info fa fa-shopping-cart"></span>Отправить на закупку снабженцам</td>
						<td class="text-right"></td>
					  </tr>
					  <tr>
						<td style="cursor: pointer;" onclick="sendPurchaseModal2();"><span class="icon text-info fa fa-shopping-cart"></span>Отправить на закупку самому себе</td>
						<td class="text-right"></td>
					  </tr>';
					  $sos = search_sos($id);
						if(!empty($sos["id"]) && $sos["status"] == 2){
					  	echo '<tr>
							<td style="cursor: pointer;" onclick="acsModal();"><span class="icon text-info fas fa-magic"></span>Создать счёт</td>
							<td class="text-right"></td>
						  </tr>';
						}
					echo '</tbody>
				</table>
			</div>
			<div class="placeOrderModal_child actualizeSupply">
				Вы действительно хотите отправить заказ №' . $order["numb"] . ' на актуализацию сроков и цен с уведомлением снабженцам?
			</div>
			<div class="placeOrderModal_child actualizeSupplyNoNotif">
				Вы действительно хотите отправить заказ №' . $order["numb"] . ' на актуализацию сроков и цен без уведомления снабженцам?
			</div>
			<div class="placeOrderModal_child sendPurchase">
				Вы действительно хотите отправить заказ №' . $order["numb"] . ' в отдел снабжения на закупку?
				<div id="sendPurchaseSend" class="mt10"></div>
			</div>
			<div class="placeOrderModal_child sendPurchase2">
				Вы действительно хотите отправить заказ №' . $order["numb"] . ' в отдел снабжения на закупку?
				<div id="sendPurchaseSend2" class="mt10"></div>
			</div>
			<div class="placeOrderModal_child sendSupplyOrder">
				Вы действительно хотите создать счёт?
				<div id="sendSupplyOrder" class="mt10"></div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary placeOrderModal_child actualizeSupplyBtn" data-dismiss="modal" onclick="sendActualizeSupply();">Отправить</button>
			<button type="button" class="btn btn-primary placeOrderModal_child actualizeSupplyNoNotifBtn" data-dismiss="modal" onclick="sendActualizeSupplyNoNotif();">Отправить</button>
			<button type="button" class="btn btn-primary placeOrderModal_child sendPurchaseBtn" data-dismiss="modal" onclick="sendPurchase();">Отправить</button>
			<button type="button" class="btn btn-primary placeOrderModal_child sendPurchase2Btn" data-dismiss="modal" onclick="sendPurchase([\'status=0\']);">Отправить</button>';
			if(!empty($sos["id"])){
				echo '<button type="button" class="btn btn-primary placeOrderModal_child sendSupplyOrderBtn" data-dismiss="modal" onclick="auto_create_supply('.$sos["id"].');">Отправить</button>';
			}
		  	echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		</div>
	  </div>
	</div>
  </div>';

	//модальное окно другие действия
	echo '<div id="modal-other" class="popup-basic bg-none mfp-with-anim mfp-hide">
			<div class="panel">
				<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Выберите дальнейшее действие</span></div>
					<div class="panel-body">
						<h3 class="mt5">Какими образом вы хотите разместить заказ?</h3>
						<hr class="short alt">
						<p>
                           <div class="">
                              <table class="table list-table">
                                <tbody>
								  <tr>
                                    <td onclick="window.open(\'/catalog_v2/specification/sales/'. $id.'\',\'_blank\'); return false;"  style="cursor: pointer;"><span class="icon text-alert fa fa-list-ul"></span>Получить спецификацию</td>
                                    <td class="text-right"></td>
                                  </tr>';
                                //   echo '<tr>
                                //     <td style="cursor: pointer;" onclick="modal_o(\''.($step_required == true ? '#modal-doc' : '#modal-error').'\', \'#modal-other\');"><span class="icon text-alert fa fa-ambulance"></span>Создать договор</td>
                                //     <td class="text-right"></td>
                                //   </tr>';
                                  echo '<tr>
                                    <td style="cursor: pointer;"><span class="icon text-info fa fa-bullhorn"></span>Отменить заказ</td>
                                    <td class="text-right"></td>
																	</tr>';
																	$allowShipmentHide0 = '';
																	$allowShipmentHide1 = '';
																	if($order["allow_shipment"] == 0){
																			$allowShipmentHide1 = 'none';
																	}
																	if($order["allow_shipment"] == 1){
																			$allowShipmentHide0 = 'none';
																	}
																	echo '<tr class="allowShipmentHide" style="display: '.$allowShipmentHide0.';">
                                    <td style="cursor: pointer;" onclick="allowShipmentModal();"><span class=" icon fa fa-truck text-info" aria-hidden="true"></span>Разрешить отгрузку</td>
                                    <td class="text-right"></td>
																	</tr>';
																	echo '<tr class="allowShipmentHide" style="display: '.$allowShipmentHide1.';">
                                    <td style="cursor: pointer;" onclick="allowShipmentModal2();"><span class=" icon fa fa-truck text-danger" aria-hidden="true"></span>Отменить отгрузку</td>
                                    <td class="text-right"></td>
								  </tr>
								  <tr '.(($uid == 33 || $uid == 38 || $uid == 4 || $uid == 1) ? "" : "hidden").'>
								  	<td style="cursor: pointer;" onclick="$(\'#modal-other\').magnificPopup(\'close\'); union_s();"><span class="icon text-alert fas fa-umbrella"></span>Объединить товары</td>
								  	<td class="text-right"></td>
									</tr>
								  ';
                                echo '</tbody>
                              </table>
                            </div>
						</p>
					</div>
					<div class="panel-footer text-right">
						<button type="button" onclick="$(\'#modal-other\').magnificPopup(\'close\');" class="btn btn-danger">Закрыть</button>
					</div>
				</div>
			</div>';



	$adminTpl->js_code[] ='	$("#add_tovar").submit(function() {
										var form_data = $(this).serialize();
										$.ajax({
										type: "POST",
										url: "'.ADMIN.'/module/sales/ajax/order_tovar_add/'.$id.'",
										data: form_data,
										success: function() {
												$("#modal-add").magnificPopup(\'close\');
												notif("success","'.$lang['info'].'", "Товар успешно добавлен");
												ajaxGet("'.ADMIN.'/module/sales/ajax/order_tovar_list/'.$id.'", "tov_list");
												document.getElementById("name").value = "";
												document.getElementById("price").value = "";
												document.getElementById("status").value = "1";

										}
										});
								});';
	$adminTpl->js_code[] = '$("#points").spinner();
	  $(".select2-single").select2();';


    $attribute = [];
    $attribute["name"] = "";
    $attribute["script"] = true;
	echo uploaderMaster("css", $attribute);
    $attribute["script"] = false;
	$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);

	echo '<section id="content" class="animated fadeIn">';
	echo '<div class="text-center" style="margin-bottom: 18px;">
			<p class="lead" style="text-transform: uppercase; display: inline;">'.$lang['sales_order_numb'].$order['numb'].'</p>
			<p class="lead" style="display: inline;">(<span class="cp title" data-id="'.$id.'">'.$order['title'].'</span>)</p>
		</div>';
	if(!$step_required&&!isset($_COOKIE['sales_required_'.$id]))
	{
			echo '<div class="row"><div id="required_alert" class="alert light alert-warning alert-dismissable">
  <button type="button" data-dismiss="alert" aria-hidden="true" class="close"></button>
  <h3 class="mt5">Внимание!</h3>
  <p>У данной продажи заполнены не все обязательные поля, а именно:<br><br><ul>';

foreach ($required_list as $key => $value)
			{
				echo '<li>'.$value.'</li>';
			}

  echo '</ul><br>Для продолжения работы с системой необходимо заполнить все обязательные поля!</p><br>
  <div class="element-groups-sm-custom"><a href="javascript:void(0);" onclick="$(\'#required_alert\').alert(\'close\');" data-dismiss="alert" aria-hidden="true" class="btn btn-white">Закрыть</a>
  <a href="javascript:void(0);" onclick="setCookie(\'sales_required_'.$id.'\', \'no\', \'expires: 86400\'); $(\'#required_alert\').alert(\'close\');" class="btn btn-default">Не напоминать</a></div>
</div></div>';
	}
       echo'
		 <div id="modal-doc" class="popup-basic admin-form bg-none mfp-with-anim mfp-hide">
            <div class="panel">
             <div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Создать договор</span></div>';
				if ($d_flag)
				{
				 echo ' <div class="panel-body">
					<h3 class="mt5">Договор с контрагентом</h3>
					<p>Договор с контрагентом по данной заявку успешно создан, вы можете скачать его в формате doc или распечатать.</p>
				  </div><div class="panel-footer text-right">
					<a target="_blank" href="'.ADMIN.'/module/verification/doc_create/'.$id.'" class="btn btn-success">Скачать</a>
					 <button type="button" class="btn btn-info">Распечатать</button>
				  </div>';
				}
				else
				{
					 echo ' <form action="'.ADMIN.'/module/sales/doc_create" method="POST">

					 <div class="panel-body p25">
					<div class="alert alert-warning alert-dismissable">
  <button type="button" data-dismiss="alert" aria-hidden="true" class="close"></button><i class="fa fa-warning pr10"></i><strong>Внимание!</strong><br>При создании договора он будет добавлен в реестр автоматически, будьте внимательны при заполнении данных!</a>.
</div>
                    <div class="section">
                    <select name="org" class="form-control">
                    <option value="1">Атри-Энерго</option>

                    <option value="2">ТТ-Энерго</option>
					 <option value="5">Атри</option>
                    </select>
                    </div>

<div class="section" id="number_block" style="display: none;">
											<label for="text" class="field prepend-icon">
											    <input id="numb" name="numb" type="text" placeholder="Номер договора (без буквы)" class="gui-input" />
												<label for="text" class="field-icon"><i class="fa fa-pencil"></i></label>
											</label>
										</div>
										<div class="section">
											<label for="text" class="field prepend-icon">
											   <input type="date" id="date_doc" name="date_doc" class="gui-input" placeholder="Дата договора"/>
												<label for="text" class="field-icon"><i class="fa fa-calendar"></i></label>
											</label>
										</div>
										<div class="section">
											<label for="text" class="field prepend-icon">
											   <input type="text" name="pr" class="gui-input" placeholder="Укажите процент предоплаты"/>
												<label for="text" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
												<span class="help-block mt5"><i class="fa fa-question"></i> Если 0% то оплата осуществляется после отгрузки!</span>
											</label>
										</div>
										<div class="section">
									<label class="option block mt15">
										<input  onclick="showhide(\'number_block\')" type="checkbox" name="checked" value="checked"><span class="checkbox"></span>Не добавлять в реестр?
									</label>
									</div>
				  </div><div class="panel-footer text-right">
					<input type="hidden" name="id" value="'.$id.'" />
									  <input type="submit" class="button btn-primary" value="Создать договор" />
				  </div>
				  </form>';
				}


			echo'	</div>
          </div>

		  <link href="/usr/tpl/corp/assets/css/smart_wizard.min.css" rel="stylesheet" type="text/css" />
		  <link href="/usr/tpl/corp/assets/css/smart_wizard_theme_arrows.min.css" rel="stylesheet" type="text/css" />
		  <div id="smartwizard">
    <ul style="">
        <li><a href="#step-1">1 этап <br /><small>Продажа создана</small></a></li>
        <li><a href="#step-2">2 этап<br /><small>Заполнены все данные о продаже</small></a></li>
        <li><a href="#step-3">3 этап<br /><small>Продажа отправлена на SOS</small></a></li>
        <li><a href="#step-4">4 этап<br /><small>Продажа отправлена на закупку</small></a></li>
		<li><a href="#step-5">5 этап<br /><small>Товары закуплены</small></a></li>
		<li><a href="#step-6">6 этап<br /><small>Продажа готова к отгрузке</small></a></li>
		<li><a href="#step-7">7 этап<br /><small>Все товары отгружены</small></a></li>
    </ul>
</div>';

	$adminTpl->js_code[] = " // Smart Wizard
      $('#smartwizard').smartWizard({
            selected: ".$step.",  // Initial selected step, 0 = first step
            keyNavigation:false, // Enable/Disable keyboard navigation(left and right keys are used if enabled)
            autoAdjustHeight:true, // Automatically adjust content height
            cycleSteps: false, // Allows to cycle the navigation of steps
            backButtonSupport: false, // Enable the back button support
            useURLhash: false, // Enable selection of the step based on url hash

showStepURLhash: false,

            anchorSettings: {
                anchorClickable: true, // Enable/Disable anchor navigation
                enableAllAnchors: false, // Activates all anchors clickable all times
                markDoneStep: true, // add done css
                enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
            },
            contentURL: null, // content url, Enables Ajax content loading. can set as data data-content-url on anchor
            disabledSteps: [],    // Array Steps disabled
            errorSteps: [".implode(",", $step_error)."],    // Highlight step with errors
            theme: 'arrows',
            transitionEffect: 'fade', // Effect on navigation, none/slide/fade
            transitionSpeed: '400'
      });

 ";

	$local_date_delivery = json_encode(\Sales\Model\SalesDetail::getLocalDateDeliverySource(), JSON_UNESCAPED_UNICODE);

	echo '
		<input type="hidden" id="mainId" value="'.$id.'">
		<input type="hidden" id="mainUid" value="'.$order['uid'].'">
		<input type="hidden" id="local_date_delivery" value=\''.$local_date_delivery.'\'>
		<div class="row" id="admin-form">
			<div class="panel mb25">
				<div class="panel-body p20 pb10">
					<div class="section row mbn admin-form">
						<div class="col-md-12 pl15">
							<div class="section row mb15">
								<div class="col-md-4">
									<div class="element-groups-lg-custom wizard-options" id="ajaxLoadBtn">';
										echo ajax_order_btn($id);
									echo '</div>';

								echo '</div>
									<div id="orderData">';
										ajax_order_data($id);
									echo '</div>
								</div>
							</div>';
							echo '</div>
						</div>

					</div>
				</div>
						<div id="modal-send" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">Отправить заказ №' . $id . ' на закупку?</h3>
								<hr class="short alt">
								<p>Вы действительно хотите отправить заказ №' . $id . ' в отдел снабжения на закупку?</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" class="btn btn-success" id="send_order">Отправить</button>
							  </div>
							</div>
						  </div>

						  <div id="modal-transfer" class="popup-basic bg-none mfp-with-anim mfp-hide">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">Создать перемещение?</h3>
								<hr class="short alt">
								<p>Вы действительно хотите создать перемещение из товаров которые есть на складе?</p>
							  </div>
							  <div class="panel-footer text-right">
								<button type="button" class="btn btn-success" id="send_transfer">Создать</button>
							  </div>
							</div>
						  </div>

                <div id="modal-act" class="popup-basic bg-none mfp-with-anim mfp-hide">
                <form method="POST" action="'.ADMIN.'/module/sales/actualize/'.$id.'">
							<div class="panel">
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <div class="panel-body">
								<h3 class="mt5">Актуализировать заказ №' . $id . '?</h3>
								<hr class="short alt">
								<p>Вы действительно хотите актуализировать заказ №' . $id . '?</p>
                                <div class="checkbox-custom mb5">
                                <input id="remains" type="checkbox" name="remains" checked>
                                <label for="remains">Остатки</label>
                                </div>
                                <div class="checkbox-custom mb5">
                                <input id="timing" type="checkbox" name="timing" checked>
                                <label for="timing">Сроки</label>
                                </div>
							  </div>
							  <div class="panel-footer text-right">
                              <input type="submit" class="btn btn-success" value="Отправить">
							  </div>
							</div>
                            </form>
						  </div>
	<script>

        $(document).on("click", "#send_order", function(){
            $("#modal-send").magnificPopup(\'close\');
            $.post("'.ADMIN.'/module/sales/send_order/'. $id.'", function(data){
                notif("primary", "Информация", data);
            });
        });

		 $(document).on("click", "#send_transfer", function(){
            $("#modal-send").magnificPopup(\'close\');
            $.post("'.ADMIN.'/module/sales/send_transfer/'. $id.'", function(data){
                notif("primary", "Информация", data);
            });
        });

		function ajax_send(name, str_url) {
			var msg   = $("#"+name).serialize();
			$.ajax({
				type: "POST",
				url: str_url,
				data: msg,
				success: function(data) {
					$("#modal-"+name).magnificPopup(\'close\');
					notif("success","'.$lang['info'].'", data);
					ajaxGet("'.ADMIN.'/module/sales/ajax/order_tovar_list/'.$id.'", "tov_list");
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
		ajaxGet("'.ADMIN.'/module/sales/ajax/comments_get/'.$id.'", "sale_comments_list");
		$("#comm").val("");
		}
		});
		}


		function ajax_send_data(name, str_url) {
			var msg   = $("#"+name).serialize();
			$.ajax({
				type: "POST",
				url: str_url,
				data: msg,
				success: function(data) {
					$("#modal-"+name).magnificPopup(\'close\');
					notif("success","'.$lang['info'].'", data);
					ajaxGet("'.ADMIN.'/module/sales/ajax/order_data/'.$id.'", "orderData");
				}
			});
		}

        function uploaderMaster(){
            '.uploaderMaster("script", $attribute).'
        }
	</script>



	<script src="/usr/modules/sales/admin/assets/js/jquery.stickytableheaders.min.js"></script>';

    // echo '<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    // <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>';
    echo '<script src="/usr/modules/sales/admin/assets/js/sort.js"></script>

    <div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="">Создать реализацию?</h5>
          </div>
          <div class="modal-body">
            Вы хотите создать реализацию?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="checkBtn">Продолжить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>';

	echo <<<HTML
		<div id="fastTask" class="panel panel-body mb20 row" style="padding: 10px;">
			<div id="fastTaskContent"><h4>Загрузка...</h4></div>
			<div id="fastTaskButtons" class="btn-group"></div>
		</div>
HTML;

	$confirm_supply_order = json_decode($order["confirm_supply_order"], true);
	if(count($confirm_supply_order) > 0){
		foreach($confirm_supply_order as $val){
			$supplyQuery = $db->query("SELECT numb FROM ".DB_PREFIX."_supply_order WHERE id = '".$val."' LIMIT 1");
			if($db->numRows($supplyQuery) > 0){
				$sq = $db->getRow($supplyQuery);
				echo '<div class="danger_block" id="danger_block_'.$val.'">
						<div class="danger_block-primary">
							<h2 class="danger_block-title">
								Подтвердите заявку на закупку №'.$sq["numb"].'!
							</h2>
							<div class="danger_block-btn">
								<input type="button" value="Подтвердить" onclick="supply_order_confirm_modal('.$val.');">
								<input type="button" value="Отправить на доработку" onclick="supply_order_cancel_modal('.$val.');">
								<input type="button" value="Посмотреть позиции" onclick="view_supply_order_position('.$val.');">
							</div>
						</div>
						<div class="danger_block-img"></div>
					</div>';
			}
		}
	}

	if($order["status_client"] == 2){
		echo <<<HTML
			<div class="status_client_block p20 mb20" style="border: 3px #3dbdcc solid; border-radius: 10px;">
				<div class="status_client_block-title" style="font-weight: bold; font-size: 20px;">Согласуйте заявку клиента</div>
				<div class="status_client_block-btn pt20">
					<button type="button" class="btn btn-primary btn-xs" onclick="status_client_block({status:3});">Согласовать</button>
				</div>
			</div>
HTML;
	}

	echo '<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список товаров:</span>
					<div class="widget-menu pull-right" >
						<div class="btn-group">';
							// $bb = callAddProduct($id, "https://atri-energo.ru/administration/module/sales/addProduct/", true);

							$victoria = new \Victoria;
							$victoria->link = "/administration/module/sales/victoria_add/{$id}";
							$bb = $victoria->button();

							$notif = "notif('primary', '".$lang['info']."', '".$lang['ajax_update_compl']."');";
							$btns = $bb.'<button onclick="updateBody(); '.$notif.' " type="button" class="btn btn-xs btn-system">
								<span class="mr10 icon fa fa-refresh"></span>Обновить
							</button>
							<button class="btn btn-xs btn-success saveChange">Сохранить</button>';
							echo $btns;
						echo '</div>

						<div id="modal-add" class="popup-basic admin-form mfp-with-anim mfp-hide modal-lg popup-lg">
							<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Добавить товар</span></div>';
								$adminTpl->footIncludes[] = '<script src="usr/tpl/corp/assets/js/search.js"></script>';
								 echo'
								<form id="add_tovar" action="javascript:void(null);">
									<div class="panel-body p25">
										<div class="section">
											<input type="text"  name="referal"  id="autocomplete-ajax" class="who form-control locinput input-rel searchtag-input has-icon" placeholder="Введите название или артикул"  autocomplete="off">
										</div>
										<div class="section">
										<select name="cid" id="cid" class="search_result form-control" multiple></select>
										</div>
										<div class="section">

										</div>
									</div>
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$id.'" />
									  <input type="submit" class="button btn-primary" value="Добавить приборы" />
									</div>
								</form>
                            </div>
						</div>
					</div>

              </div>
			  <script type="text/javascript"></script>

              <input type="hidden" value="0" id="counter">
              <input type="hidden" value="" id="saveId">
              ';


              echo '<div class="panel-body pn table-responsive">
				<form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped">
                    <thead id="table_thead">';
						echo ajax_order_table_head($id);
                    echo '</thead>
					<tbody id="tov_list">';
						ajax_order_tovar_list($id);
					echo '</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">
					  <td align="right" colspan="15">
                        <nav align="right" class="text-right">
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">
								<option value="discount">Массовая скидка</option>
								<option value="type_price">Массовый выбор цены</option>
								<option value="update_checked_price">Обновить цены</option>
								<option value="reserv">Массовый резерв</option>
								<option value="reserv_del">Удалить резерв</option>
								<option value="assembly">Отправить на сборку</option>
								<option value="del">Удалить выбранное</option>
								<option value="discount_oid">Проставить скидки по контрагенту</option>
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" disabled><span class="pd-l-sm"></span>
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

<div class="modal fade" id="reservModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Резервация</h5>
      </div>
			<div class="modal-body">
				<label for="countReserv">Количество в резерв</label>
        <input type="number" placeholder="Введите количество" class="form-control" value="1" id="countReserv" min="1">
      </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="reservBtn" data-dismiss="modal">В резерв</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="reservListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Список резервов</h5>
      </div>
      <div class="modal-body" id="reservList">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal" id="reservDelBtn">Удалить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
    </div>
  </div>
</div>';

ndr_init();

echo '<div class="modal fade" id="allowShipmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Разрешить отгрузку?</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите разрешить отгрузку?
      </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="allowShipmentBtn">Разрешить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="allowShipmentModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Отменить отгрузку?</h5>
      </div>
      <div class="modal-body">
        Вы действительно хотите отменить отгрузку?
      </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="allowShipmentBtn">Отменить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="sales_comment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Комментарий</h5>
      </div>
			<div class="modal-body">
			<form id="comment_form">
				<textarea id="sales_text_comment" name="comment" placeholder="Введите текст комментария..." class="form-control"></textarea>
				<h5>Файлы:</h5>
				<div id="sales_comment_files" class="mb20"></div>';
				$attribute = [];
        		$attribute["name"] = "1";
        		$attribute["script"] = true;
        		$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
        		$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
				echo uploaderMaster("css", $attribute);
				echo uploaderMaster("upload", $attribute);
				echo uploaderMaster("view", $attribute);
			echo '</form>
				</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="changeCommentSales">Изменить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bd-example-modal-lg" id="counterpartyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="width: 1600px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Информация по организации</h5>
      </div>
      <div class="modal-body">
		  <div class="container-fluid" id="counterpartyModalBody">

      	</div>
      </div>
		<div class="modal-footer">
        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      	</div>
    </div>
  </div>
</div>

<div class="modal fade" id="commentViewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Комментарий</h5>
      </div>
      <div class="modal-body">
		  <div class="container-fluid" id="commentViewModalBody">

      	</div>
      </div>
		<div class="modal-footer">
        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      	</div>
    </div>
  </div>
</div>

<div class="modal fade" id="sendMessModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Отправить сообщения</h5>
      </div>
      <div class="modal-body">
		<div class="container-fluid">
			<form method="POST" id="sendMessModalForm">

			</form>
      	</div>
      </div>
		<div class="modal-footer">
        	<button type="button" class="btn btn-primary" data-dismiss="modal" id="sendMessModalBtn" style="display: none;">Отправить</button>
        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      	</div>
    </div>
  </div>
</div>

<div class="modal fade" id="priceChangeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Локальная цена закупки</h5>
      </div>
	  <div class="modal-body">
		<input type="number" placeholder="Введите цену закупки" id="priceChangeModalInp" class="form-control">
      </div>
		<div class="modal-footer">
        	<button type="button" class="btn btn-primary" data-dismiss="modal" id="priceChangeModalClear" data-status="1">Очистить</button>
        	<button type="button" class="btn btn-primary" data-dismiss="modal" id="priceChangeModalSend" data-status="2">Отправить</button>
        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      	</div>
    </div>
  </div>
</div>

<div class="modal fade" id="datePostLocalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Локальная дата поставки</h5>
      </div>
	  <div class="modal-body">
		<input type="date" id="datePostLocalModalInp" class="form-control">
      </div>
		<div class="modal-footer">
        	<button type="button" class="btn btn-primary datePostLocalModalBtn" data-dismiss="modal" data-status="1">Очистить</button>
        	<button type="button" class="btn btn-primary datePostLocalModalBtn" data-dismiss="modal" data-status="2">Отправить</button>
        	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      	</div>
    </div>
  </div>
</div>

<div class="modal fade" id="masDelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить выбранное</h5>
      </div>
      <div class="modal-body">
		Вы действительно хотите удалить:
			<ul id="masDelModalUl">
			</ul>
      </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="masDelModalBtn">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="masTypePriceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Тип цены</h5>
      </div>
      <div class="modal-body">
			<select class="select2-single form-control" id="masTypePriceModalSel">';
				foreach(\Price\Models\CatalogPriceType::whereNull("parent")->get() as $cp){
					echo '<option value="'.$cp->id.'">'.$cp->title.'</option>';
				}
			echo '</select>
      </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="masTypePriceModalBtn" onclick="changeMasTypePrice();">Изменить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="masReservModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Массовый резерв</h5>
      </div>
      <div class="modal-body">
		Вы действительно хотите переместить в резерв:
			<ul id="masReservModalUl">
			</ul>
      </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="masReservModalBtn" onclick="masReserv();">Резерв</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="masReservDelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Массовое удаление резерва</h5>
      </div>
      <div class="modal-body">
		Вы действительно хотите удалить резерв:
			<ul id="masReservDelModalUl">
			</ul>
      </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="masReservDelModalBtn" onclick="masReservDel();">Удалить резерв</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="masDiscountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Массовая скидка</h5>
      </div>
      <div class="modal-body">
			<input type="number" value="" placeholder="Введите скидку" class="form-control" id="masDiscountInp">
      </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="masDiscountModalBtn" onclick="setMasDiscount();">Установить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Счета</h5>
      </div>
	  <div class="modal-body" id="payBody">

      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="assemblyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	<form id="assemblyModalForm" action="#" method="POST">
      	<div class="modal-header">
      	  <h5 class="modal-title" id="exampleModalLabel">Отправить на сборку</h5>
      	</div>
		   <div class="modal-body" id="assemblyModalBody">
		   <!--<div class="assemblyModalProtect"
				style="position: absolute;
				background-color: #ffffff00;
				z-index: 3;
				width: 100%;
				height: 100%;
				left: 0;
				top: 0;"></div> -->
	  		Вы действительно хотите отправить на сборку:
				<ul id="assemblyModalUl">
				</ul>
      	</div>
	  	<div class="modal-footer">
	  		<button type="submit" class="btn btn-primary">Отправить</button>
      	  <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	  	</div>
	  </form>
    </div>
  </div>
</div>

<div class="modal fade" id="doMoveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Создать перемещение</h5>
      </div>
	  <div class="modal-body" id="">
	  		Вы действительно хотите создать перемещение?
      </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="doMove();">Отправить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="fixSupplyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Уравнитель</h5>
      </div>
	  <div class="modal-body" id="">
			  <div>Вы действительно хотите уравнять?</div>
      </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="fixSupply('.$id.')">Уравнять</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="reserv_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">В резерв</h5>
      </div>
	  <div class="modal-body" id="">
			Добавить в резерв?
      </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="reserv_modal_btn" onclick="add_reserv($(this).data(\'id\'))">В резерв</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="del_reserv_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Удалить резерв</h5>
      </div>
	  <div class="modal-body" id="">
			Удалить резерв?
      </div>
	  <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" id="del_reserv_modal_btn" onclick="del_reserv($(this).data(\'id\'))">Удалить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="file_request" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Список файлов</h5>
      </div>
	  <div class="modal-body" id="file_request_body">

      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="status_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Лог статуса товара</h5>
      </div>
	  <div class="modal-body" id="status_modal_body">

      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="realization_view_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Реализации</h5>
      </div>
	  <div class="modal-body" id="realization_view_modal_body">

      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="assembly_view_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Сборки</h5>
      </div>
	  <div class="modal-body" id="assembly_view_modal_body">

      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="transfer_view_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Перемещения</h5>
      </div>
	  <div class="modal-body" id="transfer_view_modal_body">

      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="supply_order_confirm_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Подтвердить заявку на закупку</h5>
      </div>
	  <div class="modal-body" id="">
		Вы действительно хотите подтвердить заявку?
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="supply_order_confirm(this);" id="supply_order_confirm_modal_btn">Подтвердить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="supply_order_cancel_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Отправить на доработку заявку на закупку</h5>
      </div>
	  <div class="modal-body" id="">
		<div class="mb20">Вы действительно хотите отправить на доработку заявку на закупку?</div>
		<div>
		Причина:
		<textarea id="supply_order_cancel_modal_textarea" class="form-control" placeholder="Введите комментарий" oninput="so_btn(this);"></textarea>
		</div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="supply_order_cancel(this);" disabled id="supply_order_cancel_modal_btn">Подтвердить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div id="modal-uid-edit" class="popup-basic admin-form mfp-with-anim mfp-hide" >
	<div class="panel">
		<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Изменить ответственного</span></div>
		<form action="javascript:void(null);" id="uid-edit" onsubmit="ajax_send_data(\'uid-edit\', \''.ADMIN.'/module/sales/ajax/order_uid_edit\');">
			<div class="panel-body p25">
				<div class="section">
					<label for="uid" class="field prepend-icon">';
						$setting = [
							"mod" => "jsAdminTpl",
							"name" => "uid",
							"id" => $order["uid"],
							"ajaxUrl" => "getUsersToSelect",
							"required" => true,
						];
						oSelectCustom($setting);
						$setting["mod"]	= "select";
						oSelectCustom($setting);
					echo '</label>
				</div>
			</div>
			<div class="panel-footer">
				<input type="hidden" name="id" value="'.$id.'" />
				<input type="submit" class="button btn-primary" value="Изменить" />
			</div>
		</form>
	</div>
</div>

';

?>

<div class="modal fade admin-form" id="reestr_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Договор</h5>
      </div>
      <div class="modal-body" id="reestr_modal_body">
			<form id="reestr_modal_body_form">

			</form>
      </div>
      <div class="modal-footer">
	  	<button type="button" class="btn btn-info" onclick="reestr_modal_create2();" id="reestr_modal_create2_btn" data-dismiss="modal">Создать</button>
	  	<button type="button" class="btn btn-info" onclick="reestr_modal_create();" id="reestr_modal_create_btn">Создать</button>
	  	<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="reestr_modal_save();" id="reestr_modal_btn">Сохранить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade text-left" id="modal-form-delete" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Удалить <span class="modalFDName"></span>?</h5>
			</div>
			<div class="modal-body">
			<p>Вы действительно хотите удалить <span class="modalFDName"></span> из списка?</p>
			</div>
			<div class="modal-footer">
				<input type="button" class="btn btn-danger" id="modalFDId" data-dismiss="modal" value="Удалить">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade text-left" id="modal-sales-users" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content admin-form">
			<div class="modal-header">
				<h5 class="modal-title">Список пользователей</h5>
			</div>
			<div class="modal-body">
				<div class="d-flex">
					<?php
						$setting = [
							"mod" => "jsAdminTpl",
							"name" => "uid",
							"select_id" => "modal-sales-users-select",
							"ajaxUrl" => "getUsersAllToSelect",
							"required" => true,
						];
						oSelectCustom($setting);
						$setting["mod"]	= "select";
						oSelectCustom($setting);
					?>
					<button type="button" class="btn btn-xs btn-success" onclick="salesUsersAdd()">Добавить</button>
				</div>
				<table class="table mt20">
					<thead>
						<tr>
							<td>Пользователь</td>
							<td>Тип</td>
							<td>Действия</td>
						</tr>
					</thead>
					<tbody id="modal-sales-users-tbody"></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<?php

require_once(ROOT.'usr/modules/reestr/Support/spec_modal.php');
echo reestr_spec_modal(['btn_func' => 'reestr_spec_modal_create();']);
$adminTpl->footIncludes[] = reestr_spec_modal_js();

$fc_organization = new FastCreate("organization");
$fc_org = $fc_organization->get_fast_create();
echo $fc_org->modal;
$adminTpl->footIncludes[] = $fc_org->script;

$extendedSort = new \ExtendedSort;
$extendedSort->link = "/administration/module/sales/extendedSort/{$id}";
$extendedSort->init();

end_sales_init();

xeditableInit();

print_init();

$adminTpl->footIncludes[] = '<script>$(".table").stickyTableHeaders({fixedOffset :  $(".navbar").height(), zIndex: 5});</script>';
$adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/function.js"></script>';
$adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/price.js?051120191156"></script>';
$adminTpl->footIncludes[] = '<script src="/usr/other/sub/relations/asset/js/relations.js?'.time().'"></script>';
	$victoria_min = new \Victoria;
	$victoria_min->link = "/administration/module/sales/victoria_add_min/\${id}";
	$vm = $victoria_min->open_func();
	$adminTpl->footIncludes[] = <<<HTML
	<script>
		function change_goods_open(id){
			{$vm}
		}
	</script>
HTML;

// $adminTpl->footIncludes[] = <<<HTML
// <script>
// 	function change_goods_open(id){
// 		window.open(`/administration/module/sales/addProductMin/${id}`,'1549627856974','width=1400,height=850,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
// 	}
// </script>
// HTML;

$adminTpl->footIncludes[] = '<script src="/usr/modules/sales/admin/assets/js/FastTask.js?'.time().'"></script>';
$adminTpl->footIncludes[] = '<script src="/usr/modules/sales/admin/assets/js/script.js?'.time().'"></script>';
$adminTpl->footIncludes[] = \PriceTypeSelect::script();
$adminTpl->footIncludes[] = <<<HTML
	<script>
		\$extendedSort.sort_update = updateBody;
	</script>
HTML;


$attr = [];
$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
$attr = [
	"module" => 'sales_detail',
	"id" => $id,
	"update" => "tov_list",
	"update_url" => "/administration/module/sales/ajax/order_tovar_list/".$id,
	"status" => 1,
	"hotkeys" => $btns,
	"update_func" => "updateBody",
];


fastBar($attr);


union_modal_init([
	"base_name" => DB_PREFIX."_sales_detail",
	"parent_name" => "sales_id",
	"parent_id" => $id,
	"fields" => ["ID" => ["field" => "id"], "Наименование" => ["field" => "s_name"], "Количество" => ["field" => "count"], "Цена" => ["field" => "s_price", "view_func" => "priceSet({%VIEW_FUNC%})"]],
	"url_send" => "/administration/module/sales/union_tov/".$id,
	"send_success" => "updateBody();",
	"sql_where" => "AND del = '0'"
]);

	$adminTpl->admin_foot();
}

function order_tovar_list_query($sales){
	global $db;
	return $db->query("SELECT s.*, c.id as catalog_id, c.title, c.attach_id, c.price_data, c.sku
	FROM `".DB_PREFIX."_sales_detail` as s
	LEFT JOIN `".DB_PREFIX."_catalog` AS c on(c.id=s.cid)
	WHERE s.sales_id = '".$sales."' AND s.status != '0' AND s.del = '0'
	ORDER BY s.position");
}


function ajax_order_tovar_list($sales, $idRow = null, $changeArray = [])
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
	$uid = $core->auth->user_info['id'];

	// $assembly = new Storage\Assembly;

	$sd = Sales\Model\Sales::find($sales)->toArray();
	$clone_flag = false;
	if($sd["clone_active"] == 1){
		$clone_flag = true;
		$sd2 = Sales\Model\Sales::find($sd["clone"])->toArray();
	}

	$stock_id = empty($_COOKIE["stock_id_view"]) ? $sd["stock_id"] : $_COOKIE["stock_id_view"];

    if(empty($idRow)){
        $query = order_tovar_list_query($sales);
    } else {
        $query = $db->query("SELECT s.*, c.id as catalog_id, c.title, c.sku, c.unit, c.attach_id
		FROM `".DB_PREFIX."_sales_detail` as s,
		`".DB_PREFIX."_catalog` AS c
		WHERE s.id = '".$idRow."' AND c.id = '".$changeArray["cid"]."' AND s.status != '0'");
	}

	$load_icon = '<i class="fas fa-spinner fa-spin"></i>';

	if($db->numRows($query) > 0)
	{
		$i=0;
		$total = 0;
		$allSumPrice = 0;
		$rows = \Sales\Model\SalesDetail::getStatus();
		$supply_o_tov_class = isset($_GET["supply_o_tov"]) ? $_GET["supply_o_tov"] : [];

		// $QpriceType = $db->query("SELECT id, title FROM `".DB_PREFIX."_catalog_price_type`");
		// $qpt_data = [];
        // while($qpt = $db->getRow($QpriceType)){
		// 	$qpt_data[] = $qpt;
        // }

		$priceTypeSelect = new \PriceTypeSelect;

		while($content = $db->getRow($query))
		{
			$cache = new \Boot\System\Cache\Cache;
			if(empty($idRow)){
				$i++;

				$status_icon = '<span onclick="open_status_modal('.$content['id'].');" class="status_item">'.$rows[ $content['status'] ]["span"].'</span>';

				$s_count = $content['count'];
				$s_discount = $content['discount'];
				$s_price = $content['s_price'];
				$s_total = $content['total'];
				$s_price_type = $content["price_type"];
				$comment = $content['comment'];
			} else {
				$comment = '';
				$status_icon = $rows[1]["span"];
				$i = $changeArray["num"];
				//$s_discount = $changeArray["discount"];
				$s_discount = 0;
				$s_count = $changeArray["count"];
				$s_price_type = 3;
				$priceList = \Price::getPrice($content["catalog_id"]);
				$price_ = \Price::get_all([
					"price" => $priceList->price_list_,
					"count" => $s_count,
					"discount" => $s_discount,
				]);
				$s_price = $price_["price"];
				$s_total = $price_["total"];
			}
				$total += ($content['total']);

    			$str = "";
				$attach = \General\Model\Attach::where("pid", $content['id'])->where("module", "sales_comment");
				if($attach->exists()){
					foreach($attach as $myr){
						$str .= "<a target=\"_blank\" href=\"/download/{$myr->id}\">{$myr->name}</a><br>";
					}
				}
				if(empty($idRow)){
					$gppds = \Sales\Support\SalesSupport::get_purchase_price_date_sales($content['id']);
				} else {
					$gppds = \Sales\Support\SalesSupport::get_purchase_price_date_sales($content['id'], $changeArray["cid"]);
				}
				$flagSup = $gppds["flagSup"] ?? false;
				$price = $gppds["price"] ?? 0;
				$datePrice = empty($gppds["datePrice"]) ? 'Отсутсвует' : $gppds["datePrice"];
				$color = $gppds["color"] ?? "";
				$date_post = empty($gppds["date_post"]) ? 'Не проставлено' : $gppds["date_post"];
				$dateColor = $gppds["dateColor"] ?? "";
				$fileUrl = $gppds["fileUrl"] ?? "";

				$allSumPrice += $price * $content['count'];

				$sales_reserv = $cache->getItem("sales_reserv.{$content['id']}");
				if($sales_reserv->isHit()){
					$ndr = $sales_reserv->get();
				} else {
					$ndr = get_ndr(["cid" => $content["catalog_id"], "stock_id" => $sd["stock_id"], "module" => "sales", "pid" => $content['id'], "returnType" => "array"]);
					$sales_reserv->set($ndr);
					$cache->save($sales_reserv);
				}

				$ndr_view = get_ndr(["cid" => $content["catalog_id"], "stock_id" => $stock_id, "module" => "sales", "pid" => $content['id'], "returnType" => "view"]);


				if(empty($idRow)){
					echo '<tr id="tr_' . $content['id'] . '" data-id="' . $content['id'] . '" class="sales_item '.(in_array($content["id"], $supply_o_tov_class) ? "view_supply_order" : "").'">';
					$s_name = ee($content['s_name']);
					$s_sku = $content['s_sku'];
					$s_unit = get_unit('data', $content['s_unit'] ,'sname');
				} else {
					$s_name = ee($content['title']);
					$s_sku = $content['sku'];
					$s_unit = get_unit('data', $content['unit'] ,'sname');
				}
				$title_no = "";
				if((ee($content['s_name']) != ee($content['title'])) && empty($idRow)){
					$title_no = "text-danger";
				}
				$sku_no = "";
				if((ee($content['s_sku']) != ee($content['sku'])) && empty($idRow)){
					$sku_no = "text-danger";
				}
				echo '<td class="position" data-toggle="tooltip" data-cid="'.$content["catalog_id"].'" data-mainid="' . $content['id'] . '" title="id: ' . $content['id'] . '; cid: '. $content["catalog_id"] .'">' . $i . '</td>
				<td class="changeGoods changeElem s_name '.$title_no.'">' . $s_name . '</td>
				<td class="sku '.$sku_no.'">' . $s_sku . '</td>
				<td class="units">' . $s_unit . '</td>
				<td class="text-center count cp">' . $s_count . '</td>';


				$r_count = 0;
				if($sd["sales_type"] == 2){
					$r_count = \Sales\Support\SalesSupport::get_transfer_active_count($content['id']);
					$view_func = 'transfer_view_modal('.$content['id'].');';
				} else {
					$r_count = $content['count_realization'];
					$view_func = 'realization_view_modal('.$content['id'].');';
				}

				$count_assembly = $load_icon;
				// $count_assembly = $assembly->get_count($content['id']);
				// $count_assembly = $content['count_assembly'];

				echo '<td onclick="'.$view_func.'" class="count_realization text-center cp" style="font-weight: bold; color: '.($r_count == $s_count ? 'green' : 	($r_count == 0 ? 'red' : '#d2d200')).'">' . $r_count . '</td>
				<td class="count_assembly text-center cp" onclick="assembly_view_modal('.$content['id'].');" style="display: none; font-weight: bold;">'.$count_assembly.'</td>
				';

				echo '<td class="text-center ndr cp" data-cid="'.$content["catalog_id"].'" onclick="view_ndr_modal('.$content["catalog_id"].')">'.$ndr_view.'</td>';

				if(!check_prop_cookie("purchase_price", '')){
					echo '<td class="">
					<span onclick="'.($flagSup ? 'window.open(\''.$fileUrl.'\', \'1552240782632\',\'width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,	left=0,top=0\');return false;' : '').'" class="purchasePrice '.($flagSup ? 'link-style' : 'link-no-style').'" style="color: '.$color.'">' . number_format($price,2,'.',' 	'). ' руб.</span>';

					echo '<i class="fa fa-pencil ml10 cp text-alert purchasePriceLocal" aria-hidden="true"></i>
					<div class="date_purchse_price">'.$datePrice.'</div></td>';

					// echo '<td class=""><span class="purchasePrice">'.$load_icon.'</span>
					// <i class="fa fa-pencil ml10 cp text-alert purchasePriceLocal" aria-hidden="true"></i>
					// <div class="date_purchse_price"></div></td>
					// </td>';
				}

				echo '<td class="">';
                // echo '<select class="form-control priceType">';
				// foreach($qpt_data as $item){
				// 	echo '<option value="'.$item["id"].'" '.($s_price_type == $item["id"] ? "selected" : "").'>'.$item["title"].'</option>';
				// }
                // echo '</select>';
				$priceTypeSelect->type = $s_price_type;
        		$priceTypeSelect->cid = $content["catalog_id"];
        		$priceTypeSelect->id = $content["id"];
				echo $priceTypeSelect->view();
                echo '</td>';
				echo '<td class="price cp" data-id="'.$content["id"].'">' . number_format($s_price,2,'.',' '). ' руб.</td>
				<td class="price_once_discount" style="display:none;">' . number_format($s_total / $s_count ,2,'.',' '). ' руб.</td>
				<td class="cp" title="'.$s_discount.'">
					<span class="discount_icon"></span>
					<span class="discount" data-discount="'.$s_discount.'" data-id="'.$content["id"].'">' . (empty($s_discount) ? '0' : round($s_discount, 2)). '%</span>
				</td>
				<td class="typeNds cp" style="display: none;">' .\Price::getNds($content['type_nds']).'%</td>
				<td class="nds cp" style="display: none;">' . (empty($content['nds']) ? '0' : $content['nds']). ' руб.</td>
				<td class="total cp" data-id="'.$content["id"].'">' . number_format($s_total,2,'.',' '). ' руб.</td>
				<td class="comment">';
				if(!empty($comment)){
					echo '<span style="cursor: pointer;" data-id="'.$content['id'].'" onclick="modalViewComment(this);" class="text-center">'.short(20, $comment).'</span>

					<div class="modal fade" id="modal-comm-'.$content['id'].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLabel">Комментарий</h5>
							</div>
							<div class="modal-body">
								<p>'.$comment.'</p>'.$str.'
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
							</div>
						</div>
					</div>
				</div>';
					} else {
						echo '<span class="text-center">Нет комментария</span>';
					}
				echo '</td>
				<td class="local_date_delivery" style="display:none;"><span class="local_date_delivery_span" data-pk="'.$content['id'].'" data-value="'.(is_null($content['local_date_delivery']) ? 'null' : $content['local_date_delivery']).'"></span></td>
				<td class="status text-center">' . $status_icon . '</td>';

				$clone_reserv_icon = "";
				if($clone_flag){
					$ndr_clone = get_ndr(["cid" => $content["catalog_id"], "stock_id" => $sd2["stock_id"], "module" => "sales", "pid" => $content['clone'], "returnType" => "array"]);
					$clone_reserv_icon = '<span style="padding-right: 5px; padding-left: 5px; font-size: 28px; font-weight: bold;">|</span>'.reserv_icon($idRow, $ndr_clone);
				}

				$reserv_icon = reserv_icon($idRow, $ndr);
				echo '<td class="text-center reserv">'.$reserv_icon.$clone_reserv_icon.'</td>';

				echo '<td class=""><span style="font-weight: '.(empty($dateColor) ? "" : "bold").'; color: '.$dateColor.';">'.$date_post.'</span><i class="fa fa-pencil ml10 cp text-alert datePostLocal" aria-hidden="true" style="display: none;"></i></td>
				<td class="">
					<div class="btn-group" >';

						echo '<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu pull-right">
							<li><a disabled onclick="modal_o(\'#modal-no\')" href="javascript:void(0);">Изменить</a></li>
							<li><a href="javascript:void(0);" onclick="salesCommentModal(this);" data-id="'.$content['id'].'">Комментарий</a></li>';

							echo '<li><a href="javascript:void(0);" onclick="smart_reserve('.$content['id'].')">Резерв</a></li>';

							$request_file = json_decode($content["attach_id"], true);
							if(count($request_file) > 0){
								echo '<li><a href="javascript:void(0);" onclick="file_view_request('.implode(", ", $request_file).')">Посмотреть файлы</a></li>';
							}

							echo '<li class="divider"></li>
							<li><a href="javascript:void(0);" onclick="modal_del_gen('.$content['id'].');">Отметить на удаление</a></li>
						</ul>

					</div>
				</td>
				<td class=" w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['id'] . '" type="checkbox" data-cid="'.$content["catalog_id"].'" name="checks[]" value="' . $content['id'] . '" class="checks">
						<label for="checkbox' . $content['id'] . '"></label>
					</div>
				</td>';

				if(empty($idRow)){
					echo '</tr>';
				}
		}
        if(empty($idRow)){
            echo '<tr>
            	<td colspan="6" class=" text-right"><b>Итого:</b></td>
            	<td class=" text-left"><b class="allSumPrice">'.number_format($allSumPrice,2,'.',' ').' руб.</b></td>
            	<td class=" text-left"><input type="text" placeholder="Введите процент" class="form-control" id="changePriceProcent" autocomplete="off"></td>
            	<td colspan="2" class=" text-right"><b>Итого:</b></td>
            	<td colspan="7" class=" text-left"><b class="allSum">'.number_format($total,2,'.',' ').' руб.</b></td>
			</tr>
			<tr>
				<td colspan="10" class=" text-right"><b>НДС:</b></td>
				<td colspan="7" class=""><b id="nds_total"></b></td>
			</tr>';
		}
	} else {
		echo '<tr><td colspan="17" class="">У данной заявки еще не заполнен список приборов!</td></tr>';
	}
}

function reserv_icon($idRow, $ndr){
	$no_reserv = '<span class="text-danger reservNumber" aria-hidden="true" title="Резерв пуст">0</span>';
	$reserv_icon = "";
	if(empty($idRow)){
		if($ndr["r_this"] === 0 && $ndr["r_after_this"] === 0){
			$reserv_icon .= $no_reserv;
		} else {
			if($ndr["r_this"] > 0){
				$reserv_icon .= '<span class="text-success reservNumber" aria-hidden="true" title="Резерв">'.$ndr["r_this"].'</span>';
				if($ndr["r_after_this"] > 0){
					$reserv_icon .= '<span class="reservNumber">+</span>';
				}
			}
			if($ndr["r_after_this"] > 0){
				$reserv_icon .= '<span class="text-warning reservNumber" aria-hidden="true" title="Резерв по поступлению">'.$ndr["r_after_this"].'</span>';
			}
		}
	} else {
		$reserv_icon .= $no_reserv;
	}
	return $reserv_icon;
}

function ajax_order_tovar_add(){
	if((isset($_POST['cid']))){
		$id = intval($_POST['id']);
		$mcid = $_POST['cid'];
		$request = [];
		$request["sales_id"] = $id;
		$request["sales_detail"] = [];
		foreach($mcid as $cid){
			$count = isset($_POST['count_'.$cid]) ? $_POST['count_'.$cid] : 1;
			$discount = isset($_POST['discount_'.$cid]) ? $_POST['discount_'.$cid] : 0;
			$request["sales_detail"][] = [
				"cid" => $cid,
				"count" => $count,
				"discount" => $discount,
			];
		}
		\Sales\Support\SalesDetailSupport::mass_add($request);
	}
}

function victoria_order_tovar_add($request){
	$id = isset($request["id"]) ? $request["id"] : false;
	$items = isset($request["items"]) ? $request["items"] : [];
	if($id && count($items) > 0){
		$add = [];
		$add["sales_id"] = $id;
		$add["sales_detail"] = [];
		foreach($items as $cid => $item){
			$add["sales_detail"][] = [
				"cid" => $cid,
				"count" => $item["count"],
				"discount" => $item["discount"],
			];
		}
		\Sales\Support\SalesDetailSupport::mass_add($add);
	}
}

function ajax_order_btn($id){
	global $adminTpl, $core, $db, $admin_conf, $lang;

	$query = $db->query("SELECT s.* FROM ".DB_PREFIX."_sales as s WHERE s.id = '".$id."' LIMIT 1");
	$r = $db->getRow($query);
	?>
		<div class="ph10">
			<a href="javascript:void(0);"  onclick="placeOrder();" class="holder-style p15 text-center holder-active"><span class="fa fa-edit holder-icon"></span><br> Разместить <br>заказ</a>
		</div>
		<?php
		if((int)$r["sales_type"] === 2){
			?>
		<div class="ph10">
			<a class="holder-style p15 text-center holder-active" id="doMove" onclick="doMoveModal()"><span class="fas fa-truck holder-icon"></span><br> Создать<br>перемещение</a>
		</div>
			<?php
		} else {
		?>
		<div class="ph10">
			<a class="holder-style p15 text-center" id="myCheck"><span class="fa fa-check-circle holder-icon"></span><br> Создать<br>реализацию</a>
		</div>
		<?php
			}
		?>
		<div class="ph10">
			<a href="javascript:void(0);"  onclick="modal_print(<?php echo $id;?>);" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print holder-icon"></span><br>Вывести на<br>печать</a>
		</div>
		<div class="ph10">
			<a href="javascript:void(0);"  onclick="modal_o('#modal-other');" class="holder-style p15 text-center holder-active"><span class="fa fa-save holder-icon"></span><br> Другие <br>действия</a>
		</div>
		<div class="ph10">
			<a href="javascript:void(0);" id="sendMess" class="holder-style p15 text-center holder-active"><span class="fas fa-envelope holder-icon"></span><br> Отправить <br>сообщение</a>
		</div>

		<?php
			switch($r["end"]){
				default:
					?>
						<div>Неизвестно. Обратитесь к администратору!</div>
					<?php
				break;
				case 0:
				case 3:
					?>
						<div class="ph10">
							<a href="javascript:void(0);" onclick="end_sales_modal();" class="holder-style p15 text-center holder-active"><span class="fas fa-flag-checkered holder-icon"></span><br> Закрыть <br>продажу</a>
						</div>
					<?php
				break;
				case 1:
				case 2:
					switch($r["end"]){
						case 1:
							$text = "успешно";
							$text_color = "success";
						break;
						case 2:
							$text = "не успешно";
							$text_color = "danger";
						break;
					}
					?>
						<div class="ph10">
							<a href="javascript:void(0);" onclick="start_sales_modal({id:<?php echo $id;?>, mod: 'detail'});" class="holder-style p15 text-center holder-active"><span class="fas fa-flag-checkered holder-icon"></span><br> Возродить<br>продажу</a>
						</div>
						<div>
							<small>Комментарий завершения заказа:</small><br/>
							<b><span title="Продажа закрыта <?php echo $text; ?>" class="text-<?php echo $text_color; ?>"><?php echo empty($r["comment_end"]) ? "Не указано" : $r["comment_end"];?></span></b><br>
						</div>
					<?php
				break;
			}
		?>
	<?php
}

function ajax_order_table_head($id){
	global $adminTpl, $core, $db, $admin_conf, $lang;
	$query = $db->query("SELECT s.* FROM ".DB_PREFIX."_sales as s WHERE s.id = '".$id."' LIMIT 1");
	$r = $db->getRow($query);

	$ndsText = 'Без НДС';
	if((int)$r['nds_includes'] === 1){
		$ndsText = 'С НДС';
	}
	?>

		<tr style="background-color: white; box-shadow: 0px 28px 28px -8px rgba(0,0,0,0.73);">
			<th class="w40"><span class="pd-l-sm"></span>#</th>
			<th class="w200">Номенклатура</th>
			<th class="w150 sku-units cp" data-type="1"><span>Артикул</span><span class="text-primary fas fa-sync-alt ml10"></span></th>
			<th class="w100 text-center" data-toggle="tooltip" title="Количество">Кол-во</th>

			<th class="w100 text-center cp" data-step="0" onclick="countEtcChange(this);">
				<?php
				if((int)$r['sales_type'] === 2){
					echo '<span class="countRow" title="Количество перемещенного">Кол. пер.</span>';
				} else {
					echo '<span class="countRow" title="Количество реализованного">Кол. реал.</span>';
				}
				?>
				<span class="countRow" title="Количествово собранного" style="display:none;">Кол. соб.</span>
				<span class="text-primary fas fa-sync-alt ml10"></span>
			</th>

			<th class="w100 text-center" data-toggle="tooltip" title="Наличие / Доступно / Резерв">Н/Д/Р</th>

			<?php
			if(!check_prop_cookie("purchase_price", '')){
				?>

				<th class="w200" onclick="purchasePriceChange(this);" style="cursor: pointer;" data-step="1">
					<span>Цена закупки</span>
					<span class="text-primary fas fa-sync-alt ml10"></span>
				</th>

				<?php
			}
			?>

			<th class="w250">Тип цены</th>
			<th class="w150 cp" onclick="toggle_table_column(this);" data-type="0">
				<span class="nameList">
					<span data-column="price">Цена (<?php echo $ndsText;?>)</span>
					<span style="display:none;" data-column="price_once_discount">Цена (<?php echo $ndsText;?>) за единицу товара</span>
				</span>
				<span class="text-primary fas fa-sync-alt ml10"></span>
			</th>
			<th class="w100">Скидка</th>
			<th class="w100" style="display: none;">Тип НДС</th>
			<th class="w100" style="display: none;">НДС</th>
			<th class="w200">Сумма (<?php echo $ndsText;?>)</th>
			<th class="w200 text-center cp" onclick="toggle_table_column(this);" data-type="0">
				<span class="nameList">
					<span data-column="comment">Комментарий</span>
					<span style="display:none;" data-column="local_date_delivery">Дата поставки</span>
				</span>
				<span class="text-primary fas fa-sync-alt ml10"></span>
			</th>
			<th class="w40 text-center">Статус</th>
			<th class="w40 text-center">Резерв</th>
			<th class="w150">Дата поставки</th>
			<th class="w100">Действия</th>
			<th class="w20 text-right">
				<div class="checkbox-custom mb15">
					<input id="all" type="checkbox" name="all" onclick="setCheckboxes('tablesForm', true); return true;" class="checks">
					<label for="all"></label>
				</div>
			</th>
		</tr>

	<?php
}

function ajax_order_data($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($id))
	{
		$query_rel = $db->query("SELECT pid_in FROM ".DB_PREFIX."_relations WHERE module_in = 'reestr' AND module_out = 'sales' AND pid_out = '".$id."' LIMIT 1");
		$reestr_id = "";
		$spec_add = true;
		$spec = "Спецификация еще не создана";
		if($db->numRows($query_rel) > 0)
		{
			$rel = $db->getRow($query_rel);
			$reestr_id = $rel['pid_in'];
			$dogovor = \Reestr\ReestrModule::pact_template($reestr_id);
			$query_rel = $db->query("SELECT pid_in FROM `".DB_PREFIX."_relations` WHERE module_in = 'reestr_spec' AND module_out = 'sales' AND pid_out = '".$id."'");
			if($db->numRows($query_rel) > 0){
				$rr = [];
				while($qr = $db->getRow($query_rel)){
					$rr[] = $qr["pid_in"];
				}
				$rr = in_reestr($reestr_id, $rr);
				if(count($rr) > 0){
					$query = $db->query("SELECT * FROM `".DB_PREFIX."_reestr_doc` WHERE id = '".$rr[0]["id"]."' LIMIT 1");
					$spec_doc = $db->getRow($query);
					$spec = '<a target="_blank" href="/administration/module/reestr/view/'.$reestr_id.'&doc_id='.$spec_doc["id"].'">Спецификация №'.$spec_doc["spec_numb"].' от '.date('d.m.Y', strtotime($spec_doc["date_create"])).'</a>';
					$spec_add = false;
				}
			}
		}
		else
		{
			$dogovor = 'Договор ещё не создан';
			$d_flag=false;
			$spec = false;
		}
		// $query_rel = $db->query("SELECT pid_in FROM ".DB_PREFIX."_relations WHERE module_in = 'object' AND module_out = 'sales' AND pid_out = '".$id."' LIMIT 1");
		// if($db->numRows($query_rel) > 0)
		// {
		// 	$rel = $db->getRow($query_rel);
		// 	$query_doc = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '".$rel['pid_in']."'");
		// 	$doc = $db->getRow($query_doc);
		// 	$obj = '<b>'.$doc['name'].' ('.$doc['address'].')</b>&nbsp;&nbsp;<a target="_blank" href="'.ADMIN.'/object/view/'.$rel['pid_in'].'"><span class="fa fa-external-link"></span></a>';
		// 	$d_flag=true;
		// }
		// else
		// {
		// 	$query_rel = $db->query("SELECT pid_out FROM ".DB_PREFIX."_relations WHERE module_out = 'object' AND module_in = 'sales' AND pid_in = '".$id."' LIMIT 1");
		// 	if($db->numRows($query_rel) > 0)
		// 	{
		// 		$rel = $db->getRow($query_rel);
		// 		$query_doc = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '".$rel['pid_out']."'");
		// 		$doc = $db->getRow($query_doc);
		// 		$obj = '<b>'.$doc['name'].' ('.$doc['address'].')</b>&nbsp;&nbsp;<a target="_blank" href="'.ADMIN.'/object/view/'.$rel['pid_out'].'"><span class="fa fa-external-link"></span></a>';
		// 		$d_flag=true;
		// 	}
		// 	else
		// 	{
		// 		$obj = 'Объект еще не привязан';
		// 		$d_flag=false;
		// 	}
		// }

		$uid = $core->auth->user_info['id'];

		$query = $db->query("SELECT s.*, o.name as oname, obj.name as objname, obj.address as objaddress FROM ".DB_PREFIX."_sales as s LEFT JOIN ".DB_PREFIX."_organizations AS o on(s.oid=o.id) LEFT JOIN ".DB_PREFIX."_object as obj on (s.object = obj.id) WHERE s.id = '" . $id . "'");
		$order = $db->getRow($query);
		$countComm = $db->numRows($db->query("SELECT c.*, u.surname as surname, u.name as name, u.ochestvo as ochestvo FROM `" . DB_PREFIX . "_comments_admin` as c LEFT JOIN `" . DB_PREFIX . "_users` as u ON(c.uid = u.id) WHERE c.module = 'sales' AND c.module_id = '".$id."'"));
		echo '<input type="checkbox" value="1" id="includesNds" '.($order['nds_includes'] == 1 ? 'checked' : '').' style="display: none;">
		<div class="col-md-2">
				<small>Контрагент:</small><br/>';
					if (!empty($order['oname']))
					{
						$fc_organization = new FastCreate("organization");
						$fc_org = $fc_organization->get_fast_create();
						echo '
							<b id="counterparty" data-id="'.$order["oid"].'">
							'.$order['oname'].'
							</b>
							<a href="javascript:void(0);" class="mySelect" onclick="check_realization_active(null, '.$id.').then(() => {modal_o(\'#modal-oid-edit\'); '.oSelect("jsReturn", "oid", $order["oid"]).'}, (error_id) => {error_modal_show(error_id);})">(изменить)</a>
							&nbsp;&nbsp;
							<a target="_blank" href="'.ADMIN.'/module/organizations/'.$order['oid'].'"><span class="fa fa-external-link"></span></a>
							<i class="fas fa-plus cp text-success" '.$fc_org->button_open.'></i>
						';
					}
					else
					{
						echo '<b>Не указан</b> <a href="javascript:void(0);" class="mySelect" onclick="modal_o(\'#modal-oid-edit\'); '.oSelect("jsReturn", "oid", $order["oid"]).'">(изменить)</a>';
					}
					echo '
						<div id="modal-oid-edit" class="popup-basic admin-form mfp-with-anim mfp-hide" >
						<div class="panel">
							<div class="panel-heading"><span class="panel-title"><i class="fa fa-suitcase"></i>Изменить контрагента</span></div>
							<form action="javascript:void(null);" id="oid-edit" onsubmit="ajax_send_data(\'oid-edit\', \'ajax.php?do=order_oid_edit\');">
								<div class="panel-body p25">
									<div class="section">
										<label for="oid" class="field prepend-icon">';
											oSelect("select", "oid", $order["oid"]);
											echo	'<label for="oid" class="field-icon"><i class="fa fa-suitcase"></i></label>
										</label>
									</div>
								</div>
								<div class="panel-footer">
									<input type="hidden" name="id" value="'.$id.'" />
									<input type="submit" class="button btn-primary" value="Изменить" />
								</div>
							</form>
						</div>
					</div>



					<br/><br/>
					<small>Ответственный:</small><br/>
					<b>'. shortUserName($order['uid']).'</b>&nbsp;';

					// $groups_allow = getGroup(["group" => "admin"]);
					// $groups_allow[] = $order['uid'];
					// $groups_allow[] = 3;
					// if($core->auth->allowAccessByName($uid, "responsibleSellingChange") || empty($order['uid']) || $uid == $order['uid']){
					// 	echo '<a href="javascript:void(0);" onclick="modal_o(\'#modal-uid-edit\');  $(\'.select2-single\').select2();">(изменить)</a>';
					// }

					$object_id = empty($order['object']) ? null : $order['object'];

					echo '<br/><br/>
					<small>Основной объект:</small><br/>
					<b>'.(empty($order["objname"]) ? "Не указан" : $order["objname"]).'</b> <a href="javascript:void(0);"
					onclick="modal_o(\'#modal-object-edit\');  $(\'.select2-single\').select2();
					'.oSelect('jsReturn', 'object_id', $object_id, 'getObjectToSelect').' '.oSelect('jsReturn', 'object_oid', $order['oid'], 'getOrganizations').'">(изменить)</a>
					<div id="modal-object-edit" class="popup-basic admin-form mfp-with-anim mfp-hide">
						<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-briefcase"></i>Прикрепить объект</span></div>
								<form id="object-edit" action="javascript:void(null);" onsubmit="ajax_send_data(\'object-edit\', \''.ADMIN.'/module/sales/ajax/order_object_edit\');">
									<div class="panel-body p25">
										<div class="section">
											Номер закупки: <br>
											<div class="smart-widget sm-right smr-50">
											<label class="field prepend-icon">';
                                                        oSelect("select", "object_id", $object_id, "getObjectToSelect");
													echo '<label for="old_cat" class="field-icon"><i class="fa fa-briefcase"></i></label>
												</label>
												<button onclick="showhide(\'addition\')" type="button" class="button"><i class="fa fa-plus"></i></button>
											</div>
										</div>
										<div id="addition" style="display: none">
										<div class="section" >
											Выберите контрагента: <br>';
											oSelect("select", "object_oid", $order['oid'], "getOrganizations");
											echo '</div>
											<div class="section">
											Имя объекта: <br>
											<label for="object_name" class="field prepend-icon">
												<input id="object_name" type="text" name="object_name" value="" placeholder="Укажите имя объекта" class="gui-input">
												<label for="object_name" class="field-icon"><i class="fa fa-keyboard-o"></i></label>
											</label>
										</div>
										<div class="section">
											Адрес объекта: <br>
											<label for="object_address" class="field prepend-icon">
												<input id="object_address" type="text" name="object_address" value="" placeholder="Укажите адрес объекта" class="gui-input">
												<label for="object_address" class="field-icon"><i class="fa fa-map-marker"></i></label>
											</label>
										</div>
										</div>
									</div>
									<div class="panel-footer">
										<input type="hidden" name="id" value="'.$id.'" />
										'.(!empty($order['object']) ? '<input type="hidden" name="object_old" value="'.$order['object'].'" />' : '' ).'
										<input type="submit" class="button btn-success" value="Прикрепить" />
									</div>
								</form>
							</div>
					</div>
					<br/><br/>
					<small>Статус клиента:</small><br/>';
					$status_client_list = \Sales\Model\Sales::getStatusClient();
					echo '<b>'.$status_client_list[ $order["status_client"] ].'</b><br/><br/>
					<small>Договор: <i class="fas fa-plus text-success cp" onclick="reestr_modal();"></i></small><br/>
					<b>'.$dogovor.'</b><br/><br/>';
					if($spec){
						echo '<small>Спецификация: '.($spec_add ? '<i class="fas fa-plus text-success cp" onclick="reestr_spec_modal({sales_id:'.$id.', reestr_id:'.$reestr_id.'});"></i>' : '').'</small><br/>
						<b>'.$spec.'</b><br/><br/>';
					}
					echo '</div>
										<div class="col-md-2">
													<small>Дата подачи заявки:</small><br/>
													<b class="cpx" id="date">'. date("d.m.Y", strtotime($order['date'])).'</b><br/><br/>';

													/*
													echo '<small>Номер заказа УТ:</small><br/>
													<b>'. (!empty($order['ytid']) ? $order['ytid'] : $lang['not_specified']).'</b><br/><br/>';
													*/

													echo '<small>Сумма заказа:</small><br/>
													<b class="allSum">'. number_format($order['total'],2,'.',' ').' руб.</b><br/><br/>
													<small>Комментарий:</small><br/>
													<b><a href="javascript:void(0);" onclick="modal_o(\'#modal-comm\'); ajaxGet(\''.ADMIN.'/module/sales/ajax/comments_get/'.$order['id'].'\', \'sale_comments_list\');uploaderMaster();">Посмотреть комментарии</a></b> <span class="badge badge-danger">'.$countComm.'</span><br/><br/>

													<small>Тип продажи:</small><br/>
													<input type="hidden" id="sales_type_source" value=\''.json_encode(\Sales\Support\SalesSupport::sales_type_list_view(), JSON_UNESCAPED_UNICODE).'\'>
													<b id="sales_type" class="cpx" data-value="'.$order['sales_type'].'">'. \Sales\Model\Sales::getSalesType()[ $order['sales_type'] ].'</b><br/><br/>



                                                    <div id="modal-comm" class="popup-basic bg-none mfp-with-anim mfp-hide">
                                                    <form id="sale_comments" action="javascript:void(null);" onsubmit="ajax_comments_send(\'sale_comments\', \''.ADMIN.'/module/sales/commSave/'.$order['id'].'\');">
                                                    <div class="panel">
                                                    <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">Комментарии</span></div>
                                                    <div class="panel-body">
                                                    <h3 class="mt5">Комментарии:</h3>
													<div id="sale_comments_list" style="max-height: 300px; overflow-y: scroll;">

                                                    </div>
                                                    <hr class="short alt">

													<textarea class="form-control" rows="5" name="comm" id="comm" placeholder="Комментарий..." required></textarea>';
                                                    $attribute = [];
                                                    $attribute["name"] = "";
                                                    echo uploaderMaster("upload", $attribute);
                                                    echo uploaderMaster("view", $attribute);

                                                    echo '</div>
                                                    <div class="panel-footer">
                                                    <input type="submit" value="Добавить комментарий" class="btn btn-success">
                                                    </div>
                                                    </div>
                                                    </form>
                                                    </div>';

													echo '<label for="purchasePriceCheckbox"><input type="checkbox" id="purchasePriceCheckbox" onchange="purchasePriceColumnToggle(this);" '.(check_prop_cookie("purchase_price", '') ? "checked" : "").'> Скрыть цену закупки</label><br/><br/>';

													$extendedSort = new \ExtendedSort;

													?>
														<div>
															<i class="fas fa-balance-scale text-success fs30 cp" onclick="fixSupplyModal(this);" title="Уравнитель"></i>
															<i class="fas fa-comment-medical text-primary fs30 cp ml20" onclick="append_chat();" title="Добавиться в чат"></i>
															<?php
															echo $extendedSort->button(["class" => "ml20"]);
															if($order["clone_active"] == 1){
																?>

																<i class="far fa-handshake text-alert fs30 cp ml20" onclick="automatic_sales_modal();" title="Перепродажа на склад партнёра"></i>

																<a href="/administration/module/sales/order/<?php echo $order["clone"];?>" target="_blank">
																	<i class="fas fa-ghost text-alert fs30 cp ml20" title="Ссылка на клона"></i>
																</a>

																<?php
															}
															?>
															<i class="fas fa-users-cog text-primary fs30 ml20 cp" title="Пользователи продажи" onclick="sales_users_modal();"></i>
														</div>

													<?php

										echo '</div>
										<div class="col-md-2">
													<small>Оплата:</small><br/>';
													$order_pay = (int)$order['pay'];

													switch($order_pay){
														default:
														case 0:
															$pay = '<font color="red">Не оплачен</font>';
														break;
														case 1:
															$pay = '<font color="yellow">Частично оплачен</font>';
														break;
														case 2:
															$pay = '<font color="green">Оплачен</font>';
														break;
													}
													echo '<b id="pay" class="cp">'.$pay.'</b><br/><br/>';

                                                    $org = getOrgInfo($order['org']);
                                                    if(!$org) {
                                                        $org = 'Организация не найдена';
                                                    } else {
                                                        $org = $org['organizations']['name'];
                                                    }
													echo '<small>Организация:</small><br/>
													<b id="org" class="cpx" data-value="'.$order['org'].'">'.$org.'</b><br/><br/>';
													$stock_name = empty($order['stock_id']) ? "Не указано" : get_stock_name($order['stock_id']);
													echo '<small>Склад:</small><br/>
													<b id="stock_id" class="cpx" data-value="'.$order['stock_id'].'">'.$stock_name.'</b><br/><br/>';
													$cookie_stock_id_view = empty($_COOKIE["stock_id_view"]) ? false : $_COOKIE["stock_id_view"];
													$stock_id_view = $cookie_stock_id_view ? $cookie_stock_id_view : $order['stock_id'];
													$stock_name_view = empty($stock_id_view) ? "Не указано" : get_stock_name($stock_id_view);
													echo '<small>Склад отображения:</small><br/>
													<b id="stock_id_view" class="cpx" data-value="'.$stock_id_view.'">'.$stock_name_view.'</b>';
													if($cookie_stock_id_view){
														echo '<i class="fas fa-times ml20 text-danger cp" onclick="clear_stock_id_view();"></i>';
													}
													echo '<br/><br/>';
										echo '</div>';

		echo '<div class="col-md-2">';
		$source_appeal_list = \Sales\Model\Sales::getSourceAppeal();
		$source_appeal = $source_appeal_list[ $order['source_appeal'] ] ?? "Не указано";
		echo '<input type="hidden" id="source_appeal_source" value=\''.json_encode($source_appeal_list, JSON_UNESCAPED_UNICODE).'\'>';
		echo '<small>Источник обращения:</small><br/>
		<b id="source_appeal" class="cpx" data-value="'.$order['source_appeal'].'">'.$source_appeal.'</b><br/><br/>';

		$market_segment_list = \Sales\Model\Sales::getMarketSegment();
		$market_segment = $market_segment_list[ $order['market_segment'] ] ?? "Не указано";
		echo '<input type="hidden" id="market_segment_source" value=\''.json_encode($market_segment_list, JSON_UNESCAPED_UNICODE).'\'>';
		echo '<small>Сегмент рынка:</small><br/>
		<b id="market_segment" class="cpx" data-value="'.$order['market_segment'].'">'.$market_segment.'</b><br/><br/>';

		$by_field_activity_list = \Sales\Model\Sales::getByFieldActivity();
		$by_field_activity = $by_field_activity_list[ $order['by_field_activity'] ] ?? "Не указано";
		echo '<input type="hidden" id="by_field_activity_source" value=\''.json_encode($by_field_activity_list,JSON_UNESCAPED_UNICODE).'\'>';
		echo '<small>По сфере деятельности:</small><br/>
		<b id="by_field_activity" class="cpx" data-value="'.$order['by_field_activity'].'">'.$by_field_activity.'</b><br/><br/>';

		if ($order['sales_type'] == '1')
		{
			echo '<small>Категория продажи:</small><br/>
			<b id="sales_cat" class="cpx" data-value="'.$order['sales_cat'].'">'. \Sales\Model\Sales::getSalesCat()[ $order['sales_cat'] ].'</b><br/><br/>';
		}
		echo '</div>';

	}
}

function ajax_order_uid_edit()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$uid = $_POST['uid'];
		$request = [];
		$request["uid"] = $uid;
		\Sales\Model\Sales::find($id)->update($request);

		$clone_sales = new \Sales\CloneSales(["parent_id" => $id]);
		$clone_sales->update_parent($request);
		// $db->query("UPDATE `" . DB_PREFIX . "_sales` SET `uid` = '".$uid."' WHERE `id` = " . $id . " LIMIT 1 ;");
		$chat = new Chat();
		$chat_data = $chat->find_chat(["where" => ["module" => "sales", "pid" => $id]]);
		if(count($chat_data) > 0){
			foreach($chat_data as $chat_d){
				$chat->add_users($chat_d["id"], [$uid]);
			}
		}

		echo 'Информация успешно сохранена!';
	}
}

function ajax_order_object_edit()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$uid = $core->auth->user_info['id'];
		$object_id = isset($_POST['object_id']) ? intval($_POST['object_id']) : 0;
		$object_oid = isset($_POST['object_oid']) ? intval($_POST['object_oid']) : 0;
		$object_name = isset($_POST['object_name']) ? filter(htmlspecialchars($_POST['object_name'], ENT_QUOTES)) : '';
		$object_address = isset($_POST['object_address']) ? filter(htmlspecialchars($_POST['object_address'], ENT_QUOTES)) : '';
		//($object_id == 0)&&
		if ( (!empty($object_name)) && (!empty($object_address)) )
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_object` ( `id` , `id_client` , `uid` , `date`, `address`, `name`, `status`) VALUES (NULL, '" . $object_oid . ",', '" . $uid . "', '" . time() . "', '" . $object_address . "', '" . $object_name . "','1');");
			$object_id  = $db->insert_id();
		}
		if (!empty($_POST['object_old'])){
			relationsDelete('sales', $id, 'object', intval($_POST['object_old']));
		}
		relationsSave('sales', $id, 'object', $object_id);

		$db->query("UPDATE `" . DB_PREFIX . "_sales` SET `object` = '".$object_id."' WHERE `id` = " . $id . " LIMIT 1 ;");

		$clone_sales = new \Sales\CloneSales(["parent_id" => $id]);
		if($clone_sales->check_clone()){
			$clone_sales->update_parent(["object" => $object_id]);
			if (!empty($_POST['object_old'])){
				relationsDelete('sales', $clone_sales->clone_parent_id, 'object', intval($_POST['object_old']));
			}
			relationsSave('sales', $clone_sales->clone_parent_id, 'object', $object_id);
		}

		echo 'Информация успешно сохранена!';
	}
}

function ajax_order_device_del(){
	if(isset($_POST['id'])){
		$id = intval($_POST['id']);

		$sales = \Sales\Model\SalesDetail::find($id);
		$sales->delete($id);
		\Sales\Support\SalesDetailSupport::recount_sales($sales->sales_id);
	}
}

function order_uid_set($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($id))
	{
		$uid = $core->auth->user_info['id'];
		$db->query("UPDATE `" . DB_PREFIX . "_sales` SET `uid` = '".$uid."' WHERE `id` = " . $id . " LIMIT 1 ;");

		$chat = new Chat();
		// $chat_data_1 = $chat->find_chat("sales", $id, false, 1);
		$chat_data_1 = $chat->find_chat(["once" => "/", "where" => ["module" => "sales", "pid" => $id, "type" => 1]]);
		//
		// $chat_data_2 = $chat->find_chat("sales", $id, false, 2);
		$chat_data_2 = $chat->find_chat(["once" => "/", "where" => ["module" => "sales", "pid" => $id, "type" => 2]]);
		if(!empty($chat_data_1)){
			$chat->add_users($chat_data_1["id"], [$uid]);
		}
		if(!empty($chat_data_2)){
			$chat->add_users($chat_data_2["id"], [$uid]);
		}

		header('Location: /'.ADMIN.'/module/sales/order/'.$id);
	}
	else
	{
		header('Location: /'.ADMIN.'/module/sales');
	}
}

function union_tov_sales($data){
    global $db;
    if($data){
		$sales_id = "";
        foreach($data as $key => $val){
            if(!empty($val["union"])){
                $count = 0;
                if(count($val["union_list"]) > 0){
                    $id_str = implode(", ", $val["union_list"]);
                    $query = $db->query("SELECT SUM(count) as sum_count FROM ".DB_PREFIX."_sales_detail WHERE id IN(".$id_str.")");
                    if($db->numRows($query) > 0){
                        $r = $db->getRow($query);
                        $count = $r["sum_count"];
                        if(($key = array_search($val["union"],$val["union_list"])) !== FALSE){
                            unset($val["union_list"][$key]);
                        }
                        $query = $db->query("SELECT sd.*, s.nds_includes, s.org
                        FROM `".DB_PREFIX."_sales_detail` as sd
                        LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sd.sales_id)
                        WHERE sd.id = '".$val["union"]."' LIMIT 1");
						$r = $db->getRow($query);
						$sales_id = $r["sales_id"];

                        $query = $db->query("SELECT nds FROM ".DB_PREFIX."_org WHERE id = '".$r["org"]."' LIMIT 1");
                        $r1 = $db->getRow($query);
                        $nds = is_null($r1["nds"]) ? 3 :$r1["nds"];

                        //$price_type = $r["price_type"];
			            $price_rub = $r["s_price"];
                        $price_rub_dicount = $price_rub * ((100 - $r["discount"]) / 100);

			            $ndsCalc = 0;
			            $typeNds = \Price::getNds($nds);
			            if($r["nds_includes"] === "1"){
			            	$ndsCalc = ($price_rub_dicount * $typeNds / (100 + $typeNds)) * $count;
			            } else {
			            	$ndsCalc = ($price_rub_dicount * ($typeNds / 100)) * $count;
			            }

			            $ndsCalc = round($ndsCalc, 2, PHP_ROUND_HALF_UP);

			            $total = 0;
			            if($r["nds_includes"] === "1"){
			            	$total = $price_rub_dicount * $count;
			            } else {
			            	$total = ($count * $price_rub_dicount) + $ndsCalc;
			            }
                        $total = round($total, 2, PHP_ROUND_HALF_UP);

                        $discount_sum = $price_rub * $count - $price_rub_dicount;

						$db->query("UPDATE `".DB_PREFIX."_sales_detail` SET count = '".$count."', discount_sum = '".$discount_sum."', nds = '".$ndsCalc."', total = '".$total."' WHERE id = '".$val["union"]."' LIMIT 1");

						$id_str = implode(", ", $val["union_list"]);
						if(!empty($id_str)){
							$db->query("UPDATE `".DB_PREFIX."_sales_detail` SET del = '1' WHERE id IN(".$id_str.")");
						}
                    }
                }
            }
		}
		if(!empty($sales_id)){
			$query = $db->query("SELECT SUM(total) as sum_total, SUM(nds) as sum_nds FROM ".DB_PREFIX."_sales_detail WHERE id IN(".$id_str.")");
			$r = $db->getRow($query);
			$total = round($r["sum_total"], 2);
			$nds = round($r["sum_nds"], 2);
			$db->query("UPDATE `".DB_PREFIX."_sales` SET total = '".$total."', nds = '".$nds."' WHERE id = '".$sales_id."' LIMIT 1");
		}
    }
}

function check_reserv_uid($request){
	global $core;
	$uid = $core->auth->user_info['id'];
	$id = isset($request["id"]) ? $request["id"] : [];
	$result = [];
	$allow = getGroup(["group" => ["admin", "director"]]);
	$allow[] = 6;
	if(in_array($uid, $allow)){
		return $result;
	}
	if(count($id) > 0){
		$reserv = new Reserv;
		foreach($id as $pid){
			$flag = $reserv->check_reserv_uid($pid, "sales", 1);
			if(!is_null($flag)){
				if(!$flag){
					$result[] = $pid;
				}
			}
		}
	}
	return $result;
}

function load_max_discount_sales($id){
	global $db;
	$data = [];
	$query = order_tovar_list_query($id);
	while($r = $db->getRow($query)){
		$price_data = json_decode($r["price_data"], true);
		if(empty($price_data)){
			$data[] = [
				"id" => $r["id"],
				"max_allowable_discount" => "Не указано",
			];
		} else {
			foreach($price_data["data"] as $item){
				if(isset($item["field"]) && $item["field"] == "max_allowable_discount"){
					$data[] = [
						"id" => $r["id"],
						"max_allowable_discount" => $item["value"],
					];
					break;
				}
			}
		}
	}
	return $data;
}

function get_discount_oid_sales($request){
	global $db;
	$sales_id = isset($request["sales_id"]) ? $request["sales_id"] : false;
	$id = isset($request["id"]) ? $request["id"] : [];
	$data = ["items" => [], "query" => null];
	if(count($id) > 0 && $sales_id){
		$idi = implode(", ", $id);
		$s_data = \Sales\Model\Sales::find($sales_id);
		$qd = \Price::get_discount_query($s_data->oid);
		$q = "SELECT sd.id, sd.cid, sd.price_type, cg.discount as discount FROM `".DB_PREFIX."_sales_detail` as sd
		LEFT JOIN (
			{$qd}
		) as cg ON(sd.cid = cg.cid)
		WHERE sd.id IN({$idi})";
		$data["query"] = $q;
		$query = $db->query($q);
		if($db->numRows($query) > 0){
			while($r = $db->getRow($query)){
				// if($r["price_type"] == 4){
				// 	$r["price"] = null;
				// } else {
				// 	$price = \Price::getCatalogPrice(["cid" => $r["cid"], "type" => $r["price_type"]]);
				// 	$r["price"] = $price["price"];
				// }
				$data["items"][] = $r;
			}
		}
	}
	return $data;
}

function get_count_assembly_sales($sales_id){
	$sales_detail = \Sales\Model\SalesDetail::where("sales_id", $sales_id)->where("del", 0)->get();
	$assembly = new Storage\Assembly;
	$data = [];
	foreach($sales_detail as $item){
		$data[] = [
			"id" => $item->id,
			"count" => $assembly->get_count($item->id),
		];
	}
	return $data;
}