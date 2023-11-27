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

use InvalidArgumentException;

class RoleDoesNotExist extends InvalidArgumentException
{
    public static function named(string $roleName, ?string $guardName)
    {
        return new static("Нет роли по имени `{$roleName}` для защитника `{$guardName}`.");
    }

    /**
     * @param  int|string  $roleId
     * @return static
     */
    public static function withId($roleId, ?string $guardName)
    {
        return new static("Нет роли по идентификатору `{$roleId}` для защитника `{$guardName}`.");
    }
}
