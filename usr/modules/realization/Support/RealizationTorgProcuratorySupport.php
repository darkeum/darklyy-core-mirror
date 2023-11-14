<?php

namespace Realization\Support;

class RealizationTorgProcuratorySupport{
    static function get_procuratory_option_data($request){
        $realization_id = $request["realization_id"];
        $date = $request["date"];

        $r_d = \Realization\Model\Realization::findOrFail($realization_id)->toArray();
        $oid = $r_d["oid"];

        $d = \Realization\Model\RealizationTorg::where("realization_id", $realization_id)->first();
        if(!empty($d)){
            $d = $d->toArray();
        }
        $sel = empty($d["procuratory_id"]) ? null : $d["procuratory_id"];

        $data = \Realization\Model\RealizationTorgProcuratory::where("oid", $oid)->
        whereDate("date_from", "<=", $date)->
        whereDate("date_to", ">=", $date)->
        get()->toArray();
        
        if(count($data) > 0){
            foreach($data as $key => $d){
                $data[$key]["date_from_view"] = date("d.m.Y", strtotime($d["date_from"]));
                $data[$key]["date_to_view"] = date("d.m.Y", strtotime($d["date_to"]));
                $data[$key]["selected"] = $sel == $d["id"];
            }
        }
        return $data;
    }
}