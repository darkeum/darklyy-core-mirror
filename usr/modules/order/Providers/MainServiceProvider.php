<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Order\Providers;

use Boot\Abstracts\Providers\MainProvider;

class MainServiceProvider extends MainProvider
{
    public array $serviceProviders = [
        OrderModelServiceProvider::class
    ];

    public array $aliases = [
        // ...
    ];
}