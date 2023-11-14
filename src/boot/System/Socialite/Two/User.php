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
     * Токен обновления, который можно обменять на новый токен доступа.
     *
     * @var string
     */
    public $refreshToken;

    /**
     * Количество секунд, в течение которых токен доступа действителен.
     *
     * @var int
     */
    public $expiresIn;

    /**
     * Области действия, авторизованные пользователем. Утвержденные области могут быть подмножеством запрошенных областей.
     *
     * @var array
     */
    public $approvedScopes;

    /**
     * Установите токен на пользователя.
     *
     * @param  string  $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Установите маркер обновления, необходимый для получения нового маркера доступа.
     *
     * @param  string  $refreshToken
     * @return $this
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * Установите количество секунд, в течение которых токен доступа действителен.
     *
     * @param  int  $expiresIn
     * @return $this
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * Задайте области, утвержденные пользователем во время проверки подлинности.
     *
     * @param  array  $approvedScopes
     * @return $this
     */
    public function setApprovedScopes($approvedScopes)
    {
        $this->approvedScopes = $approvedScopes;

        return $this;
    }
}
