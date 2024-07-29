<?php

namespace App\Http\Controllers;

use App\Entities\ServiceMeta;
use Illuminate\Http\Request;

class ServiceMetaController extends Controller
{
    public function index()
    {
        $groups = ServiceMeta::all();
        return view('apps.servicemeta.list', compact('groups'));
    }

    public function create()
    {
        return view('apps.servicemeta.add');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'meta_id' => 'required|integer',
            'service_id' => 'required|integer',
            'seq' => 'required|integer',
            'meta_type_id' => 'required|integer',
            'meta_default' => 'nullable|string',
            'influx' => 'nullable|boolean',
        ]);

        ServiceMeta::create($validatedData);

        return redirect()->route('servicemeta')->with('success', 'Data added successfully');
    }

    public function edit($id)
    {
        $group = ServiceMeta::findOrFail($id);
        return view('apps.servicemeta.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
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

        return redirect()->route('servicemeta')->with('success', 'Data updated successfully');
    }

    public function destroy($id)
    {
        $group = ServiceMeta::findOrFail($id);
        $group->delete();

        return redirect()->route('servicemeta')->with('success', 'Data deleted successfully');
    }
}
