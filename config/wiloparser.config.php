<?php
return [

    // Параметры парсера WILO
    'params' => [

        'DB' => [
            'driver' => 'mysql',
            'host' => '188.127.249.171',
            'port' => '3314',
            'database' => 'dev_data',
            'username' => 'dev_user',
            'password' => 'hT3wQ9uB4a',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'old' => false,
        'types' => [
            'HEAD' => 'QH',
            'ETA' => 'QE',
            'NPSH' => 'NPSH',
            'POWER' => 'QP',

            'TORQUE' => 'TORQUE',
            'SPEED' => 'SPEED',
        ],

        'delete' => [
            'now' => false,
            'all' => false
        ],

        // Данные которые надо доставать
        'filters' => [

            // Общие
            'IDENT', // Идентификатор - ОБЯЗАТЕЛЬНОЕ ПОЛЕ !!!
            // 'NAME',
            // 'SHORTNAME',

            // НЕОБХОДИМЫЕ ПОЛЯ
            'PERFORMANCECHART', // Ключ поиска - ОБЯЗАТЕЛЬНОЕ ПОЛЕ !!!
            'POINTS', // Точки
            'WEIGHT', // Вес
            'FILENAME', // Имя файла

            // PERFORMANCECHART
            // 'PART', // Группируемое по слову
            // 'CURVEFLAGS',
            // 'VISCOSITY',
            // 'DENSITY',
            // 'SPEEDSTAGESIGN',
            // 'SHELLCVMETHOD',

            // FIELD
            // 'AXIS', // Группируемое по слову
            // 'SPEEDPTS',
            // 'MOTOR',

            // MOTORLIST
            // 'FREQUENCY',
            // 'INDEX',
            // 'MOTOR', // Есть в FIELD

            // CURVE
            // 'SPEED',
            // 'IMPELLER',
            // 'BESTEFFPOINT',

            // PART
            // 'CURVETYPE', // Тип кривой
            // 'DEFRGX',
            // 'DATE',
            // 'DIAGRX',
            // 'APPROX',
            // 'VALUE',
            // 'POINTS', // Точки

            // Группируемое по слову
            // 'LIMIT',
        ],

        // Группировка данных по первому последующему слову
        'groups' => [
            // 'MEASUREMENT',
            // 'LIMIT',
            // 'AXIS',
            // 'PART',
        ],

    ],

];
