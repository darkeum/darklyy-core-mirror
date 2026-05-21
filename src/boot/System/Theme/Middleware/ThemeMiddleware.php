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

use Illuminate\Support\Facades\Log;
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
        if ($theme == 'auto') {
            $theme = config('theme.active');
        }

        if (!$parentTheme) {
            $parentTheme = config('theme.parent');
        }

        View::composer('*', function ($view) use ($theme) {

            $name = $view->getName();

            // Проверяем, содержит ли namespace уже название темы
            if (str_contains($name, "::{$theme}")) {
                return;
            }

            $name = str_replace('::', "::{$theme}.", $name);

            if (View::exists($name)) {
                $view->setPath(
                    View::make($name)->getPath()
                );
                //Log::info('View Composer обработал: ' . $name);
            }
        });

        Theme::set($theme, $parentTheme);
        return $next($request);
    }
}
