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

function receipts_active($receipt_id, $silence = false){
	try {
		$receipts_active = new \Supply\ReceiptsActive($receipt_id);
		$options = $receipts_active->toggle();
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
function receipts_active($receipt_id, $silence = false)
{
	global $core, $db;
	if (isset($receipt_id))
	{
		$query = $db->query("SELECT active, stock_id, sales_id, supply_id, org, oid, date_receipts, total, syncRegister FROM `".DB_PREFIX."_receipts` WHERE id = '".$db->safesql($receipt_id)."'");
		$receipts = $db->getRow($query);
		$sales_id = $receipts['sales_id'];
		$supply_id = $receipts['supply_id'];
		$stock_id = $receipts['stock_id'];
		$org = $receipts['org'];
		$oid = $receipts['oid'];
		$date = $receipts['date_receipts'];
		$total = $receipts['total'];

		$balance = new \Economic\Balance;
		$balance->org = $org;
		$balance->oid = $oid;
		$balance->date = $date;
		$balance->value = $total;
		$balance->pid = $receipt_id;
		$balance->module = "receipt";
		$reestr = \Reestr\Models\Reestr::searchByModule("supply_order", $supply_id);
		$balance->reestr_id = $reestr->id ?? null;

		//проверка на количество
		$flagCount = true;
		if($receipts['active'] == '0'){
			$query = $db->query("SELECT sod.count, sod.count_receipts, rd.count as rd_count FROM ".DB_PREFIX."_receipts_detail as rd LEFT JOIN ".DB_PREFIX."_supply_order_detail as sod ON(sod.id = rd.supply_detail_id) WHERE rd.rid = '".$receipt_id."'");
			while($r = $db->getRow($query)){
				$raz = $r["count"] - ($r["count_receipts"] + $r["rd_count"]);
				if($raz < 0){
					$flagCount = false;
					break;
				}
			}
		}

		if($flagCount){
			$sync = new \Sync\Sync;
			if($receipts['active'] == '0')
			{
				logistics_receipts($receipt_id);
				\Logistics\Support\LogisticsDetailSupport::logistics_car_delivery_mark();

				$query = $db->query("SELECT id FROM `".DB_PREFIX."_attach` WHERE `pid` = '".$db->safesql($receipt_id)."' AND `module` = 'receipts'");
				if(($db->numRows($query) > 0)||($silence))
				{
					$sign = 'plus';
					receipts_active_detail($receipt_id, $sales_id, $supply_id, $stock_id, $org, $sign);
					$db->query("UPDATE `".DB_PREFIX."_receipts` SET `active` = '1' WHERE `id` = '".$db->safesql($receipt_id)."'");
					if($receipts['syncRegister'] == 1){
						$sync->syncRegister('receipts', $receipt_id, '1', $org);
					}

					$balance->action = "minus";
					$balance->create();

					if (!$silence)
					{
						echo '1';
					}
				}
				else
				{
					if (!$silence)
					{
						echo '4';
						return;
					}
				}
			}
			elseif($receipts['active'] == '1')
			{
				$sign = 'minus';
				receipts_active_detail($receipt_id, $sales_id, $supply_id, $stock_id, $org, $sign);
				$db->query("UPDATE ".DB_PREFIX."_receipts SET `active` = '0' WHERE `id` = '".$db->safesql($receipt_id)."'");
				if($receipts['syncRegister'] == 1){
					$sync->syncRegister('receipts', $receipt_id, '1', $org);
				}

				$balance->action = "plus";
				$balance->create();

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
					return;
				}
			}

			$sales_detail_id = [];
			$query = $db->query("SELECT sales_detail_id FROM `".DB_PREFIX."_receipts_detail` WHERE rid = '".$db->safesql($receipt_id)."' AND status != '0'");
			if($db->numRows($query) > 0){
				while($rd = $db->getRow($query)){
					if(!empty($rd["sales_detail_id"])){
						$sales_detail_id[] = $rd["sales_detail_id"];
					}
				}
				if($sales_detail_id && $receipt_id && $sign) sales_status_change($sales_detail_id, $receipt_id, $sign);
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
function receipts_active_detail($receipt_id, $sales_id, $supply_id, $stock_id, $org, $sign)
{
	global $core, $db;
	$source = array();
	$source['module'] = 'receipts';
	$source['pid'] = $receipt_id;
	$source['comment'] = 'Отмена проведения поступления #'.module_perfix('receipts', $receipt_id);
	if ($sign == 'plus')
	{
		$source['comment'] = 'Проведение поступления #'.module_perfix('receipts', $receipt_id);
	}
	$query = $db->query("SELECT rd.* FROM ".DB_PREFIX."_receipts_detail as rd WHERE rd.rid = '".$db->safesql($receipt_id)."' AND rd.status != '0'");
	if($db->numRows($query) > 0)
	{
		$reserv = new Reserv();
		while($content = $db->getRow($query))
		{
			$cid = $content['cid'];
			$count = $content['count'];
			updateQuantity($cid, $stock_id, $count, $sign, $source);

			if(empty($content["sales_detail_id"])){
				$count_save = $count;
				switch($sign){
					case "plus":
						$items = \DB::table("receipts_detail as rd")->
						leftJoin("stock_reserv as sr", "rd.cid", "=", "sr.cid")->
						leftJoin("supply_detail as sd", "sd.sdid", "=", "sr.pid")->
						leftJoin("supply as s", "s.id", "=", "sd.sid")->
						where("rd.id", $content["id"])->
						where("sr.stock_id", $stock_id)->
						where("sr.module", "sales")->
						where("sr.status", 2)->
						where("sd.status", 7)->
						where("s.type", 1)->
						select("sr.*")->
						get();
						foreach($items as $item){
							if($count_save >= 0){
								$new_count = $count_save - $item->count >=0 ? $item->count : $count_save;
								$count_save -= $item->count;
								$reserv->change_uid($item->uid);
								$reserv->confirm_reserv($item->pid, "sales", $stock_id, $new_count, $cid);
								\Boot\Models\StockReservSave::create([
									"receipts_detail_id" => $content["id"],
									"pid" => $item->pid,
									"module" => $item->module,
									"count" => $new_count,
									"uid" => $item->uid,
									"stock_id" => $item->stock_id,
								]);
							} else {
								break;
							}
						}
					break;
					case "minus":
						$items = \Boot\Models\StockReservSave::where("receipts_detail_id", $content["id"])->get();
						foreach($items as $item){
							$reserv->change_uid($item->uid);
							$reserv->del_reserv($item->pid, $item->module);
							$reserv->set_reserv($item->pid, $item->module, $item->stock_id);
							$item->delete();
						}
					break;
				}
			} else {
				if($reserv->check_reserv($content["sales_detail_id"], "sales", $stock_id)){
					$qq = \Boot\Models\StockReserv::where("module", "sales")->
					where("pid", $content["sales_detail_id"])->
					where("stock_id", $stock_id)->
					first();
					$reserv->change_uid($qq->uid);

					switch($sign){
						case "plus":
							$reserv->confirm_reserv($content["sales_detail_id"], "sales", $stock_id, $count, $cid);
						break;
						case "minus":
							$reserv->del_reserv($content["sales_detail_id"], "sales");
							$reserv->set_reserv($content["sales_detail_id"], "sales", $stock_id);
						break;
					}
				}
			}

			//count_receipt количество отправленного
			$supplyQuery = $db->query("SELECT count_receipts FROM ".DB_PREFIX."_supply_order_detail WHERE id = '".$content["supply_detail_id"]."'");
			$supply = $db->getRow($supplyQuery);
			$count_receipts = $supply["count_receipts"];
			switch($sign){
				case "plus":
					$count_receipts += $count;
				break;
				case "minus":
					$count_receipts -= $count;
				break;
			}
			$db->query("UPDATE ".DB_PREFIX."_supply_order_detail SET count_receipts = '".$count_receipts."' WHERE id = '".$content["supply_detail_id"]."' LIMIT 1");
		}

		//проверка на приход для supply_order
		$zero = 0;
		$query = $db->query("SELECT supply_id FROM ".DB_PREFIX."_receipts WHERE id = '".$receipt_id."' LIMIT 1");
		if($db->numRows($query) > 0){
			$r = $db->getRow($query);
			$supply_id = $r["supply_id"];
			$query = $db->query("SELECT count, count_receipts FROM ".DB_PREFIX."_supply_order_detail WHERE supply_o = '".$supply_id."' AND deleted_at IS NULL");
			if($db->numRows($query) > 0){
				$count = 0;
				$count_receipts = 0;
				while($r = $db->getRow($query)){
					$count += $r["count"];
					$count_receipts += $r["count_receipts"];
				}

				$admission = 3;
				$close = 1;
				if((double)$count !== (double)$count_receipts){
					$close = 0;
					if((double)$count_receipts === (double)$zero){
						$admission = 1;
					} else {
						$admission = 2;
					}
				}

				$db->query("UPDATE ".DB_PREFIX."_supply_order SET `admission` = '".$admission."', `close` = '".$close."' WHERE id = '".$supply_id."' LIMIT 1");
			}
		}
	}

}

function sales_status_change($id = [], $receipt_id = false, $sign = false){
	global $db;
	if(count($id) > 0){
		if($sign === 'minus'){
			$s_status = 18;
			//изменить статус продажи на 6

			\Sales\Model\SalesDetail::withTrashed()->where("id", $id)->first()->update(["status" => $s_status]);
			foreach($id as $i){
				\Sales\Model\SalesDetailStatusLog::create([
					"sales_detail_id" => $i,
					"status" => $s_status,
					"url" => "/administration/module/supply/receipts_detail/".$receipt_id,
				]);
			}

		}

		if($sign === 'plus'){
			//проверить пришли ли все товары
			$id_str = implode(", ", $id);
			$update7 = [];
			$update11 = [];

			$query = $db->query("SELECT sd.id, sd.count as sd_count, SUM(rd.count) as rd_count
			FROM `".DB_PREFIX."_sales_detail` as sd
			LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sd.sales_id)
			LEFT JOIN `".DB_PREFIX."_receipts_detail` as rd ON(rd.sales_detail_id = sd.id)
			LEFT JOIN `".DB_PREFIX."_receipts` as r ON(r.id = rd.rid)
			WHERE sd.id IN (".$id_str.") AND r.active = '1'
			GROUP BY sd.id");

			// $query = $db->query("SELECT sd.sdid, sd.count
			// FROM `".DB_PREFIX."_supply_detail` as sd
			// LEFT JOIN `".DB_PREFIX."_supply` as s ON(s.id = sd.sid)
			// WHERE sdid IN(".$id_str.") AND s.type = '1'");

			if($db->numRows($query) > 0){
				while($r = $db->getRow($query)){
					// $count = 0;
					// $qq = $db->query("SELECT rd.count
					// FROM `".DB_PREFIX."_receipts_detail` as rd
					// LEFT JOIN `".DB_PREFIX."_receipts` as r ON(r.id = rd.rid)
					// WHERE rd.sales_detail_id = '".$r["sdid"]."' AND r.active = '1'");
					// if($db->numRows($qq) > 0){
					// 	while($p = $db->getRow($qq)){
					// 		$count += $p["count"];
					// 	}
					// }

					// if($count >= $r["count"]){
					// 	//если да -> установить статус 7
					// 	//добавить в историю статус 7
					// 	$update7[] = $r["sdid"];
					// } else if($count != 0){
					// 	//иначе -> установить статус 11
					// 	//добавить в историю статус 11
					// 	$update11[] = $r["sdid"];
					// }

					if($r["rd_count"] >= $r["sd_count"]){
						$update7[] = $r["id"];
					} else {
						$update11[] = $r["id"];
					}
				}

				if(count($update7) > 0){
					//если да -> установить статус 7
					$s_status = 7;

					\Sales\Model\SalesDetail::whereIn("id", $update7)->update([
						"status" => $s_status,
						"status_client" => 6,
					]);
					foreach($update7 as $i){
						\Sales\Model\SalesDetailStatusLog::create([
							"sales_detail_id" => $i,
							"status" => $s_status,
							"url" => "/administration/module/supply/receipts_detail/".$receipt_id,
						]);
					}
				}
				if(count($update11) > 0){
					//иначе -> установить статус 11
					$s_status = 11;

					\Sales\Model\SalesDetail::whereIn("id", $update11)->update(["status" => $s_status]);
					foreach($update11 as $i){
						\Sales\Model\SalesDetailStatusLog::create([
							"sales_detail_id" => $i,
							"status" => $s_status,
							"url" => "/administration/module/supply/receipts_detail/".$receipt_id,
						]);
					}
				}
			}

			$link = [];
			foreach($id as $val){
				$link[] = "supply_o_tov[]={$val}";
			}
			$link = implode("&", $link);

			$salesQuery = $db->query("SELECT s.id, s.uid, s.numb
			FROM `".DB_PREFIX."_sales_detail` as sd
			LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sd.sales_id)
			WHERE sd.id IN(".$id_str.")");
			if($db->numRows($salesQuery) > 0){
				$sales = [];
				while($sd = $db->getRow($salesQuery)){
					if(!array_key_exists($sd["id"], $sales)){
						$sales[ $sd["id"] ] = $sd;
					}
				}
				foreach($sales as $item){
					$attr = [];
					$attr['uid'] = $item["uid"];
					$attr['title'] = 'Товары пришли';
					$attr['text'] = 'Товары по заказу №'.$item["numb"].' пришли';
					$attr['icon'] = config('app.url').'/files/push/task.png';
					$attr['url'] = "https://atri-energo.ru/administration/module/sales/order/".$item["id"]."&".$link;
					$attr['module'] = "sales";
					$attr['pid'] = $item["id"];
					sendAllNotif($attr);
				}
			}
		}
	}
}

function logistics_receipts($id){
	global $db;

	$query = $db->query("SELECT rd.*, l.count as l_count, l.ldd_id, l.ld_id
	FROM `".DB_PREFIX."_receipts_detail` as rd
	LEFT JOIN (
		SELECT ld.sod_id, ldd.count, ldd.id as ldd_id, ld.id as ld_id
		FROM `".DB_PREFIX."_logistics_driver_detail` as ldd
		LEFT JOIN `".DB_PREFIX."_logistics_detail` as ld ON(ld.id = ldd.logistics_detail_id)
		WHERE ldd.status = '2' AND ldd.receipts IS NULL
	) as l ON(l.sod_id = rd.supply_detail_id)
	WHERE rd.rid = '".$id."' AND l.ldd_id IS NOT NULL");
	if($db->numRows($query) > 0){
		$flag = false;
		while($r = $db->getRow($query)){
			if($r["l_count"] > $r["count"]){
				$count = $r["l_count"] - $r["count"];
				\Logistics\Model\LogisticsDetail::findOrFail($r["ld_id"])->update(["count" => $count, "status" => "6"]);
				$flag = true;
			}
			\Logistics\Model\LogisticsDriverDetail::findOrFail($r["ldd_id"])->update(["receipts" => \Carbon\Carbon::now()]);
		}
		if($flag){
			\Logistics\Support\LogisticsDetailSupport::notif_return_items();
		}
	}
}
*/