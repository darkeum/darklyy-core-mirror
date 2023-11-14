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

interface Factory
{
    /**
     * Получение поставщика OAuth.
     *
     * @param  string  $driver
     * @return \Boot\System\Socialite\Contracts\Provider
     */
    public function driver($driver = null);
}
