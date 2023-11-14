<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}

$dir = ROOT.'/usr/modules/storage/admin/sub/';  
includeSub($dir);

switch(isset($url[3]) ? $url[3] : null) {
	default:
		content_main();
	break;	

	case "delete_storage":
		\Storage\Model\Storage::destroy($url[4]);
	break;

	case "storage_load_excel":
		$id = intval($url[4]);
		$result = storage_load_excel($id, $_FILES);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	break;

	case "detail_save_file":
		$id = intval($url[4]);
		$request = $_POST;
		$file = $request["file"];
		$attribute = [];
		$attribute["name"] = "";
		$attribute["file"] = $file;
		$attribute["module"] = 'storage_detail';
		$attribute["mod"] = 'storage_detail';
		$attribute["id"] = $id;
		$attribute["comm_id"] = $id;
		$attribute["ftp"] = "StorageFTP";
		uploaderMaster("download", $attribute);
	break;

	case "detail_position_update":
		$request = $_POST;
		$data = json_decode($request["data"]);
		foreach($data as $id => $position){
			$storage_detail = \Storage\Model\StorageDetail::find($id);
			$storage_detail->position = $position;
			$storage_detail->save();
		}
	break;

	case "delivery_storage":
		switch(isset($url[4]) ? $url[4] : null){
            default:
				$LogisticsDeliveryDetailStorageTemp = new \Logistics\LogisticsDeliveryDetailStorageTemp;
				$LogisticsDeliveryDetailStorageTemp->view();
			break;
			case "update_info":
				$LogisticsDeliveryDetailStorageTemp = new \Logistics\LogisticsDeliveryDetailStorageTemp;
				echo $LogisticsDeliveryDetailStorageTemp->detail_info();
			break;
			case "update_list":
				$LogisticsDeliveryDetailStorageTemp = new \Logistics\LogisticsDeliveryDetailStorageTemp;
				echo $LogisticsDeliveryDetailStorageTemp->detail_list();
			break;
		}
	break;

	case "x_detail_update":
		$request = $_POST;
		$name = $request["name"];
		$value = $request["value"];
		$pk = $request["pk"];
		\Storage\Model\Storage::find($pk)->update([$name => $value]);
	break;

	case "x_detail_detail_update":
		$request = $_POST;
		$name = $request["name"];
		$value = $request["value"];
		$pk = $request["pk"];
		\Storage\Model\StorageDetail::find($pk)->update([$name => $value]);
	break;

	case "detail_delete_file":
		$id = intval($url[4]);
		\Boot\Models\Attach::find($id)->delete();
	break;
	
	case "reserv":
		switch(isset($url[4]) ? $url[4] : null){
			default;
				content_main_reserv();
			break;
			case "detail":
				$id = intval($url[5]);
				detail_reserv($id);
			break;
			case "detail_list":
				$id = intval($url[5]);		
				detail_list_reserv($id);
			break;
		}
	break;

	case "detail_union_tov":
		$data = isset($_POST["union"]) ? $_POST["union"] : false;
		detail_union_tov($data);
	break;

	case "add":
		add();
	break;

	case "assembly":
		switch(isset($url[4]) ? $url[4] : null){
			default;
				assembly();
			break;
			case "sum":
				assembly_sum();
			break;
			case "detail":
				$id = intval($url[5]);
				assembly_detail($id);
			break;
			case "assembly_detail_head":
				$id = intval($url[5]);
				assembly_detail_head($id);
			break;
			case "assembly_detail_body":
				$id = intval($url[5]);
				assembly_detail_body($id);
			break;
			case "assembly_del_tov":
				$id = isset($_POST["id"]) ? $_POST["id"] : false;
				assembly_del_tov($id);
			break;
			case "assembly_update_status":
				$id = isset($_POST["id"]) ? $_POST["id"] : false;
				$status = isset($_POST["status"]) ? $_POST["status"] : false;
				assembly_update_status($id, $status);
			break;
			case "assembly_cancel_tov":
				$id = isset($_POST["id"]) ? $_POST["id"] : false;
				$count = isset($_POST["count"]) ? $_POST["count"] : false;
				assembly_cancel_tov($id, $count);
			break;
			case "assembly_check_status":
				$id = isset($_POST["id"]) ? $_POST["id"] : false;
				assembly_check_status($id);
			break;
			case "assembly_final":
				$id = isset($_POST["id"]) ? $_POST["id"] : false;
				assembly_final($id);
			break;
			case "assembly_change_count":
				$id = isset($_POST["id"]) ? $_POST["id"] : false;
				$count = isset($_POST["count"]) ? $_POST["count"] : false;
				assembly_change_count($id, $count);
			break;
			case "create_excel":
				$id = isset($url[5]) ? $url[5] : false;
				assembly_create_excel($id);
			break;
			case "update_field":
				$id = isset($_POST["id"]) ? $_POST["id"] : false;
				$field = isset($_POST["field"]) ? $_POST["field"] : false;
				$value = isset($_POST["value"]) ? $_POST["value"] : false;
				update_field_assembly($id, $field, $value);
			break;
			case "update_field_main":
				$id = isset($_POST["id"]) ? $_POST["id"] : false;
				$field = isset($_POST["field"]) ? $_POST["field"] : false;
				$value = isset($_POST["value"]) ? $_POST["value"] : false;
				update_field_main_assembly($id, $field, $value);
			break;
			case "create_transfer_assembly":
				$data = $_POST;
				echo create_transfer_assembly($data);
			break;
			case "check_sales_type":
				$data = ["status" => false];
				$assembly_id = isset($url[5]) ? $url[5] : false;
				$assembly = \Storage\Model\StorageAssembly::find($assembly_id);
				if($assembly->sales->sales_type == 2){
					$data["status"] = true;
				}
				echo json_encode($data, JSON_UNESCAPED_UNICODE);
			break;
		}
	break;

	case "docPrint":
		docPrintMain();
	break;
	
	case "save":
		save();
	break;
	
	case "detail":
		$id = intval($url[4]);
		detail($id);
	break;
    
    case "addProduct":
        $id = intval($url[4]);
        addProduct($id, "https://atri-energo.ru/administration/module/storage/ajax/detail_tovar_add/", "https://atri-energo.ru/administration/module/storage/ajax/detail_list/", "#dev_list");
    break;

	case "victoria_add":
		$id = intval($url[4]);
		$victoria = new \Victoria;
		$victoria->id = intval($id);
		$victoria->url_add = "/administration/module/storage/detail_tovar_add/".$id;
		$victoria->local_script = '$victoria.transfer_to_document_update = opener.update_body;';
		echo $victoria->popup();
	break;

	case "detail_tovar_add":
		$id = intval($url[4]);
		$request = $_POST;
		$items = $request["items"];
		$stock_id = \Storage\Model\Storage::find($id)->stock_id;
		foreach($items as $item){
			$count_now = $item["count"];
			$count_stable = get_quantity_stock_transaction($item["cid"], $stock_id);
			\Storage\Model\StorageDetail::create(["cid" => $item["cid"], "storage_id" => $id, "count_stable" => $count_stable, "count_now" => $count_now]);
		}
	break;
	
	case "create_excel":
		$id = intval($url[4]);
		create_excel($id);
	break;
	
	case "transfer":
		switch(isset($url[4]) ? $url[4] : null) 
		{
			default:
				transfer();
			break;
			case "detail":
				$id = intval($url[5]);
				transfer_detail($id);
			break;
			case "detail_once":
				transfer_detail_once($_POST);
			break;
			case "head":
				$id = intval($url[5]);
				transfer_head($id);
			break;
			case "add":
				transfer_add();
			break;			
			case "save":
				transfer_save();
			break;	
			case "addProduct":
				$id = intval($url[5]);
				addProduct($id, "https://atri-energo.ru/administration/module/storage/ajax/transfer_tovar_add/", "https://atri-energo.ru/administration/module/storage/ajax/transfer_detail_list/");
			break;
			case "victoria_add":
				$id = intval($url[5]);
				$victoria = new \Victoria;
				$victoria->id = $id;
				$victoria->url_add = "/administration/module/storage/transfer/transfer_tovar_add/".$id;
				$victoria->local_script = '$victoria.transfer_to_document_update = opener.updateBody;';
				echo $victoria->popup();
			break;
			case "transfer_tovar_add":
				$id = intval($url[5]);
				$request = $_POST;
				transferTovarAdd(["transfer_id" => $id, "items" => $request["items"]]);
			break;
			case "victoria_add_min":
				$id = intval($url[5]);
				$victoria = new \Victoria;
				$victoria->transfer_to_document_disabled = true;
				$victoria->id = $id;
				$victoria->local_script = <<<HTML
					\$victoria.add_basket = async function(cid){
						const id = this.id;
						await opener.replace_transfer_item(id, cid);
						await this.add_basket_func(id);
						window.close();
					}
HTML;
		echo $victoria->popup();
			break;
			case "mergingCreate":
				$request = $_POST;
				echo mergingCreate($request);
			break;
			case "getTransferData":
				$id = intval($url[5]);
				echo \Storage\Model\StockTransfer::find($id)->toJson();
			break;
			case "transferUpdate":
				$id = intval($url[5]);
				\Storage\Model\StockTransfer::find($id)->update($_POST);
			break;
			case "getElectronicSign":
				$id = intval($url[5]);
				$data = getElectronicSignTransfer($id);
				echo json_encode($data, JSON_UNESCAPED_UNICODE);
			break;
			case "confirmElectronicSign":
				$id = intval($url[5]);
				$data = confirmElectronicSign($id, $_POST);
				echo json_encode($data, JSON_UNESCAPED_UNICODE);
			break;
			case "activeElectronicSign":
				$id = intval($url[5]);
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					activeElectronicSign($id);
			   	} else {
					activeElectronicSignTemplate($id);
				}
			break;
			case "checkActiveElectronicSign":
				$id = intval($url[5]);
				$data = checkActiveElectronicSign($id);
				echo json_encode($data, JSON_UNESCAPED_UNICODE);
			break;
			case "doc_create":
				$id = intval($url[5]);
				transfer_doc_create($id);
			break;
		}
	break;	
	
	case "transfer_add":
				transfer_add();
			break;	
		
	case "print":
		$id = intval($url[5]);
		print_storage($id, $url[4]);
	break;
	
	case "add":
		add();
	break;	
	
	case "save":
		save();
	break;
	
	case "create_excel":
	$id = intval($url[4]);
		create_excel($id);
	break;

	case "print_detail":
		$id = intval($url[4]);
		$format = isset($_GET["format"]) ? $_GET["format"] : "pdf";
		$type = isset($_GET["type"]) ? $_GET["type"] : "bill";
		$combine = isset($_GET["combine"]) ? true : false;
		print_detail($id, $format, $type, $combine);
	break;
	case "print_assembly":
		$id = intval($url[4]);
		$format = isset($_GET["format"]) ? $_GET["format"] : "pdf";
		$type = isset($_GET["type"]) ? $_GET["type"] : "bill";
		$combine = isset($_GET["combine"]) ? true : false;
		print_assembly($id, $format, $type, $combine);
	break;
	
	case "edit":
		$id = intval($url[4]);
		content_add($id);
	break;
	
	case "delete":
		$id = intval($url[4]);
		delete($id);
		header('Location: /'.ADMIN.'/module/content');
	break;
	
	case "activate":
		$id = intval($url[4]);
		activate($id);
		header('Location: /'.ADMIN.'/module/content');
	break;		
	
	case 'ajax':
		global $adminTpl, $db, $config;
			ajaxInit();
			$type = $url[4];			
			switch($type) 
			{				
				case "detail_list":
					$id = intval($url[5]);		
					ajax_detail_list($url[5]);
				break;	

				case "detail_head":
					$id = intval($url[5]);	
					detail_head($id);
				break;
				
                case "detail_tovar_add":
                    ajax_detail_tovar_add();
                break;
				
				case "transfer_detail_list":
					$id = intval($url[5]);		
					ajax_transfer_detail_list($url[5]);
				break;	
				
				
				case "transfer_tovar_add":					
					ajax_transfer_tovar_add();
				break;	
				
				case "order_detail_list":
					$id = intval($url[5]);		
					ajax_order_detail_list($url[5]);
				break;	
				
				case "count_send":		
					ajax_count_send();
				break;

				case "check_storage_status":
					$id = intval($url[5]);	
					check_storage_status($id);
				break;
				case "storage_confirm_status":
					$id = intval($url[5]);	
					storage_confirm_status($id);
				break;
				case "remove_element":
					$id = intval($url[5]);	
					remove_element($id);
				break;


				
				
				case "order_tovar_add":					
					ajax_order_tovar_add();
				break;	
				case "order_data":	
					$id = intval($url[5]);				
					ajax_order_data($id);
				break;	
				case "order_uid_edit":					
					ajax_order_uid_edit();
				break;	
				case "order_tovar_list":
					$vid = intval($url[5]);
					ajax_order_tovar_list($vid);
				break;	
				case "order_divece_compl":					
					ajax_order_device_compl();
				break;	
				case "order_divece_del":					
					ajax_order_device_del();
				break;	
				case "divece_list":
					ajax_device_list();
				break;
				case "divece_add":
					ajax_device_add();
				break;
				case "device_edit":
					ajax_device_edit();
				break;
				
			}
	break;	
	case "search_name":
		$search_name = new \Storage\SearchName;
		$search_name->init();
	break;
}
