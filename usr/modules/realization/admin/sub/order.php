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

function order($id) 
{
	global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	$query = $db->query("SELECT s.*, o.name as oname  FROM ".DB_PREFIX."_realization as s LEFT JOIN ".DB_PREFIX."_organizations AS o on(s.oid=o.id)  WHERE s.id = '" . $id . "'");
	$order = $db->getRow($query);
	$numb = (!empty($order['numb']) ? $order['numb'] : module_perfix('realization', $id));
	$adminTpl->admin_head($lang['realization'] .' | '. $lang['realization_order_numb'].$numb);
	$uid = $core->auth->user_info['id'];
	realization_head_nav($id, 'order');
	

	//модальное окно печати заказа
	echo '<div id="modal-print" class="popup-basic bg-none mfp-with-anim mfp-hide">
			<div class="panel">
				<div class="panel-heading"><span class="panel-icon"><i class="far fa-check-square"></i></span><span class="panel-title">Выберите дальнейшее действие</span></div>
					<div class="panel-body">
						<h3 class="mt5">Какими образом вы хотите разместить заказ?</h3>
						<hr class="short alt">
						<p>                          
                           <div class="">
                              <table class="table list-table">
                                <tbody>
                                  <tr>
                                    <td onclick="window.open(\''.ADMIN.'/module/realization/print/upd/'. $id.'&format=PDF\',\'_blank\'); return false;"  style="cursor: pointer;"><span class="icon text-info fa fa-check-square"></span>Распечатать УПД</td>
                                    <td class="text-right"> 
                                		<a href="'.ADMIN.'/module/realization/print/upd/'. $id.'&format=DOC" target="_blank">DOC</a> 
                            		</td>
                                  </tr>
                                  <tr>
                                    <td onclick="window.open(\''.ADMIN.'/module/realization/print/rashod/'. $id.'&format=PDF\',\'_blank\'); return false;" style="cursor: pointer;"><span class="icon text-info fa fa-check-square"></span>Распечатать расходную накладную</td>
                                    <td class="text-right"> 
                                		<a href="'.ADMIN.'/module/realization/print/rashod/'. $id.'&format=DOC" target="_blank">DOC</a> 
                            		</td>
                                  </tr>			
								   <tr>
                                    <td onclick="window.open(\''.ADMIN.'/module/realization/print/rashod_price/'. $id.'&format=PDF\',\'_blank\'); return false;" style="cursor: pointer;"><span class="icon text-info fa fa-check-square"></span>Распечатать расходную накладную с ценами</td>
                                    <td class="text-right"> 
                                		<a href="'.ADMIN.'/module/realization/print/rashod_price/'. $id.'&format=DOC" target="_blank">DOC</a> 
                            		</td>
                                  </tr>	 
                                </tbody>
                              </table>
                            </div>                        
						</p>
					</div>
					<div class="panel-footer text-right">
						<button type="button" onclick="$(\'#modal-print\').magnificPopup(\'close\');" class="btn btn-danger">Закрыть</button>						
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
                                  </tr>
                                  <tr>
                                    <td style="cursor: pointer;" onclick="modal_o(\'#modal-doc\', \'#modal-other\');"><span class="icon text-alert fa fa-ambulance"></span>Создать договор</td>
                                    <td class="text-right"></td>
                                  </tr>
                                  <tr>
                                    <td style="cursor: pointer;"><span class="icon text-info fa fa-bullhorn"></span>Отменить заказ</td>
                                    <td class="text-right"></td>
                                  </tr>								
                                </tbody>
                              </table>
                            </div>                        
						</p>
					</div>
					<div class="panel-footer text-right">
						<button type="button" onclick="$(\'#modal-other\').magnificPopup(\'close\');" class="btn btn-danger">Закрыть</button>						
					</div>
				</div>
			</div>';
	
	xeditableInit();    

	
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
	  $(".select2-single").select2();
		';
	
    $attribute = [];
    $attribute["name"] = "";	
    $attribute["script"] = true;	
	echo uploaderMaster("css", $attribute);
    $attribute["script"] = false;		
    $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
	echo '<section id="content" class="animated fadeIn">';
	echo '<div class="text-center">            
            <p class="lead" style="text-transform: uppercase;">'.$lang['realization_order_numb'].$numb.'</p>
			</div>';

		oSelect("jsAdminTpl", "oid", $order["oid"], "getOrganizations");
	
	echo '<input type="hidden" value="" id="saveData">
    <input type="hidden" value="'.$id.'" id="mainId">
	<input type="checkbox" value="1" id="includesNds" '.($order["nds_includes"] == 1 ? 'checked' : '').' style="display:none;">
		<div class="row" id="admin-form">			
			<div class="panel mb25">										
				<div class="panel-body p20 pb10">									
					<div class="section row mbn admin-form">
						<div class="col-md-12 pl15">
							<div class="section row mb15" id="orderData">';
								ajax_order_data($id);												
							echo '</div>							
							</div>
						</div>
					
					</div>
				</div>
				
                <div class="modal fade" id="addFileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Приложить файл</h5>
                      </div>
					  <div class="modal-body">
					  <form id="addFileModalForm" onsubmit="return false;">';
					  	$attribute = [];
					  	$attribute["name"] = "1";
					  	$attribute["script"] = true;
					  	$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
					  	$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
					  	echo uploaderMaster("css", $attribute);
					  	echo uploaderMaster("upload", $attribute);
					  	echo uploaderMaster("view", $attribute);
                      echo '</form></div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary universalBtn" data-dismiss="modal" id="addFileModalBtn">Добавить</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                      </div>
                    </div>
                  </div>
				</div>
				
				<div class="modal fade" id="delFileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Удалить файл</h5>
                      </div>
                      <div class="modal-body">
                        Вы действительно хотите удалить файл?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary universalBtn" data-dismiss="modal" id="delFileModalBtn">Удалить</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                      </div>
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
								<button type="button" onclick="location.href = \''.ADMIN.'/module/sales/send_order/'. $id.'\'" class="btn btn-success">Отправить</button>
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
		ajaxGet("'.ADMIN.'/module/realization/ajax/comments_get/'.$id.'", "sale_comments_list");		
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
					ajaxGet("'.ADMIN.'/module/realization/ajax/order_data/'.$id.'", "orderData");		
				}          
			});
		}
        
        function uploaderMaster(){
            '.uploaderMaster("script", ["name" => "1", "script" => false]).'
        }
	</script>
    
    <script>
    $(function(){
        $(document).on("change", ".checks", function(){
            //console.log("~");
            let flag = false;
            $(".checks").each(function(i, elem){
                if($(elem).prop("checked")) flag = true;
            });
            if(flag) {$("#myCheck").addClass("holder-active");}
            else {$("#myCheck").removeClass("holder-active");}
        });
		$("#myCheck").click(function(){
                                    let flag = false;
                                    $(".checks").each(function(i, elem){
                                    if($(elem).prop("checked")) flag = true;
                                    });
                                    if(flag){
                                        let msg = $("#tablesForm").serialize();
                                    $.ajax({
                                            type: "POST",
                                            url: "'.ADMIN.'/module/sales/checkGoods",
                                            data: msg,
                                            success: function(data) {
                                                if(data == "good"){
                                                    $.ajax({
														type: "POST",
														url: "'.ADMIN.'/module/sales/confirm_receipt/'.$id.'",
														data: msg,
														success: function(echoStr) {
															notif("success", "Данные отправлены!", "Проведенные товары: "+echoStr+"");
															$("#myModal").modal("hide");
															$("#numDoc").val("");
														},
													});
                                                } else {
                                                    notif("danger", "Предупреждение!", "Проведенные товары: "+data+"");
                                                }
                                            },
                                        });
                                    }
                                    else
                                    notif("danger", "Предупреждение!", "Выбирите товары");    
        });
    });
    </script>
    <link rel="stylesheet" href="/usr/modules/realization/admin/asset/css/realizationOrder.css">
    
    <style>
    .cp{
        cursor: pointer;
    }
    .cpx{
        cursor: pointer;
        border-bottom: 1px #0088cc dashed;
    }
    .hiddeBlock{
        display: none!important;
	}
	.loadData{
		filter: blur(5px);
	}
	.modal-open{
		overflow: scroll;
	}
    </style>';
	
	// if($uid == 33 || $uid == 1){
		// $torg = new \Realization\Torg;
		// echo $torg->modal(["realization_id" => $id]);
	// } else {
		echo '<div class="modal fade bd-example-modal-lg" id="torg12Modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title" id="exampleModalLabel">Реквизиты печати ТОРГ-12</h5>
			</div>
			<form method="POST" id="torg12Form" onsubmit="return false;">
  
			  <input type="hidden" value="'.$id.'" id="realization_id" name="realization_id">
  
			  <input type="hidden" id="sales_id" name="sales_id" value="">
  
			<div class="modal-body" id="torg12ModalBody">
			  <div class="container-fluid admin-form">
				  <div class="row">
					  <div class="col-sm-12">
						  <div class="mh">Реквизиты печати ТОРГ-12</div>
					  </div>
				  </div>
				  <div class="row">
				  <div class="col-sm-12">
						  <div class="col-sm-12">
							  <div class="greenH">Основание</div>
						  </div>
						  <div class="col-sm-4">
							  <span>Документ:</span>
							  <input type="text" class="form-control" id="base_doc" name="base_doc" placeholder="Введите основание документа">
						  </div>
						  <div class="col-sm-4">
							  <span>Номер:</span>
							  <input type="text" class="form-control" id="base_numb" name="base_numb" placeholder="Введите номер документа">
						  </div>
						  <div class="col-sm-4">
							  <span>Дата:</span>
							  <input type="date" class="form-control" id="base_date" name="base_date">
						  </div>
				  </div>
				  </div>
				  <div class="row">
					  <div class="col-sm-6 ">
						  <div class="col-sm-12">
							  <div class="greenH">Покупатель</div>
							  
							  <input type="hidden" value="" id="oid_id" name="oid_id">
  
						  </div>
						  <div class="col-sm-12">
							  <span>Счет покупателя:</span>
							  <select class="form-control" id="oid_bank_id" name="oid_bank_id"></select>
  
							  <input type="hidden" value="" id="oid_bank_data" name="oid_bank_data">
  
						  </div>
						  <div class="col-sm-12">
							  <span>Грузополучатель:</span>
							  <select class="form-control" id="" name="" disabled></select>
						  </div>
						  <div class="col-sm-12">
							  <span>Счет грузополучателя:</span>
							  <select class="form-control" id="" name="" disabled></select>
						  </div>
						  <div class="col-sm-12">
							  <span>Адрес доставки:</span>
							  <select class="form-control" id="oid_address" name="oid_address"></select>
						  </div>
					  </div>
					  <div class="col-sm-6">
						  <div class="col-sm-12">
							  <div class="greenH">Продавец</div>
							  
							  <input type="hidden" value="" id="org_id" name="org_id">
  
						  </div>
						  <div class="col-sm-12">
							  <span>Счет организации:</span>
							  <select class="form-control" id="org_bank_id" name="org_bank_id"></select>
  
							  <input type="hidden" value="" id="org_bank_data" name="org_bank_data">
  
						  </div>
						  <div class="col-sm-12">
							  <span>Грузоотправитель:</span>
							  <select class="form-control" id="" name="" disabled></select>
						  </div>
						  <div class="col-sm-12">
							  <span>Счет грузоотправителя:</span>
							  <select class="form-control" id="" name="" disabled></select>
						  </div>
					  </div>
				  </div>
				  <div class="row">
					  <div class="col-sm-6 ">
						  <div class="col-sm-12">
							  <div class="greenH">Доверенность</div>
						  </div>
						  <div class="col-sm-12">
							  <span>Номер:</span>
							  <div class="">
								  <div class="col-sm-5" style="padding: 0;">
									  <input type="text" class="form-control" id="" name="" disabled>
								  </div>
								  <span class="col-sm-2 text-center" style="display: flex; align-items: center; justify-content: center; height: 45px;">от</span>
								  <div class="col-sm-5" style="padding: 0;">
									  <input type="date" class="form-control" id="" name="" disabled>
								  </div>
							  </div>
						  </div>
						  <div class="col-sm-12">
							  <span>Кем выдана:</span>
							  <input type="text" class="form-control" id="" name="" disabled>
						  </div>
						  <div class="col-sm-12">
							  <span>Кому выдана:</span>
							  <input type="text" class="form-control" id="" name="" disabled>
						  </div>
					  </div>
					  <div class="col-sm-6">
						  <div class="col-sm-12">
							  <div class="greenH">Подписи</div>
						  </div>
						  <div class="col-sm-12">
							  <span>Руководитель:</span>
							  <select class="form-control" id="sign_leader_id" name="sign_leader_id"></select>
							  <input type="hidden" value="" id="sign_leader_data" name="sign_leader_data">
						  </div>
						  <div class="col-sm-12">
							  <span>Главный бухгалтер:</span>
							  <select class="form-control" id="sign_buch_id" name="sign_buch_id"></select>
							  <input type="hidden" value="" id="sign_buch_data" name="sign_buch_data">
						  </div>
						  <div class="col-sm-12">
							  <span>Груз отпустил:</span>';
								  oSelect("jsAdminTpl", "sign_ship_id", null, "getUsersToSelect");
								  oSelect("select", "sign_ship_id", null, "getUsersToSelect");
						  echo '<input type="hidden" value="" id="sign_ship_data" name="sign_ship_data">
						  </div>
						  <div class="col-sm-12">
							  <span>Отпустил (должность):</span>
							  <input type="text" class="form-control" id="sign_ship_post" name="sign_ship_post" placeholder="Введите должность">
						  </div>
					  </div>
				  </div>
			  </div>
			</div>
			</form>
			<div class="modal-footer">
			  <button type="button" class="btn btn-primary" data-dismiss="modal" id="torg12ModalSaveBtn">Сохранить</button>
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		  </div>
		</div>
	  </div>';
	// }
    
	echo '<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список товаров:</span>
					<div class="widget-menu pull-right" >
                    
						<div class="btn-group">';
							$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
							/*
							echo '<span style="display: '.($order['active'] == 0 ? '' : 'none').';" class="elemHide">';
									echo callAddProduct($id, "https://atri-energo.ru/administration/module/realization/addProduct/");
							echo '</span>';
							*/
							echo '<span><button onclick="ajaxGetJS(\'{MOD_LINK}/ajax/order_tovar_list/'.$id.'\', \'demoHighCharts.init(); '.$notif.'\', \'tov_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button></span>';
							echo '<span style="display: '.($order['active'] == 0 ? '' : 'none').';" class="elemHide">';
								echo '<button id="saveReceipt" class="btn btn-xs btn-success">Сохранить</button>';
								echo '</span>';
								
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
			  <script type="text/javascript">ajaxGet(\'{MOD_LINK}/ajax/order_tovar_list/'.$id.'\', \'tov_list\');</script>
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped" id="tablesForm">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w200">Номенклатура</th>
							<th class="w150">Артикул</th>
							<th class="w50">Ед. изм.</th>
							<th class="w100 text-center" data-toggle="tooltip" title="Количество">Кол-во</th>
							<th class="w100 text-center" data-toggle="tooltip">Осталось реализовать</th>
							<th class="w100 text-center" data-toggle="tooltip" title="Наличие / Доступно / Резерв">Н/Д/Р</th>		
							<th class="w200 cp realization_supply_price" data-toggle="tooltip" title="Закупочная цена" id="purchasePrice" onclick="change_purchase_price();" style="'.hide_supply_price().'">
								<span id="purchase_price_title" data-step="1">Цена закупки</span>
								<span class="text-primary fas fa-sync-alt ml10"></span>
							</th>
							<th class="w150">Цена</th>
							<th class="w100">Скидка</th>
							<th class="w200">Сумма</th>
							<th class="w200">Ставка НДС</th>
							<th class="w200">НДС</th>					
							<th class="w200">' . $lang['actions'] . '</th>
							<th class="w20 text-right">
								<div class="checkbox-custom mb15">
									<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;" class="checks">
									<label for="all"></label>
								</div>	
							</th>
						</tr>
                    </thead>
                    <tbody id="tov_list">					
					</tbody>
				<tfoot class="footer-menu">
                    <tr align="right">                    
					  <td align="right" colspan="14">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act">														
							</select>
							<input name="submit" type="submit" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" /><span class="pd-l-sm"></span>							
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
	
	<div class="modal fade" id="syncSalesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Синхронизация реализации</h5>
			</div>
			<div class="modal-body">
				Выберите тип синхронизации
				<ul style="list-style-type: none; padding: 10px;">
					<li style="margin-bottom: 10px;"><a href="javascript:void(0);" class="syncSalesModalBtn" data-type="1">По текущим позициям</a></li>
					<li><a href="javascript:void(0);" class="syncSalesModalBtn" data-type="2">По всей продаже</a></li>
				</ul>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="modal-pay2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Оплата через кассу</h5>
				</div>
				<div class="modal-body">
					<button onclick="kassa.openShift()">Открыть смену</button>
					<button onclick="kassa.closeShift()">Закрыть смену</button>
					<button onclick="kassa.XReport()">X отчет</button>
					<button onclick="kassa.RegisterCheck(\'0\')">Чек </button>
					<button onclick="kassa.RegisterCheck(\'1\')">Отмена </button>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="resaleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	  	<form id="resaleFormModal">
	    	<div class="modal-content">
	    	  <div class="modal-header">
	    	    <h5 class="modal-title" id="exampleModalLabel">Перепродажа</h5>
	    	  </div>
	    	  <div class="modal-body">
			  	<div>
				  <label>Выберите организацию перепродажи</label>
				  <select name="resale_org" class="form-control" id="resale_org" required>
				  	<option value="" selected disabled>Выберите организацию</option>';
				  	foreach(\Organizations\Model\Org::with("organization")->get() as $item){
						if($item->id != $order["org"]){
							echo "<option value='{$item->id}'>{$item->organization->name}</option>";
						}
					}
				  echo '</select>
				</div>
			  	<div>
				  <label>Выберите склад перепродажи</label>
				  <select name="resale_stock_id" class="form-control" id="resale_stock_id" required>
					<option value="" selected disabled>Выберите склад</option>
				  </select>
				</div>
	    	  </div>
	    	  <div class="modal-footer">
			  	<button type="submit" class="btn btn-primary">Проверить данные</button>
	    	    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	    	  </div>
	    	</div>
		</form>
	  </div>
	</div>
	';

	
	$Cashbox = \Cashbox\Models\CashierShifts::with(["cashbox" => function($query) use ($order){
		$query->where("org", $order["org"]);
	}, "cashier"])->where("status", 1)->get();
	$CashboxList = [];
	foreach($Cashbox as $c){
		if(!empty($c->cashier) && !empty($c->cashbox)){
			$CashboxList[] = "<option value='{$c->id}' 
			data-cashier_name='{$c->cashier->name}'
			data-cashier_vatin='{$c->cashier->inn}'
			data-num_device='{$c->cashbox->NumDevice}'
			>{$c->cashbox->title} ({$c->cashbox->address}; кассир: {$c->cashier->name})</option>";
		}
	}
	$CashboxList = implode("", $CashboxList);

	echo '<div class="modal fade" id="modal-pay" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="modal-pay-form">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Оплата через кассу</h5>
					</div>
					<div class="modal-body">
						<h4>Выберите кассу</h4>
						<i class="text-danger"><b>ВНИМАНИЕ!</b> Проверьте правильная ли выбрана <b>касса</b> и нужный на ней <b>кассир</b></i>
						<select class="form-control" name="cashier_shifts_id" id="modal-pay-select" required>'.$CashboxList.'</select>
						<hr>
						<h4>Укажите способ оплаты</h4>
						<div style="display: grid; grid-row-gap: 20px">
							<div class="d-flex justify-content-between align-items-center">
								<div>Сумма наличными</div>
								<div id="modal-pay-cash"></div>
							</div>
							<div class="d-flex justify-content-between align-items-center">
								<div>По карте</div>
								<div><input type="number" step="any" value="0" required min="0" name="card" id="modal-pay-card" class="form-control"></div>
							</div>
							<div class="d-flex justify-content-between align-items-center">
								<div>Итого</div>
								<div id="modal-pay-total"></div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-primary">Провести оплату</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
					</div>
				</form>
			</div>
		</div>
	</div>';

	$CashboxChecks = \Cashbox\Models\CashboxChecks::with(["cashier_shifts.cashier", "cashier_shifts.cashbox"])->
	where("realization_id", $id)->
	where("TypeCheck", 0)->
	orderByDesc("id")->
	first();

	if(!empty($CashboxChecks)){
		echo '<div class="modal fade" id="modal-pay-cancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<form id="modal-pay-cancel-form">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Отмена оплаты через кассу</h5>
						</div>
						<div class="modal-body">
							<input type="hidden" id="modal-pay-cancel-CashierName" value="'.$CashboxChecks->cashier_shifts->cashier->name.'">
							<input type="hidden" id="modal-pay-cancel-CashierVATIN" value="'.$CashboxChecks->cashier_shifts->cashier->inn.'">
							<input type="hidden" id="modal-pay-cancel-NumDevice" value="'.$CashboxChecks->cashier_shifts->cashbox->NumDevice.'">
							<input type="hidden" id="modal-pay-cancel-cashier_shifts_id" value="'.$CashboxChecks->cashier_shifts_id.'">
							<div style="display: grid; grid-row-gap: 20px">
								<div class="d-flex justify-content-between align-items-center">
									<div>Сумма наличными</div>
									<div id="modal-pay-cancel-cash" data-value="'.$CashboxChecks->data->Cash.'">'.number_format($CashboxChecks->data->Cash,2,'.',' ').' руб.</div>
								</div>
								<div class="d-flex justify-content-between align-items-center">
									<div>По карте</div>
									<div id="modal-pay-cancel-card" data-value="'.$CashboxChecks->data->ElectronicPayment.'">'.number_format($CashboxChecks->data->ElectronicPayment,2,'.',' ').' руб.</div>
								</div>
								<div class="d-flex justify-content-between align-items-center">
									<div>Итого по чеку</div>
									<div id="modal-pay-cancel-total">'.(number_format($CashboxChecks->data->Cash + $CashboxChecks->data->ElectronicPayment,2,'.',' ')).' руб.</div>
								</div>
								<div class="d-flex justify-content-between align-items-center">
									<div>Итого по реализации</div>
									<div id="modal-pay-cancel-total-realization">'.number_format($order["total"],2,'.',' ').' руб.</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary">Отменить оплату</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
						</div>
					</form>
				</div>
			</div>
		</div>';
	}

	$modal = logisticsDeliveryModal($id);
	echo $modal["modal"];
	$adminTpl->footIncludes[] = $modal["script"];

	realization_end_init_js();
	realization_end_init_modal();

	$adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/price.js?051120191156"></script>';
	$adminTpl->footIncludes[] = '<script src="/usr/other/sub/request_god/js/request_god.js"></script>';
	$adminTpl->footIncludes[] = '<script src="/usr/modules/realization/admin/asset/js/realizationOrder.js?'.time().'"></script>';
	$adminTpl->footIncludes[] = '<script src="/usr/modules/realization/admin/asset/js/realization_cashbox.js?'.time().'" type="module"></script>';

	require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
	ndr_init();

	$adminTpl->admin_foot();
} 

function logisticsDeliveryModal($id){
	return \Logistics\Support\LogisticsDeliverySupport::add_modal(["pid" => $id, "module" => "realization"]);
}


function nz_create($id = 0)
{	
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $dn_array;	
	if (!empty($id))
	{
		$query_static = $db->query("SELECT d.*, o.name as oname, o.yaddress, o.phone, o.email  FROM ".DB_PREFIX."_sales as d LEFT JOIN ".DB_PREFIX."_organizations AS o on(d.oid=o.id)  WHERE d.id = '" . $id . "'");
		$order = $db->getRow($query_static);
		
		$date = $order['date'];
		$query_rel = $db->query("SELECT pid_in FROM ".DB_PREFIX."_relations WHERE module_in = 'reestr' AND module_out = 'verification' AND pid_out = '".$id."' LIMIT 1");
		if($db->numRows($query_rel) > 0) 
		{
			$rel = $db->getRow($query_rel);
			$query_doc = $db->query("SELECT * FROM ".DB_PREFIX."_reestr WHERE id = '".$rel['pid_in']."'");
			$doc = $db->getRow($query_doc);
			$dogovor = '№ ПТТ-'.$doc['numb'].' от '.date('d.m.Y', $doc['date']);
			$d_flag=true;
		}
		else
		{
			$dogovor = 'Договор еще не создан';
			$d_flag=false;
		}
		$total = 0;	
		$order_data = array(
				'n' => array(),
				'stable_name' => array(),
				's' => array(),
				'model' => array(),
				'dn' => array(),
				'number' => array(),
				'price' => array()
			);	
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_ver_detail WHERE vid = '".$id."' AND status != '0'");		
		if($db->numRows($query) > 0) 
		{
			$i = 0;
			while($catalog = $db->getRow($query)) 
			{	
				$i++;
				$stable_name = $catalog['stable_name'];
				$sum = $catalog['sum'];
				$model = $catalog['model'];
				$dn = $dn_array[$catalog['dn']];
				$number = $catalog['number'];
				$price = $catalog['stable_price']*$sum;
				$total = $total+$price;				
				array_push($order_data['n'], $i);
				array_push($order_data['stable_name'], $stable_name);
				array_push($order_data['s'], $sum);
				array_push($order_data['model'], $model);
				array_push($order_data['dn'], $dn);
				array_push($order_data['number'], $number);
				array_push($order_data['price'], $price);	
			}
		}
		
		require_once(ROOT.'usr/plugins/PHPWord.php');
		$PHPWord = new PHPWord();
		$document = $PHPWord->loadTemplate(ROOT.'/files/tpl/ver_nz.docx');
		$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];			
			
		$document->setValue('{number}','№ПР-'.$id);
			
		$document->setValue('{d}', date('d', $date));
		$document->setValue('{m}',  date('m', $date));
		$document->setValue('{year}', date('Y', $date));
			
		$document->setValue('{org_name}', $order['oname']);
		$document->setValue('{org_address}', $order['yaddress']);
		$document->setValue('{org_phone}', $order['phone']);
		$document->setValue('{org_email}', $order['email']);
		
		$document->setValue('{total}', $total);			
		$document->setValue('{dogovor}', $dogovor);
				
		$document->cloneRow('o', $order_data);
		
		$document->save('files/reestr/'.$rel['pid_in'].'/8-order-'.$id.'-'.$doc['date'].'.docx');
		$db->query("UPDATE `" . DB_PREFIX . "_ver` SET `status` = '2' WHERE `id` = " . $id . " LIMIT 1 ;");		
		header('Location: https://atri-energo.ru/files/reestr/'.$rel['pid_in'].'/8-order-'.$id.'-'.$doc['date'].'.docx');

		
	}
	else
	{
		header('Location: /');
	}
}


function doc_create()
{	
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang, $adminTpl;	
	$id='57';
	$id = intval($_POST['id']);
	if (!empty($id))
	{			
		$uid = $core->auth->user_info['id'];		
	
			$query_static = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '" . $id . "'");
			$order = $db->getRow($query_static);
			
			$date = '1526736137';
			$numb = '152';
			$date = strtotime($_POST['date_doc']);
			$numb = intval($_POST['numb']);
			$pr = isset($_POST['pr']) ? intval($_POST['pr']) : 0;
			parseCertById($order['oid']);
		
			$query_bank = $db->query("SELECT * FROM ".DB_PREFIX."_organizations_bank WHERE oid = '".$order['oid']."' LIMIT 1");
            
            $query_org = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$order['oid']."' LIMIT 1");		
			$org = $db->getRow($query_org);
			if($db->numRows($query_bank) > 0 && isset($org['inn'], $org['dey'])) 
			{					
				
					require_once(ROOT.'usr/plugins/PHPWord.php');
					$PHPWord = new PHPWord();
                    if($_POST['typeEnergo'] == 0){
                        $document = $PHPWord->loadTemplate(ROOT.'/files/tpl/sale_doc_1.docx');
                    } else {
                        $document = $PHPWord->loadTemplate(ROOT.'/files/tpl/sale_doc_2.docx');
                    }
					$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
					
										
					
					$bank = $db->getRow($query_bank);				
					switch($org['post']) {
						default:
							$post=$org['post'];
						break;				
						case "Директор":
							$post='директора';
						break;
						case "Генеральный директор":
							$post='генерального директора';
						break;
					}
					$dey = $org['dey'];

					$result_d = getDirectorDetail($org, $order);
					
					$order_data = array(
							'n' => array(),
							'sku' => array(),
							'name' => array(),
							's' => array(),
							'u' => array(),
							'price' => array(),
							'price_nds' => array()
						);	
					$query = $db->query("SELECT * FROM ".DB_PREFIX."_sales_detail WHERE sales_id = '".$id."' AND status != '0'");		
					if($db->numRows($query) > 0) 
					{
						$i = 0;
						while($catalog = $db->getRow($query)) 
						{	
							$i++;
							$stable_name = $catalog['s_name'];
							$stable_sku = $catalog['s_sku'];
							$stable_unit = $catalog['s_unit'];
							$sum = $catalog['count'];
							$price = round(($catalog['total']/ $catalog['count']), 2);
							$total_nds = $catalog['total'];			
							array_push($order_data['n'], $i);
							array_push($order_data['sku'], $stable_sku);
							array_push($order_data['name'], $stable_name);
							array_push($order_data['s'], $sum);
							array_push($order_data['u'], $stable_unit);
							array_push($order_data['price'], number_format($price,2,'.',' '));
							array_push($order_data['price_nds'], number_format($total_nds,2,'.',' '));
						}
					}
				
					$result_s = mb_substr($result_d->name, 0, 1) . '. ';
					$result_s .= mb_substr($result_d->patronymic, 0, 1) . '. ';
					$result_s .=  $result_d->surname;
						
					if ($pr <= 0)
					{
						$pay = 'Покупатель вносит сумму в размере 100% в течении 30 рабочих дней со дня отгрузки.';
					}
					elseif(($pr > 0)&&($pr < 100))
					{
						$pr_o = 100 - $pr;
						$pay = 'Покупатель вносит предоплату в размере '.$pr.'% в течение 3 (Трех) рабочих дней с момента подписания настоящей спецификации, оставшуюся сумму в размере '.$pr_o.'% в течении 30 рабочих дней со дня отгрузки.';
					}
					else
					{
						$pay = 'Покупатель вносит предоплату в размере 100% в течение 3 (Трёх) рабочих дней с момента подписания настоящей спецификации.';
					}				
					$document->setValue('{doc_numb}', $numb);
					$document->setValue('{post}', $post);
                    if($org['type'] == "INDIVIDUAL"){
                        $document->setValue('{post_i}', "Индивидуальный предприниматель");
                    } else {
                        $document->setValue('{post_i}', $org['post']);
                    }
					$document->setValue('{director}', $result_d->result_genitive);
					$document->setValue('{director_short}', $result_s);
					$document->setValue('{dey}', $dey);
					$document->setValue('{pay}', $pay);
					
					$document->setValue('{day}', date('d', $date));
					$document->setValue('{month}', $Month_rus[ date( "n", $date) - 1 ]);
					$document->setValue('{year}', date('Y', $date));
					
					$document->setValue('{org_name}', $org['name']);
					$document->setValue('{org_inn}', $org['inn']);
					$document->setValue('{org_address}', $org['yaddress']);
                    if($org['type'] == "INDIVIDUAL"){
                        $document->setValue('{org_kpp}', "");
                    } else {
                        $document->setValue('{org_kpp}', "КПП: ".$org['kpp']);
                    }

					$document->setValue('{org_bank}', $bank['name_short']);
					$document->setValue('{org_bik}', $bank['bik']);
					$document->setValue('{org_rs}', $bank['rschet']);
					$document->setValue('{org_ks}', $bank['kschet']);
					$document->cloneRow('s', $order_data);
					$document->setValue('{total}', number_format($order['total'],2,'.',' '));
					$document->setValue('{nds}',  number_format($order['nds'],2,'.',' '));
					$document->setValue('{total_nds}', number_format($order['total'],2,'.',' '));
					$document->setValue('{total_rus}', num2str($order['total']));
					
					$dir = ROOT.'files/sales/'.$id;
					if (!file_exists($dir))
					{
						mkdir($dir, 0777);						
					}
					$file_url= 'files/sales/'.$id.'/8-'.$numb.'-'.time().'.docx';
					$document->save($file_url);						
					header('Location: https://atri-energo.ru/'.$file_url);							
				
			}
			else
			{
				$adminTpl->admin_head('Ошибка создания договора!');
				echo '<div id="content" class="animated fadeIn">';	
				$adminTpl->info('Для составления договора у контрагента заполнены не все поля!', 'error', null, $lang['error'], 'Редактировать контрагента', ADMIN.'/module/organizations/edit/'.$order['oid']);
				echo '</div>';	
				$adminTpl->admin_foot();
			}			
		/*
			$rel = $db->getRow($query);
			$query_doc = $db->query("SELECT * FROM ".DB_PREFIX."_reestr WHERE id = '".$rel['pid_in']."'");
			$doc = $db->getRow($query_doc);
			header('Location: https://atri-energo.ru/files/reestr/'.$rel['pid_in'].'/8-'.$doc['numb'].'-'.$doc['date'].'.docx');*/
		
	}		
}

function hide_supply_price_status(){
	return isset($_COOKIE["realization_supply_price"]);
}

function hide_supply_price(){
	return hide_supply_price_status() ? "display: none;" : "";
}

function ajax_order_tovar_list_query($realization){
	global $db;
	return $db->query("SELECT s.*, d.title, sd.count_realization, sd.count as sd_count, ct.ofd as ct_ofd
	FROM `".DB_PREFIX."_realization_detail` as s 
	LEFT JOIN `".DB_PREFIX."_catalog` AS d on(d.id=s.cid) 
	LEFT JOIN `".DB_PREFIX."_catalog_types` AS ct on(d.types=ct.id) 
	LEFT JOIN `".DB_PREFIX."_sales_detail` as sd ON(sd.id = s.sdid) 
	WHERE s.rid = '".$realization."' AND s.status != '0'
	ORDER BY s.position, s.id");
}

function ajax_order_tovar_list($realization)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];	
	$order = query_order_result($realization);
	$query = ajax_order_tovar_list_query($realization);
	//$query_ver = $db->query("SELECT status FROM ".DB_PREFIX."_ver WHERE id = '". $vid."' LIMIT 1");
	//$ver = $db->getRow($query_ver); 
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
	$total_nds = 0;
	$total_z = 0;
	while($content = $db->getRow($query)) 
		{
			$i++;
			$flag = true;
			/*
			if ($content['status'] == 1)
			{				
				$status_icon = '<span class="fa fa-clock-o text-warning fa-md" data-toggle="tooltip" title="Прибор проверяется"></span>';
				$flag = false;
			}
			elseif ($content['status'] == 2)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}			
			elseif ($content['status'] == 3)
			{
				$status_icon = '<span class="fa fa-circle-o text-danger fa-md"></span>';
			}
			*/
			
			switch($content['status']){
					case 1:
					$status_icon = 'Товар не заказан';
					$flag = false;
					break;
					case 2:
					$status_icon = 'Товар заказан';
					break;
					case 3:
					$status_icon = '<span class="fa fa-circle-o text-danger fa-md"></span>';
					break;
			}
		
			$total = $total + ($content['total']);
		
			$total_nds = $total_nds + ($content['nds']);
                
			if($order["active"] == 0){
				$gppds = \Sales\Model\SalesDetail::getPurchase($content['sdid']);
				$price = $gppds->price;
			} else {
				$price = $content["supply_order_price"];
			}
			
			$total_z += $price * $content['count'];
				
    $queryTC = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE pid = '".$content['id']."' AND `module` = 'sales_comment'"); 
    $str = "";
        if($db->numRows($queryTC) > 0){
			while($myr = $db->getRow($queryTC)){
				$str .= '<a target="_blank" href="/download/'.$myr['id'].'">'.$myr['name'].'</a><br>';
			}
		}

		$ndr_view = get_ndr(["cid" => $content["cid"], "stock_id" => $order["stock_id"], "module" => "sales", "pid" => null, "returnType" => "view"]);

		$unit = get_unit('data', $content['s_unit'] ,'sname');

			echo '
			<tr data-id="'.$content['id'].'" class="tov_item" id="tr_'.$content['id'].'">
				<td data-toggle="tooltip" title="' . $content['id'] . '" class="position" data-id="'.$content['id'].'">' . $i . '</td>
				<td class="s_name cp" data-id="'.$content['id'].'">' . html_entity_decode($content['s_name'], ENT_QUOTES) . '</td>
				<td>' . $content['s_sku'] . '</td>
				<td>' . $unit . '</td>
				<td class="text-center count cp" data-func="getTotal,saveDataToInp,setAllTotal,setAllNds">' . $content['count'] . '</td>	
				<td class="text-center">'.($content['sd_count'] - $content['count_realization']).'</td>			
				<td class="text-center ndr" onclick="view_ndr_modal('.$content["cid"].');">'.$ndr_view.'</td>					
				<td class="purchasePrice realization_supply_price" data-price="'.$price.'" style="'.hide_supply_price().'">' . number_format($price,2,',',' '). ' руб.</td>
				<td class="edit cp">' . number_format($content['s_price'],2,',',' '). ' руб.</td>		
				<td class="discount cp" title="'.$content['discount'].'">' . (empty($content['discount']) ? '0' : round($content['discount'], 2)). '%</td>				
				<td class="total cp">' . number_format($content['total'],2,',',' '). ' руб.</td>	
				<td class="typeNds cp">' . \Price::getNds($content['nds_type']). '%</td>	
				<td class="nds cp">' . number_format($content['nds'],2,',',' '). ' руб.</td>							
				<td class="w200">				
					<div class="btn-group" >
						<button type="button" '.($content['status'] != 1 ? 'disabled' : '').' onclick="delModal('.$content['id'].')" class="btn btn-xs btn-danger fas fa-times" title="Удалить"></button>
					</div>
					

					  <div class="modal fade" id="modal-form-'.$content['id'].'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLabel">Удалить</h5>
							</div>
							<div class="modal-body">
								Вы действительно хотите удалить '.$content['s_name'].' из списка?
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger delete" data-id="'.$content['id'].'" data-dismiss="modal">' . $lang['delete'] .'</button>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
							</div>
							</div>
						</div>
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
		echo '<tr>
		<td colspan="7" class="text-right"><b class="allTotalSupplyText realization_supply_price" style="'.hide_supply_price().'">Итого закупка:</b></td>
		<td colspan="1" class="text-left">
			<b class="allTotalSupply realization_supply_price" style="'.hide_supply_price().'">'.number_format($total_z,2,',',' ').' руб.</b>
		</td>
		
		<td colspan="1" class="text-right"><b>Итого:</b><br><b>Итого НДС:</b></td>
		<td colspan="8" class="text-left">
			<b class="allTotal">'.number_format($total,2,',',' ').' руб.</b><br>
			<b class="allNds">'.number_format($total_nds,2,',',' ').' руб.</b>
		</td>
		
		</tr>';		
		
	} 	
	else 
	{
		echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';	
	}	
}

function ajax_order_tovar_add()
{
	/*
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['cid'])))
	{
		$id = intval($_POST['id']);
		$mcid = $_POST['cid'];
        
        $str = '';
        
        foreach($mcid as $cid){
            $query_stable = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '". $cid ."' LIMIT 1");
            $tovar_stable = $db->getRow($query_stable);	
            $sum = (isset($_POST['count_'.$cid]) ? $_POST['count_'.$cid] : 1);
            $stable_sku = $tovar_stable['sku'];
            $stable_name = $tovar_stable['title'];
            $stable_barcode = $tovar_stable['barcode'];
            $stable_price_all = $tovar_stable['price'];
            $price_rub = 0;
            $price = json_decode($stable_price_all, true);
            if ($price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'] != 0)
            {
                $price_is = number_format($price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'], 0, '.', ' ').' РУБ.';
                $price_rub = $price["1bb285e9-a8f3-11e7-a442-704d7b330583"]['value'];
            }
            else if ($price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'] != 0)
            {
                $content_cbrf = get_cbrf(); 
                $pattern_cbrf = "#<Valute ID=\"([^\"]+)[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>[^>]+>([^<]+)[^>]+>[^>]+>([^<]+)#i"; 
                preg_match_all($pattern_cbrf, $content_cbrf, $out, PREG_SET_ORDER); 
                $dollar = ""; 
                $euro = ""; 
                foreach($out as $cur) 
                { 
                    if($cur[2] == 840) $dollar = str_replace(",",".",$cur[4]); 
                    if($cur[2] == 978) $euro   = str_replace(",",".",$cur[4]); 
                } 	  
			
                $price_is =  number_format($euro*$price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'],2,'.',' ').' РУБ.';;
                $price_rub = number_format($euro*$price["ee22d123-37ac-11e8-a463-704d7b330583"]['value'],2,'.','');
            }
            else
            {
                $price_is = 'по запросу';
                $price_rub = 0;
            }
            
            $str .= "(NULL, '" . $db->safesql($id) . "', '" . $db->safesql($cid) . "', '" . $db->safesql($stable_sku) . "', '" . $db->safesql($stable_name) . "', '1', '" . $db->safesql($price_rub) . "', '" . $db->safesql($stable_price_all) . "', '" . $db->safesql($sum) . "', '0', '" . $db->safesql($sum*$price_rub) . "', '1'), ";
        }

		if($db->query("INSERT INTO `" . DB_PREFIX . "_realization_detail` (`id`, `rid`, `cid`, `s_sku`, `s_name`, `price_type`, `s_price`, `s_price_all`, `count`, `discount`, `total`, `status`) VALUES ".mb_substr($str, 0, -2)."")) 
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
	*/
}

function ajax_devicedd_edit()
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

function query_order_result($id){
	global $db;
	$query = $db->query("SELECT s.*, o.name as oname, obj.name as objname, obj.address as objaddress, stock.name as stockname, sa.numb as sa_numb, o2.name as org_name, org.oid as org_oid, sa.sales_type as sa_sales_type
	FROM `".DB_PREFIX."_realization` as s 
	LEFT JOIN `".DB_PREFIX."_organizations` AS o on(s.oid=o.id) 
	LEFT JOIN `".DB_PREFIX."_org` AS org on(s.org=org.id) 
	LEFT JOIN `".DB_PREFIX."_organizations` AS o2 on(org.oid=o2.id) 
	LEFT JOIN `".DB_PREFIX."_object` as obj on (s.object = obj.id) 
	LEFT JOIN `".DB_PREFIX."_stock` as stock ON(stock.id = s.stock_id) 
	LEFT JOIN `".DB_PREFIX."_sales` as sa ON(sa.id = s.sales_id) 
	WHERE s.id = '" . $id . "'");
	$order = $db->getRow($query);
	return $order;
}

function ajax_order_data($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($id))
	{

		$uid = $core->auth->user_info['id'];
		
		$order = query_order_result($id);
		
		$countComm = $db->numRows($db->query("SELECT c.*, u.surname as surname, u.name as name, u.ochestvo as ochestvo FROM `" . DB_PREFIX . "_comments_admin` as c LEFT JOIN `" . DB_PREFIX . "_users` as u ON(c.uid = u.id) WHERE c.module = 'realization' AND c.module_id = '".$id."'"));

		if(empty($order["object"])){
			$obj = 'Объект еще не привязан';
		} else {
			$obj = '<b>'.$order['objname'].' ('.$order['objaddress'].')</b>&nbsp;&nbsp;<a target="_blank" href="'.ADMIN.'/object/view/'.$order['object'].'"><span class="fa fa-external-link"></span></a>';
		}

		echo '<div class="col-md-4">
		<div class="element-groups-lg-custom wizard-options">';
		if($order['active'] == 0){
			$modal_name = $order["sa_sales_type"] == 3 ? "modalPay();" : "conduct_modal();";
			echo '<div class="ph10" id="conductBlock">
				<a href="javascript:void(0);" onclick="'.disabled_all().' '.$modal_name.'" class="holder-style p15 text-center holder-active"><span class="fa fa-edit holder-icon"></span><br>Провести<br>&nbsp;</a>
			</div>';
		}
		if($order['active'] == 1){
			$modal_name = $order["sa_sales_type"] == 3 ? "modalPayCancel();" : "end_modal();";
			echo '<div class="ph10" id="distributeBlock">
				<a href="javascript:void(0);" style="outline-color: red;" onclick="'.$modal_name.'" class="holder-style p15 text-center holder-active"><span style="color:red;" class="fa fa-edit holder-icon"></span><br>Отменить<br>проведение</a>
			</div>';
		}	
			echo '<div class="ph10">
				<a href="javascript:void(0);" onclick="printModal();" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print holder-icon"></span><br>Вывести на<br>печать</a>
			</div>

			<div class="ph10">
				<a href="javascript:void(0);" id="syncSales" data-status="'.$order['active'].'" class="holder-style p15 text-center '.($order['active'] === "0" ? 'holder-active' : '').'"><span class="fas fa-sync-alt holder-icon"></span><br>Sync<br><br></a>
			</div>

			<div class="ph10">
				<a href="javascript:void(0);" onclick="'.($order['active'] === "0" ? 'correctRealizationModalError();' : 'correctRealizationModal();').'" class="holder-style p15 text-center '.($order['active'] === "0" ? '' : 'holder-active').'"><span class="fas fa-pencil-ruler holder-icon"></span><br>Корр.<br>реализации<br></a>
			</div>';
			$modal = logisticsDeliveryModal($id);
			echo $modal["btn_container"];

			$logistics_delivery = \Logistics\Model\LogisticsDelivery::where("module", "realization")->where("pid", $id)->first();
			if($logistics_delivery){
				echo '<div class="ph10">
					<a href="/administration/module/logistics/delivery_archive/view/'.$logistics_delivery->id.'" target="_blank" class="holder-style p15 text-center holder-active"><span class="fas fa-link holder-icon"></span><br>Перейти в<br>доставку<br></a>
				</div>';
			}

			echo '<div class="ph10">
				<a href="javascript:void(0);" class="holder-style p15 text-center" id="resale">
				<span class="fas fa-triangle holder-icon"></span><br>Перепродажа<br><br></a>
			</div>';

			echo '</div>										
		</div>
		<script type="text/javascript">//ajaxGet(\'{MOD_LINK}/ajax/order_data/'.$id.'\', \'orderData\');</script>
		<div class="col-md-8">';
			
		echo '<div class="col-md-4">
				<small>Контрагент:</small><br/>';
				$oid_change = '<a href="javascript:void(0);" onclick="modal_o(\'#modal-oid-edit\');  $(\'.select2-single\').select2();">(изменить)</a>';
				if($order['active'] == 1){
					$oid_change = "";
				} 
				
					if (!empty($order['oname']))
					{
						echo '
							<b>'.$order['oname'].'</b> '.$oid_change.'&nbsp;&nbsp;				
							<a target="_blank" href="'.ADMIN.'/module/organizations/edit/'.$order['oid'].'"><span class="fa fa-external-link"></span></a> 
						';
					}
					else
					{
						echo '<b>Не указан</b>'.$oid_change;
					}						
					echo '
						<div id="modal-oid-edit" class="popup-basic admin-form mfp-with-anim mfp-hide" >				
						<div class="panel">
							<div class="panel-heading"><span class="panel-title"><i class="fa fa-suitcase"></i>Изменить контрагента</span></div>
							<form action="javascript:void(null);" id="oid-edit" onsubmit="ajax_send_data(\'oid-edit\', \''.ADMIN.'/module/realization/ajax/order_oid_edit\');">
								<div class="panel-body p25">									
									<div class="section">';
										// echo '<label for="oid" class="field prepend-icon">
										// 	<select name="oid" class="select2-single form-control">
										// 	<option value="0" disabled selected>Выберите организацию</option>';
										// 		$query_users_list = $db->query("SELECT * FROM `".DB_PREFIX."_organizations` ORDER BY name ASC");
										// 		if($db->numRows($query_users_list) > 0) 
										// 		{
										// 			while($users_list = $db->getRow($query_users_list)) 
										// 			{
										// 				$selected = ($users_list['id'] == $order["oid"]) ? "selected" : "";
										// 				echo '<option value="'.$users_list['id'].'" '.$selected.'>'.$users_list['name'].'</option>';
										// 			}
										// 		}																
										// 		echo'	</select>
										// 		<label for="oid" class="field-icon"><i class="fa fa-suitcase"></i></label>
										// </label>';
										oSelect("select", "oid", $order["oid"], "getOrganizations");
									echo '</div>	
									<div class="section">
									<label class="option block mt15">
										<input type="checkbox" name="checked" value="checked"><span class="checkbox"></span>Коммерческое предложение?
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

					$groups_allow = getGroup(["group" => "admin"]);
					$groups_allow[] = $order['uid'];
					if(in_array($uid, $groups_allow) && $order["active"] == 0){
						echo '<a href="javascript:void(0);" onclick="modal_o(\'#modal-uid-edit\');  $(\'.select2-single\').select2();">(изменить)</a>';
					}

					echo '<div id="modal-uid-edit" class="popup-basic admin-form mfp-with-anim mfp-hide" >				
						<div class="panel">
							<div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Изменить ответственного</span></div>
							<form action="javascript:void(null);" id="uid-edit" onsubmit="ajax_send_data(\'uid-edit\', \''.ADMIN.'/module/realization/ajax/order_uid_edit\');">
								<div class="panel-body p25">									
									<div class="section">
										<label for="uid" class="field prepend-icon">
											<select name="uid" class="select2-single form-control">
											<option value="0" disabled selected>Выберите ответственного</option>';
												$query_users_list = $db->query("SELECT * FROM ".DB_PREFIX."_users WHERE `group` = '1' AND hidden=0 ORDER BY name ASC");
												if($db->numRows($query_users_list) > 0) 
												{
													while($users_list = $db->getRow($query_users_list)) 
													{
														$selected = ($users_list['id'] == $order["uid"]) ? "selected" : "";
														echo '<option value="'.$users_list['id'].'" '.$selected.'>'.$users_list['surname'].' '.$users_list['name'].' '.$users_list['ochestvo'].'</option>';
													}
												}																
												echo'	</select>													
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
					<small>Создатель:</small><br/>
					<b>'. shortUserName($order['uid_create']).'</b><br/><br/>';

					$obj_edit = '<a href="javascript:void(0);" onclick="modal_o(\'#modal-object-edit\');  $(\'.select2-single\').select2();">(изменить)</a>';
					if($order["active"] == 1){
						$obj_edit = "";
					}
					
					echo '<small>Основной объект:</small><br/>
					'.$obj.$obj_edit.' 
					<div id="modal-object-edit" class="popup-basic admin-form mfp-with-anim mfp-hide">						
						<div class="panel">
								<div class="panel-heading"><span class="panel-title"><i class="fa fa-briefcase"></i>Прикрепить объект</span></div>
								<form id="object-edit" action="javascript:void(null);" onsubmit="ajax_send_data(\'object-edit\', \''.ADMIN.'/module/realization/ajax/order_object_edit\');">
									<div class="panel-body p25">
										<div class="section">
											Номер закупки: <br>
											<div class="smart-widget sm-right smr-50">
											<label class="field prepend-icon">
												<select class="select2-single form-control" name="object_id" id="object_id">
													<option value="0" disabled selected>Выберите объект</option>';
													$query_r = $db->query("SELECT id, name FROM ".DB_PREFIX."_object  ORDER BY id ASC");
													if($db->numRows($query_r) > 0) 
														{
															while($r_list = $db->getRow($query_r)) 
															{
																
																echo '<option value="'.$r_list['id'].'">№OB-'.$r_list['id'].' ('.$r_list['name'].')</option>';
															}
														}		
													echo '	</select>
													<label for="old_cat" class="field-icon"><i class="fa fa-briefcase"></i></label>		
												</label>	
												<button onclick="showhide(\'addition\')" type="button" class="button"><i class="fa fa-plus"></i></button>	
											</div>														
										</div>
										<div id="addition" style="display: none">										
										<div class="section" >
											Выберите контрагента: <br>											
											<label class="field prepend-icon">
												<select class="select2-single form-control" name="object_oid" id="object_oid">
													<option value="0" disabled selected>Выберите поставщика</option>';												
														$query_r = $db->query("SELECT id, inn, name FROM ".DB_PREFIX."_organizations");
														if($db->numRows($query_r) > 0) 
															{
																while($r_list = $db->getRow($query_r)) 
																{
																	$selected = ($r_list['id'] == $order['oid']) ? "selected" : "";
																	echo '<option value="'.$r_list['id'].'" '.$selected.'>'.$r_list['name'].' ('.$r_list['inn'].')</option>';
																}
															}		
														echo '	
												</select>
												<label for="object_oid" class="field-icon"><i class="fa fa-building"></i></label>	
											</label>
											</div>		
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
										<input type="submit" class="button btn-success" value="Прикрепить" />
									</div>
								</form>
							</div>					
					</div>
					<br/><br/>
					<small>Продажа:</small><br/>
					<b><a href="/administration/module/sales/order/'.$order["sales_id"].'">'.$order["sa_numb"].'</a></b><br/><br/>
					<small>Комментарий:</small><br/>
					<span id="comment" data-value="'.$order["comment"].'"></span><br/><br/>
					
					</div>
										<div class="col-md-4">
													<small>Дата отгузки:</small><br/>
													<b id="dateMain">'. date("d.m.Y", strtotime($order['date'])).'</b><br/><br/>
													<small>Сумма заказа:</small><br/>
													<b class="allTotal">'. number_format($order['total'],2,',',' ').' руб.</b><br/><br/>
													<small>Комментарий:</small><br/>
													<b><a href="javascript:void(0);" onclick="modal_o(\'#modal-comm\'); ajaxGet(\''.ADMIN.'/module/realization/ajax/comments_get/'.$order['id'].'\', \'sale_comments_list\');uploaderMaster();">Посмотреть комментарии</a></b> <span class="badge badge-danger">'.$countComm.'</span><br><br>';
                                                    
													echo '<i class="far fa-hand-peace text-alert fa-2x cp" title="Запрос реализации в минус" onclick="request_send();"></i>';

													echo '<label class="ml10"><input type="checkbox" onchange="hide_supply_price(this);" '.(hide_supply_price_status() ? "checked" : "").'>Скрыть цену закупки</label>';
													
                                                    echo '<div id="modal-comm" class="popup-basic bg-none mfp-with-anim mfp-hide">
                                                    <form id="sale_comments" action="javascript:void(null);" onsubmit="ajax_comments_send(\'sale_comments\', \''.ADMIN.'/module/realization/commSave/'.$order['id'].'\');">
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
                                                    </div>
                                                    
										</div>
                                        <div class="col-md-4">
                                        <small>ТОРГ-12:</small><br/>
										<b><a href="javascript:void(0);" id="torg12">ТЫК</a></b><br/><br/>
                                        <small>???:</small><br/>
										<b><a href="javascript:void(0);" onclick="torg.open();">???</a></b><br/><br/>
                                        <small>Организация:</small><br/>
										<b>'.$order['org_name'].'</b><br/><br/>
                                        <small>Склад:</small><br/>
										<b id="stockname" class="cpx" data-disabled="'.($order["active"] == 0 ? "false" : "true").'" data-value="'.$order["stock_id"].'">'.$order['stockname'].'</b><br/><br/>
										<small>Файл:</small><br/>
										<b id="addFile" class="cp">Добавить файл</b><br/>';
											$qAttach = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `module` = 'realization' AND `pid` = '".$id."'");
											if($db->numRows($qAttach) > 0){
												while($attach = $db->getRow($qAttach)){
													echo '<a href="/download/'.$attach["id"].'">'.$attach["name"].'</a><i class="fas fa-times cp text-danger ml20 delFile" data-id="'.$attach["id"].'"></i>';
												}
											} else {
												echo 'Файлы не приложены!';
											}
                                        echo '</div>';

		echo '</div>
	</div>';
	}
}

function ajax_order_device_compl()
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

function ajax_order_uid_edit()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$uid = $_POST['uid'];		
		$db->query("UPDATE `" . DB_PREFIX . "_realization` SET `uid` = '".$uid."' WHERE `id` = " . $id . " LIMIT 1 ;");
		echo 'Информация успешно сохранена!';			
	}
}

/*
function ajax_order_send()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
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
					$db->query("INSERT INTO `" . DB_PREFIX . "_supply_order` ( `id` , `oid` , `uid` , `status`) VALUES (NULL, '" . $new_order . "', '" . $uid . "', '1');");
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
*/



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
		if (($object_id == 0)&&(!empty($object_name)))
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_object` ( `id` , `id_client` , `uid` , `date`, `address`, `name`, `status`) VALUES (NULL, '" . $object_oid . ",', '" . $uid . "', '" . time() . "', '" . $object_address . "', '" . $object_name . "','1');");				
			$object_id  = $db->insert_id();			
		}
		relationsSave('sales', $id, 'object', $object_id);
		$db->query("UPDATE `" . DB_PREFIX . "_sales` SET `object` = '".$object_id."' WHERE `id` = " . $id . " LIMIT 1 ;");
		echo 'Информация успешно сохранена!';			
	}
}

function ajax_order_device_del()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$comment = $_POST['comment'];
		$result = isset($_POST['result']) ? $_POST['result'] : 0;
		$status = '0';
		$db->query("DELETE FROM `" . DB_PREFIX . "_sales_detail` WHERE `id` = '" . $id . "' LIMIT 1 ;");
		echo 'Прибор успешно удален!';		
		
	}
}

function order_uid_set($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($id))
	{
		$uid = $core->auth->user_info['id'];		
		$db->query("UPDATE `" . DB_PREFIX . "_sales` SET `uid` = '".$uid."' WHERE `id` = " . $id . " LIMIT 1 ;");
		header('Location: /'.ADMIN.'/module/sales/order/'.$id);	
	}
	else
	{
		header('Location: /'.ADMIN.'/module/sales');	
	}
}


