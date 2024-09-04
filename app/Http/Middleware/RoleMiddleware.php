<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param string $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $id = Auth::id();

        if (!$id) {

            return response()->json([
                'status' => false,
                'message' => __('auth.unauthenticated'),
            ], 403);
        }

        $user = User::findOrFail($id);

        // Check if the user is authenticated and has the required role
        if (!$user->hasRole($role)) {

            return response()->json([
                'status' => false,
                'message' => __('auth.unauthenticated'),
            ], 403);
        }

        return $next($request);
    }
}
