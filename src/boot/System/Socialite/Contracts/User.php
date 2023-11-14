<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Contracts;

interface User
{
    /**
     * Получение уникальный идентификатор пользователя.
     *
     * @return string
     */
    public function getId();

    /**
     * Получить ник/имя для пользователя.
     *
     * @return string
     */
    public function getNickname();

    /**
     * Получить полное имя пользователя.
     *
     * @return string
     */
    public function getName();

    /**
     * Получить адрес электронной почты пользователя.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Получить URL-адрес аватара/изображения пользователя.
     *
     * @return string
     */
    public function getAvatar();
}
