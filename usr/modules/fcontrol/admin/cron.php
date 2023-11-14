<?php
$iteration = getCronIteration();

if($iteration == 840){
    // send_notif_for_lose_plan_pay_date_sales();
    \Control\Support\Control::send_notif_for_lose_plan_pay_date_sales();
    payment_date();
}

function send_notif_for_lose_plan_pay_date_sales(){
    global $db;
    $datediff = "DATEDIFF('".date("Y-m-d")."', c.plan_pay_date)";
    $query = $db->query("SELECT c.*
    FROM JMY_control as c
    LEFT JOIN JMY_supply_order as so ON(so.id = c.supply_o)
    WHERE c.pay = 0 AND c.status = 3 AND c.sales_id IS NOT NULL AND ".$datediff." >= 3 AND ".$datediff." MOD 3 = 0 AND so.close = 0");
    $sales_id = [];
    while($r = $db->getRow($query)){
        // dd($r);
        $sales = get_sales_for_supply_o($r["supply_o"]);
        $sales_id = array_merge($sales_id, $sales);
    }
    $sales_id = array_unique($sales_id);
    if(count($sales_id) > 0){
        $sales_id_str = implode(", ", $sales_id);
        $query = $db->query("SELECT * FROM JMY_sales WHERE id IN(".$sales_id_str.") AND uid NOT IN(61)");
        while($r = $db->getRow($query)){
            $attr = [];
            $attr['uid'] = $r["uid"];
            $attr['title'] = 'Дата оплаты закупки заказа №'.$r["numb"]." просрочена";
            $attr['text'] = 'Дата оплаты закупки заказа №'.$r["numb"]." просрочена";
            $attr['icon'] = config('app.url').'/files/push/new_storage.png';
            $attr['url'] = config('app.url').'/administration/module/sales/order/'.$r["id"];
            $attr['pNotif'] = true;
            $attr['pPush'] = true;	
            $attr['module'] = "sales";
            $attr['pid'] = $r["id"];		
            sendAllNotif($attr);
        }
    }
}

function payment_date(){
    global $db;
    $uid_group = getGroup(["group" => "accountant"]);
    // $uid_group = [33];
    $date = date("Y-m-d");
    $query = $db->query("SELECT * FROM `".DB_PREFIX."_control_payment` WHERE date <= '".$date."'");
    if($db->numRows($query)){
        while($r = $db->getRow($query)){
            $attr = [];
            $attr['uid'] = $uid_group;
            $attr['title'] = 'Частичная оплата заявки №'.$r["control_id"];
            $attr['text'] = 'Частичная оплата заявки №'.$r["control_id"];
            $attr['icon'] = config('app.url').'/files/push/new_storage.png';
            $attr['url'] = config('app.url').'/administration/module/fcontrol/pay_detail/'.$r["control_id"];
            $attr['pNotif'] = true;
            $attr['pPush'] = true;	
            $attr['module'] = "control_payment";
            $attr['pid'] = $r["control_id"];		
            sendAllNotif($attr);
        }
    }
}