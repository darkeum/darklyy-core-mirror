<?php
    global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["loadObjectPanel", "economyBlockList"]);
	
	$dir = ROOT.'/usr/modules/object/admin/sub_ajax/';  
	includeSub($dir);
    
	function loadObjectPanel(){
		if(isset($_POST["id"], $_POST["type"]))
		{
			$id = intval($_POST["id"]);
			$type = filter($_POST["type"]);
			switch($type)
			{
				default:
					//content_main();
					echo 'основной блок ';
				break;	
				case "economyBlock":
					//echo 'Экономический блок';
					economyBlock($id);
				break;
				case "relationBlock":
					//echo 'Экономический блок';
					relationBlock($id);
				break;
			}
		}
	}
	/*
    function getIdProductStorage(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"], $_POST["parentId"], $_POST["num"], $_POST["count"])){
            $id = $_POST["id"];
            $parentId = $_POST["parentId"];
            $num = $_POST["num"];
            $count = $_POST["count"];
            $query = $db->query("SELECT c.*, cu.name as cuname FROM JMY_catalog as c LEFT JOIN JMY_catalog_unit as cu ON(cu.unit = c.unit) WHERE c.id = '".$id."' LIMIT 1");
            $content = $db->getRow($query);
            
            echo '
			<tr data-id="'.$parentId.'">
            <td data-toggle="tooltip" title="' . $parentId . '">' . $num . '</td>
            <td>' . $content['sku'] . '</td>
            <td class="changeGoods cp" id="cg_'.$parentId.'">' . $content['title'] . '</td>
            <td class="count cp">' . $count . '</td>				
            <td>0 руб.</td>				
            <td>' . $content['cuname'] . '</td>		
            <td class="text-center">0/0/0</td>		
            
            <td class="w200 text-right">				
            <div class="btn-group" >';
            echo   '<button type="button" onclick="modal_o(\'#modal-compl-'.$parentId.'\');" class="btn btn-xs btn-danger">X</button>
            ';
            
            echo '				
            
            </div>
            
            </td>
            <td class="w20 text-right">
            <div class="checkbox-custom mb15">
            <input id="checkbox' . $parentId . '" type="checkbox" name="checks[]" class="checks" value="' . $parentId . '">
            <label for="checkbox' . $parentId . '"></label>
            </div>
            </td>
			</tr>';
        }
    }
    
    function storageUpdateProducts(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["saveId"], $_POST["saveCount"])){
            $saveId = $_POST["saveId"];
            $saveId = json_decode($saveId, true);
            
            foreach($saveId as $key => $val){
                $query = $db->query("SELECT * FROM JMY_catalog WHERE id = ".$val);
                $r = $db->getRow($query);
                $db->query("UPDATE JMY_stock_transfer_detail SET cid = '".$val."', s_name = '".$r["title"]."', s_sku = '".$r["sku"]."', s_unit = '".$r["unit"]."', s_price = 0 WHERE id = '".$key."'");
            }
            
            $saveCount = $_POST["saveCount"];
            $saveCount = json_decode($saveCount, true);
            
            foreach($saveCount as $key => $val){
                $db->query("UPDATE JMY_stock_transfer_detail SET count = '".$val."' WHERE id = '".$key."'");
            }
            
        }
    }
    
    function storageTransferRemove(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
           $id = $_POST["id"];
           $db->query("DELETE FROM JMY_stock_transfer_detail WHERE id = '".$id."' LIMIT 1");
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
            $db->query("DELETE FROM JMY_stock_transfer WHERE id = '".$id."'");
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

        $array = [];
        $query = $db->query("SELECT * FROM JMY_stock WHERE name IS NOT NULL");
        while($r = $db->getRow($query)){
            $array[] = [
                "value" => $r["id"],
                "text" => $r["name"]
            ];
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    
    function changeTransferDetail(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["pk"], $_POST["name"], $_POST["value"])){
            $pk = $_POST["pk"];
            $name = $_POST["name"];
            $value = $_POST["value"];

            $db->query("UPDATE ".DB_PREFIX."_stock_transfer SET ".$name." = '".$value."' WHERE id = '".$pk."' LIMIT 1");
        }
    }
    
    
    */
    
    
    
    
    
    
