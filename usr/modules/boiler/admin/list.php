<?php

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

global $lang;

$module_array['boiler'] = [
	'name' => 'Котёл продаж',
	'subAct' => array(
		"Заявки" => "bid",
	)
];
