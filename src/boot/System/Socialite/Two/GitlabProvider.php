<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Two;

class GitlabProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Запрашиваемые области.
     *
     * @var array
     */
    protected $scopes = ['read_user'];

    /**
     * Разделитель для запрошенных областей.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Хост экземпляра Gitlab.
     *
     * @var string
     */
    protected $host = 'https://gitlab.com';

    /**
     * Установка хоста экземпляра Gitlab.
     *
     * @param  string|null  $host
     * @return $this
     */
    public function setHost($host)
    {
        if (!empty($host)) {
            $this->host = rtrim($host, '/');
        }

        return $this;
    }

    /**
     * Полупение URL авторизации
     *
     * @param  mixed $state
     * @return void
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->host . '/oauth/authorize', $state);
    }

    /**
     * Полупение URL токена
     *
     * @param  mixed $state
     * @return void
     */
    protected function getTokenUrl()
    {
        return $this->host . '/oauth/token';
    }

    /**
     * Полупение пользователя по токену
     *
     * @param  mixed $state
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->host . '/api/v3/user', [
            'query' => ['access_token' => $token],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Структуризация данных авторизованного пользователя
     *
     * @param  mixed $state
     * @return \Boot\System\Socialite\Manager\OAuth2\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['avatar_url'],
        ]);
    }
}
