<?php

use Boot\Support\Facades\Auth;

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

//require_once('usr/modules/supply/admin/sub/send.php');

function supply_order_query($id){
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$query = $db->query("SELECT s.*, o.name as oname, o.id as oid, sl.id as slid, a.url FROM ".DB_PREFIX."_supply_order as s LEFT JOIN ".DB_PREFIX."_organizations AS o on(s.oid=o.id) LEFT JOIN ".DB_PREFIX."_sales AS sl on(s.sales_id=sl.id) LEFT JOIN JMY_attach as a on (a.pid = s.id AND a.module = 'supply') WHERE s.id = '" . $id . "' LIMIT 1");	
	$order = $db->getRow($query);
	return $order;
}

//проверка
function check_fcontrol($id){
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$error_flag = false;
	$error_info = '';
	$order = supply_order_query($id);
	//проверка на наличие товаров и их цены
	$queryTest = \Supply\Support\SupplyOrderDetailSupport::detail_query($id);
    if($db->numRows($queryTest) > 0)
	{
		$error_info_price = '';
        while($content = $db->getRow($queryTest))
        {			
			//проверка на проставление цены
			if (empty($content['price_now'])||$content['price_now'] == '0')
			{
				$error_flag = true;
				$error_info_price = '<li><b>Проставлены не все цены</b></li>';
			}
		}		
		$error_info .= $error_info_price;
	}
	else
	{
			$error_flag = true;
			$error_info .= '<li><b>Товары не заполнены</b></li>';
	}
	//проверка на объект
	if (empty($order['object']))
	{
		$error_flag = true;
		$error_info .= '<li><b>К счету не присвоен объект</b></li>';
	}
	//проверка на приложенный счет
	if (empty($order['url']))
	{
		$error_flag = true;
		$error_info .= '<li><b>К счету не приложен файл счета</b></li>';
	}
	//проверка на существование заявки
	$qt = $db->query("SELECT * FROM ".DB_PREFIX."_control WHERE supply_o = '".$id."' AND status IN (1, 3)");
	if($db->numRows($qt) > 0){
		$error_flag = true;
		$error_info .= '<li><b>Счет уже был отправлен</b></li>';
	}

	//проверка на существование заявки в продажах
	if(!empty($order["sales_id"])){
		$qq = $db->query("SELECT id FROM ".DB_PREFIX."_sales WHERE id = '".$order["sales_id"]."' AND confirm_supply_order LIKE '%".$id."%' LIMIT 1");
		if($db->numRows($qq) > 0){
			$error_flag = true;
			$error_info .= '<li><b>Счет в продажу уже был отправлен</b></li>';
		}
	}

	if ($error_flag)
	{
		echo 'Счет #'.get_numb('supply_order', $id).' не может быть отправлен на согласование, по причине: <ul style="list-style-type: none;">'.$error_info.'</ul>';
	}
}

function send_fcontrol($id){
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$order = supply_order_query($id);

	$queryTest = \Supply\Support\SupplyOrderDetailSupport::detail_query($id);
    if($db->numRows($queryTest) > 0){
		$insert_values = '';
		$sales_id_mas = [];
		$total = 0;

		$status = 5;

        while($content = $db->getRow($queryTest)){			
			//формирование списка на добавление
			$sdid = 'null';
			if(!empty($content['sale_detail_id'])){
				$sdid = "'{$content['sale_detail_id']}'";
				$sales_id_mas[] = $content['sale_detail_id'];

				\Sales\Model\SalesDetail::find($content['sale_detail_id'])->update(["status" => $status]);
				
				\Sales\Model\SalesDetailStatusLog::create([
					"sales_detail_id" => $content['sale_detail_id'], 
					"status" => $status, 
					"url" => "/administration/module/supply/order_detail/".$id,
				]);
			}
			$insert_values .=  "(NULL, '{control_id}', '".htmlspecialchars($content['title'], ENT_QUOTES)."', '".htmlspecialchars($content['sku'], ENT_QUOTES)."', '".$content['cid']."', ".$sdid.", '".$content['count']."', '".$content['price_now']."', '1', '".$content["soid"]."'),";

			$total = $total + $content['price_now'] * $content['count'];
		}	
	}

	$sales_id = (!empty($order['sales_id']) ? "'".$order['sales_id']."'" : 'null');	
	$uid = Auth::id();
	$date = date('Y-m-d H:i:s', time());
	$plan_pay_date = empty($order["date_pay"]) ? $date : $order["date_pay"];

	if($db->query("INSERT INTO `" . DB_PREFIX . "_control` ( `id` , `uid` , `date` , `supply_o` , `sales_id` ,  `total` , `oid` , `object`, `status`, `pay`, `plan_pay_date`) VALUES (NULL, '" . $db->safesql($uid) . "', '" . $db->safesql($date) . "', '" . $db->safesql($id) . "', ".$sales_id.", '" . $db->safesql($total) . "', '" . $db->safesql($order['oid']) . "', '" . $db->safesql($order['object']) . "', '1', 0, '".$plan_pay_date."');"))
	{
		$sid = $db->insert_id();
		if (!empty($insert_values))
		{
			$insert_values = str_replace("{control_id}", $sid, $insert_values);
			$insert = "INSERT INTO `" . DB_PREFIX . "_control_detail`(`id`, `control_id`, `s_name`, `s_sku`, `cid`, `sdid`, `count`, `price_now`, `status`, `sod_id`) VALUES ".substr_replace($insert_values,';',-1);
			$db->query($insert);
		}	
		supply_order_control_status($id, 1);
		relationsSave('supply_order', $id, 'control', $sid);
		$uid_array = array('3');
		$attr = [];
		$attr['uid'] = $uid_array;
		$attr['title'] = 'Новая заявка на согласование счета!';
		$attr['text'] = 'Поступила новая заявка на согласование счета, нажмите чтобы посмотреть!';
		$attr['icon'] = config('app.url').'/files/push/new_order.png';
		$attr['url'] = config('app.url').'/administration/module/fcontrol/detail/'.$sid;
		$attr['pNotif'] = true;
		$attr['pPush'] = true;
		$attr['module'] = "fcontrol";
        $attr['pid'] = $sid;
		sendAllNotif($attr);
	}
}

function supply_order_control_status($id, $status){
	global $db;
	$control_send = "";
	if($status == 1){
		$control_send = ", `control_send` = '".date("Y-m-d")."'";
	}
	$db->query("UPDATE `".DB_PREFIX."_supply_order` SET `control` = '".$status."'".$control_send." WHERE `id` = '".$db->safesql($id)."' LIMIT 1");
}