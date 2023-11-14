<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Abstracts\Providers;

use Boot\System\Core\Abstracts\Providers\MainProvider as AbstractMainProvider;

abstract class MainProvider extends AbstractMainProvider
{
    public function boot(): void
    {
        parent::boot();
    }

    public function register(): void
    {
        parent::register();
    }
}
