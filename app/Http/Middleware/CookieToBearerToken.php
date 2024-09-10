<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CookieToBearerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasCookie('_abck')) {
            $token = $request->cookie('_abck');

            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return $next($request);
    }
}
