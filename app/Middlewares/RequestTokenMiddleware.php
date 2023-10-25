<?php

namespace App\Middlewares;


class RequestTokenMiddleware {
    public static function handle($request, $next)
    {
        return $next(!$request->getHeader('token'));
    }
    
    public static function terminate($response)
    {
        
    }
}