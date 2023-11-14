<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}

global $lang;


//*******************
//Change name module
//*******************

$module_array['corrections'] = [
	'name' => 'Корректировки',
	'subAct' => array(
		'Список заявок' => '',
		'Изменения' => 'changes',		
	)
];
