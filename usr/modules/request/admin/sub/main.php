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


function main() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang, $taskAttr;
    $adminTpl->admin_head('Заявки на расчёт');
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'request';
    $atributs['module_open'] = 'view';
	load_grid('jqRequest', 'Заявки на расчёт', $atributs, ROOT.'/usr/modules/request/admin/grid/');   
	$adminTpl->admin_foot();
} 



