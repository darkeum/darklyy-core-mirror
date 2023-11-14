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
use GuzzleHttp\RequestOptions;

class YandexProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Уникальный идентификатор провайдера
     */
    public const IDENTIFIER = 'YANDEX';

    /**
     * Полупение URL авторизации
     *
     * @param  mixed $state
     * @return void
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://oauth.yandex.ru/authorize',
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
        return 'https://oauth.yandex.ru/token';
    }


    /**
     * Полупение пользователя по токену
     *
     * @param  mixed $state
     * @return void
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://login.yandex.ru/info?format=json',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }


    /**
     * Структуризация данных авторизованного пользователя
     *
     * @param  mixed $state
     * @return void
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['login'],
            'name'     => Arr::get($user, 'real_name'),
            'email'    => Arr::get($user, 'default_email'),
            'avatar'   => 'https://avatars.yandex.net/get-yapic/' . Arr::get($user, 'default_avatar_id') . '/islands-200',
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
}
