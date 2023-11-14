<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Manager;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Config implements Contracts\ConfigInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Конструктор конфигурации.
     *
     * @param  string  $key
     * @param  string  $secret
     * @param  string|callable  $callbackUri
     * @param  array  $additionalProviderConfig
     */
    public function __construct($key, $secret, $callbackUri, array $additionalProviderConfig = [])
    {
        $this->config = array_merge([
            'client_id'     => $key,
            'client_secret' => $secret,
            'redirect'      => $this->formatRedirectUri($callbackUri),
        ], $additionalProviderConfig);
    }

    /**
     * Форматирование URI обратного вызова, при необходимости разрешив относительный URI.
     *
     * @param  string  $callbackUri
     * @return string
     */
    protected function formatRedirectUri($callbackUri)
    {
        $redirect = value($callbackUri);

        return Str::startsWith($redirect, '/')
                    ? URL::to($redirect)
                    : $redirect;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->config;
    }
}
