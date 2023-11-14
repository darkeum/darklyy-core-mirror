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

$module_array['selection'] = array(
		'name' => 'Блоки и узлы учета',
		'desc' => 'ererg',
		'subAct' => array(
			'Список блоков' => '',
			'Добавить блок' => 'add',	
			'Список группы блоков' => 'group',		
			'Расходы узлов учета' => 'metering',
			'Добавить расход' => 'metering_add',	
			'Список схем узлов учета' => 'metering_scheme',
		)
);
