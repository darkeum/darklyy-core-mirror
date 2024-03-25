<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid;

use Boot\Support\Facades\Blade;
use Boot\Support\Facades\Route;
use Boot\System\Grid\GridFinder;
use Boot\Support\ServiceProvider;
use Boot\System\Grid\GridManager;
use Boot\System\Grid\GridTagCompiler;
use Boot\System\Filesystem\Filesystem;
use Boot\System\Grid\GridBladeDirectives;
use Boot\System\Grid\Controllers\GridAssets;
use Boot\System\Grid\Controllers\GridController;

class GridServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerViews();
        $this->registerLanguages();
        $this->registerConfigs();
        $this->registerBladeDirective();
    }

    /**
     * Регистрация сервиса
     *
     * @return void
     */
    public function register()
    {
        $this->registerGridSingleton();
        $this->registerGridAutoDiscovery();
    }

    /**
     * Регистрация View файлов
     *
     * @return void
     */
    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'grid');
    }

    /**
     * Регистрация Languages файлов
     *
     * @return void
     */
    public function registerLanguages()
    {
        $this->loadTranslationsFrom(__DIR__ . '/Resources/Languages', 'grid');
    }

    /**
     * Регистрация Configs файлов
     *
     * @return void
     */
    public function registerConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/Configs/grid.config.php', 'grid');
    }

    /**
     * Регистрация детектив Blade
     *
     * @return void
     */
    public function registerBladeDirective()
    {
        Blade::directive('grid', [GridBladeDirectives::class, 'grid']);

        if (method_exists($this->app['blade.compiler'], 'precompiler')) {
            $this->app['blade.compiler']->precompiler(function ($string) {
                return app(GridTagCompiler::class)->compile($string);
            });
        }
    }

    /**
     * Регистрация 
     *
     * @return void
     */
    protected function registerGridSingleton()
    {
        $this->app->singleton(GridManager::class);

        $this->app->alias(GridManager::class, 'grid');
    }

    /**
     * Поиск и регистрация компонентов Grid
     *
     * @return void
     */
    protected function registerGridAutoDiscovery()
    {
        $defaultManifestPath = app()->bootstrapPath('cache/grid-components.php');
        $this->app->singleton(GridFinder::class, function () use ($defaultManifestPath) {
            return new GridFinder(
                new Filesystem,
                config('grid.manifest_path') ?: $defaultManifestPath,
                GridParser::generatePathFromNamespace(
                    config('grid.class_namespace')
                )
            );
        });
    }

    /**
     * Регистрация маршрутов
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::get('grid/grid.js', [GridAssets::class, 'script'])->name('grid.source.js');
        Route::get('grid/grid.css', [GridAssets::class, 'style'])->name('grid.source.css');

        //Route::get('aGrid/aGrid.js', [GridAssets::class, 'assets']);
        //Route::get('aGrid/aGrid.css', [GridAssets::class, 'assets']);

        //Route::get('grid/grid.js', [GridAssets::class, 'source'])->name('grid.source.js');

        Route::group($this->getRouteGroup(), function ($router) {
            Route::any('grid/{id}', [GridController::class, 'index'])->name('grid.index');
        });

        // Модификаторы
        Route::get('grid/views/mods', [GridView::class, 'mods'])->name('grid.view.mods');
    }

    /**
     * Получение характеристик для маршрутов
     *
     * @return array
     */
    public function getRouteGroup(): array
    {
        return [
            'middleware' => ['web'],           
        ];
    }
}
