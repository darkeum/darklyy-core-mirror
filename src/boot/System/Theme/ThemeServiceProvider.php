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

use Boot\Support\Facades\View;
use Boot\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->replaceViewFinder();
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

    # Перезаписыватель View Finder - WIP
    protected function replaceViewFinder()
    {
        $active = config('theme.active');

        if (!$active) {
            return;
        }

        $finder = view()->getFinder();

        foreach ($finder->getHints() as $namespace => $paths) {

            if (!isset($paths[0])) {
                continue;
            }

            $themePath = $paths[0].DIRECTORY_SEPARATOR.$active;

            if (is_dir($themePath)) {
                view()->replaceNamespace($namespace, [
                    $themePath,
                    $paths[0]
                ]);
            }
        }
    }
}
