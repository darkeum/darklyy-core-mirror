<?php

global $lang;
switch(isset($url[1]) ? $url[1] : null) 
{
    default:
        location('/');
    break;

    case "bid":
        case "print":
            $id = intval($url[3]);
            bid_print($id, $_GET);
            exit;
        break;
    break;
}                

function bid_print($id, $data){
    global $url, $db, $core;
    require_once (ROOT.'usr/modules/sales/admin/sub/print.php');
    $request = [];
    $request["id"] = $id;
	$request["module"] = "boiler";
	$query_static = $db->query("SELECT * FROM `".DB_PREFIX."_boiler_bid` WHERE id = '" . $id . "'");
	$order = $db->getRow($query_static);
	$request["date"] = $order["date_create"];
	$request["org"] = $order["org"];
	$request["oid"] = $order["oid"];
	$order_data = [];
	$query = $db->query("SELECT bbdd.* 
    FROM `".DB_PREFIX."_boiler_bid_data_detail` as bbdd 
    LEFT JOIN `".DB_PREFIX."_boiler_bid_data` as bbd ON(bbd.id = bbdd.bbd_id)
    LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id)
    WHERE bbt.bid_id = '".$id."' AND bbdd.checkbox = '1' 
    ORDER BY bbt.position, bbd.position, bbdd.position"); 
	$org = getOrgInfo($order['org']);	
	if($db->numRows($query) > 0) {
		$i = 0;
        $total_all = 0;
        $nds_all = 0;
		while($catalog = $db->getRow($query)) {
			$i++;
			$stable_name = $catalog['s_name'];
			$stable_sku = $catalog['s_sku'];
			$stable_unit = $catalog['s_unit'];
			$sum = $catalog['count'];
			// $price = round(($catalog['total']/ $catalog['count']), 2);
            // $total_nds = $catalog['total'];
            $discount = isset($data["discount"][ $catalog["id"] ]) ? $data["discount"][ $catalog["id"] ] : $catalog['discount'];
            
            $n_p = \Price::get_all([
                "price" => $catalog['s_price'], 
                "count" => $sum, 
                "discount" => $discount, 
                "nds_includes" => $org["org"]["nds_includes"], 
                "nds_type" => $catalog["type_nds"]
            ]);
			
			// if ($org_nds != '0')
			// {
			// 	$nds = round(($total_nds/120*20), 2);
            //     $total = $total_nds - $nds;
            //     $nds_all = $nds;
			// 	$total_all = $total_all + round($total_nds, 2);
				
			// 	$price_with_nds = round(($catalog['total']/ $catalog['count']), 2);
			// 	$price = round($price_with_nds-($price_with_nds/120*20), 2);
			// }
			// else
			// {
			// 	$price = round(($catalog['total']/ $catalog['count']), 2);
			// 	$price_with_nds = $price;
			// 	$total = $total_nds;
			// 	$total_all = $total_all + round($total, 2);
			// 	$nds = 0;
            // }	
            $price = $n_p["discount_price"];
            $price_with_nds = $n_p["discount_price"];
            $nds = $n_p["nds"];
            $total = $n_p["total"];
            $total_all += $total;
            $nds_all += $nds;
            $total_with_nds = $n_p["total"];
			$date_post = '';
			$order_data[] =  array(
					'n'        			=> $i,
					'sku' 				=> $stable_sku,
					'name'     			=> $stable_name,
					's'     			=> $sum,
					'u'     			=> get_unit('data', $stable_unit),
					'price'     		=> number_format($price,2,',',''),
					'price_with_nds' 	=> number_format($price_with_nds,2,',',''),
					'nds' 				=> number_format($nds,2,',',''),
					'total' 			=> number_format($total,2,',',''),
					'total_with_nds' 	=> number_format($total_with_nds,2,',',''),
					'time'     			=> $date_post,
					'space'    			 => '',
			);
		}
	}
	$request["order_data"] = $order_data;
	$request["total"] = $total_all;
	$request["nds"] = $nds_all;
	$request["uid"] = $order["uid"];
	$request["doc_numb"] = $order["numb"];
	$request["format"] = isset($data["format"]) ? $data["format"] : '';
	$request["type"] = "kp";
	$request["autoprint"] = isset($data["autoprint"]) ? $data["autoprint"] : '';
    universal_print($request);
}