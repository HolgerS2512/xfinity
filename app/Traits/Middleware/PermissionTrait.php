<?php

namespace App\Traits\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait PermissionTrait
{
    /**
     * 
     * Applies middleware to check user permissions before allowing access to
     * specific routes. Users without the appropriate permissions will receive
     * a 403 Unauthorized response.
     * 
     */
    protected function permisssionService($request, $next)
    {
        $id = Auth::id();
        $user = User::findOrFail($id);

        // Check if user has permission to access products based on the route
        if ($request->routeIs("this->permission.create") || $request->routeIs("this->permission.store")) {
            if (!$user->hasPermission('create')) {

                return response()->json([
                    'status' => false,
                    'error' => __('auth.unauthenticated'),
                ], 403);
            }
        }

        if ($request->routeIs("this->permission.index") || $request->routeIs("this->permission.show")) {
            if (!$user->hasPermission('read')) {

                return response()->json([
                    'status' => false,
                    'error' => __('auth.unauthenticated'),
                ], 403);
            }
        }

        if ($request->routeIs("this->permission.edit")) {
            if (!$user->hasPermission('edit')) {

                return response()->json([
                    'status' => false,
                    'error' => __('auth.unauthenticated'),
                ], 403);
            }
        }

        if ($request->routeIs("this->permission.update")) {
            if (!$user->hasPermission('update')) {

                return response()->json([
                    'status' => false,
                    'error' => __('auth.unauthenticated'),
                ], 403);
            }
        }

        if ($request->routeIs("this->permission.destroy")) {
            if (!$user->hasPermission('delete')) {

                return response()->json([
                    'status' => false,
                    'error' => __('auth.unauthenticated'),
                ], 403);
            }
        }

        return $next($request);
    }
}
