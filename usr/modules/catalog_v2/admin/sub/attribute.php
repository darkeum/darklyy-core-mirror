<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    location(' /');
    exit;
}

function attribute_list()
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Атрибуты';
	$adminTpl->admin_head('Менеджер каталога | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'catalog_v2';
	$atributs['module_open'] = 'attribute/edit';
	load_grid('jqAttribute', $lnn, $atributs);	
	$adminTpl->admin_foot();
}
