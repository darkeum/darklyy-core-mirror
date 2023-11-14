<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_ACCESS')) {
    header('Location: /');
    exit;
}
function main()
{
	global $adminTpl, $config, $url, $core, $admin_conf, $db, $lang, $attrQstr;	
    switch(isset($url[2]) ? $url[2] : null){
    default:
        $lnn = 'Все товары';
		$attrQstr = "";
    break;
    case 'opt':
        $lnn = 'Меньше оптимального';
		$attrQstr = "AND n.qnorm > n.quantity ";
    break;
    case 'min':
        $lnn = 'Меньше минимального';
		$attrQstr = "AND n.qmin >= n.quantity ";
    break;
    }
	$adminTpl->admin_head('Склад');
    $atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'catalog_v2';
	$atributs['module_open'] = 'edit';
	load_grid('jqStock', $lnn, $atributs);   
	$adminTpl->admin_foot();
}

switch(isset($url[1]) ? $url[1] : null) {
	default:
		main();
	break;
}
