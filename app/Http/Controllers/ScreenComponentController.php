<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Redirect;
use DB;
use App\Http\Controllers\Controller;
use App\Entities\ScreenComponent;
use App\Entities\Screen;
use App\Entities\Component;

class ScreenComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = ScreenComponent::query();

        if ($request->has('search')) {
            $data->whereRaw('lower(comp_id) like ?', ["%{$request->search}%"]);
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
        $screens = Screen::all();
        $components = Component::all();

        $user = session()->get('user');

        return view('apps.screencomponent.list')
            ->with('data' , $data)
            ->with('screens', $screens)
            ->with('components', $components)
            ->with('username', $user->username);
    }

    public function create(Request $request)
    {
        $screens = Screen::all();
        $components = Component::all();
        return view('apps.screencomponent.add')
                ->with('screens', $screens)
                ->with('components', $components);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'screen_id' => 'required|string|max:7',
                'comp_id' => 'required|string|max:10',
                'sequence' => 'required|integer',
            ]);

            $screenComponent = new ScreenComponent();
            $screenComponent->screen_id = $validatedData['screen_id'];
            $screenComponent->comp_id = $validatedData['comp_id'];
            $screenComponent->sequence = $validatedData['sequence'];
            $screenComponent->save();

            return redirect()->route('screen_component')->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->route('screen_component_create')->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $screenComponent = ScreenComponent::findOrFail($id);
        $screens = Screen::all();
        $components = Component::all();

        return view('apps.screencomponent.edit', compact('screenComponent', 'screens', 'components'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'screen_id' => 'required|string|max:7',
                'comp_id' => 'required|string|max:10',
                'sequence' => 'required|integer',
            ]);
            $screenComponent = ScreenComponent::where('screen_id', $validatedData['screen_id'])
                ->where('comp_id', $validatedData['comp_id'])
                ->first();

            if ($screenComponent) {
                $screenComponent->sequence = $validatedData['sequence'];
                $screenComponent->save();
                
                return redirect()->route('screen_component')->with('success', 'Data berhasil diperbarui.');
            } else {
                return redirect()->route('screen_component_edit', $id)
                    ->with('error', 'Data tidak ditemukan.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->route('screen_component_edit', $id)
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }    

    public function destroy($id)
    {
        try {
            $screenComponent = ScreenComponent::findOrFail($id);
            $screenComponent->delete();

            return redirect()->route('screen_component')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('screen_component_edit')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function list_produk()
    {
        $data = Component::where('comp_id', 'like', 'PR0%')
        ->where('component_type_id', 0)
        ->get();
        
        return view('apps.biller.list-produk') ->with('data' , $data);
    }

    public function create_produk()
    {
        $screen = Screen::where('screen_title', 'like', 'Form Pilih %')
        ->get();
        
        return view('apps.biller.create-produk') ->with('screen' , $screen);
    }

    public function store_produk(Request $request)
    {
        DB::beginTransaction(); 
        try {
            $lastComponent = Component::where('comp_id', 'like', 'PR0%')
                ->orderBy('comp_id', 'desc')
                ->first();

            if ($lastComponent) {
                $lastCompIdNumber = (int)substr($lastComponent->comp_id, 2);
                $newCompId = 'PR0' . str_pad($lastCompIdNumber + 1, 2, '0', STR_PAD_LEFT);
            }
            $newComponent = new Component();
            $newComponent->comp_id = $newCompId;
            $newComponent->comp_lbl = 'Produk ' . $request->nama_produk;
            $newComponent->component_type_id = 4;
            $newComponent->comp_content_type = 0;
            $newComponent->visible = true;
            $newComponent->save(); 

            $selectedScreenId = $request->input('screen');
            $prefix = substr($selectedScreenId, 0, 2) . 'PD';

            $lastScreen = Screen::where('screen_id', 'like', $prefix . '%')
                ->orderBy('screen_id', 'desc')
                ->first();

            if ($lastScreen) {
                $lastScreenIdNumber = (int)substr($lastScreen->screen_id, 4);
                $newScreenId = $prefix . str_pad($lastScreenIdNumber + 1, 3, '0', STR_PAD_LEFT);
            }

            $newScreen = new Screen();
            $newScreen->screen_id = $newScreenId;
            $newScreen->screen_title = 'Form ' . $request->input('nama_produk');
            $newScreen->screen_type_id = 1;
            $newScreen->version = 1;
            $newScreen->action_url = $lastScreen->action_url;
            $newScreen->save(); 

            $lastScreenComponents = ScreenComponent::where('screen_id', $lastScreen->screen_id)
                ->orderBy('sequence', 'asc')
                ->get();

            foreach ($lastScreenComponents as $component) {
                $newScreenComponent = new ScreenComponent();
                $newScreenComponent->screen_id = $newScreenId;

                if (strpos($component->comp_id, substr($newCompId, 0, 2)) === 0) {
                    $newScreenComponent->comp_id = $newCompId; 
                } else {
                    $newScreenComponent->comp_id = $component->comp_id;
                }

                $newScreenComponent->sequence = $component->sequence;
                $newScreenComponent->save(); 
            }

            if ($lastComponent) {
                $lastCompIdNumber = (int)substr($lastComponent->comp_id, 2);
                $newCompId = 'PR0' . str_pad($lastCompIdNumber + 2, 2, '0', STR_PAD_LEFT);
            }

            $new_menu = new Component();
            $new_menu->comp_id = $newCompId;
            $new_menu->comp_lbl = $request->nama_produk;
            $new_menu->component_type_id = 0;
            $new_menu->comp_content_type = 0;
            $new_menu->visible = true;
            $new_menu->comp_act = $newScreenId;
            $new_menu->save(); 
            
            $lastPilihan = ScreenComponent::where('screen_id', $selectedScreenId)
                ->orderBy('sequence', 'desc')
                ->first();

            $new_pilihan_produk = new ScreenComponent();
            $new_pilihan_produk->screen_id = $selectedScreenId;
            $new_pilihan_produk->comp_id = $newCompId;
            $new_pilihan_produk->sequence = $lastPilihan->sequence + 1;
            $new_pilihan_produk->save(); 

            DB::commit(); 
            return redirect()->route('list_produk')->with('success', 'Produk berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('create_produk')->with('error', 'Produk gagal ditambahkan: ' . $e->getMessage());
        }
    }

    public function edit_produk()
    {

    }

    public function update_produk()
    {

    }
}
