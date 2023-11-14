<?php

namespace Boot\System\Core\Abstracts\Providers;

use Boot\System\Core\Loaders\RoutesLoaderTrait;
use Boot\System\Routing\RouteServiceProvider;

abstract class RoutesProvider extends RouteServiceProvider
{
    use RoutesLoaderTrait;

    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->runRoutesAutoLoader();
    }
}
