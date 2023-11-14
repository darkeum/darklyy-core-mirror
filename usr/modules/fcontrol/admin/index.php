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

$dir = ROOT.'/usr/modules/fcontrol/admin/sub/';  
includeSub($dir);

global $db, $url;

switch(isset($url[3]) ? $url[3] : null) {
	default:
		header("Location: /administration/module/control");
		exit();
		content_main();
	break;	

	case "all":
		header("Location: /administration/module/control/all");
		exit();
	break;
    
	case "pay":
        content_pay();
	break;
	
	case "detail":
        $id = $url[4];
		header("Location: /administration/module/control/{$id}");
		exit();
		detail($id);
	break;		

	case "save_date_payment":
		save_date_payment($_POST);
	break;

	case "del_date_payment":
		del_date_payment($_POST);
	break;
    
    case "pay_detail":
        $id = $url[4];
		detail($id, true);
    break;
    
    case "storage":
        content_storage();
    break;
    
    case "storage_detail":
        $id = $url[4];
        storage_detail($id);
    break;
    
    case "storage_detail_list":
        $id = $url[4];
        storage_detail_list($id);
	break;
	
	case "storage_print":
		$id = $url[4];
		storage_print($id);
	break;
    
    case "saveAllStorageForADay":
        saveAllStorageForADay();
    break;
    
    case "savePayFcotrol":
		$id = $url[4];
		
		savePayFcotrol($id, $_POST);
		
        header('Location: /'.ADMIN.'/module/fcontrol/pay_detail/'.$id);
	break;
	
	case "canclePay":
		$id = $url[4];
		$reason = isset($_POST["reason"]) ? "'".$_POST["reason"]."'" : 'null';
		$db->query("UPDATE `".DB_PREFIX."_control` SET pay = 2, reason = ".$reason." WHERE id = '".$id."' LIMIT 1");

		header('Location: /'.ADMIN.'/module/fcontrol/pay_detail/'.$id);
	break;
    
    case "saveAddPayFcotrol":
        $id = $url[4];
        $file = isset($_POST['file']) ? $_POST['file'] : false;
        $uid = $core->auth->user_info['id'];
        
        $attribute = [];
		$attribute["name"] = "1";
		$attribute["file"] = $file;
		$attribute["module"] = 'fcontrol';
		$attribute["mod"] = 'pay';
		$attribute["id"] = $id;
		$attribute["comm_id"] = $id;
		uploaderMaster("download", $attribute);
        
        header('Location: /'.ADMIN.'/module/fcontrol/pay_detail/'.$id);
    break;
    
    case 'ajax':
		global $adminTpl, $db, $config;
			ajaxInit();
			$type = $url[4];			
			switch($type) 
			{				
				case "detail_list":
					$id = intval($url[5]);		
					ajax_detail_list($id);
				break;				
				case "order_detail_head":
					$id = intval($url[5]);	
					$pay_detail = empty($_POST["pay_detail"]) ? false : true;	
					ajax_order_detail_head($id, $pay_detail);
				break;
				case "order_detail_list":
					$id = intval($url[5]);		
					ajax_order_detail_list($id);
				break;                
                case "receipts_detail_list":
					$id = intval($url[5]);		
					ajax_receipts_detail_list($id);
				break;	                
                case "logistics_detail_list":
					$id = intval($url[5]);		
					ajax_logistics_detail_list($id);
				break;				
				case "order_send":		
					ajax_order_send();
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

}
