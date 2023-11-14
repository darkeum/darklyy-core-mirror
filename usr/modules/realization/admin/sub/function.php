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


//функция вывода верхнего меню навигации
function realization_head_nav($id, $active) 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$active_array = array();
	$active_array['order'] = '';
	$active_array['task'] = '';
	$active_array['relations'] = '';
	$active_array[$active] = 'active';	
	$adminTpl->subNavIncludes[] =  '<div class="mt-30 hidden-xs"><a href="/'.ADMIN.'/module/realization/order/'.$id.'" class="btn btn-default '.$active_array['order'].' btn-sm fw600 ml10"><span class="fa fa-plus pr5"></span> '.$lang['realization_nav_info'].'</a><a href="/'.ADMIN.'/module/realization/relations/'.$id.'" class="btn btn-default  '.$active_array['relations'].' btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> '.$lang['realization_nav_relations'].'</a></div>';	
	return false;
} 
