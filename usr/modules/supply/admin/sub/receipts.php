<?php

use Boot\System\Office\OfficeSupport;

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

function receipts()
{
    global $core, $adminTpl, $db, $lang;
	$lnn = $lang['supply_main'];
	$adminTpl->admin_head($lang['supply_receipts'].' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'supply';
	$atributs['module_open'] = 'receipts_detail';
	loadGrid('jqReceipts', $lang['supply_receipts'], $atributs, ROOT.'/usr/modules/supply/admin/grid/');
	echo '
	<div class="modal fade" id="removeReceiptsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="">Удалить документ поступления</h5>
			</div>
			<div class="modal-body">
				Вы действительно хотите удалить документ поступления <span class="removeReceiptsModalNum"></span>?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal" id="removeReceiptsModalBtn">Удалить</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="removeReceiptsModalError" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="">Внимание</h5>
			</div>
			<div class="modal-body">
				Прежде чем удалить документ поступления <span class="removeReceiptsModalNum"></span>, отмените заявку	
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
			</div>
		</div>
	</div>
	';
	echo '<script src="/usr/modules/supply/admin/asset/js/scriptReceipts.js?'.time().'"></script>';
	
	print_modal();
	
	$adminTpl->admin_foot();
}

function receipts_query_result($id){
	global $core, $adminTpl, $db, $lang;
	$query = $db->query("SELECT r.*, o.name as oname, s.numb as s_numb, so.numb as so_numb, st.name as st_name 
	FROM `".DB_PREFIX."_receipts` as r 
	LEFT JOIN `".DB_PREFIX."_object` as o ON(o.id = r.object) 
	LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = r.sales_id) 
	LEFT JOIN `".DB_PREFIX."_supply_order` as so ON(so.id = r.supply_id) 
	LEFT JOIN `".DB_PREFIX."_stock` as st ON(st.id = r.stock_id) 
	WHERE r.id = '" . $id . "'"); 	
	$order = $db->getRow($query);	
	return $order;
}
    
function receipts_detail($id)  {
	global $core, $adminTpl, $db, $lang;

	$order = receipts_query_result($id);

	$adminTpl->admin_head($lang['supply_main'].' | '.$lang['supply_receipt'].' '.$order["numb"].'');	
	xeditableInit();
    $adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/price.js?'.time().'"></script>';
	$adminTpl->footIncludes[] = '<script src="/usr/modules/supply/admin/asset/js/scriptReceiptsDetail.js?'.time().'"></script>';
	
    $checked = '';
    if($order["nds_includes"] == 1) $checked = 'checked';
    echo '<input type="hidden" value="" id="saveData">
    <input type="checkbox" id="includesNds" '.$checked.' style="display: none;">
    <input type="hidden" id="mainId" value="'.$id.'">';
	echo '
    <style>
    .cp{
        cursor: pointer;
    }
    </style>
	<section id="content" class="animated fadeIn">
		<div class="text-center">            
            <p class="lead" style="text-transform: uppercase;">'.$lang['supply_receipt'].' '.$order["numb"].' ('.$order['doc_number'].')</p>
        </div>	
		<div class="row" id="admin-form">			
			<div class="panel mb25 mt5">										
				<div class="panel-body p20 pb10">									
					<div class="section row mbn admin-form">
						<div class="col-md-12 pl15">
							<div class="section row mb15" id="receipts_head">';	
								ajax_receipts_head($id);
						echo '</div>												
						</div>
					</div>
				</div>
			</div>
		</div>
        
        <div class="modal fade" id="fileModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Добавить файлы</h5>
              </div>
              <div class="modal-body">
              <form id="fileForm">';
                        $attribute = [];
                        $attribute["name"] = "";
                        $attribute["script"] = true;
                        $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                        $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                        echo uploaderMaster("css", $attribute);
                        echo uploaderMaster("upload", $attribute);
                        echo uploaderMaster("view", $attribute);
              echo '</form></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="saveFileBtn">Сохранить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal fade" id="fileRemoveModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Удалить файл?</h5>
              </div>
              <div class="modal-body">
                Вы действительно хотите удалить файл <b><span id="fileName"></span></b>?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="removeFileBtn">Удалить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
		</div>

        <div class="modal fade" id="delTovModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Удалить товар</h5>
              </div>
              <div class="modal-body">
                Вы действительно хотите удалить товар?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="delTovModalBtn" onclick="delTovBtn(this);">Удалить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
		</div>

        <!--<div class="modal fade" id="printModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Выберите дальнейшее действие</h5>
              </div>
              <div class="modal-body">
				
			  <table class="table list-table" style="padding: 0px;">
                <tbody>
                  <tr>
                    <td onclick="window.open(\'/administration/module/supply/receipts_print_pdf/'.$id.'\'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать заказ</td>
                    <td class="text-right">
						<a href="/administration/module/supply/receipts_print_pdf/'.$id.'" target="_blank">PDF</a> 
						<a href="/administration/module/supply/receipts_print_docx/'.$id.'" target="_blank">DOCX</a>  
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
		</div>--!>
		
        <div class="modal fade admin-form" id="transferModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Перемещение</h5>
              </div>
			  <div class="modal-body">
			  <select name="stock_in" id="stock_in" class="form-control">';
			  $stockQuery = $db->query("SELECT * FROM ".DB_PREFIX."_stock WHERE name IS NOT NULL");
			  while($sq = $db->getRow($stockQuery)){
				  $selected = "";
				  if($sq["id"] === "2"){
					  $flag = false;
					  $selected = "selected";
				  }
				  echo '<option value="'.$sq["id"].'" '.$selected.'>'.$sq["name"].'</option>';
			  }
			  echo '</select>';
				oSelect("jsAdminTpl", "oid", null, "getObjectToSelect");
				oSelect("select", "oid",  null, "getObjectToSelect");
			  echo '</div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="transferModalBtn">Переместить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>
        
		<div class="row">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading">
					<span class="panel-title">Список товаров:</span>
					<div class="widget-menu pull-right" >
						<div class="btn-group">';
							$notif = "notif(\'primary\', \'".$lang['info']."\', \'".$lang['ajax_update_compl']."\');";
								echo '<span style="display: '.($order['active'] == 0 ? '' : 'none').';" class="elemHide"><button class="btn btn-xs btn-success" id="saveReceipt"><i class="fa fa-floppy-o mr10" aria-hidden="true"></i>Сохранить</button></span>';
							echo '<span><button onclick="ajaxGetJS(\'{MOD_LINK}/ajax/receipts_detail_list/'.$id.'\', \'demoHighCharts.init(); '.$notif.'\', \'dev_list\');" type="button" class="btn btn-xs btn-system"><span class="mr10 icon fa fa-refresh"></span>Обновить</button></span>
						</div>							
											
					</div>	
              </div>
			  <script type="text/javascript">ajaxGet(\'{MOD_LINK}/ajax/receipts_detail_list/'.$id.'\', \'dev_list\');</script>
              <div class="panel-body pn table-responsive"> 
				<form id="tablesForm" method="POST">
                  <table class="table table-striped">
                    <thead>
						<tr>
							<th class="w40"><span class="pd-l-sm"></span>#</th>
							<th class="w50">Артикул</th>
							<th class="w100">Наименование</th>							
							<th class="w50">Кол-во</th>
							<th class="w50 text-center" data-toggle="tooltip">Осталось принять</th>
							<th class="w100" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
							<th class="w50">Ед.</th>
							<th class="w50">Цена закупки</th>
							<th class="w100">Сумма</th>
							<th class="w50">Ставка НДС</th>
							<th class="w100">НДС</th>												
							<th class="w20">' . $lang['actions'] . '</th>
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
					  <td align="right" colspan="15">
                        <nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act" id="act">							
			  					<option value="delete">Удалить</option>
							</select>
							<input name="submit" type="button" class="btn btn-success" style="display: inline-block;" id="sub" value="' .  $lang['doit'] . '" disabled onclick="subAct();"><span class="pd-l-sm"></span>							
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

	require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
	ndr_init();

	print_modal();

	$adminTpl->admin_foot();
} 

function ajax_receipts_head($id){
	global $db, $core, $lang;

	$order = receipts_query_result($id);

	$uid = $core->auth->user_info['id'];

	echo '<div class="col-md-3">
	<div class="element-groups-lg-custom wizard-options">';
	if($order['active'] == 0){
		echo '<div class="ph10" id="conductBlock">
			<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" onclick="'.disabled_all().' $(\'#conductModal\').modal(\'show\');"><span class="fa fa-check-circle holder-icon"></span><br> Провести<br>поступление</a>
		</div>';
	}
	if($order['active'] == 1){
		echo '<div class="ph10" id="distributeBlock">
			<a href="javascript:void(0);" class="holder-style p15 text-center holder-active" style="outline-color: red;" onclick="'.disabled_all().' 
			$(\'#distributeModal\').modal(\'show\');"><span style="color:red;" class="fa fa-check-circle holder-icon"></span><br>Отменить<br>поступление</a>
		</div>';
	}
		echo '<div class="ph10">
			<a href="javascript:void(0);" onclick="print_modal('.$id.');" class="holder-style p15 text-center holder-active"><span class="glyphicon glyphicon-print holder-icon"></span><br> Распечатать<br>документы</a>
		</div>';
		
		echo '<div class="ph10" id="">
			<a href="javascript:void(0);" class="holder-style p15 text-center" id="transferBtn"><span class="fa fa-exchange holder-icon"></span><br> Создать<br>перемещение</a>
		</div>';

	echo '</div>
</div>

<div class="modal fade" id="conductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">Провести?</h5>
	  </div>
	  <div class="modal-body">
		Вы действительно хотите провести?
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary universalBtn" data-dismiss="modal" id="conductBtn" ondblclick="return false;">Продолжить</button>
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	  </div>
	</div>
  </div>
</div>

<div class="modal fade" id="distributeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
  <div class="modal-header">
	<h5 class="modal-title" id="exampleModalLabel">Распровести?</h5>
  </div>
  <div class="modal-body">
	Вы действительно хотите распровести?
  </div>
  <div class="modal-footer">
	<button type="button" class="btn btn-primary universalBtn" data-dismiss="modal" id="distributeBtn" ondblclick="return false;">Продолжить</button>
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
  </div>
</div>
</div>
</div>


<div class="col-md-3">
	<input type="hidden" value="'.$order['active'].'" id="active">

	<small>Поставщик:</small><br/>
	<b>'.ee($order['oid_name']).'</b>			
	<a target="_blank" href="'.ADMIN.'/module/organizations/edit/'.$order['oid'].'"><span class="fa fa-external-link"></span></a>
	<br/><br/>
	<small>'.$lang['uid'].':</small><br/>
	<b>'. shortUserName($order['uid']).'</b><br/><br/>
	<small>Тип документа:</small><br/>
	<b id="type_doc" data-value="'.$order['type_doc'].'"></b><br/><br/>
	<b><a href="javascript:void(0);" onclick="viewReserv(true);">Посмотреть резервы</a></b>';


	echo '<div class="modal fade" id="viewReservModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

</div>
<div class="col-md-3">
	<small>Входящий номер:</small><br/>
	<b class="cp" id="doc_number">'.$order['doc_number'].'</b><br/><br/>

	<small>'.$lang['date'].':</small><br/>
	<b id="date_receipts">'. (empty($order['date_receipts']) ? 'Не указано!' : date("d.m.Y", strtotime($order['date_receipts']))).'</b><br/><br/>
	<small>Дата создания:</small><br/>
	<b>'. (empty($order['date']) ? 'Не указано!' : date("d.m.Y", strtotime($order['date']))).'</b><br/><br/>

	<span id="waybill_number_con" style="display: none;">
	<small>Номер товарной накладной:</small><br/>
	<b class="cp" id="waybill_number">'.$order['waybill_number'].'</b><br/><br/>
	</span>

	<small>Объект:</small><br/>
	<b>';
	if(empty($order['object'])){
		echo 'Не указан';
	} else {
		echo '<a href="/administration/object/view/'.$order['object'].'">'.$order['oname'].'</a>';
	}
	
	echo '</b><br/><br/>
	<small>Склад:</small><br/>
	<b class="cp" id="stock_id" data-type="select" data-stock_id="'.$order["stock_id"].'">'.$order['st_name'].'</b><br/><br/>
</div>
<div class="col-md-3">
<small>Приложенные документы:</small><br/>';
	$query = $db->query("SELECT * FROM JMY_attach WHERE `pid` = '".$id."' AND `module` = 'receipts'");
	if($db->numRows($query) > 0){
	echo '<a href="javascript:void(0);" class="cp" id="addFile">Добавить файлы</a>
	<div id="fileBasket">';
		while($r = $db->getRow($query)){
			echo '<div class="mb5"><a target="_black" href="/download/'.$r["id"].'">'.$r["name"].'</a><i class="fa fa-times ml10 cp removeFile" style="color: red;" data-id="'.$r["id"].'" data-name="'.$r["name"].'" aria-hidden="true"></i></div>';
		}
		echo '</div>';
	} else {
		echo '<a href="javascript:void(0);" class="cp" id="addFile">Добавить файлы</a>
	<div id="fileBasket"><p><b>Отсутствуют</b></p></div>';
	} 

	echo '<small>Продажа:</small><br/>
	<b>';
	$query = $db->query("SELECT DISTINCT s.id, s.numb, o.name as o_name, o.id as o_id
	FROM `".DB_PREFIX."_receipts_detail` as rd 
	LEFT JOIN `".DB_PREFIX."_sales_detail` as sd ON(sd.id = rd.sales_detail_id) 
	LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sd.sales_id)
	LEFT JOIN `".DB_PREFIX."_object` as o ON(o.id = s.object) 
	WHERE rd.rid = '".$id."' AND sd.id IS NOT NULL");
	$no_sales = 'Не указан';
	if($db->numRows($query) > 0){
		while($rd = $db->getRow($query)){
			echo '<a href="/administration/module/sales/order/'.$rd["id"].'">'.$rd['numb'].'</a>
			 - 
			<a href="administration/object/view/'.$rd["o_id"].'">'.$rd["o_name"].'</a><br/>';
		}
	} else {
		echo $no_sales;
	}
	echo '</b><br/><br/>
	<small>Счёт поставщика:</small><br/>
	<b><a href="/administration/module/supply/order_detail/'.$order["supply_id"].'">'.$order['so_numb'].'</a></b><br/><br/>

	</div>';
}

function ajax_receipts_detail_list($id)
{
	global $adminTpl, $core, $db, $admin_conf, $lang,  $dn_array;
	$query = $db->query("SELECT rd.*, sod.count_receipts, sod.count as sod_count, r.active, r.stock_id FROM ".DB_PREFIX."_receipts_detail as rd LEFT JOIN ".DB_PREFIX."_supply_order_detail as sod ON(rd.supply_detail_id = sod.id) LEFT JOIN ".DB_PREFIX."_receipts as r ON(r.id = rd.rid) WHERE rd.rid = '".$id."' AND rd.status != '0'");	
	if($db->numRows($query) > 0)
	{	
	$i=0;
	while($content = $db->getRow($query)) {

		$i++;

		$ndr_view = get_ndr(["cid" => $content["cid"], "stock_id" => $content["stock_id"], "module" => "sales", "pid" => null, "returnType" => "view"]);

		$unit = get_unit('data', $content['unit'] ,'name');

		$name = ee($content['name']);

			echo '
			<tr data-id="'.$content['id'].'" id="tr_'.$content['id'].'">
				<td data-toggle="tooltip" title="id: ' . $content['id'] . '; cid: '.$content["cid"].'"><span class="pd-l-sm"></span>' . $i . '</td>
				<td>' . $content['sku'] . '</td>
				<td>' . $name . '</td>
				<td class="cp count">' . $content['count'] . '</td>	
				<td class="text-center">'.($content['sod_count'] - $content['count_receipts']).'</td>				
				<td class="ndr" onclick="view_ndr_modal('.$content["cid"].');">'.$ndr_view.'</td>			
				<td>' . $unit . '</td>			
				<td class="cp edit">' . number_format($content['price'],2,'.',' '). ' руб.</td>	
				<td class="cp total">' . number_format($content['total'],2,'.',' '). ' руб.</td>	
				<td class="cp typeNds">'.\Price::getNds($content['type_nds']).'%</td>	
				<td class="cp nds">' . number_format($content['nds'],2,'.',' '). ' руб.</td>';				
				
				echo '<td>				
					<div class="btn-group">
						<div class="btn-group">
						<button type="button" class="btn btn-primary btn-xs" onclick="convertModal({id:\''.$content['id'].'\', unit:\''.$content['unit'].'\', count: \''.$content['count'].'\'});">Конвертировать</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu pull-right">	
							<li><a href="javascript:void(0);" onclick="delTovModal('.$content['id'].');">Удалить</a></li>
						</ul>
					</div>							  
				</td>
				<td class="w20 text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['id'] . '" type="checkbox" name="checks[]" class="checks" value="' . $content['id'] . '">
						<label for="checkbox' . $content['id'] . '"></label>
					</div>
				</td>
			</tr>';		
		}
        $query = $db->query("SELECT * FROM JMY_receipts WHERE id = '".$id."' LIMIT 1");
        $r = $db->getRow($query);
        echo '<tr>
            <td colspan="6" class="noSort text-right"><b>Итого:</b></td>
            <td colspan="7" class="noSort allTotal text-left"><b>'.number_format($r["total"],2,'.',' ').' руб.</b></td>
		</tr>
        <tr>
            <td colspan="6" class="noSort text-right"><b>В том числе НДС:</b></td>
            <td colspan="7" class="noSort allNds text-left"><b>'.number_format($r["nds"],2,'.',' ').' руб.</b></td>
		</tr>';
	} 	
	else 
	{
		echo '<tr><td colspan="12">У данной заявки еще не заполнен список приборов!</td></tr>';	
	}	
}

function receipts_print($id, $format = "docx"){
	global $db, $core, $url;
	if (!empty($id)){
		require_once(ROOT."/vendor/autoload.php");
		$tpl = ROOT.'/files/tpl/supply/receipts.docx';
		$document =  new \PhpOffice\PhpWord\TemplateProcessor($tpl);
		$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];				
		$order_data = [];

		$query = $db->query("SELECT r.*, s.name as stock_name, o.name as oid_name  FROM ".DB_PREFIX."_receipts as r LEFT JOIN ".DB_PREFIX."_stock as s ON(s.id = r.stock_id) LEFT JOIN ".DB_PREFIX."_organizations as o ON(o.id = r.oid) WHERE r.id = '".$id."' LIMIT 1");
		if($db->numRows($query) > 0){
			$receipts = $db->getRow($query);

			$doc_numb = $receipts["numb"];
			$doc_numb_in = $receipts["numb"]." (".$receipts["doc_number"].")";

			$date_first = strtotime($receipts["date_receipts"]);
			$date_day = date("d", $date_first);
			$date_month = $Month_rus[date("n", $date_first)];
			$date_year = date("Y", $date_first);
			$date = $date_day." ".$date_month." ".$date_year." г.";

			$stock = $receipts["stock_name"];

			$oid = $receipts["oid_name"];

			$org = getOrgInfo($receipts['org']);
			$org = $org["organizations"]["name"];

			$query = $db->query("SELECT * FROM ".DB_PREFIX."_receipts_detail WHERE rid = '".$id."'");
			$i = 0;
			while($r = $db->getRow($query)){
				$i++;
				$order_data[] =  [
					'n'     => $i,
					'sku' 	=> $r["sku"],
					'name'  => $r["name"],
					's'     => $r["count"],
					'u'     => get_unit('data', $r["unit"]),
				];
			}
			$document->setValue('doc_numb', $doc_numb);
			$document->setValue('doc_numb_in', $doc_numb_in);
			$document->setValue('date', $date);
			$document->setValue('stock', $stock);
			$document->setValue('oid', $oid);
			$document->setValue('org', $org);
			$document->setValue('s_numb', $i);
			$document->cloneRowAndSetValues('n', $order_data);

			$file_url = ROOT.'files/supply/'.$id.'/'.$doc_numb.'_print.docx';
			$file_url_pdf = ROOT.'files/supply/'.$id.'/'.$doc_numb.'_print.pdf';
			$document->saveAs($file_url);
			switch($format){
				default:
				case "docx":
					OfficeSupport::get_file($file_url);
				break;
				case "pdf":
					\Boot\Jobs\PdfJob::dispatchSync($file_url, $doc_numb.'_print.pdf');
					OfficeSupport::get_file($file_url_pdf);
				break;
			}
			
		}
	}
}