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
	global $adminTpl, $core, $db, $lang, $whereSM, $typeSM;
	$lnn = $lang['object_list'];
	$adminTpl->admin_head($lang['object_main'].' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'object';
	$atributs['module_open'] = 'view';	
	load_grid('jqObject', $lnn, $atributs, ROOT.'/usr/modules/object/admin/grid/');
	$adminTpl->admin_foot();
} 