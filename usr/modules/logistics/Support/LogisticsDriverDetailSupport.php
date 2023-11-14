<?php

namespace Logistics\Support;

class LogisticsDriverDetailSupport{
    static public function list_query($id){
		return static::query($id, ["where" => static::list_query_where()]);
	}

	static function query($id, $request = []){
		$control = \Supply\Support\SupplyOrderSupport::control_sql();

		$query = \DB::table("logistics_driver_detail as ldd")
        ->leftJoin("logistics_detail as ld", "ldd.logistics_detail_id", "=", "ld.id")
        ->leftJoin("supply_order_detail as sod", "sod.id", "=", "ld.sod_id")
        ->leftJoin("supply_order as so", "so.id", "=", "sod.supply_o")
        ->leftJoin("organizations as o", "o.id", "=", "so.oid")
        ->leftJoin("object as obj", "obj.id", "=", "so.object")
        ->leftJoin(\DB::raw("({$control}) as ".\DB::getTablePrefix()."c"), function($join){
            $join->on("c.supply_o", "=", "so.id");
        })
        ->select(
            "ldd.*", "ld.transport_companies",
			"sod.supply_o", "sod.s_name as sod_s_name", "sod.s_sku as sod_s_sku", "sod.count as sod_count", "sod.unit as sod_unit", "sod.date_ship as sod_date_ship", "sod.date_end as sod_date_end", "sod.price_now as sod_price_now",
			"so.numb as so_numb", "so.date_ship as so_date_ship", "so.date_end as so_date_end", "so.org as so_org", "so.bill_code as so_bill_code", "so.object as so_object", "obj.name as object_name",
			"o.name as o_name",
			"c.pay as c_pay", "c.reason as c_reason"
		)->where("ldd.driver", $id);
		if(isset($request["where"])){
			$where = $request["where"];
			$query = $where($query);
		}
	    return $query;
	}
	
	static public function list_query_where(){
		return function($query){
			return $query->where("ldd.status", 1);
		};
	}
}