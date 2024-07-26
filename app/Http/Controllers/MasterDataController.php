<?php

namespace App\Http\Controllers;

use App\Entities\MasterData;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function index()
    {
        $groups = MasterData::all();
        return view('apps.masterdata.list', compact('groups'));
    }

    public function create()
    {
        return view('apps.masterdata.add');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);
    
        // Buat entitas baru tanpa menyetel ID secara manual
        MasterData::create($validatedData);
    
        return redirect()->route('masterdata')->with('success', 'Data added successfully');
    }
    

    public function edit($id)
    {
        $group = MasterData::findOrFail($id);
        return view('apps.masterdata.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $group = MasterData::findOrFail($id);
        $group->update($validatedData);

        return redirect()->route('masterdata')->with('success', 'Data updated successfully');
    }

    public function destroy($id)
    {
        $group = MasterData::findOrFail($id);
        $group->delete();

        return redirect()->route('masterdata')->with('success', 'Data deleted successfully');
    }
}
