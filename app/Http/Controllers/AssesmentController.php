<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Assesment;

class AssesmentController extends Controller
{
    public function index(Request $request)
    {
        $data = Assesment::select('*');
        $data = $data->get();

        return view('apps.assesment.list')
                ->with('data', $data);
    }

    public function create()
    {
        return view('apps.assesment.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'poin' => 'required',
        ]);

        Assesment::create([
            'pertanyaan' => $request->pertanyaan,
            'poin' => $request->poin,
        ]);

        return redirect()->route('assesment')->with('success', 'Pertanyaan Assesment berhasil ditambahkan');
    }


    public function edit($id)
    {
        $data = Assesment::find($id);

        if (!$data) {
            return redirect()->route('assesment')->with('error', 'Data tidak ditemukan');
        }

        return view('apps.assesment.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'poin' => 'required',
        ]);

        $data = Assesment::find($id);

        if (!$data) {
            return redirect()->route('assesment')->with('error', 'Data tidak ditemukan');
        }

        $data->update([
            'pertanyaan' => $request->pertanyaan,
            'poin' => $request->poin,
        ]);

        return redirect()->route('assesment')->with('success', 'Pertanyaan Assesment berhasil diupdate');
    }

    public function destroy($id)
    {
        $data = Assesment::find($id);

        if (!$data) {
            return redirect()->route('assesment')->with('error', 'Data tidak ditemukan');
        }
        $data->delete();
        return redirect()->route('assesment')->with('success', 'Pertanyaan Assesment berhasil dihapus');
    }
}
