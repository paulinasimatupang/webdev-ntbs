<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class CheckPermissionMiddleware
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
        $user = Auth::user();
        $role = $user->role; // Assuming this returns a Role instance

        if (!$role) {
            Log::warning('User dengan ID: ' . $user->id . ' tidak memiliki role.');
            abort(403, 'Tidak Memiliki Akses.');
        }

        // Fetch route_name and feature_group using role_id
        $permissions = Permission::whereIn('id', function ($query) use ($role) {
            $query->select('permission_id')
                ->from('role_has_permissions')
                ->where('role_id', $role->id);
        })->get(['route_name', 'feature_group']);

        // Log permission information
        Log::info('User ID: ' . $user->id . ' memiliki permissions: ' . $permissions->pluck('route_name')->implode(', '));

        // Create an array for allowed routes and parent features
        $routes_user = [];

        foreach ($permissions as $permission) {
            // Split route_name if it's stored with ' | '
            $routes = explode(' | ', $permission->route_name);
            $routes_user = array_merge($routes_user, $routes);

            // If feature_group exists, add it to the allowed routes
            if (!empty($permission->feature_group)) {
                $routes_user[] = $permission->feature_group;
            }
        }

        Log::info('User ID: ' . $user->id . ' memiliki routes: ' . implode(', ', $routes_user));

        // Get the current route being accessed
        $currentRoute = $request->route()->getName();

        Log::info('User ID: ' . $user->id . ' mencoba mengakses route: ' . $currentRoute);

        // Check if the current route or its parent feature is in the allowed routes
        if (!in_array($currentRoute, $routes_user)) {
            Log::warning('User ID: ' . $user->id . ' mencoba mengakses route: ' . $currentRoute . ' tanpa permission yang sesuai.');
            abort(403, 'Tidak Memiliki Akses.');
        }

        return $next($request);
    }
}
