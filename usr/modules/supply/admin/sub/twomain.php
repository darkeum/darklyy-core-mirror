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


function twomain() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Закупки';
	$adminTpl->admin_head('Менеджер закупок | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'supply';
	$atributs['module_open'] = 'twodetail';
    $atributs['js'] = '  setTimeout(function () {$("#jqSupplyTwoMain").jqGrid(\'setGridParam\',{postData:{status: \'1\'}}); $(\'#jqSupplyTwoMain\').trigger(\'reloadGrid\');}, 150);';
	load_grid('jqSupplyTwoMain', $lnn, $atributs);
	$adminTpl->admin_foot();
} 



