<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
// use App\Entities\Permission;

class PermissionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:view permission', ['only' => ['index']]);
    //     $this->middleware('permission:create permission', ['only' => ['create','store']]);
    //     $this->middleware('permission:update permission', ['only' => ['update','edit']]);
    //     $this->middleware('permission:delete permission', ['only' => ['destroy']]);
    // }

    public function index()
    {
        $permissions = Permission::get();
        return view('apps.permission.index', [
            'permissions' => $permissions]);
        
    }

    public function create()
    {
        return view('apps.permission.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name'
            ]
        ]);

        Permission::create([
            'name' => $request->name
        ]);

        return redirect()->route('permissions.index')->with('status', 'Permission Created Successfully');
    }

    public function edit(Permission $permission)
    {
        // return $permission;
        return view('apps.permission.edit', [
            'permission' => $permission
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name,'.$permission->id
            ]
        ]);

        $permission->update([
            'name' => $request->name
        ]);

        return redirect('permissions')->with('status','Permission Updated Successfully');
    }

    public function destroy($permissionId)
    {
        $permission = Permission::find($permissionId);
        $permission->delete();
        return redirect('permissions')->with('status','Permission Deleted Successfully');
    }
}