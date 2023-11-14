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
    $adminTpl->admin_head('Атридок');
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'doc';
    $atributs['module_open'] = 'view';
    $atributs['button'] = 'Добавить документ';
	$atributs['button_url'] = '/administration/module/doc/add';	
	$atributs['button_onclick'] = 'window.open(\'/administration/module/doc/add\')';
	load_grid('jqDoc', 'Атридок', $atributs, ROOT.'/usr/modules/doc/admin/grid/');   
	$adminTpl->admin_foot();
} 



