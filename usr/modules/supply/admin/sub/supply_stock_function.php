<?php

//require_once("usr/modules/supply/admin/sub/supply_stock_function.php");

function add_supply_stock_detail($cid, $count){
    global $db;
    //поиск заявки в которую добавить
    $id = check_supply_stock();
    if(!$id){
        $id = create_supply_stock();
    }
    //поиск подобной номенклатуры
    $sid = check_supply_stock_detail($id, $cid);
    if($sid){
        //если есть -> то добавить
        append_supply_stock_detail($sid, $count);
    } else {
        //если нет -> то создать
        create_supply_stock_detail($id, $cid, $count);
    }
}

function check_supply_stock(){
    global $db;
    $query = $db->query("SELECT id FROM ".DB_PREFIX."_supply_stock WHERE status = '1' ORDER BY id DESC LIMIT 1");
    if($db->numRows($query) > 0){
        $ss = $db->getRow($query);
        $id = $ss["id"];
        return $id;
    } else {
        return false;
    }
}

function create_supply_stock(){
    global $db;
    $date = date("Y-m-d");
    $db->query("INSERT INTO ".DB_PREFIX."_supply_stock (date, status) VALUES ('".$date."', 1)");
    $id = $db->insert_id();
    return $id;
}

function create_supply_stock_detail($ssid, $cid, $count){
    global $db;
    $date = date("Y-m-d");
    $db->query("INSERT INTO ".DB_PREFIX."_supply_stock_detail (`ssid`, `cid`, `count`, `date`, `date_edit`, `status`) VALUES ('".$ssid."', '".$cid."', '".$count."', '".$date."', '".$date."', '1')");
    $iid = $db->insert_id();
    return $iid;
}

function append_supply_stock_detail($id, $count){
    global $db;
    $query = $db->query("SELECT count FROM ".DB_PREFIX."_supply_stock_detail WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);
    $new_count = $r["count"] + $count;
    $date = date("Y-m-d");
    $db->query("UPDATE ".DB_PREFIX."_supply_stock_detail SET count = '".$new_count."', date_edit = '".$date."' WHERE id = '".$id."' LIMIT 1");
    $iid = $db->insert_id();
    return $iid;
}

function check_supply_stock_detail($ssid, $cid){
    global $db;
    $query = $db->query("SELECT id FROM ".DB_PREFIX."_supply_stock_detail WHERE cid = '".$cid."' AND ssid = '".$ssid."' LIMIT 1");
    if($db->numRows($query) > 0){
        $ssd = $db->getRow($query);
        $id = $ssd["id"];
        return $id;
    } else {
        return false;
    }
}

function ajax_order_send_stock()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id'], $_POST['count']))
	{
		$id = intval($_POST['id']);
		$count = $_POST['count'];
		$comment = isset($_POST['comment']) ? $_POST['comment'] : "";
		$order_numb = isset($_POST['order_numb']) ? intval($_POST['order_numb']) : 0;	
		$new_order = isset($_POST['new_order']) ? intval($_POST['new_order']) : 0;
		orderSendStock($id, $count, $comment, $order_numb, $new_order);
	}
}

function ajax_order_send_mas_stock()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if(isset($_POST['id']))
	{
		$id = $_POST['id'];
		$comment = isset($_POST['comment']) ? $_POST['comment'] : "";
		$order_numb = isset($_POST['order_numb']) ? intval($_POST['order_numb']) : 0;	
		$new_order = isset($_POST['new_order']) ? intval($_POST['new_order']) : 0;
		
		if(is_array($id) && count($id) > 0){
			$id_str = implode(", ", $id);
			$query = $db->query("SELECT id, count, count_send FROM ".DB_PREFIX."_supply_stock_detail WHERE id IN (".$id_str.")");
			if($db->numRows($query) > 0){
				while($r = $db->getRow($query)){
					$l_id = $r["id"];
                    $count = $r["count"] - $r["count_send"];
                    if($count > 0){
                        $order_numb = orderSendStock($l_id, $count, $comment, $order_numb, $new_order);
                    }
				}
			}
		}
	}
}

function orderSendStock($id, $count, $comment, $order_numb = 0, $new_order = 0){
	global $adminTpl, $core, $db, $admin_conf, $lang;
	$query_s = $db->query("SELECT ssd.*, c.unit, c.title, c.sku FROM ".DB_PREFIX."_supply_stock_detail as ssd LEFT JOIN ".DB_PREFIX."_catalog as c ON(c.id = ssd.cid) WHERE ssd.id = '".$id."' LIMIT 1");
		if($db->numRows($query_s) > 0)
		{
			$sd = $db->getRow($query_s);
			if ($sd['count'] >= $sd['count_send'] + $count)
			{
				$unit = empty($sd["unit"]) ? 'null' : "'".$sd["unit"]."'";
                $uid = $core->auth->user_info['id'];
                $org = 1;
                
				if (($order_numb == '0' || empty($order_numb)) && $new_order != '0')
				{
                    $date_create = \Carbon\Carbon::now();
					$numb_array = \Boot\Support\Module::numb('supply_order', $org, $date_create);
					$numb = $numb_array['numb'];
					$numb_counter = $numb_array['numb_counter'];
					$db->query("INSERT INTO `" . DB_PREFIX . "_supply_order` (`numb_counter`, `numb`, `org`, `oid`, `uid`, `status`, `pay`, `nds_includes`, `date_create`) VALUES ('" . $numb_counter . "', '" . $numb . "', '" . $org . "', '" . $new_order . "', '" . $uid . "', '1', '0', '1', '{$date_create}');");
					$order_numb = $db->insert_id();
                }
                
                $s_name = $sd["title"];
                $s_sku = $sd["sku"];

				if($order_numb != '0')
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_supply_order_detail` ( `supply_o`, `count`, `status`, `cid`, `unit`, `comment`, `supply_s`, `s_name`, `s_sku`) VALUES ('" . $db->safesql($order_numb) . "', '" . $db->safesql($count) . "', '1', '".$sd["cid"]."', ".$unit.", '" . $db->safesql($comment) . "', '".$sd["id"]."', '".$s_name."', '".$s_sku."')");

					$count_send = $sd['count_send'] + $count;
					$db->query("UPDATE " . DB_PREFIX . "_supply_stock_detail SET `status` = '2', count_send = '".$count_send."' WHERE `id` = '" . $id . "' LIMIT 1 ;");
                    
                    /*
                    $query_r = $db->query("SELECT status FROM ".DB_PREFIX."_relations WHERE pid_in = '".$sd['sid']."' AND module_in = 'supply' AND module_out = 'supply_order' AND pid_out = '".$order_numb."' LIMIT 1");					
					if($db->numRows($query_r) <= 0)
					{
						relationsSave('supply', $sd['sid'], 'supply_order', $order_numb);
                    }
                    */
					echo 'Информация успешно сохранена!';	
					return $order_numb;
				}
				else
				{
					echo 'Ошибка!!!';	
				}
			}
		}
}