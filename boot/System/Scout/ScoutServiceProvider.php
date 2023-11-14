<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/


namespace Boot\System\Scout;

use Typesense\Client;
use Boot\System\Scout\Builder;
use Boot\Support\Facades\Config;
use Boot\Support\ServiceProvider;
use Boot\System\Scout\EngineManager;
use Boot\System\Scout\Console\FlushCommand;
use Boot\System\Scout\Console\IndexCommand;
use Boot\System\Scout\Console\ImportCommand;
use Boot\System\Scout\Console\DeleteIndexCommand;
use Boot\System\Scout\Engines\Typesense\Typesense;
use Boot\System\Scout\Console\DeleteAllIndexesCommand;
use Boot\System\Scout\Console\SyncIndexSettingsCommand;
use Boot\System\Scout\Engines\Typesense\Mixin\BuilderMixin;
use Boot\System\Scout\Engines\Typesense\Engines\TypesenseEngine;

class ScoutServiceProvider extends ServiceProvider
{
    /**
     * Регистрация службы
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EngineManager::class, function ($app) {
            return new EngineManager($app);
        });

        $this->app->singleton(Typesense::class, static function () {
            $client = new Client(Config::get('scout.typesense'));

            return new Typesense($client);
        });

        $this->app->alias(Typesense::class, 'typesense');
    }

    /**
     * Запуск службы
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FlushCommand::class,
                ImportCommand::class,
                IndexCommand::class,
                SyncIndexSettingsCommand::class,
                DeleteIndexCommand::class,
                DeleteAllIndexesCommand::class,
            ]);
        }
        $this->app[EngineManager::class]->extend('typesense', static function ($app) {
            $client = new Client(Config::get('scout.typesense'));

            return new TypesenseEngine(new Typesense($client));
        });

        $this->registerMacros();
    }

    /**
     * Регистрация макроса
     *
     * @throws \ReflectionException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function registerMacros(): void
    {
        Builder::mixin($this->app->make(BuilderMixin::class));
    }
}
