<?php

namespace Boot\ServiceProviders;

use Hashids\Hashids;
use Boot\Support\ServiceProvider;
use Vinkla\Hashids\HashidsFactory;
use Vinkla\Hashids\HashidsManager;
use Illuminate\Contracts\Container\Container;
use Boot\App\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class HashidsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->setupConfig();
    }

    protected function setupConfig(): void
    {
        // $source = realpath($raw = __DIR__ . '/../config/hashids.php') ?: $raw;

        // if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
        //     $this->publishes([$source => config_path('hashids.php')]);
        // } elseif ($this->app instanceof LumenApplication) {
        //     $this->app->configure('hashids');
        // }

        // $this->mergeConfigFrom($source, 'hashids');
    }

    public function register(): void
    {
        $this->registerFactory();
        $this->registerManager();
        $this->registerBindings();
    }

    protected function registerFactory(): void
    {
        $this->app->singleton('hashids.factory', function () {
            return new HashidsFactory();
        });

        $this->app->alias('hashids.factory', HashidsFactory::class);
    }

    protected function registerManager(): void
    {
        $this->app->singleton('hashids', function (Container $app) {
            $config = $app['config'];
            $factory = $app['hashids.factory'];

            return new HashidsManager($config, $factory);
        });

        $this->app->alias('hashids', HashidsManager::class);
    }

    protected function registerBindings(): void
    {
        $this->app->bind('hashids.connection', function (Container $app) {
            $manager = $app['hashids'];

            return $manager->connection();
        });

        $this->app->alias('hashids.connection', Hashids::class);
    }

    public function provides(): array
    {
        return [
            'hashids',
            'hashids.factory',
            'hashids.connection',
        ];
    }
}
