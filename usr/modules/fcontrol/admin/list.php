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

$module_array['fcontrol'] = array(
		'name' => 'Финансы и контроль',
		'subAct' => array(
			'Контроль закупок' => '',
			'Контроль закупок(все)' => 'all',
			'Контроль остатков' => 'storage',		
			'Заявки на оплату' => 'pay',		
		)
);
