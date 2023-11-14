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

function realization_active($realization_id, $silence = false){
	try {
		$realization_active = new \Realization\RealizationActive($realization_id);
		$options = $realization_active->toggle();
		$value = $options["code"];
	} catch (\Boot\Exceptions\OptionsException $e) {
		$options = $e->get_options();
		$value = $options["code"] ?? $e->getMessage();
	} finally{
		if (!$silence){
			echo $value ?? null;
		}
	}
}

/*
function realization_active($realization_id, $silence = false)
{
	global $core, $db;
	if (isset($realization_id))
	{
		$query = $db->query("SELECT r.active, r.stock_id, r.sales_id, r.org, r.date, r.total, r.oid, s.sales_type as s_sales_type
		FROM `".DB_PREFIX."_realization` as r
		LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = r.sales_id)
		WHERE r.id = '".$db->safesql($realization_id)."'");
		$receipts = $db->getRow($query);
		$sales_id = $receipts['sales_id'];
		$stock_id = $receipts['stock_id'];
		$date = $receipts['date'];
		$org = $receipts['org'];
		$oid = $receipts['oid'];
		$total = $receipts['total'];

		//проверка на количество
		$flagCount = true;
		if($receipts['active'] == '0'){
			$query = $db->query("SELECT sd.count, sd.count_realization, rd.count as rd_count FROM ".DB_PREFIX."_realization_detail as rd LEFT JOIN ".DB_PREFIX."_sales_detail as sd ON(sd.id = rd.sdid) WHERE rd.rid = '".$realization_id."'");
			while($r = $db->getRow($query)){
				$raz = $r["count"] - ($r["count_realization"] + $r["rd_count"]);
				if($raz < 0){
					$flagCount = false;
					break;
				}
			}
		}

		if($flagCount){
			$action = false;
			$sync = new \Sync\Sync;

			$balance = new \Economic\Balance;
			$balance->org = $org;
			$balance->oid = $oid;
			$balance->date = $date;
			$balance->value = $total;
			$balance->pid = $realization_id;
			$balance->module = "realization";
			$reestr = \Reestr\Models\Reestr::searchByModule("sales", $sales_id);
			$balance->reestr_id = $reestr->id ?? null;

			if($receipts['active'] == '0')
			{
				$action = 'minus';
				realization_active_detail($realization_id, $sales_id, $stock_id, $org, $action);
				$db->query("UPDATE `".DB_PREFIX."_realization` SET `active` = '1' WHERE `id` = '".$db->safesql($realization_id)."'");
				$intrval = getIntervalKv(date('Y-m-d', time()));
				$date = strtotime($date);
				if((strtotime($intrval['sd']) <= $date)&&(strtotime($intrval['ed']) >= $date))
				{
					if($receipts["s_sales_type"] == 3){
						$sync->syncRegister('realization_cashbox', $realization_id, '1', $org);
					} else {
						// syncRegister('realization', $realization_id, '2', $org);
						$sync->syncRegister('realization', $realization_id, '1', $org);
					}
				}

				$balance->action = "plus";
				$balance->create();

				if($org == '2')
				{
					// realization_automatic($realization_id);
				}
				if (!$silence)
				{
					echo '1';
				}
			}
			elseif($receipts['active'] == '1')
			{
				$action = 'plus';
				realization_active_detail($realization_id, $sales_id, $stock_id, $org, $action);
				$db->query("UPDATE `".DB_PREFIX."_realization` SET `active` = '0' WHERE `id` = '".$db->safesql($realization_id)."'");
				$intrval = getIntervalKv(date('Y-m-d', time()));
				$date = $receipts['date'];
				$date = strtotime($date);
				if((strtotime($intrval['sd']) <= $date)&&(strtotime($intrval['ed']) >= $date))
				{
					if($receipts["s_sales_type"] == 3){
						$sync->syncRegister('realization_cashbox', $realization_id, '1', $org);
					} else {
						// syncRegister('realization', $realization_id, '2', $org);
						$sync->syncRegister('realization', $realization_id, '1', $org);
					}
				}

				$balance->action = "minus";
				$balance->create();

				if($org == '2')
				{
					// realization_automatic($realization_id);
				}
				if (!$silence)
				{
					echo '2';
				}
			}
			else
			{
				if (!$silence)
				{
					echo '3';
				}
			}

			if($action){
				$sales_detail_id = [];
				$query = $db->query("SELECT sdid FROM ".DB_PREFIX."_realization_detail WHERE rid = '".$db->safesql($realization_id)."' AND status != '0'");
				if($db->numRows($query) > 0){
					while($rd = $db->getRow($query)){
						if(!empty($rd["sdid"])){
							$sales_detail_id[] = $rd["sdid"];
						}
					}
					sales_status_change2($sales_detail_id, $action, $realization_id, $sales_id);
				}
			}
		}
		else
		{
			if (!$silence)
			{
				echo '5';
			}
		}

	}
}
function realization_active_detail($realization_id, $sales_id, $stock_id, $org, $sign)
{
	global $core, $db;
	$sales_data = \Sales\Model\Sales::findOrFail($sales_id)->toArray();

	$reserv = new \Reserv();
	$reserv->change_uid($sales_data["uid"]);
	$query = $db->query("SELECT rd.*, ct.service as ct_service
	FROM ".DB_PREFIX."_realization_detail as rd
	LEFT JOIN ".DB_PREFIX."_catalog as c ON(c.id = rd.cid)
	LEFT JOIN ".DB_PREFIX."_catalog_types as ct ON(ct.id = c.types)
	WHERE rd.rid = '".$db->safesql($realization_id)."' AND rd.status != '0'");
	$source = array();
	$source['module'] = 'realization';
	$source['pid'] = $realization_id;
	$source['comment'] = 'Проведение реализации #'.module_perfix('realization', $realization_id);
	if ($sign == 'plus')
	{
		$source['comment'] = 'Отмена проведения реализации #'.module_perfix('realization', $realization_id);
	}
	if($db->numRows($query) > 0)
	{
		$supply_order_price_all = 0;
		while($content = $db->getRow($query))
		{
			$cid = $content['cid'];
			$count = $content['count'];
			if($content["ct_service"] == 0){
				if($sign === 'minus'){
					insert_shipment_minus(['cid' => $cid, 'module' => $source['module'], 'pid' => $source['pid'], 'stock_id' => $stock_id, 'count' => $count, 'comment' => $source['comment'], 'insert' => "before"]);
				}
				updateQuantity($cid, $stock_id, $count, $sign, $source);
			}
			if($sign === 'minus'){
				$gppds = \Sales\Model\SalesDetail::getPurchase($content['sdid']);
				$supply_order_price = empty($gppds->price) ? 0 : $gppds->price;
				$supply_order_price_all += $supply_order_price * $count;
				$db->query("UPDATE ".DB_PREFIX."_realization_detail SET supply_order_price = '".$supply_order_price."' WHERE id = '".$content["id"]."' LIMIT 1");
			} else {
				$db->query("UPDATE ".DB_PREFIX."_realization_detail SET supply_order_price = NULL WHERE id = '".$content["id"]."' LIMIT 1");
			}

			//обновление количества реализованного
			if(!empty($content["sdid"])){

				//удаление резервов
				if($sign == "minus"){
					$reserv->end_reserv($content["sdid"], "sales", $stock_id, $content["count"]);
				}

				$query_sales_detail = $db->query("SELECT count, count_realization FROM ".DB_PREFIX."_sales_detail WHERE id = '".$content["sdid"]."' LIMIT 1");
				$sales_detail = $db->getRow($query_sales_detail);
				$count_realization = $sales_detail["count_realization"];
				if($sign === 'minus'){
					$count_realization += $count;
				} else {
					$count_realization -= $count;
				}
				$db->query("UPDATE ".DB_PREFIX."_sales_detail SET count_realization = '".$count_realization."' WHERE id = '".$content["sdid"]."' LIMIT 1");
			}



		}
		if($sign === 'minus'){
			$db->query("UPDATE ".DB_PREFIX."_realization SET supply_order_price = '".$supply_order_price_all."' WHERE id = '".$realization_id."' LIMIT 1");
		} else {
			$db->query("UPDATE ".DB_PREFIX."_realization SET supply_order_price = NULL WHERE id = '".$realization_id."' LIMIT 1");
		}
	}
}

function sales_status_change2($id, $action, $realization_id, $sales_id){
	global $db;

	$id_str = implode(", ", $id);
	if(count($id) > 0){
		if($action === "plus"){
			$s_status = 19;
			foreach($id as $i){
				\Sales\Model\SalesDetail::find($i)->update(["status" => $s_status]);
				\Sales\Model\SalesDetailStatusLog::create([
					"sales_detail_id" => $i,
					"status" => $s_status,
					"url" => "/administration/module/realization/order/".$realization_id,
				]);
			}
		}

		//проверить реализацию всех товаров
		$update12 = [];
		$update13 = [];
		$update0 = [];

		$query = $db->query("SELECT id, count FROM `".DB_PREFIX."_sales_detail` WHERE id IN(".$id_str.")");
		if($db->numRows($query) > 0){
			while($r = $db->getRow($query)){
				$count = 0;
				$qq = $db->query("SELECT rd.count FROM ".DB_PREFIX."_realization_detail as rd LEFT JOIN ".DB_PREFIX."_realization as r ON(r.id = rd.rid) WHERE rd.sdid = '".$r["id"]."' AND r.active = '1'");
				if($db->numRows($qq) > 0){
					while($p = $db->getRow($qq)){
						$count += $p["count"];
					}
				}
				if($count >= $r["count"]){
					//если да -> установить статус 12
					//добавить в историю статус 12
					$update12[] = $r["id"];
				} else if($count != 0){
					//иначе -> установить статус 13
					//добавить в историю статус 13
					$update13[] = $r["id"];
				}
			}

			if(count($update12) > 0){
				$s_status = 12;
				foreach($update12 as $i){
					\Sales\Model\SalesDetail::find($i)->update(["status" => $s_status]);
					if($action == 'minus'){
						\Sales\Model\SalesDetailStatusLog::create([
							"sales_detail_id" => $i,
							"status" => $s_status,
							"url" => "/administration/module/realization/order/".$realization_id,
						]);
					}
				}
			}

			if(count($update13) > 0){
				$s_status = 13;
				foreach($update13 as $i){
					\Sales\Model\SalesDetail::find($i)->update(["status" => $s_status]);
					if($action == 'minus'){
						\Sales\Model\SalesDetailStatusLog::create([
							"sales_detail_id" => $i,
							"status" => $s_status,
							"url" => "/administration/module/realization/order/".$realization_id,
						]);
					}
				}
			}

			if($action == "plus"){
				$status_client = 6;
			}

			if($action == "minus"){
				$status_client = 7;
			}
			\Sales\Model\SalesDetail::whereIn("id", $id)->update(["status_client" => $status_client]);
			\Sales\Model\Sales::find($sales_id)->re_status_client();
		}
	}
}
*/