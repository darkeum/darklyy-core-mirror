<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    location(' /');
    exit;
}


function delete($id) {
global $db;
/*
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_catalog WHERE id = '" . $id . "'");
	$catalog = $db->getRow($query);
	$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` = '" . $catalog['id'] . "' AND `module` = 'catalog'");
	full_rmdir(ROOT . initDC('catalog', $catalog['id']));
	$db->query("DELETE FROM `" . DB_PREFIX . "_catalog` WHERE `id` = " . $id . " LIMIT 1");
	$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_catalog=user_catalog-1 WHERE `nick` ='" . $catalog['author'] . "' LIMIT 1", true);
	workTags($id, $catalog['tags'], 'delete');
	deleteComments('catalog', $id);*/
}

function workTags($id, $tags, $do = 'add')
{
global $db;
	if(!empty($tags))
	{
		$tag = array_map('trim', explode(',', $tags));
		foreach($tag as $t)
		{
			if($do == 'add')
			{
				$db->query("INSERT INTO `" . DB_PREFIX . "_tags` ( `id` , `tag` , `module` ) VALUES (NULL, '" . $db->safesql($t) . "', 'catalog');");
			}
			elseif($do == 'delete')
			{
				$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $t . "' and module='catalog' LIMIT 1");			
			}
		}
	}
}

function changeuGroup($var)
{
global $adminTpl, $db, $catalog_conf;
    $content = '<select class="form-control" name="{varName}">';
	$query2 = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups`");
	while($rows2 = $db->getRow($query2)) 
	{
		$sel = ($catalog_conf[$var] == $rows2['id']) ? 'selected' : '';
		$content .= '<option value="' . $rows2['id'] . '" ' . $sel . '>' . $rows2['name'] . '</option>';
	}
	$content .= '</select>';
	return $content;
}