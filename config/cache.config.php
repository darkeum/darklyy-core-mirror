<?php

use Boot\Support\Str;

return [

    /*
    |------------------------------------------------- -------------------------
    | Хранилище кэша по умолчанию
    |------------------------------------------------- -------------------------
    |
    | Этот параметр управляет подключением к кэшу по умолчанию, которое используется во время
    | используя эту кэширующую библиотеку. Это соединение используется, когда другое
    | явно не указывается при выполнении данной функции кэширования.
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |------------------------------------------------- -------------------------
    | Хранилища кеша
    |------------------------------------------------- -------------------------
    |
    | Здесь вы можете определить все «хранилища» кеша для вашего приложения как
    | а также их водители. Вы даже можете определить несколько хранилищ для
    | тот же драйвер кеша для группировки типов элементов, хранящихся в ваших кешах.
    |
    | Поддерживаемые драйверы: "apc", "array", "database", "file",
    |         "memcached", "redis", "dynamodb", "octane", "null"
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => tmp_path('cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |------------------------------------------------- -------------------------
    | Префикс ключа кэша
    |------------------------------------------------- -------------------------
    |
    | При использовании хранилища на основе оперативной памяти, такого как APC или Memcached, может
    | быть другими приложениями, использующими тот же кеш. Итак, мы укажем
    | значение, чтобы получить префикс для всех наших ключей, чтобы мы могли избежать коллизий.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'jmy'), '_') . '_cache_'),

];
