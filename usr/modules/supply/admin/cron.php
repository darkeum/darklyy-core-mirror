<?php
$iteration = getCronIteration();
if($iteration % 720 == 0)
{
	echo 'ok';
}
if($iteration == 840){
	$result = find_supply_order_no_control();
	if(count($result) > 0){
		foreach($result as $val){
			notif_supply_order_no_control($val);
		}
	}
}
// if($iteration == 842){
// 	notif_change_date_logistic();
// }

function find_supply_order_no_control(){
	global $db;
	$result = [];
	$date = date("Y-m-d", strtotime("-1 days"));
	$query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_order` WHERE date_create = '".$date."' AND control = '0'");
	if($db->numRows($query) > 0){
		while($r = $db->getRow($query)){
			$result[] = [
				"id" => $r["id"],
				"numb" => $r["numb"],
			];
		}
	}
	return $result;
}

function notif_supply_order_no_control($data){
	$attr = [];
	$attr['uid'] = getGroup(["group" => "supply"]);
	$attr['title'] = 'Счёт №'.$data["numb"].' не был отправлен на согласование!';
	$attr['text'] = 'Счёт №'.$data["numb"].' не был отправлен на согласование!';
	$attr['icon'] = config('app.url').'/files/push/comment.png';
	$attr['url'] = config('app.url').'/administration/module/supply/order_detail/'.$data["id"];
	$attr['pNotif'] = true;
	$attr['pPush'] = true;
	$attr['module'] = "supply_order";
    $attr['pid'] = $data["id"];
	sendAllNotif($attr);
}

function notif_change_date_logistic(){
	global $db;
	$query = $db->query("SELECT * FROM `".DB_PREFIX."_supply_order` WHERE `control_send` IS NOT NULL AND `control` IN(1, 2) AND  `control_send` <= '".date("Y-m-d", strtotime("-2 days"))."'");
	if($db->numRows($query) > 0){
		$uids = getGroup(["group" => "logistic"]);
		while($r = $db->getRow($query)){
			$attr = [];
			$attr['uid'] = $uids;
			$text = 'Счёт №'.$r["numb"].' не был согласован, поменяйте дату';
			$attr['title'] = $text;
			$attr['text'] = $text;
			$attr['icon'] = config('app.url').'/files/push/comment.png';
			$attr['url'] = config('app.url').'/administration/module/supply/logistics_detail/'.$r["id"];
			$attr['pNotif'] = true;
			$attr['pPush'] = true;
			$attr['module'] = "supply_order";
    		$attr['pid'] = $r["id"];
			sendAllNotif($attr);
		}
	}
}
