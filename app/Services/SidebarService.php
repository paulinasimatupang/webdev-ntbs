<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class SidebarService
{
    public function getSidebarItems()
    {
        $user = Auth::user();
        $role = $user->role;

        // Fetch user permissions
        $permissions = Permission::whereIn('id', function ($query) use ($role) {
            $query->select('permission_id')
                ->from('role_has_permissions')
                ->where('role_id', $role->id);
        })->get();

        // Filter permissions by parent feature
        $features = $permissions->groupBy('feature_group')
            ->filter(function ($group) {
                // Check if any permission exists for the group
                return $group->isNotEmpty();
            });

        return $features;
    }
}
