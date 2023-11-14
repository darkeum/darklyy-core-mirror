<?php
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;	

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

function metering_scheme(){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;	
    $lnn = 'Схема узла учета'; 
	$adminTpl->admin_head('Список схем узла учета');
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'selection';
    $atributs['module_open'] = 'metering_detail_scheme';
    $atributs['button'] = 'Добавить схему';
	$atributs['button_url'] = '/administration/module/selection/metering_scheme_add';	
    load_grid('jqMeteringScheme', $lnn, $atributs, ROOT.'/usr/modules/selection/admin/grid/'); 
	$adminTpl->admin_foot();
}