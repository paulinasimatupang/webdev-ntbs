<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:view role', ['only' => ['index']]);
    //     $this->middleware('permission:create role', ['only' => ['create','store','addPermissionToRole','givePermissionToRole']]);
    //     $this->middleware('permission:update role', ['only' => ['update','edit']]);
    //     $this->middleware('permission:delete role', ['only' => ['destroy']]);
    // }

    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all(); // Get all permissions

        // Get the current user and their permissions
        $user = auth()->user();
        $role = $user->role; // Assuming user has a role

        if ($role) {
            $userPermissions = Permission::whereIn('id', function ($query) use ($role) {
                $query->select('permission_id')
                    ->from('role_has_permissions')
                    ->where('role_id', $role->id);
            })->pluck('route_name')->toArray();

            // Process route names to get the list of user permissions
            $routes_user = [];
            foreach ($userPermissions as $routeName) {
                $routes_user = array_merge($routes_user, explode(' | ', $routeName));
            }
        } else {
            $routes_user = [];
        }

        return view('apps.role.list', [
            'roles' => $roles,
            'permissions' => $permissions,
            'routes_user' => $routes_user, // Pass the user routes to the view
        ]);
    }

    public function create()
    {
        return view('apps.role.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name'
            ]
        ]);

        Role::create([
            'name' => $request->name
        ]);

        return redirect()->route('roles.list')->with('status', 'Role Created Successfully');
    }

    public function edit(Role $role)
    {
        return view('apps.role.edit', [
            'role' => $role
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name,' . $role->id
            ]
        ]);

        $role->update([
            'name' => $request->name
        ]);

        return redirect()->route('roles.list')->with('status', 'Role Updated Successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.list')->with('status', 'Role Deleted Successfully');
    }


    public function addPermissionToRole(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->all();  // Simplified

        return view('apps.role.add-permissions', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function givePermissionToRole(Request $request, Role $role)
    {
        $request->validate([
            'permission' => 'required|array'
        ]);

        $role->syncPermissions($request->permission);

        return redirect()->back()->with('status', 'Permissions added to role');
    }
}
