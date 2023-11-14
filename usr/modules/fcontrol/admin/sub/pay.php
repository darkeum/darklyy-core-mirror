<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function content_pay() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = $lang['fcontrol_pay'];
	$adminTpl->admin_head($lang['fcontrol'].' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'fcontrol';
	$atributs['module_open'] = 'pay_detail';
	$atributs['button'] = isset($_COOKIE["plan_pay_date_all"]) ? 'Нормальный режим' : 'Режим все оплаты';
	$atributs['button_onclick'] = 'plan_pay_date_toggle();';
	$atributs['button_url'] = '/administration/module/fcontrol/pay';	

	loadGrid('jqFcontrolPay', $lnn, $atributs, ROOT.'/usr/modules/fcontrol/admin/grid/');
	
	$atributs['js'] = 'setTimeout(function () {$("#jqFcontrolPay").jqGrid(\'setGridParam\',{postData:{pay: \'1\'}});  $(\'#jqFcontrolPay\').trigger(\'reloadGrid\');}, 150);';	
	?>

		<script>
			function plan_pay_date_toggle(){
				const name = 'plan_pay_date_all';
				const c = Cookies.get(name);
				if(c === undefined){
					Cookies.set(name, 'true');
				} else {
					Cookies.remove(name);
				}
				location.href = '/administration/module/fcontrol/pay';
			}
		</script>

	<?php
	$adminTpl->admin_foot();
} 

function savePayFcotrol($id, $request){
	global $db, $core;

	$query = $db->query("SELECT supply_o FROM `".DB_PREFIX."_control` WHERE id = ".$id);
	$r = $db->getRow($query);
	$s_data = \Supply\Model\SupplyOrder::find($r["supply_o"])->toArray();

	$update = [];
	$update["pay_num"] = $request["pay_num"];
    $pay_date = $request["pay_date"];
	$pay_date = str_replace("T", " ", $pay_date);
	$update["pay_date"] = $pay_date;
    $file = isset($request['file']) ? $request['file'] : false;
	$update["pay_uid"] = $core->auth->user_info['id'];
	$pay_total = empty($request["pay_total"]) ? false : $request["pay_total"];
	$update["pay"] = 1;
	$control = \Control\Model\Control::findOrFail($id);
	if($pay_total){
		$update["pay"] = 3;
		$pay_total_all = $control->pay_total + $pay_total;
		$update["pay_total"] = $pay_total_all;
		if($pay_total_all >= $s_data["total"]){
			$update["pay"] = 1;
		}
	}
	
	$control->update($update);
	$db->query("UPDATE `".DB_PREFIX."_supply_order` SET pay = 1 WHERE id = ".$r["supply_o"]." LIMIT 1");
	
	$query = $db->query("SELECT * FROM `".DB_PREFIX."_control_detail` WHERE control_id = '".$id."' AND sdid != '0' AND sdid IS NOT NULL");
	if($db->numRows($query) > 0){
		$s_status = 20;
		
		while($mm = $db->getRow($query)){
			\Sales\Model\SalesDetail::find($mm["sdid"])->update(["status" => $s_status]);
				
            \Sales\Model\SalesDetailStatusLog::create([
                "sales_detail_id" => $mm["sdid"], 
                "status" => $s_status, 
                "url" => "/administration/module/fcontrol/pay_detail/".$id,
            ]);
		}
	}
    
    $attribute = [];
	$attribute["name"] = "";
	$attribute["file"] = $file;
	$attribute["module"] = 'fcontrol';
	$attribute["mod"] = 'pay';
	$attribute["id"] = $id;
	$attribute["comm_id"] = $id;
	uploaderMaster("download", $attribute);

	$attr = [];
	$attr['uid'] = $s_data["uid"];
	$attr['title'] = 'Счёт поставщика оплачен';
	$attr['text'] = 'Счёт поставщика оплачен';
	$attr['icon'] = config('app.url').'/files/push/new_order.png';
	$attr['url'] = config('app.url').'/administration/module/supply/order_detail/'.$r["supply_o"];
	$attr['pNotif'] = true;
	$attr['pPush'] = true;
	$attr['module'] = "supply_order";
    $attr['pid'] = $r["supply_o"];
	sendAllNotif($attr);
}