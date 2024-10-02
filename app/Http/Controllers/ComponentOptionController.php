<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\CompOption;

class ComponentOptionController extends Controller
{
    public function list_produk(Request $request)
    {
        $data = CompOption::whereIn('comp_id', ['PR006', 'PR007', 'PR008', 'PR009', 'PR010'])
        ->with('component') 
        ->orderBy('comp_id', 'asc')
        ->get();

        if ($request->has('search')) {
            $data->whereRaw('lower(name) like ?', ["%{$request->search}%"]);
        }

        $total = $data->count();

        if ($request->has('order_type') && $request->has('order_by')) {
            $orderType = $request->get('order_type') == 'asc' ? 'asc' : 'desc';
            $data->orderBy($request->get('order_by'), $orderType);
        }

        return view('apps.masterdata.list-produk')
            ->with('data', $data);
    }

    public function create(){

    }

    public function store(){

    }

    public function edit($opt_id){
        try {
            $data = CompOption::where('opt_id', $opt_id)->firstOrFail();

            return view('apps.masterdata.edit-produk', compact('data'));
        } catch (Exception $e) {
            return redirect()->route('list_produk')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function update(){

    }
}
