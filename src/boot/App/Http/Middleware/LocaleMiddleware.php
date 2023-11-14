<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\App\Http\Middleware;

use Closure;

class LocaleMiddleware
{
    public function handle($request, Closure $next)
    {
        $availLocale = ['en' => 'en', 'ru' => 'ru'];

        if (session()->has('locale') && array_key_exists(session()->get('locale'), $availLocale)) {

            app()->setLocale(session()->get('locale'));
        }
        return $next($request);
    }
}
