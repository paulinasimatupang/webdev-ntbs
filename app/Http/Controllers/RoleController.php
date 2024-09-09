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
        return view('apps.role.list', ['roles' => $roles]);
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
        // Fetch all permissions and group them by parent feature and feature
        $permissions = Permission::all()->groupBy('feature_group')->map(function ($permissionsByParent) {
            return $permissionsByParent->groupBy('feature');
        });

        $rolePermissions = $role->permissions->pluck('id')->all();  // Permissions yang dimiliki role

        return view('apps.role.add-permissions', [
            'role' => $role,
            'permissionsGroupedByParent' => $permissions,
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
