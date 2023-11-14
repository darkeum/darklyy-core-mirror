<?php

namespace Boot\System\Core\Abstracts\Providers;

use Boot\System\Core\Loaders\AliasesLoaderTrait;
use Boot\System\Core\Loaders\ProvidersLoaderTrait;
use Boot\Support\ServiceProvider;

abstract class MainProvider extends ServiceProvider
{
    use ProvidersLoaderTrait;
    use AliasesLoaderTrait;

    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->loadServiceProviders();
        $this->loadAliases();
    }

    /**
     * Register anything in the container.
     */
    public function register(): void
    {
    }
}
