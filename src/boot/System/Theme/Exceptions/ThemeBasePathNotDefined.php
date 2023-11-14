<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Theme\Exceptions;

use Exception;

class ThemeBasePathNotDefined extends Exception
{
    public function __construct()
    {
        parent::__construct('Указанный путь не найден');
    }
}
