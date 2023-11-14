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


function send_transfer($id) 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	if(isset($id))
	{
		$sales = $db->query("SELECT id, object FROM ".DB_PREFIX."_sales WHERE id='".$db->safesql($id)."'  LIMIT 1");
		if($db->numRows($sales) > 0)
		{	
			
			$query = $db->query("SELECT * FROM ".DB_PREFIX."_sales_detail WHERE sales_id = '".$db->safesql($id)."' AND status='7' ORDER BY id");		
			if($db->numRows($query) > 0) 
			{
				$result_sales = $db->getRow($sales);	
				$object_id = $result_sales['object'];
				$uid = $core->auth->user_info['id'];
				$date = date('Y-m-d H:i:s', time());
				$comment = '';				
				//добавление заявки на перемещение
				$supply = $db->query("SELECT id FROM ".DB_PREFIX."_stock_transfer WHERE sales_id='".$db->safesql($id)."' LIMIT 1");						
				if($db->numRows($supply) <= 0)
				{
					if($db->query("INSERT INTO `" . DB_PREFIX . "_stock_transfer` ( `id` , `uid` , `object`,  `sales_id`,  `stock_out`, `stock_in`, `comment` , `date` , `active`, `status`) VALUES (NULL, '" . $db->safesql($uid) . "', '" . $db->safesql($object_id) . "', '" . $db->safesql($id) . "', '1','2', '" . $db->safesql($comment) . "','" . $db->safesql($date) . "', '0', '1');"))
					{
						$transfer_id = $db->insert_id();
						/*
						relationsSave('sales', $id, 'supply', $sid);
						$uid_array = array('37','8');
						$attr = [];
						$attr['uid'] = $uid_array;
						$attr['title'] = 'Новая заявка на закупку!';
						$attr['text'] = 'Поступила новая заявка на заказ, нажмите чтобы посмотреть!';
						$attr['icon'] = config('app.url').'/files/push/new_order.png';
						$attr['url'] = config('app.url').'/administration/module/supply/detail/'.$sid;
						$attr['pNotif'] = true;
						$attr['pPush'] = true;
						$attr['module'] = "supply";
            			$attr['pid'] = $sid;
						sendAllNotif($attr);
						*/
					}
				}					
				else
				{
					$supply_info = $db->getRow($supply);
					$transfer_id = $supply_info['id'];
				}	
				$insert_values = '';
				$sales_detail_ids = array();				
				while($sales_detail = $db->getRow($query)) 
				{
					$supply_detail = $db->query("SELECT id FROM ".DB_PREFIX."_stock_transfer_detail WHERE transfer_id='".$transfer_id."' AND sdid='".$db->safesql($sales_detail['id'])."' LIMIT 1");					
					if($db->numRows($supply_detail) <= 0)
					{
						//Список ид добавленных в заказ
						$sales_detail_ids[] = $sales_detail['id'];
						//добавление заявки в заказы
						$s_price = \Price::getPrice($sales_detail['cid']);
						$s_price = $s_price->price_purchase_;
						$insert_values .=  "(NULL, '".$transfer_id."','".$sales_detail['cid']."', '".$sales_detail['id']."', '".htmlspecialchars($sales_detail['s_name'], ENT_QUOTES)."', '".htmlspecialchars($sales_detail['s_sku'], ENT_QUOTES)."', '".$sales_detail['s_unit']."', '".$s_price."', '".$sales_detail['count']."', '1'),";					
						
					}						
				}								
				if(!empty(implode(',', $sales_detail_ids)))
				{
					//$update_sales_status = "UPDATE `JMY_sales_detail` SET status = '4' WHERE `id` IN (".implode(',', $sales_detail_ids).");";
					//$db->query($update_sales_status);
				}				
				if (!empty($insert_values))
				{
					$insert = "INSERT INTO `JMY_stock_transfer_detail`(`id`, `transfer_id`, `cid`, `sdid`, `s_name`, `s_sku`, `s_unit`, `s_price`, `count`, `status`) VALUES ".substr_replace($insert_values,';',-1);
					$db->query($insert);
					//$db->query("UPDATE ".DB_PREFIX."_sales SET `status` = 4, `supply` = '".$db->safesql($sid)."' WHERE `id` = '".$db->safesql($id)."'");				
                    echo "Отправлено";
				}
				else
				{
                    echo "Данные уже были отправлены";
				}					
				
			}				
		}
		else
		{
            echo "Упс, что то пошло не так...";
		}
	}
	else
	{
        echo "Упс, что то пошло не так...";
	}
} 
