<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Настройка бота Telegram
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN', 'YOUR BOT TOKEN HERE')
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройка бота Telegram
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'viber-bot-api' => [
        'token' => env('VIBER_BOT_TOKEN', 'YOUR BOT TOKEN HERE'),
        'name' => 'АТРИ-БОТ',
        'avatar' => config('app.url').'/files/avatars/bot.png',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Настройка бота Telegram
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */
    'github_webhook_secret' => env('GITHUB_WEBHOOK_SECRET'),

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_URL'),
    ],

    'yandex' => [
        'client_id'     => env('YANDEX_CLIENT_ID'),
        'client_secret' => env('YANDEX_CLIENT_SECRET'),
        'redirect'      => env('YANDEX_URL'),
    ],

    'vk' => [
        'client_id'     => env('VK_CLIENT_ID'),
        'client_secret' => env('VK_CLIENT_SECRET'),
        'redirect'      => env('VK_URL'),
    ],


];
