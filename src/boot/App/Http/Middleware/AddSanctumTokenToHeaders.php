<?php

namespace Boot\App\Http\Middleware;

use Closure;
use Boot\System\Http\Request;

class AddSanctumTokenToHeaders
{
    public function handle(Request $request, Closure $next)
    {
       
        // If the URL contains a token parameter - attach it as the Authorization header
        if ($request->has('token') && !$request->headers->has('Authorization')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->token);
        }
        return $next($request);
    }
}