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


$dir = ROOT.'/usr/modules/selection/admin/sub/';  
includeSub($dir);

global $adminTpl, $db, $config;

switch(isset($url[3]) ? $url[3] : null) {
	default:
		content_main();
	break;
	
	case "metering":
		content_metering();
	break;	

	case "add":
		add();
	break;	
	
	case "metering_add":
		metering_add();
	break;	
	
	case "metering_scheme_add":
		metering_scheme_add();
	break;
	
	case "request":
		content_request();
	break;

	case "view_request":
	case "view":
		$id = $url[4];
		view_request($id);
	break;

	case "create":	
		$type = $url[4];
		$id = $url[5];
		createSales($id, $type);
	break;	

	
	case "type_request":
		$id = $url[4];
		type_request($id);
	break;
	
	case "view_request_free":
		$id = $url[4];
		view_request_free($id);
	break;

	case "fileCommView":
		$id = $url[4];
		fileCommView($id);
	break;

	case "getDetailBlock":
		$id = isset($_POST["id"]) ? $_POST["id"] : false;
		if($id){
			require_once("usr/modules/selection/ajax.php");
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_selection_request WHERE id = '".$id."' LIMIT 1");
			$r = $db->getRow($query);
			$blocks_code = json_decode($r["detail_code"], true);
			$scheme_code = $r["detail_scheme"];
			$data = getDetail($blocks_code, $scheme_code);
			echo $data;
		}
	break;

	case "detail":		
		$id = intval($url[4]);
		detail($id);
	break;
	
	case "metering_detail":		
		$id = intval($url[4]);
		metering_detail($id);
	break;
	
	case "metering_detail_scheme":		
		$id = intval($url[4]);
		metering_detail_scheme($id);
	break;
    
    case "addProduct":
        $id = intval($url[4]);
        addProduct($id, "https://atri-energo.ru/administration/module/selection/ajax/detail_tovar_add/", "https://atri-energo.ru/administration/module/selection/ajax/detail_tovar_list/");
	break;  
	
    case "addProductMetering":
        $id = intval($url[4]);
        addProduct($id, "https://atri-energo.ru/administration/module/selection/ajax/metering_detail_tovar_add/", "https://atri-energo.ru/administration/module/selection/ajax/metering_detail_tovar_list/");
	break;
	
	case "addProductMeteringScheme":
        $id = intval($url[4]);
        addProduct($id, "https://atri-energo.ru/administration/module/selection/ajax/metering_detail_scheme_tovar_add/", "https://atri-energo.ru/administration/module/selection/ajax/metering_detail_scheme_tovar_list/");
	break;
	
    case "addProductMin":
        $id = intval($url[4]);
		addProduct($id, "", "", "#tov_list", "2", "selection");
    break;
	
	case "addProductMinMetering":
        $id = intval($url[4]);
		addProduct($id, "", "", "#tov_list", "2", "selection");
    break;
	
	case "addProductMinMeteringRs":
        $id = $url[4];
		addProduct($id, "", "", "#tov_list", "2", "metering_rs");
    break;
	
	case "addProductMinMeteringScheme":
        $id = intval($url[4]);
		addProduct($id, "", "", "#tov_list", "2", "selection");
    break;

	case "group":
		selection_group();
	break;	
	
	case "metering_scheme":
		metering_scheme();
	break;	
	
	case "save":
		save();
	break;
	
	case "metering_save":
		metering_save();
	break;
	
	case "metering_scheme_save":
		metering_scheme_save();
	break;
	

	

	
	
	case 'ajax':
		global $adminTpl, $db, $config;
			$type = $url[4];			
			switch($type) 
			{				
				case "detail_tovar_list":
					$id = intval($url[5]);	

					$ic_data = [
						"num" => isset($_POST["num"]) ? $_POST["num"] : false,
						"cid" => isset($_POST["cid"]) ? $_POST["cid"] : false,
						"count" => isset($_POST["count"]) ? $_POST["count"] : false,
						"parentId" => isset($_POST["parentId"]) ? $_POST["parentId"] : false,
					];

					$change = true;
					foreach($ic_data as $key => $val){
						if($val === false){
							$change = false;
							break;
						}
					}

					ajax_detail_tovar_list($id, $change, $ic_data);
				break;				
				case "metering_detail_tovar_list":
					$id = intval($url[5]);	

					$ic_data = [
						"num" => isset($_POST["num"]) ? $_POST["num"] : false,
						"cid" => isset($_POST["cid"]) ? $_POST["cid"] : false,
						"count" => isset($_POST["count"]) ? $_POST["count"] : false,
						"parentId" => isset($_POST["parentId"]) ? $_POST["parentId"] : false,
					];

					$change = true;
					foreach($ic_data as $key => $val){
						if($val === false){
							$change = false;
							break;
						}
					}

					ajax_metering_detail_tovar_list($id, $change, $ic_data);
				break;	
				case "metering_detail_scheme_tovar_list":
					$id = intval($url[5]);	

					$ic_data = [
						"num" => isset($_POST["num"]) ? $_POST["num"] : false,
						"cid" => isset($_POST["cid"]) ? $_POST["cid"] : false,
						"count" => isset($_POST["count"]) ? $_POST["count"] : false,
						"parentId" => isset($_POST["parentId"]) ? $_POST["parentId"] : false,
					];

					$change = true;
					foreach($ic_data as $key => $val){
						if($val === false){
							$change = false;
							break;
						}
					}

					ajax_metering_detail_scheme_tovar_list($id, $change, $ic_data);
				break;	
				
				case "detail_tovar_add":					
					ajax_detail_tovar_add();
				break;	
				case "metering_detail_tovar_add":					
					ajax_metering_detail_tovar_add();
				break;	
				case "metering_detail_scheme_tovar_add":					
					ajax_metering_detail_scheme_tovar_add();
				break;	
				
				case "detail_header":
					$id = intval($url[5]);	
					ajax_detail_header($id);
				break;
				case "metering_detail_header":
					$id = intval($url[5]);	
					ajax_metering_detail_header($id);
				break;
				case "metering_detail_scheme_header":
					$id = intval($url[5]);	
					ajax_metering_detail_scheme_header($id);
				break;
			}
	break;	
}
