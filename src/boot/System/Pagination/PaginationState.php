<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksander
* @description Paginator state class
*/

namespace Boot\System\Pagination;

use \Boot\System\Request;

class PaginationState
{
    /**
     * Назначение данных для работы с pagination
     *    
     * @return void
     */
    public static function resolveUsing($app)
    {
        Paginator::viewFactoryResolver(function () use ($app) {
            return $app['view'];
        });

        Paginator::currentPathResolver(function () use ($app) {
            return $app['request']->url();
        });

        Paginator::currentPageResolver(function ($pageName = 'page') use ($app) {
            $page = $app['request']->input($pageName);

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return (int) $page;
            }

            return 1;
        });

        Paginator::queryStringResolver(function () use ($app) {
            return $app['request']->query();
        });

        CursorPaginator::currentCursorResolver(function ($cursorName = 'cursor') use ($app) {
            return Cursor::fromEncoded($app['request']->input($cursorName));
        });
    }
}
