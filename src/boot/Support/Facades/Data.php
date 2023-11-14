<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Data standardization Helper class
*/

namespace Boot\Support\Facades;

use \Dadata\DadataClient;


class Data
{

    /**
     * Подключение клиента Dadata
     *
     * @return object    Возращает экземляр клиента Dadata
     */
    protected static function client()
    {
        $token = "ddeafb9ff43e79578243d8f031f6e9d8b047ca0e";
        $secret = "e03a8f8ceaa114f8d8e0e09af92a0f3b069f1d40";
        return new DadataClient($token, $secret);
    }

    /**
     * Получение стандартизованных данных
     *
     * @param  string   $name Тип данных
     * @param  string   $value Запрашиваемое значение
     * @return array    Возвращает стандартизированный массив с запрошенным значением
     */
    public static function clean($name, $value)
    {
        return static::client()->clean($name, $value);
    } 
    
    /**
     * Получение данных организации по ИНН
     *
     * @param  string   $name Тип данных
     * @param  string   $value Запрашиваемое значение
     * @return array    Возвращает стандартизированный массив с запрошенным значением
     */
    public static function findById($name, $value)
    {
        
        return static::client()->findById($name, $value);
    }

    /**
     * Стандартизация ФИО
     *
     * @param  string  $value ФИО
     * @return array   Возвращает стандартизированный массив с запрошенным ФИО
     */
    public static function name($value)
    {
        return static::clean('name', $value);
    }

    /**
     * Стандартизация телефона
     *
     * @param  string  $value Телефон
     * @return array   Возвращает стандартизированный массив с запрошенным телефоном
     */
    public static function phone($value)
    {
        return static::clean('phone', $value);
    }

    /**
     * Стандартизация адреса
     *
     * @param  string  $value Адрес
     * @return array   Возвращает стандартизированный массив с запрошенным адресом
     */
    public static function address($value)
    {
        return static::clean('address', $value);
    } 
    
    /**
     * Получение данных организации по ИНН
     *
     * @param  string  $value ИНН
     * @return array   Возвращает стандартизированный массив по запрошенному ИНН
     */
    public static function inn($value)
    {
        $response = static::findById('party', $value);
        return $response[0] ?? $response;
    }   
    
    /**
     * Получение данных банка по БИК/SWIFT/ИНН
     *
     * @param  string   $value БИК/SWIFT/ИНН банка
     * @return array    Возвращает стандартизированный массив по запрошенному БИК/SWIFT/ИНН
     */
    public static function bank($value)
    {
        $response = static::findById('bank', $value);
        return $response[0] ?? $response;
    }

   
}
