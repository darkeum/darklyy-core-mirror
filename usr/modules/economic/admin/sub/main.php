<?php

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


function main() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = '';
	$adminTpl->admin_head();
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'economic/act';
	$atributs['module_open'] = 'editor';
	$atributs['button'] = 'Добавить';
	$atributs['button_url'] = '/administration/module/economic/act/editor';	
	//$atributs['js'] = 'setTimeout(function () {$("#jqSales").jqGrid(\'setGridParam\',{postData:{end: \'0\'}}); $(\'#jqSales\').trigger(\'reloadGrid\');}, 150);';
    loadGrid('jqAct', $lnn, $atributs, ROOT.'/usr/modules/economic/admin/grid/');
    
	//$adminTpl->footIncludes[] = '<script src="/usr/modules/***"></script>';
	$adminTpl->admin_foot();
} 