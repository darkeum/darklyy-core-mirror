<?php

namespace Storage;

class Assembly{
    static function get_count($sdid){
        $count = 0;
        $items = \Storage\Model\StorageAssemblyDetail::with("storage_assembly")->whereHas("storage_assembly", function($query){
            $query->where("status", 2);
        })->where("sdid", $sdid)->get();
        foreach($items as $item){
            switch((int)$item->storage_assembly->type){
                case 1:
                    $count += $item->count;
                break;
                case 2:
                    $count -= $item->count;
                break;
            }
        }
        return $count;
    }
}