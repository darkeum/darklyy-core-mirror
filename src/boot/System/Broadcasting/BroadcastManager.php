<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2025 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Broadcasting;

use Boot\App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Contracts\Broadcasting\Factory as FactoryContract;
use Illuminate\Broadcasting\BroadcastManager as LaravelBaseBroadcastManager;

class BroadcastManager extends LaravelBaseBroadcastManager implements FactoryContract
{

    /**
     * Register the routes for handling broadcast channel authentication and sockets.
     *
     * @param  array|null  $attributes
     * @return void
     */
    public function routes(?array $attributes = null)
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        $attributes = $attributes ?: ['middleware' => ['web']];

        $this->app['router']->group($attributes, function ($router) {
            $router->match(
                ['get', 'post'],
                '/broadcasting/auth',
                '\\' . BroadcastController::class . '@authenticate'
            )->withoutMiddleware([VerifyCsrfToken::class]);
        });
    }

    /**
     * Register the routes for handling broadcast user authentication.
     *
     * @param  array|null  $attributes
     * @return void
     */
    public function userRoutes(?array $attributes = null)
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        $attributes = $attributes ?: ['middleware' => ['web']];

        $this->app['router']->group($attributes, function ($router) {
            $router->match(
                ['get', 'post'],
                '/broadcasting/user-auth',
                '\\' . BroadcastController::class . '@authenticateUser'
            )->withoutMiddleware([VerifyCsrfToken::class]);
        });
    }
}
