<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace App\Providers;

use Boot\Abstracts\Providers\MainProvider;

class MainServiceProvider extends MainProvider
{
    public array $serviceProviders = [
        AdminThemeServiceProvider::class,
        AdminMenuServiceProvider::class
    ];

    public array $aliases = [
        // ...
    ];
}