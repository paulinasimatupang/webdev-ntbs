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
        $permissions = Permission::all();
        
        $groupedPermissions = $permissions->reduce(function ($result, $permission) {
            $parts = explode('|', $permission->feature);
            $mainFeature = trim($parts[0]);
            $subFeature = isset($parts[1]) ? trim($parts[1]) : null;
            
            if (!isset($result[$mainFeature])) {
                $result[$mainFeature] = [];
            }
            
            if ($subFeature) {
                if (!isset($result[$mainFeature][$subFeature])) {
                    $result[$mainFeature][$subFeature] = [];
                }
                $result[$mainFeature][$subFeature][] = $permission;
            } else {
                if (!isset($result[$mainFeature])) {
                    $result[$mainFeature] = [];
                }
                $result[$mainFeature][] = $permission;
            }
            
            return $result;
        }, []);
        
        $rolePermissions = $role->permissions->pluck('id')->all();

        return view('apps.role.add-permissions', [
            'role' => $role,
            'groupedPermissions' => $groupedPermissions,
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
