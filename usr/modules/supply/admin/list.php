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

$module_array['supply'] = array(
		'name' => 'Менеджер закупок',
		'desc' => 'erg',
		'subAct' => array(
			'Заявки на закупку' => '',
			'Заявки на сроки' => 'twomain',
			'Счета поставщиков' => 'order',
			'Логистика' => 'logistics',
			'Документы поступления' => 'receipts',
			'Добавить закупку' => 'add',
			'Поиск по номенклатуре' => 'search_name',			
		)
);
