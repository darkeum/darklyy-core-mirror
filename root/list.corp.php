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

	global $lang, $core;
	//echo $core->auth->user_info['exgroup'];
	$component_array = array(

		'cats' => array (
			'name' => _AP_CATS,
			'desc' => _AP_CATS_DESC,
			'subAct' => array(
				_AP_CATS_LIST => '',
				_AP_CATS_ADD => 'add',
				_AP_CATS_FADD => 'scan_cat',
			),
			'shown' => 1
		),



		'comments' => array (
			'name' => _AP_COMMENTS,
			'desc' => _AP_COM_DESC,
			'subAct' => array(
				_AP_COMMENTS_MAIN => '',
				_AP_COMMENTS_MODER => '../moderation/com/comments',
			),
			'shown' => 1
		),
		'user' => array (
			'name' => _AP_USERS,
			'desc' => _AP_USERS_DESC,
			'subAct' => array(
				'Список' => '',
				'Группы' => '../groups',
			),
			'shown' => 1
		),
		'client' => array (
			'name' => 'Менеджер клиентов',
			'desc' => _AP_USERS_DESC,
			'subAct' => array(
				'Список' => '',
				'Массовое добавление' => 'mass',
				'Группы' => '../groups',
			),
			'shown' => 1
		),



		'seminar' => array (
			'name' => 'Семинар',
			'desc' => _AP_PUB_DESC,
			'shown' => 1,
			'subAct' => array(

			)
		),
		'attribute' => array (
			'name' => 'Атрибуты',
			'desc' => _AP_PUB_DESC,
			'shown' => 1,
			'subAct' => array(
				'Атрибуты' => '',
				'Список всех атрибутов' => 'categoryAllAttribute',
			)
		),


		'letter' => array (
			'name' => 'Реестр корреспонденции',
			'desc' => _AP_PUB_DESC,
			'shown' => 1,
			'subAct' => array(
				'Исходящие' => 'out',
				'Входящие' => 'in',
				'Добавить' => 'add',
			)
		),

		'object' => array (
			'name' => 'Менеджер объектов',
			'desc' => _AP_PUB_DESC,
			'shown' => 1,
			'subAct' => array(
				'Список объектов' => '',
				'Карта' => 'map',
				'Добавить объект' => 'add',
			)
		),
		'documents' => array (
			'name' => 'Реестр документов',
			'desc' => _AP_PUB_DESC,
			'shown' => 1,
			'subAct' => array(
				'Список объектов' => '',
				'Карта' => 'map',
				'Добавить объект' => 'add',
			)
		),
		'calendar' => array (
			'name' => 'Календарь событий',
			'desc' => _AP_PUB_DESC,
			'shown' => 1,
			'subAct' => array(
				'Календарь' => '',
				'Настройки' => 'config'
			)
		),
		'stock' => array (
			'name' => 'Менеджер склада',
			'desc' => _AP_PUB_DESC,
			'shown' => 1,
			'subAct' => array(
				'Все товары' => '',
				'Меньше оптимального' => 'opt',
				'Меньше минимального' => 'min',
			)
		),


	);



	$services_array = array(

		'search' => array (
			'name' => _AP_SEARCH,
			'desc' => _AP_SEARCH_DESC,
			'shown' => 1
		),

	);
