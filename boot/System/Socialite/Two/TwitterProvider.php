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

use Illuminate\Support\Arr;

class TwitterProvider extends AbstractProvider
{
    /**
     * Запрашиваемые области.
     *
     * @var array
     */
    protected $scopes = ['users.read', 'tweet.read'];

    /**
     * Указывает, следует ли использовать PKCE.
     *
     * @var bool
     */
    protected $usesPKCE = true;

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
    public function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://twitter.com/i/oauth2/authorize', $state);
    }

    /**
     * Полупение URL токена
     *
     * @param  mixed $state
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'https://api.twitter.com/2/oauth2/token';
    }

    /**
     * Полупение пользователя по токену
     *
     * @param  mixed $state
     * @return array
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.twitter.com/2/users/me', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'query' => ['user.fields' => 'profile_image_url'],
        ]);

        return Arr::get(json_decode($response->getBody(), true), 'data');
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
            'avatar' => $user['profile_image_url'],
        ]);
    }

    
    /**
     * Получить ответ токена доступа
     *
     * @param  mixed $code
     * @return void
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }
}
