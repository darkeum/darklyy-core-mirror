<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Router facades class
*/

namespace Boot\Support\Facades;

use \Boot\Support\Facades\Route;
use \Boot\Support\Str;

/**
 * @deprecated Данный фасад считается устаревшим и скоро будет удален
 *             Используйте \Boot\Support\Facades\Route;
 */
class Router
{
    /**
     * Добавление префикса маршруту
     *
     * @deprecated  Данная функция считается устаревшей и скоро будет удалена
     *              Используйте \Boot\Support\Facades\Route::mount;     
     * @param  string $baseRoute 
     * @param  mix  $fn
     * @param  void  $name
     * @return mix
     */
    public static function mount($baseRoute, $fn, $name = null)
    {
        if (Str::substrCount($baseRoute, 'administration') > 0) {
            if (!empty($name)) {
                return Route::name($name)->prefix($baseRoute)->middleware(['auth', 'permission:portal access'])->group($fn);
            } else {
                return Route::prefix($baseRoute)->middleware(['auth', 'permission:portal access'])->group($fn);
            }
        } else {
            if (!empty($name)) {
                return Route::name($name)->prefix($baseRoute)->group($fn);
            }
            return Route::prefix($baseRoute)->group($fn);
        }
    }

    /**
     * Добавление маршрута с методом GET
     *
     * @deprecated  Данная функция считается устаревшей и скоро будет удалена
     *              Используйте \Boot\Support\Facades\Route::get;   
     * @param  string $pattern 
     * @param  array|string|callable|null  $fn
     * @return mix
     */
    public static function get($pattern, $fn)
    {
        return Route::get($pattern, $fn);
    }

    /**
     * Добавление маршрута с методом POST
     *
     * @deprecated  Данная функция считается устаревшей и скоро будет удалена
     *              Используйте \Boot\Support\Facades\Route::post;   
     * @param  string $pattern 
     * @param  array|string|callable|null  $fn
     * @return mix
     */
    public static function post($pattern, $fn)
    {
        return Route::post($pattern, $fn);
    }

    /**
     * Добавление маршрута на все методы
     *
     * @deprecated  Данная функция считается устаревшей и скоро будет удалена
     *              Используйте \Boot\Support\Facades\Route::any;   
     * @param  string $pattern 
     * @param  array|string|callable|null  $fn
     * @return mix
     */
    public static function all($pattern, $fn)
    {
        return Route::any($pattern, $fn);
    }

    /**
     * Добавление стандартных маршрутов
     *
     * @deprecated  Данная функция считается устаревшей и скоро будет удалена
     *              Используйте \Boot\Support\Facades\Route::resource;    
     * @param  string $pattern 
     * @param  array|string|callable|null  $fn
     * @return mix
     */
    public static function resource($pattern, $fn)
    {
        Route::match(['get', 'post'], $pattern . '/grid', [$fn, 'grid']);
        return Route::resource($pattern, $fn)->parameters([
            '' => 'id',
        ]);
    }

    /**
     * Получить имя текущего маршрута
     *
     * @deprecated  Данная функция считается устаревшей и скоро будет удалена
     *              Используйте \Boot\Support\Facades\Route::currentRouteName;    
     * @param  string $pattern 
     * @param  array|string|callable|null  $fn
     * @return mix
     */
    public static function getCurrentName()
    {
        return Route::currentRouteName();
    }




    public static function  before($methods, $pattern, $fn)
    {
    }



    public static function  run($callback = null)
    {
    }

    public static function getName($name, $params = null)
    {
        return  route($name, $params);
    }



    public static function dumpAlias()
    {
        $router = new RouterSystem();
        dd($router->alias);
    }

    public static function getAlias()
    {
        $router = new RouterSystem();
        return $router->alias;
    }

    public static function getRoutes()
    {
        $router = new RouterSystem();
        return $router->router;
    }

    public static function trigger404()
    {
        $router = new RouterSystem();
        return  $router->trigger404();
    }

    public static function set404($fn)
    {
        $router = new RouterSystem();
        return  $router->set404($fn);
    }

    public static function setBasePath($path = '/')
    {
        $router = new RouterSystem();
        return  $router->setBasePath($path);
    }

    public static function middleware($path = '/')
    {
        $router = new RouterSystem();
        return  $router->setBasePath($path);
    }
}
