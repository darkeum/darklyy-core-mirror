<?php
    global $db, $allowFuncs, $url, $core;
	
	$allowFuncs = array_merge($allowFuncs, ["updateProductsRealization", "getStockList", "fastChangeRealization", "realization_active_ajax", "delRealizationOrder", "removeReservRealization", "changeRealizationOrder", "torg12Realization", "torg12Data", "getPostOrgTorg", "torg12Save", "syncSales", "saveFileRealization", "delFileRealization", "checkFileRealization", "realization_sync", "check_minus_count_realization", "createrReceiptsByRealization", "torgSave", "torgGetBank", "torgGetProcuratory", "check_torg", "setPositionRealization"]);
    
    function updateProductsRealization(){
        if(isset($_POST["saveData"], $_POST["mainId"], $_POST["totalAll"], $_POST["ndsAll"])){
            $saveData = $_POST["saveData"];
            if(mb_strlen($saveData) > 0){
                $saveData = json_decode($saveData, true);
                foreach($saveData as $key => $val){
                    $discount_sum = round(($val["edit"] * $val["count"] - $val["total"]), 2);
                    $nds_type = $val["typeNds"];
                    $realization_detail = \Realization\Model\RealizationDetail::findOrFail($key);
                    $realization_detail->count =        $val["count"];
                    $realization_detail->s_price =      $val["edit"];
                    $realization_detail->total =        $val["total"];
                    $realization_detail->nds =          $val["nds"];
                    $realization_detail->nds_type =     \Price::getNds($nds_type, ["mode" => true]);
                    $realization_detail->discount =     $val["discount"];
                    $realization_detail->discount_sum = $discount_sum;
                    $realization_detail->s_name =       $val["s_name"];
                    $realization_detail->save();
                }
            }
            
            $mainId =  $_POST["mainId"];
            $totalAll =  $_POST["totalAll"];
            $ndsAll =  $_POST["ndsAll"];
            $realization = \Realization\Model\Realization::findOrFail($mainId);
            $realization->total = $totalAll;
            $realization->nds = $ndsAll;
            $realization->save();
        } else {
            echo "Не хватает данных";
        }
    }
    
    function fastChangeRealization(){
        if(isset($_POST["pk"], $_POST["name"])){
            $id = $_POST["pk"];
            $name = $_POST["name"];
            $value = $_POST["value"];
            $realization = \Realization\Model\Realization::find($id);
            $realization->$name = $value;
            $realization->save();
        } else echo 'Не хватает данных';
    }
    
    function getStockList(){
        global $db, $allowFuncs, $url, $core;
        $request = $_POST;
        $id = empty($request["id"]) ? false : $request["id"];
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_realization` WHERE id = '".$id."' LIMIT 1");
        $realization = $db->getRow($query);
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_stock` WHERE org = '".$realization["org"]."'");
        $result = [];
        $i = 0;
        while($r = $db->getRow($query)){
            if(!empty($r['name'])){
                $result[$i]['value'] = $r['id'];
                $result[$i]['text'] = $r['name'];
                $i++;
            }
        }
        $result = json_encode($result);
        echo $result;
    }
    
    function realization_active_ajax(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST['realization_id'])){
            require_once(ROOT.'/usr/modules/realization/admin/sub/realization_active.php');
            require_once(ROOT.'/usr/modules/realization/admin/sub/realization_automatic.php');
            $realization_id = $_POST['realization_id'];
            realization_active($realization_id);
        } else echo 'Не хватает данных';
    }
	
	function realization_sync(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST['realization_id']))
		{
			$realization_id = $_POST['realization_id'];	
            $query = $db->query("SELECT * FROM JMY_realization WHERE id='".$realization_id."' LIMIT 1");
			if($db->numRows($query) > 0) 
			{	
				$realization = $db->getRow($query);   
                $sync = new Sync\Sync;      
                $sync->syncRegister('realization', $realization_id, '1', $realization['org']);
				echo '1';
			}
			else
			{
				echo '2';
			}
        } else echo '2';
    }
    
    function delRealizationOrder(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"], $_POST["mainId"])){
            $id = $_POST["id"];
            $mainId = $_POST["mainId"];
            $db->query("DELETE FROM `JMY_realization_detail` WHERE id = '".$id."' LIMIT 1");
            
            $total = 0;
            $nds = 0;
            $query = $db->query("SELECT * FROM JMY_realization_detail WHERE rid = '".$mainId."'");
            while($r = $db->getRow($query)){
                $total += $r["total"];
                $nds += $r["nds"];
            }
            $db->query("UPDATE JMY_realization SET total = '".$total."', nds = '".$nds."' WHERE id = '".$mainId."'");
        } else echo 'Не хватает данных';
    }
    
    function removeReservRealization(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST['realization_id'])){
            $realization_id = $_POST['realization_id'];
            $query = $db->query("SELECT * FROM ".DB_PREFIX."_realization_detail WHERE rid = '".$realization_id."'");
            $reserv = new Reserv();
            while($r = $db->getRow($query)){
                $reserv->end_reserv($r["sdid"], "sales", 1, $r["count"]);
            }
        } else echo 'Не хватает данных';    
    }

    function changeRealizationOrder(){
        $name = $_POST["name"];
        $value = $_POST["value"];
        $pk = $_POST["pk"];
        $realization = \Realization\Model\Realization::find($pk);
        switch($name){
            case "date":
                if(date("Y", strtotime($value)) != date("Y", strtotime($realization->date))){
                    $numb_array = \Boot\Support\Module::numb('realization', $realization->org, $value);
                    $realization->numb = $numb_array['numb'];
                    $realization->numb_counter = $numb_array['numb_counter'];
                }
            break;
        }
        $realization->{$name} = $value;
        $realization->save();
    }

    function torg12Realization(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $query = $db->query("SELECT print_info FROM ".DB_PREFIX."_realization WHERE id = '".$id."' LIMIT 1");
            $r = $db->getRow($query);
            if(!empty($r["print_info"])){
                echo $r["print_info"];
            } else {
                $array = [
                    "base" => [
                        "doc" => "",
                        "numb" => "",
                        "date" => "",
                    ],
                    "oid" => [
                        "bank" => [
                            "id" => ""
                        ]
                    ],
                    "org" => [
                        "bank" => [
                            "id" => ""
                        ]
                    ],
                    "sign" => [
                        "leader" => [
                            "id" => ""
                        ],
                        "buch" => [
                            "id" => ""
                        ],
                        "ship" => [
                            "post" => "",
                            "data" => ""
                        ],
                    ],
                ];
                $json = json_encode($array, JSON_UNESCAPED_UNICODE);
                echo $json;
            }
        }
    }

    function torg12Data(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];

            $oid_bank_id = [];
            $org_bank_id = [];
            $users_sign_leader = [];
            $users_sign_buch = [];
            $users_sign_ship = [];
            $oid_address = "";

            $query = $db->query("SELECT * FROM JMY_realization WHERE id = '".$id."' LIMIT 1");
            $realization = $db->getRow($query);
            $sales_id = "";
            $sales_date = "";
            if(!empty($realization["sales_id"])){
                $sales_id = $realization["sales_id"];
                
                $query = $db->query("SELECT org, oid, date, numb FROM JMY_sales WHERE id = '".$realization["sales_id"]."' LIMIT 1");
                $sales = $db->getRow($query);
                $sales_date = $sales["date"];

                $query = $db->query("SELECT oid FROM JMY_org WHERE id = '".$sales["org"]."' LIMIT 1");
                $org = $db->getRow($query);

                $query = $db->query("SELECT yaddress FROM JMY_organizations WHERE id = '".$sales["oid"]."'");
                $organizations = $db->getRow($query);
                $oid_address = htmlspecialchars($organizations["yaddress"], ENT_QUOTES);

                $query = $db->query("SELECT * FROM JMY_organizations_bank WHERE oid IN (".$org["oid"].", ".$sales["oid"].")");
                
                while($r = $db->getRow($query)){
                    switch($r["oid"]){
                        case $sales["oid"]:
                            $oid_bank_id[$r["id"]] = $r["rschet"].', '.(empty($r["name_short"]) ? $r["name"] : $r["name_short"]);
                        break;
                        case $org["oid"]:
                            $org_bank_id[$r["id"]] = $r["rschet"].', '.(empty($r["name_short"]) ? $r["name"] : $r["name_short"]);
                        break;
                    }
                }

                $rd = $realization["date"];
                $users_sign = \Boot\Models\UsersSign::where("org", $sales["org"])->where(function($query) use ($rd){
                    $query->orWhere(function($query) use ($rd){
                        $query->whereDate("date_start", "<=", $rd)->whereDate("date_end", ">=", $rd);
                    })->orWhere(function($query){
                        $query->whereNull("date_start")->whereNull("date_end");
                    });
                })->get()->toArray();
                foreach($users_sign as $r){
                    if($r["type"] == 1){
                        switch($r["type_post"]){
                            case "1":
                                $users_sign_leader[$r["id"]] = shortUserName($r["uid"]);
                            break;
                            case "2":
                                $users_sign_buch[$r["id"]] = shortUserName($r["uid"]);
                            break;
                            case "3":
                                $users_sign_ship[$r["id"]] = shortUserName($r["uid"]);
                            break;
                        }
                    } else {
                        switch($r["type_post"]){
                            case "1":
                                switch($r["sign"]){
                                    case "1":
                                        $users_sign_leader[$r["id"]] = shortUserName($r["uid"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от '.date("d.m.Y", strtotime($r["base_date"]));
                                    break;
                                    case "2":
                                        $users_sign_leader[$r["id"]] = shortUserName($r["uid"]).', '.getTypePost($r["type_post"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от '.date("d.m.Y", strtotime($r["base_date"]));
                                    break;
                                    case "3":
                                        $users_sign_leader[$r["id"]] = shortUserName($r["uid"]);
                                    break;
                                }
                            break;
                            case "2":
                                switch($r["sign"]){
                                    case "1":
                                        $users_sign_buch[$r["id"]] = shortUserName($r["uid"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от '.date("d.m.Y", strtotime($r["base_date"]));
                                    break;
                                    case "2":
                                        $users_sign_buch[$r["id"]] = shortUserName($r["uid"]).', '.getTypePost($r["type_post"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от '.date("d.m.Y", strtotime($r["base_date"]));
                                    break;
                                    case "3":
                                        $users_sign_buch[$r["id"]] = shortUserName($r["uid"]);
                                    break;
                                }
                            break;
                            case "3":
                                switch($r["sign"]){
                                    case "1":
                                        $users_sign_ship[$r["id"]] = shortUserName($r["uid"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от '.date("d.m.Y", strtotime($r["base_date"]));
                                    break;
                                    case "1":
                                        $users_sign_ship[$r["id"]] = shortUserName($r["uid"]).', '.getTypePost($r["type_post"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от '.date("d.m.Y", strtotime($r["base_date"]));
                                    break;
                                    case "3":
                                        $users_sign_ship[$r["id"]] = shortUserName($r["uid"]);
                                    break;
                                }
                            break;
                        }    
                    }
                }
            }

            $arr = [
                'января',
                'февраля',
                'марта',
                'апреля',
                'мая',
                'июня',
                'июля',
                'августа',
                'сентября',
                'октября',
                'ноября',
                'декабря'
            ];

            $month = date('n', strtotime($sales_date));

            $array = [
                "base" => [
                    "doc" => "Заказ клиента №".$sales["numb"]." от ".date("d", strtotime($sales_date))." ".$arr[$month - 1]." ".date("Y", strtotime($sales_date)),
                    "numb" => $sales["numb"],
                    "date" => date("Y-m-d", strtotime($sales_date)),
                ],
                "sales_id" => $sales_id,
                "oid" => [
                    "bank" => [
                        "id" => $oid_bank_id,
                    ],
                    "address" => $oid_address,
                    "id" => $sales["oid"],
                ],
                "org" => [
                    "bank" => [
                        "id" => $org_bank_id
                    ],
                    "id" => $sales["org"],
                ],
                "sign" => [
                    "leader" => [
                        "data" => $users_sign_leader
                    ],
                    "buch" => [
                        "data" => $users_sign_buch
                    ],
                    "ship" => [
                        "data" => $users_sign_ship
                    ],
                ],
            ];
            $json = json_encode($array, JSON_UNESCAPED_UNICODE);
            echo $json;
        }
    }

    function getPostOrgTorg(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"], $_POST["realization_id"])){
            $id = $_POST["id"];
            $realization_id = $_POST["realization_id"];
            $query = $db->query("SELECT o.oid 
            FROM `".DB_PREFIX."_realization` as r 
            LEFT JOIN `".DB_PREFIX."_org` as o ON(r.org = o.id)
            WHERE r.id = '".$realization_id."' 
            LIMIT 1");
            $realization = $db->getRow($query);
            $str = "";
            $query = $db->query("SELECT post FROM `".DB_PREFIX."_users_org` WHERE uid = '".$id."' AND oid = '".$realization["oid"]."' LIMIT 1");
            if($db->numRows($query) > 0){
                $users_org = $db->getRow($query);
                $str = $users_org["post"];
            }

            echo $str;
        }
    }

    function torg12Save(){
        global $db, $allowFuncs, $url, $core;

        $realization_id = isset($_POST["realization_id"]) ? $_POST["realization_id"] : null;

        if(!empty($realization_id)){
            $print_info = [];
			
            $print_info['base']['doc'] = isset($_POST["base_doc"]) ? $_POST["base_doc"] : null;
            $print_info['base']['numb'] = isset($_POST["base_numb"]) ? $_POST["base_numb"] : null;
            $print_info['base']['date'] = isset($_POST["base_date"]) ? $_POST["base_date"] : null;
            $print_info['base']['module'] = 'sales';
            $print_info['base']['pid'] = isset($_POST["sales_id"]) ? $_POST["sales_id"] : null;
            
            $print_info['oid']['id'] = isset($_POST["oid_id"]) ? $_POST["oid_id"] : null;
            $print_info['oid']['address'] = isset($_POST["oid_address"]) ? $_POST["oid_address"] : null;
            $print_info['oid']['bank']['id'] = isset($_POST["oid_bank_id"]) ? $_POST["oid_bank_id"] : null; 
            $print_info['oid']['bank']['data'] = isset($_POST["oid_bank_data"]) ? htmlspecialchars($_POST["oid_bank_data"], ENT_QUOTES) : null;
                                    
            $print_info['org']['id'] = isset($_POST["org_id"]) ? $_POST["org_id"] : null;
            $print_info['org']['bank']['id'] = isset($_POST["org_bank_id"]) ? $_POST["org_bank_id"] : null;
            $print_info['org']['bank']['data'] = isset($_POST["org_bank_data"]) ? htmlspecialchars($_POST["org_bank_data"], ENT_QUOTES) : null;
            
            $print_info['sign']['leader']['id'] = isset($_POST["sign_leader_id"]) ? $_POST["sign_leader_id"] : null;
            $print_info['sign']['leader']['data'] = isset($_POST["sign_leader_data"]) ? $_POST["sign_leader_data"] : null;
            $print_info['sign']['buch']['id'] = isset($_POST["sign_buch_id"]) ? $_POST["sign_buch_id"] : null;
            $print_info['sign']['buch']['data'] = isset($_POST["sign_buch_data"]) ? $_POST["sign_buch_data"] : null;
            $print_info['sign']['ship']['id'] = isset($_POST["sign_ship_id"]) ? $_POST["sign_ship_id"] : null;
            $print_info['sign']['ship']['data'] = isset($_POST["sign_ship_data"]) ? $_POST["sign_ship_data"] : null;
            $print_info['sign']['ship']['post'] = isset($_POST["sign_ship_post"]) ? $_POST["sign_ship_post"] : null;
        }

        $json = json_encode($print_info, JSON_UNESCAPED_UNICODE);
        $db->query("UPDATE `JMY_realization` SET `print_info`= '".$json."' WHERE id = '".$realization_id."' LIMIT 1");

    }

    function syncSales(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"], $_POST["type"])){

            $id = $_POST["id"];
            $type = $_POST["type"];

            $totalAll = 0;
            $ndsAll = 0;

            $str = "";
            $where = "";

            switch($type){
                case "1":
                $query = $db->query("SELECT sdid FROM ".DB_PREFIX."_realization_detail WHERE rid = '".$id."'");
                while($r = $db->getRow($query)){
                    $str .= $r["sdid"].", ";
                }
                $str = mb_substr($str, 0, -2);
                $where = "sd.id IN (".$str.")";
                break;
                case "2":
                $query = $db->query("SELECT sales_id FROM ".DB_PREFIX."_realization WHERE id = '".$id."' LIMIT 1");
                $r = $db->getRow($query);
                $where = "sd.sales_id = '".$r["sales_id"]."'";
                break;
            }

            $db->query("DELETE FROM ".DB_PREFIX."_realization_detail WHERE rid = '".$id."'");

            $query = $db->query("SELECT sd.*, s.nds_includes FROM ".DB_PREFIX."_sales_detail as sd LEFT JOIN ".DB_PREFIX."_sales as s ON(s.id = sd.sales_id) WHERE ".$where." AND sd.del = '0'");

            while($rsd = $db->getRow($query)){
                $count = $rsd["count"] - $rsd["count_realization"];
                if($count > 0){
                    $cid = empty($rsd["cid"]) ? 'null' : $rsd["cid"];
                    $sdid = "'".$rsd["id"]."'";
                    $s_name = empty($rsd["s_name"]) ? 'null' : htmlspecialchars($rsd["s_name"], ENT_QUOTES);
                    $s_sku = empty($rsd["s_sku"]) ? 'null' : "'".$rsd["s_sku"]."'";
                    $s_price = empty($rsd["s_price"]) ? 0 : $rsd["s_price"];
                    $s_price_type = empty($rsd["s_price_type"]) ? 'null' : $rsd["s_price_type"];
                    $s_unit = empty($rsd["s_unit"]) ? 'null' : $rsd["s_unit"];
                    $discount = empty($rsd["discount"]) ? 0 : $rsd["discount"];
                    $discount_sum = empty($rsd["discount_sum"]) ? '0' : $rsd["discount_sum"];
                    $nds_type = empty($rsd["type_nds"]) ? 3 : $rsd["type_nds"];
                    $nds_includes =  is_null($rsd["nds_includes"]) ? 1 : $rsd["nds_includes"];
    
                    $ndsSSS = \Price::getNds($nds_type);
    
                    if($nds_includes == 0){
                        $ndsCalc = ($s_price * $count - ($count * $s_price * ($discount / 100))) * ($ndsSSS / 100);
                        $total = $count * $s_price - ($count * $s_price * ($discount / 100)) + $ndsCalc;
                    } else {
                        $total = $count * $s_price - ($count * $s_price * ($discount / 100));
                        $ndsCalc = $total / (100 + $ndsSSS) * $ndsSSS;
                    }

                    $ndsCalc = round($ndsCalc, 2, PHP_ROUND_HALF_UP);
                    $total = round($total, 2, PHP_ROUND_HALF_UP);
    
                    $totalAll += $total;
                    $ndsAll += $ndsCalc;
    
                    $db->query("INSERT INTO JMY_realization_detail (rid, cid, sdid, s_name, s_sku, s_price, s_price_type, s_unit, count, discount, discount_sum, nds, total, status, nds_type) VALUES (".$id.", ".$cid.", ".$sdid.", '".$s_name."', ".$s_sku.", ".$s_price.", ".$s_price_type.", '".$s_unit."', ".$count.", ".$discount.", ".$discount_sum.", ".$ndsCalc.", ".$total.", 1, '".$nds_type."')");
                }
            }

            $totalAll = round($totalAll, 2, PHP_ROUND_HALF_DOWN);
            $totalAll = round($totalAll, 2, PHP_ROUND_HALF_DOWN);

            $db->query("UPDATE JMY_realization SET nds_includes = '".$nds_includes."', total = '".$totalAll."', nds = '".$ndsAll."' WHERE id = '".$id."' LIMIT 1");
            
        } else {
            echo 'Не хватает данных'; 
        }
    }

    function saveFileRealization(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $file = isset($_POST["file"]) ? $_POST["file"] : false; 

            $attribute = [];
            $attribute["name"] = "1";
            $attribute["file"] = $file;
            $attribute["module"] = 'realization';
            $attribute["mod"] = 'realization';
            $attribute["id"] = $id;
            $attribute["comm_id"] = $id;
            uploaderMaster("download", $attribute);
        }
    }

    function checkFileRealization(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `module` = 'realization' AND `pid` = '".$id."'");
            if($db->numRows($query) > 0){
                $val = 1;
            } else {
                $val = 0;
            }
            $db->query("UPDATE ".DB_PREFIX."_realization SET scan = '".$val."' WHERE id = '".$id."' LIMIT 1");
        }
    }

    function delFileRealization(){
        global $db, $allowFuncs, $url, $core;
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE id = '".$id."' LIMIT 1");
            $r = $db->getRow($query);
            unlink(ROOT.$r["url"]);
            $db->query("DELETE FROM ".DB_PREFIX."_attach WHERE id = '".$id."'");
        } 
    }	

    function check_minus_count_realization(){
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $list = \Realization\Support\RealizationSupport::check_minus_count($id);
            $result = ["status" => true, "items" => []];
            if(count($list) > 0){
                $result["status"] = false;
                $items = \Realization\Model\RealizationDetail::find($list)->toArray();
                foreach($items as $item){
                    $result["items"][] = $item;
                }
            }
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }
    }
	
	function createrReceiptsByRealization()
	{
		global $db, $url, $core;
		
		$rid = $_GET['rid'];
		$org = $_GET['org'];	
		$stock = '71';
		$query = $db->query("SELECT * FROM `".DB_PREFIX."_realization`  WHERE id = '".$rid."'");
		if($db->numRows($query) > 0)
		{
			$rr = $db->getRow($query);							
			$date = (!empty($_POST['date']) ? $_POST['date'] : $rr['date']);
			$orgInfo = getOrgInfo($rr['org'], 'array');
			$oid = $orgInfo['org']['oid'];		
			$uid = $core->auth->user_info['id'];
			$object = $rr['object'];
			$bill_code = '';
			$querySales = $db->query("SELECT numb FROM `".DB_PREFIX."_sales`  WHERE id = '".$rr['sales_id']."'");
			if($db->numRows($querySales) > 0)
			{
				$rSales = $db->getRow($querySales);	
				$bill_code = $rSales['numb'];
			}			
			$date_bill = date('Y-m-d', strtotime($date));
			$date_work = $date_bill;
			$date_ship = $date_bill;
			$date_end = $date_bill;
			$comment = '';
			$status = '1';
			$delivery = '1';
			$pay = '1';
			$numb_array = getNumbCounter('supply_order', $org);
			$numb = $numb_array['numb'];
			$numb_counter = $numb_array['numb_counter'];
			$insert = "INSERT INTO `" . DB_PREFIX . "_supply_order` ( `id` ,  `numb_counter` , `numb` , `org` , `oid` , `uid` ,  `object` ,  `bill_code` , `date_bill` , `date_work` , `date_ship` , `date_end` , `comment` , `status`, `delivery`) VALUES (NULL, '" . $numb_counter . "', '" . $numb . "', '" . $org . "', '" . $oid . "', '" . $uid . "', '" . $object . "', '" . $bill_code . "', '" . $date_bill . "', '" . $date_work . "', '" . $date_ship . "', '" . $date_end . "', '" . $comment . "', '".$status."', ".$delivery.");";
			//echo $insert;
			//exit;			
			if($db->query($insert))
			{				
				$supply_id = $db->insert_id();					
			}				
			$supply_order_id = $supply_id;
			$query = $db->query("SELECT sd.*, s.nds_includes FROM JMY_realization_detail as sd LEFT JOIN JMY_realization as s ON(s.id = sd.rid) WHERE rid IN(".$rid.")");
			$data = [];
			$total = 0;
			$nds = 0;			
				while($r = $db->getRow($query)){
					$nds_includes = 1; //$r["nds_includes"]
					$nds_type = 3; ////$r["type_nds"]
					$price_data = [
						"nds_includes" => $nds_includes,
						"discount" => $r["discount"],
						"price" => $r["s_price"],
						"count" => $r["count"],
						"nds_type" => $nds_type, 
					];
					$result = \Price::get_all($price_data);
					$data[] = "('".$r["cid"]."', '".$supply_order_id."', '".$r["count"]."', '1', '".$r["s_unit"]."', '".$r["s_name"]."', '".$r["s_sku"]."', '".round($r["total"]/$r['count'], 2)."', '".$r["total"]."', '".$nds_type."', '".$r["nds"]."')";

					$total = $total +    $r["total"];
					$nds = $nds + $r["nds"];
				}
			dd($data);
			dd($total); 
			dd($nds); 
			$db->query("INSERT INTO JMY_supply_order_detail (`cid`, `supply_o`, `count`, `status`, `unit`, `s_name`, `s_sku`, `price_now`, `total`, `type_nds`, `nds`) VALUES ".implode(", ", $data));
			$db->query("UPDATE `".DB_PREFIX."_supply_order` SET total = '".$total."',  nds = '".$nds."' WHERE id = ".$supply_order_id." LIMIT 1");	
			
			
			
			$id = $supply_id;
			$query = $db->query("SELECT so.sales_id, so.total, so.oid, o.name, o.inn, so.nds_includes, so.nds, so.object FROM JMY_supply_order as so LEFT JOIN JMY_organizations as o ON(so.oid = o.id) WHERE so.id = '".$id."' LIMIT 1");
			$res = $db->getRow($query);
			$sales_id = isset($res["sales_id"]) ? "'".$res["sales_id"]."'" : 'null';
			$uid = $core->auth->user_info['id'];
			$numDoc = $rr["numb"];
			$datePost = $date_ship;
			$stock_id = $stock;
			$nds_includes =  is_null($res["nds_includes"]) ? 1 : $res["nds_includes"];
			$object = empty($res["object"]) ? "null" : "'".$res["object"]."'";
			
			$numb_array = getNumbCounter('receipts', $org);
			$numb = $numb_array['numb'];
			$numb_counter = $numb_array['numb_counter'];
			$db->query("INSERT INTO JMY_receipts (numb_counter, numb, sales_id, supply_id, uid, date, active, status, oid, oid_name, inn, doc_number, date_receipts, org, stock_id, nds_includes, object) VALUES('".$numb_counter."', '".$numb."', ".$sales_id.", '".$id."', '".$uid."', '".date("Y-m-d H:i:s")."', '0', '1', '".$res["oid"]."', '".$res["name"]."', '".$res["inn"]."', '".$numDoc."', '".$datePost."', '".$org."', '".$stock_id."', '".$nds_includes."', ".$object.")");
			$receipt_id = $db->insert_id();
			relationsSave('supply_order', $id, 'receipts', $receipt_id);
			$file = isset($_POST['file']) ? $_POST['file'] : false;
			
						$attribute = [];
						$attribute["name"] = "";
						$attribute["file"] = $file;
						$attribute["module"] = 'supply';
						$attribute["mod"] = 'receipts';
						$attribute["id"] = $receipt_id;
						$attribute["comm_id"] = $receipt_id;
						uploaderMaster("download", $attribute);
			
			//$idMas = $_POST["checks"];
			$str = '';
			$echoStr = '';
			$total = 0;
			$totalAll = 0;
			$nds = 0;
			$ndsAll = 0;
		
			$query = $db->query("SELECT so.nds_includes, sod.id, sod.supply_o,  sod.cid, sod.count, sod.count_receipts, sod.price_now, sod.sale_detail_id as sale_detail_id, sod.total, sod.type_nds, sod.nds, sod.unit, sod.s_name, sod.s_sku FROM JMY_supply_order as so LEFT JOIN JMY_supply_order_detail as sod ON(so.id = sod.supply_o) WHERE sod.supply_o = '".$supply_id."' ");
			
			while($res = $db->getRow($query)){
				$count = $res["count"] - $res["count_receipts"];

				// $ndsSSS = getNds($res["type_nds"]);

				// if($nds_includes == 0){
				// 	$ndsCalc = $res["price_now"] * ($ndsSSS / 100) * $count;
				// 	$total = $count * $res["price_now"] + $ndsCalc;
				// } else {
				// 	$total = $count * $res["price_now"];
				// 	$ndsCalc = $total / (100 + $ndsSSS) * $ndsSSS;
				// }

				// $total = round($total, 2);
				// $ndsCalc = round($ndsCalc, 2);

				$data = \Price::get_all([
					"price" => $res["price_now"], 
					"count" => $count, 
					"nds_includes" => $nds_includes, 
					"nds_type" => $res["type_nds"]
				]);

				$total = $data["total"];
				$ndsCalc =  $data["nds"];
				
				if($uid == 1){
					$count = $res["count"];
					$total = $res["total"];
					$ndsCalc =  $res["nds"];
				}
				

				$sales_detail_id = isset($res["sale_detail_id"]) ? "'".$res["sale_detail_id"]."'" : 'null';
				$str .= "('".$receipt_id."', '".$res["cid"]."', ".$sales_detail_id.", '".$res["id"]."', '".htmlspecialchars($res["s_name"], ENT_QUOTES)."', '".$res["s_sku"]."', '".$res["unit"]."', '".$count."', '".$res["price_now"]."', '1', ".$total.", ".$res["type_nds"].", ".$ndsCalc."), ";
				$totalAll += $total;
				$ndsAll += $ndsCalc;

				$echoStr .= $res["id"].'; ';
			}
			
			$totalAll = round($totalAll, 2);
			$ndsAll = round($ndsAll, 2);

			$db->query("UPDATE JMY_receipts SET total = '".$totalAll."', nds = '".$ndsAll."' WHERE id = '".$receipt_id."' LIMIT 1");
			$db->query("INSERT INTO JMY_receipts_detail (rid, cid, sales_detail_id, supply_detail_id, name, sku, unit, count, price, status, total, type_nds, nds) VALUES ".mb_substr($str, 0, -2)."");
			
				
			$queryAttach = $db->query("SELECT * FROM `".DB_PREFIX."_attach`  WHERE `module` = 'realization' AND `pid` = '".$rid."' ");
			if($db->numRows($queryAttach) > 0)
			{
				$dir = 'files/supply/'.$receipt_id.'/';
				if (!file_exists(ROOT.$dir))
				{
					mkdir(ROOT.$dir, 0777);
						
				}
				while($rAttach = $db->getRow($queryAttach))
				{
					$file = ROOT.$rAttach['url'];
					$newname = uniqid().'_'.$rAttach['name'];
					$newfile = $dir.$newname;
					copy($file, ROOT.$newfile);
                    $db->query("INSERT INTO `JMY_attach`(`id`, `name`, `url`, `pid`, `module`, `downloads`, `access`) VALUES (NULL,'".$newname."','".$newfile."','".$receipt_id."','receipts','0','1');");
				}
			}
		}
    }
    
function torgSave(){
    $request = $_POST;
    if(!empty($request["procuratory_id"])){
        $rtp = $request["procuratory_id"];
        if($rtp == -1){
            $rtp = \Realization\Model\RealizationTorgProcuratory::create($request);
            $request["procuratory_id"] = $rtp;
        } else {
            \Realization\Model\RealizationTorgProcuratory::findOrFail($rtp)->update($request);
        }
    } 
    if(empty($request["id"])){
        \Realization\Model\RealizationTorg::create($request);
    } else {
        \Realization\Model\RealizationTorg::findOrFail($request["id"])->update($request);
    }
}

function torgGetBank(){
    $request = $_POST;
    $torg = new \Realization\Torg;
    $oid = $request["oid"];
    $data = $torg->get_bank($oid);
    echo $torg->data_to_option(["data" => $data]);
}

function torgGetProcuratory(){
    $request = $_POST;
    $data = \Realization\Support\RealizationTorgProcuratorySupport::get_procuratory_option_data($request);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function check_torg(){
    echo \Realization\Support\RealizationTorgSupport::check_torg($_POST);
}

function setPositionRealization(){
    $request = $_POST;
    if(isset($request["res"])){
        $res = json_decode($request["res"], true);
        foreach($res as $id => $position){
            \Realization\Model\RealizationDetail::where("id", $id)->update(["position" => $position]);
        }
    }
}