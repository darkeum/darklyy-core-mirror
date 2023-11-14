<?php
    global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["storageUpdateProducts", "saveByAvailability", "storageTransferRemove", "transferSendStorage", "getDocShipStorage", "setReservStatusStorage", "removeTransfer", "delAllStorage", "getListStorageTransfer", "changeTransferDetail", "sendNotifStorage", "checkStockTransfer", "checkFileTransfer", "saveFileTransfer", "delFileTransfer", "check_minus_count_transfer", "check_transfer_count", "storage_transfer_sync", "check_active_transfer"]);
    
    function storageUpdateProducts(){
        if(isset($_POST["saveId"], $_POST["saveCount"])){
            $saveId = $_POST["saveId"];
            $saveId = json_decode($saveId, true);
            
            if(is_array($saveId) && count($saveId) > 0){
                foreach($saveId as $key => $val){
                    $data = \Storage\Model\StockTransferDetail::find($key);
    
                    $r = \Catalog_v2\Model\Catalog::find($val)->toArray();
                    
                    $price = \Price::getPrice($val);
                    $price_rub = $price->price_purchase_;
                    
                    $data->update([
                        "cid" => $val, 
                        "s_name" => $r["title"], 
                        "s_sku" => $r["sku"], 
                        "s_unit" => $r["unit"], 
                        "s_price" => $price_rub, 
                    ]);
                }
            }

            $items = [];
            
            $saveCount = $_POST["saveCount"];
            $saveCount = json_decode($saveCount, true);
            if(is_array($saveCount) && count($saveCount) > 0){
                foreach($saveCount as $key => $val){
                    $items[$key]["count"] = $val;
                }
            }
            $savePrice = $_POST["savePrice"] ?? [];
            $savePrice = json_decode($savePrice, true);
            if(is_array($savePrice) && count($savePrice) > 0){
                foreach($savePrice as $key => $val){
                    $items[$key]["s_price"] = $val;
                }
            }

            foreach($items as $id => $item){
                \Storage\Model\StockTransferDetail::find($id)->update($item);
            }
        }
    }
    
    function storageTransferRemove(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
           $id = $_POST["id"];
           \Storage\Model\StockTransferDetail::destroy($id);
        } else echo 'Не хватает данных';
    }
    
    function transferSendStorage(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            require_once(ROOT."/usr/modules/storage/admin/sub/transfer_active.php");
           $id = $_POST["id"];
           transfer_active($id);
        } else echo 'Не хватает данных';
    }

    function getDocShipStorage(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["docNum"])){
            $i = 0;
           $docNum = $_POST["docNum"];
           $query = $db->query("SELECT id, realization  FROM JMY_sales WHERE id = '".$docNum."' AND allow_shipment = '1'");
           if($db->numRows($query) > 0){
               $i++;
			   $rrr= $db->getRow($query);
               echo '<div style="margin-bottom: 20px;"><a href="https://atri-energo.ru/administration/module/realization/order/'. $rrr['realization'].'" style="color: black;">Распечатать документ <b>продажи</b> №-'.$docNum.'</a></div>';
           }
           $query = $db->query("SELECT id FROM JMY_stock_transfer WHERE id = '".$docNum."'");
           if($db->numRows($query) > 0){
                $i++;
                echo '<div style="margin-bottom: 20px;"><a href="/administration/module/storage/print/transfer/'.$docNum.'" style="color: black;">Распечатать документ <b>перемещения</b> №-'.$docNum.'</a></div>';
            }
           if($i == 0){
                echo 'Документы отсутствуют';
           }
        } else echo 'Не хватает данных';
    }

    function setReservStatusStorage(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"], $_POST["mainId"])){
            $id = $_POST["id"];
            $mainId = $_POST["mainId"];
            if(is_array($id)){
                foreach($id as $val){
                    $db->query("UPDATE `JMY_storage_detail` SET status = '2' WHERE id = '".$val."' LIMIT 1");
                }
            } else {
                $db->query("UPDATE `JMY_storage_detail` SET status = '2' WHERE id = '".$id."' LIMIT 1");
            }

            $query = $db->query("SELECT status FROM JMY_storage_detail WHERE storage_id = '".$mainId."' AND status = '1'");
            if($db->numRows($query) == 0){
                $db->query("UPDATE JMY_storage SET status = '2' WHERE id = '".$mainId."' LIMIT 1");
            }
        } else echo 'Не хватает данных';
    }
    
    function removeTransfer(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            // $db->query("DELETE FROM JMY_stock_transfer WHERE id = '".$id."'");
            $db->query("UPDATE `".DB_PREFIX."_stock_transfer` SET del = '1' WHERE id = '".$id."' LIMIT 1");
        } else echo 'Не хватает данных';
    }
    
    function delAllStorage(){
        global $db, $allowFuncs, $url, $core;
        $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;
        if($checks){
            $str = implode(', ', $checks);
            $db->query("DELETE FROM `JMY_storage_detail` WHERE id IN(".$str.")");
        }
    }

    function getListStorageTransfer(){
        global $db, $allowFuncs, $url, $core;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $data = \Storage\Model\StockTransfer::find($id)->toArray();
        $request = [];
        $request["org"] = $data["org"];
        $array = get_stock_data($request);
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    
    function changeTransferDetail(){
        if(isset($_POST["pk"], $_POST["name"], $_POST["value"])){
            $pk = $_POST["pk"];
            $name = $_POST["name"];
            $value = $_POST["value"];

            $StockTransfer = \Storage\Model\StockTransfer::find($pk);

            $request = [];

            if($name == "org"){
                $numb_array = \Boot\Support\Module::numb('stock_transfer', $value, $StockTransfer->date);
                $numb = $numb_array['numb'];
                $numb_counter = $numb_array['numb_counter'];
                $request["numb"] = $numb;
                $request["numb_counter"] = $numb_counter;

                $data = get_stock_data(["org" => $value]);
                $request["stock_out"] = "";
                $request["stock_in"] = "";
                if(isset($data[0])){
                    $request["stock_out"] = $data[0]["value"];
                    if(isset($data[1])){
                        $request["stock_in"] = $data[1]["value"];
                    } else {
                        $request["stock_in"] = $data[0]["value"];
                    }
                }
            }

            $request[ $name ] = $value;
            $StockTransfer->update($request);
        }
    }

    function sendNotifStorage(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $uid = $core->auth->user_info['id'];
            $attr = [];
            $attr['uid'] = [35]; //35
            $attr['title'] = fullUserName($uid).' создал новую заявку на перерасчет №'.$id;
            $attr['text'] = fullUserName($uid).' создал новую заявку на перерасчет №'.$id;
            $attr['icon'] = config('app.url').'/files/push/task.png';
            $attr['url'] = config('app.url').'/administration/module/storage/detail/'.$id;
            $attr['module'] = "storage";
            $attr['pid'] = $id;
            sendAllNotif($attr);

            $db->query("UPDATE JMY_storage SET send_notif_uid = '".$uid."' WHERE id = '".$id."' LIMIT 1");
        }
    }

    function checkStockTransfer(){
        global $db, $allowFuncs, $url, $core;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        if($id){
            $query = $db->query("SELECT stock_out, stock_in FROM JMY_stock_transfer WHERE id = '".$id."' LIMIT 1");
            $r = $db->getRow($query);
            if(empty($r["stock_out"])){
                echo 'Склад отправления не указан';
            }
            if(empty($r["stock_in"])){
                echo 'Склад назначения не указан';
            }
            if((int)$r["stock_out"] === (int)$r["stock_in"]){
                echo 'Склад отправления равен складу назначения!';
            }
        }
    }
    
    function checkFileTransfer(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `module` = 'transfer' AND `pid` = '".$id."'");
            if($db->numRows($query) > 0){
                $val = 1;
            } else {
                $val = 0;
            }
            $db->query("UPDATE ".DB_PREFIX."_stock_transfer SET scan = '".$val."' WHERE id = '".$id."' LIMIT 1");
        }
    }

    function saveFileTransfer(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $file = isset($_POST["file"]) ? $_POST["file"] : false; 

            $attribute = [];
            $attribute["name"] = "5";
            $attribute["file"] = $file;
            $attribute["module"] = 'transfer';
            $attribute["mod"] = 'transfer';
            $attribute["id"] = $id;
            $attribute["comm_id"] = $id;
            uploaderMaster("download", $attribute);
        }
    }

    function delFileTransfer(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE id = '".$id."' LIMIT 1");
            $r = $db->getRow($query);
            unlink($r["url"]);
            $db->query("DELETE FROM ".DB_PREFIX."_attach WHERE id = '".$id."'");
        } 
    }	

    function check_minus_count_transfer(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $query = $db->query("SELECT std.*, st.stock_out 
            FROM `".DB_PREFIX."_stock_transfer_detail` as std 
            LEFT JOIN `".DB_PREFIX."_stock_transfer` as st ON(st.id = std.transfer_id) 
            WHERE std.transfer_id = '".$id."'");
            if($db->numRows($query)){
                $data = [];
                while($r = $db->getRow($query)){
                    if(empty($r["sdid"])){
                        $pid = $r["id"];
                        $module = "transfer";
                    } else {
                        $pid = $r["sdid"];
                        $module = "sales";
                    }
                    $data[$r["id"]] = [
                        "pid" => $pid,
                        "module" => $module,
                        "count" => $r["count"],
                        "cid" => $r["cid"],
                        "stock_id" => $r["stock_out"]
                    ];
                }
                $uid = $core->auth->user_info['id'];
                // if($uid == 33){
                //     dd($data);
                // }
                
                // echo check_minus_count($data);
                echo check_minus_count_custom(["data" => $data, "module" => "transfer", "pid" => $id]);
            }
        }
    }
    
    function check_transfer_count(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $query = $db->query("SELECT sd.count as sd_count, std.* 
            FROM `".DB_PREFIX."_stock_transfer_detail` as std 
            LEFT JOIN `".DB_PREFIX."_sales_detail` as sd ON(std.sdid = sd.id)
            WHERE std.transfer_id = '".$id."' AND std.sdid IS NOT NULL");
            $error = [];
            if($db->numRows($query) > 0){
                while($r = $db->getRow($query)){
                    $sdid = $r["sdid"];
                    $count_transfer = $r["count"];
                    $count_sales = $r["sd_count"];
                    $count_check = $count_sales - $count_transfer;

                    $st = $db->query("SELECT std.count 
                    FROM `".DB_PREFIX."_stock_transfer_detail` as std
                    LEFT JOIN `".DB_PREFIX."_stock_transfer` as st ON(st.id = std.transfer_id)
                    WHERE std.sdid = '".$sdid."' AND st.active = '1'");
                    if($db->numRows($st) > 0){
                        while($s = $db->getRow($st)){
                            $count_check -= $s["count"];
                        }
                    }
                    if($count_check < 0){
                        $error[] =  $r["id"];
                    }
                }
            }
            if(count($error) > 0){
                echo implode("; ", $error);
            }
        }
    }
	
	function storage_transfer_sync(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST['transfer_id']))
		{
			$transfer_id = $_POST['transfer_id'];	
            $query = $db->query("SELECT * FROM JMY_stock_transfer WHERE id='".$transfer_id."' LIMIT 1");
			if($db->numRows($query) > 0) 
			{	
				$transfer = $db->getRow($query);           
				$sync = new \Sync\Sync;
$sync->syncRegister('stock_transfer', $transfer_id, '1', $transfer['org']);
				echo '1';
			}
			else
			{
				echo '2';
			}
        } else echo '2';
    }
    
    function check_active_transfer(){
        global $db;
        $request = $_POST;
        $id = empty($request["id"]) ? false : $request["id"];
        $active = "fail";
        if($id){
            $query = $db->query("SELECT active FROM `".DB_PREFIX."_stock_transfer` WHERE id = '".$id."' LIMIT 1");
            if($db->numRows($query) > 0){
                $r = $db->getRow($query);
                $active = $r["active"];
            }
        }
        echo $active;
    }
    
    
    
    
    
    
