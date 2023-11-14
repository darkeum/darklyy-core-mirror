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

$dir = ROOT.'/usr/modules/sales/admin/sub/';  
includeSub($dir);

function redirect_new_sales(){
	// return false;
	$uid = \Boot\Support\Facades\Auth::id();
	$uid_allow = [1,17];
	return !in_array($uid, $uid_allow);
	// return $uid == 120 || $uid == 48;
}

switch(isset($url[3]) ? $url[3] : null) {
	default:
		if(redirect_new_sales()){
			header("Location: /administration/module/sales2");
		} else {
			content_main();
		}
	break;

	case "contract_test":
		$request = ["sales_id" => 19664, "reestr_id" => 1973];
		$document = contract_create($request);
		$name = time().'.docx';
		$file_url= 'files/sales/'.$name;
		$document->save(ROOT.$file_url);
	break;

	case "middleware_end_sales":
		echo json_encode(\Sales\Support\SalesSupport::end_sales_middleware($_POST["sales_id"]), JSON_UNESCAPED_UNICODE);
	break;

	case "check_can_delete":
		$request = $_POST;
		$status = true;
		$data = [];
		$id = $request["id"];
		echo check_can_delete_module($id, \Sales\Model\SalesDetail::class);
	break;

	case "site":
		grid_site();
	break;

	case "add":
		if(redirect_new_sales()){
			header("Location: /administration/module/sales2/create");
		} else {
			add();
		}
	break;	

	case "ttt":
		// $ftp = new StorageFTP;
		// $ftp->remote_file = "storage/test/test2.txt";
		// $ftp->local_file = "files/test.txt";
		// $ftp->get_file_id(1);
		// $ftp->get();
		// $ftp->put();
		// $ext = pathinfo("storage/test/test2.txt");
		// dd($ext);
		// $request = ["file" => ["tmp_name" => "files/test.txt", "name" => "test2.txt"], "id" => "1", "module" => "test", "mod" => "test"];
		// $ftp->put_file_attach($request);
	break;

	case "sales_users_list":
		echo \Sales\Model\SalesUsers::with("user")->where("sales_id", $url[4])->get()->toJson();
	break;

	case "sales_users_list_add":
		echo \Sales\Model\SalesUsers::firstOrCreate($_POST)->toJson();
	break;

	case "sales_users_list_delete":
		\Sales\Model\SalesUsers::find($url[4])->delete();
	break;

	case "check_documents_held":
		$sales_id = isset($url[4]) ? $url[4] : false;
		$result = ["check" => true];
		if(\Realization\Model\Realization::where("sales_id", $sales_id)->where("active", 1)->exists()){
			$result["check"] = false;
		}
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	break;

	case "smart_reserve":
		echo json_encode(\Sales\Support\SalesSupport::smart_reserve($url[4]), JSON_UNESCAPED_UNICODE);
	break;

	case "get_reserve":
		echo json_encode(\Sales\Support\SalesSupport::get_reserve($_POST), JSON_UNESCAPED_UNICODE);
	break;

	case "get_count_assembly":
		echo json_encode(get_count_assembly_sales($url[4]), JSON_UNESCAPED_UNICODE);
	break;

	case "extendedSort":
		$id = isset($url[4]) ? $url[4] : false;
		if($id){
			$request = $_POST;
			$request["data"] = \Sales\Model\SalesDetail::where("sales_id", $id)->get()->toArray();
			$extendedSort = new \ExtendedSort;
			$data = $extendedSort->sort($request);
			$update = ["position" => 1];
			foreach($data as $item){
				\Sales\Model\SalesDetail::where("id", $item["id"])->update($update);
				$update["position"]++;
			}
		}
	break;

	case "fast_task":
		echo json_encode(\Sales\FastTask::create($_POST), JSON_UNESCAPED_UNICODE);
	break;

	case "get_fast_task_data":
		echo json_encode(\Sales\FastTask::get($url[4]), JSON_UNESCAPED_UNICODE);
	break;
	case "get_fast_task_data_all":
		echo json_encode(\Sales\FastTask::all($url[4]), JSON_UNESCAPED_UNICODE);
	break;
	case "fast_task_end":
		$request = $_POST;
		$task = \Task\Model\Task::find($request["task_id"]);
		$task->update(["status" => 3]);
		\Task\Model\TaskComments::create(["tid" => $task->id, "comment" => $request["end_message"] ?? "", "cat" => 3]);
		$sales = \Sales\Model\Sales::find($request["sales_id"]);
		$sales->task_id = $sales->task_title = $sales->task_date_end = null;
		$sales->save();
		echo $task->toJson();
	break;
	case "prohibited":
		prohibited();
	break;
	
	case "task":
		$id = intval($url[4]);
		relations($id, true);
	break;
	
	case "relations":
		$id = intval($url[4]);
		relations($id, false);
	break;
	
	case "test_s":
		dd(get_manufacture_price([
			"cid" => 92311,
			"manufacture_price" => 1308461.35,
			"price_type" => 2,
		]));
	break;

	case "get_discount_oid":
		echo json_encode(get_discount_oid_sales($_POST), JSON_UNESCAPED_UNICODE);
	break;

	case "load_max_discount":
		echo json_encode(load_max_discount_sales($url[4]), JSON_UNESCAPED_UNICODE);
	break;

	case "check_reserv_uid":
		$result = check_reserv_uid($_POST);
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	break;

	case "automatic_sales":
		$sales_id = intval($url[4]);
		$post = $_POST;
		$request = [];
		$request["sales_detail_id"] = isset($post["checks"]) ? $post["checks"] : [];
		$automatic_sales = new \Sales\AutomaticSales($sales_id, $request);
		$error = $automatic_sales->automatic();
		echo json_encode($error, JSON_UNESCAPED_UNICODE);
	break;

	/*
	case "script":
		$sales = $db->query("SELECT * FROM JMY_sales WHERE fix = 0");
		while($r1 = $db->getRow($sales)){
			$salesDetail = $db->query("SELECT * FROM JMY_sales_detail WHERE sales_id = ".$r1["id"]);
			$total = 0;
			$nds = 0;
			while($r = $db->getRow($salesDetail)){
				$total += $r["total"];
				$nds += $r["nds"];
			}
			$db->query("UPDATE JMY_sales SET total = '".$total."', nds = '".$nds."', fix = 1 WHERE id = ".$r1["id"]);
		}
	break;
	*/
	
	case "relations_add":
        $id = intval($url[4]);
		relations_add($id);
	break;
	
	case "relations_save":
		$id = intval($url[4]);
		relations_save($id);
	break;
		
	case "order":		
		$id = intval($url[4]);
		if(redirect_new_sales()){
			if(isset($_GET["supply_o_tov"])){
				$url = "/administration/module/sales2/{$id}?";
				foreach ($_GET["supply_o_tov"] as $key => $value) {
					$url .= 'supply_o_tov[]='.$value;
					if(isset($value[$key+1])){
						$url .= '&';
					}
				}
				header("Location: {$url}");
			} else {
				header("Location: /administration/module/sales2/{$id}");
			}
			
		} else {
			order($id);
		}
	break;
    
    case "addProduct":
        $id = intval($url[4]);
        addProduct($id, "https://atri-energo.ru/administration/module/sales/ajax/order_tovar_add/", "https://atri-energo.ru/administration/module/sales/ajax/order_tovar_list/");
	break;
	
	case "addProductMin":
		$id = intval($url[4]);
		addProduct($id, "", "", "#tov_list", "2", "sales");
	break;

	case "victoria_add":
		$id = intval($url[4]);
		$victoria = new \Victoria;
		$victoria->id = $id;
		$victoria->url_add = "/administration/module/sales/victoria_order_tovar_add/".$id;
		// $victoria->url_update = "/administration/module/sales/ajax/order_tovar_list/".$id;
		$victoria->local_script = '$victoria.transfer_to_document_update = opener.updateBody;';
		echo $victoria->popup();
	break;

	case "victoria_add_min":
		$id = intval($url[4]);
		$victoria = new \Victoria;
		$victoria->transfer_to_document_disabled = true;
		$victoria->id = $id;
		// $victoria->url_add = "/administration/module/sales/victoria_order_tovar_add/".$id;
		// $victoria->url_update = "/administration/module/sales/ajax/order_tovar_list/".$id;
		$victoria->local_script = <<<HTML
			\$victoria.add_basket = async function(cid){
				const id = this.id;
				await opener.replace_sales_item(id, cid);
				await this.add_basket_func(id);
				this.close();
			}
HTML;
		echo $victoria->popup();
	break;
    
    case "commSave":
        $id = intval($url[4]); 
		$comm = isset($_POST['comm']) ? $_POST['comm'] : "";
		$comments_admin = \Boot\Models\CommentsAdmin::create([
			"text" => $comm,
			"module" => "sales",
			"module_id" => $id,
		]);
        $orid = $comments_admin->id;
		if(isset($_POST["file"])){
			$attribute = [];
			$attribute["name"] = "";
			$attribute["file"] = $_POST["file"];
			$attribute["module"] = 'sales';
			$attribute["mod"] = 'sales';
			$attribute["id"] = $id;
			$attribute["comm_id"] = $orid;
			uploaderMaster("download", $attribute);
		}
        
        header('Location: /'.ADMIN.'/module/sales/order/'.$id);
	break;
	
	case "search_name":
		$search_name = new \Sales\SearchName;
		$search_name->init();
	break;

	case "union_tov":
		$data = isset($_POST["union"]) ? $_POST["union"] : false;
		union_tov_sales($data);
	break;	
    
    case "commSaveDetail":
        $id = intval($url[4]);
		$sid = intval($url[5]);
		$comment = isset($_POST['comment']) ? "'".$_POST['comment']."'" : 'null';
		$db->query("UPDATE `" . DB_PREFIX . "_sales_detail` SET `comment` = ".$comment." WHERE `id` = " . $id . " LIMIT 1 ;");
		$file = isset($_POST["file"]) ? $_POST["file"] : false;
        $attribute = [];
        $attribute["name"] = "1";
		$attribute["file"] = $file;
		$attribute["module"] = 'sales';
		$attribute["mod"] = 'sales_comment';
		$attribute["id"] = $sid;
		$attribute["comm_id"] = $id;
		uploaderMaster("download", $attribute);
        //header('Location: /'.ADMIN.'/module/sales/order/'.$sid);
    break;
	
	case "doc_create":
		doc_create();		
	break;	

	case "get_contract":
		get_contract($_POST);
	break;

	case "get_spec":
		get_spec($_POST);
	break;
	
	case "send_order":
		$id = intval($url[4]);
		send_order($id);		
	break;

	case "send_order2":
		$id = intval($url[4]);
		send_order2($id);		
	break;
	
	case "send_transfer":
		$id = intval($url[4]);
		send_transfer($id);		
	break;
    
    case "actualize":
		$id = intval($url[4]);
		actualize($id);		
	break;

    case "actualize_check":
		$id = intval($url[4]);
		actualize_check($id);		
	break;

    case "actualize_supply":
		$id = intval($url[4]);
		actualize_supply($id);		
	break;
	
	case "device":
		content_device();
	break;
	
	case "save":
		save();
	break;
	
	case "print":
		$id = intval($url[5]);
		print_sales($id, $url[4]);		
	break;
	break;
	
	case "edit":
		$id = intval($url[4]);
		content_add($id);
	break;
	
	case "order_uid_set":
		$id = intval($url[4]);
		order_uid_set($id);
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

	case "victoria_order_tovar_add":
		victoria_order_tovar_add($_POST);
	break;
	
	case 'ajax':
		global $adminTpl, $db, $config;
			ajaxInit();
			$type = $url[4];			
			switch($type) 
			{				
				case "order_list":
					ajax_order_list();
				break;	
				case "order_tovar_add":					
					ajax_order_tovar_add();
				break;	
				case "order_data":	
					$id = intval($url[5]);				
					ajax_order_data($id);
				break;	
				case "order_btn":	
					$id = intval($url[5]);				
					ajax_order_btn($id);
				break;	
				case "order_table_head":	
					$id = intval($url[5]);				
					ajax_order_table_head($id);
				break;	
				case "order_uid_edit":					
					ajax_order_uid_edit();
				break;	
				case "order_object_edit":					
					ajax_order_object_edit();
				break;	
				case "order_tovar_list":
					$vid = intval($url[5]);
					ajax_order_tovar_list($vid);
				break;	
				case "order_tovar_list_min":
					$sales = 1;
					$idRow = intval($url[5]);
					$changeArray = [
						"num" => $_POST["num"],
						"cid" => $_POST["cid"],
						"count" => $_POST["count"],
						"discount" => $_POST["discount"],
					];
					ajax_order_tovar_list($sales, $idRow, $changeArray);
				break;	
				// case "order_divece_compl":					
				// 	ajax_order_device_compl();
				// break;	
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
				case "comments_get":
				$id = $url[5];
					$query = $db->query("SELECT c.*, u.surname as surname, u.name as name, u.ochestvo as ochestvo FROM `" . DB_PREFIX . "_comments_admin` as c LEFT JOIN `" . DB_PREFIX . "_users` as u ON(c.uid = u.id) WHERE c.module = 'sales' AND c.module_id = '".$id."'");
					
                    
													while($r = $db->getRow($query))
													{
                                                        $queryTC = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE pid = '".$r['id']."' AND `module` = 'sales'");
														echo '<div class="panel br-a" style="padding:10px">
														<div class="form-group">
															<label>'.$r['date'].' '.$r['surname'].' '.mb_substr($r['name'], 0, 1).'. '.mb_substr($r['ochestvo'], 0, 1).'.'.'</label>
														</div>
														<div class="form-group">'.$r['text'].'</div>';
                                                        if($db->numRows($queryTC) > 0){
                                                            while($myr = $db->getRow($queryTC)){
                                                            echo '<a target="_blank" href="/download/'.$myr['id'].'">'.$myr['name'].'</a><br>';
                                                            }
                                                        }
														echo '</div>';
													}
				break;
			}
	break;	
}