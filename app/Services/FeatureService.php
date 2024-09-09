<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class FeatureService
{
    public function getFeatureItems()
    {
        $user = Auth::user();
        $role = $user->role;

        // Fetch user permissions
        $permissions = Permission::whereIn('id', function ($query) use ($role) {
            $query->select('permission_id')
                ->from('role_has_permissions')
                ->where('role_id', $role->id);
        })->get();

        // Group permissions by feature, not by feature group
        $features = $permissions->groupBy('feature')
            ->filter(function ($group) {
                // Check if any permission exists for the feature
                return $group->isNotEmpty();
            });

        return $features;
    }

    public function getUserAllowedRoutes()
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role) {
            // Get the list of route names allowed for the user's role
            $userPermissions = Permission::whereIn('id', function ($query) use ($role) {
                $query->select('permission_id')
                    ->from('role_has_permissions')
                    ->where('role_id', $role->id);
            })->pluck('route_name')->toArray();

            // Flatten route names if they are separated by " | "
            $routes_user = [];
            foreach ($userPermissions as $routeName) {
                $routes_user = array_merge($routes_user, explode(' | ', $routeName));
            }

            return $routes_user;
        }

        return [];
    }
}
