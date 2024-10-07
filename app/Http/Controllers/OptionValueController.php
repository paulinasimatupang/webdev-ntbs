<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\CompOption;
use App\Entities\Component;
use App\Entities\OptionValue;
use DB;

class OptionValueController extends Controller
{
    protected $provider;

    public function __construct()
    {
        $this->provider = Component::where('comp_id', 'like', 'PR%')
                                    ->where('component_type_id', 4)
                                    ->pluck('comp_id')
                                    ->toArray();
    }

    public function list(Request $request)
    {
        $data = OptionValue::join('comp_option', 'option_value.opt_id', '=', 'comp_option.opt_id')
            ->whereIn('comp_option.comp_id', $this->provider)
            ->select('option_value.*')
            ->with('comp_option');

        if ($request->has('search')) {
            $data->whereRaw('lower(option_value.meta_id) like ?', ["%{$request->search}%"]);
        }

        if ($request->has('order_type') && $request->has('order_by')) {
            $orderType = $request->get('order_type') == 'asc' ? 'asc' : 'desc';
            $data->orderBy($request->get('order_by'), $orderType);
        }

        $data = $data->get();

        return view('apps.biller.list-subproduk')->with('data', $data);
    }

    public function create()
    {
        $component = Component::whereIn('comp_id', $this->provider)
        ->with('comp_option')
        ->get();

        return view('apps.biller.create-subproduk', compact('component'));
    }

    public function store(Request $request)
    {
        try {
            
            $inputProduk = $request->input('items');
            if (strpos($inputProduk, '-') !== false) {
                list($optId, $optLabel) = explode(' - ', $inputProduk, 2);

                $existingCompOption = CompOption::where('opt_id', trim($optId))->first();
                if ($existingCompOption) {
                    return redirect()->route('create_sub_produk')
                        ->with('failed', 'Opt ID sudah terdaftar di CompOption. Silakan gunakan yang lain.')
                        ->withInput();
                }

                $lastSeq = CompOption::where('comp_id', $request->input('comp_id'))
                ->max('seq');

                $compOption = new CompOption();
                $compOption->comp_id = $request->input('comp_id');
                $compOption->opt_id = trim($optId);
                $compOption->seq = $lastSeq !== null ? $lastSeq + 1 : 0;
                $compOption->opt_label = trim($optLabel);
                $compOption->save();

                $existingOptionValue = OptionValue::where('opt_id', trim($optId))
                    ->where('meta_id', $request->input('meta_id'))
                    ->first();

                if ($existingOptionValue) {
                    return redirect()->route('create_sub_produk')
                        ->with('failed', 'Gabungan opt_id dan meta_id sudah terdaftar. Silakan gunakan yang lain.')
                        ->withInput();
                }

                $optionValue = new OptionValue();
                $optionValue->opt_id = trim($optId);
                $optionValue->meta_id = $request->input('meta_id');
                $optionValue->default_value = $request->input('default_value');
                $optionValue->save();
            } else {
                $inputProduk = $request->input('inputProduk');
                
                $existingOptionValue = OptionValue::where('opt_id', $inputProduk)
                    ->where('meta_id', $request->input('meta_id'))
                    ->first();

                if ($existingOptionValue) {
                    return redirect()->route('create_sub_produk')
                        ->with('failed', 'Gabungan opt_id dan meta_id sudah terdaftar. Silakan gunakan yang lain.')
                        ->withInput();
                }

                $optionValue = new OptionValue();
                $optionValue->opt_id = $inputProduk;
                $optionValue->meta_id = $request->input('meta_id');
                $optionValue->default_value = $request->input('default_value');
                $optionValue->save();
            }

            return redirect()->route('list_sub_produk')->with('success', 'Produk berhasil ditambahkan.');
        } 
        catch (\Exception $e) {
            return redirect()->route('create_sub_produk')->with('failed', 'Gagal menambahkan produk: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($opt_id, $meta_id)
    {
        try {
            $data = Optionvalue::where('opt_id', $opt_id)
            ->where('meta_id', $meta_id)
            ->firstOrFail();

            return view('apps.biller.edit-subproduk', compact('data'));
        } catch (Exception $e) {
            return redirect()->route('list_sub_produk')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $opt_id, $meta_id)
    {
        $request->validate([
            'default_value' => 'required',
        ]);

        try {
            $updated = DB::connection('pgsql_billiton')->table('option_value')
            ->where('opt_id', '=', $opt_id)
            ->where('meta_id', '=', $meta_id)
            ->update([
                'default_value' => $request->input('default_value'),
            ]);

            if ($updated === 0) {
                throw new Exception('Tidak ada record yang diperbarui');
            }
            DB::commit();

            return redirect()->route('list_sub_produk')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('edit_sub_produk')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->route('edit_sub_produk')
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function get_nominal($opt_id) 
    {
        try {
            $fee = 0;
            $nominal_debit = 0;
            $buffer = 0;

            $data = OptionValue::where('opt_id', $opt_id)
            ->get();

            foreach ($data as $item) {
                if ($item->meta_id === 'nominal_debit') {
                    $nominal_debit = $item->default_value;
                }
                if ($item->meta_id === 'fee') {
                    $fee = $item->default_value; 
                }
    
                if ($item->meta_id === 'buffer') {
                    $buffer = $item->default_value;
                }
            }

            $total = $nominal_debit + $fee + $buffer;
            return response()->json([
                'success' => true,
                'total' => $total, 
                'fee' => $fee
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found for the given opt_id.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

}
