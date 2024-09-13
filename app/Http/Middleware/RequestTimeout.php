<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        set_time_limit(30);  // 30 seconds then timeout

        return $next($request);
    }
}
