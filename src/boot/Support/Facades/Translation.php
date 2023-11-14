<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Env Helper class
*/

namespace Boot\Support\Facades;

use \Boot\System\Translation\Translation as TranslationBase;

class Translation
{

    /**
     * Получить текущий URL без строки запроса
     *
     * @return string Метод возвращает текущий URL без строки запроса
     */
    public static function trans($value)
    {
        $trans = app('translation');
        return  $trans->trans($value);
    }

   
}
