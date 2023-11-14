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


function content_main_reserv() 
{
	global $adminTpl, $core, $db, $lang, $whereSM, $typeSM;
	$lnn = 'Список резервов';
	$adminTpl->admin_head('Заявки на резерв | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'storage';
	$atributs['module_open'] = 'reserv/detail';
	//$atributs['js'] = '  setTimeout(function () {$("#jqStorageMain").jqGrid(\'setGridParam\',{postData:{status: \'1\'}}); $(\'#jqStorageMain\').trigger(\'reloadGrid\');}, 150);';	
	$whereSM = "s.type IN (4, 5) AND";
	$typeSM = "reserv";
	load_grid('jqStorageMain', $lnn, $atributs, ROOT.'/usr/modules/storage/admin/grid/');
	$adminTpl->admin_foot();
} 