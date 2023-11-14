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

global $lang;
$uid = $core->auth->user_info['id'];
$query_tc = $db->query("SELECT toid, fromid, coperformerid, observerid FROM " . DB_PREFIX . "_task WHERE status <> '3'");
$me = 0;
$my = 0;
$coper = 0;
$observ = 0;
if($db->numRows($query_tc) > 0) 
{
	
	$count_type_2 = 0;
	$count_type_3 = $db->numRows($query_tc);
	while($sc = $db->getRow($query_tc)) 
	{
		if($sc['toid'] == $uid)
		{
			$me++;
		}
		if($sc['fromid'] == $uid)
		{
			$my++;
		}
		if(!empty($sc['coperformerid']))
		{
            $arr = json_decode($sc['coperformerid'], true);
            foreach($arr as $key => $val)
			{
				if($val == $uid)
				{
					$coper++;
				}
            }  
        }
		if(!empty($sc['observerid']))
		{
            $arr = json_decode($sc['observerid'], true);
            foreach($arr as $key => $val)
			{
				if($val == $uid)
				{
					$observ++;
				}
            }  
        }
	}
}
$module_array['task'] = array(
		'name' => 'Задачи',
		'desc' => '',
		'subAct' => array(
			'Мои задачи' => '',
            'Поставленные задачи' => 'my',
            'Соисполнитель' => 'coperformer',
            'Наблюдатель' => 'observer',
			'Архив' => 'archive',
			'Создать задачу' => 'add',	
		),
		'subCount' => array(
			'' => $me,
            'my' => $my,
            'coperformer' => $coper,
            'observer' => $observ
		)
);
