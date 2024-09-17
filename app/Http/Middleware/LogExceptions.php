<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogExceptions
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            Log::error('Exception caught in middleware', [
                'message' => $e->getMessage(),
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
