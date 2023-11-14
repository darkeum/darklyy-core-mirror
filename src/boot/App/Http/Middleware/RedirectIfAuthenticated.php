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

use Boot\System\Routing\RouteServiceProvider;
use Closure;
use Boot\System\Http\Request;
use Boot\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Обработать входящий запрос.
     *
     * @param  \Boot\System\Http\Request  $request
     * @param  \Closure(\Boot\System\Http\Request): (\Boot\System\Http\Response|\Boot\System\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Boot\System\Http\Response|\Boot\System\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }   
        return $next($request);
    }
}
