<?php

// global $adminTpl, $db, $core, $lang;
		// $adminTpl->admin_head($lang['organizations'] . ' | ' . $lang['org_save']);
		// //получение данных
		// $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		// $inn = isset($_POST['inn']) ? filter($_POST['inn']) : '';
		// $yaddress = isset($_POST['yaddress']) ? filter($_POST['yaddress']) : '';
		// $maddress = isset($_POST['maddress']) ? filter($_POST['maddress']) : '';
		// $name_full = isset($_POST['name']) ? filter($_POST['name']) : '';
		// $name = isset($_POST['sname']) ? filter($_POST['sname']) : '';
		// $kpp = isset($_POST['kpp']) ? filter($_POST['kpp']) : '';
		// $ogrn = isset($_POST['ogrn']) ? filter($_POST['ogrn']) : '';
		// $post = isset($_POST['post']) ? filter($_POST['post']) : '';
		// $director = isset($_POST['director']) ? filter($_POST['director']) : '';
		// $reestr_id = isset($_POST['radio_reestr']) ? (empty($_POST['radio_reestr']) ? 'null' : filter($_POST['radio_reestr'])) : 'null';

		// $saveBank = isset($_POST["saveBank"]) ? $_POST["saveBank"] : '';

		// $phone = isset($_POST['phone']) ? filter($_POST['phone']) : '';
		// $email = isset($_POST['email']) ? filter($_POST['email']) : '';
		// $dey = isset($_POST['dey']) ? filter($_POST['dey']) : '';
		// $usersCF = isset($_POST['usersCF']) ? $_POST['usersCF'] : [];

		// \Organizations\OrganizationsTemplate::check_auth_group();

		// echo '<div id="content" class="animated fadeIn">';

		// if (isset($_POST['edit']) && ($id != 0)) {
		// 	//если редактирование
		// 	$db->query("UPDATE `" . DB_PREFIX . "_organizations` SET 				
		// 			`name` = '" .  $db->safesql($name) . "', 
		// 			`name_full` = '" .  $db->safesql($name_full) . "', 
		// 			`yaddress` = '" .  $db->safesql($yaddress) . "', 
		// 			`maddress` = '" .  $db->safesql($maddress) . "', 
		// 			`inn` = '" .  $db->safesql($inn) . "', 
		// 			`kpp` = '" .  $db->safesql($kpp) . "', 
		// 			`ogrn` = '" .  $db->safesql($ogrn) . "', 
		// 			`post` = '" .  $db->safesql($post) . "', 
		// 			`director` = '" .  $db->safesql($director) . "', 
		// 			`dey` = '" .  $db->safesql($dey) . "', 
		// 			`phone` = '" .  $db->safesql($phone) . "', 
		// 			`email` = '" .  $db->safesql($email) . "',					
		// 			`reestr_id` = " .  $db->safesql($reestr_id) . "					
		// 		WHERE `id` = " . $id . " LIMIT 1 ;");
		// 	$adminTpl->info($lang['org_edit_success'], 'info', null, $lang['info'], $lang['org_list'], ADMIN . '/module/organizations');

		// 	$saveBankArray = [];
		// 	foreach($saveBank as $key => $val){
		// 		$json = json_decode($val, true);
		// 		if (!empty($json["id"])) {
		// 			$query_bank = $db->query("SELECT * FROM " . DB_PREFIX . "_organizations_bank WHERE id = '" . $json["id"] . "' LIMIT 1");
		// 			$bankR = $db->getRow($query_bank);

		// 			$db->query("UPDATE `" . DB_PREFIX . "_organizations_bank` SET 				
		// 					`bik` = '" .  $db->safesql($json["bik"]) . "', 
		// 					`rschet` = '" .  $db->safesql($json["rschet"]) . "', 
		// 					`name_short` = '" .  $db->safesql($json["name_short"]) . "', 
		// 					`name` = '" .  $db->safesql($json["name"]) . "', 
		// 					`address` = '" .  $db->safesql($json["address"]) . "', 
		// 					`kschet` = '" .  $db->safesql($json["kschet"]) . "'				
		// 				WHERE `id` = " . $bankR['id'] . " LIMIT 1 ;");

		// 			$saveBankArray[] = $json["id"];
		// 		} else {
		// 			$db->query("INSERT INTO `" . DB_PREFIX . "_organizations_bank` ( `oid` , `bik` , `rschet` , `name_short` , `name` , `address` , `kschet`, `status`) VALUES ('" . $db->safesql($id) . "', '" . $db->safesql($json["bik"]) . "',  '" . $db->safesql($json["rschet"]) . "', '" . $db->safesql($json["name_short"]) . "', '" . $db->safesql($json["name"]) . "', '" . $db->safesql($json["address"]) . "', '" . $db->safesql($json["kschet"]) . "', '1');");
		// 			$saveBankArray[] = $db->insert_id();
		// 		}
		// 	}

		// 	$arrayBankDel = [];
		// 	$ttt = $db->query("SELECT id FROM " . DB_PREFIX . "_organizations_bank WHERE oid = '".$id."'");
		// 	while($tt = $db->getRow($ttt)){
		// 		if(!in_array($tt["id"], $saveBankArray)){
		// 			$arrayBankDel[] = $tt["id"];
		// 		}
		// 	}

		// 	if(count($arrayBankDel) > 0){
		// 		$str = implode(', ', $arrayBankDel);
		// 		if(mb_strlen($str) > 0){
		// 			$db->query("DELETE FROM " . DB_PREFIX . "_organizations_bank WHERE id IN(".$str.")");
		// 		}
		// 	}


		// 	$qUsersOrg = $db->query("SELECT id, uid FROM ".DB_PREFIX."_users_org WHERE oid = '".$id."'");
		// 	$arryUsers = [];
		// 	while($quo = $db->getRow($qUsersOrg)){
		// 		$arryUsers[$quo["uid"]] = $quo["id"];
		// 	}

		// 	foreach($usersCF as $key => $val){
		// 		$json = json_decode($val, true);
		// 		if(empty($json["data"]["id"])){

		// 			$db->query("INSERT INTO ".DB_PREFIX."_users (`nick`, `password`, `tail`, `email`, `phone`, `surname`, `name`, `ochestvo`, `group`, `regdate`, `active`, `hidden`, `phone2`) VALUES ('".$json["data"]["emailCF"]."', '".md5(time())."', '".gencode(rand(6, 11))."', '".$json["data"]["emailCF"]."', '".$json["data"]["phoneCF"]."', '".$json["data"]["surnameCF"]."', '".$json["data"]["nameCF"]."', '".$json["data"]["ochestvoCF"]."', '2', '".time()."', '1', '0', '".$json["data"]["phone2CF"]."')");
		// 			$uid = $db->insert_id();

		// 		} else {	

		// 			$db->query("UPDATE ".DB_PREFIX."_users SET `email` = '".$json["data"]["emailCF"]."', `phone` = '".$json["data"]["phoneCF"]."', `surname` = '".$json["data"]["surnameCF"]."', `name` = '".$json["data"]["nameCF"]."', `ochestvo` = '".$json["data"]["ochestvoCF"]."', `phone2` = '".$json["data"]["phone2CF"]."' WHERE id = '".$json["data"]["id"]."' LIMIT 1");
		// 			$uid = $json["data"]["id"];

		// 			unset($arryUsers[$uid]);
					
		// 		}

		// 		$zz = $db->query("SELECT id FROM ".DB_PREFIX."_users_org WHERE uid = '".$uid."' AND oid = '".$id."' LIMIT 1");
		// 		if($db->numRows($zz) > 0){

		// 			$z = $db->getRow($zz);
		// 			$db->query("UPDATE ".DB_PREFIX."_users_org SET `uid` = '".$uid."', `oid` = '".$id."', `post` = '".$json["data"]["postCF"]."', comment = '".$json["data"]["commentCF"]."', `type` = '".$json["data"]["typeCF"]."' WHERE id = '".$z["id"]."'");

		// 		} else {

		// 			$db->query("INSERT INTO ".DB_PREFIX."_users_org (`uid`, `oid`, `post`, `choose`, `status`, `comment`, `type`) VALUES ('".$uid."', '".$id."', '".$json["data"]["postCF"]."', '0', '1', '".$json["data"]["commentCF"]."', '".$json["data"]["typeCF"]."')");

		// 		}
		// 	}

		// 	foreach($arryUsers as $k => $v){
		// 		$db->query("DELETE FROM JMY_users_org WHERE id = '".$v."' LIMIT 1");
		// 	}

		// } else {
		// 	//если новое
		// 	$dadata = new Dadata($apiKey, $secretKey);
		// 	$result = $dadata->clean('name', $director);
		// 	$db->query("INSERT INTO `" . DB_PREFIX . "_organizations` ( `id` , `name` , `name_full` , `yaddress` , `maddress` , `inn` , `kpp` , `ogrn` , `post` , `director` , `director_detail` , `dey` , `phone` , `email`, `status`) VALUES (NULL, '" . $db->safesql($name) . "', '" . $db->safesql($name_full) . "',  '" . $db->safesql($yaddress) . "', '" . $db->safesql($maddress) . "', '" . $db->safesql($inn) . "', '" . $db->safesql($kpp) . "', '" . $db->safesql($ogrn) . "', '" . $db->safesql($post) . "', '" . $db->safesql($director) . "',  '" . $db->safesql(json_encode($result, JSON_UNESCAPED_UNICODE)) . "', '" . $db->safesql($dey) . "', '" . $db->safesql($phone) . "', '" . $db->safesql($email) . "', '1');");
		// 	$id = $db->insert_id();
		// 	foreach($saveBank as $key => $val){
		// 		$json = json_decode($val, true);
		// 		$db->query("INSERT INTO `" . DB_PREFIX . "_organizations_bank` ( `oid` , `bik` , `rschet` , `name_short` , `name` , `address` , `kschet`, `status`) VALUES ('" . $db->safesql($id) . "', '" . $db->safesql($json["bik"]) . "',  '" . $db->safesql($json["rschet"]) . "', '" . $db->safesql($json["name_short"]) . "', '" . $db->safesql($json["name"]) . "', '" . $db->safesql($json["address"]) . "', '" . $db->safesql($json["kschet"]) . "', '1');");
		// 	}


		// 	foreach($usersCF as $key => $val){
		// 		$json = json_decode($val, true);
		// 		if(empty($json["data"]["id"])){
		// 			$db->query("INSERT INTO ".DB_PREFIX."_users (`nick`, `password`, `tail`, `email`, `phone`, `surname`, `name`, `ochestvo`, `group`, `regdate`, `active`, `hidden`, `phone2`) VALUES ('".$json["data"]["emailCF"]."', '".md5(time())."', '".gencode(rand(6, 11))."', '".$json["data"]["emailCF"]."', '".$json["data"]["phoneCF"]."', '".$json["data"]["surnameCF"]."', '".$json["data"]["nameCF"]."', '".$json["data"]["ochestvoCF"]."', '2', '".time()."', '1', '0', '".$json["data"]["phone2CF"]."')");
		// 			$uid = $db->insert_id();
		// 		} else {
		// 			$db->query("UPDATE ".DB_PREFIX."_users SET `nick` = '".$json["data"]["emailCF"]."', `email` = '".$json["data"]["emailCF"]."', `phone` = '".$json["data"]["phoneCF"]."', `surname` = '".$json["data"]["surnameCF"]."', `name` = '".$json["data"]["nameCF"]."', `ochestvo` = '".$json["data"]["ochestvoCF"]."', phone2 = '".$json["data"]["phone2CF"]."' WHERE id = '".$json["data"]["id"]."' LIMIT 1");
		// 			$uid = $json["data"]["id"];
		// 		}
		// 		$db->query("INSERT INTO ".DB_PREFIX."_users_org (`uid`, `oid`, `post`, `choose`, `status`, `comment`, type) VALUES ('".$uid."', '".$id."', '".$json["data"]["postCF"]."', '0', '1', '".$json["data"]["commentCF"]."', '".$json["data"]["typeCF"]."')");
		// 	}
		// 	$adminTpl->info($lang['org_add_success'], 'info', null, $lang['info'], $lang['org_list'], ADMIN . '/module/organizations');
		// }
		// echo '</div>';
		// $adminTpl->admin_foot();