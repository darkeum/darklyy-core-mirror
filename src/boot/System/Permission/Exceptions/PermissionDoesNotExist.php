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

class PermissionDoesNotExist extends InvalidArgumentException
{
    public static function create(string $permissionName, string $guardName = '')
    {
        return new static("Нет разрешения по имени `{$permissionName}` для защитника `{$guardName}`.");
    }

    public static function withId(int $permissionId, string $guardName = '')
    {
        return new static("Нет разрешения с идентификатором `{$permissionId}` для защитника `{$guardName}`.");
    }
}
