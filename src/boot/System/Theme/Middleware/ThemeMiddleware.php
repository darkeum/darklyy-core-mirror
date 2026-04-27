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
        if ($theme == 'auto') {

            $response = $next($request);

            // Проверяем, что это view-ответ
            if (method_exists($response, 'getOriginalContent') &&
                $response->original instanceof \Illuminate\View\View) {

                $active = config('theme.active');
                Theme::set($active, $parentTheme);

                $viewName = $response->original->getName();

                if ($active !== 'main') {
                    $viewName = str_replace('::', "::{$active}.", $response->original->getName());
                }
                //dd($viewName);

                $data = $response->original->getData();
                $response->setContent(view($viewName, $data));

                return $response;

            }
        }

        Theme::set($theme, $parentTheme);
        return $next($request);
    }
}
