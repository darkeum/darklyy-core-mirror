<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission\Middlewares;

use Closure;
use Boot\Support\Facades\Auth;
use Boot\System\Permission\Exceptions\UnauthorizedException;
use Boot\System\Permission\Guard;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        $authGuard = Auth::guard($guard);

        $user = $authGuard->user();

        // Для клиентов Passport, осуществляющих межмашинное взаимодействие
        if (! $user && $request->bearerToken() && config('permission.use_passport_client_credentials')) {
            $user = Guard::getPassportClient($guard);
        }

        if (! $user) {
            throw UnauthorizedException::notLoggedIn();
        }

        if (! method_exists($user, 'hasAnyPermission')) {
            throw UnauthorizedException::missingTraitHasRoles($user);
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        if (! $user->canAny($permissions)) {
            throw UnauthorizedException::forPermissions($permissions);
        }

        return $next($request);
    }

    /**
     * Укажите разрешение и защиту для промежуточного программного обеспечения.
     *
     * @param  array|string  $permission
     * @param  string|null  $guard
     * @return string
     */
    public static function using($permission, $guard = null)
    {
        $permissionString = is_string($permission) ? $permission : implode('|', $permission);
        $args = is_null($guard) ? $permissionString : "$permissionString,$guard";

        return static::class.':'.$args;
    }
}
