<?php

namespace  App\Http\Controllers;
use DB;
use Redirect;
use App\Entities\User;
use Illuminate\Http\Request;
use  Spatie\Permission\Models\Role;
use App\Entities\Branch;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:view user', ['only' => ['index']]);
    //     $this->middleware('permission:create user', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:update user', ['only' => ['update', 'edit']]);
    //     $this->middleware('permission:delete user', ['only' => ['destroy']]);
    // }

    public function menu(){
        return view('apps.user.menu');
    }

    public function index()
    {
        $user = session()->get('user');
        if($user){
            $role_user = $user->role_id;
            $role = Role::find($role_user);
            
            if ($role && $role->name == 'Operator Pusat'||$role->name == 'Supervisor Pusat') {
                $users = User::with('role')->whereHas('role', function($query) {
                    $query->where('name', '!=', 'Agen')->where('name', '!=', 'Super Admin');
                })->get();
            }
            else if ($role && $role->name == 'Super Admin') {
                $users = User::with('role')->whereHas('role', function($query) {
                    $query->where('name', '!=', 'Agen');
                })->get();
            }

        }

        foreach ($users as $user) {
            if ($user->status == 0) {
                $user->status_text = 'Pending';
            } else if($user->status == 1) {
                $user->status_text = 'Active';
            } else if($user->status == 2) {
                $user->status_text = 'Deactive';
            } else if($user->status == 3) {
                $user->status_text = 'Blocked';
            } else if($user->status == 4) {
                $user->status_text = 'Reject';
            }
        }
        
        return view('apps.user.index', ['users' => $users]);
    }

    public function request_list()
    {
        $users = User::with('role')
            ->where('status', 0)
            ->whereHas('role', function($query) {
                $query->where('name', '!=', 'Agen')
                    ->where('name', '!=', 'Super Admin');
            })
            ->get();

        return view('apps.user.list-request', ['users' => $users]);
    }

    public function detail_request($id)
    {
        $user = User::with(['role', 'branch'])->find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan.');
        }

        return view('apps.user.detail', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();
        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);

            if($role){
                if ($role && $role->name == 'Operator Pusat') {
                    $roles = Role::whereNotIn('name', ['Super Admin', 'Agen'])->get();
                }
                if ($role && $role->name == 'Super Admin') {
                    $roles = Role:: where('name', '!=', 'Agen')->get();
                }
            }
        }  

        $branch = Branch::all();

        return view('apps.user.create', compact('roles', 'branch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:20',
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);
        $status = ($role && $role->name == 'Super Admin' ||  $role->name == 'Supervisor Pusat') ? 1 : 0;

        $user = User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'status' => $status,
            'created_at' => now()
        ]);

        $user->syncRoles([$request->role_id]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function acceptUser($id)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', $id)->first();
            if (!$user) {
                throw new \Exception("User not found");
            }

            $user->status = 1;
            $user->save();

            DB::commit();
            return redirect()->route('users.menu')->with('success', 'Permintaan Berhasil Diterima.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error : ' . $e->getMessage());
            return Redirect::to('user')
                ->with('error', $e->getMessage());
        }
    }

    public function rejectUser($id)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', $id)->first();
            if (!$user) {
                throw new \Exception("User not found");
            }

            $user->status = 4;
            $user->save();

            DB::commit();
            return redirect()->route('users.menu')->with('success', 'Permintaan Berhasil Diterima.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error : ' . $e->getMessage());
            return Redirect::to('users')
                ->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('apps.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id', 
        ]);

        $user = User::findOrFail($id);
        $user->fullname = $request->fullname;
        $user->username = $request->username;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

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
