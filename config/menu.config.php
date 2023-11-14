<?php

use Boot\Support\Facades\Facade;

return [
    'ids' => ['407', '309', '310', '281', '296', '272', '313', '292', '294'],

    'cats' => [
        ['tag' => 'about',         'name' => 'О компании',             'url'   => '/'],
        ['tag' => 'news',          'name' => 'Новости',                'route' => 'news.index'],
        ['tag' => 'revs',          'name' => 'Отзывы клиентов',        'route' => 'reviews.index'],

        ['tag' => 'certs_lic',     'name' => 'Сертификаты и лицензии', 'url'   => 'certificates'],
        ['tag' => 'certs_serv',    'name' => 'Сертификаты',            'url'   => 'certificates/sertifikaty-servisa'],

        ['tag' => 'comps',         'name' => 'Рекламации',             'route' => 'service.complaints.index'],
        ['tag' => 'order',         'name' => 'Заявки на сервис',       'route' => 'service.order.index'],

        ['tag' => 'mounting',      'name' => 'Монтаж',                 'url'   => 'page/mounting'],
        ['tag' => 'servicing',     'name' => 'Обслуживание объектов',  'url'   => 'page/servicing'],
        ['tag' => 'objects',       'name' => 'Наши объекты',           'url'   => 'project/installation_objects'],
        ['tag' => 'map',           'name' => 'Карта объектов',         'url'   => 'project/map'],

        ['tag' => 'docs',          'name' => 'Документы',              'url'   => 'content/documents.html'],
        ['tag' => 'job',           'name' => 'Вакансии',               'url'   => 'job'],
        ['tag' => 'partners',      'name' => 'Наши партнеры',          'url'   => 'partner'],
        ['tag' => 'contacts',      'name' => 'Контакты',               'url'   => 'feedback'],

        // ['tag' => '#', 'name' => '#', 'url'   => '#'],
    ],

    'breadcrumbs' => [
        ['name' => 'Главная', 'url' => '/'],
        ['name' => 'О компании', 'url' => 'page/about'],
    ],

];