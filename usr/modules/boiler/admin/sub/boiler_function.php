<?php

// require_once(ROOT.'usr/modules/boiler/admin/sub/boiler_function.php');

use Boiler\Model\BoilerBidDataDetail;
use Catalog\Models\Catalog;
use Orgs\Models\Org;

function bid_data_detail_add($request){
    global $db;
    //["id" => "", "goods" => [["cid" => "", "count" => ""],]]
    $id = $request["id"];
    
    $query = $db->query("SELECT bb.org, bb.nds_includes 
        FROM `".DB_PREFIX."_boiler_bid_data` as bbd
        LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id) 
        LEFT JOIN `".DB_PREFIX."_boiler_bid` as bb ON(bb.id = bbt.bid_id)
        WHERE bbd.id = '".$id."' LIMIT 1");
	$r1 = $db->getRow($query);
	$query = $db->query("SELECT id FROM `".DB_PREFIX."_boiler_bid_data_detail` WHERE bbd_id = '".$id."'");
	$position = $db->numRows($query);
	$query = $db->query("SELECT nds FROM `".DB_PREFIX."_org` WHERE id = '".$r1["org"]."' LIMIT 1");
	$r = $db->getRow($query);
    $nds = is_null($r["nds"]) ? 3 :$r["nds"];
    foreach($request["goods"] as $item){
        $position++;
        $cid = $item["cid"];
        $query_stable = $db->query("SELECT * FROM `".DB_PREFIX."_catalog` WHERE id = '". $cid ."' LIMIT 1");
        $tovar_stable = $db->getRow($query_stable);	
        // $sum = (isset($_POST['count_'.$cid]) ? $_POST['count_'.$cid] : 1);
        $sum = isset($item['count']) ? $item['count'] : 1;
        $stable_sku = $tovar_stable['sku'];
        $stable_name = $tovar_stable['title'];
        $unit = empty($tovar_stable['unit']) ? 'null' : "'".$tovar_stable['unit']."'";
        
        $p = \Price::getPrice($cid);
        $price_rub = empty($p->price_list) ? 0 : $p->price_list;
        $price_type = empty($p->price_list_type->id) ? 3 : $p->price_list_type->id;
        $discount = 0;

        $data = \Price::get_all([
            "price" => $price_rub, 
            "count" => $sum, 
            "discount" => $discount,
            "nds_includes" => $r1["nds_includes"], 
            "nds_type" => $nds,
        ]);

        $ndsCalc = $data["nds"];
        $total = $data["total"];

        $str[] = "('" . $db->safesql($id) . "', '" . $db->safesql($cid) . "', '" . $db->safesql($stable_sku) . "', '" . $db->safesql($stable_name) . "', '".$price_type."', '" . $db->safesql($price_rub) . "', ".$unit.", '" . $db->safesql($sum) . "', '".$nds."', '".$ndsCalc."', '" . $db->safesql($total) . "', '".$position."', '".$data["discount"]."')";
    }
    $db->query("INSERT INTO `" . DB_PREFIX . "_boiler_bid_data_detail` (`bbd_id`, `cid`, `s_sku`, `s_name`, `price_type`, `s_price`, `s_unit`, `count`, `type_nds`, `nds`, `total`, `position`, `discount`) VALUES ".implode(", ", $str)."");

    change_all_price_boiler_bid($id);
}

function bid_data_detail_add_model($request){

    //["id" => "", "goods" => [["cid" => "", "count" => ""],]]
    $id = $request["id"];
    $r1 = DB::table('boiler_bid_data')
            ->select('boiler_bid.org', 'boiler_bid.nds_includes')
            ->leftJoin('boiler_bid_type', 'boiler_bid_data.bid_type_id', '=', 'boiler_bid_type.id')
            ->leftJoin('boiler_bid', 'boiler_bid_type.bid_id', '=', 'boiler_bid.id')
            ->where('boiler_bid_data.id', $id)
            ->first();
            // dd($r1);  
    $position = BoilerBidDataDetail::where('bbd_id', $id)->count();	
    $r = Org::find($r1->org);  
    $nds = is_null($r->nds) ? 3 : $r->nds;
    $str = [];
    foreach($request["goods"] as $item){
        $position++;
        $cid = $item["cid"];
        $tovar_stable = Catalog::find($cid);
        $sum = isset($item['count']) ? $item['count'] : 1;
        $stable_sku = empty($tovar_stable->sku) ? null : $tovar_stable->sku;
        $stable_name = empty($tovar_stable->title) ? null : $tovar_stable->title;
        $unit = empty($tovar_stable->unit) ? null : $tovar_stable->unit;        
        $p = \Price::getPrice($cid);
        $price_rub = empty($p->price_list) ? 0 : $p->price_list;
        $price_type = empty($p->price_list_type->id) ? 3 : $p->price_list_type->id;
        $discount = 0;

        $data = \Price::get_all([
            "price" => $price_rub, 
            "count" => $sum, 
            "discount" => $discount,
            "nds_includes" => $r1->nds_includes, 
            "nds_type" => $nds,
        ]);

        $ndsCalc = $data["nds"];
        $total = $data["total"];
        $str[] = [
            'bbd_id' => $id,
            'cid' => $cid,
            's_sku' => $stable_sku,
            's_name' => $stable_name,
            'price_type' => $price_type,
            's_price' => $price_rub,
            's_unit' => $unit,
            'count' => $sum,
            'type_nds' => $nds,
            'nds' => $ndsCalc,
            'total' => $total,
            'position' => $position,
            'discount' => $data["discount"],
        ];       
    }
    BoilerBidDataDetail::insert($str);
}
