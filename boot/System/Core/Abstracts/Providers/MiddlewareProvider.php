<?php

namespace Boot\System\Core\Abstracts\Providers;

use Boot\System\Core\Loaders\MiddlewaresLoaderTrait;

abstract class MiddlewareProvider extends MainProvider
{
    use MiddlewaresLoaderTrait;

    protected array $middlewares = [];

    protected array $middlewareGroups = [];

    protected array $middlewarePriority = [];

    protected array $routeMiddleware = [];

    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->loadMiddlewares();
    }

    /**
     * Register anything in the container.
     */
    public function register(): void
    {
    }
}
