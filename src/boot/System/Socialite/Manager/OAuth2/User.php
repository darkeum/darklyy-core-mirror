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

use Boot\System\Socialite\Two\User as BaseUser;

class User extends BaseUser
{
    /**
     * Учетные данные пользователя.
     *
     * Например: access_token, refresh_token, и т.д.
     *
     * @var array
     */
    public $accessTokenResponseBody;

    /**
     * Установка учетных данных пользователя.
     *
     * Может включать такие вещи, как токен и токен обновления
     *
     * @param  array  $accessTokenResponseBody
     * @return $this
     */
    public function setAccessTokenResponseBody(array $accessTokenResponseBody)
    {
        $this->accessTokenResponseBody = $accessTokenResponseBody;

        return $this;
    }
}
