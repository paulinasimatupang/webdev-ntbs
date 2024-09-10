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

    $permissions = Permission::whereIn('id', function ($query) use ($role) {
        $query->select('permission_id')
            ->from('role_has_permissions')
            ->where('role_id', $role->id);
    })->get();

    $features = [];
    
    foreach ($permissions as $permission) {
        if (strpos($permission->feature, ' | ') !== false) {
            [$mainFeature, $subFeature] = explode(' | ', $permission->feature);
            $features[$mainFeature][$subFeature] = $permission->feature;
        } else {
            $features[$permission->feature] = [];
        }
    }

    return $features;
}


    public function getUserAllowedRoutes()
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role) {
            $userPermissions = Permission::whereIn('id', function ($query) use ($role) {
                $query->select('permission_id')
                    ->from('role_has_permissions')
                    ->where('role_id', $role->id);
            })->pluck('route_name')->toArray();

            $routes_user = [];
            foreach ($userPermissions as $routeName) {
                $routes_user = array_merge($routes_user, explode(' | ', $routeName));
            }

            return $routes_user;
        }

        return [];
    }
}
