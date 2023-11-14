<?php

function orderSend($id, $count, $comment, $order_numb = 0, $new_order = 0){
	global $adminTpl, $core, $db, $admin_conf, $lang;
	require_once(ROOT.'usr/modules/supply/admin/sub/update_status_supply_detail.php');
	$confirm_status = supply_detail_confirm_status();
	$confirm_status_str = implode(", ", $confirm_status);
	$query_s = $db->query("SELECT s.status, s.sdid, s.cid, s.sid, s.count, s.count_send, sd.sales_id, s.s_unit, s.s_sku, s.s_name, supply.org
	FROM `".DB_PREFIX."_supply_detail` as s 
	LEFT JOIN `".DB_PREFIX."_supply` as supply on(s.sid = supply.id)
	LEFT JOIN `".DB_PREFIX."_sales_detail` AS sd on(s.sdid = sd.id) 
	WHERE s.id = '".$id."' LIMIT 1");
		if($db->numRows($query_s) > 0)
		{
			$sd = $db->getRow($query_s);
			$sales_id = $sd['sales_id'];
			// if ($sd['count'] >= $sd['count_send'] + $count)
			// {
				$query_sales = $db->query("SELECT object, org FROM `".DB_PREFIX."_sales` WHERE id = '".$sales_id."' LIMIT 1");
				$result_sales = $db->getRow($query_sales);
				
				$org = empty($sd["org"]) ? $result_sales['org'] : $sd["org"];
				$object = empty($result_sales['object']) ? null : $result_sales['object'];
				$sale_detail_id = $sd['sdid'];
				$unit = $sd["s_unit"];
				$uid = $core->auth->user_info['id'];
				$nds_includes = 1;
				if (($order_numb == '0' || empty($order_numb)) && $new_order != '0'){
					$date_create = \Carbon\Carbon::now();
					$numb_array = \Boot\Support\Module::numb('supply_order', $org, $date_create);
					$numb = $numb_array['numb'];
					$numb_counter = $numb_array['numb_counter'];
					$supply = \Supply\Model\SupplyOrder::create([
						"numb_counter" => $numb_counter,
						"numb" => $numb,
						"org" => $org,
						"oid" => $new_order,
						"sales_id" => $sales_id,
						"object" => $object,
						"uid" => $uid,
						"status" => 1,
						"pay" => 0,
						"nds_includes" => $nds_includes,
						"date_create" => $date_create,
					]);
					// $db->query("INSERT INTO `" . DB_PREFIX . "_supply_order` ( `id`, `numb_counter`, `numb`, `org` ,`oid`, `sales_id`, `object`, `uid`, `status`, `pay`, `nds_includes`, `date_create`) VALUES (NULL, '" . $numb_counter . "', '" . $numb . "', '" . $org . "', '" . $new_order . "', '" . $sales_id . "', '" . $object . "', '" . $uid . "', '1', 0, '{$nds_includes}', '{$date_create}');");
					$order_numb = $supply->id;
				}

				$chat = new Chat();
				$chat_find = $chat->find_chat(["once" => "/", "where" => ["module" => "sales", "pid" => $sales_id]]);
				if(count($chat_find) > 0){
					$chat->add_users($chat_find["id"], [$uid]);
				}

				$s_sku = $sd["s_sku"];
				$s_name = $sd["s_name"];

				if($order_numb != '0')
				{

					// $db->query("INSERT INTO `" . DB_PREFIX . "_supply_order_detail` (`sale_detail_id`, `supply_o`, `supply_d`, `comment`, `count`, `status`, `cid`, `unit`, `s_sku`, `s_name`) VALUES ('" . $db->safesql($sale_detail_id) . "', '" . $db->safesql($order_numb) . "', '" . $db->safesql($id) . "', '" . $db->safesql($comment) . "', '" . $db->safesql($count) . "', '1', '".$sd["cid"]."', ".$unit.", '".$s_sku."', '".$s_name."');");
					// $sod_id = $db->insert_id();
					$price = \Price::getPrice($sd["cid"]);
					$p = \Price::get_all([
						"price" => $price->price_purchase_, 
						"count" => $count, 
						"nds_includes" => $nds_includes, 
						"nds_type" => 3,
					]);

					$SupplyOrderDetail = \Supply\Model\SupplyOrderDetail::create([
						"sale_detail_id" => $sale_detail_id, 
						"supply_o" => $order_numb, 
						"supply_d" => $id,
						"comment" => $comment,
						"count" => $p["count"],
						"status" => 1,
						"cid" => $sd["cid"],
						"unit" => $unit,
						"s_sku" => $s_sku,
						"s_name" => $s_name,
						"price_now" => $p["price"],
						"total" => $p["total"],
						"type_nds" => $p["nds_type"],
						"nds" => $p["nds"],
					]);
					$sod_id = $SupplyOrderDetail->id;

					$count_send = $sd['count_send'] + $count;
					$status = supply_detail_count_status($count_send, $sd["count"]);
					$db->query("UPDATE `" . DB_PREFIX . "_supply_detail` SET `status` = '".$status."', count_send = '".$count_send."' WHERE `id` = '" . $id . "' LIMIT 1 ;");
					
					update_status_supply_detail($sd["sid"]);
					
					$query_r = $db->query("SELECT status FROM ".DB_PREFIX."_relations WHERE pid_in = '".$sd['sid']."' AND module_in = 'supply' AND module_out = 'supply_order' AND pid_out = '".$order_numb."' LIMIT 1");					
					if($db->numRows($query_r) <= 0)
					{
						relationsSave('supply', $sd['sid'], 'supply_order', $order_numb);
					}
					echo 'Информация успешно сохранена!';	
					return ["supply_order" => $order_numb, "supply_order_detail" => $sod_id];
				}
				else
				{
					echo 'Ошибка!!!';	
				}
			// }
		}
}