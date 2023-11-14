<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Manager\OAuth2;

use Illuminate\Support\Arr;
use Boot\System\Socialite\Two\AbstractProvider as BaseProvider;
use Boot\System\Socialite\Two\InvalidStateException;
use Boot\System\Socialite\Manager\ConfigTrait;
use Boot\System\Socialite\Manager\Contracts\OAuth2\ProviderInterface;
use Boot\System\Socialite\Manager\SocialiteWasCalled;

abstract class AbstractProvider extends BaseProvider implements ProviderInterface
{
    use ConfigTrait;

    /**
     * @var array
     */
    protected $credentialsResponseBody;

    /**
     * Кэшированный экземпляр пользователя.
     *
     * @var \Boot\System\Socialite\Manager\OAuth2\User|null
     */
    protected $user;

    /**
     * @param  string  $providerName
     * @return string
     */
    public static function serviceContainerKey($providerName)
    {
        return SocialiteWasCalled::SERVICE_CONTAINER_PREFIX.$providerName;
    }

    /**
     * @return \Boot\System\Socialite\Manager\OAuth2\User
     *
     * @throws \Boot\System\Socialite\Two\InvalidStateException
     */
    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        if ($this->hasInvalidState()) {
            throw new InvalidStateException();
        }

        $response = $this->getAccessTokenResponse($this->getCode());
        $this->credentialsResponseBody = $response;

        $this->user = $this->mapUserToObject($this->getUserByToken(
            $token = $this->parseAccessToken($response)
        ));

        if ($this->user instanceof User) {
            $this->user->setAccessTokenResponseBody($this->credentialsResponseBody);
        }

        return $this->user->setToken($token)
                    ->setRefreshToken($this->parseRefreshToken($response))
                    ->setExpiresIn($this->parseExpiresIn($response));
    }

    /**
     * Получение токена доступа из тела ответа.
     *
     * @param  array  $body
     * @return string
     */
    protected function parseAccessToken($body)
    {
        return Arr::get($body, 'access_token');
    }

    /**
     * Получение токена обновления из тела ответа.
     *
     * @param  array  $body
     * @return string
     */
    protected function parseRefreshToken($body)
    {
        return Arr::get($body, 'refresh_token');
    }

    /**
     * Получение срока действия из тела ответа.
     *
     * @param  array  $body
     * @return string
     */
    protected function parseExpiresIn($body)
    {
        return Arr::get($body, 'expires_in');
    }
}
