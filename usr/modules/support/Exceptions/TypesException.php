<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Support\Exceptions;

use Boot\System\Http\Response;
use Boot\Abstracts\Exceptions\Exception;

class TypesException extends Exception
{
    public $code = Response::HTTP_BAD_REQUEST;
    public $message = 'Не найден указанный тип';
}