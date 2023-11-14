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


function transfer() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Перемещение товаров';
	$adminTpl->admin_head('Менеджер склада | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'storage';
	$atributs['module_open'] = 'transfer/detail';
	$atributs['button'] = 'Добавить перемещение';
	$atributs['button_url'] = '/administration/module/storage/transfer/add';	
	// $atributs['js'] = '$("#jqStorageTransfer").jqGrid("setGridParam", {gridComplete: function(){ }});';
	loadGrid('jqStorageTransfer', $lnn, $atributs, ROOT.'/usr/modules/storage/admin/grid/');
	$adminTpl->footIncludes[] = '<script src="usr/modules/storage/admin/asset/js/function.js?'.time().'"></script>';	
	$adminTpl->footIncludes[] = '<script src="usr/modules/storage/admin/asset/js/scriptTransferMain.js"></script>';	
	$adminTpl->footIncludes[] = '<script src="/usr/modules/storage/admin/asset/js/storageTransferGrid.js?5646654156564"></script>';
	$adminTpl->footIncludes[] = <<<HTML
		<script>
			$(function(){
				setTimeout(() => {
					$("#jqStorageTransfer").jqGrid('setGridParam',{postData:{status: '0'}}).trigger('reloadGrid');
				}, 300);
			});
		</script>
HTML;
	echo '
	<div class="modal fade" id="removeTransferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Удалить перемещение</h5>
			</div>
			<div class="modal-body">
				Вы действительно хотите удалить перемещение <span class="removeTransferModalNum"></span>?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="removeTransferModalBtn">Удалить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="removeTransferModalError" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Внимание</h5>
			</div>
			<div class="modal-body">
				Прежде чем удалить перемещение <span class="removeTransferModalNum"></span>, отмените заявку	
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>
	';
	merging_modal('mergingModalCreate();');
	$adminTpl->admin_foot();
} 

function transfer_add($edit_id = null)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	if (empty($edit_id))
	{
		$date = gmdate('d.m.Y H:i'); 
		$object = false; 		
		$lnn = 'Создание нового перемещения';
		$rtype = null;
	}
	else
	{
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_stock_transfer WHERE id = '" . $edit_id . "'");
		$transfer = $db->getRow($query);
		$id = $transfer['id']; 
		$lnn = 'Редактирование перемещения';
		$rtype = $transfer['object'];
	}	

	$fc = new FastCreate("object");
	$object = $fc->get_fast_create();
	$adminTpl->footIncludes[] = $object->script;

	$adminTpl->admin_head($lang['cats'].' | '.$lnn);
	datetimepickerInit('date');
	$adminTpl->js_code[] = '$(".select2-single").select2();';	
	echo '<div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">'. $lnn .'</span>
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" action="{ADMIN}/module/storage/transfer/save" method="post" enctype="multipart/form-data" name="cats" role="form" class="form-horizontal parsley-form">
					<div class="form-group">
						<label for="type"  class="col-lg-3 control-label">'.$lang['org'].'</label>
						<div class="col-lg-4">
								<label class="field select">
									<select class="form-control" name="org" id="org">';
										$query_o = $db->query("SELECT n.*, o.name FROM ".DB_PREFIX."_org as n LEFT JOIN ".DB_PREFIX."_organizations as o on(o.id=n.oid) ORDER BY id ASC");
										if($db->numRows($query_o) > 0) 
										{
											while($o_list = $db->getRow($query_o)) 
											{
												// $selected = ($o_list['id'] == $org) ? "selected" : "";
												$selected = "";
												echo '<option value="'.$o_list['id'].'" '.$selected.'>'.$o_list['name'].'</option>';
											}
										}		
								echo '	</select><i class="arrow double"></i>
								</label>
						</div>
					</div>
					<div class="form-group">
						<label for="object"  class="col-lg-3 control-label">Объект</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">';
								oSelect("jsAdminTpl", "object", null, "getObjectToSelect");
								oSelect("select", "object", null, "getObjectToSelect");
								echo '<button '.($object->button_open).' type="button" class="button"><i class="fa fa-plus"></i></button>
							</div>	
						</div>	
					</div>
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Склад отправления</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">
								<label for="stock_out" class="field prepend-icon">
									<select name="stock_out" class="select2-single gui-input">';
										echo get_stock_select_option(["selected" => 1]);
									echo '</select>								
									<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
								</label>
							</div>	
						</div>	
					</div>	
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Склад назначения</label>
						<div class="col-lg-4">
							<div class="smart-widget sm-right smr-50">
								<label for="stock_in" class="field prepend-icon">
									<select name="stock_in" class="select2-single gui-input">';
										echo get_stock_select_option(["selected" => 2]);
									echo '</select>						
									<label for="translit" class="field-icon"><i class="fa fa-user"></i></label>												
								</label>
							</div>	
						</div>	
					</div>	
					<div class="form-group">
						<label for="date"  class="col-lg-3 control-label">Дата перемещения</label>
						<div class="col-lg-4">
							<label for="date" class="field prepend-picker-icon">
								<input id="date" type="text" name="date" placeholder="Введите дату " class="gui-input" value="'.$date.'">
							</label>
						</div>	
					</div>	
					<div class="form-group">
						<label class="col-sm-3 control-label">Комментарий</label>
						<div class="col-sm-4">
							<label for="comment" class="field prepend-icon">
								<textarea name="comment" id="comment" placeholder="" class="gui-textarea"></textarea>
								<label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer">Введите ваш комментарий к закупке!</span>
							</label>
							</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-success btn-parsley" id="sub" value="Создать перемещение" />
						</div>
					</div>
				</form>
			</div>
		</div>';

		echo $object->modal;

	$adminTpl->admin_foot();
}


function transfer_save() {
global $adminTpl, $db, $core, $lang;

	$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);	
	$date = !empty($_POST['date']) ? date('Y-m-d', datetostamp(filter($_POST['date']))) : null;
	$object= isset($_POST['object']) ? intval($_POST['object']) : false;	
	$org = isset($_POST['org']) ? intval($_POST['org']) : false;	
	$comment= isset($_POST['comment']) ? $_POST['comment'] : false;
	$stock_in = isset($_POST['stock_in']) ? $_POST['stock_in'] : '1';
	$stock_out = isset($_POST['stock_out']) ? $_POST['stock_out'] : '1';

	echo '<div id="content" class="animated fadeIn">';	
	if(!empty($date)&&!empty($object))
	{
		$data = [
			"org" => $org,
			"date" => $date,
			"object" => $object,
			"comment" => $comment,
			"stock_in" => $stock_in,
			"stock_out" => $stock_out,
		];
		transfer_create($data);
		$adminTpl->info('Перемещение успешно добавлена!', 'info', null, $lang['info'], 'Список перемещений', '/administration/module/storage/transfer');
	}
	else
	{
		$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
	}
	echo '</div>';
	$adminTpl->admin_foot();
} 

function transfer_head($id){
	global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	$order = get_query_order($id);
	$query_stock_in = $db->query("SELECT name FROM ".DB_PREFIX."_stock WHERE id = '".$order['stock_in']."' LIMIT 1");		
			$stock_in = $db->getRow($query_stock_in);
			$query_stock_out = $db->query("SELECT name FROM ".DB_PREFIX."_stock WHERE id = '".$order['stock_out']."' LIMIT 1");		
			$stock_out = $db->getRow($query_stock_out);
	
	$comment = $order["comment"];

	echo '<div class="col-md-6">
	<div class="element-groups-lg-custom wizard-options">
		<div class="ph10 printBlock">
			<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" onclick="printDoc();">
				<span class="fa fa-print holder-icon"></span><br> Распечатать <br>документ
			</a>
		</div>';

		$group = getGroup(["group" => ["storage", "admin"]]);
		$group[] = 13;
		$group[] = 6;
		$uid = $core->auth->user_info['id'];
		$name = "transfer";	

		if($order["active"] == 0){
			echo '<div class="ph10 transferBlock"><a class="holder-style p15 text-center holder-active" href="javascript:void(0);" onclick="'.disabled_all().' endTransfer();"><span class="fa fa-check-circle holder-icon"></span><br>Провести<br>Перемещение</a></div>';
		} else {
			echo '<div class="ph10 transferBlock"><a style="outline-color: red;" class="holder-style p15 text-center holder-active" href="javascript:void(0);" onclick="'.disabled_all().' startTransfer();"><span style="color: red;" class="fa fa-check-circle holder-icon"></span><br>Отменить<br>Перемещение</a></div>';
		}

		echo '<div class="ph10" onclick="mergingModal();"><a class="holder-style p15 text-center holder-active"><span class="fal fa-box-check holder-icon"></span><br> Создать <br>комплектацию</a></div>';

		$modal = logisticsDeliveryModal($id);
		echo $modal["btn_container"];
		// echo '<div class="ph10" data-toggle="modal" data-target="#deliveryModal"><a class="holder-style p15 text-center holder-active"><span class="fas fa-truck holder-icon"></span><br> Доставка <br><br></a></div>';
	echo '</div>
</div>
<div class="col-md-2">
	<small>Объект:</small><br/>
	<b>'.$order['objname'].'</b>&nbsp;&nbsp;
	<i class="fas fa-edit cp text-primary" data-toggle="modal" data-target="#object_modal"></i>
	<br/><br/>
	<small>Ответственный:</small><br/>
	<b>'. shortUserName($order['uid']).'</b><br/><br/>';
	$query = $db->query("SELECT s.id, s.numb, s.title 
	FROM `".DB_PREFIX."_stock_transfer_detail` as std
	LEFT JOIN `".DB_PREFIX."_sales_detail` as sd ON(sd.id = std.sdid)
	LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sd.sales_id)
	WHERE std.transfer_id = '".$id."' AND std.sdid IS NOT NULL GROUP BY s.id");
	if($db->numRows($query) > 0){
		echo '<small>Продажа:</small><br/>';
		while($s = $db->getRow($query)){
			echo '<b><a  href="/administration/module/sales/order/'.$s["id"].'" target="_blank">'. $s['numb'].' ('.(empty($s["title"]) ? "Не указано" : $s["title"]).')</a></b><br/>';	
		}
		echo '<br/>';
	}
	echo '<small>Организация:</small><br/>
	<b id="org" data-value="'.$order['org'].'">'. $order['org_name'].'</b><br/><br/>';
	echo <<<HTML
		<small>Комментарий:</small><br/>
		<b><span id="comment" data-value="{$comment}">{$comment}</span></b><br/><br/>
HTML;
echo '</div>
<div class="col-md-2">
	<small>Склад отправления:</small><br/>
	<b id="stock_out" class="cp" data-value="'.$order["stock_out"].'">'.$stock_out['name'].'</b><br/><br/>
	<small>Склад назначения:</small><br/>
	<b id="stock_in" class="cp" data-value="'.$order["stock_in"].'">'.$stock_in['name'].'</b><br/><br/>
	<small>Дата:</small><br/>
	<b>'.date("d.m.Y", strtotime($order['date'])).'</b><br/><br/>													
</div>
<div class="col-md-2">';
	$uid_gave = empty($order["uid_gave"]) ? "Не указан" : shortUserName($order["uid_gave"]);
	$uid_accepted = empty($order["uid_accepted"]) ? "Не указан" : shortUserName($order["uid_accepted"]);
	$uid_gave_modal = "";
	$uid_accepted_modal = "";
	if($order["active"] == 0){
		$uid_gave_modal = 'data-toggle="modal" data-target="#uid_goods_modal_uid_gave"';
		$uid_accepted_modal = 'data-toggle="modal" data-target="#uid_goods_modal_uid_accepted"';
	}
	echo '<small>Груз отдал:</small><br/>
	<b class="cp" '.$uid_gave_modal.'>'.$uid_gave.'</b><br/><br/>
	<small>Груз принял:</small><br/>
	<b class="cp" '.$uid_accepted_modal.'>'.$uid_accepted.'</b><br/><br/>
	<small>Файл скана:</small><br/>
	<b id="addFile" class="cp" onclick="add_file_modal();">Добавить файл</b><br/>';
		$qAttach = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `module` = 'transfer' AND `pid` = '".$id."'");
		if($db->numRows($qAttach) > 0){
			while($attach = $db->getRow($qAttach)){
				echo '<a href="/download/'.$attach["id"].'">'.$attach["name"].'</a><i class="fas fa-times cp text-danger ml20" onclick="del_file_modal('.$attach["id"].');" data-id="'.$attach["id"].'"></i>';
			}
		} else {
			echo 'Файлы не приложены!';
		}									
 echo '</div>';
}

function get_query_order($id){
	global $db;
	$query = $db->query("SELECT t.*, obj.name as objname, s.numb as s_numb, s.id as s_id, org.name as org_name
	FROM `".DB_PREFIX."_stock_transfer` as t 
	LEFT JOIN `".DB_PREFIX."_object` as obj on(obj.id=t.object) 
	LEFT JOIN `".DB_PREFIX."_sales` as s on(s.id=t.sales_id) 
	LEFT JOIN `".DB_PREFIX."_org` as o on(o.id=t.org) 
	LEFT JOIN `".DB_PREFIX."_organizations` as org on(org.id=o.oid) 
	WHERE t.id = '" . $id . "'"); 	
	$order = $db->getRow($query);
	return $order;
}

function transfer_detail($id, $logistic = false)  
{
global $adminTpl, $core, $db, $config, $admin_conf, $lang, $dn_array;
	$uid = $core->auth->user_info['id'];
	$rows_status = array();
	$rows_status[0] = 'Отменен';
	$rows_status[1] = 'Ожидает закупки';
    $rows_status[2] = 'Ожидает отправки';
	$rows_status[3] = 'Закуплено';
	$order = get_query_order($id);
    // $queryTest = $db->query("SELECT so.sales_id FROM JMY_supply_order_detail as sod LEFT JOIN  JMY_supply_order as so ON(so.id = sod.supply_o) WHERE so.id = '".$id."' LIMIT 1");
    // $rqt = $db->getRow($queryTest);
			
	$adminTpl->admin_head('Перемещение товаров №SN-'.$id);
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
    <input type="hidden" value="" id="saveCount">
    <input type="hidden" value="" id="savePrice">
    ';
	$attribute = [];
	$attribute["name"] = "";
	$attribute["script"] = true;
	$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
	$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
	echo uploaderMaster("css", $attribute);
	echo '

	<input type="hidden" value="'.$id.'" id="mainId">
	
	<input type="hidden" value="'.$order["active"].'" id="active">

	<section id="content" class="animated fadeIn">
		<div class="text-center">            
              <p class="lead">ПЕРЕМЕЩЕНИЕ ТОВАРОВ #<span id="numb">'.(empty($order["numb"]) ? module_perfix('storage_transfer', $id) : $order["numb"]).'</span> (<span id="title" class="cp">'.$order["title"].'</span>)</p>
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
					ajaxGet("'.ADMIN.'/module/supply/ajax/order_detail_list/'.$id.'", "tov_list");					
				}          
			});
		}        
	</script>    
       
		<div class="row" id="admin-form">			
			<div class="panel mb25 mt5">										
				<div class="panel-body p20 pb10">									
					<div class="section row mbn admin-form">
						<div class="col-md-12 pl15">
							<div class="section row mb15" id="transfer_head">';	
							
									transfer_head($id);
								
									echo '</div>												
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title" id="exampleModalLabel">Каким образом вы хотите провести перемещение?</h5>
			</div>
			<div class="modal-body">
				<div id="transferModalStep_1">
					<div class="element-groups-lg-custom wizard-options d-flex justify-content-around">
						<div class="ph10">
							<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" onclick="transferModalElectronicSign();">
								<span class="fas fa-sms holder-icon"></span><br> Электронное <br>подтверждение
							</a>
						</div>
						<div class="ph10">
							<a href="javascript:void(0);" class="holder-style p15 text-center holder-active transferBtn" data-dismiss="modal">
								<span class="fas fa-signature holder-icon"></span><br> Подпись <br><br>
							</a>
						</div>
					</div>
				</div>
				<div id="transferModalStep_2" class="transferModalStep">
					<form id="transferModalStep_2Form"></form>
				</div>
				<div id="transferModalStep_3" class="transferModalStep mt20">
					Загрузка данных...
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary transferBtn d-none" data-dismiss="modal" data-type="1">Провести</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		  </div>
		</div>
	  </div>

	  <div class="modal fade" id="transferModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title" id="exampleModalLabel">Отменить перемещение</h5>
			</div>
			<div class="modal-body">
			  Вы уверены что хотите отменить перемещение?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary transferBtn" data-dismiss="modal" data-type="2">Отменить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		  </div>
		</div>
	  </div>

		<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список товаров перемещения:</span>
					<div class="widget-menu pull-right">';
					echo '<span id="addProduct" style="display: '.($order["active"] === '0' ? '' : 'none').';" class="addProduct">';

					$victoria = new \Victoria;
					$victoria->link = "/administration/module/storage/transfer/victoria_add/{$id}";
					$bb = $victoria->button();
					echo $bb;
					// echo callAddProduct($id, "https://atri-energo.ru/administration/module/storage/transfer/addProduct/", true);
					
					echo '</span>';
					$notif = "notif('primary', '".$lang['info']."', '".$lang['ajax_update_compl']."');";
					echo '<button onclick="updateBody().then(() => {'.$notif.'});" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button>';
					echo '<button type="button" class="btn btn-xs btn-success" id="saveSupply" '.($order["active"] === "0" ? "" : "disabled").'>Сохранить</button>											
					</div>	
              </div>

			  <script type="text/javascript">ajaxGet(\'{MOD_LINK}/ajax/transfer_detail_list/'.$id.'\', \'tov_list\');</script>
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST" onsubmit="return false;">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w50">Артикул</th>
							<th class="w200">Наименование</th>							
							<th class="w50">Кол-во</th>
							<th class="w50">Цена</th>
							<th class="w50">Ед.</th>
							<th class="w50 text-center" title="Наличие / Доступно / Резерв">Склад отправления Н/Д/Р</th>																		
							<th class="w50 text-center" title="Наличие / Доступно / Резерв">Склад назначения Н/Д/Р</th>																		
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
					  <td align="right" colspan="15">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">	
								<option value="del">Удалить выбранное</option>								
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" disabled onclick="act_rout();"><span class="pd-l-sm"></span>
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
    
    <div class="modal fade" id="removeModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Удалить?</h5>
          </div>
          <div class="modal-body">
            Вы действитель хотите удалить?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="removeBtn">Удалить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
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
		  	$attribute["name"] = "5";
		  	$attribute["script"] = true;
		  	$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
		  	$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
		  	echo uploaderMaster("css", $attribute);
		  	echo uploaderMaster("upload", $attribute);
		  	echo uploaderMaster("view", $attribute);
          echo '</form></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary universalBtn" data-dismiss="modal" id="addFileModalBtn" onclick="add_file();">Добавить</button>
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
            <button type="button" class="btn btn-primary universalBtn" data-dismiss="modal" id="delFileModalBtn" onclick="del_file($(this).data(\'id\'));">Удалить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
	</div>
	
	<div class="modal fade" id="object_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog admin-form" role="document">
			<form id="object_modal_form">
		  		<div class="modal-content">
					<div class="modal-header">
					  <h5 class="modal-title" id="exampleModalLabel">Изменить объект</h5>
					</div>
					<div class="modal-body">';
						oSelect("jsAdminTpl", "object", null, "getObjectToSelect");
						oSelect("select", "object", null, "getObjectToSelect");
					echo '</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Изменить</button>
					  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
					</div>
		  		</div>
		  </form>
		</div>
	</div>';

	$modal = logisticsDeliveryModal($id);
	echo $modal["modal"];
	$adminTpl->footIncludes[] = $modal["script"];

	uid_goods_modal(["name" => "uid_gave", "id" => $order["uid_gave"]]);
	uid_goods_modal(["name" => "uid_accepted", "id" => $order["uid_accepted"]]);

	merging_modal('mergingModalCreate();');

	$attr = [
        "module" => 'stock_transfer_detail', 
        "id" => $id, 
        "update_func" => "updateBody", 
        "status" => 1,
		"hotkeys" => "",
		"copy_disabled" => $order["active"] == 0 ? "false" : "true",
    ];
    
    
    fastBar($attr);

	$adminTpl->footIncludes[] = '<script src="usr/other/sub/price/js/price.js?'.time().'"></script>';	
	$adminTpl->footIncludes[] = '<script src="usr/modules/storage/admin/asset/js/function.js?'.time().'"></script>';	
	$adminTpl->footIncludes[] = '<script src="usr/modules/storage/admin/asset/js/script.js?'.time().'"></script>';
	$victoria_min = new \Victoria;
	$victoria_min->link = "/administration/module/storage/transfer/victoria_add_min/\${id}";
	$vm = $victoria_min->open_func();
	$adminTpl->footIncludes[] = <<<HTML
	<script>
		function change_goods_open(id){
			{$vm}
		}
	</script>
HTML;
	xeditableInit();
	require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
	ndr_init();
	$adminTpl->admin_foot();
} 

function logisticsDeliveryModal($id){
	return \Logistics\Support\LogisticsDeliverySupport::add_modal(["pid" => $id, "module" => "stock_transfer"]);
}

function uid_goods_modal($request){
	$name = $request["name"];
	$id = $request["id"];
	$select_setting = [
        "mod" => "jsAdminTpl",
        "name" => $name,
        "id" => $id,
        "ajaxUrl" => "getUsersToSelect",
        "required" => true,
        "return" => true,
    ];
	oSelectCustom($select_setting);
	$select_setting["mod"] = "select";
	$select = oSelectCustom($select_setting);
	echo <<<HTML
	<div class="modal fade admin-form" id="uid_goods_modal_{$name}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="uid_goods_modal_form_{$name}">
		  		<div class="modal-header">
					<h5 class="modal-title">Изменение ответсвенного</h5>
		  		</div>
		  		<div class="modal-body">
						{$select}
		  		</div>
		  		<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Сохранить</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		  		</div>
			</form>
		</div>
	  </div>
	</div>
HTML;
}

function get_query_transfer_detail(){
	return "SELECT so.s_price, so.transfer_id, so.s_sku, so.s_name, so.id as soid, so.count, so.s_unit, st.active, so.cid 
	FROM `".DB_PREFIX."_stock_transfer_detail` as so 
	LEFT JOIN `".DB_PREFIX."_stock_transfer` as st ON(st.id = so.transfer_id)";
}

function ajax_transfer_detail_list($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;

	$order = get_query_order($id);
    
	$query = get_query_transfer_detail();
    $queryTest = $db->query("{$query} WHERE so.transfer_id = '".$id."' AND so.status != 0");
    
    if($db->numRows($queryTest) > 0){
		$i = 0;
		$total = 0;
        while($content = $db->getRow($queryTest))
        {
            $i++;
            // if(empty($content["sale_detail_id"])){
            //     $price = 0;
            // } else {
            //     $querySales = $db->query("SELECT * FROM ".DB_PREFIX."_sales_detail WHERE id = '".$content["sale_detail_id"]."' LIMIT 1");
            //     $salesGap = $db->getRow($querySales);
            //     $price = $salesGap["s_price"];
			// }

			$content["num"] = $i;
            echo transfer_detail_template($content['soid'], $content["cid"], $order, $content);

			$total += $content['count'] * $content['s_price'];

		}
		
		echo '<tr>
			<td colspan="4" class="text-right"><b>Итого:</b></td>
			<td><b class="totalAll">'.number_format($total ,2,'.',' ') .' руб.</b></td>
			<td colspan="4"></td>
		</tr>';
        
    } else {
        echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';	
    }
}

function transfer_detail_template($id, $cid, $order, $content){
	$s_name = ee($content['s_name']);
	$price = number_format($content['s_price'],2,'.',' ');
	$unit = get_unit('data', $content['s_unit'] ,'sname');
	$ndr_view_out = get_ndr(["cid" => $cid, "stock_id" => $order["stock_out"], "module" => "sales", "pid" => null, "returnType" => "view"]);
	$ndr_view_in = get_ndr(["cid" => $cid, "stock_id" => $order["stock_in"], "module" => "sales", "pid" => null, "returnType" => "view"]);
	$btn = $content["active"] == 0 ? "<button type=\"button\" data-id=\"{$id}\" class=\"btn btn-xs btn-danger removeDetail\">X</button>" : "";
	return <<<HTML
		<tr data-id="{$id}" class="transfer_item" id="transfer_item_{$id}">
			<td data-toggle="tooltip" title="id: {$id}; cid: {$cid}">{$content['num']}</td>
			<td>{$content['s_sku']}</td>
			<td class="changeGoods cp" data-id="{$id}" id="cg_{$id}">{$s_name}</td>
			<td class="count cp">{$content['count']}</td>				
			<td class="price cp" data-id="{$id}" data-price="{$content['s_price']}">{$price} руб.</td>				
			<td>{$unit}</td>		
			<td class="text-center ndr" onclick="view_ndr_modal({$cid})">{$ndr_view_out}</td>		
			<td class="text-center ndr" onclick="view_ndr_modal({$cid})">{$ndr_view_in}</td>		
			<td class="text-right">				
				<div class="btn-group">
					{$btn}
				</div>
			</td>
			<td class="w20 text-right">
				<div class="checkbox-custom mb15">
					<input id="checkbox{$id}" type="checkbox" name="checks[]" class="checks" value="{$id}">
					<label for="checkbox{$id}"></label>
				</div>
			</td>
		</tr>
HTML;
}

function transfer_detail_once($request){
	global $db;
	$id = $request["id"];
	$cid = $request["cid"];
	$query = get_query_transfer_detail();
	$q = $db->query("{$query} WHERE so.id = '{$id}'");
	$content = $db->getRow($q);
	$catalog = \Catalog_v2\Model\Catalog::find($cid);
	$content["num"] = $request["num"];
	$content["cid"] = $request["cid"];
	$content["s_sku"] = $catalog["sku"];
	$content["s_name"] = $catalog["title"];
	$order = get_query_order($content["transfer_id"]);
	echo transfer_detail_template($id, $request["cid"], $order, $content);
}

function ajax_transfer_tovar_add()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['cid'])))
	{
		$transfer_id = intval($_POST['id']);
		$mcid = $_POST['cid'];
        
		$add = ["transfer_id" => $transfer_id, "items" => []];
        
        foreach($mcid as $cid){
			$count = (isset($_POST['count_'.$cid]) ? $_POST['count_'.$cid] : 1);
			$add["items"][] = [
				"cid" => $cid,
				"count" => $count,
			];
		}

		transferTovarAdd($add);
		
		echo '1';
	}
	else
	{
		echo '2';
	}
}

function transferTovarAdd($request){
	//["transfer_id" => "", "items" => ["cid" => "", "count" => ""]]
	$transfer_id = $request["transfer_id"];
	$items = $request["items"];
	foreach($items as $item){
		$cid = $item["cid"];
		$count = $item["count"];
		$tovar_stable = \Catalog_v2\Model\Catalog::findOrFail($cid);

		$price = \Price::getPrice($cid);
		$price_rub = $price->price_purchase_;

		$add = [];
		$add["transfer_id"] = $transfer_id;
		$add["cid"] = $cid;
		$add["s_name"] = $tovar_stable['title'];
		$add["s_sku"] = $tovar_stable['sku'];
		$add["s_unit"] = $tovar_stable['unit'];
		$add["count"] = $count;
		$add["s_price"] = $price_rub;
			
		\Storage\Model\StockTransferDetail::create($add);
	}
}

function ajax_transfer_device_del()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$supply_detail_id = intval($_POST['supply_d']);
		//$comment = $_POST['comment'];
		$result = isset($_POST['result']) ? $_POST['result'] : 0;
		$status = '0';
		$db->query("DELETE FROM `" . DB_PREFIX . "_supply_order_detail` WHERE `id` = '" . $id . "' LIMIT 1 ;");
		$db->query("UPDATE ".DB_PREFIX."_supply_detail SET `status` = 1 WHERE `id` = '".$db->safesql($supply_detail_id)."' LIMIT 1");			
		//echo "DELETE FROM `" . DB_PREFIX . "_supply_order_detail` WHERE `id` = '" . $id . "' LIMIT 1 ;";
		echo 'Прибор успешно удален!';		
		
	}
}

function mergingCreate($request){
	global $db;
	$transfer_id = $request["transfer_id"];
	if(is_array($request["transfer_id"])){
		$transfer_id = $request["transfer_id"][0];
	}
	if(empty($request["merging_id"])){
		$order = get_query_order($transfer_id);
		$merging = \Merging\Model\Merging::create(["stock_id" => $order["stock_in"], "org" => $order["org"], "type" => 1, "object" => $order["object"], "transfer_list" => [$transfer_id]]);
		$merging_id = $merging->id;
	} else {
		$merging_id = $request["merging_id"];
		$merging = \Merging\Model\Merging::findOrFail($merging_id);
		$transfer_list = is_array($merging->transfer_list) ? $merging->transfer_list : [];
		$transfer_list[] = $transfer_id;
		$transfer_list = array_unique($transfer_list);
		$merging->transfer_list = $transfer_list;
		$merging->save();
	}
	

	$cid = [];
	$price = [];
	if(isset($request["id"]) && is_array($request["id"]) && count($request["id"]) > 0){
		$id_str = implode(",", $request["id"]);
		$query = $db->query("SELECT * FROM `".DB_PREFIX."_stock_transfer_detail` WHERE id IN(".$id_str.")");
	} else {
		if(is_array($request["transfer_id"])){
			$transfer_id_str = implode(", ", $request["transfer_id"]);
		} else {
			$transfer_id_str = $request["transfer_id"];
		}
		$query = $db->query("SELECT * FROM `".DB_PREFIX."_stock_transfer_detail` WHERE transfer_id IN(".$transfer_id_str.")");
	}

	$add = ["id" => $merging_id, "items" => []];

	if($db->numRows($query) > 0){
		while($r = $db->getRow($query)){
			if(isset($add["items"][ $r["cid"] ])){
				$add["items"][ $r["cid"] ]["count"] += $r["count"];
			} else {
				$add["items"][ $r["cid"] ]["count"] = $r["count"];
			}
		}
	}
	
	require_once(ROOT.'usr/modules/merging/admin/sub/tovar_save.php');
	tovar_add($add);
	return $merging_id;
}

function merging_modal($func){
	?>

		<div class="modal fade admin-form" id="merging_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Создать комплектацию</h5>
		      </div>
		      <div class="modal-body">
			  	<?php
			  		oSelect("jsAdminTpl", "merging_id", null, "getMergingToSelect");
					oSelect("select", "merging_id", null, "getMergingToSelect");
				?>
		      </div>
		      <div class="modal-footer">
			  	<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="<?php echo $func;?>">Выполнить</button>
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		      </div>
		    </div>
		  </div>
		</div>

	<?php
}

function getElectronicSignTransfer($id){
	$data = \Storage\Model\StockTransfer::find($id)->toArray();

	$template = [];
	$template[] = "<div>";
	
	if($data["uid_gave"] == $data["uid_accepted"]){
		sendElectronicSignTransfer([
			"id" => $id,
			"uid" => $data["uid_gave"],
			"data" => $data,
		]);
		$uid_gave = shortUserName($data["uid_gave"]);
		$template[] = <<<HTML
			<div>Код ({$uid_gave}):</div>
			<input type="text" class="form-control" placeholder="Введите код" name="uid" id="input_uid" required>
HTML;
	} else {
		sendElectronicSignTransfer([
			"id" => $id,
			"uid" => $data["uid_gave"],
			"data" => $data,
		]);
		sendElectronicSignTransfer([
			"id" => $id,
			"uid" => $data["uid_accepted"],
			"data" => $data,
		]);
		$uid_gave = shortUserName($data["uid_gave"]);
		$uid_accepted = shortUserName($data["uid_accepted"]);
		$template[] = <<<HTML
		<div>Код ({$uid_gave}):</div>
		<input type="text" class="form-control" placeholder="Введите код" name="uid_gave" id="input_uid_gave" required>
		<div>Код ({$uid_accepted}):</div>
		<input type="text" class="form-control" placeholder="Введите код" name="uid_accepted" id="input_uid_accepted" required>
HTML;
	}
	$template[] = <<<HTML
		<div class="d-flex justify-content-end"><button type="submit" class="btn btn-success btn-xs fas fa-share"></button></div>
	</div>
HTML;

	return ["template" => implode("", $template)];
}

function sendElectronicSignTransfer($request){
	$uid = $request["uid"];
	\Boot\Models\Users::find($uid)->notify(new \Storage\Notifications\ElectronicSignTransfer($request));
	
	// $id = $request["id"];
	// $data = $request["data"];

	// $ElectronicConfirmationAdd = [
	// 	"pid" => $id,
	// 	"module" => "transfer",
	// 	"uid" => $uid,
	// ];
	// $where = $ElectronicConfirmationAdd;
	// $where["used"] = 0;
	// $edata = \Boot\Models\ElectronicConfirmation::where($where)->orderByDesc("id")->first();
	// if(!empty($edata)){
	// 	$iid = $edata->id;
	// 	$code = $edata->code;
	// } else {
	// 	$code = \Boot\Models\ElectronicConfirmation::generate_code();
	// 	$ElectronicConfirmationAdd["code"] = $code;
	// 	$ElectronicConfirmation = \Boot\Models\ElectronicConfirmation::create($ElectronicConfirmationAdd);
	// 	$iid = $ElectronicConfirmation->id;
	// }
	// \Boot\Models\Users::find($uid)->notify(new \Storage\Notifications\ElectronicSignTransfer());
	

	// $attr = [];
	// $attr['title'] = "Код подтверждения перемещения №{$data['numb']}";
    // $attr['icon'] = config('app.url').'/files/push/task.png';
    // $attr['url'] = config('app.url').'/administration/module/storage/transfer/activeElectronicSign/'.$iid;
    // $attr['module'] = "transfer_code";
	// $attr['pid'] = $iid;
	// $attr['uid'] = $uid;
	// $attr['text'] = $code;

	// sendViber($attr);
}

function confirmElectronicSign($id, $request){
	$data = \Storage\Model\StockTransfer::find($id)->toArray();

	$confirm = false;
	$uid_gave = false;
	$uid_accepted = false;

	$uid_gave_name = shortUserName($data["uid_gave"]);
	$uid_accepted_name = shortUserName($data["uid_accepted"]);

	if($data["uid_gave"] == $data["uid_accepted"]){
		$code = $request["uid"];
		$uid = confirmElectronicSignOne([
			"id" => $id,
			"data" => $data,
			"code" => $code,
			"uid_field" => "uid_gave",
		]);

		if($uid->count() > 0){
			confirmElectronicSignOneUsed($uid);
			$confirm = true;
			$uid_gave = true;
			$uid_accepted = true;
		}
	} else {
		$uid_gave_code = $request["uid_gave"];
		$uid_accepted_code = $request["uid_accepted"];
		$uid_gave = confirmElectronicSignOne([
			"id" => $id,
			"data" => $data,
			"code" => $uid_gave_code,
			"uid_field" => "uid_gave",
		]);
		$uid_accepted = confirmElectronicSignOne([
			"id" => $id,
			"data" => $data,
			"code" => $uid_accepted_code,
			"uid_field" => "uid_accepted",
		]);
		if($uid_gave->count() > 0 && $uid_accepted->count() > 0){
			confirmElectronicSignOneUsed($uid_gave);
			confirmElectronicSignOneUsed($uid_accepted);
			$confirm = true;
			$uid_gave = true;
			$uid_accepted = true;
		} 
	}
	return [
		"confirm" => $confirm, 
		"uid_gave" => $uid_gave, 
		"uid_gave_id" => $data["uid_gave"], 
		"uid_gave_name" => $uid_gave_name, 
		"uid_accepted" => $uid_accepted, 
		"uid_accepted_id" => $data["uid_accepted"], 
		"uid_accepted_name" => $uid_accepted_name
	];
}

function confirmElectronicSignOne($request){
	$id = $request["id"];
	$data = $request["data"];
	$code = $request["code"];
	$uid_field = $request["uid_field"];

	$uid = $data[ $uid_field ];

	$module = "transfer";
	
	return \Boot\Models\ElectronicConfirmation::where("code", $code)->
	where("module", $module)->where("pid", $id)->where("uid", $uid)->where("used", 0)->get();
}

function confirmElectronicSignOneUsed($edata){
	foreach($edata as $item){
		$item->update(["used" => 1]);
	}
}

function activeElectronicSignTemplate($id){
	global $adminTpl, $core;
	$uid = $core->auth->user_info['id'];
	$d = \Boot\Models\ElectronicConfirmation::find($id)->toArray();
	if($d["uid"] == $uid){
		$adminTpl->admin_head('');
		$btn = "";
		$template_hidden = "";
		if($d["used"] == 0 && $d["active"] == 0){
			$btn = <<<HTML
				<button type="submit" class="btn btn-success" id="btn_active">Подтвердить</button>
HTML;
		$template_hidden = 'style="display:none;"';
		}

		$template = <<<HTML
			<div {$template_hidden} id="temp_used">Заявка использована</div>
HTML;

		$data = \Storage\Model\StockTransfer::find($d["pid"])->toArray();

		echo <<<HTML
			<div class="d-flex justify-content-center mt20">
				<form id="ElectronicSignForm">
					<div class="mb20"><a href="/administration/module/storage/transfer/detail/{$data['id']}" target="_blank">Перемещение №{$data["numb"]}</a></div>
					{$btn}
					{$template}
				</form>
			</div>
HTML;
		$adminTpl->footIncludes[] = <<<HTML
			<script>
				$(function(){
					$("#ElectronicSignForm").on("submit", async function(e){
						e.preventDefault();
						const lw = new LW;
						lw.on();
						await activeElectronicSign();
						swal('Успешно!', 'Данные отправлены', 'success');
						$("#temp_used").show();
						$("#btn_active").hide();
						lw.off();
					});

					function activeElectronicSign(){
						return new Promise((resolve, reject) => {
							$.post("/administration/module/storage/transfer/activeElectronicSign/{$id}", function(){
								resolve();
							});
						});
					}
				});
			</script>
HTML;
		$adminTpl->admin_foot();
	}
}

function activeElectronicSign($id){
	\Boot\Models\ElectronicConfirmation::find($id)->update(["active" => 1]);
}

function checkActiveElectronicSign($id){
	$data = [];
	$d = \Boot\Models\ElectronicConfirmation::where("active", 1)->where("module", "transfer")->where("pid", $id)->where("used", 0)->get()->toArray();
	if(count($d) > 0){
		$st = \Storage\Model\StockTransfer::find($id)->toArray();
		foreach($d as $i){
			$code = $i["code"];
			$type = false;
			if($st["uid_gave"] == $st["uid_accepted"]){
				$type = "uid";
			} else {
				if($i["uid"] == $st["uid_gave"]){
					$type = "uid_gave";
				}
				if($i["uid"] == $st["uid_accepted"]){
					$type = "uid_accepted";
				}
			}
			$data[] = ["code" => $code, "type" => $type];
		}
	}

	return $data;
}

function transfer_doc_create($id){
	$d = \Boot\Models\ElectronicConfirmation::where("module", "transfer")->where("pid", $id)->where("used", 1)->get()->toArray();
	$st = \Storage\Model\StockTransfer::find($id)->toArray();
	$uid_gave = "";
	$uid_gave_date = "";
	$uid_gave_code = "";
	$uid_accepted = "";
	$uid_accepted_date = "";
	$uid_accepted_code = "";
	foreach($d as $item){
		$uid = shortUserName($item["uid"]);
		$date = date("d.m.Y", strtotime($item["created_at"]));
		$code = md5($item["code"]);
		if($st["uid_gave"] == $item["uid"]){
			$uid_gave = $uid;
			$uid_gave_date = $date;
			$uid_gave_code = $code;
		}
		if($st["uid_accepted"] == $item["uid"]){
			$uid_accepted = $uid;
			$uid_accepted_date = $date;
			$uid_accepted_code = $code;
		}
	}
	$data = print_storage_file($id, "storage", [
		"uid_gave_sign" => "Документ подписан электронной подписью {$uid_gave} от {$uid_gave_date} ({$uid_gave_code})",
		"uid_accepted_sign" => "Документ подписан электронной подписью {$uid_accepted} от {$uid_accepted_date} ({$uid_accepted_code})",
	]);
	$file_url = ROOT.$data["clear_file_url"];
	$ext = pathinfo($file_url);
	$ext2 = pathinfo($data["clear_file_url"]);
	$new_name = time()."_".$ext["basename"];
	rename($file_url, $ext["dirname"]."/".$new_name);
	\Boot\Models\Attach::create(["module" => "transfer", "pid" => $id, "url" => $ext2["dirname"]."/".$new_name, "name" => $new_name]);
}