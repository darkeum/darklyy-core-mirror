<?php

class CommentsAdmin{
    public function add($request){
        //["text" => "", "module" => "", "module_id" => ""]
        global $db, $core;
        $id = false;
        if(!empty($request["text"])){
            $uid = empty($request["uid"]) ? $core->auth->user_info['id'] : $request["uid"];
            $date = date("Y-m-d H:i:s");
            $db->query("INSERT INTO `".DB_PREFIX."_comments_admin` (`uid`, `text`, `date`, `module`, `module_id`) VALUES ('".$uid."', '".$request["text"]."', '".$date."', '".$request["module"]."', '".$request["module_id"]."')");
            $id = $db->insert_id();
        }
        return $id;
    }
    public function delete($id){
        global $db;
        if(!empty($id)){
            $db->query("DELETE `".DB_PREFIX."_comments_admin` WHERE id = '".$id."' LIMIT 1");
        }
    }
    public function find($request){
        global $db;
        $result = [];
        if(!empty($request["where"])){
            $query = $db->query("SELECT * FROM `".DB_PREFIX."_comments_admin` WHERE ".$request["where"]);
            if($db->numRows($query) > 0){
                while($r = $db->getRow($query)){
                    $result[] = $r;
                }
            }
        }
        return $result;
    }
}