<?php

namespace Sales\Support;

use \Boot\Support\Facades\Auth;

class SalesActualizeSupport{
    //["notice" => true]
    static function create($sales_id, $sales_detail_id, $options = []){
	    $uid_array = getGroup(["group" => "supply"]);
	    $notice = $options["notice"] ?? true;

	    $supply = static::search_actualize($sales_id);
	    if(empty($supply)){
            $supply = \Supply\Model\Supply::create([
                "uid" => Auth::id(),
                "sales_id" => $sales_id,
                "date" => \Carbon\Carbon::now(),
                "type" => 2,
                "status" => 1,
            ]);
            $sid = $supply->id;
            relationsSave('sales', $sales_id, 'supply_date', $sid);
	    	if($notice){
                $attr = [];
                $attr['uid'] = $uid_array;
                $attr['title'] = 'Новая заявка на актуализацию данных!';
                $attr['text'] = 'Поступила новая заявка на актуализацию данных, нажмите чтобы посмотреть!';
                $attr['icon'] = config('app.url').'/files/push/new_order.png';
                $attr['url'] = config('app.url').'/administration/module/supply/twodetail/'.$sid;
                $attr['pNotif'] = true;
                $attr['pPush'] = true;
                $attr['module'] = "twodetail";
                $attr['pid'] = $sid;
                sendAllNotif($attr);
            }
	    } else {
	    	$sid = $supply->id;
	    	if($supply->status == 2){
                $supply->status = 1;
                $supply->save();

	    		if($notice){
	    			$attr = [];
	    			$attr['uid'] = $uid_array;
	    			$attr['title'] = 'Дополнение заявки на актуализацию данных!';
	    			$attr['text'] = 'Поступили обновления заявки на актуализацию данных, нажмите чтобы посмотреть!';
	    			$attr['icon'] = config('app.url').'/files/push/new_order.png';
	    			$attr['url'] = config('app.url').'/administration/module/supply/twodetail/'.$sid;
	    			$attr['pNotif'] = true;
	    			$attr['pPush'] = true;
	    			$attr['module'] = "twodetail";
	    			$attr['pid'] = $sid;
	    			sendAllNotif($attr);
	    		}
	    	}
	    }

        $supply_detail = [];
        foreach(\Sales\Model\SalesDetail::whereIn("id", $sales_detail_id)->get() as $item){
            if(\Supply\Model\SupplyDetail::whereHas("supply", function($query){
                $query->where("type", 2);
            })->where("sdid", $item->id)->doesntExist()){
                $supply_detail[] = \Supply\Model\SupplyDetail::create([
                    "sid" => $sid,
                    "sdid" => $item->id,
                    "cid" => $item->cid,
                    "s_name" => $item->s_name,
                    "s_sku" => $item->s_sku,
                    "price_type" => $item->price_type,
                    "s_price" => $item->s_price,
                    "s_price_all" => $item->s_price_all,
                    "s_unit" => $item->s_unit,
                    "count" => $item->count,
                    "comment" => $item->comment,
                    "status" => 1,
                ]);
                \Sales\Model\SalesDetailStatusLog::create([
	    			"sales_detail_id" => $item->id, 
	    			"status" => 2, 
	    			"url" => "/administration/module/supply/twodetail/".$sid,
	    		]);
                $item->status = 2;
                $item->save();
            }
        }
        return [
            "supply" => $supply,
            "supply_detail" => $supply_detail,
        ];
    }

    static function search_actualize($sales_id){
        return \Supply\Model\Supply::where("type", 2)->where("sales_id", $sales_id)->first();
    }
}