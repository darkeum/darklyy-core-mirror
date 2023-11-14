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
use Boot\System\Socialite\Two\InvalidStateException;
use RuntimeException;
use Boot\System\Socialite\Manager\OAuth2\AbstractProvider;
use Boot\System\Socialite\Manager\OAuth2\User;

class VkProvider extends AbstractProvider
{

    /**
     * Уникальный идентификатор провайдера
     */
    public const IDENTIFIER = 'VK';

    /**
     * Версия API
     */
    public const VERSION = '5.131';

    /**
     * Доступные поля
     *
     * @var array
     */
    protected $fields = ['id', 'email', 'first_name', 'last_name', 'screen_name', 'photo_200'];

    /**
     * Использовать состояние 
     *
     * @var array
     */
    protected $stateless = true;

    /**
     * Область видимости при запросе
     *
     * @var array
     */
    protected $scopes = ['email'];

    /**
     * Полупение URL авторизации
     *
     * @param  mixed $state
     * @return void
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://oauth.vk.com/authorize',
            $state
        );
    }

    /**
     * Полупение URL токена
     *
     * @param  mixed $state
     * @return void
     */
    protected function getTokenUrl()
    {
        return 'https://oauth.vk.com/access_token';
    }

    /**
     * Полупение пользователя по токену
     *
     * @param  mixed $state
     * @return array
     */
    protected function getUserByToken($token)
    {
        $formToken = [];

        if (is_array($token)) {
            $formToken['email'] = $token['email'] ?? null;

            $token = $token['access_token'];
        }

        $params = http_build_query([
            'access_token' => $token,
            'fields'       => implode(',', $this->fields),
            'lang'         => $this->getConfig('lang', 'en'),
            'v'            => self::VERSION,
        ]);

        $response = $this->getHttpClient()->get('https://api.vk.com/method/users.get?' . $params);

        $contents = (string) $response->getBody();

        $response = json_decode($contents, true);

        if (!is_array($response) || !isset($response['response'][0])) {
            throw new RuntimeException(sprintf(
                'Invalid JSON response from VK: %s',
                $contents
            ));
        }

        return array_merge($formToken, $response['response'][0]);
    }
    
    /**
     * Получение данных пользователя
     *
     * @return void
     */
    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException();
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $user = $this->mapUserToObject($this->getUserByToken($response));

        $this->credentialsResponseBody = $response;

        if ($user instanceof User) {
            $user->setAccessTokenResponseBody($this->credentialsResponseBody);
        }

        return $user->setToken($this->parseAccessToken($response))
            ->setExpiresIn($this->parseExpiresIn($response));
    }

    /**
     * Структуризация данных авторизованного пользователя
     *
     * @param  mixed $state
     * @return \Boot\System\Socialite\Manager\OAuth2\User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => Arr::get($user, 'id'),
            'nickname' => Arr::get($user, 'screen_name'),
            'name'     => trim(Arr::get($user, 'first_name') . ' ' . Arr::get($user, 'last_name')),
            'email'    => Arr::get($user, 'email'),
            'avatar'   => Arr::get($user, 'photo_200'),
        ]);
    }

    /**
     * Получение полей токена
     *
     * @param  mixed $code
     * @return void
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * Установка пользовательских полей из ВК.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Дополнительные ключи
     *
     * @return void
     */
    public static function additionalConfigKeys()
    {
        return ['lang'];
    }
}