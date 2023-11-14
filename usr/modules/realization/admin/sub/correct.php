<?php

function correctRealization($id, $request = []){
    global $db, $core;
    $query = $db->query("SELECT * FROM `".DB_PREFIX."_realization` WHERE id = '".$id."' LIMIT 1");
    $realization = $db->getRow($query);

    if(empty($request["realization_detail_id"])){
        $where = "rid = '".$id."'";
    } else {
        $realization_detail_id = $request["realization_detail_id"];
        $rdis = implode(", ", $realization_detail_id);
        $where = "id IN(".$rdis.")";
    }

    $query = $db->query("SELECT * FROM `".DB_PREFIX."_realization_detail` WHERE ".$where);
    if($db->numRows($query) > 0 && $realization["active"] == 1){
        $correct_id = correctCreate(["pid" => $id, "module" => "realization", "realization_id" => $id, "org" => $realization["org"]]);
        $values = [];
        while($r = $db->getRow($query)){
            $price_type = empty($r["s_price_type"]) ? 3 : $r["s_price_type"];
            $discount = empty($r["discount"]) ? 0 : $r["discount"];
            $discount_sum = empty($r["discount_sum"]) ? 0 : $r["discount_sum"];
            $values[] = "('".$correct_id."', '".$r["id"]."', '".$r["s_price"]."', '".$r["count"]."', '".$price_type."', '".$discount."', '".$discount_sum."', '".$r["nds"]."', '".$r["nds_type"]."', '".$r["total"]."', '".$r["id"]."')";
        }
        correctDetailCreate($values);
        return $correct_id;
    }
    return false;
}

function correctCorrect($id){
    global $db, $core;
    $query = $db->query("SELECT * FROM `".DB_PREFIX."_correct` WHERE id = '".$id."' LIMIT 1");
    $correct = $db->getRow($query);

    $query = $db->query("SELECT * FROM `".DB_PREFIX."_correct_detail` WHERE correct_id = '".$id."'");
    if($db->numRows($query) > 0 && $correct["active"] == 1){
        $correct_id = correctCreate(["pid" => $id, "module" => "correct", "realization_id" => $correct["realization_id"], "org" => $correct["org"]]);
        $values = [];
        while($r = $db->getRow($query)){
            $price_type = empty($r["s_price_type"]) ? 3 : $r["s_price_type"];
            $discount = empty($r["discount"]) ? 0 : $r["discount"];
            $discount_sum = empty($r["discount_sum"]) ? 0 : $r["discount_sum"];
            $values[] = "('".$correct_id."', '".$r["id"]."', '".$r["s_price"]."', '".$r["count"]."', '".$price_type."', '".$discount."', '".$discount_sum."', '".$r["nds"]."', '".$r["nds_type"]."', '".$r["total"]."', '".$r["realization_detail_id"]."')";
        }
        correctDetailCreate($values);
        return $correct_id;
    }
    return false;
}

function correctCreate($data = []){
    //["pid" => "", "module" => "", "realization_id" => "", "org" => ""]
    global $db, $core;
    $uid = $core->auth->user_info['id'];
    $date_create = date("Y-m-d");
    $date = date("Y-m-d");
    $numb = \Boot\Support\Module::numb('correct', $data["org"], $date);
    $db->query("INSERT INTO `".DB_PREFIX."_correct` (`pid`, `module`, `uid`, `realization_id`, `numb_counter`, `numb`, `org`, `date_create`, `date`) VALUES ('".$data["pid"]."', '".$data["module"]."', '".$uid."', '".$data["realization_id"]."', '".$numb["numb_counter"]."', '".$numb["numb"]."', '".$data["org"]."', '".$date_create."', '".$date."')");
    $iid = $db->insert_id();
    return $iid;
}

function correctDetailCreate($values = []){
    global $db;
    if(count($values) > 0){
        $values_str = implode(", ", $values);
        $db->query("INSERT INTO `".DB_PREFIX."_correct_detail` (`correct_id`, `pid_detail`, `s_price`, `count`, `s_price_type`, `discount`, `discount_sum`, `nds`, `nds_type`, `total`, `realization_detail_id`) VALUES ".$values_str);
    }
}