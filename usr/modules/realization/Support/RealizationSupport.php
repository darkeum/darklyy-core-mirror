<?php

namespace Realization\Support;

class RealizationSupport{
    static function check_minus_count($id){
        global $db;
        $query = $db->query("SELECT rd.*, r.stock_id, r.org 
            FROM `".DB_PREFIX."_realization_detail` as rd 
            LEFT JOIN `".DB_PREFIX."_realization` as r ON(r.id = rd.rid) 
            WHERE rid = '".$id."'");
        $org = "";
        $result = [];
        if($db->numRows($query)){
            $data = [];
            while($r = $db->getRow($query)){
                $data[$r["id"]] = [
                    "pid" => $r["sdid"],
                    "module" => "sales",
                    "count" => $r["count"],
                    "cid" => $r["cid"],
                    "stock_id" => $r["stock_id"],
                ];
                $org = $r["org"];
            }
            $result = check_minus_count_custom(["data" => $data, "org" => $org, "module" => "realization", "pid" => $id, "mod" => "all"]);
        }
        return $result;
    }
}