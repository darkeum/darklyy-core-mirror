<?php

function notif_overdue_sales(){
    global $db;
    $notif_send = new NotifSend();

    $query = $db->query("SELECT s.*, sd.s_name, sd.s_sku, sd.id as sd_id, so.date_end 
    FROM `".DB_PREFIX."_supply_order_detail` as sod 
    LEFT JOIN `".DB_PREFIX."_supply_order` as so ON(so.id = sod.supply_o) 
    LEFT JOIN `".DB_PREFIX."_sales_detail` as sd ON(sod.sale_detail_id = sd.id) 
    LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sd.sales_id) 
    LEFT JOIN `".DB_PREFIX."_notif_send` as ns ON(sd.id = ns.pid) 
    LEFT JOIN `".DB_PREFIX."_receipts_detail` as rd ON(rd.supply_detail_id = sod.id) 
    LEFT JOIN `".DB_PREFIX."_receipts` as r ON(r.id = rd.rid) 
    WHERE ns.id IS NULL 
    AND so.date_end IS NOT NULL 
    AND so.date_end < '".date("Y-m-d")."' 
    AND s.end = '0'
    AND s.uid IS NOT NULL
    AND s.uid != '0'
    AND (r.active = '0' OR r.id IS NULL) 
    ");

    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            if(!empty($r["date_end"]) && !empty($r["uid"])){
                $attr = [];
                $attr['uid'] = $r["uid"];
                $attr['title'] = 'Просрочен срок поставки!';
                $attr['text'] = "Просрочен срок поставки товара ".$r["s_name"]."(".$r["s_sku"].") в продаже №" . $r["numb"];
                $attr['icon'] = config('app.url').'/files/push/comment.png';
                $attr['url'] = config('app.url').'/administration/module/sales/order/' . $r["id"];
                $attr['pNotif'] = true;
                $attr['pPush'] = true;
                $attr['module'] = "sales";
                $attr['pid'] = $r["id"];
                $nid = sendAllNotif($attr);
    
                $data = ["nid" => $nid, "pid" => $r["sd_id"], "module" => "sales", "type" => "sales_expired"];
                $notif_send->add($data);
            }
        }
    }
}

//notif_overdue_sales();
$iteration = getCronIteration();
if($iteration == 780){
    notif_overdue_sales();
}