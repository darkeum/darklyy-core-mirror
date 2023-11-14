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

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Boot\System\Http\Request;
use Boot\System\Http\RedirectResponse;
use Boot\System\Socialite\Contracts\Provider as ProviderContract;

/**
 * @method Boot\System\Socialite\Two\AbstractProvider setHost()
 * @method Boot\System\Socialite\Two\AbstractProvider setConfig()
 */

abstract class AbstractProvider implements ProviderContract
{
    /**
     * Экземпляр HTTP-запроса.
     *
     * @var \Boot\System\Http\Request
     */
    protected $request;

    /**
     * Экземпляр HTTP-клиента.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Идентификатор клиента.
     *
     * @var string
     */
    protected $clientId;

    /**
     * Секретный ключ клиента.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * URL-адрес перенаправления.
     *
     * @var string
     */
    protected $redirectUrl;

    /**
     * Пользовательские параметры, которые будут отправлены вместе с запросом.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Запрашиваемые области.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * Разделительный символ для запрошенных областей.
     *
     * @var string
     */
    protected $scopeSeparator = ',';

    /**
     * Тип кодировки в запросе.
     *
     * @var int Может быть либо PHP_QUERY_RFC3986, либо PHP_QUERY_RFC1738.
     */
    protected $encodingType = PHP_QUERY_RFC1738;

    /**
     * Указывает, следует ли использовать состояние сеанса.
     *
     * @var bool
     */
    protected $stateless = false;

    /**
     * Указывает, следует ли использовать PKCE.
     *
     * @var bool
     */
    protected $usesPKCE = false;

    /**
     * Пользовательские параметры конфигурации Guzzle.
     *
     * @var array
     */
    protected $guzzle = [];

    /**
     * Кэшированный экземпляр пользователя.
     *
     * @var \Boot\System\Socialite\Two\User|null
     */
    protected $user;

    /**
     * Создайте новый экземпляр поставщика.
     *
     * @param  \Boot\System\Http\Request  $request
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  string  $redirectUrl
     * @param  array  $guzzle
     * @return void
     */
    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl, $guzzle = [])
    {
        $this->guzzle = $guzzle;
        $this->request = $request;
        $this->clientId = $clientId;
        $this->redirectUrl = $redirectUrl;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Получение URL-адрес аутентификации для поставщика.
     *
     * @param  string  $state
     * @return string
     */
    abstract protected function getAuthUrl($state);

    /**
     * Получите URL-адрес маркера для поставщика.
     *
     * @return string
     */
    abstract protected function getTokenUrl();

    /**
     * Получите необработанного пользователя для данного токена доступа.
     *
     * @param  string  $token
     * @return array
     */
    abstract protected function getUserByToken($token);

    /**
     * Сопоставьте необработанный массив пользователей с экземпляром Socialite User.
     *
     * @param  array  $user
     * @return \Boot\System\Socialite\Two\User
     */
    abstract protected function mapUserToObject(array $user);

    /**
     * Перенаправить пользователя приложения на экран аутентификации провайдера.
     *
     * @return \Boot\System\Http\RedirectResponse
     */
    public function redirect()
    {
        $state = null;

        if ($this->usesState()) {
            $this->request->session()->put('state', $state = $this->getState());
        }

        if ($this->usesPKCE()) {
            $this->request->session()->put('code_verifier', $this->getCodeVerifier());
        }

        return new RedirectResponse($this->getAuthUrl($state));
    }

    /**
     * Создайте URL-адрес аутентификации для поставщика из заданного базового URL-адреса.
     *
     * @param  string  $url
     * @param  string  $state
     * @return string
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        return $url.'?'.http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);
    }

    /**
     * Получите параметры GET для запроса кода.
     *
     * @param  string|null  $state
     * @return array
     */
    protected function getCodeFields($state = null)
    {
        $fields = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            'response_type' => 'code',
        ];

        if ($this->usesState()) {
            $fields['state'] = $state;
        }

        if ($this->usesPKCE()) {
            $fields['code_challenge'] = $this->getCodeChallenge();
            $fields['code_challenge_method'] = $this->getCodeChallengeMethod();
        }

        return array_merge($fields, $this->parameters);
    }

    /**
     * Форматирование заданных областей.
     *
     * @param  array  $scopes
     * @param  string  $scopeSeparator
     * @return string
     */
    protected function formatScopes(array $scopes, $scopeSeparator)
    {
        return implode($scopeSeparator, $scopes);
    }
     
    /**
     * Получение пользователя
     *
     * @return void
     */
    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }

        $response = $this->getAccessTokenResponse($this->getCode());

        $this->user = $this->mapUserToObject($this->getUserByToken(
            $token = Arr::get($response, 'access_token')
        ));

        return $this->user->setToken($token)
                    ->setRefreshToken(Arr::get($response, 'refresh_token'))
                    ->setExpiresIn(Arr::get($response, 'expires_in'))
                    ->setApprovedScopes(explode($this->scopeSeparator, Arr::get($response, 'scope', '')));
    }

    /**
     * Получите экземпляр Social User из известного токена доступа.
     *
     * @param  string  $token
     * @return \Boot\System\Socialite\Two\User
     */
    public function userFromToken($token)
    {
        $user = $this->mapUserToObject($this->getUserByToken($token));

        return $user->setToken($token);
    }

    /**
     * Определите, имеет ли текущий запрос/сеанс несоответствующее «состояние».
     *
     * @return bool
     */
    protected function hasInvalidState()
    {
        if ($this->isStateless()) {
            return false;
        }

        $state = $this->request->session()->pull('state');

        return empty($state) || $this->request->input('state') !== $state;
    }

    /**
     * Получите ответ маркера доступа для данного кода.
     *
     * @param  string  $code
     * @return array
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            'form_params' => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Получите поля POST для запроса токена.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        $fields = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];

        if ($this->usesPKCE()) {
            $fields['code_verifier'] = $this->request->session()->pull('code_verifier');
        }

        return $fields;
    }

    /**
     * Получить код из запроса.
     *
     * @return string
     */
    protected function getCode()
    {
        return $this->request->input('code');
    }

    /**
     * Объединить области запрошенного доступа.
     *
     * @param  array|string  $scopes
     * @return $this
     */
    public function scopes($scopes)
    {
        $this->scopes = array_unique(array_merge($this->scopes, (array) $scopes));

        return $this;
    }

    /**
     * Установите области запрошенного доступа.
     *
     * @param  array|string  $scopes
     * @return $this
     */
    public function setScopes($scopes)
    {
        $this->scopes = array_unique((array) $scopes);

        return $this;
    }

    /**
     * Получить текущие области.
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Установите URL-адрес перенаправления.
     *
     * @param  string  $url
     * @return $this
     */
    public function redirectUrl($url)
    {
        $this->redirectUrl = $url;

        return $this;
    }

    /**
     * Получите экземпляр HTTP-клиента Guzzle.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client($this->guzzle);
        }

        return $this->httpClient;
    }

    /**
     * Установите экземпляр HTTP-клиента Guzzle.
     *
     * @param  \GuzzleHttp\Client  $client
     * @return $this
     */
    public function setHttpClient(Client $client)
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Установите экземпляр запроса.
     *
     * @param  \Boot\System\Http\Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Определите, работает ли поставщик с состоянием.
     *
     * @return bool
     */
    protected function usesState()
    {
        return ! $this->stateless;
    }

    /**
     * Определите, работает ли поставщик как апатрид.
     *
     * @return bool
     */
    protected function isStateless()
    {
        return $this->stateless;
    }

    /**
     * Указывает, что поставщик должен работать как апатрид.
     *
     * @return $this
     */
    public function stateless()
    {
        $this->stateless = true;

        return $this;
    }

    /**
     * Получите строку, используемую для состояния сеанса.
     *
     * @return string
     */
    protected function getState()
    {
        return Str::random(40);
    }

    /**
     * Определите, использует ли поставщик PKCE.
     *
     * @return bool
     */
    protected function usesPKCE()
    {
        return $this->usesPKCE;
    }

    /**
     * Включает PKCE для провайдера.
     *
     * @return $this
     */
    public function enablePKCE()
    {
        $this->usesPKCE = true;

        return $this;
    }

    /**
     * Генерирует случайную строку нужной длины для проверки кода PKCE.
     *
     * @return string
     */
    protected function getCodeVerifier()
    {
        return Str::random(96);
    }

    /**
     * Генерирует вызов кода PKCE на основе верификатора кода PKCE в сеансе.
     *
     * @return string
     */
    protected function getCodeChallenge()
    {
        $hashed = hash('sha256', $this->request->session()->get('code_verifier'), true);

        return rtrim(strtr(base64_encode($hashed), '+/', '-_'), '=');
    }

    /**
     * Возвращает хэш-метод, используемый для вычисления запроса кода PKCE.
     *
     * @return string
     */
    protected function getCodeChallengeMethod()
    {
        return 'S256';
    }

    /**
     * Задайте пользовательские параметры запроса.
     *
     * @param  array  $parameters
     * @return $this
     */
    public function with(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }
}
