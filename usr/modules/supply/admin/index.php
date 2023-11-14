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

$dir = ROOT.'/usr/modules/supply/admin/sub/';  
includeSub($dir);

switch(isset($url[3]) ? $url[3] : null) {
	default:
		content_main();
	break;	

	case "check_can_delete":
		$request = $_POST;
		$status = true;
		$data = [];
		$id = $request["id"];
		switch($request["module"]){
			case "supply":
				$class = \Supply\Model\SupplyDetail::class;
			break;
			case "supply_order":
				$class = \Supply\Model\SupplyOrderDetail::class;
			break;
		}
		echo check_can_delete_module($id, $class);
	break;

	case "check_can_change":
		$request = $_POST;
		$supply_order_detail = \Supply\Model\SupplyOrderDetail::with("supply_detail")->where("id", $request["id"])->first();
		$data = [
			"status" => true, 
			"data" => [],
		];
		if(!empty($supply_order_detail->supply_detail)){
			$data["status"] = false;
			$data["data"] = [
				"error" => "Позиция имеет связь. Для продолжения удалите позицию из заявки на закупку!", 
				"link" => "/administration/module/supply/detail/{$supply_order_detail->supply_detail->sid}",
			];
		}
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	break;

	case "detach_from_sale":
		$request = $_POST;
		$id = $request["id"];
		$supply_order_detail = \Supply\Model\SupplyOrderDetail::findOrFail($id);

		if(!empty($supply_order_detail->supply_d)){
			$supply_detail = \Supply\Model\SupplyDetail::with("supply")->where("id", $supply_order_detail->supply_d)->first();
			if(!empty($supply_detail->supply)){
				$supply_detail->supply->update(["status" => 1]);
			}
		}

		$status = 24;
		\Sales\Model\SalesDetail::findOrFail($supply_order_detail->sale_detail_id)->update(["status" => $status]);
		\Sales\Model\SalesDetailStatusLog::create([
			"sales_detail_id" => $supply_order_detail->sale_detail_id, 
			"status" => $status, 
			"url" => "/administration/module/supply/order_detail/".$supply_order_detail->supply_o,
		]);

		$supply_order_detail->update(["sale_detail_id" => null, "supply_d" => null]);
		echo json_encode([
			"supply_order_detail" => $supply_order_detail,
			"supply_detail" => $supply_detail,
		], JSON_UNESCAPED_UNICODE);
	break;
	
    case "twomain":
		twomain();
	break;

	case "delete_item_twodetail":
		$request = $_POST;
		$id = $request["id"];
		$supply = \Supply\Model\SupplyDetail::findOrFail($id);
		$supply->delete();
		echo $supply->toJson();
	break;

	case "close_supply":
		$id = intval($url[4]);
		$supply = \Supply\Model\Supply::findOrFail($id);
		$supply->status = 2;
		$supply->save();
		echo $supply->toJson();
	break;

	case "logistics_receipts":
		\Supply\LogisticsReceiptsRouter::route();
	break;

	case "twodetail":
        $id = intval($url[4]);
		twodetail($id);
	break;

	case "auto_create_supply":
		$data = $_POST;
		auto_create_supply($data);
	break;

	case "supply_order_modal_view":
		$id = intval($url[4]);
		echo supply_order_modal_view($id);
	break;
	case "supply_order_modal_delete":
		$id = intval($url[4]);
		echo supply_order_modal_delete($id);
	break;

	case "twodetail_auto_create_supply_check":
		$data = $_POST;
		echo twodetail_auto_create_supply_check($data);
	break;

	case "twodetail_update_field":
		$id = isset($_POST["id"]) ? $_POST["id"] : false;
		$field = isset($_POST["field"]) ? $_POST["field"] : false;
		$value = isset($_POST["value"]) ? $_POST["value"] : false;
		twodetail_update_field_detail($id, $field, $value);
	break;

	case "logistics_fix":
		// $query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_order` WHERE `control_send` IS NOT NULL AND `control` IN(1, 2) AND  `control_send` <= '".date("Y-m-d", strtotime("-2 days"))."'");
		// $query = $db->query("SELECT so.*, c.date as c_date, c.status as c_status FROM `".DB_PREFIX."_supply_order` as so LEFT JOIN JMY_control as c ON(so.id = c.supply_o) WHERE so.control IN(1, 2) AND control_send IS NULL AND c.date IS NOT NULL AND c.status IN(1, 2)");
		
		// dd($db->numRows($query));
		// while($r = $db->getRow($query)){
		// 	dd($r);
		// 	$date = date("Y-m-d", strtotime($r["c_date"]));
		// 	$db->query("UPDATE `".DB_PREFIX."_supply_order` SET control_send = '".$date."' WHERE id = '".$r["id"]."' LIMIT 1");
		// }
	break;

	case "prohibited":
		prohibited();
	break;

	case "update_supply_order":
		$id = isset($_POST["id"]) ? $_POST["id"] : false;
		$field = isset($_POST["field"]) ? $_POST["field"] : false;
		$value = isset($_POST["value"]) ? $_POST["value"] : null; 
		$request = [];
		$request[ $field ] = $value;
		\Supply\Model\SupplyOrder::findOrFail($id)->update([$field => $value]);
	break;
	case "update_supply_order_detail":
		$id = isset($_POST["id"]) ? $_POST["id"] : false;
		$field = isset($_POST["field"]) ? $_POST["field"] : false;
		$value = isset($_POST["value"]) ? $_POST["value"] : null; 
		$request = [];
		$request[ $field ] = $value;
		\Supply\Model\SupplyOrderDetail::find($id)->update($request);
	break;

	case "order_detail_head":
		$id = isset($url[4]) ? $url[4] : false;
		$logistic = isset($_POST["logistic"]) ? $_POST["logistic"] : false;
		$logistic = $logistic == "true" ? true : false;
		echo order_detail_head($id, $logistic);
	break;

	/* SUPPLY STOCK */

	case "supply_stock":
		supply_stock_main();
	break;

	case "supply_stock_detail":
		$id = isset($url[4]) ? $url[4] : false;
		supply_stock_detail($id);
	break;

	case "supply_stock_detail_list":
		$id = isset($url[4]) ? $url[4] : false;
		supply_stock_detail_list($id);
	break;

	case "ajax_order_send_stock":
		ajax_order_send_stock();
	break;

	case "ajax_order_send_mas_stock":
		ajax_order_send_mas_stock();
	break;

	/* SUPPLY STOCK END */
    
	case "order":
		order();
	break;		

	case "order_delete":
		$id = intval($url[4]);
		\Supply\Model\SupplyOrder::find($id)->delete();
	break;

	case "search_name":
		$search_name = new \Supply\SearchName;
		$search_name->init();
	break;
    
    case "commSave":
        $id = intval($url[4]);
        $sid = intval($url[5]);
        $db->query("UPDATE `" . DB_PREFIX . "_sales` SET `comment` = '".$_POST['comm']."' WHERE `id` = " . $id . " LIMIT 1 ;");
        header('Location: /'.ADMIN.'/module/supply/detail/'.$sid);
    break;
	
	case "order_detail":
	$id = intval($url[4]);
		order_detail($id);
	break;	

	case "order_detail_load_file":
		$id = intval($url[4]);
		order_detail_load_file($id);
	break;
    
    case "logistics":
        logistics();
    break;
    
    case "logistics_detail":
		$id = intval($url[4]);
        order_detail($id, true);
	break;	
    
    case "getCatProduct":
        if(isset($_POST["id"], $_POST["parentId"], $_POST["num"], $_POST["count"], $_POST["supply_d"])){
            $id = $_POST["id"];
            $parentId = $_POST["parentId"];
            $num = $_POST["num"];
            $count = $_POST["count"];
			$supply_d = $_POST["supply_d"];

			$content = \Catalog_v2\Model\Catalog::findOrFail($id)->toArray();
			$sdd = \Supply\Model\SupplyOrderDetail::find($parentId)->toArray();

			$order = get_query_result_so($sdd["supply_o"]);

			$so = \Supply\Model\SupplyOrder::find($sdd["supply_o"])->toArray();

			$content["i_pos"] = $num;
			$content["soid"] = $parentId;
			$content["count"] = $count;
			$content["so_unit"] = $content["unit"];
			$content["s_sku"] = $content["sku"];
			$content["s_name"] = $content["title"];
			$content["count_receipts"] = 0;
			$content["price_now"] = 0;
			$content["sototal"] = 0;
			$content["nds"] = 0;
			$content["type_nds"] = 3;
			$content["supply_d"] = $supply_d;
			$content["org"] = $order["org"];
			$content["nds_includes"] = $so["nds_includes"];
			
			template_order($content);
        }
    break;
		
	case "script_object":
        $query = $db->query("SELECT * FROM JMY_supply_order WHERE sales_id IS NOT NULL");
        while($r = $db->getRow($query)){
            $qq = $db->query("SELECT * FROM JMY_sales WHERE id = ".$r["sales_id"]);    
			$sales = $db->getRow($qq);
			$object = $sales["object"];
			if (empty($sales["object"]))
			{
				$object = 0;
			}
            $db->query("UPDATE JMY_supply_order SET object = '".$object."' WHERE id = ".$r["id"]);
        }
    break;
    
    case "updateProducts":
        if(isset($_POST["json"])){
            $json = $_POST["json"];
            $json = json_decode($json, true);
            
            foreach($json as $key => $val){
                $db->query("UPDATE JMY_supply_order_detail SET cid = '".$val."' WHERE id = '".$key."'");
            }
        }
    break;
    
    case "receipts":
        receipts();
	break;

	case "automatic_receipts":
		// $receipt_id = intval($url[4]);
		// $automatic_receipts = new \Supply\AutomaticReceipts($receipt_id);
		// $automatic_receipts->automatic();
	break;
	
	case "receipts_print_docx":
		$id = intval($url[4]);
		receipts_print($id, "docx");
	break;

	case "receipts_print_pdf":
		$id = intval($url[4]);
		receipts_print($id, "pdf");
	break;
    
    case "receipts_detail":
        $id = intval($url[4]);
        receipts_detail($id);
    break;
	
	case "detail":
		$id = intval($url[4]);
		detail($id, true);
	break;
	
	case "add":
		add();
	break;	
    
    case "addProduct":
        $id = intval($url[4]);
        addProduct($id, "/administration/module/supply/ajax/order_tovar_add/", "/administration/module/supply/ajax/order_detail_list/");
	break;

	case "victoria_add":
		$id = intval($url[4]);
		$victoria = new \Victoria;
		$victoria->id = $id;
		$victoria->url_add = "/administration/module/supply/ajax/victoria_order_tovar_add/{$id}";
		// $victoria->url_update = "/administration/module/sales/ajax/order_tovar_list/".$id;
		$victoria->local_script = '$victoria.transfer_to_document_update = opener.update_body;';
		echo $victoria->popup();
	break;
	
	case "change_goods_order":
		$id = intval($url[4]);
		addProduct($id, "", "", "#tov_list", 2, "supply_order");
	break;
	
	case "victoria_add_min":
		$id = intval($url[4]);
		$victoria = new \Victoria;
		$victoria->transfer_to_document_disabled = true;
		$victoria->id = $id;
		$victoria->local_script = <<<HTML
			\$victoria.add_basket = async function(cid){
				const id = this.id;
				await opener.change_goods(cid, id);
				window.close();
			}
HTML;
		echo $victoria->popup();
	break;

	case "save":
		save();
	break;
    
    case "saveLogistics":
		save(true);
	break;
	
	case "create_excel":
		$id = intval($url[4]);
		create_excel($id);
	break;

	case "create_excel2":
		$id = intval($url[4]);
		create_excel2($id);
	break;
	
	case "create_excel_detail":
	$id = intval($url[4]);
		create_excel_detail($id);
	break;
	
	case "edit":
    $id = intval($url[4]);
		add($id);
	break;

	case "end_manager":
		$id = intval($url[4]);
		supply_end_manager($id);
	break;
	
	case "send":
    	$id = intval($url[4]);
		send_fcontrol($id);
	break;
    
    case "editLogistics":
    $id = intval($url[4]);
		add($id, true);
	break;
    
	case "confirm_receipt":
		$id = intval($url[4]);
		echo confirm_receipt($id, $_POST);
    break;
    
    case "checkGoods":
		echo checkGoodsSupply($_POST);
    break;
    
    /*
    case "editPrice":
        if(isset($_POST["id"], $_POST["price"])){
            $id = $_POST["id"];
            $price_now = $_POST["price"];		
            $db->query("UPDATE JMY_supply_order_detail SET price_now = '".$price_now."' WHERE id = '".$id."'");
            $query = $db->query("SELECT * FROM JMY_supply_order_detail WHERE id = '".$id."' LIMIT 1");
            //обновление суммы
            $res = $db->getRow($query);
            $query_order = $db->query("SELECT * FROM JMY_supply_order_detail WHERE supply_o = '".$res['supply_o']."'");
            $sum = 0;
            while($r = $db->getRow($query_order))
            {
                $sum = $sum + $r['price_now'] * $r['count'];           
            }
            $db->query("UPDATE `" . DB_PREFIX . "_supply_order` SET `total` = '".$sum."' WHERE `id` = " . $res['supply_o'] . " LIMIT 1 ;");
		}
    break;
	*/
    
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

	case "action":
		$type = $_POST['act'];
		if(is_array($_POST['checks'])) {
			switch($type) {
				case "activate":
					foreach($_POST['checks'] as $id) {
						activate(intval($id));
					}
					break;			
				
				case "deActivate":
					foreach($_POST['checks'] as $id) {
						$db->query("UPDATE `" . DB_PREFIX . "_content` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
					}
					break;			
					
				case "reActivate":
					foreach($_POST['checks'] as $id) {
						$db->query("UPDATE `" . DB_PREFIX . "_content` SET `active` = NOT `active` WHERE `id` = " . $id . " LIMIT 1 ;");
					}
					break;
				
				case "delete":
					foreach($_POST['checks'] as $id) {
						delete(intval($id));
					}
					break;
			}
		}
		header('Location: /'.ADMIN.'/module/verification');
	break;
	
	case 'ajax':
		global $adminTpl, $db, $config;
			ajaxInit();
			$type = $url[4];			
			switch($type) 
			{				
				case "detail_head":
					$id = intval($url[5]);		
					ajax_detail_head($id);
				break;	
				case "detail_list":
					$id = intval($url[5]);		
					ajax_detail_list($id);
				break;	
				
				case "order_detail_list":
					$id = intval($url[5]);
					$request = isset($_POST) ? $_POST : [];
					ajax_order_detail_list($id, false, $request);
				break;	
                
                case "receipts_detail_list":
					$id = intval($url[5]);		
					ajax_receipts_detail_list($id);
				break;	

                case "receipts_head":
					$id = intval($url[5]);		
					ajax_receipts_head($id);
				break;	
				
				case "order_send":		
					ajax_order_send();
				break;
				case "order_send_mas":		
					ajax_order_send_mas();
				break;

				case "order_tovar_add":					
					ajax_order_tovar_add();
				break;	
				case "victoria_order_tovar_add":
					$request = $_POST;
					if(isset($request["id"], $request["items"]) && is_array($request["items"])){
						$id = $request["id"];
						$items = $request["items"];
						$data = ["supply_o" => $id, "items" => $items];
						// dd($data);
						supply_order_tovar_add($data);
					} else {
						echo "Товары не выбраны";
					}
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
					$request = $_POST;
					$id = $request["id"];
					$data = \Supply\Support\SupplyOrderDetailSupport::c_delete($id);
					echo json_encode($data, JSON_UNESCAPED_UNICODE);
					// ajax_order_device_del();
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
	
    case "save_date":	
					$cid = intval($url[4]);
                    $id = intval($url[5]);
                    $sdid = intval($url[6]);
                    $db->query("UPDATE `" . DB_PREFIX . "_supply_detail` SET `date_post` = '".$_POST['date_post']."', `status` = 2 WHERE `id` = " . $cid . " LIMIT 1 ;");
                    $db->query("UPDATE `" . DB_PREFIX . "_sales_detail` SET `date_post` = '".$_POST['date_post']."' WHERE `id` = " . $sdid . " LIMIT 1 ;");
                    
                    $query = $db->query('SELECT status FROM `' . DB_PREFIX . '_supply_detail` WHERE `sid` = '.$id);
                    $flag = true;
                    while($val = $db->getRow($query)){
                        if($val['status'] == 1) $flag = false;
                    }
                    if($flag) {
                    $db->query("UPDATE `" . DB_PREFIX . "_supply` SET `status` = 2 WHERE `id` = " . $id . " LIMIT 1 ;");
					
					$id_sales = $db->getRow($db->query("SELECT sales_id FROM `" . DB_PREFIX . "_sales_detail` WHERE `id` = " . $sdid . " LIMIT 1 "));
					//$db->query("UPDATE ".DB_PREFIX."_sales SET `status` = 3 WHERE `id` = '".$id_sales."' LIMIT 1");
					
                    $query2 = $db->query('SELECT * FROM `' . DB_PREFIX . '_supply` WHERE `id` = '.$id.' LIMIT 1');
                    $val = $db->getRow($query2);
                        $attr = [];
						$attr['uid'] = $val['uid'];
						$attr['title'] = 'Данные по заявке актуализированы!';
						$attr['text'] = 'Данные по заявке актуализированы, нажмите чтобы посмотреть!';
						$attr['icon'] = config('app.url').'/files/push/new_order.png';
						$attr['url'] = config('app.url').'/administration/module/sales/order/'.$val['sales_id'];
						$attr['pNotif'] = true;
						$attr['pPush'] = true;
						$attr['module'] = "sales";
            			$attr['pid'] = $val["sales_id"];
						sendAllNotif($attr);
                    }
					header('Location: /'.ADMIN.'/module/supply/twodetail/'.$id);
	break;
    
    case 'two_ajax':
		global $adminTpl, $db, $config;
			ajaxInit();
			$type = $url[4];			
			switch($type) 
			{				
				case "detail_head":
					$id = intval($url[5]);		
					two_ajax_detail_head($url[5]);
				break;	
				case "detail_list":
					$id = intval($url[5]);		
					two_ajax_detail_list($url[5]);
				break;	
                
                case "order_data":	
					$id = intval($url[5]);				
					two_ajax_order_data($id);
				break;
                
                case "save_date":	
					$cid = intval($url[5]);
                    $id = intval($url[6]);
                    $db->query("UPDATE `" . DB_PREFIX . "_supply_detail` SET `date_post` = ".$_POST['date_post']." WHERE `id` = " . $cid . " LIMIT 1 ;");
					header('Location: /'.ADMIN.'/module/supply/twodetail/'.$id);
				break;
			}
	break;	

}
