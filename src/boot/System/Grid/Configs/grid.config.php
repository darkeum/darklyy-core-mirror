<?php

return [

    // Defaults vars
    'class_namespace' => 'App\\Grids',
    'grid_path' => 'tytj',
    'encoding' => 'utf-8',

    'db_driver' => 'Pdo',
    'clear' => false,
    'client' => '/phpgrid/client/vendor',

    'pdo_dsn' => null,
    'pdo_user' => 'root',
    'pdo_pass' => '',
    'pdo_options' => null,

    'debug_output' => true,

    # ...
    'input' => [
        'grid' => 'grid',
        'patch' => '',
        'url' => '',
        'module' => '',
        'operation' => 'oper',
    ],

    # ...
    'actions' => [
        'show' => ['Просмотр', 'eye'],
        'edit' => ['Редактировать', 'edit'],
        'destroy' => ['Удалить', 'trash'],

        'print' => ['Распечатать', 'printer'],
        'download' => ['Скачать', 'download'],
    ],

    # ignored
    'ignored' => ['actions', 'checkbox'],

    # Параметры
    'params' => [
        'configs' => [
            'icon' => ''
        ]
    ],

    # Значения по умолчанию Grid
    'default' => [

        # Поля которые необходимо фильтровать
        'filters' => [
            'label',
            'format_date',
            //'render',
            'manual',
            'encode',
            'sortable',
            'search',
            'search_op',
            'width',
            'className',
            'icon',
            'priority'
        ],

        # ...
        'cols' => [

            'title' => '',              // Заголовок столбца по умолчанию

            // Типы используемых полей для фильтров
            'type' => 'input',          // Тип поля по умолчанию ('input', 'select', 'date', 'date_range', 'actions')

            // Сделать подгрузку переводов из Lang файла, а не от сюда!!!
            'label' => [                // Список label для полей - placeholder для поля по умолчанию
                'input' => 'Найти',
                'select' => 'Выбрать',
                'date' => 'Выбрать дату',
                'date_range' => 'Выбрать дату',
                'checkbox' => 'Отметить все',
                'actions' => 'Действия',
            ],

            //
            'render' => [
                false,
                // '_' => true,
                'text' => 'text',
                'input' => 'input',
            ],

            //
            'popover' => 'top',         // Использование Bootstrap popover - Позиция по умолчанию

            // 'db' => '',              //
            // 'db_agg' => '',          //
            'unset' => false,           //
            'replace' => null,          // Замена значения в строке если нужно
            'formatter' => null,        //

            // Для flatpickr
            'format_date' => [          // Формат даты для поля date_range. (По умолчанию - 'd.m.Y')
                '_' => null,
                'date' => 'd.m.Y',
                'date_range' => 'd.m.Y',
            ],

            //
            'manual' => [
                '_' => false,
                'actions' => true
            ],

            'visible' => true,          // Видимость столбца. (По умолчанию - Показывать)

            'sortable' => [             // Сортировка на столбце по умолчанию
                '_' => true,
                'actions' => false,
            ],

            'editable' => false,        //

            'search' => [               // Поиск? По чему? Что за поиск?
                '_' => true,
                'actions' => false,
            ],

            'search_op' => [            // Опция для формирования запроса query sql по умолчанию
                '_' => 'auto',
                'input' => 'like',
                'select' => 'equal',
                'date' => 'date',
                'date_range' => 'date_range',
                'actions' => false,
            ],

            'other' => null,            // Если ли нужны

            'width' => [                // Ширина столбца по умолчанию (Если не указывать то будет ширина auto)
                '_' => 'auto',
                'actions' => 0,         // Ширина столбца для actions (Действия)
            ],

            // 'classes' => '',            // Css Классы по умолчанию ? я хз что это. Было это. Не понятно...

            'className' => [            // Css Класс по умолчанию (Будет добавлен к столбцу)
                '_' => false,
                'actions' => 'text-center',
            ],

            'icon' => [                 // Задает иконку Пример : 'feathersettings' или 'settings' для data-feather
                'actions' => 'settings'
            ],

            'prefix' => null,           // Фейковый префикс ...

            'null' => null,             // Если ничего нет Null

            'encode' => [               // Кодирование в текст (Если нужно вывести код html то обязательно false)
                '_' => true,
                'actions' => false
            ],

            'excel_hidden' => false,    //

            'excel_only' => false,      //

            'priority' => [             // Приоритет
                '_' => null,
                'actions' => 0,
            ],
        ],

        # ...
        'nav' => [
            'search' => false,         // Поле поиска
            'builder' => true,         // Конструктор поиска
            'mods' => true,            // Модификаторы таблицы

            'export' => [
                'excel' => true,
                'pdf' => true,
                'print' => true,
                'copy' => true,
                'csv' => true,
            ],
        ],

        # Список действий мыши по умолчанию (select, open, 'opennt', 'opennw')
        'mouse' => [
            'click' => null,
            'dblclick' => 'open',
        ],

        'options' => [],                // Список строк выпадающего списка по умолчанию ???
        'reserved_col_names' => ['page', 'sidx', 'sord', 'nd', 'oper', 'filters', 'rd'],
    ],

    'default_fancy' => [

        # Поля которые необходимо фильтровать
        'filters' => [
            'label',
            'format_date',
            //'render',
            'manual',
            'encode',
            'sortable',
            'search',
            'search_op',
            'width',
            'className',
            'icon',
            'priority'
        ],

        # ...
        'cols' => [

            'title' => '',              // Заголовок столбца по умолчанию

            // Типы используемых полей для фильтров
            'type' => 'input',          // Тип поля по умолчанию ('input', 'select', 'date', 'date_range', 'actions')

            // Сделать подгрузку переводов из Lang файла, а не от сюда!!!
            'label' => [                // Список label для полей - placeholder для поля по умолчанию
                'input' => 'Найти',
                'select' => 'Выбрать',
                'date' => 'Выбрать дату',
                'date_range' => 'Выбрать дату',
                'checkbox' => 'Отметить все',
                'actions' => 'Действия',
            ],

            //
            'render' => [
                false,
                // '_' => true,
                'text' => 'text',
                'input' => 'input',
            ],

            //
            'popover' => 'top',         // Использование Bootstrap popover - Позиция по умолчанию

            // 'db' => '',              //
            // 'db_agg' => '',          //
            'unset' => false,           //
            'replace' => null,          // Замена значения в строке если нужно
            'formatter' => null,        //

            // Для flatpickr
            'format_date' => [          // Формат даты для поля date_range. (По умолчанию - 'd.m.Y')
                '_' => null,
                'date' => 'd.m.Y',
                'date_range' => 'd.m.Y',
            ],

            //
            'manual' => [
                '_' => false,
                'actions' => true
            ],

            'visible' => true,          // Видимость столбца. (По умолчанию - Показывать)

            'sortable' => [             // Сортировка на столбце по умолчанию
                '_' => true,
                'actions' => false,
            ],

            'editable' => false,        //

            'search' => [               // Поиск? По чему? Что за поиск?
                '_' => true,
                'actions' => false,
            ],

            'search_op' => [            // Опция для формирования запроса query sql по умолчанию
                '_' => 'auto',
                'input' => 'like',
                'select' => 'equal',
                'date' => 'date',
                'date_range' => 'date_range',
                'actions' => false,
            ],

            'other' => null,            // Если ли нужны

            'width' => [                // Ширина столбца по умолчанию (Если не указывать то будет ширина auto)
                '_' => 'auto',
                'actions' => 0,         // Ширина столбца для actions (Действия)
            ],

            // 'classes' => '',            // Css Классы по умолчанию ? я хз что это. Было это. Не понятно...

            'className' => [            // Css Класс по умолчанию (Будет добавлен к столбцу)
                '_' => false,
                'actions' => 'text-center',
            ],

            'icon' => [                 // Задает иконку Пример : 'feathersettings' или 'settings' для data-feather
                'actions' => 'settings'
            ],

            'prefix' => null,           // Фейковый префикс ...

            'null' => null,             // Если ничего нет Null

            'encode' => [               // Кодирование в текст (Если нужно вывести код html то обязательно false)
                '_' => true,
                'actions' => false
            ],

            'excel_hidden' => false,    //

            'excel_only' => false,      //

            'priority' => [             // Приоритет
                '_' => null,
                'actions' => 0,
            ],
        ],

        # ...
        'nav' => [
            'search' => false,         // Поле поиска
            'builder' => true,         // Конструктор поиска
            'mods' => true,            // Модификаторы таблицы

            'export' => [
                'excel' => true,
                'pdf' => true,
                'print' => true,
                'copy' => true,
                'csv' => true,
            ],
        ],

        # Список действий мыши по умолчанию (select, open, 'opennt', 'opennw')
        'mouse' => [
            'click' => null,
            'dblclick' => 'open',
        ],

        'options' => [],                // Список строк выпадающего списка по умолчанию ???
        'reserved_col_names' => ['page', 'sidx', 'sord', 'nd', 'oper', 'filters', 'rd'],
    ],

];
