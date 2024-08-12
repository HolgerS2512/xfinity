<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class RemoveLaravelTokenCookie
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // If have `laravel_token` remove this
        $response->headers->setCookie(Cookie::create('laravel_token', null, -1, '/'));

        return $response;
    }
}