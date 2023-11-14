<?php

namespace Realization\Support;

class RealizationTorgSupport{
    static function check_torg($request){
        $realization_id = $request["realization_id"];
        return \Realization\Model\RealizationTorg::where("realization_id", $realization_id)->exists();
    }
}