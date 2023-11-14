<?php

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function download_supply_order($id){
	$data = \Supply\Support\SupplyOrderSupport::get_attach($id);
	if(count($data) > 0){
		if(count($data) > 1){
			$zip = new ZipArchive();
			$fileName = "files_".time().".zip";
			$zip->open($fileName, ZIPARCHIVE::CREATE);
			foreach($data as $r){
				$zip->addFile($r["url"], $r["name"]);
			}
			$zip->close();
			if(file_exists($fileName)){
                header('Content-type: application/zip');
                header('Content-Disposition: attachment; filename="'.$fileName.'"');
                readfile($fileName);
                unlink($fileName);
			}
		} else {
			$url = $data[0]["url"];
			header("Location: /{$url}");
		}
	}
}

function logistics_driver_add($request){
	$driver = $request["driver"];
	$id = $request["id"] ?? [];
	$count = $request["count"] ?? [];

	if(count($id) > 0){
		foreach($id as $i){
			$count_main = \Logistics\Support\LogisticsDetailSupport::get_count($i);
			$count = $count[ $i ] ?? $count_main;
			$count_diff = $count_main - $count;
			\Logistics\Model\LogisticsDriverDetail::create(["driver" => $driver, "logistics_detail_id" => $i, "count" => $count, "count_save" => $count]);
			$update = [];
			if($count_diff > 0){
				$update["count"] = $count_diff;
			} else {
				$update["status"] = 2;
				$update["count"] = 0;
			}
			// $update["status"] = 2;
			\Logistics\Model\LogisticsDetail::findOrFail($i)->update($update);
		}
		$attr = [];
    	$attr['uid'] = $driver;
    	$attr['title'] = 'Логистика';
    	$attr['text'] = "Поступила заявка на забор груза";
    	$attr['icon'] = config('app.url').'/files/push/new_storage.png';
    	$attr['url'] = config('app.url').'/administration/module/logistics/logistics_driver/detail_min/'.$driver;
    	$attr['pNotif'] = true;
		$attr['pPush'] = true;
		$attr['module'] = "logistics";
        $attr['pid'] = 0;
    	sendAllNotif($attr);
	}
}

function logistics_driver_pick_up($request){
	$supply_o = $request["supply_o"];
	$id = isset($request["id"]) ? $request["id"] : [];

	$ldd_where = \Logistics\Support\LogisticsDriverDetailSupport::list_query_where();
	$query = \DB::table("logistics_driver_detail as ldd")
	->leftJoin("logistics_detail as ld", "ldd.logistics_detail_id", "=", "ld.id")
	->leftJoin("supply_order_detail as sod", "sod.id", "=", "ld.sod_id")
	->select("ldd.*", "sod.sale_detail_id as sod_sale_detail_id", "ld.count as ld_count")
	->where("sod.supply_o", $supply_o);
	$query = $ldd_where($query);
	
	if($query->exists()){
		$logistics_detail_id_list = [];
		foreach($query->get() as $r){
			$r = (array) $r;
			$flag = false;
			$update_sales_flag = false;
			$s_status = 0;
			$update_ld = [];
			$update_ldd = [];

			$count_main = \Logistics\Support\LogisticsDetailSupport::get_count($r["logistics_detail_id"]);
			
			if(in_array($r["id"], $id)){
				$update_sales_flag = true;
				$s_status = 22;
				$update_ld["status"] = 3;
				$update_ldd["status"] = 2;

				if($r["count"] < $r["count_save"]){
					$s_status = 23;
					$update_ld["status"] = 6;
					$update_ld["count"] = $r["ld_count"] + $r["count_save"] - $r["count"];
					// $update_ld["count"] = $r["count_save"] - $r["count"];
					$update_ldd["receipts"] = date("Y-m-d H:i:s");
					$flag = true;
					if($r["count"] <= 0){
						$update_sales_flag = false;
					}
				}
			} else {
				$update_ld["status"] = 6;
				$update_ldd["status"] = 3;
				$update_ld["count"] = $r["ld_count"] + $r["count"];
				$flag = true;
			}

			if(isset($update_ld["count"]) && $update_ld["count"] > $count_main){
				$update_ld["count"] = null;
			}

			\Logistics\Model\LogisticsDetail::findOrFail($r["logistics_detail_id"])->update($update_ld);
			\Logistics\Model\LogisticsDriverDetail::findOrFail($r["id"])->update($update_ldd);
			if($flag){
				$logistics_detail_id_list[] = $r["logistics_detail_id"];
			}
			if(!empty($r["sod_sale_detail_id"]) && $update_sales_flag){
				\Sales\Model\SalesDetail::find($r["sod_sale_detail_id"])->update(["status" => $s_status]);
            	\Sales\Model\SalesDetailStatusLog::create([
            	    "sales_detail_id" => $r["sod_sale_detail_id"], 
            	    "status" => $s_status, 
            	    "url" => "/administration/module/supply/order_detail/".$supply_o,
            	]);
			}
		}
		if($flag){
			\Logistics\Support\LogisticsDetailSupport::notif_return_items(["logistics_detail_id" => $logistics_detail_id_list]);
		}
	}
}

function logistics_notif_send($id, $request){
	global $db;
	\Logistics\Model\LogisticsDetail::findOrFail($id)->update(["status" => 4]);

	$query = $db->query("SELECT so.id as so_id, so.uid as so_uid 
	FROM `".DB_PREFIX."_logistics_detail` as ld 
	LEFT JOIN `".DB_PREFIX."_supply_order_detail` as sod ON(sod.id = ld.sod_id) 
	LEFT JOIN `".DB_PREFIX."_supply_order` as so ON(so.id = sod.supply_o)
	WHERE ld.id = '{$id}' LIMIT 1");
	$r = $db->getRow($query);
	$uid = [];
	$uid[] = $r["so_uid"];

	$text = empty($request["text"]) ? "Товар не будет доставлен в срок!" : $request["text"];

	$attr = [];
    $attr['uid'] = $uid;
    $attr['title'] = 'Логистика';
    $attr['text'] = $text;
    $attr['icon'] = config('app.url').'/files/push/new_storage.png';
    $attr['url'] = config('app.url').'/administration/module/supply/order_detail/'.$r["so_id"];
    $attr['pNotif'] = true;
    $attr['pPush'] = true;
	sendAllNotif($attr);
	$attr['module'] = "logistics";
    $attr['pid'] = $id;
}

function supply_order_link($supply_o){
	global $db;
	$query = $db->query("SELECT ld.id 
	FROM `".DB_PREFIX."_logistics_detail` as ld 
	LEFT JOIN `".DB_PREFIX."_supply_order_detail` as sod ON(sod.id = ld.sod_id)
	WHERE sod.supply_o = '{$supply_o}' AND ld.status = '6'");
	if($db->numRows($query) > 0){
		while($r = $db->getRow($query)){
			// dd($r);
			\Logistics\Model\LogisticsDetail::findOrFail($r["id"])->update(["status" => 4]);
		}
	}
	header("Location: /administration/module/supply/order_detail/{$supply_o}");
}