<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use DB;
use App\Entities\ServiceMeta;
use App\Entities\MetaType;
use App\Entities\Service;
use Exception;
use Illuminate\Support\Facades\Log;


class ServiceMetaController extends Controller
{
    public function index()
    {
        try {
            $username = auth()->user()->username;
            $groups = ServiceMeta::all();
            return view('apps.servicemeta.list', compact('groups', 'username'));
        } catch (Exception $e) {
            return redirect()->route('servicemeta')
                ->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $meta_type = MetaType::all();
        $service = Service::all();
        return view('apps.servicemeta.add', compact('meta_type', 'service'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'meta_id' => 'required',
                'service_id' => 'required',
                'seq' => 'required',
                'meta_type_id' => 'required',
                'meta_default' => 'nullable',
                'influx' => 'required',
            ]);


            ServiceMeta::create($validatedData);
            DB::commit();
            return redirect()->route('servicemeta')->with('success', 'Data berhasil ditambahkan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('servicemeta_create')
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $group = ServiceMeta::findOrFail($id);
            return view('apps.servicemeta.edit', compact('group'));
        } catch (Exception $e) {
            return redirect()->route('servicemeta')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'meta_id' => 'required|integer',
                'service_id' => 'required|string',
                'seq' => 'required|integer',
                'meta_type_id' => 'required|string',
                'meta_default' => 'nullable|string',
                'influx' => 'nullable|string',
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

    public function list_parameter()
    {
        try {
            $username = auth()->user()->username; 

            $groups = ServiceMeta::whereIn('meta_id', ['rek_penerima', 'buffer', 'fee'])
                ->whereIn('service_id', ['BPR001'])
                ->with('service') 
                ->orderBy('service_id')
                ->get();

                // Array untuk menyimpan meta_id yang sudah ditemukan
                $filteredGroups = [];
                $seenMetaIds = [];

                foreach ($groups as $group) {
                    // Gunakan kombinasi beberapa kolom untuk lebih akurat menyaring
                    $uniqueKey = $group->meta_id . '-' . $group->service_id;
        
                    if (!in_array($uniqueKey, $seenMetaIds)) {
                        $filteredGroups[] = $group; // Simpan hanya satu influx berdasarkan kombinasi unik
                        $seenMetaIds[] = $uniqueKey; // Tandai kombinasi unik sudah disimpan
                    }
                }
                $groups = $filteredGroups;
            return view('apps.masterdata.list-parameter', compact('groups', 'username'));
        } catch (Exception $e) {
            Log::error('Terjadi kesalahan saat memuat data.', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('list_parameter')
                ->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    public function edit_parameter($meta_id, $service_id, $seq, $influx)
    {
        try {
            $group = ServiceMeta::where([
                ['meta_id', $meta_id],
                ['service_id', $service_id],
                ['seq', $seq],
                ['influx', $influx]
            ])->firstOrFail();

            return view('apps.masterdata.edit-parameter', compact('group'));
        } catch (Exception $e) {
            return redirect()->route('list_parameter')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function update_parameter(Request $request, $meta_id, $service_id, $seq, $influx)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validate([
                'meta_default' => 'nullable|string',
            ]);

            $oldMetaDefault = DB::connection('pgsql_billiton')->table('service_meta')
                ->where('meta_id', '=', $meta_id)
                ->where('service_id', '=', $service_id)
                ->where('seq', '=', $seq)
                ->where('influx', '=', $influx)
                ->value('meta_default');

            if ($oldMetaDefault === null) {
                throw new Exception('Nilai meta_default tidak ditemukan.');
            }

            $updated = DB::connection('pgsql_billiton')->table('service_meta')
                ->where('meta_id', '=', $meta_id)
                ->where('service_id', '=', $service_id)
                ->where('seq', '=', $seq)
                ->where('influx', '=', $influx)
                ->update([
                    'meta_default' => $request->input('meta_default'),
                ]);

            if ($updated === 0) {
                throw new Exception('Tidak ada record yang diperbarui.');
            }

            DB::connection('pgsql_billiton')->table('service_meta')
                ->where('meta_id', '=', $meta_id)
                ->where('meta_default', '=', $oldMetaDefault)
                ->update([
                    'meta_default' => $request->input('meta_default'),
                ]);

            DB::commit();
            return redirect()->route('list_parameter')->with('success', 'Data berhasil diperbarui.');
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Update failed: ' . $e->getMessage(), [
                'meta_id' => $meta_id,
                'service_id' => $service_id,
                'seq' => $seq,
            ]);
            return redirect()->route('edit_parameter', ['meta_id' => $meta_id, 'service_id' => $service_id, 'seq' => $seq])
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function findServiceMeta(Request $request)
    {
        $request->validate([
            'service_id' => 'required|string',
        ]);
    
        $service_id = $request->input('service_id');
        $serviceMeta = ServiceMeta::where('service_id', $service_id)
                                  ->where('meta_default', '!=', '')
                                  ->get(['meta_id', 'meta_default']);
    
        if ($serviceMeta->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Service Meta with default meta not found',
                'data' => []
            ], 404);
        }
    
        $result = $serviceMeta->pluck('meta_default', 'meta_id');
    
        if ($result->has('nominal_debit') && $result->has('buffer') && $result->has('fee')) {
            $nominalDebit = (float) $result->get('nominal_debit');
            $buffer = (float) $result->get('buffer');
            $fee = (float) $result->get('fee');
            $totalTransfer = $nominalDebit + $buffer + $fee;
            $result->put('total_transfer', $totalTransfer);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Service Meta retrieved successfully',
            'service_meta' => $result
        ], 200);
    }    
}
