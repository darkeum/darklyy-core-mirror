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

use Boot\Support\Facades\View;
use Boot\System\Http\Request;
use Boot\System\Theme\Theme;
use Closure;

class ThemeMiddleware
{
    /**
     * Обработчик входящего запроса
     *
     * @param  \Boot\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $theme, string $parentTheme = null)
    {
        $response = $next($request);

        //if ($theme == 'auto') {
        //    $theme = config('theme.active');
        //}
        //
        //// Проверяем, что это view-ответ
        //if (method_exists($response, 'getOriginalContent') &&
        //    $response->original instanceof \Illuminate\View\View && $theme !== 'main') {
        //    $name = str_replace('::', "::{$theme}.", $response->original->getName());
        //    $response->setContent(view($name, $response->original->getData()));
        //}

        Theme::set($theme, $parentTheme);
        return $response;

    }
}
