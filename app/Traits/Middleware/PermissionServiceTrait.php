<?php

namespace App\Traits\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait PermissionServiceTrait
{
    /**
     * 
     * Applies middleware to check user permissions before allowing access to
     * specific routes. Users without the appropriate permissions will receive
     * a 403 Unauthorized response.
     * 
     */
    protected function permisssionService($request, $next, $permissionName)
    {
        $id = Auth::id();

        $user = User::findOrFail($id);

        // Check if user has permission to access products based on the route
        if ($request->routeIs("$permissionName.create") || $request->routeIs("$permissionName.store")) {
            return (!$user->hasPermission('create'));
        }

        if ($request->routeIs("$permissionName.index") || $request->routeIs("$permissionName.show")) {
            return (!$user->hasPermission('read'));
        }

        if ($request->routeIs("$permissionName.edit")) {
            return (!$user->hasPermission('edit'));
        }

        if ($request->routeIs("$permissionName.update")) {
            return (!$user->hasPermission('update'));
        }

        if ($request->routeIs("$permissionName.destroy")) {
            return (!$user->hasPermission('delete'));
        }

        return $next($request);
    }
}
