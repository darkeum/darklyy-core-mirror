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
	$lnn = $lang['fcontrol_control'];
	$adminTpl->admin_head($lang['fcontrol'].' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'fcontrol';
	$atributs['module_open'] = 'detail';
	loadGrid('jqFcontrol', $lnn, $atributs, ROOT.'/usr/modules/fcontrol/admin/grid/');
    // $atributs['js'] = '  setTimeout(function () {$("#jqFcontrol").jqGrid(\'setGridParam\',{postData:{status: \'2\'}}); $(\'#jqFcontrol\').trigger(\'reloadGrid\');}, 150);';	
	$adminTpl->admin_foot();
} 