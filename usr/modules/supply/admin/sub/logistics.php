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

//  Статусы
//  1 - 3-я дата не проставлена 
//  2 - 3-я дата проставлена
//  3 - 3-я дата проставлена и доп дата

function logistics() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Закупки';
	$adminTpl->admin_head('Менеджер закупок | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'supply';
	$atributs['module_open'] = 'logistics_detail';
	$atributs['table_class'] = 'noclass';
	$atributs['js'] = '  setTimeout(function () {$("#jqSupplyLogistics").jqGrid(\'setGridParam\',{postData:{arrived: \'0\'}}); $(\'#jqSupplyLogistics\').trigger(\'reloadGrid\');}, 150);';	
	load_grid('jqSupplyLogistics', $lnn, $atributs, ROOT.'/usr/modules/supply/admin/grid/');
	echo '<script src="/usr/modules/supply/admin/asset/js/scriptLogistics.js"></script>';
	$adminTpl->admin_foot();
} 
