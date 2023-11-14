<?php

function add($data){
    if(is_array($data)){
        $require_data = ["name", "address"];
        $flag = true;
        foreach($data as $key => $val){
            if(!in_array($key, $require_data) && !empty($val)){
                $flag = false;
                break;
            }
        }
    
        if($flag){
            $object = \Object\Model\ObjectModel::create([
                "uid" => \Boot\Support\Facades\Auth::id(),
                "date" => time(),
                "address" => $data["address"],
                "name" => $data["name"],
                "status" => 1,
            ]);
            return $object->id;
        } else {
            return 'error';
        }
    } else {
        return 'error';
    }
}