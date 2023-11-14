<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite;

use Illuminate\Support\Arr;
use Boot\Support\Str;
use InvalidArgumentException;
use Boot\Support\Manager;
use Boot\System\Socialite\Two\VkProvider;
use Boot\System\Socialite\Two\GithubProvider;
use Boot\System\Socialite\Two\GitlabProvider;
use Boot\System\Socialite\Two\GoogleProvider;
use Boot\System\Socialite\Two\YandexProvider;
use Boot\System\Socialite\One\TwitterProvider;
use Boot\System\Socialite\Two\FacebookProvider;
use Boot\System\Socialite\Two\LinkedInProvider;
use Boot\System\Socialite\Two\BitbucketProvider;
use League\OAuth1\Client\Server\Twitter as TwitterServer;
use Boot\System\Socialite\Two\TwitterProvider as TwitterOAuth2Provider;

class SocialiteManager extends Manager implements Contracts\Factory
{
    /**
     * Получение экземпляра драйвера.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createGithubDriver()
    {
        $config = $this->config->get('services.github');

        return $this->buildProvider(
            GithubProvider::class,
            $config
        );
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createYandexDriver()
    {
        $config = $this->config->get('services.yandex');

        return $this->buildProvider(
            YandexProvider::class,
            $config
        );
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createVkDriver()
    {
        $config = $this->config->get('services.vk');

        return $this->buildProvider(
            VkProvider::class,
            $config
        );
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createFacebookDriver()
    {
        $config = $this->config->get('services.facebook');

        return $this->buildProvider(
            FacebookProvider::class,
            $config
        );
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createGoogleDriver()
    {
        $config = $this->config->get('services.google');

        return $this->buildProvider(
            GoogleProvider::class,
            $config
        );
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createLinkedinDriver()
    {
        $config = $this->config->get('services.linkedin');

        return $this->buildProvider(
            LinkedInProvider::class,
            $config
        );
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createBitbucketDriver()
    {
        $config = $this->config->get('services.bitbucket');

        return $this->buildProvider(
            BitbucketProvider::class,
            $config
        );
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createGitlabDriver()
    {
        $config = $this->config->get('services.gitlab');

        return $this->buildProvider(
            GitlabProvider::class,
            $config
        )->setHost($config['host'] ?? null);
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createTwitterDriver()
    {
        $config = $this->config->get('services.twitter');

        if (($config['oauth'] ?? null) === 2) {
            return $this->createTwitterOAuth2Driver();
        }

        return new TwitterProvider(
            $this->container->make('request'),
            new TwitterServer($this->formatConfig($config))
        );
    }

    /**
     * Создание экземпляра указанного драйвера.
     *
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    protected function createTwitterOAuth2Driver()
    {
        $config = $this->config->get('services.twitter');

        return $this->buildProvider(
            TwitterOAuth2Provider::class,
            $config
        );
    }

    /**
     * Создание экземпляра поставщика OAuth 2.
     *
     * @param  string  $provider
     * @param  array  $config
     * 
     * @return \Boot\System\Socialite\Two\AbstractProvider
     */
    public function buildProvider($provider, $config)
    {
        return new $provider(
            $this->container->make('request'),
            $config['client_id'],
            $config['client_secret'],
            $this->formatRedirectUrl($config),
            Arr::get($config, 'guzzle', [])
        );
    }

    /**
     * Форматирование конфигурации сервера.
     *
     * @param  array  $config
     * @return array
     */
    public function formatConfig(array $config)
    {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => $this->formatRedirectUrl($config),
        ], $config);
    }

    /**
     * Форматирование URL-адрес обратного вызова, при необходимости разрешив относительный URI.
     *
     * @param  array  $config
     * @return string
     */
    protected function formatRedirectUrl(array $config)
    {
        $redirect = value($config['redirect']);

        return Str::startsWith($redirect, '/')
            ? $this->container->make('url')->to($redirect)
            : $redirect;
    }

    /**
     * Забыть обо всех разрешенных экземплярах драйверов.
     *
     * @return $this
     */
    public function forgetDrivers()
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Установка экземпляра контейнера, используемый менеджером.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->app = $container;
        $this->container = $container;

        return $this;
    }

    /**
     * Получение имя драйвера по умолчанию.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Socialite driver was specified.');
    }
}
