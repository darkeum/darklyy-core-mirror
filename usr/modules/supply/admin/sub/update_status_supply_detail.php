<?php
//require_once('usr/modules/supply/admin/sub/update_status_supply_detail.php');
function update_status_supply_detail($supply_id){
    global $db;
    $query = $db->query("SELECT status FROM `" . DB_PREFIX . "_supply` WHERE `id` = " . $supply_id . " LIMIT 1");
    if($db->numRows($query) > 0){
        $r = $db->getRow($query);
        if($r["status"] > 0){
            $query_supply = $db->query("SELECT status FROM ".DB_PREFIX."_supply_detail WHERE sid = '".$supply_id."' AND status IN ('1', '4', '10', '11', '12') AND deleted_at IS NULL");		
            if($db->numRows($query_supply) <= 0){
                $status = 2;
            } else {
                $status = 1;
            }
            $db->query("UPDATE `" . DB_PREFIX . "_supply` SET `status` = '".$status."' WHERE `id` = " . $supply_id . " LIMIT 1");
            return $status;
        }
    }
}

function supply_detail_confirm_status(){
    $status = [5, 7];
    return $status;
}

function supply_detail_count_status($count_send, $count){
    $status = 5;
    if($count_send < $count){
        $status = 15;
    }
    return $status;
}