<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Permission\Exceptions;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException
{
    private $requiredRoles = [];

    private $requiredPermissions = [];

    public static function forRoles(array $roles): self
    {
        $message = 'У пользователя нет подходящих ролей.';

        if (config('permission.display_role_in_exception')) {
            $message .= ' Необходимые роли '.implode(', ', $roles);
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredRoles = $roles;

        return $exception;
    }

    public static function forPermissions(array $permissions): self
    {
        $message = 'У пользователя нет необходимых разрешений.';

        if (config('permission.display_permission_in_exception')) {
            $message .= ' Необходимые разрешения '.implode(', ', $permissions);
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $permissions;

        return $exception;
    }

    public static function forRolesOrPermissions(array $rolesOrPermissions): self
    {
        $message = 'У пользователя нет необходимых прав доступа.';

        if (config('permission.display_permission_in_exception') && config('permission.display_role_in_exception')) {
            $message .= ' Необходимые роли или разрешения '.implode(', ', $rolesOrPermissions);
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $rolesOrPermissions;

        return $exception;
    }

    public static function missingTraitHasRoles(Authorizable $user): self
    {
        $class = get_class($user);

        return new static(403, "Класс авторизации `{$class}` должен быть Boot\System\Permission\Traits\HasRoles.", null, []);
    }

    public static function notLoggedIn(): self
    {
        return new static(403, 'Пользователь не авторизован.', null, []);
    }

    public function getRequiredRoles(): array
    {
        return $this->requiredRoles;
    }

    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }
}
