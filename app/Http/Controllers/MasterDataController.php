<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MasterDataController extends Controller
{


    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all(); // Get all permissions

        // Get the current user and their role
        $user = auth()->user();
        $role = $user->role; // Assuming user has a role

        if ($role) {
            // Get all permissions for the user's role
            $userPermissions = Permission::whereIn('id', function ($query) use ($role) {
                $query->select('permission_id')
                    ->from('role_has_permissions')
                    ->where('role_id', $role->id);
            })->get();

            // Group permissions by feature
            $featurePermissions = $userPermissions->groupBy('feature');

            // Check if any permission exists for each feature
            $permissionsByFeature = [];
            foreach ($permissions as $permission) {
                $feature = $permission->feature;
                if (!isset($permissionsByFeature[$feature])) {
                    $permissionsByFeature[$feature] = false;
                }
                if ($userPermissions->where('feature', $feature)->count() > 0) {
                    $permissionsByFeature[$feature] = true;
                }
            }
        } else {
            $permissionsByFeature = [];
        }

        return view('apps.masterdata.list', [
            'roles' => $roles,
            'permissions' => $permissions,
            'permissionsByFeature' => $permissionsByFeature, // Pass the feature permissions to the view
        ]);
    }

}
