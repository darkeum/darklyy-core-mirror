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


function actualize($id) 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	if(isset($id))
	{
		$sales = $db->query("SELECT id FROM ".DB_PREFIX."_sales WHERE id='".$db->safesql($id)."' AND status='1' LIMIT 1");
		if($db->numRows($sales) > 0)
		{
	
			$db->query("UPDATE ".DB_PREFIX."_sales SET `status` = 2, `sos` = 1 WHERE `id` = '".$db->safesql($id)."'");
			
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_sales_detail WHERE sales_id = '".$db->safesql($id)."'  ORDER BY id");		
			if($db->numRows($query) > 0) 
			{
				$uid = $core->auth->user_info['id'];
				$date = date('Y-m-d H:i:s', time());
				$comment = '';
                $remains = isset($_POST['remains']) ? $_POST['remains'] : false;
                $timing = isset($_POST['timing']) ? $_POST['timing'] : false;
				//добавление заявки в заказы
                if($timing){
				$supply = $db->query("SELECT id FROM ".DB_PREFIX."_supply WHERE sales_id='".$db->safesql($id)."' AND type = 2 LIMIT 1");						
				if($db->numRows($supply) <= 0)
				{
					if($db->query("INSERT INTO `" . DB_PREFIX . "_supply` ( `id` , `uid` , `sales_id` ,  `date` , `comment` , `type`, `status`) VALUES (NULL, '" . $db->safesql($uid) . "', '" . $db->safesql($id) . "', '" . $db->safesql($date) . "', '" . $db->safesql($comment) . "','2', '1');"))
					{
						$sid = $db->insert_id();
						relationsSave('sales', $id, 'supply_date', $sid);
						$uid_array = array('37','8');
						$attr = [];
						$attr['uid'] = $uid_array;
						$attr['title'] = 'Новая заявка на актуализацию данных!';
						$attr['text'] = 'Поступила новая заявка на заказ, нажмите чтобы посмотреть!';
						$attr['icon'] = config('app.url').'/files/push/new_order.png';
						$attr['url'] = config('app.url').'/administration/module/supply/detail/'.$sid;
						$attr['pNotif'] = true;
						$attr['pPush'] = true;
						$attr['module'] = "supply";
                    	$attr['pid'] = $sid;
						//sendAllNotif($attr);
						
					}
				}					
				else
				{
					$supply_info = $db->getRow($supply);
					$sid = $supply_info['id'];
				}
				}
				//добавление заявки на склад
                if($remains){
				$storage = $db->query("SELECT id FROM ".DB_PREFIX."_storage WHERE sales_id='".$db->safesql($id)."' LIMIT 1");						
				if($db->numRows($storage) <= 0)
				{
					if($db->query("INSERT INTO `" . DB_PREFIX . "_storage` ( `id` , `uid` , `sales_id` ,  `date` , `comment` , `status`) VALUES (NULL, '" . $db->safesql($uid) . "', '" . $db->safesql($id) . "', '" . $db->safesql($date) . "', '" . $db->safesql($comment) . "', '1');"))
					{
						$storage_id = $db->insert_id();
						relationsSave('sales', $id, 'storage', $storage_id);
						$uid_array = array('35');
						$attr = [];
						$attr['uid'] = $uid_array;
						$attr['title'] = 'Новая заявка на актуализацию данных!';
						$attr['text'] = 'Поступила новая заявка на сверку количества, нажмите чтобы посмотреть!';
						$attr['icon'] = config('app.url').'/files/push/new_storage.png';
						$attr['url'] = config('app.url').'/administration/module/storage/detail/'.$storage_id;
						$attr['pNotif'] = true;
						$attr['pPush'] = true;
						$attr['module'] = "storage";
                    	$attr['pid'] = $storage_id;
						//sendAllNotif($attr);
					}
				}					
				else
				{
					$storage_info = $db->getRow($storage);
					$storage_id = $storage_info['id'];
				}
				}
				
								
				$insert_values = '';
				$insert_values_storage = '';
				while($sales_detail = $db->getRow($query)) 
				{
					$supply_detail = $db->query("SELECT id FROM ".DB_PREFIX."_supply_detail WHERE sdid='".$db->safesql($sales_detail['id'])."' LIMIT 1");						
					if($db->numRows($supply_detail) <= 0)
					{
						//добавление заявки в заказы
                        if($timing)
						$insert_values .=  "(NULL, '".$sid."','".$sales_detail['id']."', '".$sales_detail['cid']."', '".htmlspecialchars($sales_detail['s_name'], ENT_QUOTES)."', '".htmlspecialchars($sales_detail['s_sku'], ENT_QUOTES)."', '".$sales_detail['price_type']."', '".$sales_detail['s_price']."', '".$sales_detail['s_price_all']."', '".$sales_detail['s_unit']."', '".$sales_detail['count']."', '".$sales_detail['comment']."', '".$sales_detail['status']."'),";
						
						//добавление заявки на склад
                        if($remains)
						$insert_values_storage .=  "(NULL, '".$sales_detail['id']."','".$storage_id."', NULL, '', '1'),";
						
					}						
				}
				if (!empty($insert_values_storage))
				{
					$insert_storage = "INSERT INTO `JMY_storage_detail`(`id`, `sale_detail_id`, `storage_id`, `count_now`, `comment`, `status`) VALUES ".substr_replace($insert_values_storage,';',-1);
					$db->query($insert_storage);
				}
				
				if (!empty($insert_values))
				{
					$insert = "INSERT INTO `JMY_supply_detail`(`id`, `sid`, `sdid`, `cid`, `s_name`, `s_sku`, `price_type`, `s_price`, `s_price_all`, `s_unit`, `count`, `comment`, `status`) VALUES ".substr_replace($insert_values,';',-1);
					$db->query($insert);
					header('Location: /'.ADMIN.'/module/sales/send_ok/'.$id);
				}
				else
				{
					header('Location: /'.ADMIN.'/module/sales/send_null/'.$id);
				}					
				
			}				
		}
		else
		{
			header('Location: /'.ADMIN.'/module/sales/send_no/'.$id);
		}
	}
	else
	{
		header('Location: /'.ADMIN.'/module/sales/send_no/'.$id);
	}
} 
