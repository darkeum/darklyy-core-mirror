<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Support;

class Currencyable
{
    public $value;

    /**
     * Конструктор класса
     *
     * @param  array $value Массив данных с валютой
     * @return void
     */
    public function __construct(array $value)
    {
        $this->value = (array) $value;
    }

    /**
     * Получить имя валюты
     *
     * @return string
     */
    public function name()
    {
        return $this->value['name'];
    }

    /**
     * Получить трехбуквенное обозначение
     *
     * @return string
     */
    public function alpha3()
    {
        return $this->value['alpha3'];
    }

    /**
     * Получить код валюты
     *
     * @return string
     */
    public function numeric()
    {
        return $this->value['numeric'];
    }
    
    /**
     * Получить страны где используется валюта
     *
     * @return string
     */
    public function country()
    {
        return $this->value['country'];
    }

    /**
     * Получить символьной иконки валюты
     *
     * @return string
     */
    public function icon()
    {
        return $this->value['icon'] ?? '';
    }

    /**
     * Получить короткое наименование валюты
     *
     * @return string
     */
    public function short()
    {
        return $this->value['short'] ?? '';
    }

    /**
     * Получить количество цифр после десятичного разделителя
     *
     * @return string
     */
    public function exp()
    {
        return $this->value['exp'];
    }
}
