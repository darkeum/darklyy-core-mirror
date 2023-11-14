<?php

namespace Sales\Support;

class SalesAssemblySupport{

    static function get_count($sales_detail_id){
        $data = new \stdClass;
        $data = [];
        foreach(\Sales\Model\SalesDetail::with("sales")->whereIn("id", $sales_detail_id)->get() as $item){
            $assembly_count = \Storage\Assembly::get_count($item->id);
            $count = $item->count - $assembly_count;
            $ndr = get_ndr(["cid" => $item->cid, "quantity" => "",  "stock_id" => $item->sales->stock_id, "module" => "sales", "pid" => $item->id, "returnType" => "array"]);
            $count_d = $ndr["d"] + $ndr["r_this"];
            if($count_d < $count){
                if($count_d == 0){
                    $count = 0;
                } else {
                    $count = $count_d;
                }
            }
            if($count < 0) $count = 0;

            $element = new \stdClass;
            $element->sales_detail = $item;
            $element->count = $count;
            $data[] = $element;
        }
        return $data;
    }

    static function create($sales_id, $sales_detail_id, $options = []){
        // ["count"]
        $count_list = $options["count"] ?? [];
        $data = [];
        $sales_detail = [];

        foreach(\Sales\Model\SalesDetail::whereIn("id", $sales_detail_id)->get() as $item){
            $count =  $count_list[ $item->id ] ?? 0;
            if($count > 0){
                $data[ $item->id ] = ["count" => $count];
                $sales_detail[] = $item;
            }
        }

        $result = ["assembly" => null, "assembly_detail" => []];

        if(count($data) > 0){
            $assembly = \Storage\Model\StorageAssembly::create(["sales_id" => $sales_id, "type" => 1]);
            $result["assembly"] = $assembly;
            $iid = $assembly->id;

            foreach($data as $key => $item){
                $sad = \Storage\Model\StorageAssemblyDetail::create(["said" => $iid, "sdid" => $key, "count_save" => $item["count"], "status" => 1]);
                $result["assembly_detail"][] = $sad;
                setLog("info", $sad->id, "assembly", 'Добавление товара на сборку №'.$sad->said.'; id - '.$sad->id.'; продажа №'.$sales_id.'; id товара продажи '.$sad->sdid.'; количество - '.$sad->count);
            }

            $s_status = 8;
            
            foreach($sales_detail as $item){
                $item->update(["status" => $s_status]);

                \Sales\Model\SalesDetailStatusLog::create([
                    "sales_detail_id" => $item->id, 
                    "status" => $s_status, 
                    "url" => "/administration/module/storage/assembly/detail/".$iid,
                ]);
            }

            $uid_group = getGroup(["group" => "storage"]);
            //$uid_group[] = 33;
            $attr = [];
            $attr['uid'] = $uid_group;
            $attr['title'] = 'Новая заявка на сборку №'.$iid;
            $attr['text'] = 'Новая заявка на сборку №'.$iid;
            $attr['icon'] = config('app.url').'/files/push/new_storage.png';
            $attr['url'] = config('app.url').'/administration/module/storage/assembly/detail/'.$iid;
            $attr['pNotif'] = true;
            $attr['pPush'] = true;	
            $attr['module'] = "assembly";
            $attr['pid'] = $iid;		
            sendAllNotif($attr);
        }

        return $result;
    }
}