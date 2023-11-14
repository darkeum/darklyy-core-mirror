<?php

function change_log_main(){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Журнал изменений';
	$adminTpl->admin_head('Каталог товаров | '.$lnn);
	$atributs = array();
	$atributs['opts'] = '';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'catalog_v2';
    $atributs['module_open'] = 'edit';	

    load_grid('jqChangeLog', $lnn, $atributs, ROOT.'/usr/modules/catalog_v2/admin/grid/');

	$adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/change_log.js"></script>';
    
    $adminTpl->admin_foot();
}

function change_log_confirm($id){
    global $db;
    if($id){
        $date = date("Y-m-d H:i:s");
        $db->query("UPDATE ".DB_PREFIX."_catalog_change_log SET status = '2', date_confirm = '".$date."' WHERE id = '".$id."' LIMIT 1");
    }
}