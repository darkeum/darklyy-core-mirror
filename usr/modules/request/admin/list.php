<?php

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

global $lang;
$module_array['request'] = array(
		'name' => 'Заявки на расчёт',
		'desc' => '',
		'subAct' => array(
			'Заявки' => '',	
			'Добавить заявку' => 'add',	
		),
);
