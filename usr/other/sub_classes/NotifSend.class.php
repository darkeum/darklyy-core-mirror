<?php

class NotifSend{
    public function add($data){
        global $db;
        //["nid" => "", "pid" => "", "module" => "", "type" => ""];
        $date = date("Y-m-d H:i:s");
        $db->query("INSERT INTO ".DB_PREFIX."_notif_send (`nid`, `pid`, `module`, `type`, `date`) VALUES ('".$data["nid"]."', '".$data["pid"]."', '".$data["module"]."', '".$data["type"]."', '".$date."')");
    }

    public function find($data){
        global $db;
        //["return_type" => "", "pid" => "", "module" => "", "type" => ""];
        $data_true = ["pid", "module", "type"];
        if(is_array($data) && count($data) > 0){
            $where = [];
            foreach($data as $key => $val){
                if(in_array($key, $data_true)){
                    $where[] = "'".$key."' = '".$val."'";
                }
            }
            $where_str = implode(", ", $where);
            
            $return_type = "";
            if(array_key_exists("return_type", $data)){
                $return_type = $data["return_type"];
            }

            $query = $db->query("SELECT * FROM ".DB_PREFIX."_notif_send WHERE ".$where_str);
            switch($return_type){
                default:
                    $arr = [];
                    if($db->numRows($query) > 0){
                        while($r = $db->getRow($query)){
                            $arr[] = $r;
                        }
                    }
                    return $arr;
                break;
                case "count":
                    return $db->numRows($query);
                break;
                case "above_zero":
                    return $db->numRows($query) > 0;
                break;
            }
        }
    }
}