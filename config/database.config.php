<?php

use Boot\Support\Str;

return [

    /*
    |------------------------------------------------- -------------------------
    | Имя подключения к базе данных по умолчанию
    |------------------------------------------------- -------------------------
    |
    | Здесь вы можете указать, какое из подключений к базе данных ниже вы хотите
    | для использования в качестве соединения по умолчанию для всей работы с базой данных. Конечно
    | вы можете использовать много подключений одновременно, используя библиотеку базы данных.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |------------------------------------------------- -------------------------
    | Подключения к базе данных
    |------------------------------------------------- -------------------------
    |
    | Вот каждая настройка подключения к базе данных для вашего приложения.
    | Конечно, примеры настройки каждой платформы базы данных, которая
    | поддерживаемый Framework, показан ниже, чтобы упростить разработку.
    |
    |
    | Вся работа с базой данных в Framework выполняется с помощью средств PHP PDO.
    | поэтому убедитесь, что у вас есть драйвер для вашей конкретной базы данных
    | Choice, установленный на вашем компьютере перед началом разработки.
    |
    */

    'connections' => [

        'atrienergo' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('DB_PREFIX', ''),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
            'modes' => [
                //'ONLY_FULL_GROUP_BY', // Disable this to allow grouping by one column
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_AUTO_CREATE_USER',
                'NO_ENGINE_SUBSTITUTION'
            ],
        ],

        'atrienergo_backup' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE_BACKUP', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('DB_PREFIX', ''),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
            'modes' => [
                //'ONLY_FULL_GROUP_BY', // Disable this to allow grouping by one column
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_AUTO_CREATE_USER',
                'NO_ENGINE_SUBSTITUTION'
            ],
        ],

        'scada' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL_SCADA'),
            'host' => env('DB_HOST_SCADA', '127.0.0.1'),
            'port' => env('DB_PORT_SCADA', '5432'),
            'database' => env('DB_DATABASE_SCADA', 'forge'),
            'username' => env('DB_USERNAME_SCADA', 'forge'),
            'password' => env('DB_PASSWORD_SCADA', ''),
            'charset' => 'utf8',
            'prefix' => env('DB_PREFIX_SCADA', ''),
            'prefix_indexes' => true,
            'schema' => 'public',
            // 'sslmode' => 'prefer',         
        ],

        'AEWIN' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_AEWIN', '127.0.0.1'),
            'port' => env('DB_PORT_AEWIN', '3306'),
            'database' => env('DB_DATABASE_AEWIN', 'forge'),
            'username' => env('DB_USERNAME_AEWIN', 'forge'),
            'password' => env('DB_PASSWORD_AEWIN', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('DB_PREFIX_AEWIN', ''),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
            'modes' => [
                //'ONLY_FULL_GROUP_BY', // Disable this to allow grouping by one column
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_AUTO_CREATE_USER',
                'NO_ENGINE_SUBSTITUTION'
            ],
        ],

        'LOG' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_LOG', '127.0.0.1'),
            'port' => env('DB_PORT_LOG', '3306'),
            'database' => env('DB_DATABASE_LOG', 'forge'),
            'username' => env('DB_USERNAME_LOG', 'forge'),
            'password' => env('DB_PASSWORD_LOG', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('DB_PREFIX_LOG', ''),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
            'modes' => [
                //'ONLY_FULL_GROUP_BY', // Disable this to allow grouping by one column
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_AUTO_CREATE_USER',
                'NO_ENGINE_SUBSTITUTION'
            ],
        ],

        'atrienergo_price' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE_PRICE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('DB_PREFIX', ''),
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
            'modes' => [
                //'ONLY_FULL_GROUP_BY', // Disable this to allow grouping by one column
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_AUTO_CREATE_USER',
                'NO_ENGINE_SUBSTITUTION'
            ],
        ],

    ],

    /*
    |------------------------------------------------- -------------------------
    | Таблица репозитория миграции
    |------------------------------------------------- -------------------------
    |
    | В этой таблице отслеживаются все миграции, которые уже выполнялись для
    | ваше приложение. Используя эту информацию, мы можем определить, какой из
    | миграции на диске фактически не выполнялись в базе данных.
    |
    */

    'migrations' => 'migrations',

    /*
    |------------------------------------------------- -------------------------
    | Базы данных Redis
    |------------------------------------------------- -------------------------
    |
    | Redis — это быстрое и расширенное хранилище ключей с открытым исходным кодом, которое также
    | предоставляет более богатый набор команд, чем обычная система ключ-значение
    | такие как APC или Memcached. 
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],
];
