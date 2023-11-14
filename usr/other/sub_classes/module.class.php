<?php

use \Boot\Support\Facades\Auth;
use \Carbon\Carbon;

class Module{
    static public function sales_to_realization($sales_id, $request){
        //["sales_detail_id" => [], "active" => 1]
        //sales_detail
        $arrayCheck = [];
        if(isset($request["sales_detail"])){
            $sales_detail = $request["sales_detail"];
        } else {
            $sales_detail = \Sales\Model\SalesDetail::find($request["sales_detail_id"]);
        }

        //проверка
        $add_flag = false;
        foreach($sales_detail as $r){
            $raz = $r->count - $r->count_realization;
            $arrayCheck[ $r->id ] = $raz;
            if($raz > 0){
                $add_flag = true;
            }
        }

        $rid = false;
        $realization_detail = [];

        if($add_flag){
            $rs = \Sales\Models\Sales::findOrFail($sales_id);

            $realization_data = [];
    
            $realization_data["sales_id"] = $sales_id;
    
            $realization_data["object"] = empty($rs->object) ? null : $rs->object;
            $realization_data["oid"] = empty($rs->oid) ? null : $rs->oid;
            $realization_data["org"] = empty($rs->org) ? null : $rs->org;
            $realization_data["nds"] = empty($rs->nds) ? 0 : $rs->nds;
            $realization_data["total"] = empty($rs->total) ? 0 : $rs->total;
            $realization_data["nds_includes"] =  is_null($rs->nds_includes) ? 1 : $rs->nds_includes;
    
            if(empty($rs->stock_id)){
                $org_info = getOrgInfo($realization_data["org"]);
                $realization_data["stock_id"] = $org_info["org"]["stock_id_default"];
            } else {
                $realization_data["stock_id"] = $rs->stock_id;
            }

            $realization_data["uid"] = $rs->uid; 
    
            $realization = \Realization\Model\Realization::create($realization_data);
            $rid = $realization->id;
            
            $totalAll = 0;
            $ndsAll = 0;
            foreach($sales_detail as $rsd){
                if($arrayCheck[ $rsd->id ] > 0){
                    $realization_detail_data = [];
    
                    $realization_detail_data["rid"] = $rid;
    
                    $realization_detail_data["cid"] = empty($rsd->cid) ? null : $rsd->cid;
                    $realization_detail_data["position"] = empty($rsd->position) ? null : $rsd->position;
                    $realization_detail_data["sdid"] = $rsd->id;
                    $realization_detail_data["s_name"] = empty($rsd->s_name) ? null : $rsd->s_name;
                    $realization_detail_data["s_sku"] = empty($rsd->s_sku) ? null : $rsd->s_sku;
                    $realization_detail_data["s_price"] = empty($rsd->s_price) ? 0 : $rsd->s_price;
                    $realization_detail_data["s_price_type"] = empty($rsd->s_price_type) ? 3 : $rsd->s_price_type;
                    $realization_detail_data["s_unit"] = empty($rsd->s_unit) ? null : $rsd->s_unit;
                    $realization_detail_data["count"] = $arrayCheck[ $rsd->id ];
                    $realization_detail_data["discount"] = empty($rsd->discount) ? 0 : $rsd->discount;
                    $realization_detail_data["nds_type"] = is_null($rsd->type_nds) ? 3 : $rsd->type_nds;
    
                    $data = \Price::get_all([
                        "price" => $realization_detail_data["s_price"], 
                        "count" => $realization_detail_data["count"], 
                        "discount" => $realization_detail_data["discount"], 
                        "nds_includes" => $realization_data["nds_includes"], 
                        "nds_type" => $realization_detail_data["nds_type"]
                    ]);
    
                    $realization_detail_data["s_price"] = $data["price"];
                    $realization_detail_data["discount"] = $data["discount_no_round"];
                    $realization_detail_data["discount_sum"] = $data["discount_sum"];
                    $realization_detail_data["nds"] = $data["nds"];
                    $realization_detail_data["total"] = $data["total"];
    
                    $totalAll += $data["total"];
                    $ndsAll += $data["nds"];
    
                    $realization_detail[] = \Realization\Model\RealizationDetail::create($realization_detail_data);
                } else {
                    continue;
                }
            }
            $totalAll = round($totalAll, 2, PHP_ROUND_HALF_DOWN);
            $ndsAll = round($ndsAll, 2, PHP_ROUND_HALF_DOWN);
            $realization->update(["total" => $totalAll, "nds" => $ndsAll]);

            if(!empty($request["active"])){
                $realization_active = new \Realization\RealizationActive($rid);
                $realization_active->active();
            }
        }
    
        return [
            "realization_id" => $rid,
            "realization_count" => $arrayCheck,
            "realization" => $realization ?? null,
            "realization_detail" => $realization_detail,
        ];
    }

    static public function supply_to_receipts($supply_id, $request){
        //["doc_number" => "", "date_receipts" => "", "org" => "", "stock_id" => "", "supply_detail_id" => "", "active" => ""]
        //supply_detail, check_attach

        $supply_detail = $request["supply_detail"] ?? false;
        $supply_detail_id = $request["supply_detail_id"] ?? false;
        if(($supply_detail && count($supply_detail) > 0) || ($supply_detail_id && count($supply_detail_id) > 0)){
            $supply_order = \Supply\Model\SupplyOrder::findOrFail($supply_id);
            $organization = \Organizations\Model\Organizations::findOrFail($supply_order->oid);

            $receipts_add = [];
            $receipts_add["doc_number"] = $request["doc_number"];
            $receipts_add["date_receipts"] = empty($request["date_receipts"]) ? date("Y-m-d") : $request["date_receipts"];
            $receipts_add["org"] = $request["org"];
            $receipts_add["stock_id"] = $request["stock_id"];
    
            $receipts_add["supply_id"] = $supply_id;
            $receipts_add["sales_id"] = $supply_order->sales_id;
            $receipts_add["nds_includes"] = is_null($supply_order->nds_includes) ? 1 : $supply_order->nds_includes;
            $receipts_add["object"] = $supply_order->object;
            $receipts_add["oid"] = $supply_order->oid;
            $receipts_add["oid_name"] = $organization->name;
            $receipts_add["inn"] = $organization->inn;
    
            $receipts = \Supply\Model\Receipts::create($receipts_add);

            $totalAll = 0;
            $ndsAll = 0;

            if(!$supply_detail) $supply_detail = $supply_order->supply_order_detail()->whereIn("id", $supply_detail_id)->get();

            foreach($supply_detail as $item){
                $receipts_detail_add = [];

                $count_receipts = $item->receipts_detail()->whereHas("receipts", function($query){
                    $query->where("active", 1);
                })->sum("count");

                $receipts_detail_add["count"] = $item->count - $count_receipts;
                if($receipts_detail_add["count"] > 0){
                    $data = \Price::get_all([
                        "price" => $item->price_now, 
                        "count" => $receipts_detail_add["count"], 
                        "nds_includes" => $receipts_add["nds_includes"], 
                        "nds_type" => $item->type_nds,
                    ]);

                    $receipts_detail_add["price"] = $data["price"];
                    $receipts_detail_add["type_nds"] = $data["nds_type"];
                    $receipts_detail_add["total"] = $data["total"];
                    $receipts_detail_add["nds"] = $data["nds"];

                    $receipts_detail_add["rid"] = $receipts->id;
                    $receipts_detail_add["cid"] = $item->cid;
                    $receipts_detail_add["sales_detail_id"] = $item->sale_detail_id;
                    $receipts_detail_add["supply_detail_id"] = $item->id;
                    $receipts_detail_add["name"] = $item->s_name;
                    $receipts_detail_add["sku"] = $item->s_sku;
                    $receipts_detail_add["unit"] = $item->unit;

                    $receipts_detail[] = \Supply\Model\ReceiptsDetail::create($receipts_detail_add);

                    $totalAll += $receipts_detail_add["total"];
		            $ndsAll += $receipts_detail_add["nds"];
                }
            }
            $receipts->update(["total" => $totalAll, "nds" => $ndsAll]);

            if(!empty($request["active"])){
                $check_attach = $request["check_attach"] ?? true;
                $receipts_active = new \Supply\ReceiptsActive($receipts->id, $check_attach);
                $receipts_active->active();
            }
        }

        return [
            "receipts_id" => $receipts->id,
            "receipts" => $receipts,
            "receipts_detail" => $receipts_detail,
        ];
    }

    public function supply_to_control($id, $request = []){
	    $supply_order = \Supply\Model\SupplyOrder::with("supply_order_detail")->where("id", $id)->first();

        if(!empty($supply_order->supply_order_detail) && count($supply_order->supply_order_detail) > 0){

            $orgInfo = getOrgInfo($supply_order->org);

            $control = \Control\Models\Control::create([
                "uid" => Auth::id(),
                "date" => Carbon::now(),
                "supply_o" => $id,
                "total" => $supply_order->supply_order_detail->sum("total"),
                "plan_pay_date" => empty($supply_order->date_pay) ? Carbon::now() : $supply_order->date_pay,
                "oid" => $supply_order->oid,
                "object" => $supply_order->object,
            ]);

            $status = 5;
            foreach($supply_order->supply_order_detail as $item){
                if(!empty($item->sale_detail_id)){
                    \Sales\Model\SalesDetail::find($item->sale_detail_id)->update(["status" => $status]);
	    			\Sales\Model\SalesDetailStatusLog::create([
	    				"sales_detail_id" => $item->sale_detail_id, 
	    				"status" => $status, 
	    				"url" => "/administration/module/supply/order_detail/".$id,
	    			]);
	    		}

                \Control\Models\ControlDetail::create([
                    "control_id" => $control->id,
                    "s_name" => $item->s_name,
                    "s_sku" => $item->s_sku,
                    "cid" => $item->cid,
                    "sod_id" => $item->id,
                    "sdid" => $item->sale_detail_id,
                    "count" => $item->count,
                    "price_now" => $item->price_now,
                ]);
            }

            $supply_order->control = 1;
            $supply_order->control_send = Carbon::now();
            $supply_order->save();

	    	relationsSave('supply_order', $id, 'control', $control->id);
	    	$attr = [];
	    	$attr['uid'] = [ $orgInfo["org"]["director"] ];
	    	$attr['title'] = 'Новая заявка на согласование счета!';
	    	$attr['text'] = 'Поступила новая заявка на согласование счета, нажмите чтобы посмотреть!';
	    	$attr['icon'] = config('app.url').'/files/push/new_order.png';
	    	$attr['url'] = config('app.url')."/administration/module/fcontrol/detail/{$control->id}";
	    	$attr['pNotif'] = true;
	    	$attr['pPush'] = true;
	    	$attr['module'] = "fcontrol";
            $attr['pid'] = $control->id;
	    	sendAllNotif($attr);
        }
    }

    static function sales_to_transfer($id, $options = []){
        //["sales_detail_id" => []]

        $sales_detail_id = $options["sales_detail_id"];

        $uid = Auth::id();
	
        $sales = \Sales\Model\Sales::findOrFail($id);
        
		$stock_in = $options["stock_in"] ?? 2;
		$stock_out = $options["stock_out"] ?? $sales->stock_id;
		$numb_array = \Boot\Support\Module::numb('stock_transfer', $sales->org, date("Y-m-d"));
		$numb = $numb_array['numb'];
		$numb_counter = $numb_array['numb_counter'];
		
        $stock_transfer = \Storage\Model\StockTransfer::create([
            "numb_counter" => $numb_counter,
            "numb" => $numb,
            "uid" => $uid,
            "uid_gave" => $options["uid_gave"] ?? null,
            "uid_accepted" => $options["uid_accepted"] ?? null,
            "object" => $options["object"] ?? $sales->object,
            "org" => $options["org"] ?? $sales->org,
            "sales_id" => $id,
            "stock_out" => $stock_out,
            "stock_in" => $stock_in,
            "date" => \Carbon\Carbon::now(),
            "active" => 0,
            "status" => 1,
        ]);
        $transfer_id = $stock_transfer->id;
		setLog("info", $transfer_id, "stock_transfer", 'Создание перемещения №'.$numb.' из продажи №'.$sales->numb.'; id продажи - '.$id.'; организация - '.$sales->org.'; Склад отправитель - '.$stock_out.', склад получатель  - '.$stock_in.'; объект - '.$sales->object);		

        $stock_transfer_detail = [];
        foreach(\Sales\Model\SalesDetail::where("sales_id", $id)->whereIn("id", $sales_detail_id)->get() as $item){
            if(isset($options["count"]) && isset($options["count"][ $item->id ])){
                $count = $options["count"][ $item->id ];
            } else {
                $count = $item->count;
                $count -= \Storage\Model\StockTransferDetail::whereHas("stock_transfer", function($query){
                    $query->where("active", 1);
                })->where("sdid", $item->id)->sum("count");
            }

            if($count > 0){
                $price = \Sales\Model\SalesDetail::getPurchase($item->id);
                $s_price = empty($price->price) ? 0 : $price->price;
                $stock_transfer_detail[] = \Storage\Model\StockTransferDetail::create([
                    "transfer_id" => $transfer_id,
                    "cid" => $item->cid,
                    "sdid" => $item->id,
                    "s_name" => $item->s_name,
                    "s_sku" => $item->s_sku,
                    "s_unit" => $item->s_unit,
                    "s_price" => $s_price,
                    "count" => $count,
                    "status" => 1,
                ]);
            }
        }

        return [
            "stock_transfer" => $stock_transfer,
            "stock_transfer_detail" => $stock_transfer_detail,
        ];
    }
}