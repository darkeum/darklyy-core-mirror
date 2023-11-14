<?php

/**
 * @name        JMY CORE
 * @link        https://jmy.su/
 * @copyright   Copyright (C) 2012-2018 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


$dir = ROOT.'/usr/modules/organizations/admin/sub/';  
includeSub($dir);

global $adminTpl, $core, $db, $admin_conf, $url, $lang;
switch (isset($url[3]) ? $url[3] : null) {
	default:
		content_main();
	break;

	case 'add':
		\Organizations\OrganizationsTemplate::add();
	break;

	case "check_org":
		echo json_encode(\Organizations\Model\Organizations::check_org($_POST), JSON_UNESCAPED_UNICODE);
	break;

	case "getBankList":
		echo json_encode(["data" => \Organizations\Model\OrganizationsBank::where("oid", $_POST["id"] ?? false)->get()->toArray()], JSON_UNESCAPED_UNICODE);
	break;

	case "edit":
		\Organizations\OrganizationsTemplate::check_auth_group();
		\Organizations\OrganizationsTemplate::add($url[4]);
	break;

	case "update_db":
		\Organizations\OrganizationsTemplate::update_db();
	break;

	case "getDataUser":
		echo \Organizations\OrganizationsTemplate::getDataUser($_POST);
	break;

	case "save":
		\Organizations\OrganizationsTemplate::save($_POST);
	break;

	case "delete":
		\Organizations\OrganizationsTemplate::check_auth_group();
		\Organizations\Model\Organizations::find($url[4])->delete();
		header('Location: /' . ADMIN . '/module/organizations');
	break;

	case "print":
		\Organizations\OrganizationsTemplate::print($url[4]);
	break;

	case "action":
		\Organizations\OrganizationsTemplate::check_auth_group();
		$type = $_POST['submit'];
		if (is_array($_POST['checks'])) {
			switch ($type) {
				case $lang['delete']:
					\Organizations\Model\Organizations::find($_POST['checks'])->delete();
				break;
			}
		}
		header('Location: /' . ADMIN . '/module/organizations');
	break;

	case 'ajax':
		global $adminTpl, $db, $lang;
		ajaxInit();
		$type = $url[4];
		switch ($type) {
			case "isgeo":
				if (isset($_POST['address'])) {
					$params = array(
						'format'  => 'json',
						'geocode' => 'Россия, ' . $_POST['address'],
						'results' => 1,
					);
					$response = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?' . http_build_query($params, '', '&')));

					if ($response->response->GeoObjectCollection->featureMember[0]->GeoObject->metaDataProperty->GeocoderMetaData->kind == 'house') {
						echo 'true';
					} else {
						echo 'false';
					}
				} else {
					echo 'false';
				}
			break;
			case "org_add":
				if (isset($url[5])) {
					echo ajax_org_add($url[5]);
				} else {
					echo ajax_org_add();
				}
			break;
			case "org_list":
				if (isset($url[5])) {
					echo ajax_org_list($url[5]);
				} else {
					echo ajax_org_list();
				}

			break;
		}
	break;
}