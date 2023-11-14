<?php

namespace Supply\Support;

class SupplyDetailSupport{
    static public function detail_query($request){
        global $db;
        $where = [];
        if(!empty($request["id"])){
            $where[] = "sd.sid = '{$request["id"]}'";
        }
        if(!empty($request["detail_id"])){
            $where[] = "sd.id IN({$request["detail_id"]})";
        }
        $where = count($where) > 0 ? implode(" AND ", $where) : "TRUE";
        return $db->query("SELECT sd.*, cm.name as cm_name, sod.count as sod_count
        FROM `" . DB_PREFIX . "_supply_detail` as sd 
        LEFT JOIN `" . DB_PREFIX . "_catalog` as c on(c.id = sd.cid) 
        LEFT JOIN `" . DB_PREFIX . "_catalog_manufacture` as cm on(c.man = cm.id)
        LEFT JOIN (
            SELECT supply_d, SUM(count) as count 
            FROM `".DB_PREFIX."_supply_order_detail` as sod 
            WHERE deleted_at IS NULL GROUP BY sod.supply_d
        ) as sod ON(sod.supply_d = sd.id)
        WHERE sd.deleted_at IS NULL AND {$where}");
    }
}