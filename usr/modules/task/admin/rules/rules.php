<?php
global $adminTpl, $config, $core, $url, $admin_conf, $db, $lang;
$uid = $core->auth->user_info['id'];
    //все кто есть в задаче, кроме наблюдателей
    function hideEtcUserFunc($task){
        global $adminTpl, $config, $core, $url, $admin_conf, $db, $lang;
        $hideEtcUser = false;
        $uid = $core->auth->user_info['id'];
        $checkArr = [$task["toid"], $task["fromid"]];
        if(!empty($task["coperformerid"])){
            $arr = json_decode($task['coperformerid'], true);
            foreach($arr as $key => $val){
                $checkArr[] = $val;
            }
        }
        /*
        if(!empty($task["observerid"])){
            $arr = json_decode($task['observerid'], true);
            foreach($arr as $key => $val){
                $checkArr[] = $val;
            }
        }
        */
        if(!in_array($uid, $checkArr)){
            $hideEtcUser = true;
        }
        return $hideEtcUser;
    }
    
    //админы
    function hideAdminFunc($type = "status"){
        global $adminTpl, $config, $core, $url, $admin_conf, $db, $lang;
        $uid = $core->auth->user_info['id'];
        $hideAdmin = false;
        $checkArr = [1, 3, 33];
        if(!in_array($uid, $checkArr)){
            $hideAdmin = true;
        }
        switch($type){
            case "status":
                return $hideAdmin;
            break;
            case "array":
                return $checkArr;
            break;
        }
        
    }
    
    
    //ответственный и постановщик
    function hideFromToFunc($task){
        global $adminTpl, $config, $core, $url, $admin_conf, $db, $lang;
        $uid = $core->auth->user_info['id'];
        $hideFromTo = false;
        $checkArr = [$task["toid"], $task["fromid"]];
        if(!in_array($uid, $checkArr)){
            $hideFromTo = true;
        } 
        return $hideFromTo;
    }
       