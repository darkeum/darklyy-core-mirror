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

$module_array['sales'] = array(
		'name' => 'Менеджер заказов',
		'desc' => 'ererg',
		'subAct' => array(
			'Список заказов' => '',
			'Заказы на согласование' => 'site',
			'Добавить заказ' => 'add',	
			'Поиск по номенклатуре' => 'search_name'		
		),
		'subCount' => array(
            'site' => \Sales\Model\Sales::where("status_client", 2)->count(),
		)
);
