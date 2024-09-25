<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Service;
use Redirect;

class ServiceBillerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Service::query();

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

        $user = session()->get('user');

        return view('apps.service_biller.list')
            ->with('data', $data)
            ->with('username', $user->username);
    }

    /**
     * Get param2 value by service_id
     *
     * @param string $service_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParam2($service_id)
    {
        // Mencari service berdasarkan service_id
        $service = Service::where('service_id', $service_id)->first();

        // Jika service tidak ditemukan, kembalikan response dengan pesan dan null
        if (!$service) {
            return response()->json([
                'message' => 'Service not found',
            ], 404);
        }

        // Mengambil nilai param2
        $param2 = $service->param2;

        // Jika param2 ada, hitung jumlah kemunculan param2 yang sama
        if ($param2) {
            $count = Service::where('param2', $param2)->count();
        } else {
            $count = 0; // Jika param2 tidak ada, set count ke 0
        }

        // Mengembalikan service_id, param2, dan jumlah kemunculan
        return response()->json([
            'service_id' => $service_id,
            'param2' => $param2,
            'count' => $count
        ], 200);
    }

}
