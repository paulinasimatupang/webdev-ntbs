<?php

namespace App\Http\Controllers;
use DB;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Cabang;

class CabangController extends Controller
{
    public function index(Request $request)
    {
        $data = Cabang::select('*');

        $total = $data->count();

        $data = $data->get();

        $user = session()->get('user');

        return view('apps.cabang.list')
                ->with('data', $data)
                ->with('username', $user->username);
    }

    public function create(Request $request){
        return view('apps.cabang.add');
    }

    public function store(Request $request){
        DB::beginTransaction();
            try {
                $check = Cabang::where('kode_cabang', $request->kode_cabang)
                                ->orWhere('nama_cabang',$request->nama_cabang)
                                ->first();
                if($check){
                    Redirect::to('cabang/create')
                                ->with('error', 'Kode atau Nama Cabang Sudah Terdaftar');
                }
                
                $cabang = Cabang::create([
                                'kode_cabang'          =>  $request->kode_cabang,
                                'nama_cabang'          => $request->nama_cabang,
                                'created_at'           => now()
                            ]);

                DB::commit();
                return Redirect::to('cabang')
                                ->with('message', 'Merchant created');
            } catch (Exception $e) {
                DB::rollBack();
                    return Redirect::to('cabang/create')
                                ->with('error', $e->getMessage())
                                ->withInput();
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                    return Redirect::to('cabang/create')
                                ->with('error', $e->getMessage())
                                ->withInput();
            }
    }
}
