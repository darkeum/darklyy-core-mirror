<?php
	global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["funcApprove", "saveStatusStorageControl", "setPlanPayDateControl"]);
    
    function funcApprove(){
        if(isset($_POST["id"], $_POST["status"], $_POST["comment"])){
            $id = $_POST["id"];
            $status = $_POST["status"];
            $comment = $_POST["comment"];

            \Control\Support\Control::end($id, $status, $comment);
        }
    }
    
    function saveStatusStorageControl(){
        if(isset($_POST["json"], $_POST["mainId"])){
            $json = $_POST["json"];
            $json = json_decode($json, true);
            $mainId = $_POST["mainId"];
            
            $ids = array_keys($json);
            foreach(\Fcontrol\Model\ControlStorageDetail::find($ids) as $item){
                $storage_detail = $item->storage_detail;
                $storage = $storage_detail->storage;

                $status = $json[ $item->id ];
                if($status == 2 && $item->status == 1){
                    $source = [];
                    $source['module'] = 'storage';
                    $source['pid'] = $item->storage_id;
                    $source['comment'] = 'Корректировка остатков #'.module_perfix('storage', $item->storage_id);
                    $action = $storage_detail->count_now > 0 ? "plus" : "minus";
                    updateQuantity($storage_detail->cid, $storage->stock_id, abs($storage_detail->count_now), $action, $source);
                }

                $item->status = $status;
                $item->save();
            }

            if(\Fcontrol\Model\ControlStorageDetail::where("control_storage_id", $mainId)->where("status", 1)->count() == 0){
                $control_storage = \Fcontrol\Model\ControlStorage::find($mainId);
                $control_storage->status = 2;
                $control_storage->save();
            }
        }
    }
    
    function setPlanPayDateControl(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["pk"])){
			$id = $_POST["pk"];
			$date = $_POST["value"];
			$name = $_POST["name"];
			$db->query("UPDATE JMY_control SET `".$name."` = '".$date."' WHERE id = '".$id."' LIMIT 1");
		}
    }