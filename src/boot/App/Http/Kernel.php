<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\App\Http;

class Kernel extends \Boot\System\Http\Kernel
{
    /**
     * Стек промежуточного программного обеспечения приложения.
     *
     * @var array
     */
    protected $middleware = [
        // \Boot\App\Http\Middleware\TrustHosts::class,
        \Boot\App\Http\Middleware\TrustProxies::class,
        \Boot\System\Http\Middleware\HandleCors::class,
        \Boot\System\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Boot\System\Http\Middleware\ValidatePostSize::class,
        \Boot\App\Http\Middleware\TrimStrings::class,
        \Boot\System\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Boot\App\Http\Middleware\AddSanctumTokenToHeaders::class,
    ];

    /**
     * Группы промежуточного слоя маршрута приложения.
     *
     * @var array
     */
    protected $middlewareGroups = [
          'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Boot\System\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // \Boot\App\Http\Middleware\VerifyCsrfToken::class,ы
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Boot\App\Http\Middleware\LocaleMiddleware::class,
            \Boot\App\Http\Middleware\AddSanctumTokenToHeaders::class,

        ],

        'api' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Boot\System\Session\Middleware\StartSession::class,
            \Boot\System\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Boot\App\Http\Middleware\AddSanctumTokenToHeaders::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

        ],
    ];

    /**
     * ПО промежуточного слоя маршрута приложения.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Boot\App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \Boot\App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \Boot\System\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Boot\System\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Boot\System\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        'abilities' => \Boot\System\Sanctum\Http\Middleware\CheckAbilities::class,
        'ability' => \Boot\System\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        'theme' => \Boot\System\Theme\Middleware\ThemeMiddleware::class,
    ];
}
