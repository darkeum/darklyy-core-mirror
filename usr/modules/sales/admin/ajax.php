<?php

use PHP_CodeSniffer\Generators\HTML;

	global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["order_oid_edit", "get_price_item", "save_change_sales", "getSearchListSales", "getCatProductSales", "setPosition", "confirmRealization", "fastChangeSales", "getOrgListSales", "setReserv", "setReservAll", "getReservList", "delReserv", "getReservAllList", "setAllowShipmenSales", "setNewSales", "getCommentSalesDetail", "getInfoOrgSales", "setUserOrgIdFavorite", "listUserOrg", "sendAllMessSales", "changeTitleSales", "priceChangeLocalSales", "datePostLocaSales", "masDelSales", "getPayDetailSales", "checkZeroSum", "checkSumMoreSales", "sendMoveSales", "fixSupply", "check_realization_active", "add_reserv_sales", "del_reserv_sales", "file_request_sales", "sales_sync", "get_status_log", "get_purchase_price_date_sales_ajax", "check_assembly_sales", "sync_assembly_sales", "get_realization_sales", "get_assembly_sales", "get_transfer_sales", "view_supply_order_position", "supply_order_send_control", "get_send_tov_sales", "check_all_realization_and_transfer", "end_sales", "change_sales_field_value", "reestr_load_sales", "check_reestr_create_sales", "get_sales_data", "get_assemply_list_sales", "getStockSales", "changeSalesDetailX", "getPriceTypeSelectDataSales"]);
	
	function sales_sync(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST['sales_id']))
		{
			$sales_id = $_POST['sales_id'];	
            $query = $db->query("SELECT * FROM JMY_sales WHERE id='".$sales_id."' LIMIT 1");
			if($db->numRows($query) > 0) 
			{	
				$sales = $db->getRow($query);           
				// syncRegister('sales', $sales_id, '1', $sales['org']);
                $sync = new \Sync\Sync;
                $sync->syncRegister('sales', $sales_id, '1', $sales['org']);
				echo '1';
			}
			else
			{
				echo '2';
			}
        } else echo '2';
    }
	
	
	function order_oid_edit(){
		if(isset($_POST["oid"], $_POST["id"])){
			$id = $_POST["id"];
			$oid = $_POST["oid"];
            \Sales\Model\Sales::find($id)->update(["oid" => $oid]);
		}
	}	
	
	function get_price_item(){
		global $db, $allowFuncs, $url;
		if(isset($_POST["cid"], $_POST["price_type"])){
			$cid = $_POST["cid"];
            $price_type = $_POST["price_type"];
            $id = isset($_POST["id"]) ? $_POST["id"] : false;
            echo get_price_h($cid, $price_type, $id);
		} else echo 'Не хватает данных';
    }
    
    function get_price_h($cid, $price_type, $id = false){
        if($price_type === "1"){
            if($id){
                $l_price = \Sales\Model\SalesDetail::getPurchase($id);
                return $l_price->price;
            } else {
                $price = \Price::getPrice($cid);
                return $price->price_purchase_;
            }
        } 
        $price = \Price::getPrice($cid);
        return $price->price_list_;
    }
	
	function save_change_sales(){
		global $db, $allowFuncs, $url;
        $result = ["error" => 0];
		if(isset($_POST["json"], $_POST["saveId"])){
			$json = $_POST["json"];
			$json = json_decode($json, true);
            $saveKey = "";

            $reserv = new Reserv();
            
			foreach($json as $key => $val){
                $request = [];
                $flag_count_change = false;
                foreach($val as $field => $value){
                    switch($field){
                        default:
                            $request[ $field ] = $value;
                        break;
                        case "typePrice":
                            $request["price_type"] = $value;
                        break;
                        case "price":
                            $request["s_price"] = $value;
                        break;
                        case "count":
                            $request[ $field ] = $value;
                            $flag_count_change = true;
                        break;
                    }
                }

                $saveKey = $key;

                \Sales\Model\SalesDetail::find($key)->update($request);

                if($flag_count_change && $reserv->check_reserv($key, "sales", 1)){
                    $count = \Sales\Support\SalesSupport::get_count_realization_left($key);
                    $reserv->set_reserv($key, "sales", 1, $count);
                }
            }
            
            $saveId = isset($_POST["saveId"]) ? $_POST["saveId"] : "";
            $saveId = empty($saveId) ? [] : json_decode($saveId, true);

            if(count($saveId) > 0){
                foreach($saveId as $key => $val){
                    $request = [];
                    $r = \Catalog_v2\Model\Catalog::findOrFail($val)->toArray();
                    $ev = \Sales\Model\SalesDetail::find($key)->toArray();
                    foreach($ev as $k => $v){
                        switch($k){
                            default:
                                $request[ $k ] = $v;
                            break;
                            case "cid":
                                $request[ $k ] = $val;
                            break;
                            case "s_name":
                                $request[ $k ] = $r["title"];
                            break;
                            case "s_sku":
                                $request[ $k ] = $r["sku"];
                            break;
                            case "s_unit":
                                $request[ $k ] = $r["unit"];
                            break;
                            case "comment":
                            case "status":
                            case "id":
                            case "del":
                            case "count_realization":
                            case "s_price_local":
                                //пропуск
                            break;
                        }
                    }
                    // $change_sales_id = $sales_detail->add($request);
                    // $sales_detail->delete($key);

                    \Sales\Support\SalesDetailSupport::replace($key, $request);
                }
            }
            
            
			if(!empty($saveKey)){
				$query = $db->query("SELECT sd.sales_id, s.org FROM JMY_sales_detail as sd LEFT JOIN JMY_sales as s ON(s.id = sd.sales_id) WHERE sd.id =".$saveKey." LIMIT 1");
			    $r = $db->getRow($query);

                \Sales\Support\SalesDetailSupport::recount_sales($r["sales_id"]);
                
                
                // syncRegister('sales', $r["sales_id"], '2', $r['org']);
                $sync = new \Sync\Sync;
                $sync->syncRegister('sales', $r["sales_id"], '2', $r['org']);
            }
		}
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
	}
	
	
	function getSearchListSales(){
	global $db, $allowFuncs, $url;
		if(isset($_POST["search"], $_POST["counter"])){
            $search = $_POST["search"];
            $counter = $_POST["counter"];
            $masSearch = explode(" ", $search);
            $str = "";
            foreach($masSearch as $val){
                $str .= "(title LIKE '%".$val."%' OR sku LIKE '%".$val."%') AND ";
            }
            $query = $db->query("SELECT * FROM JMY_catalog WHERE ".mb_substr($str, 0, -4)." LIMIT ".$counter.", 50");
            while($r = $db->getRow($query)){
                echo '<div class="elemList" id="rp_'.$r["id"].'" onselectstart="return false;" onmousedown="return false;">'.$r["title"].'</div>';
            }
            if($db->numRows($query) == 50){
                echo '<div class="loadMore btn btn-info btn-block" onselectstart="return false;" onmousedown="return false;">Ещё</div>';
            }
        }
	}
    
    function setPosition(){
    global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["res"])){
            $res = $_POST["res"];
            $json = json_decode($res);
            
            foreach($json as $key => $val){
                $db->query("UPDATE JMY_sales_detail SET position = ".$val." WHERE id = ".$key);
            }
        }
    }
    /*
    function checkGoodsRealization(){
    global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["checks"])){
            $checks = $_POST["checks"];
            $flag = true;
            $data = "";
            foreach($checks as $val){
                $sales_detail = $db->query("SELECT count, count_realization FROM JMY_sales_detail WHERE id = ".$val);
                $r = $db->getRow($sales_detail);
                if(($r["count"] - $r["count_realization"]) === 0){
                    $flag = false;
                    $data .= $val."; ";
                }
            }
            if($flag){
                echo "good";
            } else {
                echo $data;
            }
        }
    }
    */
    function confirmRealization(){
    global $db, $allowFuncs, $url, $lang, $core;
        if(isset($url[2], $_POST["checks"])){
            $sales_id = $url[2];
            $checks = $_POST["checks"];

            $module = new \Module;
            $data = $module->sales_to_realization($sales_id, ["sales_detail_id" => $checks]);

            echo $data["realization_id"];

            // //проверка
            // $arrayCheck = [];
            // $sumCheck = 0;
            // foreach($checks as $val){
            //     $query = $db->query("SELECT count, count_realization FROM JMY_sales_detail WHERE id = '".$val."' LIMIT 1");
            //     $r = $db->getRow($query);

            //     $raz = $r["count"] - $r["count_realization"];
            //     $arrayCheck[$val] = $raz;
            //     $sumCheck += $raz;
            // }

            // if($sumCheck > 0){
            //     $sales = $db->query("SELECT * FROM JMY_sales WHERE id = ".$url[2]);
            //     $rs = $db->getRow($sales);
            //     $uid = $rs["uid"];
            //     $uid_create = $core->auth->user_info['id'];
            //     $object = empty($rs["object"]) ? 'null' : $rs["object"];
            //     $oid = empty($rs["oid"]) ? 'null' : $rs["oid"];
            //     $org = empty($rs["org"]) ? 'null' : $rs["org"];
            //     $nds = empty($rs["nds"]) ? 0 : $rs["nds"];
            //     $total = empty($rs["total"]) ? 0 : $rs["total"];
            //     $nds_includes =  is_null($rs["nds_includes"]) ? 1 : $rs["nds_includes"];
			// 	$query = $db->query("SELECT id FROM JMY_stock WHERE org = '".$org."' AND position IS NOT NULL ORDER BY position ASC LIMIT 1");
			// 	$stock = $db->getRow($query);
			// 	$stock_id = $stock['id'];
			// 	$numb_array = getNumbCounter('realization', $org);
			// 	$numb = $numb_array['numb'];
			// 	$numb_counter = $numb_array['numb_counter'];
            //     $db->query("INSERT INTO JMY_realization (numb_counter, numb, sales_id, object, uid, uid_create, oid, org, date, date_active, nds, pay, active, status, stock_id, total, nds_includes) VALUES ('".$numb_counter."', '".$numb."', ".$url[2].", ".$object.", '".$uid."', '".$uid_create."', ".$oid.", ".$org.",     '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".$nds.", 0, 0, 1, '".$stock_id."', ".$total.", '".$nds_includes."')");
            //     $rid = $db->insert_id();
			//     relationsSave('sales', $url[2], 'realization', $rid);
                
            //     $totalAll = 0;
            //     $ndsAll = 0;
            //     $price = new Price;
            //     foreach($checks as $val){
            //         if((double)$arrayCheck[$val] === (double)0){
            //             continue;
            //         } else {
            //             $sales_detail = $db->query("SELECT * FROM JMY_sales_detail WHERE id = ".$val);
            //             $rsd = $db->getRow($sales_detail);
            //             $cid = empty($rsd["cid"]) ? 'null' : $rsd["cid"];
            //             $s_name = empty($rsd["s_name"]) ? 'null' : $rsd["s_name"];
            //             $s_sku = empty($rsd["s_sku"]) ? 'null' : "'".$rsd["s_sku"]."'";
            //             $s_price = empty($rsd["s_price"]) ? 0 : $rsd["s_price"];
            //             $s_price_type = empty($rsd["s_price_type"]) ? 3 : $rsd["s_price_type"];
            //             $s_unit = empty($rsd["s_unit"]) ? 'null' : "'".$rsd["s_unit"]."'";
            //             $count = $arrayCheck[$val];
            //             $discount = empty($rsd["discount"]) ? 0 : $rsd["discount"];
            //             $nds_type = is_null($rsd["type_nds"]) ? 3 : $rsd["type_nds"];

            //             $data = $price->get_all([
            //                 "price" => $s_price, 
            //                 "count" => $count, 
            //                 "discount" => $discount, 
            //                 "nds_includes" => $nds_includes, 
            //                 "nds_type" => $nds_type
            //             ]);

            //             $totalAll += $data["total"];
            //             $ndsAll += $data["nds"];

            //             $db->query("INSERT INTO JMY_realization_detail (rid, cid, sdid, s_name, s_sku, s_price, s_price_type, s_unit, count, discount, discount_sum, nds, total, status, nds_type) VALUES (".$rid.", ".$cid.",     ".$val.", '".$s_name."', ".$s_sku.", ".$s_price.", ".$s_price_type.", ".$s_unit.", ".$count.", ".$data["discount_no_round"].", ".$data["discount_sum"].", ".$data["nds"].", ".$data["total"].", 1, '".$nds_type."')");
            //         }
            //     }
            //     $totalAll = round($totalAll, 2, PHP_ROUND_HALF_DOWN);
            //     $ndsAll = round($ndsAll, 2, PHP_ROUND_HALF_DOWN);
            //     $db->query("UPDATE JMY_realization SET total = ".$totalAll.", nds = '".$ndsAll."' WHERE id = '".$rid."' LIMIT 1");
            
            //     echo $rid;
            // }
            
			
        }
    }
    
    function fastChangeSales(){
        if(isset($_POST["pk"])){
            \Sales\Support\SalesSupport::fast_change_sales(
                $_POST["pk"], 
                $_POST["name"], 
                $_POST["value"]
            );
        } else echo 'Не хватает данных';
    }
    
    function getOrgListSales(){
        global $db, $allowFuncs, $url, $lang, $core;
        $query = $db->query("SELECT o.*, org.id as orgid FROM JMY_org as org LEFT JOIN JMY_organizations as o ON(o.id = org.oid)");
        $res = [];
        $i = 0;
        while($r = $db->getRow($query)){
            $res[$i]['value'] = $r['orgid'];
            $res[$i]['text'] = htmlspecialchars_decode ($r['name']);
            $i++;
        }
        $res = json_encode($res);
        echo $res;
    }

    function setReserv(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["pid"], $_POST["cid"], $_POST["count"], $_POST["module"], $_POST["stock_id"])){
            /*
            $reserv = new Reserv();
            $reserv->setReserv([
                "pid" => $_POST["pid"], 
                "module" => $_POST["module"], 
                "count" => $_POST["count"], 
                "cid" => $_POST["cid"], 
                "stock_id" => $_POST["stock_id"]
            ]);
            */
        } else echo 'Не хватает данных';
    }

    function setReservAll(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["id"], $_POST["module"], $_POST["stock_id"])){
            /*
            $reserv = new Reserv();
            $reserv->setReservAll([
                "id" => $_POST["id"], 
                "module" => $_POST["module"], 
                "stock_id" => $_POST["stock_id"]
            ]);
            */
        }
        
    }
    
    function getReservList(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["pid"], $_POST["module"], $_POST["stock_id"], $_POST["uid"])){
            $pid = $_POST["pid"];
            $module = $_POST["module"];
            $stock_id = $_POST["stock_id"];
            $uid = $_POST["uid"];
            $uidThis = $core->auth->user_info['id'];
            $query = $db->query("SELECT id, count, date, uid FROM JMY_stock_reserv WHERE module = '".$module."' AND pid = '".$pid."' AND status != '0'");
            while($r = $db->getRow($query)){
                $disabled = 'disabled';
                if($uidThis == $uid || $uidThis == $r["uid"]){
                    $disabled = '';
                }
                echo '<div class="checkbox"><label><input type="checkbox" value="1" class="reservCheckDel" data-id="'.$r["id"].'" '.$disabled.'>Резерв в количестве <b>'.$r["count"].'</b> шт. от <b>'.date("d.m.Y H:i:s", strtotime($r["date"])).'</b>, '.shortUserName($r["uid"]).'</label></div>';
            }
        } else echo 'Не хватает данных';
    }

    function delReserv(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["id"])){
            //$reserv = new Reserv();
            //$reserv->delReserv(["id" => $_POST["id"]]);
        } else  echo 'Не хватает данных';
    }

    function getReservAllList(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["cid"])){
            $cid = $_POST["cid"];
            $query = $db->query("SELECT id, module, pid, count, date, uid, stock_id 
            FROM `".DB_PREFIX."_stock_reserv` 
            WHERE cid = '".$cid."' AND status = '1' AND deleted_at IS NULL
            ORDER BY id, stock_id");
            $array = [];
            if($db->numRows($query) > 0){
                while($r = $db->getRow($query)){
                    switch($r["module"]){
                        case "sales":
                            $qs = $db->query("SELECT sd.sales_id, s.numb
                            FROM `".DB_PREFIX."_sales_detail` as sd 
                            LEFT JOIN `".DB_PREFIX."_sales` as s ON(s.id = sd.sales_id) 
                            WHERE sd.id = '".$r["pid"]."'");

                            $qsr = $db->getRow($qs);

                            if(array_key_exists($r["pid"], $array)){
                                $array[$r["pid"]]["count"] += $r["count"];
                            } else {
                                $array[$r["pid"]] = [
                                    "numb" => $qsr["numb"],
                                    "sales_id" => $qsr["sales_id"],
                                    "module" => $r["module"],
                                    "count" => $r["count"],
                                    "date" => $r["date"],
                                    "uid" => $r["uid"],
                                    "stock_id" => $r["stock_id"],
                                ];
                            }
                        break;
                    }
                }
                $data = [];
                foreach($array as $val){
                    $data[ $val["stock_id"] ][] = $val;
                }
                foreach($data as $stock_id => $d){
                    $stock = get_stock_name($stock_id, ["org" => true]);
                    echo '<h3>'.$stock["name"].' ('.$stock["org_name"].')</h3><hr/>';
                    foreach($d as $val){
                        echo '<div>
                        <a target="_blank" href="/administration/module/sales/order/'.$val["sales_id"].'">
                        Продажа '.$val["numb"].', в резерве <b>'.$val["count"].'</b> шт. от <b>'.date("d.m.Y H:i:s", strtotime($val["date"])).'</b>, '.shortUserName($val["uid"]).'
                        </a>
                        </div>';
                    }
                }
            } else echo 'Нет резервов';
            
        } else  echo 'Не хватает данных';
    }

    function setAllowShipmenSales(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $query = $db->query("SELECT allow_shipment FROM JMY_sales WHERE id = '".$id."' LIMIT 1");
            if($db->numRows($query) > 0){
                $r = $db->getRow($query);
                $val = "";
                switch($r["allow_shipment"]){
                    case "0":
                        $val = 1;
                    break;
                    case "1":
                        $val = 0;
                    break;
                }
                $db->query("UPDATE JMY_sales SET allow_shipment = '".$val."' WHERE id = '".$id."' LIMIT 1");
            }
        } else  echo 'Не хватает данных';
    }
    
    function getCommentSalesDetail(){
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $sales_detail = \Sales\Model\SalesDetail::findOrFail($id);
            $attach = \Boot\Models\Attach::where("pid", $sales_detail->id)->where("module", "sales_comment")->get();
            $data = [
                "comment" => $sales_detail->comment,
                "files" => []
            ];
            if(!empty($attach)){
                foreach($attach as $item){
                    $data["files"][] = ["id" => $item->id, "name" => $item->name];
                }
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }
    
    function getInfoOrgSales(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["id"], $_POST["sales_id"])){
            $id = $_POST["id"];
            $sales_id = $_POST["sales_id"];
            $query = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE id = '".$id."' LIMIT 1");
            if($db->numRows($query) > 0){
                $org = $db->getRow($query);

                ?>
                    <style>
                        .activeComment{
                            cursor: pointer;
                            color: blue;
                        }
                        .uoifActive{
                            color: red!important;
                        }
                        .uoif{
                            color: #d4c4c4;
                        }
                    </style>
                    <div class="row">
                        <div class="col-sm-6" style="border-right: 1px #e5e5e5 solid;">
                        <h3 style="color: #b3b3b3">Основные данные:</h3>
                            <table class="table"> 
                                <tbody>
                                    <tr>
                                        <td>ИНН:</td>
                                        <td><?php echo $org["inn"];?></td>
                                    </tr>
                                    <tr>
                                        <td>Краткое наименование:</td>
                                        <td><?php echo $org["name"];?></td>
                                    </tr>
                                    <tr>
                                        <td>Наименование:</td>
                                        <td><?php echo $org["name_full"];?></td>
                                    </tr>
                                    <tr>
                                        <td>Юридический адрес:</td>
                                        <td><?php echo $org["yaddress"];?></td>
                                    </tr>
                                    <tr>
                                        <td>Почтовый адрес:</td>
                                        <td><?php echo $org["maddress"];?></td>
                                    </tr>
                                    <tr>
                                        <td>КПП:</td>
                                        <td><?php echo $org["kpp"];?></td>
                                    </tr>
                                    <tr>
                                        <td>ОГРН:</td>
                                        <td><?php echo $org["ogrn"];?></td>
                                    </tr>
                                    <tr>
                                        <td>Должность руководителя:</td>
                                        <td><?php echo $org["post"];?></td>
                                    </tr>
                                    <tr>
                                        <td>Руководитель:</td>
                                        <td><?php echo $org["director"];?></td>
                                    </tr>
                                    <tr>
                                        <td>Действует на основании:</td>
                                        <td><?php echo $org["dey"];?></td>
                                    </tr>
                                    <tr>
                                        <td>Телефон/Факс:</td>
                                        <td><?php echo $org["phone"];?></td>
                                    </tr>
                                    <tr>
                                        <td>E-mail:</td>
                                        <td><?php echo $org["email"];?></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="col-sm-6">
                        <h3 style="color: #b3b3b3">Контактные лица:</h3>
                            <table class="table">
                                <thead>
                                    <th>ФИО</th>
                                    <th>E-mail</th>
                                    <th>Телефон</th>
                                    <th>Доп телефон</th>
                                    <th>Должность</th>
                                    <th>Действия</th>
                                </thead>
                                <tbody style=" max-height: 560px; overflow: auto;">
                <?php

                $query = $db->query("SELECT u.id as uid, u.surname, u.name, u.ochestvo, u.email, u.phone, u.phone2, uo.id as uoid, uo.post, uo.comment FROM ".DB_PREFIX."_users_org as uo LEFT JOIN ".DB_PREFIX."_users as u ON(uo.uid = u.id) WHERE uo.oid = '".$id."'");

                $qSales = $db->query("SELECT uoif FROM ".DB_PREFIX."_sales WHERE id = '".$sales_id."' LIMIT 1");
                $qs = $db->getRow($qSales);
                while($user = $db->getRow($query)){
                    ?>
                        <tr>
                            <td><?php echo $user["surname"]." ".$user["name"]." ".$user["ochestvo"]; ?></td>
                            <td><?php echo $user["email"]; ?></td>
                            <td><?php echo $user["phone"]; ?></td>
                            <td><?php echo $user["phone2"]; ?></td>
                            <td><?php echo $user["post"]; ?></td>
                            <td>
                                <?php 
                                    if(empty($user["comment"])){
                                        $activeComment = "";
                                    } else {
                                        $activeComment = "activeComment";
                                    }
                                    if($qs["uoif"] !== $user["uoid"]){
                                        $uoifActive = "cp";
                                    } else {
                                        $uoifActive = "uoifActive";
                                    }
                                    echo '<i class="fa fa-commenting-o fa-lg '.$activeComment.'" aria-hidden="true" data-comment="'.$user["comment"].'" title="Комментарий"></i>';
                                    echo '<i class="fa fa-star fa-lg ml10 uoif '.$uoifActive.'" aria-hidden="true" data-id="'.$user["uoid"].'"></i>';
                                ?>
                            </td>
                        </tr>
                    <?php
                }
                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php
            }
        } else echo 'Не хватает данных';
    }

    function setUserOrgIdFavorite(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["id"], $_POST["sales_id"])){
            $id = $_POST["id"];
            $sales_id = $_POST["sales_id"];
            $db->query("UPDATE ".DB_PREFIX."_sales SET uoif = '".$id."' WHERE id = '".$sales_id."' LIMIT 1");
        } else echo 'Не хватает данных';
    }

    function listUserOrg(){
        if(isset($_POST["id"])){
            $oid = $_POST["id"];
            foreach(\Sales\Support\SalesSupport::list_messages_oid($oid) as $item){
                $mess = [];
                foreach($item->messengers as $messenger){
                    $mess[] = "data-{$messenger->name}=\"".($messenger->status ? "true" : "false")."\"";
                }
                $mess = implode(" ", $mess);
                echo <<<HTML
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" class="userListOrg" data-uid="{$item->users_org->uid}" {$mess}>
                            {$item->users_org->user->full_user_name}
                        </label>
                    </div>
HTML;
            }
        }
    }

    function sendAllMessSales(){
        if(isset($_POST["role"], $_POST["uid"], $_POST["sales_id"])){
            \Sales\Support\SalesSupport::send_message($_POST["sales_id"], $_POST["uid"], $_POST["role"]);
        }
    }

    function changeTitleSales(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["pk"], $_POST["value"])){
            $id = $_POST["pk"];
            $title = $_POST["value"];
            $db->query("UPDATE ".DB_PREFIX."_sales SET `title` = '".$db->safesql($title)."' WHERE `id` = '".$db->safesql($id)."' LIMIT 1");
        } else {
            http_response_code(502);
        }
    }

    function priceChangeLocalSales(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["id"], $_POST["value"])){
            $id = $_POST["id"];
            $value = $_POST["value"];
            if(!empty($value) || $value == 0){
                $value = "'".$value."'";
            } else {
                $value = 'null';
            }
            $db->query("UPDATE ".DB_PREFIX."_sales_detail SET `s_price_local` = ".$value." WHERE `id` = '".$id."' LIMIT 1");
        } else {
            http_response_code(502);
        }
    }

    function datePostLocaSales(){
        global $db, $allowFuncs, $url, $lang, $core;
        if(isset($_POST["id"], $_POST["value"])){
            $id = $_POST["id"];
            $value = $_POST["value"];
            if(empty($value)){
                $value = 'null';
            } else {
                $value = "'".$value."'";
            }
            $db->query("UPDATE ".DB_PREFIX."_sales_detail SET `date_post` = ".$value." WHERE `id` = '".$id."' LIMIT 1");
        } else {
            http_response_code(502);
        }
    }

    function masDelSales(){
        $sales_id = $_POST["sales_id"];
        $array = $_POST["array"];
        \Sales\Model\SalesDetail::destroy($array);
        \Sales\Support\SalesDetailSupport::recount_sales($sales_id);
    }

    function getPayDetailSales(){
        if(isset($_POST["id"])){
            $data = \Sales\Support\SalesSupport::getPayDetail($_POST["id"]);
            echo $data->template;
        }
    }

    function checkZeroSum(){
        global $db, $allowFuncs, $url, $lang, $core;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $uid_hack = [34];
        $uid = $core->auth->user_info['id'];
        if(!in_array($uid, $uid_hack)){
            if($id){
                $error = [];
                $errorFlag = false;
                if(is_array($id)){
                    $where = "id IN(".implode(", ", $id).")";
                } else {
                    $where = "sales_id = '".$id."'";
                }
                $query = $db->query("SELECT s_name, total FROM `".DB_PREFIX."_sales_detail` WHERE ".$where." AND del = '0'");
                while($r = $db->getRow($query)){
                    $total = $r["total"];
                    $s_name = $r["s_name"];
                    if(empty($total) || $total <= 0){
                        $errorFlag = true;
                        $error[] = $s_name;
                    }
                }
                if($errorFlag){
                    $error = implode(", ", $error);
                    echo $error;
                }
            } else echo 'Не хватает данных';
        }
    }

    function checkSumMoreSales(){
        global $db, $allowFuncs, $url, $lang, $core;
        $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;
        if($checks){
            $checks = implode(", ", $checks);
            $error = [];
            $errorFlag = false;
            $query = $db->query("SELECT id, s_name, s_price, cid FROM ".DB_PREFIX."_sales_detail WHERE id IN(".$checks.")");
            while($r = $db->getRow($query)){
                $s_price = $r["s_price"];
                $s_name = $r["s_name"];
                $cid = $r["cid"];

                $priceFunc = \Price::getPrice($cid);
                $price = $priceFunc->price_purchase_;

                $supplyPriceQuery = $db->query("SELECT sod.price_now, so.date_bill, so.date_ship, so.date_end, so.id as soid FROM ".DB_PREFIX."_supply_order_detail as sod LEFT JOIN ".DB_PREFIX."_supply_order as so ON(sod.supply_o = so.id) WHERE sod.sale_detail_id = '".$r['id']."' LIMIT 1");
                if($db->numRows($supplyPriceQuery) > 0){
                    $supplyPrice = $db->getRow($supplyPriceQuery);
                    $price = $supplyPrice["price_now"];
                }

                if($price > $s_price){
                    $errorFlag = true;
                    $error[] = $s_name;
                }
            }
            if($errorFlag){
                $error = implode(", ", $error);
                echo $error;
            }
        } else echo 'Не хватает данных';
    }

    function sendMoveSales(){
        $sales_id = isset($_POST["sales_id"]) ? $_POST["sales_id"] : false;
        $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;

        $data = \Module::sales_to_transfer($sales_id, ["sales_detail_id" => $checks]);

        echo json_encode(["transfer_id" => $data["stock_transfer"]->id], JSON_UNESCAPED_UNICODE);
    }

    function fixSupply(){
        global $db, $core;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        if($id){
            $clone_sales = new \Sales\CloneSales(["parent_id" => $id]);
            if($clone_sales->check_clone()){
                $id = $clone_sales->clone_parent_id;
            }
            $date = date("Y-m-d H:i:s");
            $query = $db->query("SELECT sd.*, sup.count as sup_count, sup.cid as sup_cid, sup.sid as sup_sid, sup.id as sup_id, sup.sdid as sup_sdid FROM ".DB_PREFIX."_sales_detail as sd LEFT JOIN ".DB_PREFIX."_supply_detail as sup ON(sup.id = sd.supply_detail_id) LEFT JOIN ".DB_PREFIX."_supply as s ON(s.id = sup.sid) WHERE sd.sales_id = '".$id."' AND sd.del = '0' AND s.type = '1' AND sd.supply_detail_id IS NOT NULL");
            if($db->numRows($query) > 0){
                $sales_array = [];
                $change_array_id = [];
                $editFlag = false;
                $supply_id = "";
                while($r = $db->getRow($query)){
                    $sales_array[] = $r["id"];

                    if(empty($supply_id)){
                        $supply_id = $r["sup_sid"];
                    }

                    //проверка количества
                    if($r["count"] != $r["sup_count"]){
                        $editFlag = true;
                        $db->query("UPDATE ".DB_PREFIX."_supply_detail SET status = '10', count = '".$r["count"]."' WHERE id = '".$r["sup_id"]."' LIMIT 1");
                        setLog("info", $r["sup_id"], "supply_detail", 'УРАВНИТЕЛЬ. Заявка №'.$supply_id.'. Уравнение количества '.$r["sup_count"].' -> '.$r["count"]);
                    }

                    //проверка на замену
                    if($r["cid"] != $r["sup_cid"]){
                        $editFlag = true;
                        $db->query("UPDATE ".DB_PREFIX."_supply_detail SET status = '12' WHERE id = '".$r["sup_id"]."' LIMIT 1");
                        $change_array_id[] = $r["sup_id"];
                        //добавить запись новую запись
                        $db->query("INSERT INTO ".DB_PREFIX."_supply_detail (`sid`, `sdid`, `cid`, `s_name`, `s_sku`, `price_type`, `s_price`, `s_price_all`, `s_unit`, `count`, `status`) VALUES ('".$r["sup_sid"]."', '".$r["id"]."', '".$r["cid"]."', '".$r["s_name"]."', '".$r["s_sku"]."', '".$r["price_type"]."', '".$r["s_price"]."', '".$r["s_price_all"]."',  '".$r["s_unit"]."', '".$r["count"]."', '1')");
                        $new_sup_id = $db->insert_id();
                        //обновить supply_detail_id в продаже
                        $db->query("UPDATE ".DB_PREFIX."_sales_detail SET supply_detail_id = '".$new_sup_id."', status = '4' WHERE id = '".$r["id"]."' LIMIT 1");

                        setLog("info", $r["sup_id"], "supply_detail", 'УРАВНИТЕЛЬ. Заявка №'.$supply_id.'. Замена товара '.$r["sup_cid"].' -> '.$r["cid"]);
                    }
                }

                //проверка на удаление
                $adRules = "";
                if(count($change_array_id) > 0){
                    $adRules = " AND sd.id NOT IN(".(implode(", ", $change_array_id)).")";
                }
                $query = $db->query("SELECT sd.* FROM ".DB_PREFIX."_supply_detail as sd LEFT JOIN ".DB_PREFIX."_supply as s ON(s.id = sd.sid) WHERE s.sales_id = '".$id."'".$adRules."");
                if($db->numRows($query) > 0){
                    $supply_array = [];
                    $supply_array_key = [];
                    while($supply = $db->getRow($query)){
                        $supply_array[] = $supply["sdid"];
                        $supply_array_key[$supply["sdid"]] = $supply["id"];
                    }
                }
                if(count($supply_array) > 0){
                    $del_array = array_diff($supply_array, $sales_array);
                    if(count($del_array) > 0){
                        $editFlag = true;
                        $del_array_sup = [];
                        foreach($del_array as $val){
                            $del_array_sup[] = $supply_array_key[$val];
                        }
                        $db->query("UPDATE ".DB_PREFIX."_supply_detail SET status = '11', count = '0' WHERE id IN(".(implode(", ", $del_array_sup)).")");
                        foreach($del_array_sup as $val){
                            setLog("info", $val, "supply_detail", 'УРАВНИТЕЛЬ. Заявка №'.$supply_id.'. Товар удален');
                        }
                    }
                }
                

                if($editFlag){
                    $db->query("UPDATE ".DB_PREFIX."_supply SET status = '1', date_edit = '".$date."' WHERE id = '".$supply_id."' LIMIT 1");
                    $attr = [];
                    $attr['uid'] = getGroup(["group" => "supply"]);
                    $attr['title'] = 'Изменение заявки на закупку №'.$supply_id;
                    $attr['text'] = 'Изменение заявки на закупку №'.$supply_id;
                    $attr['icon'] = config('app.url').'/files/push/new_storage.png';
                    $attr['url'] = config('app.url').'/administration/module/supply/detail/'.$supply_id;
                    $attr['pNotif'] = true;
                    $attr['pPush'] = true;			
                    $attr['module'] = "supply";
                    $attr['pid'] = $supply_id;
                    sendAllNotif($attr);
                }

            }
        }
    }

    function check_realization_active(){
        global $db;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $sales_id = isset($_POST["sales_id"]) ? $_POST["sales_id"] : false;
        if($id || $sales_id){
            if($id){
                $query = $db->query("SELECT r.id FROM ".DB_PREFIX."_realization_detail as rd LEFT JOIN ".DB_PREFIX."_realization as r ON(r.id = rd.rid) WHERE r.active = '1' AND rd.sdid = '".$id."' LIMIT 1");
            } else {
                $query = $db->query("SELECT r.id FROM ".DB_PREFIX."_realization as r WHERE r.active = '1' AND r.sales_id = '".$sales_id."' LIMIT 1");
            }
            
            if($db->numRows($query) > 0){
                $r = $db->getRow($query);
                echo $r["id"];
            }
        }
    }

    function add_reserv_sales(){
        $id = $_POST["id"] ?? false;
        $sales_id = $_POST["sales_id"] ?? false;
        if($id && $sales_id){
            $sales = \Sales\Model\Sales::find($sales_id);
            $stock_id = empty($sales->stock_id) ? false : $sales->stock_id;

            $re = function($id) use ($stock_id, $sales){
                $reserv = new Reserv();
                $count = \Sales\Support\SalesSupport::get_count_realization_left($id);
                $reserv->set_reserv($id, "sales", $stock_id, $count);
                $clone_sales = new \Sales\CloneSales(["children_id" => $id]);
                $clone_sales->add_reserv(["count" => $count]);
                if($reserv->data->count == $reserv->count){
                    $status_client = 6;
                }
                if($reserv->future_count > 0){
                    $status_client = 5;
                }
                if(isset($status_client)){
                    \Sales\Model\SalesDetail::where("id", $id)->update(["status_client" => $status_client]);
                } 
                if($reserv->data->count == $reserv->count){
                    \Sales\Support\SalesSupport::status_work($sales->id, ["sales" => $sales]);
                }
            };

            if($stock_id){
                if(is_array($id)){
                    if(count($id) > 0){
                        foreach($id as $val){
                            $re($val);
                        }
                    }
                } else {
                    $re($id);
                }
                $sales->re_status_client();
            }
        } 
    }

    function del_reserv_sales(){
        $id = $_POST["id"] ?? false;
        $sales_id = $_POST["sales_id"] ?? false;

        $re = function($id){
            $reserv = new Reserv();
            $reserv->del_reserv($id, "sales");
            $clone_sales = new \Sales\CloneSales(["children_id" => $id]);
            $clone_sales->delete_reserv();

            \Sales\Model\SalesDetail::where("id", $id)->update(["status_client" => 4]);
        };

        if($id && $sales_id){
            if(is_array($id)){
                foreach($id as $val){
                    $re($val);
                }
            } else {
                $re($id);
            }
            \Sales\Model\Sales::find($sales_id)->re_status_client();
        } 
    }

    function file_request_sales(){
        global $db;
        $id = isset($_POST["array_data"]) ? $_POST["array_data"] : false;
        if($id){
            $query = $db->query("SELECT id, name FROM ".DB_PREFIX."_attach WHERE id IN(".implode(", ", $id).")");
            while($r = $db->getRow($query)){
                echo '<div><a target="_blank" href="/download/'.$r["id"].'">'.$r["name"].'</a></div>';
            }
        } else {
            echo 'Файлы отсутствуют';
        }
    }

    function get_status_log(){
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        if($id){
            echo \Sales\Support\SalesDetailStatusLogSupport::get_view($id);
        }
    }

    function get_purchase_price_date_sales_ajax(){
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        if($id){
            $array = \Sales\Support\SalesSupport::get_purchase_price_date_sales($id);
            $json = json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $json;
        }
    }

    function check_assembly_sales(){
        global $db;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        if($id){
            $sync = \Sales\Support\SalesSupport::check_assembly($id);
            if(count($sync) > 0){
                echo '<div style="margin-top: 20px;"></div>';
                foreach($sync as $val){
                    echo '<div style="font-weight: bold;">'.$val["s_name"].'</div>';
                }
                echo '<div style="margin-bottom: 20px;"></div>';
            }
        }
    }

    function sync_assembly_sales(){
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        if($id){
            $sync = \Sales\Support\SalesSupport::check_assembly($id);
            if(count($sync) > 0){
                $sync1 = [];
                $sync2 = [];
                foreach($sync as $key => $val){
                    switch((int)$val["type_assembly"]){
                        case 1:
                            $sync1[$key] = $val;
                        break;
                        case 2:
                            $sync2[$key] = $val;
                        break;
                    }
                }
                if(count($sync1) > 0){
                    $assembly = \Storage\Model\StorageAssembly::create(["sales_id" => $id, "type" => 1]);
                    $iid = $assembly->id;
                    foreach($sync1 as $key => $item){
                        \Storage\Model\StorageAssemblyDetail::create(["said" => $iid, "sdid" => $key, "count_save" => $item["count"], "status" => 1]);
                    }
                }
                if(count($sync2) > 0){
                    $assembly = \Storage\Model\StorageAssembly::create(["sales_id" => $id, "type" => 2]);
                    $iid = $assembly->id;
                    foreach($sync2 as $key => $item){
                        \Storage\Model\StorageAssemblyDetail::create(["said" => $iid, "sdid" => $key, "count_save" => $item["count"], "status" => 1]);
                    }
                }
            }
        }
    }

    function get_realization_sales(){
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        if($id){
            $data = \Sales\Support\SalesDetailSupport::get_realization_sales($id);
            echo $data->template;
        }
    }

    function get_assembly_sales(){
        global $db;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        if($id){
            $query = $db->query("SELECT sa.* FROM `".DB_PREFIX."_storage_assembly_detail` as sad LEFT JOIN `".DB_PREFIX."_storage_assembly` as sa ON(sa.id = sad.said) WHERE sad.sdid = '".$id."'");
            if($db->numRows($query) > 0){
                while($r = $db->getRow($query)){
                    echo '<div><a style="color:'.((int)$r["status"] === 1 ? 'red' : 'green').';" href="/administration/module/storage/assembly/detail/'.$r["id"].'" target="_blank">Сборка №'.$r["id"].'</a></div>';
                }
            } else {
                echo 'Сборки отсутствуют';
            }
        }
    }

    function get_transfer_sales(){
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        if($id){
            $data = \Sales\Support\SalesDetailSupport::get_transfer_sales($id);
            echo $data->template;
        }
    }

    function view_supply_order_position(){
        $supply_order = isset($_POST["supply_order"]) ? $_POST["supply_order"] : false;
        $sdid = [];
        if($supply_order){
            foreach(\Supply\Model\SupplyOrderDetail::where("supply_o", $supply_order)->whereNotNull("sale_detail_id")->get() as $item){
                $sdid[] = $item->sale_detail_id;
            }
        }
        echo json_encode($sdid, JSON_UNESCAPED_UNICODE);
    }

    function supply_order_send_control(){
        $sales_id = isset($_POST["sales_id"]) ? $_POST["sales_id"] : false;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $status = isset($_POST["status"]) ? (int)$_POST["status"] : false;
        $comment = isset($_POST["comment"]) ? $_POST["comment"] : null;
        if($id && $status && $sales_id){
            \Sales\Support\SalesSupport::supply_order_send_control($sales_id, $id, $status, $comment);
        }
    }

    function get_send_tov_sales(){
        global $db;
        $checks = isset($_POST["checks"]) ? $_POST["checks"] : [];
        if(count($checks) > 0){
            $checks_str = implode(", ", $checks);
            $query = $db->query("SELECT * FROM ".DB_PREFIX."_sales_detail WHERE id IN(".$checks_str.") AND supply_detail_id IS NOT NULL");
            if($db->numRows($query) > 0){
                ?>
                    <div class="mb10">Список отправленных товаров (<b>отправленные товары повторно не отправляются</b>):</div>
                    <ul>
                <?php
                while($r = $db->getRow($query)){
                    ?>

                    <li>(<b><?php echo $r["s_sku"];?></b>) <?php echo $r["s_name"];?></li>

                    <?php
                }
                ?>
                    </ul>
                <?php
            }
        }
    }

    function check_all_realization_and_transfer(){
        $id = $_POST["id"] ?? false;
        if($id){
            $flag = \Sales\Support\SalesSupport::check_all_realization_and_transfer($id);
            if(count($flag) > 0){
                echo json_encode($flag, JSON_UNESCAPED_UNICODE);
            }
        }
    }

    function end_sales(){
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $end = isset($_POST["end"]) ? $_POST["end"] : 0;
        $comment_end = isset($_POST["comment_end"]) ? $_POST["comment_end"] : null;
        if($id){
            \Sales\Support\SalesSupport::end_sales($id, $end, $comment_end);
        }
    }

    function change_sales_field_value(){
        global $db;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $field = isset($_POST["field"]) ? $_POST["field"] : false;
        $value = isset($_POST["value"]) ? $_POST["value"] : '';
        if($id && $field){
            $db->query("UPDATE ".DB_PREFIX."_sales SET `".$field."` = '".$value."' WHERE id = '".$id."' LIMIT 1");
        }
    }

function reestr_load_sales(){
    global $db;
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    if($id){
        $query = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '".$id."' LIMIT 1");
        $sales = $db->getRow($query);
        $oid = $sales["oid"];
        if(empty($oid)){
            echo 'Контрагент не указан';
            return;
        }
        $data = \Reestr\ReestrModule::load_pact_data([
            "oid" => $oid,
            "module_out" => "sales",
            "pid_out" => $id,
            "onchange" => "dis_off_reestr();",
        ]);
        echo $data["template"];
    }
}

function check_reestr_create_sales(){
    $id = $_POST["id"] ?? false;
    if($id){
        try {
            \Sales\Support\SalesReestrSupport::check($id);
        } catch (\Error $e) {
            echo "<div>{$e->getMessage()}</div>";
        }
    } else {
        echo "<div>SalesID не указано</div>";
    }
}

function get_sales_data(){
    global $db;
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    $result = [];
    if($id){
        if(is_array($id)){
            $id_str = implode(", ", $id);
        } else {
            $id_str = $id;
        }
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_sales` WHERE id IN(".$id_str.")");
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                $result[] = $r;
            }
        }
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

function get_assemply_list_sales(){
    $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;
    if($checks){
        $items = \Sales\Support\SalesAssemblySupport::get_count($checks);
        foreach($items as $item){
            echo <<<HTML
                <li>
                    {$item->sales_detail->s_name}
                    <input type="hidden" value="{$item->sales_detail->id}" name="checks[]">
                    <input type="number" value="{$item->count}" 
                        name="count[{$item->sales_detail->id}]" 
                        class="form-control" 
                        placeholder="Введите количество" 
                        min="0" 
                        max="{$item->count}" 
                        step="any" 
                        required
                    >
            </li>
HTML;
        }
    }
}

function getStockSales(){
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    $request = [];
    if($id){
        $data = \Sales\Model\Sales::find($id)->toArray();
        $request["org"] = $data["org"];
    }
    $stock = get_stock_data($request);
    echo json_encode($stock, JSON_UNESCAPED_UNICODE);
}

function changeSalesDetailX(){
    $request = $_POST;
    $field = $request["name"];
    $value = $request["value"];
    $id = $request["pk"];

    \Sales\Model\SalesDetail::find($id)->update([$field => $value]);
}

function getPriceTypeSelectDataSales(){
    $request = $_POST;
    $result = [];
    if($request["type"] == 1){
        $price_data = \Sales\Model\SalesDetail::getPurchase($request["id"], ["cid" => $request["cid"]]);
        $result = ["price" => $price_data->price];
    } else {
        $result = \PriceTypeSelect::getPrice($request);
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}