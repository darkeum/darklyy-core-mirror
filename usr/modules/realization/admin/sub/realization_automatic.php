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


function realization_automatic($rid)
{
	global $core, $db;
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_realization WHERE id = '".$db->safesql($rid)."'"); 	
	$realization = $db->getRow($query);
	$sales_id = $realization['sales_id'];
	$stock_main = '1';
	$stock_sub = $realization['stock_id'];
	$org_main = '1'; 
	$org_sub = $realization['org']; 
	$org_sub_oid = '523';
	$uid = $core->auth->user_info['id'];
	$oid = $realization['oid'];
	$date = $realization['date'];
	$object = (empty($realization['object']) ? 'NULL' : "'".$realization['object']."'");
	$sales_cat = '1';
	$sales_type = '1';
	$total_all = 0;
	$total_nds_all = 0;
	$detail_array = array();
	$query_detail = $db->query("SELECT * FROM ".DB_PREFIX."_realization_detail WHERE rid = '".$rid."'");	
	while($r = $db->getRow($query_detail))
	{
		//подготавливаем данные
		$count = $r['count'];
		$price_type = '1';
		$price_array = \Price::getPrice($r['cid']);
		$price = $price_array->price_purchase_;
		$discount = '-4';
		$discount_sum = $price*($discount/100);		
		$price_with_discount = $price - $discount_sum;
		$nds = $price_with_discount/120*20;
		$nds_total = $nds*$count;
		$total = $price_with_discount*$count;
		$total_all = $total_all + $total;
		$total_nds_all = $total_nds_all + $nds_total;
		//заполняем данные в массив
		$detail_array[$r['id']]['cid'] = $r['cid'];
		$detail_array[$r['id']]['sku'] = $r['s_sku'];
		$detail_array[$r['id']]['name'] = $r['s_name'];
		$detail_array[$r['id']]['price_type'] = $price_type;
		$detail_array[$r['id']]['price'] = $price;
		$detail_array[$r['id']]['unit'] = $r['s_unit'];
		$detail_array[$r['id']]['count'] = $count;
		$detail_array[$r['id']]['discount'] = $discount;
		$detail_array[$r['id']]['discount_sum'] = $discount_sum;
		$detail_array[$r['id']]['price_with_discount'] = $price_with_discount;
		$detail_array[$r['id']]['nds'] = $nds;
		$detail_array[$r['id']]['nds_total'] = $nds_total;
		$detail_array[$r['id']]['total'] = $total;
		$detail_array[$r['id']]['position'] = $total;
	}
	//получение информация об головной организации
	$org_info = getOrgInfo($org_main);		
	$nds_includes_main = $org_info['org']['nds_includes'];
	$nds_type_main = $org_info['org']['nds'];
	$org_main_oid = $org_info['org']['oid'];
	$org_main_name = $org_info['organizations']['name'];
	$org_main_inn = $org_info['organizations']['inn'];	
	//создание или обновление сопутствующих документов
	if(empty($realization['relation']))
	{
		//создание			
		//создание заказа клиента		
		$numb_sales_array = \Boot\Support\Module::numb('sales', $org_main, $date);
		$numb_sales = $numb_sales_array['numb'];
		$numb_sales_counter = $numb_sales_array['numb_counter'];
		$db->query("INSERT INTO `" . DB_PREFIX . "_sales` ( `id` ,  `numb_counter` ,  `numb` , `uid` , `oid` , `org` , `sales_cat` , `sales_type` ,`date` ,  `date_add` , `pay` , `status`, `object`, `nds_includes`, `total`, `nds`) VALUES (NULL, '" . $numb_sales_counter . "', '" . $numb_sales . "', '" . $uid . "', '" . $org_sub_oid . "', '" . $org_main . "', '" . $sales_cat . "', '" . $sales_type . "','" . $date . "', '" . time() . "', '0', '1', ".$object.", '".$nds_includes_main."', '".$total_all."', '".$total_nds_all."');");
		$sales_id = $db->insert_id();
		if (!empty($realization['object']))
		{
			relationsSave('object', $realization['object'], 'sales', $sales_id);
		}
		/* ------------------------ */
		if(!empty($detail_array))
		{
			foreach ($detail_array as $key => $value)			
			{
				$db->query("INSERT INTO `" . DB_PREFIX . "_sales_detail` (`id`, `sales_id`, `cid`, `s_sku`, `s_name`, `price_type`, `discount`, `s_price`, `count`, `total`, `status`, `type_nds`, `nds`, `s_unit`) VALUES (NULL, '" . $sales_id . "', '" . $value['cid'] . "', '" . $db->safesql($value['sku']) . "', '" . $db->safesql($value['name']) . "', '".$value['price_type']."', '" . $db->safesql($value['discount']) . "', '" . $db->safesql($value['price']) . "', '" . $db->safesql($value['count']) . "', '" . $db->safesql($value['total']) . "', '1', '".$nds_type_main."', '".$value['nds_total']."', ".$value['unit'].")");
				$detail_array[$key]['sdid'] = $db->insert_id();
			}
		}
		//создание реализации
		$numb_realization_array = \Boot\Support\Module::numb('realization', $org_main, $date);
		$numb_realization = $numb_realization_array['numb'];
		$numb_realization_counter = $numb_realization_array['numb_counter'];	
		$db->query("INSERT INTO JMY_realization (numb_counter, numb, sales_id, object, uid, oid, org, date, date_active, nds, pay, active, status, stock_id, total, nds_includes) VALUES ('".$numb_realization_counter."', '".$numb_realization."', ".$sales_id.", ".$object.", ".$uid.", ".$org_sub_oid.", ".$org_main.", '".$date."', '".$date."', ".$total_nds_all.", 0, 0, 1, '".$stock_main."', ".$total_all.", '".$nds_includes_main."')");
        $realization_id = $db->insert_id();
		/* ------------------------ */
		if(!empty($detail_array))
		{
			$str = '';
			foreach ($detail_array as $key => $value)			
			{
				$str .= "(". $realization_id.", ".$value['cid'].", ".$value['sdid'].", '".$value['name']."', '".$value['sku']."', ".$value['price'].", ".$value['price_type'].", '".$value['unit']."', ".$value['count'].", ".$value['discount'].", ".$value['discount_sum'].", ".$value['nds_total'].", ".$value['total'].", 1, '".$nds_type_main."'), ";
			}
			$db->query("INSERT INTO JMY_realization_detail (rid, cid, sdid, s_name, s_sku, s_price, s_price_type, s_unit, count, discount, discount_sum, nds, total, status, nds_type) VALUES ".mb_substr($str, 0, -2)."");
		}		
		//создание счета поставщика		
		$date_supply_order = \Carbon\Carbon::now();
		$numb_supply_array = \Boot\Support\Module::numb('supply_order', $org_sub, $date_supply_order);
		$numb_supply = $numb_supply_array['numb'];
		$numb_supply_counter = $numb_supply_array['numb_counter'];
		$comment = 'Автоматический счет';
		$db->query("INSERT INTO `" . DB_PREFIX . "_supply_order` ( `id` ,  `numb_counter` , `numb` , `org` , `oid` , `uid` ,  `object` ,  `bill_code` , `date_bill` , `date_work` , `date_ship` , `date_end` , `comment` , `total` , `nds` , `status`, `delivery`, `date_create`) VALUES (NULL, '" . $numb_supply_counter . "', '" . $numb_supply . "', '" . $org_sub . "', '" . $org_main_oid . "', '" . $uid . "', " . $object . ", '" . $numb_sales . "', '" . $date . "', '" . $date . "', '" . $date . "', '" . $date . "', '" . $comment . "', '" . $total_all . "', '" . $total_nds_all . "', '1', '1', '".$date_supply_order."');");
		$supply_id = $db->insert_id();
		/* ------------------------ */
		if(!empty($detail_array))
		{			
			foreach ($detail_array as $key => $value)			
			{
				$unit = $value['unit'];
				if (empty($value['unit']))
				{
					$unit = 'null';
				}				
				$db->query("INSERT INTO `" . DB_PREFIX . "_supply_order_detail` (`cid`, `supply_o`, `count`, `price_now`,`total`,`type_nds`, `nds`, `unit`, `status`) VALUES ('" . $db->safesql($value['cid']) . "', '" . $supply_id . "', '" . $db->safesql($value['count']) . "', '" . $db->safesql($value['price_with_discount']) . "', '" . $db->safesql($value['total']) . "', '" . $nds_type_main . "', '" . $db->safesql($value['nds_total']) . "', " . $unit . ", '1')");
				$detail_array[$key]['supply_detail_id'] = $db->insert_id();
			}			
		}		
		//создание документа поступления
		$numb_receipts_array = \Boot\Support\Module::numb('receipts', $org_sub, $date);
		$numb_receipts = $numb_receipts_array['numb'];
		$numb_receipts_counter = $numb_receipts_array['numb_counter'];
		$db->query("INSERT INTO JMY_receipts (numb_counter, numb, supply_id, uid, date, active, status, oid, oid_name, inn, doc_number, date_receipts, org, stock_id, nds_includes, object, total, nds) VALUES('".$numb_receipts_counter."', '".$numb_receipts."', '".$supply_id."', '".$uid."', '".$date."', '0', '1', '".$org_main_oid."', '".$org_main_name."', '".$org_main_inn."', '".$numb_realization."', '".$date."', '".$org_sub."', '".$stock_sub."', '".$nds_includes_main."', ".$object.", ".$total_all.", ".$total_nds_all.")");
		$receipts_id = $db->insert_id();
		/* ------------------------ */
		if(!empty($detail_array))
		{
			$str = '';
			foreach ($detail_array as $key => $value)			
			{
				$str .= "('".$receipts_id."', '".$value['cid']."', '".$value['supply_detail_id']."', '".htmlspecialchars($value['name'], ENT_QUOTES)."', '".$value['sku']."', '".$value['unit']."', '".$value['count']."', '".$value['price_with_discount']."', '1', ".$value['total'].", ".$nds_type_main.", ".$value['nds_total']."), ";
			}
			$db->query("INSERT INTO JMY_receipts_detail (rid, cid, supply_detail_id, name, sku, unit, count, price, status, total, type_nds, nds) VALUES ".mb_substr($str, 0, -2)."");
		}
		$relation_array = array();
		$relation_array['sales_id'] = $sales_id;
		$relation_array['realization_id'] = $realization_id;
		$relation_array['supply_id'] = $supply_id;
		$relation_array['receipts_id'] = $receipts_id;
		$relation_json = json_encode($relation_array, JSON_UNESCAPED_UNICODE);
		$db->query("UPDATE JMY_realization SET relation = '".$relation_json."' WHERE id = '".$rid."' LIMIT 1");
		//проведение документов при необходимости
		if($realization['active'] == '1')
		{
			realization_active($realization_id, true);
			require_once(ROOT.'/usr/modules/supply/admin/sub/receipts_active.php');
			receipts_active($receipts_id, true);
		}
	}
	else
	{
		//обновление
		$relation_array = json_decode($realization['relation'], true);
		$sales_id = $relation_array['sales_id'];
		$realization_id = $relation_array['realization_id'];
		$supply_id = $relation_array['supply_id'];
		$receipts_id = $relation_array['receipts_id'];
		//распроведение документов
		$query_realization_check = $db->query("SELECT * FROM ".DB_PREFIX."_realization WHERE id = '".$db->safesql($realization_id)."'"); 	
		$realization_check = $db->getRow($query_realization_check);
		if($realization_check['active'] == '1')
		{
			realization_active($realization_id, true);
		}
		$query_receipts_check = $db->query("SELECT * FROM ".DB_PREFIX."_receipts WHERE id = '".$db->safesql($receipts_id)."'"); 	
		$receipts_check = $db->getRow($query_receipts_check);
		if($receipts_check['active'] == '1')
		{
			require_once(ROOT.'/usr/modules/supply/admin/sub/receipts_active.php');
			receipts_active($receipts_id, true);
		}
		//обновление заказ клиента
		$db->query("UPDATE JMY_sales SET total = '".$total_all."', nds = '".$total_nds_all."' WHERE id = '".$sales_id."' LIMIT 1");
		$db->query("DELETE FROM `JMY_sales_detail` WHERE sales_id = '".$sales_id."'");
		/* ------------------------ */
		if(!empty($detail_array))
		{
			foreach ($detail_array as $key => $value)			
			{
				$db->query("INSERT INTO `" . DB_PREFIX . "_sales_detail` (`id`, `sales_id`, `cid`, `s_sku`, `s_name`, `price_type`, `discount`, `s_price`, `count`, `total`, `status`, `type_nds`, `nds`, `s_unit`) VALUES (NULL, '" . $sales_id . "', '" . $value['cid'] . "', '" . $db->safesql($value['sku']) . "', '" . $db->safesql($value['name']) . "', '".$value['price_type']."', '" . $db->safesql($value['discount']) . "', '" . $db->safesql($value['price']) . "', '" . $db->safesql($value['count']) . "', '" . $db->safesql($value['total']) . "', '1', '".$nds_type_main."', '".$value['nds_total']."', ".$value['unit'].")");
				$detail_array[$key]['sdid'] = $db->insert_id();
			}
		}
		//обновление реализации
		$db->query("UPDATE JMY_realization SET total = '".$total_all."', nds = '".$total_nds_all."' WHERE id = '".$realization_id."' LIMIT 1");
		$db->query("DELETE FROM `JMY_realization_detail` WHERE rid = '".$realization_id."'");
		/* ------------------------ */
		if(!empty($detail_array))
		{
			$str = '';
			foreach ($detail_array as $key => $value)			
			{
				$str .= "(". $realization_id.", ".$value['cid'].", ".$value['sdid'].", '".$value['name']."', '".$value['sku']."', '".$value['price']."', '".$value['price_type']."', '".$value['unit']."', '".$value['count']."', '".$value['discount']."', '".$value['discount_sum']."', '".$value['nds_total']."', '".$value['total']."', 1, '".$nds_type_main."'), ";
			}
			$db->query("INSERT INTO JMY_realization_detail (rid, cid, sdid, s_name, s_sku, s_price, s_price_type, s_unit, count, discount, discount_sum, nds, total, status, nds_type) VALUES ".mb_substr($str, 0, -2)."");
		}	
		//обновление счета поставщика
		$db->query("UPDATE JMY_supply_order SET total = '".$total_all."', nds = '".$total_nds_all."' WHERE id = '".$supply_id."' LIMIT 1");
		$db->query("DELETE FROM `JMY_supply_order_detail` WHERE supply_o = '".$supply_id."'");
		/* ------------------------ */
		if(!empty($detail_array))
		{			
			foreach ($detail_array as $key => $value)			
			{
				$unit = $value['unit'];
				if (empty($value['unit']))
				{
					$unit = 'null';
				}	
				$db->query("INSERT INTO `" . DB_PREFIX . "_supply_order_detail` (`cid`, `supply_o`, `count`, `price_now`,`total`,`type_nds`, `nds`, `unit`, `status`) VALUES ('" . $db->safesql($value['cid']) . "', '" . $supply_id . "', '" . $db->safesql($value['count']) . "', '" . $db->safesql($value['price_with_discount']) . "', '" . $db->safesql($value['total']) . "', '" . $nds_type_main . "', '" . $db->safesql($value['nds_total']) . "', " . $db->safesql($unit) . ", '1')");
				$detail_array[$key]['supply_detail_id'] = $db->insert_id();
			}			
		}
		//обновление документа поступления
		$db->query("UPDATE JMY_supply_order SET total = '".$total_all."', nds = '".$total_nds_all."' WHERE id = '".$receipts_id."' LIMIT 1");
		$db->query("DELETE FROM `JMY_receipts_detail` WHERE rid = '".$receipts_id."'");
		/* ------------------------ */
		if(!empty($detail_array))
		{
			$str = '';
			foreach ($detail_array as $key => $value)			
			{
				$str .= "('".$receipts_id."', '".$value['cid']."', '".$value['supply_detail_id']."', '".htmlspecialchars($value['name'], ENT_QUOTES)."', '".$value['sku']."', '".$value['unit']."', '".$value['count']."', '".$value['price_with_discount']."', '1', ".$value['total'].", ".$nds_type_main.", ".$value['nds_total']."), ";
			}
			$db->query("INSERT INTO JMY_receipts_detail (rid, cid, supply_detail_id, name, sku, unit, count, price, status, total, type_nds, nds) VALUES ".mb_substr($str, 0, -2)."");
		}
		//проведение документов при необходимости
		if($realization['active'] == '1')
		{
			realization_active($realization_id, true);
			require_once(ROOT.'/usr/modules/supply/admin/sub/receipts_active.php');
			receipts_active($receipts_id, true);
		}	
	}
}