<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Control\Providers;

use Boot\Abstracts\Providers\MainProvider;

class MainServiceProvider extends MainProvider
{
    public array $serviceProviders = [
        ControlModelServiceProvider::class
    ];

    public array $aliases = [
        // ...
    ];
}