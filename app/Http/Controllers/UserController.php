<?php

namespace  App\Http\Controllers;

use App\Entities\User;
use Illuminate\Http\Request;
use  Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view user', ['only' => ['index']]);
        $this->middleware('permission:create user', ['only' => ['create','store']]);
        $this->middleware('permission:update user', ['only' => ['update','edit']]);
        $this->middleware('permission:delete user', ['only' => ['destroy']]);
    }

    public function index()
    {
        $users = User::all();
        return view('apps.user.index', ['users' => $users]);
    }

    public function create()
    {
        $roles = Role::all();
        return view('apps.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Validasi input yang diterima
        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:20',
            'role_id' => 'required|exists:roles,id', // Validasi role_id
        ]);
    
        // Membuat user baru dengan data yang diberikan
        $user = User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id, // Menyimpan role_id
        ]);
    
        // Redirect ke halaman users.index dengan pesan sukses
        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); // Pastikan data roles juga dikirim
        return view('apps.user.edit', compact('user', 'roles'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id', // Pastikan validasi role_id yang benar
        ]);
    
        $user = User::findOrFail($id);
        $user->fullname = $request->fullname;
        $user->username = $request->username; 
        $user->email = $request->email;
    
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->save();
    
        // Update user role with syncRoles
        $user->syncRoles([$request->role_id]);
    
        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }
    

    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}

