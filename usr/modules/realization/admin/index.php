<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    header('Location: /');
    exit;
}


$dir = ROOT.'/usr/modules/realization/admin/sub/';  
includeSub($dir);

switch(isset($url[3]) ? $url[3] : null) {
	default:
		content_main();
	break;

	case "add":
		add();
	break;	

	case "get_realization_list":
		$id = intval($url[4]);
		$query = ajax_order_tovar_list_query($id);
		$data = [];
		while($r = $db->getRow($query)){
			$r["unit_name"] = get_unit('data', $r['s_unit'] ,'sname');
			$data[] = $r;
		}
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	break;
	
	case "task":
		$id = intval($url[4]);
		relations($id, true);
	break;

	case "get_data":
		$id = intval($url[4]);
		$realization = \Realization\Model\Realization::with("org_.organization")->where("id", $id)->first();
		echo empty($realization) ? json_encode([]) : $realization->toJson();
	break;
	
	case "relations":
		$id = intval($url[4]);
		relations($id, false);
	break;
	
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
		order($id);
	break;
    
    case "addProduct":
        $id = intval($url[4]);
        addProduct($id, "https://atri-energo.ru/administration/module/realization/ajax/order_tovar_add/", "https://atri-energo.ru/administration/module/realization/ajax/order_tovar_list/");
    break;
    
    case "commSave":
        $id = intval($url[4]); 
		$uid = $core->auth->user_info['id'];
		$comment = \Boot\Models\CommentsAdmin::create(["text" => $_POST['comm'], "module" => "realization", "module_id" => $id]);
        $orid = $comment->id;
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
        // header('Location: /'.ADMIN.'/module/sales/order/'.$id);
    break;
    
    case "commSaveDetail":
        $id = intval($url[4]);
        $sid = intval($url[5]);
        $db->query("UPDATE `" . DB_PREFIX . "_sales_detail` SET `comment` = '".$_POST['comment-'.$id]."' WHERE `id` = " . $id . " LIMIT 1 ;");
        $attribute = [];
        $attribute["name"] = "";
		$attribute["file"] = $_POST["file"];
		$attribute["module"] = 'sales';
		$attribute["mod"] = 'sales_comment';
		$attribute["id"] = $sid;
		$attribute["comm_id"] = $id;
		uploaderMaster("download", $attribute);
        // header('Location: /'.ADMIN.'/module/sales/order/'.$sid);
    break;
	
	case "doc_create":
		doc_create();		
	break;	
	
	case "send_order":
		$id = intval($url[4]);
		send_order($id);		
	break;
    
    case "actualize":
		$id = intval($url[4]);
		actualize($id);		
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
				case "order_divece_compl":					
					ajax_order_device_compl();
				break;	
				case "order_divece_del":					
					ajax_order_device_del();
				break;	
				case "divece_list":
					ajax_device_list();
				break;
				case "order_oid_edit":
					$id = isset($_POST["id"]) ? $_POST["id"] : false;
					$oid = isset($_POST["oid"]) ? $_POST["oid"] : false;
					if($id && $oid){
						$db->query("UPDATE JMY_realization SET oid = ".$oid." WHERE id = ".$id);
					}
				break;
				case "divece_add":
					ajax_device_add();
				break;
				case "device_edit":
					ajax_device_edit();
				break;
				case "comments_get":
				$id = $url[5];
					$query = $db->query("SELECT c.*, u.surname as surname, u.name as name, u.ochestvo as ochestvo FROM `" . DB_PREFIX . "_comments_admin` as c LEFT JOIN `" . DB_PREFIX . "_users` as u ON(c.uid = u.id) WHERE c.module = 'realization' AND c.module_id = '".$id."'");
					
                    
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

	case "correctRealization":
		$id = isset($url[4]) ? $url[4] : false;
		$checks = isset($_POST["checks"]) ? $_POST["checks"] : [];
		echo correctRealization($id, ["realization_detail_id" => $checks]);
	break;
	case "correctCorrect":
		$id = isset($url[4]) ? $url[4] : false;
		echo correctCorrect($id);
	break;

	case "correct":
		switch($url[4] ?? null){
			default:
				correct_main();
			break;
			case "change_x":
				$request = $_POST;
				\Realization\Model\Correct::findOrFail($request["pk"])->update([$request["name"] => $request["value"]]);
			break;
			case "file_add":
				$id = $url[5] ?? false;
				$file = $_POST["file"] ?? false;
				$attribute = [];
        		$attribute["name"] = "";
        		$attribute["file"] = $file;
        		$attribute["module"] = 'correct';
        		$attribute["mod"] = 'correct';
        		$attribute["id"] = $id;
        		$attribute["comm_id"] = $id;
        		uploaderMaster("download", $attribute);
			break;
		}
	break;
	case "correct_detail":
		$id = isset($url[4]) ? $url[4] : false;
		correct_detail($id);
	break;
	case "correct_detail_header":
		$id = isset($url[4]) ? $url[4] : false;
		correct_detail_header($id);
	break;
	case "correct_detail_body":
		$id = isset($url[4]) ? $url[4] : false;
		correct_detail_body($id);
	break;
	case "correct_save":
		$id = isset($_POST["id"]) ? $_POST["id"] : false;
		$data = isset($_POST["data"]) ? $_POST["data"] : false;
		correct_save($id, $data);
	break;
	case "correct_check_minus":
		$id = isset($url[4]) ? $url[4] : false;
		correct_check_minus($id);
	break;
	case "correct_end":
		$id = isset($url[4]) ? $url[4] : false;
		correct_end($id);
	break;
	case "correct_start":
		$id = isset($url[4]) ? $url[4] : false;
		correct_start($id);
	break;
	case "correct_print":
		$id = isset($url[4]) ? $url[4] : false;
		correct_print($id);
	break;
	case "correct_check_delete":
		$id = isset($url[4]) ? $url[4] : false;
		$data = \Realization\Support\CorrectDetailSupport::check_delete($id);
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	break;
	case "correct_delete":
		$id = isset($url[4]) ? $url[4] : false;
		$data = \Realization\Support\CorrectDetailSupport::check_delete($id);
		if(!$data["error"]){
			\Realization\Model\CorrectDetail::destroy($id);
		}
	break;


	case "checkDelItem":
		$request = $_POST;
		$result = ["status" => true, "error" => []];
		$id = $request["id"] ?? false;
		if($id){
			$realization_detail = \Realization\Model\RealizationDetail::with("realization")->where("id", $id)->first();
			if($realization_detail->realization->active == 1){
				$result["status"] = false;
				$result["error"][] = "Прежде чем удалить позицию, распроведите реализацию!";
			}

			$correct_detail = \Realization\Model\CorrectDetail::with("correct")->where("realization_detail_id", $id)->first();
			if($correct_detail){
				$result["status"] = false;
				$result["error"][] = "Прежде чем удалить позицию, удалите корректировку ({$correct_detail->correct->numb})!";
			}
		} else {
			throw new \Exception("Empty `id`!");
		}
		echo json_encode($result, JSON_UNESCAPED_UNICODE);
	break;


	case "get_resale_stock":
		$request = $_POST;
		$org = $request["org"];
		echo \Stocks\Models\Stock::where("org", $org)->get()->toJson();
	break;
	case "check_resale":
		$request = $_POST;
		try {
			$resale = new \Realization\Resale($request["realization_id"], $request["realization_detail_id"], $request["resale_org"], $request["resale_stock_id"]);
			$resale->check_data();
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}
		echo json_encode(["error" => $error ?? null], JSON_UNESCAPED_UNICODE);
	break;
	case "resale":
		$request = $_POST;
		$resale = new \Realization\Resale($request["realization_id"], $request["realization_detail_id"], $request["resale_org"], $request["resale_stock_id"]);
		if(!$resale->start()){
			$error = $resale->get_error();
			dd($resale, $error);
		}
		echo json_encode([
			"error" => $error ?? null,
		], JSON_UNESCAPED_UNICODE);
	break;
}
