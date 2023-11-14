<?php

use Price\UI\CLI\CatalogPriceDaemon;
global $db, $allowFuncs, $url, $core;

$allowFuncs = array_merge($allowFuncs, ["getSearchListSupply", "getOrgList", "getOrderList", "getNdsType", "updateProductsSupply", "delSupplyOrder", "removeOnPassSupplyDetail", "warehousePositionSupplyDetail", "receiptsSetActive", "updateProductsReceipt", "updateDocNumberSupply", "saveFileSupply", "removeFileSupply", "warehousePositionDelSupplyDetail", "agreeWithTheWarehouseSupply", "fastChangeSupplyOrder", "fastChangeSupplyOrderDetail", "changeDocReceipts", "getListReservSupplyOrder", "transferReceipt", "removeReceipts", "changeTwoDetail", "confirmExecutionTwoDetail", "getListNoFileTwoDetail", "saveBillTwoDetail", "getBillListTwoDetail", "removeBillAllTwoDetail", "getBillListDetailTwoDetail", "removeBillTwoDetail", "logisticsSetArrived", "checkSendCount", "delTovSupplyDetail", "delTovReceipt", "check_active_receipts", "loadFileSupplyOrder", "receipts_sync", "updateValueSupplyDetail", "checkZeroSumSupplyOrder", "check_supply_order_for_fcontrol", "send_supply_order_for_control", "get_stock_source", "set_mas_date_supply", "price_active_change_supply_order", "check_status_confirm_receipts", "get_receipts_supply", "supply_logistic_status"]);

function getSearchListSupply()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["search"], $_POST["counter"])) {
        $search = $_POST["search"];
        $counter = $_POST["counter"];
        $masSearch = explode(" ", $search);
        $str = "";
        foreach ($masSearch as $val) {
            $str .= "(title LIKE '%" . $val . "%' OR sku LIKE '%" . $val . "%') AND ";
        }
        $query = $db->query("SELECT * FROM JMY_catalog WHERE " . mb_substr($str, 0, -4) . " LIMIT " . $counter . ", 50");
        while ($r = $db->getRow($query)) {
            echo '<div class="elemList" id="rp_' . $r["id"] . '" onselectstart="return false;" onmousedown="return false;">' . $r["title"] . '</div>';
        }
        if ($db->numRows($query) == 50) {
            echo '<div class="loadMore btn btn-info btn-block" onselectstart="return false;" onmousedown="return false;">Ещё</div>';
        }
    }
}

function updateProductsSupply()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["saveId"], $_POST["saveData"], $_POST["includesNds"], $_POST["supplyId"], $_POST["totalAll"])) {
        $saveId = $_POST["saveId"];
        if (mb_strlen($saveId) > 0) {
            $saveId = json_decode($saveId, true);

            foreach ($saveId as $key => $val) {
                $query = $db->query("SELECT * FROM `".DB_PREFIX."_catalog` WHERE id = '".$val."' LIMIT 1");
                $r = $db->getRow($query);
                $db->query("UPDATE `".DB_PREFIX."_supply_order_detail` SET `cid` = '" . $val . "', `s_name` = '".$r["title"]."', `s_sku` = '".$r["sku"]."' WHERE id = '" . $key . "'");
            }
        }

        $saveData = $_POST["saveData"];
        if (mb_strlen($saveData) > 0) {
            $saveData = json_decode($saveData, true);
            foreach ($saveData as $key => $val) {
                $db->query("UPDATE `".DB_PREFIX."_supply_order_detail` SET count = " . $saveData[$key]["count"] . ", price_now = '" . $saveData[$key]["edit"] . "', total = " . $saveData[$key]["total"] . ", nds = " . $saveData[$key]["nds"] . ", type_nds = " . \Price::getNds($saveData[$key]["typeNds"], ["mode" => true]) . " WHERE id = '" . $key . "'");

                $pp = $db->query("SELECT supply_d FROM `" . DB_PREFIX . "_supply_order_detail` WHERE id = '" . $key . "' LIMIT 1");
                $p = $db->getRow($pp);
                $pp = $db->query("SELECT count FROM `" . DB_PREFIX . "_supply_order_detail` WHERE supply_d = '" . $p["supply_d"] . "'");
                $count_send = 0;
                while ($z = $db->getRow($pp)) {
                    $count_send += $z["count"];
                }
                $db->query("UPDATE `" . DB_PREFIX . "_supply_detail` SET count_send = '" . $count_send . "' WHERE id = '" . $p["supply_d"] . "' LIMIT 1");
            }
        }

        $nds_includes = $_POST["includesNds"] == 'true' ? 1 : 0;
        $supplyId =  $_POST["supplyId"];
        $totalAll =  $_POST["totalAll"];
        $db->query("UPDATE `".DB_PREFIX."_supply_order` SET nds_includes = " . $nds_includes . ", total = '" . $totalAll . "' WHERE id = " . $supplyId);
        priceSave($supplyId);
    } else {
        echo "Не хватает данных";
    };
}

function getOrgList()
{
    global $db, $allowFuncs, $url, $core;
    $query_r = $db->query("SELECT id, inn, name FROM " . DB_PREFIX . "_organizations");
    $option_org = '<option value="0" selected disabled>Выберите контрагента</option>';
    if ($db->numRows($query_r) > 0) {
        while ($r_list = $db->getRow($query_r)) {
            $selected = '';
            $option_org .= '<option value="' . $r_list['id'] . '" ' . $selected . '>' . $r_list['name'] . ' (' . $r_list['inn'] . ')</option>';
        }
    }
    echo $option_org;
}

function getOrderList()
{
    global $db, $allowFuncs, $url, $core;
    $query_r = $db->query("SELECT s.id, o.name, s.numb FROM " . DB_PREFIX . "_supply_order as s LEFT JOIN JMY_organizations as o on(o.id=s.oid) ORDER BY s.id DESC");
    $option_zakaz = '<option value="0" selected disabled>Выберите заказ</option>';
    if ($db->numRows($query_r) > 0) {
        while ($r_list = $db->getRow($query_r)) {
            $selected = '';
            $option_zakaz .= '<option value="' . $r_list['id'] . '" ' . $selected . '>Заказ №' . $r_list['numb'] . ' (' . $r_list['name'] . ')</option>';
        }
    }
    echo $option_zakaz;
}


function getNdsType()
{
    $nds = \Price::getNds("all");
    foreach ($nds as $key => $val) {
        echo '<option class="ndsTypeOp" value="' . $key . '">' . $val . '</option>';
    }
}

function delSupplyOrder()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $query = $db->query("SELECT * FROM JMY_supply_order_detail WHERE supply_o = " . $id);

        while ($r = $db->getRow($query)) {
            $db->query("UPDATE JMY_supply_detail set status = 1, `count_send` = `count_send` - " . $r["count"] . " WHERE id = " . $r["supply_d"]);
        }
        $db->query("DELETE FROM JMY_supply_order_detail WHERE supply_o = " . $id);
        $db->query("DELETE FROM JMY_supply_order WHERE id = " . $id);
    } else echo "Не хватает данных";
}

function removeOnPassSupplyDetail(){
    $request = $_POST;
    $id = $request["id"];
	$data = \Supply\Support\SupplyOrderDetailSupport::c_delete($id);
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function warehousePositionSupplyDetail()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        if(is_array($id)){
            $str_id = implode(", ", $id);
        } else {
            $str_id = $id;
        }
        
        require_once(ROOT."usr/modules/supply/admin/sub/supply_stock_function.php");
        $db->query("UPDATE `".DB_PREFIX."_supply_detail` SET status = 7 WHERE id IN (" . $str_id.")");
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_detail` WHERE id IN (" . $str_id.")");
        $a_sales = [];
        $sid = "";
        while($r = $db->getRow($query)){
            $a_sales[] = $r["sdid"];
            $sid = $r["sid"];
            add_supply_stock_detail($r["cid"], $r["count"]);
        }
        
        $s_status = 14;
        foreach($a_sales as $i){
            \Sales\Model\SalesDetail::find($i)->update(["status" => $s_status]);
            \Sales\Model\SalesDetailStatusLog::create([
                "sales_detail_id" => $i, 
                "status" => $s_status, 
                "url" => "/administration/module/supply/detail/".$sid,
            ]);
        }

        require_once(ROOT.'usr/modules/supply/admin/sub/update_status_supply_detail.php');
        update_status_supply_detail($sid);
    }
}

function warehousePositionDelSupplyDetail(){
    if (isset($_POST["id"])) {
        $id = $_POST["id"];

        $supply_detail = \Supply\Model\SupplyDetail::find($id);
        $supply_detail->status = 4;
        $supply_detail->save();
        $sdid = $supply_detail->sdid;
        $sid = $supply_detail->sid;
        

        $s_status = 4;

        \Sales\Model\SalesDetail::find($sdid)->update(["status" => $s_status]);
        \Sales\Model\SalesDetailStatusLog::create([
            "sales_detail_id" => $sdid, 
            "status" => $s_status, 
            "url" => "/administration/module/supply/detail/".$sid,
        ]);

        require_once(ROOT.'usr/modules/supply/admin/sub/update_status_supply_detail.php');
		update_status_supply_detail($sid);
    }
}

function receiptsSetActive()
{
    require_once('sub/receipts_active.php');
    if (isset($_POST["receipt_id"])) {
        $receipt_id = intval($_POST["receipt_id"]);
        echo receipts_active($receipt_id);
    } else {
        echo '3';
    }
}

function receipts_sync()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST['receipts_id'])) {
        $receipts_id = $_POST['receipts_id'];
        $query = $db->query("SELECT * FROM JMY_receipts WHERE id='" . $receipts_id . "' LIMIT 1");
        if ($db->numRows($query) > 0) {
            $receipts = $db->getRow($query);
            $sync = new \Sync\Sync;
            $sync->syncRegister('receipts', $receipts_id, '1', $receipts['org']);
            echo '1';
        } else {
            echo '2';
        }
    } else echo '2';
}

function logisticsSetArrived()
{
    global $core, $db;
    if (isset($_POST["logistics_id"])) {
        $id = intval($_POST["logistics_id"]);
        $query = $db->query("SELECT * FROM JMY_supply_order WHERE `id` = '" . $id . "' LIMIT 1");
        if ($db->numRows($query) > 0) {
            $r = $db->getRow($query);
            if ($r['arrived'] != '1') {
                $db->query("UPDATE JMY_supply_order SET arrived = 1 WHERE id = " . $id . " LIMIT 1");
                echo '1';
            } else {
                $db->query("UPDATE JMY_supply_order SET arrived = 0 WHERE id = " . $id . " LIMIT 1");
                echo '2';
            }
        } else {
            echo '3';
        }
    } else {
        echo '3';
    }
}


function updateProductsReceipt()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["saveData"], $_POST["receiptId"], $_POST["totalAll"], $_POST["ndsAll"])) {

        $saveData = $_POST["saveData"];
        if (mb_strlen($saveData) > 0) {
            $saveData = json_decode($saveData, true);
            foreach ($saveData as $key => $val) {
                $db->query("UPDATE JMY_receipts_detail SET count = " . $saveData[$key]["count"] . ", price = '" . $saveData[$key]["edit"] . "', total = " . $saveData[$key]["total"] . ", nds = " . $saveData[$key]["nds"] . ", type_nds = " . \Price::getNds($saveData[$key]["typeNds"], ["mode" => true]) . " WHERE id = '" . $key . "' LIMIT 1");
            }
        }

        $receiptId =  $_POST["receiptId"];
        $totalAll =  $_POST["totalAll"];
        $ndsAll =  $_POST["ndsAll"];
        $db->query("UPDATE JMY_receipts SET total = '" . $totalAll . "', nds = '" . $ndsAll . "' WHERE id = " . $receiptId . " LIMIT 1");
    } else {
        echo "Не хватает данных";
    }
}

function updateDocNumberSupply()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["doc_number"])) {
        $id = $_POST["id"];
        $doc_number = $_POST["doc_number"];
        $db->query("UPDATE JMY_receipts SET doc_number = '" . $doc_number . "' WHERE id = '" . $id . "' LIMIT 1");
    } else {
        echo "Не хватает данных";
    }
}

function saveFileSupply()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["file"], $_POST["id"])) {
        $id = $_POST["id"];
        $file = $_POST["file"];
        $attribute = [];
        $attribute["name"] = "";
        $attribute["file"] = $file;
        $attribute["module"] = 'supply';
        $attribute["mod"] = 'receipts';
        $attribute["id"] = $id;
        $attribute["comm_id"] = $id;
        uploaderMaster("download", $attribute);

        $query = $db->query("SELECT * FROM JMY_attach WHERE `pid` = '" . $id . "' AND `module` = 'receipts'");
        if ($db->numRows($query) > 0) {
            while ($r = $db->getRow($query)) {
                echo '<div class="mb5"><a target="_black" href="' . $r["url"] . '">' . $r["name"] . '</a><i class="fa fa-times ml10 cp removeFile" data-id="' . $r["id"] . '" data-name="' . $r["name"] . '" style="color: red;" aria-hidden="true"></i></div>';
            }
        }
    } else {
        echo "Не хватает данных";
    }
}

function removeFileSupply()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $db->query("DELETE FROM JMY_attach WHERE id = '" . $id . "' LIMIT 1");
    } else {
        echo "Не хватает данных";
    }
}

function agreeWithTheWarehouseSupply()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["supply_id"], $_POST["id"])) {
        $supply_id = $_POST["supply_id"];
        $id = $_POST["id"];
        if(count($id) > 0){
            $id_str = implode(", ", $id);
            $uid = $core->auth->user_info['id'];
            $date = date('Y-m-d H:i:s', time());

            $supply = $db->query("SELECT sales_id FROM JMY_supply WHERE id = '" . $supply_id."'");
            $qsupply = $db->getRow($supply);

            $sales_id = $qsupply["sales_id"];

            $sales = $db->query("SELECT id, oid, object FROM " . DB_PREFIX . "_sales WHERE id = '" . $sales_id . "' LIMIT 1");
            $qs = $db->getRow($sales);
            if ($db->query("INSERT INTO `" . DB_PREFIX . "_storage` (`uid` , `sales_id`, `supply_id` ,  `date` , `status`, `oid`, `object`, `type`) VALUES ('" . $db->safesql($uid) . "', '"    . $db->safesql($sales_id) . "', '" . $db->safesql($supply_id) . "', '" . $db->safesql($date) . "', '1', '" . $qs["oid"] . "', '" . $qs["object"] . "', '2')")) {

                $storage_id = $db->insert_id();
                /*
                        relationsSave('sales', $id, 'storage', $storage_id);
                        $uid_array = array('35');
                        $attr = [];
                        $attr['uid'] = $uid_array;
                        $attr['title'] = 'Новая заявка на актуализацию данных!';
                        $attr['text'] = 'Поступила новая заявка на сверку количества, нажмите чтобы посмотреть!';
                        $attr['icon'] = config('app.url').'/files/push/new_storage.png';
                        $attr['url'] = config('app.url').'/administration/module/storage/detail/'.$storage_id;
                        $attr['pNotif'] = true;
                        $attr['pPush'] = true;
                        sendAllNotif($attr);
                    */
            }

            $insert_values_storage = '';
            $query = $db->query("SELECT * FROM JMY_supply_detail WHERE id IN (".$id_str.")");
            while ($r = $db->getRow($query)) {
                $insert_values_storage .=  "('" . $r['cid'] . "', '" . $r['sdid'] . "','" . $storage_id . "', '" . $r["id"] . "', '1'),";
            }

            if (!empty($insert_values_storage)) {
                $insert_storage = "INSERT INTO `JMY_storage_detail`(`cid`, `sale_detail_id`, `storage_id`, `supply_detail_id`, `status`) VALUES " . substr_replace($insert_values_storage, ';   ', -1);
                $db->query($insert_storage);
            }
        }
    }
}


function fastChangeSupplyOrder(){
    if (isset($_POST["pk"])) {
        $id = $_POST["pk"];
        $name = $_POST["name"];
        $value = $_POST["value"];
        $supply_order = \Supply\Model\SupplyOrder::find($id);

        $update = [];

        if(!empty($name) && !empty($id)){
            require_once(ROOT."usr/modules/supply/admin/sub/function.php");
            send_notif_change_date($id);
        }

        switch ($name) {
            default:
                $update[ $name ] = $value;
            break;
            case "date_ship":
                if (empty($supply_order->date_ship)) {
                    $attr = [];
                    $attr['uid'] = 16;
                    $attr['title'] = 'Новая заявка на доставку!';
                    $attr['text'] = "Вам поступила новая заявка на доставку по номеру SN-" . $id;
                    $attr['icon'] = config('app.url').'/files/push/comment.png';
                    $attr['url'] = config('app.url').'/administration/module/supply/logistics_detail/' . $id;
                    $attr['pNotif'] = true;
                    $attr['pPush'] = true;
                    $attr['module'] = "supply_logistics";
                    $attr['pid'] = $id;
                    sendAllNotif($attr);
                }
                $update[ $name ] = $value;
                if(empty($supply_order["date_end"])){
                    $date_end = strtotime("{$value} + 1 week");
                    $update["date_end"] = date('Y-m-d',$date_end);
                }
            break;
        }
        $supply_order->update($update);
    } else {
        echo "Не хватает данных";
    }
}

function fastChangeSupplyOrderDetail(){
    $id = $_POST["pk"];
    $name = $_POST["name"];
    $value = $_POST["value"];
    $request = [];
	$request[ $name ] = $value;
    switch($name){
        case "date_ship":
            if(!empty($value)){
                $date_end = \Carbon\Carbon::parse($value);
                $date_end->addDays(7);
                $request["date_end"] = $date_end->format("Y-m-d");
            } else {
                $request[ $name ] = null;
            }
        break;
    }
    $SupplyOrderDetail = \Supply\Model\SupplyOrderDetail::find($id);
    $SupplyOrderDetail->update($request);
    echo $SupplyOrderDetail->toJson();
}

function saveFuncHelp($orid, $myDate)
{
    global $db, $allowFuncs, $url, $core;
    $step1 = $db->query("SELECT `supply_d` FROM `" . DB_PREFIX . "_supply_order_detail` WHERE `supply_o` = '" . $orid . "'");
    while ($r = $db->getRow($step1)) {
        $step2 = $db->query("SELECT `sdid` FROM `" . DB_PREFIX . "_supply_detail` WHERE id = '" . $r['supply_d'] . "' LIMIT 1");
        $step2 = $db->getRow($step2);
        //$db->query("UPDATE `" . DB_PREFIX . "_supply_detail` SET `status` = 2 WHERE `id` = '" . $r['supply_d'] . "' LIMIT 1");
        //$db->query("UPDATE `" . DB_PREFIX . "_sales_detail` SET `status` = 2 WHERE `id` = '" . $step2['sdid'] . "' LIMIT 1"); //`date_post` = '".$myDate."'

        $step3 = $db->query("SELECT `sales_id` FROM `" . DB_PREFIX . "_sales_detail` WHERE `id` = '" . $step2['sdid'] . "' LIMIT 1");
        $step3 = $db->getRow($step3);

        $step4 = $db->query("SELECT * FROM `" . DB_PREFIX . "_sales_detail` WHERE `sales_id` = '" . $step3['sales_id'] . "'");
        $flag = true;
        while ($k = $db->getRow($step4)) {
            if ($k['status'] == 1) {
                $flag = false;
                break;
            }
        }
        if ($flag) {
            //$db->query("UPDATE `" . DB_PREFIX . "_sales` SET `status` = 5 WHERE `id` = '" . $step3['sales_id'] . "' LIMIT 1");
        }
    }
}

function priceSave($id)
{
    global $core, $db;
    $uid = $core->auth->user_info['id'];
    $date = date('Y-m-d H:i:s', time());
    $query = $db->query("SELECT id, aid FROM `" . DB_PREFIX . "_catalog_price` WHERE `module` = 'supply_order' AND `pid` = '" . $id . "'");
    if ($db->numRows($query) > 0) {
        $r = $db->getRow($query);
        $price_id = $r['id'];
        if (empty($r['aid'])) {
            $att = $db->query("SELECT id FROM `" . DB_PREFIX . "_attach` WHERE `pid` = '" . $id . "' AND `module` = 'supply' ORDER BY id DESC LIMIT 1");
            if ($db->numRows($att) > 0) {
                $ra = $db->getRow($att);
                $db->query("UPDATE `" . DB_PREFIX . "_catalog_price` SET `aid` = '" . $ra['id'] . "' WHERE `id` = '" . $price_id . "' LIMIT 1");
            }
        }
    } else {
        $aid = 'NULL';
        $comment = 'Заведение закупочных цен на основании счета поставщика #' . module_perfix('supply_order', $id);
        $att = $db->query("SELECT id FROM `" . DB_PREFIX . "_attach` WHERE `pid` = '" . $id . "' AND `module` = 'supply' ORDER BY id DESC LIMIT 1");
        if ($db->numRows($att) > 0) {
            $ra = $db->getRow($att);
            $aid = "'" . $ra['id'] . "'";
        }
        if ($db->query("INSERT INTO `" . DB_PREFIX . "_catalog_price` (`id` , `price_type`, `uid` ,  `aid` , `module`, `pid`, `date`, `comment`, `active`, `status`) VALUES (NULL, '1', '" . $db->safesql(intval($uid)) . "', " . $db->safesql(intval($aid)) . ", 'supply_order', '" . $db->safesql(intval($id)) . "', '" . $db->safesql($date) . "', '" . $db->safesql($comment) . "', '1', '1')")) {
            $price_id = $db->insert_id();
        }
    }
    $query = $db->query("SELECT sod.cid, sod.price_now, so.nds_includes
    FROM `" . DB_PREFIX . "_supply_order_detail` as sod 
    LEFT JOIN `" . DB_PREFIX . "_supply_order` as so ON(sod.supply_o = so.id) 
    WHERE sod.supply_o = '" . $db->safesql(intval($id)) . "'");
    if ($db->numRows($query) > 0) {
        $nds = \Price::getNds(3);
        while ($r = $db->getRow($query)) {
            $price = $r['price_now'];
            if ($price != 0 && !empty($price)) {
                if(empty($r["nds_includes"])){
                    $price *= (100 + $nds) / 100;
                }
                \Price\Models\CatalogPriceDetail::create(["cid" => $r['cid'], "price_id" => $price_id, "sum" => $price]);
            }
        }
        CatalogPriceDaemon::key_create();
    }
}

function changeDocReceipts()
{
    global $core, $db;
    if (isset($_POST["pk"], $_POST["value"], $_POST["name"])) {
        $pk = $_POST["pk"];
        $value = $_POST["value"];
        $name = $_POST["name"];
        switch ($name) {
            case "date_receipts":
                $value = date("Y-m-d", strtotime($value));
                break;
        }
        $db->query("UPDATE " . DB_PREFIX . "_receipts SET " . $name . " = '" . $value . "' WHERE id = '" . $pk . "' LIMIT 1");
    } else {
        http_response_code(502);
    }
}

function getListReservSupplyOrder()
{
    global $core, $db;
    $id = isset($_POST["id"]) ? $_POST["id"] : null;
    $rid = isset($_POST["rid"]) ? $_POST["rid"] : null;

    if (!empty($rid) || !empty($id)) {
        $array = [];
        if (empty($id)) {
            $receiptQuery = $db->query("SELECT supply_detail_id FROM " . DB_PREFIX . "_receipts_detail WHERE rid = '" . $rid . "'");
            while ($r = $db->getRow($receiptQuery)) {
                $array[] = $r["supply_detail_id"];
            }
            $id = implode(", ", $array);
        } else {
            $query = $db->query("SELECT id FROM " . DB_PREFIX . "_supply_order_detail WHERE supply_o = '" . $id . "'");
            while ($r = $db->getRow($query)) {
                $array[] = $r["id"];
            }
            $id = implode(", ", $array);
        }


        $strArray = [];
        $query = $db->query("SELECT * FROM " . DB_PREFIX . "_supply_order_detail WHERE id IN(" . $id . ")");
        if ($db->numRows($query) > 0) {
            while ($r = $db->getRow($query)) {
                $testQuery = $db->query("SELECT id, count FROM " . DB_PREFIX . "_stock_reserv WHERE module = 'sales' AND pid = '" . $r["sale_detail_id"] . "' AND status != '0'");
                if ($db->numRows($testQuery) > 0) {
                    $salesQuery = $db->query("SELECT sales_id FROM " . DB_PREFIX . "_sales_detail WHERE id = '" . $r["sale_detail_id"] . "' LIMIT 1");
                    $sales = $db->getRow($salesQuery);

                    $catalogQuery = $db->query("SELECT c.*, cu.sname FROM " . DB_PREFIX . "_catalog as c LEFT JOIN " . DB_PREFIX . "_catalog_unit as cu ON(c.unit = cu.unit) WHERE c.id = '" . $r["cid"] . "' LIMIT 1");
                    $catalog = $db->getRow($catalogQuery);

                    $strArray[$sales["sales_id"]][$r["sale_detail_id"]] = [
                        "title" => $catalog["title"],
                        "sku" => $catalog["sku"],
                        "unit" => $catalog["sname"],
                        "count" => 0,
                    ];

                    while($reserv = $db->getRow($testQuery)){
                        $strArray[$sales["sales_id"]][$r["sale_detail_id"]]["count"] += $reserv["count"];
                    }
                }
            }
        }
        if (count($strArray) > 0) {
            echo '<button class="btn btn-primary btn-block btn-sm">Распечатать</button>';
            foreach ($strArray as $key => $val) {
                echo '<h3>Продажа №' . $key . '</h3>';
                echo '
                    <table class="table">
                        <thead>
                            <th>Наименование</th>
                            <th>Артикул</th>
                            <th>Единицы измерения</th>
                            <th>Количество</th>
                        </thead>
                        <tbody>
                    ';

                foreach ($val as $k => $v) {
                    echo '
                            <tr>
                                <td>' . $v["title"] . '</td>
                                <td>' . $v["sku"] . '</td>
                                <td>' . $v["unit"] . '</td>
                                <td>' . $v["count"] . '</td>
                            </tr>
                        ';
                }

                echo '</tbody></table>';
            }
        }
    }
}

function transferReceipt()
{
    global $core, $db;
    $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;
    $stock_in = isset($_POST["stock_in"]) ? $_POST["stock_in"] : false;
    $oid = isset($_POST["oid"]) ? $_POST["oid"] : false;
    $receiptId = isset($_POST["receiptId"]) ? $_POST["receiptId"] : false;
    $uid = $core->auth->user_info['id'];
    if ($checks && $stock_in && $oid && $receiptId) {
        $query = $db->query("SELECT * FROM `" . DB_PREFIX . "_receipts` WHERE id = '" . $receiptId . "'");
        $r = $db->getRow($query);
        $sales_id = empty($r["sales_id"]) ? 'null' : "'" . $r["sales_id"] . "'";
        $stock_out = $r["stock_id"];
        $org = $r["org"];

        $date = \Carbon\Carbon::now();
        $numb_array = \Boot\Support\Module::numb('stock_transfer', $org, $date);
		$numb = $numb_array['numb'];
		$numb_counter = $numb_array['numb_counter'];

        $db->query("INSERT INTO `" . DB_PREFIX . "_stock_transfer` (`uid`, `object`, `sales_id`, `stock_out`, `stock_in`, `date`, `active`, `status`, `org`, `numb_counter`, `numb`) VALUES ('" . $uid . "', '" . $oid . "', " . $sales_id . ", '" . $stock_out . "', '" . $stock_in . "', '" . $date . "', '0', '1', '".$org."', '".$numb_counter."', '".$numb."')");
        $iid = $db->insert_id();

        foreach ($checks as $key => $val) {
            $query = $db->query("SELECT * FROM " . DB_PREFIX . "_receipts_detail WHERE id = '" . $val . "' LIMIT 1");
            $e = $db->getRow($query);

            $cid = $e["cid"];
            $sdid = empty($e["sales_detail_id"]) ? 'null' : "'" . $e["sales_detail_id"] . "'";
            $s_name = $e["name"];
            $s_sku = $e["sku"];
            $s_unit = $e["unit"];
            $s_price = $e["price"];
            $count = $e["count"];

            $db->query("INSERT INTO `" . DB_PREFIX . "_stock_transfer_detail` (`transfer_id`, `cid`, `sdid`, `s_name`, `s_sku`, `s_unit`, `s_price`, `count`, `status`) VALUES ('" . $iid . "', '" . $cid . "', " . $sdid . ", '" . $s_name . "', '" . $s_sku . "', '" . $s_unit . "', '" . $s_price . "', '" . $count . "', '1')");
        }
        echo $iid;
    }
}

function removeReceipts()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        \Supply\Model\Receipts::find($id)->delete();
        // $db->query("UPDATE `".DB_PREFIX."_receipts` SET del = '1' WHERE id = '".$id."' LIMIT 1");
        // $db->query("DELETE FROM JMY_receipts WHERE id = '" . $id . "'");
    } else echo 'Не хватает данных';
}

function changeTwoDetail()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["type"], $_POST["value"], $_POST["supply_id"], $_POST["status"])) {
        $id = $_POST["id"];
        $supply_id = $_POST["supply_id"];
        $status = $_POST["status"];
        $type = $_POST["type"];
        $value = trim($_POST["value"]) === "" ? 'null' : "'" . $_POST["value"] . "'";
        if ($value === 'null') {
            $date = 'null';
        } else {
            $date = "'" . date("Y-m-d") . "'";
        }
        $db->query("UPDATE " . DB_PREFIX . "_supply_detail SET " . $type . " = " . $value . ", status = '" . $status . "', sos_price_date = " . $date . " WHERE id = '" . $id . "' LIMIT 1");
    } else echo 'Не хватает данных';
}

function confirmExecutionTwoDetail()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $date = date("Y-m-d H:i:s");
        $db->query("UPDATE `" . DB_PREFIX . "_supply` SET status = '2', date_end = '".$date."' WHERE id = '" . $id . "'");

        $query = $db->query("SELECT sa.uid, sa.id, sa.numb as sa_numb FROM " . DB_PREFIX . "_supply as su LEFT JOIN " . DB_PREFIX . "_sales as sa ON(su.sales_id = sa.id) WHERE su.id = '" . $id . "' LIMIT 1");
        $r = $db->getRow($query);
        if (!empty($r["uid"])) {
            $attr['uid'] = $r["uid"];
            $attr['title'] = 'Данные по продаже №' . $r["sa_numb"] . ' актуализированы';
            $attr['text'] = 'Данные по продаже №' . $r["sa_numb"] . ' актуализированы';
            $attr['icon'] = config('app.url').'/files/push/new_storage.png';
            $attr['url'] = config('app.url').'/administration/module/sales/order/' . $r["id"];
            $attr['pNotif'] = true;
            $attr['pPush'] = true;
            sendAllNotif($attr);
        }
        
        $query = $db->query("SELECT sdid FROM ".DB_PREFIX."_supply_detail WHERE sid = '".$id."'");
        if($db->numRows($query) > 0){
            $sales_id = [];
            while($p = $db->getRow($query)){
                $sales_id[] = $p["sdid"];

                \Sales\Model\SalesDetailStatusLog::create([
                    "sales_detail_id" => $p["sdid"], 
                    "status" => 3, 
                    "url" => "/administration/module/supply/twodetail/".$id,
                ]);
            }

            \Sales\Model\SalesDetail::find($sales_id)->update(["status" => 3]);
        }
    } else echo 'Не хватает данных';
}

function getListNoFileTwoDetail()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $query = $db->query("SELECT * FROM " . DB_PREFIX . "_supply_detail WHERE sid = '" . $id . "' AND sos_bill_aid IS NULL");
        echo '<div class="checkbox"><label><input type="checkbox" id="checkAll">Выбрать все</label></div>
            <ul style="padding-left: 0; max-height: 200px; overflow-x: auto; list-style-type: none;">';
        while ($r = $db->getRow($query)) {
            echo '<li><div class="checkbox"><label><input type="checkbox" name="checkbox[]" value="' . $r["id"] . '" class="local_check" data-id="' . $r["id"] . '">' . $r["s_name"] . (empty($r["s_sku"]) ? '' : ' (' . $r["s_sku"] . ')') . '</label></div></li>';
        }
        echo '</ul>';
    } else echo 'Не хватает данных';
}

function saveBillTwoDetail()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];

        $file = isset($_POST["file"]) ? $_POST["file"] : false;
        $attribute = [];
        $attribute["name"] = "1";
        $attribute["file"] = $file;
        $attribute["module"] = 'supply';
        $attribute["mod"] = 'twodetail_bill';
        $attribute["id"] = $id;
        $attribute["comm_id"] = $id;
        $attribute["ftp"] = "StorageFTP";
        $rid = uploaderMaster("download", $attribute);

        $checkbox = $_POST["checkbox"];
        $str = implode(", ", $checkbox);
        $db->query("UPDATE " . DB_PREFIX . "_supply_detail SET sos_bill_aid = '" . $rid . "' WHERE id IN (" . $str . ")");
    } else echo 'Не хватает данных';
}

function getBillListTwoDetail()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"])) {
        $id = $_POST["id"];

        $query = $db->query("SELECT * FROM " . DB_PREFIX . "_attach WHERE `pid` = '" . $id . "' AND `module` = 'twodetail_bill'");
        while ($r = $db->getRow($query)) {
            echo '<div data-id="' . $r["id"] . '"><a href="/download/' . $r["id"] . '" target="_blank">' . $r["name"] . '</a><i class="fas fa-pencil-alt ml20 cp text-primary editBill"></i><i class="fas fa-trash-alt ml20 cp text-danger removeBillAll"></i></div>';
        }
    } else echo 'Не хватает данных';
}

function removeBillAllTwoDetail()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["supply_id"])) {
        $id = $_POST["id"];
        $supply_id = $_POST["supply_id"];

        $db->query("UPDATE " . DB_PREFIX . "_supply_detail SET sos_bill_aid = null WHERE sid = '" . $supply_id . "' AND sos_bill_aid = '" . $id . "'");

        $query = $db->query("SELECT url FROM " . DB_PREFIX . "_attach WHERE id = '" . $id . "' LIMIT 1");
        $r = $db->getRow($query);
        unlink($r["url"]);
        $db->query("DELETE FROM " . DB_PREFIX . "_attach WHERE id = '" . $id . "' LIMIT 1");
    } else echo 'Не хватает данных';
}

function getBillListDetailTwoDetail()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["supply_id"])) {
        $id = $_POST["id"];
        $supply_id = $_POST["supply_id"];
        $query = $db->query("SELECT * FROM " . DB_PREFIX . "_supply_detail WHERE sos_bill_aid = '" . $id . "' AND sid = '" . $supply_id . "'");
        echo '<div class="checkbox"><label><input type="checkbox" id="checkAll">Выбрать все</label></div>
            <ul style="padding-left: 0; max-height: 200px; overflow-x: auto; list-style-type: none;">';
        while ($r = $db->getRow($query)) {
            echo '<li><div class="checkbox"><label><input type="checkbox" name="checkbox[]" value="' . $r["id"] . '" class="local_check" data-id="' . $r["id"] . '">' . $r["s_name"] . (empty($r["s_sku"]) ? '' : ' (' . $r["s_sku"] . ')') . '</label></div></li>';
        }
        echo '</ul>';
    } else echo 'Не хватает данных';
}

function removeBillTwoDetail()
{
    global $db, $allowFuncs, $url, $core;
    if (isset($_POST["id"], $_POST["supply_id"])) {
        $id = $_POST["id"];
        $supply_id = $_POST["supply_id"];
        $checkbox = $_POST["checkbox"];
        $str = implode(", ", $checkbox);

        $db->query("UPDATE " . DB_PREFIX . "_supply_detail SET sos_bill_aid = null WHERE id IN (" . $str . ")");

        $query = $db->query("SELECT id FROM " . DB_PREFIX . "_supply_detail WHERE sid = '" . $supply_id . "' AND sos_bill_aid = '" . $id . "'");
        if ($db->numRows($query) == 0) {
            $query = $db->query("SELECT url FROM " . DB_PREFIX . "_attach WHERE id = '" . $id . "' LIMIT 1");
            $r = $db->getRow($query);
            unlink($r["url"]);
            $db->query("DELETE FROM " . DB_PREFIX . "_attach WHERE id = '" . $id . "' LIMIT 1");
        }
    } else echo 'Не хватает данных';
}

function checkSendCount()
{
    global $db, $allowFuncs, $url, $core;
    /*
        $count = isset($_POST["count"]) ? $_POST["count"] : false;
        $id = isset($_POST["id"]) ? $_POST["id"] : false;
        $query = $db->query("SELECT sd.count, sd.id FROM ".DB_PREFIX."_supply_order_detail as sod LEFT JOIN ".DB_PREFIX."_supply_detail as sd ON(sd.id = sod.supply_d) WHERE sod.id = '".$id."' LIMIT 1");
        $r = $db->getRow($query);
        if(!empty($r["id"])){
            $countSum = $r["count"];
            $query = $db->query("SELECT id, count FROM ".DB_PREFIX."_supply_order_detail as sod WHERE supply_d = '".$r["id"]."'");
            while($s = $db->getRow($query)){
                if($s["id"] !== $id){
                    $countSum -= $s["count"];
                }
            }
            if($countSum < $count){
                echo 'error';
            }
        }
        */
}

function delTovSupplyDetail(){
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    $sid = isset($_POST["sid"]) ? $_POST["sid"] : false;
    if ($id) {
        if(!is_array($id)) $id = [$id];
        foreach(\Supply\Model\SupplyDetail::with(["sales_detail" => function($query){
            $query->withTrashed();
        }])->whereIn('id', $id)->get() as $item){
            $item->sales_detail->supply_detail_id = null;
            $item->sales_detail->save();
            $item->delete();
        }

        require_once(ROOT.'usr/modules/supply/admin/sub/update_status_supply_detail.php');
        update_status_supply_detail($sid);
    }
}

function delTovReceipt()
{
    global $db, $allowFuncs, $url, $core;
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    if(is_array($id)){
        $id_str = implode(", ", $id);
    } else {
        $id_str = $id;
    }
    $receipts_id = isset($_POST["receipts_id"]) ? $_POST["receipts_id"] : false;
    if($id && $receipts_id){
        $db->query("DELETE FROM `JMY_receipts_detail` WHERE id IN(" . $id_str . ")");
    
        $query = $db->query("SELECT * FROM `" . DB_PREFIX . "_receipts_detail` WHERE rid = '" . $receipts_id . "'");
        $total = 0;
        $nds = 0;
        while ($r = $db->getRow($query)) {
            $total += $r["total"];
            $nds += $r["nds"];
        }
        $total = round($total, 2);
        $nds = round($nds, 2);
        $db->query("UPDATE `" . DB_PREFIX . "_receipts` SET total = '" . $total . "', nds = '" . $nds . "' WHERE id = '" . $receipts_id . "' LIMIT 1");
    }
}

function check_active_receipts()
{
    global $db, $allowFuncs, $url, $core;
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    $query = $db->query("SELECT active FROM " . DB_PREFIX . "_receipts WHERE id = '" . $id . "' LIMIT 1");
    $r = $db->getRow($query);
    echo $r["active"];
}

function loadFileSupplyOrder()
{
    $supply_id = isset($_POST["supply_id"]) ? $_POST["supply_id"] : false;
    $file = isset($_POST['file']) ? $_POST['file'] : false;

    $attribute = [];
    $attribute["name"] = "";
    $attribute["file"] = $file;
    $attribute["module"] = "supply";
    $attribute["mod"] = "supply";
    $attribute["id"] = $supply_id;
    $attribute["comm_id"] = $supply_id;
    $attribute["ftp"] = "StorageFTP";
    uploaderMaster("download", $attribute);
}

function updateValueSupplyDetail()
{
    global $db, $allowFuncs, $url, $core;
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    $supply_id = isset($_POST["supply_id"]) ? $_POST["supply_id"] : false;
    $field = isset($_POST["field"]) ? $_POST["field"] : false;
    $value = isset($_POST["value"]) ? "'" . $_POST["value"] . "'" : 'null';
    if ($id && $field && $supply_id) {
        $db->query("UPDATE " . DB_PREFIX . "_supply_detail SET `" . $field . "` = " . $value . " WHERE id = '" . $id . "' LIMIT 1");
        if ($field === "status") {
            require_once(ROOT.'usr/modules/supply/admin/sub/update_status_supply_detail.php');
            update_status_supply_detail($supply_id);
        }
    }
}

function checkZeroSumSupplyOrder(){
    global $db;
    $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;
    if($checks){
        $id_str = implode(", ", $checks);
        $query = $db->query("SELECT sod.*, c.title FROM ".DB_PREFIX."_supply_order_detail as sod LEFT JOIN ".DB_PREFIX."_catalog as c ON(c.id = sod.cid) WHERE sod.id IN(".$id_str.")");
        if($db->numRows($query) > 0){
            $error = [];
            while($r = $db->getRow($query)){
                if(empty($r["total"])){
                    $error[] = $r["title"];
                }
            }
            if(count($error) > 0){
                echo implode(", ", $error);
            }
        }
    }
}

function check_supply_order_for_fcontrol(){
    global $db;
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    if($id){
        require_once(ROOT.'usr/modules/supply/admin/sub/send.php');
        check_fcontrol($id);
    } else {
        echo 'error';
    }
}

function send_supply_order_for_control(){
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    $date = empty($_POST["date"]) ? \Carbon\Carbon::now() : $_POST["date"];
    $comment = empty($_POST["comment"]) ? '' : $_POST["comment"];
    if($id && $date){
        if(!empty($comment)){
            \Boot\Models\CommentsAdmin::create(["text" => $comment, "module" => "supply_order_control", "module_id" => $id]);
        }
        //сохраняем дату оплаты
        $supply_order = \Supply\Model\SupplyOrder::findOrFail($id);
        $supply_order->date_pay = $date;
        $supply_order->save();
        //если нет продажи, создаем заявку Антону
        if(empty($supply_order->sales_id)){
            (new \Module)->supply_to_control($id);
        } else {
            //инчае отправляем заявку отделу продаж
            $sales = \Sales\Model\Sales::findOrFail($supply_order->sales_id);
            $confirm_supply_order = $sales->confirm_supply_order;
            $confirm_supply_order[] = (String)$id;
            $sales->confirm_supply_order = $confirm_supply_order;
            $sales->save();

            $supply_order->control = 1;
            $supply_order->control_send = \Carbon\Carbon::now();
            $supply_order->save();

            $uid_send = [];
            $uid_send[] = $sales->uid;

            foreach(\Supply\Model\Supply::where("sales_id", $supply_order->sales_id) as $item){
                $uid_send[] = $item->uid;
            }

		    $attr = [];
		    $attr['uid'] = array_unique($uid_send);
		    $attr['title'] = 'Новая заявка на подтверждение закупки!';
		    $attr['text'] = 'Поступила новая заявка на подтверждение закупки, нажмите чтобы посмотреть!';
		    $attr['icon'] = config('app.url').'/files/push/new_order.png';
		    $attr['url'] = config('app.url').'/administration/module/sales/order/'.$supply_order->sales_id;
		    $attr['pNotif'] = true;
		    $attr['pPush'] = true;
            sendAllNotif($attr);
        }
    }
}

function get_stock_source(){
    global $db;
    $query = $db->query("SELECT id, name FROM ".DB_PREFIX."_stock WHERE name IS NOT NULL");
    $stock = [];
    while($r = $db->getRow($query)){
        if(!empty($r["name"])){
            $stock[] = [
                "value" => $r["id"],
                "text" => $r["name"],
            ];
        }
    }
    echo json_encode($stock, JSON_UNESCAPED_UNICODE);
}

function set_mas_date_supply(){
    global $db;
    $checks = isset($_POST["checks"]) ? $_POST["checks"] : [];
    $checks_str = implode(", ", $checks);
    $date = isset($_POST["date"]) ? $_POST["date"] : date("Y-m-d");
    if(count($checks) > 0){
        $db->query("UPDATE ".DB_PREFIX."_supply_detail SET date_post = '".$date."' WHERE id IN(".$checks_str.")");
    }
}

function price_active_change_supply_order(){
    global $db;
    $price_id = isset($_POST["price_id"]) ? $_POST["price_id"] : false;
    if($price_id){
        $query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog_price WHERE id = '".$price_id."' LIMIT 1");
        if($db->numRows($query) > 0){
            $r = $db->getRow($query);
            $active = 1;
            if($r["active"] === '1'){
                $active = 0;
            }
            $db->query("UPDATE ".DB_PREFIX."_catalog_price SET active = '".$active."', daemon = 0 WHERE id = '".$price_id."' LIMIT 1");
            CatalogPriceDaemon::key_create();
        }
    }
}

function check_status_confirm_receipts(){
    global $db;
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    if($id){
        $query = $db->query("SELECT * FROM ".DB_PREFIX."_receipts WHERE id = '".$id."' LIMIT 1");
        if($db->numRows($query) > 0){
            $r = $db->getRow($query);
            if($r["active"] === "1"){
                echo "active";
            }
        }
    }
}

function get_receipts_supply(){
    global $db;
    $id = isset($_POST["id"]) ? $_POST["id"] : false;
    if($id){
        $query = $db->query("SELECT r.* FROM `".DB_PREFIX."_receipts_detail` as rd LEFT JOIN `".DB_PREFIX."_receipts` as r ON(r.id = rd.rid) 
        WHERE rd.supply_detail_id = '".$id."'");
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                echo '<div><a style="color:'.((int)$r["active"] === 1 ? 'green' : 'red').';" href="/administration/module/supply/receipts_detail/'.$r["id"].'" target="_blank">Поступление №'.$r["numb"].'</a></div>';
            }
        } else {
            echo 'Поступления отсутствуют';
        }
    }
}

function supply_logistic_status(){
    global $db;
    $request = $_POST;
    $id = $request["id"];
    $type = $request["type"];
    $list = [];
    if($type == "supply"){
        $query = $db->query("SELECT id FROM `".DB_PREFIX."_supply_order_detail` WHERE supply_o = '{$id}'");
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                $list[] = $r["id"];
            }
        }
    } else {
        $list[] = $id;
    }
    if(count($list) > 0){
        $list_str = implode(", ", $list);
        $query = $db->query("SELECT ld.*, so.date_end as so_date_end, sod.date_end as sod_date_end
        FROM `".DB_PREFIX."_logistics_detail` as ld 
        LEFT JOIN `".DB_PREFIX."_supply_order_detail` as sod ON(sod.id = ld.sod_id)
        LEFT JOIN `".DB_PREFIX."_supply_order` as so ON(so.id = sod.supply_o)
        WHERE ld.status = 4 AND ld.sod_id IN({$list_str})");
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                $date_end = empty($r["sod_date_end"]) ? $r["so_date_end"] : $r["sod_date_end"];
                if(date("Y-m-d", strtotime($date_end)) > date("Y-m-d")){
                    \Logistics\Model\LogisticsDetail::findOrFail($r["id"])->update(["status" => 1]);
                }
            }
        }
    }
}