<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Settings;

use Boot\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
       
    }

    public function register()
    {
        $this->app->bind('settings',function(){
            return new SettingsManager();
        });

        // $this->app->alias(Settings::class, 'settings');
    }

    
}
