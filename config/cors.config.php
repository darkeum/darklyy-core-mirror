<?php

return [

    /*
    |------------------------------------------------- -------------------------
    | Конфигурация общего доступа к ресурсам между источниками (CORS)
    |------------------------------------------------- -------------------------
    |
    | Здесь вы можете настроить параметры для обмена ресурсами между источниками.
    | или "CORS". Это определяет, какие кросс-происхождение операции могут выполняться
    | в веб-браузерах. Вы можете изменить эти настройки по мере необходимости.
    |
    | Чтобы узнать больше: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS.
    |
    */

    'paths' => ['https://api.atri-energo.ru/*', 'https://api.ae.ru/*', 'http://api.localhost/*', 'https://xn--80aqu.xn--h1andf.xn--p1acf/*','sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
