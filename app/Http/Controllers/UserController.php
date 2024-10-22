<?php

namespace  App\Http\Controllers;
use DB;
use Redirect;
use App\Entities\User;
use Illuminate\Http\Request;
use  Spatie\Permission\Models\Role;
use App\Entities\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Mail\sendPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
        $jumlah_request = User::where('status', 0)
        ->whereHas('role', function($query) {
            $query->where('name', '!=', 'Agen')
                ->where('name', '!=', 'Super Admin');
        })
        ->count();
        return view('apps.user.menu', compact('jumlah_request'));
    }

    public function index()
    {
        $user = session()->get('user');
        if($user){
            $role_user = $user->role_id;
            $role = Role::find($role_user);
            
            if ($role && $role->name == 'Operator Pusat'||$role->name == 'Supervisor Pusat') {
                $users = User::with('role')->whereHas('role', function($query) {
                    $query->where('name', '!=', 'Agen')->where('name', '!=', 'Super Admin')
                    ->whereIn('status', [1, 2]);
                })->get();
            }
            else if ($role && $role->name == 'Super Admin') {
                $users = User::with('role')
                ->whereHas('role', function($query) {
                    $query->where('name', '!=', 'Agen')
                    ->whereIn('status', [1, 2]);
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
        $rules = [
            'username' => 'required|unique:users,username',
        ];

        $messages = [
            'username.unique' => 'Username harus unik, username yang anda masukkan sudah terdaftar.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->route('users.create')
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::find($request->role_id);
        $status = ($role && $role->name == 'Super Admin' ||  $role->name == 'Supervisor Pusat') ? 1 : 0;

        $user = User::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'username' => $request->username,
            'nrp' => $request->nrp,
            'no_hp' => $request->no_hp,
            'status' => $status,
            'branchid' => $request->branch_id,
            'created_at' => now()
        ]);

        if ($role && ($role->name == 'Super Admin' || $role->name == 'Supervisor Pusat')) {
            return $this->acceptUser($user->id);
        }

        return redirect()->route('users.menu')->with('success', 'User berhasil dibuat.');
    }

    public function acceptUser($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if (!$user) {
                throw new \Exception("User tidak ditemukan");
            }

            $role_id = $user->role_id;
            $role_name = Role::where('id', $role_id)->value('name'); 
            $password = implode('', array_merge(
                array_map(function() { return chr(mt_rand(65, 90)); }, range(1, 3)),
                array_map(function() { return chr(mt_rand(97, 122)); }, range(1, 3)),
                array_map(function() { return mt_rand(0, 9); }, range(1, 2))
            ));
            $password = str_shuffle($password);
            $passwordBcrypt = bcrypt($password);

            Log::info('PasswordGenerate: ' . $password);

            $user->password = $passwordBcrypt;
            $user->status = 1;
            $user->updated_at = now();
            $user->save();

            // $sender_email = env('MAIL_USERNAME');

            $pesan = '<p>Halo ' . htmlspecialchars($user->fullname) . ',</p>';
            $pesan .= '<p>Pendaftaran Anda telah kami setujui, Anda telah terdaftar sebagai ' . htmlspecialchars($role_name) . ' pada Portal NTBS.</p>';
            $pesan .= '<p>Berikut informasi Anda yang telah terdaftar sebagai ' . htmlspecialchars($role_name) . ':</p>';
            $pesan .= '<p>Username: ' . htmlspecialchars($user->username) . '</p>';
            $pesan .= '<p>Password: ' . htmlspecialchars($password) . '</p>';
            $pesan .= '<p>Gunakan Username dan Password di atas untuk mengakses halaman http://reportntbs.selada.id/.</p>';
            $pesan .= '<p>Salam Hangat,</p>';
            $pesan .= '<p><b>NTBS LAKUPANDAI</b></p>';

            $detail_message = [
                'sender' =>'administrator@selada.id',
                'subject' => '[NTBS LAKUPANDAI] Pendaftaran User Portal NTBS Berhasil',
                'isi' => $pesan
            ];

            Mail::to($user->email)->send(new sendPassword($detail_message));

            DB::commit();
            return redirect()->route('users.menu')->with('success', 'Permintaan Berhasil Diterima.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error : ' . $e->getMessage());
            return redirect()->route('users.detail', $id)->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            'fullname' => 'required',
            'role_id' => 'required', 
        ]);
        
        $user = User::findOrFail($id);
        $user->fullname = $request->fullname;
        $user->role_id = $request->role_id;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->save();
        
        $user->syncRoles([$request->role_id]);
        
        $role_id = $user->role_id;
        $role_name = Role::where('id', $role_id)->value('name');

        return redirect()->route('users.index')->with('success', 'Data user behasil di edit');
    }


    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
