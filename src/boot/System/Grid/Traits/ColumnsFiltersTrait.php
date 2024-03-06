<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Grid\Traits;

use Boot\Support\Str;
use Boot\Support\Facades\DB;
use Boot\System\Grid\Utils\Utils;

trait ColumnsFiltersTrait
{
    # Функция проверки
    public function check($type, $filters, $default = null)
    {
        return array_key_exists($type, $filters) ? $filters[$type] : $default;
    }

}
