<?php

use Boot\Support\Currency;
	global $db, $allowFuncs, $url, $core;
	
    $allowFuncs = array_merge($allowFuncs, ["setNewSales", "assemblySales"]);
    
    function setNewSales(){
        global $db, $allowFuncs, $url, $core;
        $mas = $_POST["cid"];
        $mas = json_decode($mas, true);
        $uid = $core->auth->user_info['id'];
        $query = $db->query("SELECT oid FROM JMY_users_org WHERE uid = '".$uid."' AND choose = '1' LIMIT 1");
        $r = $db->getRow($query);
        $query = $db->query("SELECT object FROM JMY_users_object WHERE uid = '".$uid."' AND choose = '1' LIMIT 1");
        $r1 = $db->getRow($query);
        $oid = empty($r["oid"]) ? 'null' : "'".$r["oid"]."'";
        $object = empty($r["object"]) ? 'null' : "'".$r["object"]."'";
		
	
		$date = \Carbon\Carbon::now();
		$org = '1';
		$numb_array = \Boot\Support\Module::numb('sales', $org, $date);
		$numb = $numb_array['numb'];
		$numb_counter = $numb_array['numb_counter'];
		
		
		
		
		
        $db->query("INSERT INTO `JMY_sales`(`numb_counter`, `numb`, `org`, `oid`, `object`, `date`, `date_add`, `nds_includes`, `pay`, `sales_type`, `sales_cat`, `status`, `md5`, `uid_send`) VALUES (".$numb_counter.", '".$numb."', '1', ".$oid.", ".$object.", '".$date."', '".time()."', '1', '0', '1', '1', '1', '".md5(time().rand())."', '".$uid."')");
        $ii = $db->insert_id();
        $total_all = 0;
        $nds_all = 0;
        foreach($mas as $val){
            $cid = mb_substr($val["id"], 2);
            $query = $db->query("SELECT c.* FROM JMY_catalog as c WHERE c.id = '".$cid."' LIMIT 1");
            $catalog = $db->getRow($query);
            $query = $db->query("SELECT sum FROM JMY_catalog_price as cp LEFT JOIN JMY_catalog_price_detail as cpd ON(cp.id = cpd.price_id) WHERE cp.price_type = '3' AND cpd.cid = '".$cid."' AND cp.active = '1' ORDER BY cp.date DESC");
            $price = $db->getRow($query);
            if(empty($price["sum"])){
                $query = $db->query("SELECT sum FROM JMY_catalog_price as cp LEFT JOIN JMY_catalog_price_detail as cpd ON(cp.id = cpd.price_id) WHERE cp.price_type = '2' AND cpd.cid = '".$cid."' AND cp.active = '1' ORDER BY cp.date DESC");
                $euro = $db->getRow($query);
                if(empty($euro["sum"])){
                    $price_type = "4";
                    $s_price = 0;
                } else {
                    $price_type = "2";
                    $s_price = round($euro["sum"] * Currency::get_exchange()->get(978), 2);
                }
            } else {
                $price_type = "3";
                $s_price = $price["sum"];
            }
            $ndsVal = \Price::getNds("3");
            $nds = $s_price / ($ndsVal + 100) * $ndsVal;
            $nds = round($nds*$val["num"], 2);
            $total = $val["num"] * $s_price;
            $total = round($total, 2);
            $db->query("INSERT INTO `JMY_sales_detail`(`sales_id`, `cid`, `s_sku`, `s_name`, `price_type`, `s_price`, `s_unit`, `count`, `discount`, `discount_sum`, `type_nds`, `nds`, `total`, `status`) VALUES ('".$ii."', '".$cid."', '".$catalog["sku"]."', '".$catalog["title"]."', '".$price_type."', '".$s_price."', '".$catalog["unit"]."', '".$val["num"]."', '0', '0', '3', '".$nds."', '".$total."', '1')");

            $total_all += $total;
            $nds_all += $nds;
        }
        $db->query("UPDATE JMY_sales SET nds = '".$nds_all."', total = '".$total_all."' WHERE id = '".$ii."'");

        $chat = new Chat();
		$chat->create_chat("sales", $ii, null, "Продажа №".$numb, 1);
		$chat->create_chat("sales", $ii, null, "Продажа с сайта №".$numb, 2);
    }

    function assemblySales(){
        $sales_id = isset($_POST["sales_id"]) ? $_POST["sales_id"] : false;
        $checks = isset($_POST["checks"]) ? $_POST["checks"] : false;
        $count_list = isset($_POST["count"]) ? $_POST["count"] : [];
        if($sales_id && $checks){
            \Sales\Support\SalesAssemblySupport::create($sales_id, $checks, ["count" => $count_list]);
        }
    }