<?php

namespace App\Http\Controllers;
use DB;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Pengaduan;
use App\Entities\Role;

class PengaduanController extends Controller
{
    public function menu() 
    {
        $jumlah_pending = Pengaduan::where('status', 0)->count();
        $jumlah_process = Pengaduan::where('status', 1)->count();

        return view('apps.pengaduan.menu', compact('jumlah_pending', 'jumlah_process'));
    }

    public function list_pending(Request $request)
    {
        $data = Pengaduan::select('*')
                ->whereIn('status', [0]);

        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);
            
            if ($role && $role->name == 'Customer Service Cabang') {
                $branch_id = $user->branchid;
                
                $data->whereHas('merchant', function($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                });
            }
        }

        if($request->has('search')){
            $data = $data->whereRaw('lower(name) like (?)',["%{$request->search}%"]);
        }

        $total = $data->count();
    
        if($request->has('limit')){
            $data->take($request->get('limit'));
            
            if($request->has('offset')){
            	$data->skip($request->get('offset'));
            }
        }

        if($request->has('order_type')){
            if($request->get('order_type') == 'asc'){
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'));
                }else{
                    $data->orderBy('request_time');
                }
            }else{
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'), 'desc');
                }else{
                    $data->orderBy('request_time', 'desc');
                }
            }
        }else{
            $data->orderBy('request_time', 'asc');
        }

        $data = $data->get();
        return view('apps.pengaduan.list-pending')
                ->with('data', $data)
                ->with('username', $user->username);
    }

    public function list_process(Request $request)
    {
        $data = Pengaduan::select('*')
                ->whereIn('status', [1]);

        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);
            
            if ($role && $role->name == 'Customer Service Cabang') {
                $branch_id = $user->branchid;
                
                $data->whereHas('merchant', function($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                });
            }
        }

        if($request->has('search')){
            $data = $data->whereRaw('lower(name) like (?)',["%{$request->search}%"]);
        }

        $total = $data->count();
    
        if($request->has('limit')){
            $data->take($request->get('limit'));
            
            if($request->has('offset')){
            	$data->skip($request->get('offset'));
            }
        }

        if($request->has('order_type')){
            if($request->get('order_type') == 'asc'){
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'));
                }else{
                    $data->orderBy('request_time');
                }
            }else{
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'), 'desc');
                }else{
                    $data->orderBy('request_time', 'desc');
                }
            }
        }else{
            $data->orderBy('request_time', 'asc');
        }

        $data = $data->get();
        return view('apps.pengaduan.list-process')
                ->with('data', $data)
                ->with('username', $user->username);
    }

    public function list_resolved(Request $request)
    {
        $data = Pengaduan::select('*')
                ->whereIn('status', [2]);

        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);
            
            if ($role && $role->name == 'Customer Service Cabang') {
                $branch_id = $user->branchid;
                
                $data->whereHas('merchant', function($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                });
            }
        }

        if($request->has('search')){
            $data = $data->whereRaw('lower(name) like (?)',["%{$request->search}%"]);
        }

        $total = $data->count();
    
        if($request->has('limit')){
            $data->take($request->get('limit'));
            
            if($request->has('offset')){
            	$data->skip($request->get('offset'));
            }
        }

        if($request->has('order_type')){
            if($request->get('order_type') == 'asc'){
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'));
                }else{
                    $data->orderBy('request_time');
                }
            }else{
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'), 'desc');
                }else{
                    $data->orderBy('request_time', 'desc');
                }
            }
        }else{
            $data->orderBy('request_time', 'asc');
        }

        $data = $data->get();
        return view('apps.pengaduan.list-resolved')
                ->with('data', $data)
                ->with('username', $user->username);
    }

    public function detail_pending($id)
    {
        $data = Pengaduan::with('merchant')->find($id);
        if($data){
            return view('apps.pengaduan.detail-pending')
                ->with('data', $data);
        } else {
            return Redirect::to('pengaduan_pending')
                ->with('error', 'Data not found');
        }
    }

    public function detail_process($id)
    {
        $data = Pengaduan::with('merchant')->find($id);
        if($data){
            return view('apps.pengaduan.detail-process')
                ->with('data', $data);
        } else {
            return Redirect::to('pengaduan_process')
                ->with('error', 'Data not found');
        }
    }

    public function detail_resolved($id)
    {
        $data = Pengaduan::with('merchant')->find($id);
        if($data){
            return view('apps.pengaduan.detail-resolved')
                ->with('data', $data);
        } else {
            return Redirect::to('pengaduan_resolved')
                ->with('error', 'Data not found');
        }
    }

    public function onProcessRequest($id)
    {
        DB::beginTransaction();
        try {
            $data = Pengaduan::where('id', $id)->first();
            if (!$data) {
                throw new \Exception("data not found");
            }

            $data->status = 1;
            $data->save();

            DB::commit();
            return redirect()->route('pengaduan')->with('success', 'Permintaan Berhasil Diterima.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error : ' . $e->getMessage());
            return Redirect::to('pengaduan')
                ->with('error', $e->getMessage());
        }
    }

    public function resolvedRequest($id)
    {
        DB::beginTransaction();
        try {
            $data = Pengaduan::where('id', $id)->first();
            if (!$data) {
                throw new \Exception("data not found");
            }

            $data->status = 2;
            $data->reply_time = now();
            $data->save();

            DB::commit();
            return redirect()->route('pengaduan')->with('success', 'Permintaan Berhasil Diterima.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error : ' . $e->getMessage());
            return Redirect::to('pengaduan')
                ->with('error', $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $pengaduan = new Pengaduan();
            $pengaduan->mid = $request->mid;
            $pengaduan->kategori = $request->kategori;
            $pengaduan->judul = $request->judul;
            $pengaduan->deskripsi = $request->deskripsi;
            $pengaduan->status = 0; 
            $pengaduan->request_time = now(); 
            $pengaduan->save();

            return response()->json([
                'message' => 'Pengaduan created successfully',
                'data' => $pengaduan
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create Pengaduan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}