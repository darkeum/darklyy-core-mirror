<?php

return [

    /*
    |------------------------------------------------- -------------------------
    | Файловая система по умолчанию
    |------------------------------------------------- -------------------------
    |
    | Здесь вы можете указать диск файловой системы по умолчанию, который следует использовать
    | по каркасу. «Локальный» диск, а также различные облачные
    | Диски на основе доступны для вашего приложения.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |------------------------------------------------- -------------------------
    | Диски файловой системы
    |------------------------------------------------- -------------------------
    |
    | Здесь вы можете настроить столько «дисков» файловой системы, сколько пожелаете.
    | может даже настроить несколько дисков одного и того же драйвера. По умолчанию есть
    | были настроены для каждого драйвера в качестве примера необходимых опций.
    |
    | Поддерживаемые драйверы: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => ROOT,
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0700,
                ],
            ],
        ],

        'custom' => [
            'driver' => 'local',
            'root' => public_path(),
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0700,
                ],
            ],
        ],

        'files' => [
            'driver' => 'local',
            'root' => ROOT.'/files',
            'url' => env('APP_URL') . '/files',
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0644,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0755,
                ],
            ],
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],

        'ftp' => [
            'driver' => 'ftp',
            'host' => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),
            'port' => env('FTP_PORT', 21),
            'passive' => true,
            // 'ssl' => true,
            'timeout' => 30,
        ],

        'nas' => [
            'driver' => 'ftp',
            'host' => env('NAS_HOST'),
            'root' => 'WEB SERVER/AE',
            'username' => env('NAS_USERNAME'),
            'password' => env('NAS_PASSWORD'),
            'port' => (int)env('NAS_PORT', 2121),
            'passive' => true,
            // 'ssl' => true,
            'timeout' => 30,
        ],

    ],

    /*
    |------------------------------------------------- -------------------------
    | Символические ссылки
    |------------------------------------------------- -------------------------
    |
    | Здесь вы можете настроить символические ссылки, которые будут созданы, когда
    | Выполняется Darksander-команда `storage:link`. Ключи массива должны быть
    | расположение ссылок и значения должны быть их целями.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
