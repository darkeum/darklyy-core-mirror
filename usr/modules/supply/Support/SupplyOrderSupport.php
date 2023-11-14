<?php

namespace Supply\Support;

class SupplyOrderSupport{
    static public function control_sql(){
        return "SELECT * FROM `".\DB::getTablePrefix()."control` WHERE status = '3'";
    }

    static function getAttach($supply_order_id){
        return \Boot\Models\Attach::where("pid", $supply_order_id)->where("module", "supply")->get();
    }

    static public function get_attach($id){
        $attach = [];
        foreach(static::getAttach($id) as $r){
            $attach[] = $r;
        }
        return $attach;
    }
}