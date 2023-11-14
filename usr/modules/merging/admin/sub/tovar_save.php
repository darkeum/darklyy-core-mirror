<?php

function tovar_add_main($data){
    global $db;
    $allow_data = ["id","cid"];
    foreach($allow_data as $val){
        if(!isset($data[$val])){
            return false;
        }
    }
    $id = $data["id"];
    $query = $db->query("SELECT * FROM `".DB_PREFIX."_merging_detail` WHERE merging_id = '".$id."' AND cid = '".$data["cid"]."'");
    if($db->numRows($query) == 0){
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_catalog` WHERE id = '".$data["cid"]."' LIMIT 1");
        $r = $db->getRow($query);
    
        $db->query("UPDATE `".DB_PREFIX."_merging` SET cid = '".$data["cid"]."', name = '".$r["title"]."', unit = '".$r["unit"]."', sku = '".$r["sku"]."' WHERE id = '".$id."' LIMIT 1");
    }
}

// function tovar_add($data){
//     global $db;
//     $allow_data = ["id","cid"];
//     foreach($allow_data as $val){
//         if(!isset($data[$val])){
//             return false;
//         }
//     }
//     $id = $data["id"];
//     $cid_str = implode(", ", $data["cid"]);
//     $count_values = array_count_values ($data["cid"]);
//     $query = $db->query("SELECT * FROM `".DB_PREFIX."_merging` WHERE id = '".$id."' LIMIT 1");
//     $merging = $db->getRow($query);
//     $query = $db->query("SELECT * FROM `".DB_PREFIX."_catalog` WHERE id IN (".$cid_str.")");
//     if($db->numRows($query) > 0){
//         // $md = [];
//         $MergingDetail = new \Merging\MergingDetail;
//         while($r = $db->getRow($query)){
//             $count = isset($data["count_".$r["id"]]) ? $data["count_".$r["id"]] : 1;
//             if($merging["cid"] != $r["id"]){
//                 $for_count = $count_values[$r["id"]];
//                 // if(empty($data["price"][ $r["id"] ])){
//                 //     $priceList = getCatalogPrice(["cid" => $r["id"], "type" => 1]);
//                 //     $price = $priceList["data"]["price"];
//                 // } else {
//                 //     $price = $data["price"][ $r["id"] ];
//                 // }
//                 for($i=0;$i<$for_count;$i++){
//                     $MergingDetail->add(["merging_id" => $id, "cid" => $r["id"], "name" => $r["title"], "unit" => $r["unit"], "sku" => $r["sku"], "count" => $count]);
//                     // $md[] = "('".$id."', '".$r["id"]."', '".$r["title"]."', '".$r["unit"]."', '".$r["sku"]."', '".$count."', '".$price."')";
//                 }
//             }
//         }
//         // if(count($md) > 0){
//         //     $md_str = implode(", ", $md);
//         //     $db->query("INSERT INTO `".DB_PREFIX."_merging_detail` (`merging_id`, `cid`, `name`, `unit`, `sku`, `count`, `price`) VALUES ".$md_str);
//         // }
//     }
// }

function tovar_add($request){
	$id = isset($request["id"]) ? $request["id"] : false;
	$items = isset($request["items"]) ? $request["items"] : [];
	if($id && count($items) > 0){
		foreach($items as $cid => $item){
            $c = \Catalog_v2\Model\Catalog::findOrFail($cid)->toArray();
            \Merging\Model\MergingDetail::create(["merging_id" => $id, "cid" => $cid, "name" => $c["title"], "unit" => $c["unit"], "sku" => $c["sku"], "count" => $item["count"]]);
		}
	}
}