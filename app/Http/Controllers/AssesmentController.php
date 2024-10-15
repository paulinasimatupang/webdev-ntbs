<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Assesment;
use Illuminate\Support\Facades\Validator;

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
        $messages = [
            'pertanyaan.required' => 'Pertanyaan harus diisi.',
            'pertanyaan.regex' => 'Pertanyaan harus mengandung huruf.',
        ];
        
        $rules = [
            'pertanyaan' => 'required|regex:/[a-zA-Z]/',
            'poin' => 'required',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return redirect()->route('assesment_create') 
                ->withErrors($validator)
                ->withInput();
        }

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
        
        $messages = [
            'pertanyaan.required' => 'Pertanyaan harus diisi.',
            'pertanyaan.regex' => 'Pertanyaan harus mengandung huruf.',
        ];
        
        $rules = [
            'pertanyaan' => 'required|regex:/[a-zA-Z]/',
            'poin' => 'required',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return redirect()->route('assesment_update', ['id' => $id]) 
                ->withErrors($validator)
                ->withInput();
        }

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
