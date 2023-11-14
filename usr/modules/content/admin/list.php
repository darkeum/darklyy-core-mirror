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




$module_array['content'] = array(
		'name' => 'Статические страницы',
		'desc' => 'uu',
		'subAct' => array(
			'Список' => '',
			'Добавить' => 'add',			
			'Настройки' => 'config',
		)
);

$toconfig['content'] = array
(
		'name' => 'Статические страницы',
		'link' => 'module/content/config',
		'param' => 'content_conf'
);