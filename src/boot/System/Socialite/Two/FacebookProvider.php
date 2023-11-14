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

class FacebookProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Базовый URL-адрес Facebook Graph.
     *
     * @var string
     */
    protected $graphUrl = 'https://graph.facebook.com';

    /**
     * Версия Graph API для запроса.
     *
     * @var string
     */
    protected $version = 'v3.3';

    /**
     * Запрашиваемые поля пользователя.
     *
     * @var array
     */
    protected $fields = ['name', 'email', 'gender', 'verified', 'link'];

    /**
     * Запрашиваемые области.
     *
     * @var array
     */
    protected $scopes = ['email'];

    /**
     * Отображение диалогового окна во всплывающем окне.
     *
     * @var bool
     */
    protected $popup = false;

    /**
     * Повторно запросите отклоненное разрешение.
     *
     * @var bool
     */
    protected $reRequest = false;

    /**
     * Маркер доступа, который последний раз использовался для получения пользователя.
     *
     * @var string|null
     */
    protected $lastToken;

    /**
     * Полупение URL авторизации
     *
     * @param  mixed $state
     * @return void
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.facebook.com/' . $this->version . '/dialog/oauth', $state);
    }

    /**
     * Полупение URL токена
     *
     * @param  mixed $state
     * @return void
     */
    protected function getTokenUrl()
    {
        return $this->graphUrl . '/' . $this->version . '/oauth/access_token';
    }


    /**
     * Получение токена из ответа
     *
     * @param  mixed $code
     * @return void
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'form_params' => $this->getTokenFields($code),
        ]);

        $data = json_decode($response->getBody(), true);

        return Arr::add($data, 'expires_in', Arr::pull($data, 'expires'));
    }

    /**
     * Полупение пользователя по токену
     *
     * @param  mixed $state
     * @return array
     */
    protected function getUserByToken($token)
    {
        $this->lastToken = $token;

        $params = [
            'access_token' => $token,
            'fields' => implode(',', $this->fields),
        ];

        if (!empty($this->clientSecret)) {
            $params['appsecret_proof'] = hash_hmac('sha256', $token, $this->clientSecret);
        }

        $response = $this->getHttpClient()->get($this->graphUrl . '/' . $this->version . '/me', [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => $params,
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
        $avatarUrl = $this->graphUrl . '/' . $this->version . '/' . $user['id'] . '/picture';

        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => null,
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $avatarUrl . '?type=normal',
            'avatar_original' => $avatarUrl . '?width=1920',
            'profileUrl' => $user['link'] ?? null,
        ]);
    }

        
    /**
     * Получение полей
     *
     * @param  mixed $state
     * @return void
     */
    protected function getCodeFields($state = null)
    {
        $fields = parent::getCodeFields($state);

        if ($this->popup) {
            $fields['display'] = 'popup';
        }

        if ($this->reRequest) {
            $fields['auth_type'] = 'rerequest';
        }

        return $fields;
    }

    /**
     * Установка пользовательских полей для запроса Facebook.
     *
     * @param  array  $fields
     * @return $this
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Использование диалогового окна для отображения.
     *
     * @return $this
     */
    public function asPopup()
    {
        $this->popup = true;

        return $this;
    }

    /**
     * Повторно запросить разрешения, которые ранее были отклонены.
     *
     * @return $this
     */
    public function reRequest()
    {
        $this->reRequest = true;

        return $this;
    }

    /**
     * Получите последний использованный токен доступа.
     *
     * @return string|null
     */
    public function lastToken()
    {
        return $this->lastToken;
    }

    /**
     * Укажите, какую версию графа следует использовать.
     *
     * @param  string  $version
     * @return $this
     */
    public function usingGraphVersion(string $version)
    {
        $this->version = $version;

        return $this;
    }
}
