<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Theme\Middleware;

use Closure;
use Boot\System\Http\Request;
use Boot\System\Theme\Theme;

class ThemeMiddleware
{
    /**
     * Обработчик входящего запроса
     *
     * @param  \Boot\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $theme, string $parentTheme = null)
    {
        Theme::set($theme, $parentTheme);

        return $next($request);
    }
}
