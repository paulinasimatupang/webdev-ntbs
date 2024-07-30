<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use DB;
use App\Entities\ServiceMeta;
use App\Entities\MetaType;
use App\Entities\Service;
use Exception;

class ServiceMetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $username = auth()->user()->username; // Atau cara lain untuk mendapatkan username
            $groups = ServiceMeta::all();
            return view('apps.servicemeta.list', compact('groups', 'username'));
        } catch (Exception $e) {
            return redirect()->route('servicemeta')
                ->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $meta_type = MetaType::query()->get();
        $service = Service::query()->get();
        return view('apps.servicemeta.add')
                ->with('meta_type', $meta_type)
                ->with('service', $service);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'meta_id' => 'required|integer',
                'service_id' => 'required|integer',
                'seq' => 'required|integer',
                'meta_type_id' => 'required|integer',
                'meta_default' => 'nullable|string',
                'influx' => 'nullable|boolean',
            ]);

            ServiceMeta::create($validatedData);
            DB::commit();
            return redirect()->route('servicemeta')->with('success', 'Data berhasil ditambahkan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('servicemetacreate')
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $group = ServiceMeta::findOrFail($id);
            $meta_type = MetaType::query()->get();
            $service = Service::query()->get();
            return view('apps.servicemeta.edit', compact('group', 'meta_type', 'service'));
        } catch (Exception $e) {
            return redirect()->route('servicemeta')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'meta_id' => 'required|integer',
                'service_id' => 'required|integer',
                'seq' => 'required|integer',
                'meta_type_id' => 'required|integer',
                'meta_default' => 'nullable|string',
                'influx' => 'nullable|boolean',
            ]);

            $group = ServiceMeta::findOrFail($id);
            $group->update($validatedData);
            DB::commit();
            return redirect()->route('servicemeta')->with('success', 'Data berhasil diperbarui.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('servicemeta_edit', $id)
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $group = ServiceMeta::findOrFail($id);
            $group->delete();
            DB::commit();
            return redirect()->route('servicemeta')->with('success', 'Data berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('servicemeta')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
