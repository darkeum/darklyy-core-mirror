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

class WildcardPermissionNotImplementsContract extends InvalidArgumentException
{
    public static function create()
    {
        return new static('Класс разрешений Wildcard должен реализовывать контракт Boot\System\Permission\Contracts\Wildcard.');
    }
}
