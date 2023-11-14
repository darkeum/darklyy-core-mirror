<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander
*/

namespace Calls\Providers;

use Boot\Abstracts\Providers\MainProvider;

class MainServiceProvider extends MainProvider
{
    public array $serviceProviders = [];

    public array $aliases = [];
}
