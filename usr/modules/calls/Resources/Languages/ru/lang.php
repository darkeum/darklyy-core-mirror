<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (25.05.2023 7:45)
*/

return [
    'module' => 'Телефонная книга',
    'admin' => [
        'list'                                                => 'Список звонков',
        'manager'                                             => 'Звонки',
        'contacts'                                            => 'Контакты',
        'confirm'                                             => 'Подтвердите ваше действие!',
        'delete'                                              => 'Удалить',
        'redirect'                                            => 'Перейти',
        'listen'                                              => 'Прослушать',
        'save'                                                => 'Скачать',
        'editing phones'                                      => 'Редактирование телефонов',
    ],
    'components' => [
        'success_edit'                                        => 'Редактирование успешно',
        'success_create'                                      => 'Добавление успешно',
    ],
    'general' => [
        'list_calls' => 'Список звонков',
        'list_phones' => 'Список телефонов',
        'contact_create' => 'Добавить телефон',
    ],
    'grid' => [
        'title' => 'Список телефонов',
        'src_title' => 'Вызывающий абонент',
        'dst_title' => 'Вызываемый абонент',
        'calldate_title' => 'Время звонка',
        'duration_title' => 'Длительность звонка',
        'uid' => 'Пользователь',
        'phoneid' => 'Телефон',
        'signature' => 'Должность',
        'status' => 'Статус файла'
    ]
];