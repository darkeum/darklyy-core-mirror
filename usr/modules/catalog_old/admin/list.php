<?php
if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


$module_array['catalog'] = array(
		'name' => 'Каталог товаров',
		'desc' => 'Новостной модуль поможет быстро создавать новости на Вашем сайте, а так же редактировать их.',	
		'subAct' => array(
			'Список' => '',
			'Добавить' => 'add',
			'Теги' => 'tags',
			'Группы товаров' => 'group',
			'Настройки' => 'config',
		)
);

$menu_array['catalog'] = array(
		'name' =>  'Менеджер каталога',
		'icon' => 'fa fa-paint-brush'		
);

$toconfig['catalog'] = array
(
'name' => 'Новости системы',
'link' => 'module/catalog/config',
'param' => 'catalog_conf'
);