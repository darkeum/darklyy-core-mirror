<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Конфигурация для модуля Page
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    '' => '',
    'paginate' => 10,

    // Список страниц модуля
    // 'pages' => ['Имя модуля' => 'Заголовок Title для модуля']
    // или
    // 'pages' => ['Имя модуля' => ['title' => '...', 'cats' => ['name' => '...', 'url или route' => '...' ]]]
    'pages' => [
        'mounting' => [
            'title' => 'Монтаж',
            'cats' => 'mounting,certs_lic,objects,map'
        ],
        'servicing' => [
            'title' => 'Обслуживание объектов',
            'cats' => 'servicing,certs_lic,objects,map'
        ],
        'about' => [
            'title' => 'О нас',
            'full' => true,
        ],
        // 'other' => 'Название из конфига - Одномерный',
        'other' => [
            'title' => 'Название из конфига - Многомерный'
        ],
    ],

    'default' => [
        'cats' => 'certs_lic,objects,map',
        'cats_custom' => [
            ['name' => 'Item 1', 'url' => ''],
            ['name' => 'Item 2', 'url' => ''],
            ['name' => 'Item 3', 'url' => ''],
            ['name' => 'Item 4', 'url' => ''],
            ['name' => 'Item 5', 'url' => ''],
        ],
        // 'breadcrumbs' => []
    ],

];
