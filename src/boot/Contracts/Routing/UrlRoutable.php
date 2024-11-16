<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2024 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Contracts\Routing;

interface UrlRoutable
{
    /**
     * Получение значения ключа маршрута модели.
     *
     * @return mixed
     */
    public function getRouteKey();

    /**
     * Получение ключа маршрута для модели.
     *
     * @return string
     */
    public function getRouteKeyName();

    /**
     * Получение модели для связанного значения.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Boot\System\Database\Model\Model|null
     */
    public function resolveRouteBinding($value, $field = null);

    /**
     * Извлечение дочерней модели для связанного значения.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Boot\System\Database\Model\Model|null
     */
    public function resolveChildRouteBinding($childType, $value, $field);
}
