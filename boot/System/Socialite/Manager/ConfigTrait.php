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

use Illuminate\Support\Arr;
use Boot\System\Socialite\Manager\Contracts\ConfigInterface;

trait ConfigTrait
{

    /**
     * Массив с конфигурацией
     *
     * @var array
     */
    protected $config;

    /**
     * Установка конфигурации
     *
     * @param  \Boot\System\Socialite\Manager\Contracts\OAuth1\ProviderInterface|\Boot\System\Socialite\Manager\Contracts\OAuth2\ProviderInterface  $config
     * @return void
     */
    public function setConfig(ConfigInterface $config)
    {
        $config = $config->get();

        $this->config = $config;
        $this->clientId = $config['client_id'];
        $this->clientSecret = $config['client_secret'];
        $this->redirectUrl = $config['redirect'];

        return $this;
    }

    /**
     * Дополнительные ключи конфигурации
     *
     * @return void
     */
    public static function additionalConfigKeys()
    {
        return [];
    }

    /**
     * Получение конфигурации
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed|array
     */
    protected function getConfig($key = null, $default = null)
    {
        if (!empty($key) && empty($this->config[$key])) {
            return $default;
        }
        return $key ? Arr::get($this->config, $key, $default) : $this->config;
    }
}
