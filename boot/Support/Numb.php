<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Str Helper class
*/

namespace Boot\Support;


class Numb
{
    /**
     * Функция заменяет запятые на точки.
     */
    public static function commasReplace($value)
    {
        return str_replace(',', '.', $value);
    }

    /**
     * Функция заменяет точки на запятые.
     */
    public static function dotsReplace($value)
    {
        return str_replace('.', ',', $value);
    }

    /**
     * Функция заменяет точки на запятые.
     */
    public static function formatMoney($value, $currency = 'руб.', $currency_separator = ' ', $decimals = 2, $decimal_separator  = '.', $thousands_separator = ' ')
    {
        $value = static::prepareNumeric($value);
        $prepare = number_format($value, $decimals, $decimal_separator, $thousands_separator);
        if (!empty($currency)) {
            $prepare = $prepare . $currency_separator . $currency;
        }
        return $prepare;
    }


    /**
     * Подготовка числа.
     * Функция сначала меняет запятые на точки потом приводит к числу и если $dotsReplace = true то меняет обратно точки на запятые.
     */
    public static function prepareNumeric($value, $dotsReplace = false)
    {
        $value = static::commasReplace($value);
        $value =  floatval($value);
        if ($dotsReplace === true) {
            $value = static::dotsReplace($value);
        }
        return $value;
    }
}
