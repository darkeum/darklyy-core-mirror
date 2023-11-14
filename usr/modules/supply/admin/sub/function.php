<?php

//require_once(ROOT."usr/modules/supply/admin/sub/function.php");

//old_function
function send_notif_change_date2($id){
    global $db;
    $sales_id = get_sales_for_supply_o($id);
    if(count($sales_id) > 0){
        $sales_id_str = implode(", ", $sales_id);
        $query = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id IN(".$sales_id_str.")");
        while($r = $db->getRow($query)){
            $attr = [];
            $attr['uid'] = $r["uid"];
            $attr['title'] = 'Изменение даты в продаже №'.$r["numb"];
            $attr['text'] = 'Изменение даты в продаже №'.$r["numb"];
            $attr['icon'] = config('app.url').'/files/push/comment.png';
            $attr['url'] = config('app.url').'/administration/module/sales/order/' . $r["id"];
            $attr['pNotif'] = true;
            $attr['pPush'] = true;
            $attr['module'] = "sales";
            $attr['pid'] = $r["id"];
            sendAllNotif($attr);
        }
    }
}

function send_notif_change_date($id){
    global $db;
    $query = $db->query("SELECT sd.*, s.uid, s.numb, s.uid
    FROM `".DB_PREFIX."_supply_order_detail` as sod 
    LEFT JOIN `".DB_PREFIX."_sales_detail` as sd ON(sod.sale_detail_id = sd.id)
    LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sd.sales_id)
    WHERE sod.supply_o = '".$id."' AND sod.sale_detail_id IS NOT NULL
    ");
    if($db->numRows($query) > 0){
        $sales_id = [];
        $sales_tov = [];
        $sales_tov_id = [];
        while($r = $db->getRow($query)){
            $sales_id[] = $r["sales_id"];
            $sales_tov[$r["sales_id"]][] = $r["s_name"];
            $sales_tov_id[$r["sales_id"]][] = $r["id"];
            $sales_uid[$r["sales_id"]] = $r["uid"];
            $sales_numb[$r["sales_id"]] = $r["numb"];
        }
        $sales_id_unique = array_unique($sales_id);
        $get_name = "&supply_o_tov[]=";
        foreach($sales_id_unique as $s_id){
            $attr = [];
            $attr['uid'] = $sales_uid[ $s_id ];
            $attr['title'] = 'Изменение даты в продаже №'.$sales_numb[ $s_id ];
            $attr['text'] = 'Изменение даты в продаже №'.$sales_numb[ $s_id ].". (Товары: ".implode(', ', $sales_tov[ $s_id ]).")";
            $attr['icon'] = config('app.url').'/files/push/comment.png';
            $attr['url'] = config('app.url').'/administration/module/sales/order/' . $s_id.$get_name.implode($get_name, $sales_tov_id[ $s_id ]);
            $attr['pNotif'] = true;
            $attr['pPush'] = true;
            $attr['module'] = "sales";
            $attr['pid'] = $s_id;
            sendAllNotif($attr);
        }
    }
}

function auto_supply_o_total($supply_o){
	global $db;
	$query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_order_detail` WHERE supply_o = '".$supply_o."'");
	$total = 0;
	$nds = 0;
	while($r = $db->getRow($query)){
		$total += $r["total"];
		$nds += $r["nds"];
	}
	$db->query("UPDATE `".DB_PREFIX."_supply_order` SET total = '".$total."',  nds = '".$nds."' WHERE id = ".$supply_o." LIMIT 1");
}