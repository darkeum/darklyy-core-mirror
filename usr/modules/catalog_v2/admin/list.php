<?php
if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


$module_array['catalog_v2'] = array(
		'name' => 'Каталог товаров',
		'desc' => 'Новостной модуль поможет быстро создавать новости на Вашем сайте, а так же редактировать их.',	
		'subAct' => array(
			'Список' => '',
			'Добавить' => 'add',
			'Атрибуты' => 'attribute',
			'Производители' => 'manufacture',
			'Сертификаты' => 'cert',
			// 'Настройки' => 'config',
			'Импорт' => 'import',
			// 'Загрузка по категории' => 'load_cat',
			'Объединение товаров' => 'union_goods',
			'Журнал изменений' => 'change_log',
			'Насосы' => 'pump',
			'Бэкапы' => 'backup',
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