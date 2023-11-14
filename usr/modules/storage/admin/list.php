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

$module_array['storage'] = array(
		'name' => 'Менеджер склада',
		'desc' => 'erg',
		'subAct' => array(
			'Заявки на пересчет' => '',	
			'Добавить заявку' => 'add',
			// 'Менеджер резервов' => 'reserv',		
			'Перемещения' => 'transfer',		
			'Добавить перемещение' => 'transfer_add',
			'Разрешенная отгрузка' => 'docPrint',		
			'Сборка товаров' => 'assembly',		
			'Сборка товаров с суммой' => 'assembly/sum',		
			'Поиск по номенклатуре' => 'search_name',		
		)
);
