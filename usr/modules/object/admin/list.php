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

$module_array['object'] = array(
		'name' => 'Менеджер объектов',
		'desc' => 'erg',
		'subAct' => array(
			'Список объектов' => '',	
			'Карта объектов' => 'map',		
			'Добавить объект' => 'add'				
		)
);
