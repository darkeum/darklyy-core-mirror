<?php

//require_once("usr/modules/catalog_v2/admin/sub/notif.php");

function catalog_change_init($data){
    global $db, $core;
    //cid, type
    $uid_group = getGroup(["group" => "catalog"]);
    $uid = $core->auth->user_info['id']; 
    if(!in_array($uid, $uid_group)){
        notif_create_product($data["cid"], $data["type"]);
        append_change_log($data["cid"], $data["type"]);
    }
}

function notif_create_product($cid, $type){
    global $db;

    $uid = getGroup(["group" => "catalog"]);
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '".$cid."' LIMIT 1");
    if($db->numRows($query) > 0){
        $catalog = $db->getRow($query);
        $attr = [];
        $attr['uid'] = $uid;
        
        switch($type){
            default:
            case "1":
            $attr['title'] = 'Создана новая номенклатура';
            $attr['text'] = 'Создана новая номенклатура. Id -> '.$cid.'; наименование -> '.$catalog["title"].'; артикул -> '.$catalog["sku"].'; создал -> '.shortUserName($catalog["uid"]).'; дата -> '.(date("d.m.Y H:i:s", $catalog["date"])).'; ';
            break;
            case "2":
            $attr['title'] = 'Изменена номенклатура';
            $attr['text'] = 'Изменена номенклатура. Id -> '.$cid.'; наименование -> '.$catalog["title"].'; артикул -> '.$catalog["sku"].'; создал -> '.shortUserName($catalog["uid"]).'; дата -> '.(date("d.m.Y H:i:s", $catalog["date"])).'; ';
            break;
        }
        
        $attr['icon'] = config('app.url').'/files/push/new_order.png';
        $attr['url'] = config('app.url').'/administration/module/catalog_v2/edit/'.$cid;
        $attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "catalog_v2";
        $attr['pid'] = $cid;
        sendAllNotif($attr);
    }
}

function append_change_log($cid, $type){
    global $db, $core;
    $uid = $core->auth->user_info['id']; 
    $date = date("Y-m-d H:i:s");
    $db->query("INSERT INTO ".DB_PREFIX."_catalog_change_log (cid, uid, date, type) VALUES ('".$cid."', '".$uid."', '".$date."', '".$type."')");
    $iid = $db->insert_id();
    return $iid;
}