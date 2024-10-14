<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Entities\PersenFee;
use Exception;

class PersenFeeController extends Controller
{
    public function index()
    {
        try {
            $persenFees = PersenFee::all();
            return view('apps.persen_fee.list', compact('persenFees'));
        } catch (Exception $e) {
            return redirect()->route('persen_fee')
                ->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('apps.persen_fee.add');
    }
    
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'penerima' => 'required',
                'persentase' => 'required',
            ]); 
            
            $totalPersentase = PersenFee::sum('persentase');

            $sisaPersentase = 100 - $totalPersentase;
        
            if ($request->persentase > $sisaPersentase) {
                return redirect()->route('persen_fee_create')
                    ->with('error', 'Total persentase tidak boleh melebihi 100%. Persentase yang masih bisa diinput: ' . $sisaPersentase . '%.')
                    ->withInput();
            }            
        
            $persenFee = PersenFee::create($validatedData);
    
            DB::commit();
            return redirect()->route('persen_fee')->with('success', 'Persen Fee berhasil ditambahkan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('persen_fee_create')
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function edit($id)
    {
        try {
            $group = PersenFee::findOrFail($id);
            return view('apps.persen_fee.edit', compact('group'));
        } catch (Exception $e) {
            return redirect()->route('persen_fee')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'penerima' => 'required',
                'persentase' => 'required',
            ]);

            $group = PersenFee::findOrFail($id);

            $totalPersentase = PersenFee::where('id', '!=', $id)->sum('persentase');

            $sisaPersentase = 100 - $totalPersentase;

            if ($request->persentase > $sisaPersentase) {
                return redirect()->route('persen_fee_edit', ['id' => $id])
                    ->with('error', 'Total persentase tidak boleh melebihi 100%. Persentase yang masih bisa diinput: ' . $sisaPersentase . '%.')
                    ->withInput();
            }

            $group->update([
                'penerima' => $validatedData['penerima'],
                'persentase' => $validatedData['persentase'],
            ]);

            DB::commit();
            return redirect()->route('persen_fee')->with('success', 'Persen Fee berhasil diperbarui.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('persen_fee_edit', ['id' => $id])
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $group = PersenFee::findOrFail($id);
            $group->delete();
            DB::commit();
            return redirect()->route('persen_fee')->with('success', 'Persen Fee berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('persen_fee')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
