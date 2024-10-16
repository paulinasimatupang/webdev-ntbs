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
                    Redirect::to('branch/create')
                                ->with('error', 'Kode atau Nama Branch Sudah Terdaftar');
                }
                
                $cabang = Branch::create([
                                'branch_code'          =>  $request->kode_cabang,
                                'branch_name'          => $request->nama_cabang,
                            ]);

                DB::commit();
                return Redirect::to('branch')
                                ->with('message', 'Merchant created');
            } catch (Exception $e) {
                DB::rollBack();
                    return Redirect::to('branch/create')
                                ->with('error', $e->getMessage())
                                ->withInput();
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                    return Redirect::to('branch/create')
                                ->with('error', $e->getMessage())
                                ->withInput();
            }
    }

    public function edit(Request $request, $id) {
        $branch = Branch::where('branch_id', '=', $id)->first();
    
        if (!$branch) {
            return redirect()->route('branch')->with('error', 'Cabang tidak ditemukan');
        }
    
        return view('apps.cabang.edit', ['branch' => $branch]);
    }

    public function update(Request $request, $id) {
        DB::beginTransaction();
        try {
            $cabang = Branch::where('branch_id', '=', $id)->firstOrFail();
    
            $cabang->update([
                'branch_code' => $request->kode_cabang,
                'branch_name' => $request->nama_cabang,
            ]);
    
            DB::commit();
            return Redirect::to('branch')
                            ->with('message', 'Cabang berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('branch/edit'.$id)
                            ->with('error', $e->getMessage())
                            ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('branch/edit'.$id)
                            ->with('error', $e->getMessage())
                            ->withInput();
        }
    }

    public function destroy($id)
    {
        $data = Branch::find($id);

        if (!$data) {
            return redirect()->route('branch')->with('error', 'Data tidak ditemukan');
        }
        $data->delete();
        return redirect()->route('branch')->with('success', 'Data cabang berhasil dihapus');
    }
}
