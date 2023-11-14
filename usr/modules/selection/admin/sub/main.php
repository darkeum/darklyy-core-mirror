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


function content_main() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Блок';
	$adminTpl->admin_head('Блок | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'selection';
	$atributs['module_open'] = 'detail';
	load_grid('jqSelection', $lnn, $atributs, ROOT.'/usr/modules/selection/admin/grid/');
	$adminTpl->admin_foot();
} 