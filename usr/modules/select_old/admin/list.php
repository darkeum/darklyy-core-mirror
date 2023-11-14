<?php
if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


$module_array['select'] = array(
		'name' => 'Заявки на рассчет',
		'desc' => 'Новостной модуль поможет быстро создавать новости на Вашем сайте, а так же редактировать их.',	
		'subAct' => array(
			'Список' => '',
			'Добавить' => 'add',			
			'Настройки' => 'config',
		)
);




$toconfig['partner'] = array
(
'name' => 'Новости системы',
'link' => 'module/partner/config',
'param' => 'partner_conf'
);