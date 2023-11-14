<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Facades;

use Boot\Support\Facades\Facade;
use Boot\System\Socialite\Contracts\Factory;

/**
 * @method static \Boot\System\Socialite\Contracts\Provider driver(string $driver = null)
 *
 * @see \Boot\System\Socialite\SocialiteManager
 */
class Socialite extends Facade
{
    /**
     * Имя для регистрации фасада
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
