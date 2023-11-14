<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

global $lang;


//*******************
//Change name module
//*******************

$LogisticsDeliveryDetailTemp = new \Logistics\LogisticsDeliveryDetailTemp;
$query = $db->query($LogisticsDeliveryDetailTemp->logistics_delivery_query());
$delivery_count = $db->numRows($query);

$module_array['logistics'] = [
	'name' => 'Логистика',
	'subAct' => array(
		"Забор" => "",
		"Доставка" => "delivery",
		"Архив доставки" => "delivery_archive",
		"Автопарк" => "car_park",
		"Автопарк добавить" => "car_park/create",
	),
	'subCount' => array(
		'delivery' => $delivery_count,
	)
];
