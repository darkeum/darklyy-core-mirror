<?php

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function logistics_delivery_driver_add($request){
    $driver = $request["driver"];
    $id = isset($request["id"]) ? $request["id"] : [];
    if(count($id) > 0){
        foreach($id as $i){
            \Logistics\Model\LogisticsDelivery::findOrFail($i)->update(["status" => 2, "driver" => $driver]);
        }
        $attr = [];
    	$attr['uid'] = $driver;
    	$attr['title'] = 'Логистика';
    	$attr['text'] = "Поступила заявка на доставку груза";
    	$attr['icon'] = config('app.url').'/files/push/new_storage.png';
    	$attr['url'] = config('app.url').'/administration/module/logistics/logistics_delivery_driver/detail_min/'.$driver;
    	$attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "logistics";
        $attr['pid'] = $i;
    	sendAllNotif($attr);

        $attr = [];
    	$attr['uid'] = getGroup(["group" => "storage"]);
    	$attr['title'] = 'Логистика';
    	$attr['text'] = "Поступила заявка на доставку груза";
    	$attr['icon'] = config('app.url').'/files/push/new_storage.png';
    	$attr['url'] = config('app.url').'/administration/module/storage/delivery_storage';
    	$attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "logistics";
        $attr['pid'] = $i;
    	sendAllNotif($attr);
    }
}

function add_logistics_delivery_route($request){
    $module = $request["module"];
    $pid = $request["pid"];
    switch($module){
        case "realization":
            $request["query_detail"] = "SELECT id FROM `".DB_PREFIX."_realization_detail` WHERE rid = '{$pid}'";
        break;
        case "stock_transfer":
            $request["query_detail"] = "SELECT id FROM `".DB_PREFIX."_stock_transfer_detail` WHERE transfer_id = '{$pid}'";
        break;
    }
    $data = add_logistics_delivery($request);
    switch($module){
        case "realization":
            if($request["delivery_type"] == 1){

                $realization = \Realization\Model\Realization::find($pid);
                $cid = get_delivery_cid();
                $catalog_data = \Catalog_v2\Model\Catalog::findOrFail($cid)->toArray();
                $price_data = \Price::get_all(["price" => $request["delivery_price"]]);
                $sales_id = $realization->sales_id;
                $add = [
                    "sales_id" => $sales_id,
                    "cid" => $catalog_data["id"],
                    "s_sku" => $catalog_data["sku"],
                    "s_name" => $catalog_data["title"],
                    "price_type" => 4,
                    "s_price" => $price_data["price"],
                    "s_unit" => $catalog_data["unit"],
                    "count" => $price_data["count"],
                    "type_nds" => $price_data["nds_type"],
                    "nds_type" => $price_data["nds_type"],
                    "nds" => $price_data["nds"],
                    "total" => $price_data["total"],
                ];
                $sales = \Sales\Model\SalesDetail::create($add);
                $sdid = $sales->id;
                $add["rid"] = $pid;
                $add["sdid"] = $sdid;
                \Realization\Model\RealizationDetail::create($add);

                \Sales\Support\SalesDetailSupport::recount_sales($sales_id);

                $realization->total += $price_data["total"];
                $realization->nds += $price_data["nds"];
                $realization->save();
            }
        break;
    }
    return $data;
}

function add_logistics_delivery($request){
    //pid, module, query_detail
    global $db;
    $result = [
        "logistics_delivery_id" => false,
        "count" => 0,
        "error_message" => false,
    ];

    // $pid = $request["pid"];
    // $module = $request["module"];
    $query_detail = $request["query_detail"];
    if(\Logistics\Support\LogisticsDeliverySupport::is_create($request)){
        $result["error_message"] = "Заявка была отправлена";
    } else {
        $query = $db->query($query_detail);
        if($db->numRows($query) > 0){
            $logistics_delivery = \Logistics\Model\LogisticsDelivery::create($request);
            $logistics_delivery_id = $logistics_delivery->id;
            $result["logistics_delivery_id"] = $logistics_delivery_id;
            while($r = $db->getRow($query)){
                \Logistics\Model\LogisticsDeliveryDetail::create(["logistics_delivery_id" => $logistics_delivery_id, "pid" => $r["id"]]);
                $result["count"]++;
            }
        }
    }
    
    return $result;
}
