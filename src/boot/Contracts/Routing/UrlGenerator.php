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

interface UrlGenerator
{
    /**
     * Получение текущего URL-адреса для запроса.
     *
     * @return string
     */
    public function current();

    /**
     * Получение URL-адреса предыдущего запроса.
     *
     * @param  mixed  $fallback
     * @return string
     */
    public function previous($fallback = false);

    /**
     * Генерация абсолютного URL-адреса по указанному пути.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = null);

    /**
     * Создание безопасного, абсолютного URL-адреса для указанного пути.
     *
     * @param  string  $path
     * @param  array  $parameters
     * @return string
     */
    public function secure($path, $parameters = []);

    /**
     * Генерация URL-адреса для актива приложения.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    public function asset($path, $secure = null);

    /**
     * Получение URL-адреса именованного маршрута.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function route($name, $parameters = [], $absolute = true);

    /**
     * Получение URL-адреса действия контроллера.
     *
     * @param  string|array  $action
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    public function action($action, $parameters = [], $absolute = true);

    /**
     * Получение пространства имен корневого контроллера.
     *
     * @return string
     */
    public function getRootControllerNamespace();

    /**
     * Установка пространства имен корневого контроллера.
     *
     * @param  string  $rootNamespace
     * @return $this
     */
    public function setRootControllerNamespace($rootNamespace);
}
