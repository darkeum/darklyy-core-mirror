<?php
    
    /**
        * @name        JMY CORE
        * @link        https://jmy.su/
        * @copyright   Copyright (C) 2012-2019 JMY LTD
        * @license     LICENSE.txt (see attached file)
        * @version     VERSION.txt (see attached file)
        * @author      Komarov Ivan
    */
    
    if (!defined('ADMIN_SWITCH')) {
        header('Location: /');
        exit;
    }
    
    $dir = ROOT.'/usr/modules/task/admin/sub/';  
    includeSub($dir);
    
    global $adminTpl, $config, $core, $url, $admin_conf, $db, $lang;

    // engineering_works();

    switch(isset($url[3]) ? $url[3] : null) {
        default:
        $type = 'me';
        if(isset($url[3])){
            $type = $url[3];
        }
        switch($type){
            default:
                main($type);
            break;
            case "admin":
            require_once(ROOT."/usr/modules/task/admin/rules/rules.php");
            $hideAdmin = hideAdminFunc();
            if(!$hideAdmin){
                main($type);
            } else {
                header('Location: /');
            }
            break;
        }
        break;	
        case "add":
            add();
        break;
       
        case "price_test":
            $price = new Price(["org" => 1]);
            $data = [
                "price" => 117.17,
                "count" => 20,
            ];

            $all = $price->get_all($data);
            dd($all);
            ?>
                <script src="/usr/other/sub/price/js/price.js"></script>
                <script>
                    const price = new Price;
                    console.log(price.get_all({
                        price: <?php echo $data["price"];?>,
                        count: <?php echo $data["count"];?>,
                        discount: <?php echo $data["discount"];?>,
                        nds_includes: 1,
                        nds_proc: 20,
                    }));
                </script>
            <?php
        break;
		case "check_magic":
            $sales_id = [2642];
            $supply_order_id = 4098;
            $query = $db->query("SELECT sd.*, s.nds_includes FROM JMY_realization_detail as sd LEFT JOIN JMY_realization as s ON(s.id = sd.rid) WHERE rid IN(".implode(", ", $sales_id).")");
            $data = [];
            $total = 0;
            $nds = 0;
            $price = new Price;
            
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
				$result = $price->get_all($price_data);
				if($r["nds"] != $result["nds"])
				{
                $data[] = "('".$r["cid"]."', '".$supply_order_id."', '".$r["count"]."', '1', '".$r["s_unit"]."', '".$r["s_name"]."', '".$r["s_sku"]."', '".round($r["total"]/$r['count'], 2)."', '".$r["total"]."', '".$nds_type."', '".$r["nds"]."')";
				}
                $total = $total +    $result["total"];
                $nds = $nds + $result["nds"];
            }
            dd($data);
            dd($total); 
            dd($nds); 
			//$db->query("DELETE FROM JMY_receipts_detail WHERE rid='".$supply_order_id."'");
         //   $db->query("INSERT INTO JMY_receipts_detail (`cid`, `supply_o`, `count`, `status`, `unit`, `s_name`, `s_sku`, `price_now`, `total`, `type_nds`, `nds`) VALUES ".implode(", ", $data));
            //$db->query("UPDATE `".DB_PREFIX."_supply_order` SET total = '".$total."',  nds = '".$nds."' WHERE id = ".$supply_order_id." LIMIT 1");
        break;
		
		case "realization_magic":
            $sales_id = [3331];
            $supply_order_id = 4211;

            $query = $db->query("SELECT sd.*, s.nds_includes FROM JMY_realization_detail as sd LEFT JOIN JMY_realization as s ON(s.id = sd.rid) WHERE rid IN(".implode(", ", $sales_id).")");
            $data = [];
            $total = 0;
            $nds = 0;
            $price = new Price;
            
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
				$result = $price->get_all($price_data);
                $data[] = "('".$r["cid"]."', '".$supply_order_id."', '".$r["count"]."', '1', '".$r["s_unit"]."', '".$r["s_name"]."', '".$r["s_sku"]."', '".round($r["total"]/$r['count'], 2)."', '".$r["total"]."', '".$nds_type."', '".$r["nds"]."')";

                $total = $total +    $r["total"];
                $nds = $nds + $r["nds"];
            }
            dd($data);
            dd($total); 
            dd($nds); 
             $db->query("INSERT INTO JMY_supply_order_detail (`cid`, `supply_o`, `count`, `status`, `unit`, `s_name`, `s_sku`, `price_now`, `total`, `type_nds`, `nds`) VALUES ".implode(", ", $data));
             $db->query("UPDATE `".DB_PREFIX."_supply_order` SET total = '".$total."',  nds = '".$nds."' WHERE id = ".$supply_order_id." LIMIT 1");
        break;
        case "sales_magic":
            $sales_id = [9267, 9271, 9261];
            $supply_order_id = 0;

            $query = $db->query("SELECT sd.*, s.nds_includes FROM JMY_sales_detail as sd LEFT JOIN JMY_sales as s ON(s.id = sd.sales_id) WHERE sales_id IN(".implode(", ", $sales_id).")");
            $data = [];
            $total = 0;
            $nds = 0;
            $price = new Price;
            
            while($r = $db->getRow($query)){
                $nds_includes = 1; //$r["nds_includes"]
                $nds_type = 3; ////$r["type_nds"]
                $price_data = [
					"nds_includes" => $nds_includes,
					"price" => $r["s_price"],
					"count" => $r["count"],
					"nds_type" => $nds_type, 
				];
				$result = $price->get_all($price_data);
                $data[] = "('".$r["cid"]."', '".$supply_order_id."', '".$r["count"]."', '1', '".$r["s_unit"]."', '".$r["s_name"]."', '".$r["s_sku"]."', '".$result["price"]."', '".$result["total"]."', '".$nds_type."', '".$result["nds"]."')";

                $total = $result["total"];
                $nds = $result["nds"];
            }
            dd($data);
            dd($total); 
            dd($nds); 
            // $db->query("INSERT INTO JMY_supply_order_detail (`cid`, `supply_o`, `count`, `status`, `unit`, `s_name`, `s_sku`, `price_now`, `total`, `type_nds`, `nds`) VALUES ".implode(", ", $data));
            // $db->query("UPDATE `".DB_PREFIX."_supply_order` SET total = '".$total."',  nds = '".$nds."' WHERE id = ".$supply_order_id." LIMIT 1");
        break;
		 case "script_null_st":
		 	$qq = $db->query("SELECT *  FROM `JMY_catalog` WHERE `quantity` != 0 AND (category = ',0,' OR category IS NULL OR category = '') ");
			$storage_id = 1664;
	 	$source['module'] = 'storage';
		 	$source['pid'] = '0';
			$source['comment'] = 'Обнуление остатков';
			if($db->numRows($qq) > 0)
		 	{
				while($r = $db->getRow($qq))
			{
				//echo $r['id'];				
                $result[] = "('".$r["id"]."', '".$storage_id."', '".$r["quantity"]."')";
				$result_str = implode(", ", $result);
				
				
			}
			$db->query("INSERT INTO JMY_storage_detail (`cid`, `storage_id`, `count_now`) VALUES ".$result_str);				
			echo "INSERT INTO JMY_storage_detail (`cid`, `storage_id`, `count_now`) VALUES ".$result_str;
            }
		// break;
        // case "script":
        //     $result = [];
        //     $storage_id = 1513;

        //     //реализации
        //     $list = [3140,3139,3138,3133,3127,3129,3128,3126,3124,3130,3135,3136,3125,3139,3140,3141];
        //     $list_str = implode(", ", $list);
        //     $query = $db->query("SELECT * FROM JMY_realization_detail WHERE rid IN (".$list_str.")");
        //     if($db->numRows($query) > 0){
        //         while($r = $db->getRow($query)){
        //             $result[] = "('".$r["cid"]."', '".$storage_id."', '1')";
        //         }
        //     }

        //     // перемещения
        //     $list = [1906,1907,1903,1896,1895,1894,1892,1898,1901,1900,1899,1897,1922,1921,1917,1920,1913,1914,1916,1915,1926,1931,1925,1911,1912,1891,1910,1924,1928,1927,1930,1932,1909,1908,1933,1936,1937,1935];
        //     $list_str = implode(", ", $list);
        //     $query = $db->query("SELECT * FROM JMY_stock_transfer_detail WHERE transfer_id IN (".$list_str.")");
        //     if($db->numRows($query) > 0){
        //         while($r = $db->getRow($query)){
        //             $result[] = "('".$r["cid"]."', '".$storage_id."', '1')";
        //         }
        //     }

        //     // поступления
        //     $list = [3631,3506,3530,3486,3488,3535,3488,3496,3471,3520,3456,3531,3381,3173,3514,3543,3110];
        //     $list_str = implode(", ", $list);
        //     $query = $db->query("SELECT * FROM JMY_supply_order_detail WHERE supply_o IN (".$list_str.")");
        //     if($db->numRows($query) > 0){
        //         while($r = $db->getRow($query)){
        //             $result[] = "('".$r["cid"]."', '".$storage_id."', '1')";
        //         }
        //     }

        //     $result_str = implode(", ", $result);
        //     $db->query("INSERT INTO JMY_storage_detail (`cid`, `storage_id`, `count_now`) VALUES ".$result_str);
        // break;
        // case "script":
        //     $query = $db->query("SELECT * FROM JMY_reestr WHERE doc IS NOT NULL");
        //     while($r = $db->getRow($query)){
        //         if(!empty($r["doc"])){
        //             $doc = json_decode($r["doc"], true);
        //             foreach($doc as $val){
        //                 if(isset($val["type"]) && !empty($val["type"])){
        //                     $type = $val["type"];
        //                     $comment = isset($val["comment"]) ? "'".$val["comment"]."'" : 'null';
        //                     $doc_id = 'null';
        //                     $scan = 'null';
        //                     if(isset($val["file"])){
        //                         if(!empty($val["file"]["doc"])){
        //                             $doc_id = "'".$val["file"]["doc"]["id"]."'";
        //                         }
        //                         if(!empty($val["file"]["scan"])){
        //                             $scan = "'".$val["file"]["scan"]["id"]."'";
        //                         }
        //                     }
                            
        //                     $title = isset($val["title"]) ? "'".$val["title"]."'" : 'null';
        //                     $db->query("INSERT INTO JMY_reestr_doc (`reestr_id`, `type`, `comment`, `title`, `doc`, `scan`) VALUES ('".$r["id"]."', '".$type."', ".$comment.", ".$title.", ".$doc_id.", ".$scan.")");
        //                 }   
        //             }
        //         }
        //     }
        // break;
		/*
		case "script":
            $query = $db->query("SELECT * FROM JMY_realization WHERE org = '2' AND active='1' AND date >= '2019-07-01 00:00:00'");
			$i = 0;
            while($r = $db->getRow($query))
			{
				$i++;
                echo $i. ' ' .$r["id"].' -> '.$r["id_1c"].'<br>';
				$date = date('Y-m-d H:i:s'); 
				//$db->query("INSERT INTO `" . DB_PREFIX . "_sync_relation` ( `id` , `id_1c` , `module` , `pid` ,  `sync_base` , `date` , `status`) VALUES (NULL, '" . $r["id_1c"] . "', 'catalog_v2', '" . $r["pid"] . "', '3', '" . $date . "', '1');");
				$db->query("INSERT INTO `" . DB_PREFIX . "_sync_register` ( `id` , `uid` , `module` , `pid` ,  `type` , `date_add` , `sync_base`, `status`) VALUES (NULL, '1', 'realization', '" . $r["id"] . "', '1', '" . $date . "', '3',  '1');");
            }
        break;
		*/
/*
        case "script":
            $query = $db->query("SELECT * FROM JMY_sync_relation WHERE module = 'catalog_v2' AND sync_base = '1'");
			$i = 0;
            while($r = $db->getRow($query))
			{
				$i++;
                echo $i. ' ' .$r["pid"].' -> '.$r["id_1c"].'<br>';
				$date = date('Y-m-d H:i:s'); 
				//$db->query("INSERT INTO `" . DB_PREFIX . "_sync_relation` ( `id` , `id_1c` , `module` , `pid` ,  `sync_base` , `date` , `status`) VALUES (NULL, '" . $r["id_1c"] . "', 'catalog_v2', '" . $r["pid"] . "', '3', '" . $date . "', '1');");
				$db->query("INSERT INTO `" . DB_PREFIX . "_sync_register` ( `id` , `uid` , `module` , `pid` ,  `type` , `date_add` , `sync_base`, `status`) VALUES (NULL, '1', 'catalog_v2', '" . $r["pid"] . "', '1', '" . $date . "', '3',  '1');");
            }
        break;*/


/*
        case "script":
            $query = $db->query("SELECT sd.*, sud.id as sudid FROM JMY_supply_detail as sud LEFT JOIN JMY_sales_detail as sd ON(sd.id = sud.sdid) LEFT JOIN JMY_supply as s ON(s.id = sud.sid) WHERE s.type = 1");
            while($r = $db->getRow($query)){
                echo $r["sudid"].' -> '.$r["id"].'<br>';
                $db->query("UPDATE JMY_sales_detail SET supply_detail_id = '".$r["sudid"]."' WHERE id = '".$r["id"]."' LIMIT 1");
            }
        break;
*/
/*
            case "script":
                $query = $db->query("SELECT * FROM JMY_relations WHERE `module_out` = 'supply_date'");
                while($r = $db->getRow($query)){
                    $qq = $db->query("SELECT * FROM JMY_supply WHERE id = '".$r["pid_out"]."'");
                    while($d = $db->getRow($qq)){
                        if($d["type"] == 1){
                            echo $r['pid_out']."<br>";
                            $db->query("UPDATE JMY_relations SET module_out = 'supply' WHERE id = '".$r['id']."' LIMIT 1");
                        }
                    }
                }
            break;
*/
/*
        case "script":
            $query = $db->query("SELECT * FROM JMY_users WHERE `group` = '1'");
            $str = [];
            while($r = $db->getRow($query)){
                $org = 1;
                $uid = $r["id"];
                $str[] = "(NULL, '".$org."', '".$uid."', '".$org.$uid."', '".date("Y-m-d H:i:s")."')";
            }
            $db->query("INSERT INTO JMY_users_org_relation VALUES ".implode(", ", $str)."");
        break;
*/
/*
        case "script":
        $query = $db->query("SELECT rd.*, c.unit as c_unit FROM  JMY_stock_transfer_detail as rd LEFT JOIN JMY_catalog as c ON(c.id = rd.cid) WHERE rd.s_unit IS NULL OR rd.s_unit NOT REGEXP '^[0-9]+$' OR rd.s_unit LIKE '%null%'");
        echo $db->numRows($query)."<br>";
        while($r = $db->getRow($query)){
            echo $r["id"]." -> ".$r["c_unit"]."<br>";
            $unit = $r["c_unit"];

            $db->query("UPDATE JMY_stock_transfer_detail SET s_unit = '".$unit."' WHERE id = '".$r["id"]."' LIMIT 1");
        }
        break;  
*/
/*
        case "script":
            $query = $db->query("SELECT * FROM JMY_request WHERE type = '3' AND status = '3'");
            while($r = $db->getRow($query)){
                $spec = json_decode($r["specification"], true);
                if(array_key_exists("sku", $spec) && !empty($spec["sku"])){
                    $sku = $spec["sku"];
                    $qq = $db->query("SELECT id FROM JMY_catalog WHERE sku LIKE '".$sku."' LIMIT 1");
                    $catalog = $db->getRow($qq);
                    $db->query("UPDATE JMY_request SET final_cid = '".$catalog["id"]."' WHERE id = '".$r["id"]."' LIMIT 1");
                }
            }
        break;
*/
/*
      
*/
/* 
        case "pro":
        $source['module'] = 'transform';
	    $source['pid'] = '';
	    $source['comment'] = 'Преобразование номенклатуры в cid => 43983';
            updateQuantity('19056', 1, 1100, 'minus', $source);
            updateQuantity('23745', 1, 1100, 'minus', $source);
            //updateQuantity('43983', 1, 506, 'plus', $source);
        break;
        */
/*
        case "script":
            $aa = 0;
            $query = $db->query("SELECT * FROM JMY_supply_order_detail WHERE s_name IS NULL OR s_sku IS NULL");
            while($r = $db->getRow($query)){
                if(empty($r["supply_d"])){
                    $qq = $db->query("SELECT * FROM JMY_catalog WHERE id = '".$r["cid"]."' LIMIT 1");
                    $p = $db->getRow($qq);
                    $s_name = $p["title"];
                    $s_sku = $p["sku"];
                } else {
                    $qq = $db->query("SELECT * FROM JMY_supply_detail WHERE id = '".$r["supply_d"]."' LIMIT 1");
                    $p = $db->getRow($qq);
                    $s_name = $p["s_name"];
                    $s_sku = $p["s_sku"];
                }
                $s_name = htmlspecialchars($s_name, ENT_QUOTES);
                echo $r["id"].": ".$s_name." --- ".$s_sku."<br>";
                $db->query("UPDATE JMY_supply_order_detail SET s_name = '".$s_name."', s_sku = '".$s_sku."' WHERE id = '".$r["id"]."' LIMIT 1");
            }
        break;
        */

        /*
        case "catalog_change":
            $query = $db->query("SELECT * FROM JMY_catalog WHERE title LIKE '%\"%' OR title LIKE \"%'%\"");
            while($r = $db->getRow($query)){
                $title = stripcslashes($r["title"]);
                $title = htmlspecialchars($title, ENT_QUOTES);
                echo $r["id"]." -> ".$title."<br>";
                $db->query("UPDATE JMY_catalog SET title = '".$title."' WHERE id = '".$r["id"]."' LIMIT 1");
            }
        break;
        */
		
		case "script":		
			$attr['uid']	= '1';
			$attr['title']	= 'Проверка';
			$attr['text']	= 'Проверка';
			$attr['icon']	= '';
			$attr['url']	= 'Проверка';
			sendPush($attr);
			echo 'ok';
        break;  

        case "save":
            task_save();
        break;

        case "view":
        if(isset($url[4])){
            $id = intval($url[4]);
            
            $query = $db->query("SELECT * FROM ".DB_PREFIX."_task WHERE id = '" . $id . "' LIMIT 1");
            $task = $db->getRow($query);
            $uid = $core->auth->user_info['id'];
            require_once(ROOT."/usr/modules/task/admin/rules/rules.php");
            $hideAdmin = hideAdminFunc("array");
            $checkArr = [$task["toid"], $task["fromid"]];
            $checkArr = array_merge($hideAdmin, $checkArr);
            if(!empty($task["coperformerid"])){
                $arr = json_decode($task['coperformerid'], true);
                foreach($arr as $key => $val){
                    $checkArr[] = $val;
                }
            }
            if(!empty($task["observerid"])){
                $arr = json_decode($task['observerid'], true);
                foreach($arr as $key => $val){
                    $checkArr[] = $val;
                }
            }
            if(in_array($uid, $checkArr)){
                view2($id);
            } else {
                view2($id, "true");
            }
        } else {
            view2($id, "true");
        }
        break;
    }
