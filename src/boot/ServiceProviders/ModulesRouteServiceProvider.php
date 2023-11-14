<?php

namespace Boot\ServiceProviders;

use Boot\System\Cache\RateLimiting\Limit;
use Boot\System\Routing\RouteServiceProvider as ServiceProvider;
use Boot\System\Http\Request;
use Boot\Support\Facades\RateLimiter;
use Boot\Support\Facades\Route;
use Boot\App\Loaders\RoutesLoaderTrait;

class ModulesRouteServiceProvider extends ServiceProvider
{
    use RoutesLoaderTrait;

    /**
     * Путь к «домашнему» маршруту для вашего приложения.
     *
     * Это используется аутентификацией Laravel для перенаправления пользователей после входа в систему.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Определите привязки модели маршрута, фильтры шаблонов и т. д.
     *
     * @return void
     */
    public function boot()
    {   
        parent::boot();
    }  

    public function map(): void
    {
        $this->runRoutesAutoLoader();
    }
}
