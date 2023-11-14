<?php

namespace Logistics\Support;

class LogisticsDetailSupport{
    static public function view_status(){
        return "1, 4, 6";
    }

    static public function list_query($request = []){
        $control = \Supply\Support\SupplyOrderSupport::control_sql();
        $search = empty($request["search"]) ? false : $request["search"];
        if($search){
            $search = trim($search);
        }
        $status = empty($request["status"]) ? static::view_status() : $request["status"];
        $status = explode(", ", $status);

        $query = \DB::table("logistics_detail as ld")
        ->leftJoin("supply_order_detail as sod", "sod.id", "=", "ld.sod_id")
        ->leftJoin("catalog", "catalog.id", "=", "sod.cid")
        ->leftJoin("supply_order as so", "so.id", "=", "sod.supply_o")
        ->leftJoin("organizations as o", "o.id", "=", "so.oid")
        ->leftJoin("object as obj", "obj.id", "=", "so.object")
        ->leftJoin(\DB::raw("({$control}) as ".\DB::getTablePrefix()."c"), function($join){
            $join->on("c.supply_o", "=", "so.id");
        })
        ->select(
            "ld.*", 
            "sod.supply_o", "sod.s_name as sod_s_name", "sod.s_sku as sod_s_sku", "sod.count as sod_count", "sod.unit as sod_unit", "sod.date_ship as sod_date_ship", "sod.date_end as sod_date_end", "sod.price_now as sod_price_now",
            "so.numb as so_numb", "so.date_ship as so_date_ship", "so.date_end as so_date_end", "so.org as so_org", "so.bill_code as so_bill_code", "so.object as so_object", "obj.name as object_name",
            "o.name as o_name",
            "catalog.weight as catalog_weight",
            "c.pay as c_pay", "c.reason as c_reason"
        )
        ->orderBy("ld.status", "DESC")
        ->orderBy("so.date_ship", "ASC");
        $query = $query->whereNull("ld.deleted_at")->where("so.close", 0)->whereIn("ld.status", $status);
        if($search){
            $query = $query->where(function($query) use ($search){
                $query
                ->orWhere("o.name", "LIKE", "%{$search}%")
                ->orWhere("so.numb", "LIKE", "%{$search}%")
                ->orWhere("so.bill_code", "LIKE", "%{$search}%");
            });
        }
        if(isset($request["date_ship_active"]) && !empty($request["date_ship"])){
            $date_ship = $request["date_ship"];
            $query = $query->where(function($query) use ($date_ship){
                $query->orWhere("sod.date_ship", $date_ship)->orWhere("so.date_ship", $date_ship);
            });
        }
	    return $query;
    }

    static public function get_count($id){
        global $db;
        $query = $db->query("SELECT ld.count, sod.count as sod_count
        FROM `".DB_PREFIX."_logistics_detail` as ld
        LEFT JOIN `".DB_PREFIX."_supply_order_detail` as sod ON(sod.id = ld.sod_id)
        WHERE ld.deleted_at IS NULL AND ld.id = '".$id."' LIMIT 1");
        $r = $db->getRow($query);
        return static::count($r);
    }

    static public function count($request){
        //["count", "sod_count"]
        return empty($request["count"]) ? $request["sod_count"] : $request["count"]; 
    }

    static function find_add(){
        $rsql = \Supply\Support\SupplyOrderDetailSupport::receipts_sql();
        $result = [
            "count" => 0,
            "items" => [],
        ];
        $date_start = "2020-08-01";
        $date_end = date("Y-m-d", strtotime("+1 week"));
        $q = "SELECT sod.*, rd.count as rd_count
        FROM `".\DB::getTablePrefix()."supply_order_detail` as sod
        LEFT JOIN `".\DB::getTablePrefix()."supply_order` as so ON(so.id = sod.supply_o)
        LEFT JOIN (
            SELECT cd.* 
            FROM `".\DB::getTablePrefix()."control_detail` as cd 
            LEFT JOIN `".\DB::getTablePrefix()."control` as c ON(c.id = cd.control_id)
            WHERE c.status = '3'
        ) as cd ON(sod.id = cd.sod_id)
        LEFT JOIN (
            {$rsql}
        ) as rd ON(rd.supply_detail_id = sod.id) 
        WHERE (
            so.date_ship BETWEEN '{$date_start}' AND '{$date_end}'
            OR sod.date_ship BETWEEN '{$date_start}' AND '{$date_end}'
        ) 
        AND (rd.count < sod.count OR rd.count IS NULL)
        AND cd.id IS NOT NULL
        AND sod.deleted_at IS NULL
        AND so.close = 0
        AND sod.id NOT IN(
            SELECT sod_id FROM `".\DB::getTablePrefix()."logistics_detail`
        )";
        $data = \DB::select($q);
        $result["query"] = $q;
        $result["count"] = count($data);
        foreach($data as $r){
            $result["items"][] = (array)$r;
        }
        return $result;
    }

    static function auto_add(){
        $data = static::find_add();
        $count_add = 0;
        $add_items = [];
        if($data["count"] > 0){
            foreach($data["items"] as $item){
                $insert = [];
                if(!in_array($item["id"], $add_items)){
                    $count_add++;
                    $add_items[] = $item["id"];
                    $insert["sod_id"] = $item["id"];
                    if($item["rd_count"] != 0){
                        $insert["count"] = $item["count"] - $item["rd_count"];
                    }
                    \Logistics\Model\LogisticsDetail::create($insert);
                }
            }
        }
        $data["count"] = $count_add;
        return $data;
    }

    static function notif_return_items($request = []){
        $logistics_detail_id = isset($request["logistics_detail_id"]) ? $request["logistics_detail_id"] : [];
        $list = [];
        foreach($logistics_detail_id as $val){
            $list[] = "ri[]={$val}";
        }
        $list = implode("&", $list);
        $attr = [];
        $uid = getGroup(["group" => "logistic"]);
        $attr['uid'] = $uid;
        $attr['title'] = 'Логистика';
        $attr['text'] = "Груз не забран";
        $attr['icon'] = config('app.url').'/files/push/new_storage.png';
        $attr['url'] = config('app.url').'/administration/module/logistics?'.$list;
        $attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "logistics";
        $attr['pid'] = 0;
        sendAllNotif($attr);
    }

    static function logistics_car_delivery(){
        global $db;
        $result = [
            "count" => 0,
            "items" => [],
        ];
        $rsql = \Supply\Support\SupplyOrderDetailSupport::receipts_sql();
        $status = static::view_status();
        $q = "SELECT ld.*, rd.count as rd_count, sod.count as sod_count
        FROM `".DB_PREFIX."_logistics_detail` as ld
        LEFT JOIN `".DB_PREFIX."_supply_order_detail` as sod ON(sod.id = ld.sod_id)
        LEFT JOIN (
            {$rsql}
        ) as rd ON(rd.supply_detail_id = ld.sod_id) 
        WHERE ld.status IN($status)
        AND rd.count IS NOT NULL";
        $query = $db->query($q);
        $count = $db->numRows($query);
        $result["query"] = $q;
        if($count > 0){
            $result["count"] = $count;
            while($r = $db->getRow($query)){
                $result["items"][] = $r;
            }
        }
        return $result;
    }

    static function logistics_car_delivery_mark(){
        $items = static::logistics_car_delivery();
        $items["update_items"] = [];
        $items["update_data"] = [];
        $items["update_items_count"] = 0;
        if($items["count"] > 0){
            foreach($items["items"] as $item){
                $update = [];
                if($item["rd_count"] == $item["sod_count"]){
                    $update["status"] = 5;
                } else {
                    $count = static::count($item);
                    $remaining_count = $item["sod_count"] - $item["rd_count"];
                    $remaining_count = $remaining_count;
                    if((string)$count != (string)$remaining_count){
                        $update["count"] = $remaining_count;
                        $update["status"] = 4;
                    }
                }
                
                if(count($update) > 0){
                    \Logistics\Model\LogisticsDetail::withTrashed()->where("id", $item["id"])->update($update);
                    $update["id"] = $item["id"];
                    $items["update_items"][] = $item;
                    $items["update_data"][] = $update;
                    $items["update_items_count"]++;
                }
            }
        }
        return $items;
    }

    static function logistics_status(){
        $query = static::list_query();
        foreach($query->get() as $r){
            $r = (array)$r;
            $date = empty($r["sod_date_end"]) ? $r["so_date_end"] : $r["sod_date_end"];
            if($date < date("Y-m-d") && $r["status"] != 4){
                \Logistics\Model\LogisticsDetail::findOrFail($r["id"])->update(["status" => 4]);
            }
        }
    }

    static function delete_supply_order($sod_id){
        \Logistics\Model\LogisticsDetail::where("sod_id", $sod_id)->delete();
    }
}