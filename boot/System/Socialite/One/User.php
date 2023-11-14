<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\One;

use Boot\System\Socialite\AbstractUser;

class User extends AbstractUser
{
    /**
     * Токен доступа пользователя.
     *
     * @var string
     */
    public $token;

    /**
     * Секрет токен доступа пользователя.
     *
     * @var string
     */
    public $tokenSecret;

    /**
     * Установка токена пользователя.
     *
     * @param  string  $token
     * @param  string  $tokenSecret
     * @return $this
     */
    public function setToken($token, $tokenSecret)
    {
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;

        return $this;
    }
}
