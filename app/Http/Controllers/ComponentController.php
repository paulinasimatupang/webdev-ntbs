<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Component;
use App\Entities\ComponentType;
use App\Entities\ComponentContentType;

class ComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Component::query();

        if ($request->has('search')) {
            $data->whereRaw('lower(name) like ?', ["%{$request->search}%"]);
        }

        if ($request->has('status')) {
            $data->where('status', $request->status);
        }

        $total = $data->count();

        if ($request->has('limit')) {
            $data->limit($request->get('limit'));

            if ($request->has('offset')) {
                $data->offset($request->get('offset'));
            }
        }

        if ($request->has('order_type') && $request->has('order_by')) {
            $orderType = $request->get('order_type') == 'asc' ? 'asc' : 'desc';
            $data->orderBy($request->get('order_by'), $orderType);
        }

        $data = $data->get();
        $comp_type = ComponentType::all();
        $comp_content_type = ComponentContentType::all();
        $user = session()->get('user');

        return view('apps.component.list')
            ->with('data', $data)
            ->with('comp_type', $comp_type)
            ->with('comp_cont_type', $comp_content_type)
            ->with('username', $user->username);
    }

    public function create(Request $request){
        $comp_type = ComponentType::query()->get();
        $comp_content_type = ComponentContentType::query()->get();
        return view('apps.component.add')
                ->with('comp_type', $comp_type)
                ->with('comp_content_type', $comp_content_type);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'comp_id' => 'required|string|max:5',
                'component_type_id' => 'required|string|max:2',
                'comp_content_type' => 'required|string|max:2',
                'visible' => 'required|boolean',
                'comp_lbl' => 'required|string|max:100',
                'comp_act' => 'nullable|string|max:100',
                'mandatory' => 'required|boolean',
                'disabled' => 'required|boolean',
                'min_length' => 'required|integer|max:4',
                'max_length' => 'required|integer\max:4',
                'comp_lbl_en' => 'required|string|max:100',
            ]);
            $component = new Component();
            $component->comp_id = $validatedData['comp_id'];
            $component->component_type_id = $validatedData['component_type_id'];
            $component->comp_content_type = $validatedData['comp_content_type'];
            $component->visible = $validatedData['visible'];
            $component->comp_lbl = $validatedData['comp_lbl'];
            $component->comp_act = $validatedData['comp_act'];
            $component->mandatory = $validatedData['mandatory'];
            $component->disabled = $validatedData['disabled'];
            $component->min_length = $validatedData['min_length'];
            $component->max_length = $validatedData['max_length'];
            $component->comp_lbl_en = $validatedData['comp_lbl_en'];
            $component->save();
            return Redirect::to('component')->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            return Redirect::to('component/create')->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $component = Component::findOrFail($id);
        $comp_type = ComponentType::all();
        $comp_content_type = ComponentContentType::all();
        return view('apps.screen.edit', compact('component', 'comp_type', 'comp_content_type'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'comp_id' => 'required|string|max:5',
                'component_type_id' => 'required|string|max:2',
                'comp_content_type' => 'required|string|max:2',
                'visible' => 'required|boolean',
                'comp_lbl' => 'required|string|max:100',
                'comp_act' => 'nullable|string|max:100',
                'mandatory' => 'required|boolean',
                'disabled' => 'required|boolean',
                'min_length' => 'required|integer|max:4',
                'max_length' => 'required|integer\max:4',
                'comp_lbl_en' => 'required|string|max:100',
            ]);
            $component = Component::findOrFail($id);
            $component->comp_id = $validatedData['comp_id'];
            $component->component_type_id = $validatedData['component_type_id'];
            $component->comp_content_type = $validatedData['comp_content_type'];
            $component->visible = $validatedData['visible'];
            $component->comp_lbl = $validatedData['comp_lbl'];
            $component->comp_act = $validatedData['comp_act'];
            $component->mandatory = $validatedData['mandatory'];
            $component->disabled = $validatedData['disabled'];
            $component->min_length = $validatedData['min_length'];
            $component->max_length = $validatedData['max_length'];
            $component->comp_lbl_en = $validatedData['comp_lbl_en'];
            $component->save();
            return Redirect::to('component')->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            return Redirect::route('component_edit', $id)
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $component = Component::findOrFail($id);
            $component->delete();
            return Redirect::to('component')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return Redirect::route('component')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
