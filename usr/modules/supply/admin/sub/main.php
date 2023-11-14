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
	global $adminTpl, $lang;
	$lnn = $lang['supply_request'];
	$adminTpl->admin_head($lang['supply_main'].' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'supply';
	$atributs['module_open'] = 'detail';
	$atributs['js'] = 'setTimeout(function () {$("#jqSupplyMain").jqGrid(\'setGridParam\',{postData:{status: \'1\'}}); $(\'#jqSupplyMain\').trigger(\'reloadGrid\');}, 1050);';	
	loadGrid('jqSupplyMain', $lnn, $atributs, ROOT.'/usr/modules/supply/admin/grid/');
	$adminTpl->admin_foot();
} 