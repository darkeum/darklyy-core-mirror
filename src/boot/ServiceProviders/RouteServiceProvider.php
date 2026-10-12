<?php

namespace Boot\ServiceProviders;

use Boot\System\Http\Request;
use Boot\Support\Facades\Route;
use Boot\Support\Facades\Config;
use Boot\Support\Facades\RateLimiter;
use Boot\System\Cache\RateLimiting\Limit;
use Boot\System\Routing\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{


    /**
     * The controller namespace for the application.
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * The callback that should be used to load the application's routes.
     *
     * @var \Closure|null
     */
    protected $loadRoutesUsing;

    /**
     * The global callback that should be used to load the application's routes.
     *
     * @var \Closure|null
     */
    protected static $alwaysLoadRoutesUsing;

    /**
     * The callback that should be used to load the application's cached routes.
     *
     * @var \Closure|null
     */
    protected static $alwaysLoadCachedRoutesUsing;



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
        $this->configureRateLimiting();

        $this->routes(function () {

            Route::domain(Config::get('core.api.url'))
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Файл с редиректами для модулей которых еще нет
            Route::middleware('web')
                ->group(base_path('routes/redirect.php'));
        });

    }

    /**
     * Настройте ограничители скорости для приложения.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
