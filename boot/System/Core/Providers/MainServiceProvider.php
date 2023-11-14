<?php

namespace Boot\System\Core\Providers;

use App\Providers\MainServiceProvider as AppService;
use Boot\System\Core\Providers\RoutesProvider;
use Boot\System\Core\Abstracts\Providers\MainProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;

class MainServiceProvider extends MainProvider
{
    /**
     * Register any Service Providers on the Ship layer (including third party packages).
     */
    public array $serviceProviders = [
        RoutesProvider::class,
        AppService::class
    ];

    /**
     * Register any Alias on the Ship layer (including third party packages).
     */
    protected array $aliases = [];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**
         * Load the ide-helper service provider only in non production environments.
         */
        if ($this->app->isLocal()) {
            // $this->app->register(IdeHelperServiceProvider::class);
        }

        parent::register();
    }
}
