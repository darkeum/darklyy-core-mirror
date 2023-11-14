<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Manager\Contracts\OAuth2;

use Boot\System\Socialite\Two\ProviderInterface as SocialiteOauth2ProviderInterface;
use Boot\System\Socialite\Manager\Contracts\ConfigInterface as Config;

interface ProviderInterface extends SocialiteOauth2ProviderInterface
{
    /**
     * @param  \Boot\System\Socialite\Manager\Contracts\ConfigInterface  $config
     * @return $this
     */
    public function setConfig(Config $config);
}
