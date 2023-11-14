<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Scada Language Lines
    |--------------------------------------------------------------------------
    |
    | Этот языковой файл используется в модуле "Диспетчеризация"
    | Вы можете изменить эти языковые строки в соответствии с 
	| требованиями вашего приложения.
    |
    */

    'main.edit.title'                           => 'Редактирование объекта диспетчеризации #:numb',
    'main.create.title'                         => 'Добавления объекта диспетчеризации',
    'main.title'                                => 'Наименование объекта диспетчеризации',
    'main.title.placeholder'                    => 'Введите наименование объекта диспетчеризации',
    'main.address'                              => 'Адрес объекта диспетчеризации',
    'main.address.placeholder'                  => 'Введите адрес объекта диспетчеризации',
    'main.ip'                                   => 'IP адрес объекта диспетчеризации',
    'main.ip.placeholder'                       => 'Укажите IP адрес объекта диспетчеризации',
    'main.port'                                 => 'Порт объекта диспетчеризации',
    'main.port.placeholder'                     => 'Укажите порт объекта диспетчеризации',
    'main.rtuConverter'                         => 'Использовать Modbus поверх TCP?',
    'main.status'                               => 'Объект включен в работу?',
    'main.updated'                              => 'Объект диспетчеризации успешно обновлен',
    'main.created'                              => 'Объект диспетчеризации успешно создан',

    'tags.edit.title'                           => 'Редактирование тега #:numb',
    'tags.create.title'                         => 'Добавления тега',
    'tags.scada'                                => 'Выберите объект диспетчеризации',
    'tags.title'                                => 'Наименование тега',
    'tags.title.placeholder'                    => 'Введите наименование тега',
    'tags.unit_id'                              => 'Адрес устройства',
    'tags.unit_id.placeholder'                  => 'Укажите адрес устройства',
    'tags.address'                              => 'Адрес тега',
    'tags.address.placeholder'                  => 'Укажите адрес тега',
    'tags.quantity'                             => 'Длина тега',
    'tags.quantity.placeholder'                 => 'Укажите длину',
    'tags.registr'                              => 'Регистр тега',
    'tags.endian'                               => 'Перестановка байтов',
    'tags.actions'                              => 'Доступные действия',
    'tags.actions_type'                         => 'Дополнительное действие',
    'tags.type'                                 => 'Тип тега',
    'tags.byte'                                 => 'Извлечь указанные байт',
    'tags.byte.placeholder'                     => 'При необходимости укажете байт который необходимо извлечь',
    'tags.recalculation'                        => 'Пересчитывать значение?',
    'tags.coefficient_a'                        => 'Коэффициент A',
    'tags.coefficient_a.placeholder'            => 'Укажите Коэффициент A',
    'tags.coefficient_b'                        => 'Коэффициент B',
    'tags.coefficient_b.placeholder'            => 'Укажите Коэффициент B',
    'tags.field_type'                           => 'Тип поля для мнемосхемы',
    'tags.field_id'                             => 'ID поля для мнемосхемы',
    'tags.field_id.placeholder'                 => 'При необходимости укажите ID поля для мнемосхемы',
    'tags.notifiable'                           => 'Оповещать об изменении?',
    'tags.status'                               => 'Тег включен в работу?',
    'tags.updated'                              => 'Тег успешно обновлен',
    'tags.created'                              => 'Тег успешно создан',

    'info'                                      => 'Информация',
    'create'                                    => 'Добавить',
    'update'                                    => 'Обновить',
    'no_use'                                    => 'Не используется',
    'no'                                        => 'Нет',
    'yes'                                       => 'Да',

];
