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

class WildcardPermissionNotProperlyFormatted extends InvalidArgumentException
{
    public static function create(string $permission)
    {
        return new static("Разрешение подстановочного знака `{$permission}` имеет неправильный формат.");
    }
}
