<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Theme;

use Boot\Support\ServiceProvider;
use Boot\System\Theme\Commands\MakeThemeCommand;

class ThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
       
    }

    public function register()
    {
        $this->registerThemeFinder();
    }
 

    protected function registerThemeFinder(): void
    {
        $this->app->singleton('theme.finder', function ($app) {
            $themeFinder = new ThemeViewFinder(
                $app['files'],
                $app['config']['view.paths']
            );

            $themeFinder->setHints(
                $this->app->make('view')->getFinder()->getHints()
            );

            return $themeFinder;
        });

        if (config('theme.active')) {
            $this->app->make('theme.finder')->setActiveTheme(config('theme.active'), config('theme.parent'));
        }
    }
}
