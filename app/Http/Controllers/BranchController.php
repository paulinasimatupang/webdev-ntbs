<?php

namespace App\Http\Controllers;
use DB;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Branch;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $data = Branch::select('*');

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
                $check = Branch::where('branch_code', $request->kode_cabang)
                                ->orWhere('branch_name',$request->nama_cabang)
                                ->first();
                if($check){
                    Redirect::to('cabang/create')
                                ->with('error', 'Kode atau Nama Branch Sudah Terdaftar');
                }
                
                $cabang = Branch::create([
                                'branch_code'          =>  $request->kode_cabang,
                                'branch_name'          => $request->nama_cabang,
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
