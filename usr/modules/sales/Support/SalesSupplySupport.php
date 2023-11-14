<?php

namespace Sales\Support;

use \Boot\Support\Facades\Auth;

class SalesSupplySupport{
    static function create($sales_id, $sales_detail_id, $options = []){
        $status = $options["status"] ?? 1;
        $org = $options["org"] ?? null;
        $uid = Auth::id();

        $s_id = $sales_id;
        $sd_id = $sales_detail_id;

        $clone_sales = new \Sales\CloneSales(["parent_id" => $sales_id]);
        if($clone_sales->check_clone()){
            $s_id = $clone_sales->clone_parent_id;
            $sd_id = [];
            foreach(\Sales\Model\SalesDetail::whereIn("id", $sales_detail_id)->get() as $item){
                $sd_id[] = $item->clone;
            }
        }

        $sales = \Sales\Model\Sales::find($s_id);

        $sales_detail = \Sales\Model\SalesDetail::whereIn("id", $sd_id)->get();
        $supply_detail = [];
        if($sales_detail->count() > 0){
            $date = \Carbon\Carbon::now();
            $uid_array = getGroup(["group" => "supply"]);

            $supply = \Supply\Model\Supply::firstOrCreate([
                "sales_id" => $s_id,
                "type" => 1,
                "org" => $org,
            ], [
                "uid" => $uid,
                "date" => $date,
                "status" => $status,
                "date_edit" => $date,
            ]);
            $sid = $supply->id;
            if($supply->wasRecentlyCreated ){
                relationsSave('sales', $s_id, 'supply', $sid);
                if($status > 0){
                    $attr = [];
                    $attr['uid'] = $uid_array;
                    $attr['title'] = 'Новая заявка на закупку!';
                    $attr['text'] = 'Поступила новая заявка на закупку, нажмите чтобы посмотреть!';
                    $attr['icon'] = config('app.url').'/files/push/new_order.png';
                    $attr['url'] = config('app.url').'/administration/module/supply/detail/'.$sid;
                    $attr['pNotif'] = true;
                    $attr['pPush'] = true;
                    $attr['module'] = "supply";
                    $attr['pid'] = $sid;
                    sendAllNotif($attr);
                }
                
                $sales->supply = $sid;
            } else {
                $supply->status = $status;
                $supply->date_edit = $date;
                $supply->save();
                if($status > 0){
                    $attr = [];
                    $attr['uid'] = $uid_array;
                    $attr['title'] = 'Дополнение заявка на закупку!';
                    $attr['text'] = 'Поступили обновления заявки на закупку, нажмите чтобы посмотреть!';
                    $attr['icon'] = config('app.url').'/files/push/new_order.png';
                    $attr['url'] = config('app.url').'/administration/module/supply/detail/'.$sid;
                    $attr['pNotif'] = true;
                    $attr['pPush'] = true;
                    $attr['module'] = "supply";
                    $attr['pid'] = $sid;
                    sendAllNotif($attr);
                }
            }

            $s_status = 4;

            foreach($sales_detail as $item){
                $SupplyDetail = \Supply\Model\SupplyDetail::create([
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
                $supply_detail[] = $SupplyDetail;
                $item->supply_detail_id = $SupplyDetail->id;
                $item->status = $s_status;
                $item->save();

                \Sales\Model\SalesDetailStatusLog::create([
                    "sales_detail_id" => $item->id, 
                    "status" => $s_status, 
                    "url" => "/administration/module/supply/detail/".$sid,
                ]);

                if(!empty($org) && $org != $sales->org){
                    $orgInfo = getOrgInfo($org);
                    if(!empty($orgInfo["org"]["stock_id_default"])){
                        $reserv = new \Reserv();
                        $reserv->set_reserv($item->id, "sales", $orgInfo["org"]["stock_id_default"], $item->count);
                    }
                }
            }

            $sales->status_client = 5;
            $sales->save();
            \Sales\Support\SalesSupport::status_work($s_id, ["sales" => $sales]);
        }
        return [
            "sales" => $sales,
            "sales_detail" => $sales_detail,
            "supply" => $supply ?? null,
            "supply_detail" => $supply_detail,
        ];
    }
}