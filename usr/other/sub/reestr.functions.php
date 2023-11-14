<?php

function in_reestr($reestr_id, $reestr){
    global $db;
    $result = [];
    if(count($reestr) > 0 && !empty($reestr_id)){
        $reestr_str = implode(", ", $reestr);
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_reestr_doc` WHERE reestr_id = '".$reestr_id."' AND id IN(".$reestr_str.")");
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                $result[] = $r;
            }
        }
    }
    return $result;
}

function find_doc_in_reestr($reestr_id, $options = []){
    $type = $options["type"] ?? 1;
    $limit = $options["limit"] ?? false;
    $result = [];
    if(!empty($reestr_id)){
        $query = \Reestr\Models\ReestrDoc::where("reestr_id", $reestr_id)->where("type", $type);
        if($limit){
            $query = $query->limit($limit);
        }
        foreach($query->get()->toArray() as $r){
            $result[] = $r;
        }
    }
    return $result;
}