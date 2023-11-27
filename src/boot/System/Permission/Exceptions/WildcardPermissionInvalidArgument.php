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

class WildcardPermissionInvalidArgument extends InvalidArgumentException
{
    public static function create()
    {
        return new static('Разрешение с подстановочным знаком должно быть строкой, идентификатором разрешения или экземпляром разрешения.');
    }
}
