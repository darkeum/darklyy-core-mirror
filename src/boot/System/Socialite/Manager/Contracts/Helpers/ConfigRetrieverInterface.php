<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\System\Socialite\Manager\Contracts\Helpers;

interface ConfigRetrieverInterface
{
    /**
     * @param  string  $providerName
     * @param  array  $additionalConfigKeys
     * @return \Boot\System\Socialite\Manager\Contracts\ConfigInterface
     */
    public function fromServices($providerName, array $additionalConfigKeys = []);
}
