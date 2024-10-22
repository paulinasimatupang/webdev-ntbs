<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TerminalCreateRequest;
use App\Http\Requests\TerminalUpdateRequest;
use App\Repositories\TerminalRepository;
use App\Validators\TerminalValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use App\Entities\Terminal;
use App\Entities\Merchant;
use App\Entities\Pengaduan;
use App\Entities\TerminalBilliton;
use App\Entities\TerminalUserBilliton;
use App\Entities\UsersBilliton;
use App\Entities\Imei;
use App\Services\SendPushNotification;



/**
 * Class TerminalsController.
 *
 * @package namespace App\Http\Controllers;
 */
class TerminalsController extends Controller
{
    /**
     * @var TerminalRepository
     */
    protected $repository;

    /**
     * @var TerminalValidator
     */
    protected $validator;

    /**
     * TerminalsController constructor.
     *
     * @param TerminalRepository $repository
     * @param TerminalValidator $validator
     */
    public function __construct(TerminalRepository $repository, TerminalValidator $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = Terminal::select('*');

        // $data = $data->whereHas('merchant.user', function($query){
        //     $query->where(function($q){
        //         $q->where('is_user_mireta', '!=', 1)->orWhereNull('is_user_mireta');
        //     });
        //     $query->where(function($q){
        //         $q->where('is_development_user', '!=', 1)->orWhereNull('is_development_user');
        //     });
        // });

        if ($request->has('search')) {
            $data = $data->whereRaw('lower(name) like (?)', ["%{$request->search}%"]);
        }

        if ($request->has('status')) {
            $data = $data->where('status', $request->status);
        }

        $total = $data->count();

        if ($request->has('limit')) {
            $data->take($request->get('limit'));

            if ($request->has('offset')) {
                $data->skip($request->get('offset'));
            }
        }

        if ($request->has('order_type')) {
            if ($request->get('order_type') == 'asc') {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'));
                } else {
                    $data->orderBy('created_at');
                }
            } else {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'), 'desc');
                } else {
                    $data->orderBy('created_at', 'desc');
                }
            }
        } else {
            $data->orderBy('created_at', 'desc');
        }

        $data = $data->get();

        foreach ($data as $item) {
            if ($item->merchant_id) {
                $item->status = 'Used';
            } else {
                $item->status = 'Not Used';
            }
        }

        $user = session()->get('user');

        return view('apps.terminals.list')
            ->with('data', $data)
            ->with('username', $user->username);
    }

    public function create(Request $request)
    {
        $merchant = Merchant::where('terminal_id', null)->orderBy('name')->get();
        $merchant = $merchant->where('status_agen', 1);


        return view('apps.terminals.add')
            ->with('merchant', $merchant);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TerminalCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $imei = $request->input('imei');
            $merchantId = $request->input('merchant_id');

            // Validasi input
            if (!$imei || !$merchantId) {
                throw new \Exception('IMEI atau Merchant ID tidak valid.');
            }

            // Validasi merchant
            $merchant = Merchant::where('mid', $merchantId)->first();
            if (!$merchant) {
                throw new \Exception('Merchant not found');
            }

            // Membuat TID baru
            $prefix = 'TID';
            $lastTid = Terminal::where('tid', 'LIKE', $prefix . '%')
                ->orderBy('created_at', 'desc')
                ->first();

            $tid = $lastTid
                ? $prefix . str_pad((int) substr($lastTid->tid, strlen($prefix)) + 1, 6, '0', STR_PAD_LEFT)
                : $prefix . '000001';

            // Pastikan TID unik
            while (Terminal::where('tid', $tid)->exists()) {
                $tid = $prefix . str_pad(++$newNumber, 6, '0', STR_PAD_LEFT);
            }

            // Simpan data terminal
            $terminalData = [
                'tid' => $tid,
                'imei' => $imei,
                'merchant_id' => $merchantId,
            ];

            $data = $this->repository->create($terminalData);
            if (!$data) {
                throw new \Exception('Failed to create terminal');
            }

            // Simpan dan aktivasi terminal
            $merchant->terminal_id = $tid;
            $merchant->save();

            $activated = $this->activateBilliton(new TerminalUpdateRequest([
                'merchant_id' => $merchantId,
                'tid' => $tid,
            ]), $data->id);

            if (!$activated) {
                throw new \Exception('Terminal created but activation failed');
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Terminal created and activated',
                'data' => $data,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating terminal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TerminalCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */

    public function activateBilliton(TerminalUpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Validasi data
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            // Ambil terminal
            $terminal = Terminal::where('id', $id)->first();
            if (!$terminal) {
                throw new \Exception('Terminal not found');
            }

            // Siapkan data merchant
            $reqData = [];
            if ($request->has('merchant_id')) {
                $merchant = Merchant::where('mid', $request->merchant_id)->first();
                if ($merchant) {
                    $reqData['merchant_id'] = $merchant->mid;
                    $reqData['merchant_name'] = $merchant->name;
                    $reqData['merchant_address'] = $merchant->address;
                    $reqData['merchant_account_number'] = $merchant->no;
                } else {
                    $reqData['merchant_id'] = null;
                    $reqData['merchant_name'] = null;
                    $reqData['merchant_address'] = null;
                    $reqData['merchant_account_number'] = null;
                }

                // Update terminal
                $this->repository->update($reqData, $id);
            }

            // Simpan terminal Billiton
            $terminalBilliton = new TerminalBilliton();
            $terminalBilliton->terminal_id = $terminal->tid;
            $terminalBilliton->terminal_type = '1';
            $terminalBilliton->terminal_imei = $terminal->imei;
            $terminalBilliton->terminal_name = $terminal->serial_number;
            $terminalBilliton->merchant_id = $terminal->merchant_id;
            $terminalBilliton->terminal_sim_number = $terminal->iccid;
            $terminalBilliton->save();

            if ($terminalBilliton) {
                $user = UsersBilliton::select('*')
                    ->orderBy('user_uid', 'desc')
                    ->first();

                if ($user) {
                    $userId = (int) $user->user_uid + 1;
                    $usersBilliton = UsersBilliton::create([
                        'user_uid' => $userId,
                        'user_status_uid' => 1,
                        'user_type_uid' => 1,
                        'username' => $terminal->tid,
                        'version' => '1.7',
                        'brand' => $terminal->merchant_name,
                        'model' => $terminal->merchant_address,
                        'os_ver' => 'AGEN BJB BISA',
                        'account_name' => $terminal->merchant_account_number,
                        'app_ver' => $terminal->sid,
                        'need_approval' => 't',
                        'batch_no' => 0,
                    ]);

                    if ($usersBilliton) {
                        $terminalUserBilliton = TerminalUserBilliton::create([
                            'terminal_id' => $terminal->tid,
                            'user_uid' => $userId,
                        ]);

                        if ($terminalUserBilliton) {
                            DB::commit();
                            return true;
                        } else {
                            DB::rollback();
                            return false;
                        }
                    } else {
                        DB::rollback();
                        return false;
                    }
                } else {
                    DB::rollback();
                    return false;
                }
            }

            DB::rollback();
            return false;
        } catch (Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateBilliton(TerminalUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validasi data
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            // Ambil terminal berdasarkan id
            $terminal = Terminal::where('id', $id)->first();

            if ($terminal) {
                // Buat instance baru TerminalBilliton dan simpan data yang di-update
                $terminalBilliton = new TerminalBilliton();
                $terminalBilliton->terminal_id = $terminal->tid;
                $terminalBilliton->terminal_type = '1';
                $terminalBilliton->terminal_imei = $terminal->imei;
                $terminalBilliton->terminal_name = $terminal->serial_number;
                $terminalBilliton->merchant_id = $terminal->merchant_id;
                $terminalBilliton->terminal_sim_number = $terminal->iccid;
                $terminalBilliton->save();

                if ($terminalBilliton) {
                    DB::commit();
                    return true;
                } else {
                    DB::rollback();
                    return false;
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->repository->find($id);

        $response = [
            'status' => true,
            'message' => 'Success',
            'data' => $data,
        ];

        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $terminal = Terminal::find($id);
        $checkTerminalBilliton = false;
        if ($terminal) {
            $terminalBilliton = TerminalBilliton::where('terminal_imei', $terminal->imei)
                ->first();

            if ($terminalBilliton) {
                $checkTerminalBilliton = true;
                $tidBilliton = $terminalBilliton->terminal_id;
            } else {
                $checkTerminalBilliton = false;
                $tidBilliton = " ";
            }

            $merchant = Merchant::where('terminal_id', null)
                ->orWhere('terminal_id', $terminal->tid)
                ->orderBy('name')
                ->get();

            return view('apps.terminals.edit')
                ->with('terminal', $terminal)
                ->with('merchant', $merchant)
                ->with('tidBilliton', $tidBilliton)
                ->with('checkTerminalBilliton', $checkTerminalBilliton);
        } else {
            return Redirect::to('terminal')
                ->with('error', 'Data not found');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TerminalUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(TerminalUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $reqData = $request->all();

            if ($request->has('merchant_id')) {
                $merchant = Merchant::where('mid', $request->merchant_id)->first();
                if ($merchant) {
                    $reqData['merchant_id'] = $merchant->mid;
                    $reqData['merchant_name'] = $merchant->name;
                    $reqData['merchant_address'] = $merchant->address;
                    $reqData['merchant_account_number'] = $merchant->no;
                } else {
                    $reqData['merchant_id'] = null;
                    $reqData['merchant_name'] = null;
                    $reqData['merchant_address'] = null;
                    $reqData['merchant_account_number'] = null;
                }
            }

            $data = $this->repository->update($reqData, $id);

            if (!$data) {
                DB::rollBack();
                return Redirect::to('terminal/' . $id . '/edit')->with('error', 'Failed to update terminal data.');
            }

            $terminalBilliton = TerminalBilliton::where('terminal_id', $request->get('tid'))->first();
            if ($terminalBilliton) {
                $terminalBilliton->update([
                    'terminal_imei' => $request->get('imei'),
                ]);
            } else {
                DB::rollBack();
                return Redirect::to('terminal/' . $id . '/edit')->with('error', 'TerminalBilliton not found for terminal_id: ' . $request->get('tid'));
            }

            $billitonUpdateSuccess = $this->updateBilliton($request, $id);

            if (!$billitonUpdateSuccess) {
                DB::rollBack();
                return Redirect::to('terminal/' . $id . '/edit')->with('error', 'Failed to update Billiton data.');
            }

            DB::commit();
            return Redirect::to('terminal')->with('message', 'Terminal updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::to('terminal/' . $id . '/edit')
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('terminal/' . $id . '/edit')
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Terminal::find($id);

            if ($data) {
                if ($data->merchant_id != null) {
                    $merchant = Merchant::where('mid', $data->merchant_id)->first();
                    if ($merchant) {
                        $merchant->terminal_id = null;
                        $merchant->save();
                    }
                }

                TerminalBilliton::where('terminal_id', $id)->delete();

                $deleted = $this->repository->delete($id);

                if ($deleted) {
                    DB::commit();
                    return redirect()->route('terminal')->with('success', 'Terminal berhasil dihapus.');
                }
            }


            return redirect()->route('terminal')->with('failed', 'Terminal tidak ditemukan.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('terminal')->with('failed', 'Terjadi kesalahan', $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return redirect()->route('terminal')->with('failed', 'Terjadi kesalahan', $e->getMessage());
        }
    }

    public function deleteMerchantData($id, $mid)
    {
        $terminal = Terminal::find($id);
        if ($terminal) {
            $terminal->merchant_id = null;
            $terminal->merchant_name = null;
            $terminal->merchant_address = null;
            $terminal->merchant_account_number = null;
            $terminal->save();
        }

        $merchant = Merchant::where('mid', $mid)->first();
        if ($merchant) {
            $merchant->terminal_id = null;
            $merchant->save();
        }
    }

    public function list_request(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = Imei::select('*');
        $user = session()->get('user');

        if ($request->has('search')) {
            $data = $data->whereRaw('lower(name) like (?)', ["%{$request->search}%"]);
        }

        $data = $data->get();

        $user = session()->get('user');

        return view('apps.terminals.list-request')
            ->with('data', $data)
            ->with('username', $user->username);
    }

    public function acceptChangeImei($id)
    {
        DB::beginTransaction();
        try {
            // Ambil data IMEI berdasarkan ID
            $imeiRequest = Imei::where('id', $id)->first();
            if (!$imeiRequest) {
                throw new \Exception("Request IMEI not found");
            }

            $terminal = Terminal::where('tid', $imeiRequest->tid)->first();
            if (!$terminal) {
                throw new \Exception("Terminal not found");
            }

            $pengaduan = Pengaduan::find($imeiRequest->id_pengaduan);
            if ($pengaduan) {
                $pengaduan->status = 2; // Update status pengaduan
                $pengaduan->save();
            }

            $imeiRequest->status = true; // Set IMEI request as approved
            $imeiRequest->save();

            // Cari Merchant berdasarkan MID atau TID yang ada pada IMEI
            $merchant = Merchant::where('mid', $imeiRequest->mid)->first();
            if (!$merchant) {
                throw new \Exception("Merchant not found");
            }

            // Ambil FCM token dari Merchant
            $fcmToken = $merchant->fcm_token;

            // Validasi token FCM
            if ($this->isValidFcmToken($fcmToken)) {
                // Jika FCM token tersedia, kirim notifikasi
                $notificationService = new SendPushNotification();
                $notificationService->sendNotificationToToken($fcmToken, [
                    'title' => 'IMEI Request Approved',
                    'message' => "Permintaan penggantian IMEI untuk terminal {$terminal->tid} telah disetujui.",
                ]);
            } else {
                Log::warning("FCM token tidak valid atau tidak ditemukan untuk merchant MID: {$merchant->mid}");
            }

            DB::commit();
            return redirect()->route('imei_request')->with('success', 'IMEI request approved successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in acceptChangeImei: ' . $e->getMessage());
            return redirect()->route('imei_request')->with('error', $e->getMessage());
        }
    }

    public function rejectChangeImei($id)
    {
        DB::beginTransaction();
        try {
            Log::info('Rejecting IMEI request with ID: ' . $id); // Tambahkan logging

            // Ambil data IMEI berdasarkan ID
            $imeiRequest = Imei::where('id', $id)->first();
            if (!$imeiRequest) {
                throw new \Exception("Request IMEI not found");
            }

            // Log data imeiRequest untuk debugging
            Log::info('IMEI Request found: ' . json_encode($imeiRequest));

            $pengaduan = Pengaduan::find($imeiRequest->id_pengaduan);
            if ($pengaduan) {
                $pengaduan->status = 3;
                $pengaduan->save();
            }

            $imeiRequest->status = false;
            $imeiRequest->delete();

            // Cari Merchant berdasarkan MID atau TID yang ada pada IMEI
            $merchant = Merchant::where('mid', $imeiRequest->mid)->first();
            if (!$merchant) {
                throw new \Exception("Merchant not found");
            }

            // Ambil FCM token dari Merchant
            $fcmToken = $merchant->fcm_token;

            if ($this->isValidFcmToken($fcmToken)) {
                $notificationService = new SendPushNotification();
                $notificationService->sendNotificationToToken($fcmToken, [
                    'title' => 'IMEI Request Rejected',
                    'message' => "Permintaan penggantian IMEI untuk Terminal {$imeiRequest->tid} ditolak.",
                ]);
            }

            DB::commit();
            return redirect()->route('imei_request')->with('success', 'IMEI request rejected successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in rejectChangeImei: ' . $e->getMessage());
            return redirect()->route('imei_request')->with('error', $e->getMessage());
        }
    }


    public function storeImei(Request $request)
    {
        // Check if user is authenticated
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'tid' => 'required|string|max:255',
            'imei' => 'required|string|max:255',
            'mid' => 'required|string|max:255',
            'id_pengaduan' => 'required|string|max:255',
        ]);

        // If validation fails, return the error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Create a new IMEI entry in the database
            $imei = Imei::create([
                'tid' => $request->input('tid'),
                'imei' => $request->input('imei'),
                'mid' => $request->input('mid'),
                'id_pengaduan' => $request->input('id_pengaduan'),
                'status' => false
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'IMEI data saved successfully',
                'data' => $imei
            ], 201);
        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Failed to save IMEI data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function isValidFcmToken($fcmToken)
    {
        // Validasi sederhana untuk FCM token. Ini dapat disesuaikan dengan aturan validasi yang diperlukan.
        return !empty($fcmToken) && preg_match('/^[a-zA-Z0-9\-_:.]+$/', $fcmToken);
    }

    public function checkStatus(Request $request)
    {

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validasi input
        $request->validate([
            'tid' => 'required|string',
            'mid' => 'required|string',
        ]);

        // Ambil tid dan mid dari request
        $tid = $request->tid;
        $mid = $request->mid;

        // Cari status berdasarkan tid dan mid
        $imeiRecord = Imei::where('tid', $tid)
            ->where('mid', $mid)
            ->first();

        // Cek apakah data ditemukan
        if ($imeiRecord) {
            // Return status IMEI
            return response()->json([
                'success' => true,
                'message' => 'Data found',
                'status' => $imeiRecord->status, // Pastikan kolom 'status' ada di tabel imei
                'data' => $imeiRecord
            ], 200);
        } else {
            // Jika data tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404);
        }
    }

    public function updateImei(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validasi input request
        $request->validate([
            'tid' => 'required|string',
            'mid' => 'required|string',
            'imei' => 'required|string',
        ]);

        // Ambil tid, mid, dan imei dari request
        $tid = $request->tid;
        $mid = $request->mid;
        $newImei = $request->imei;

        // Mulai transaksi database
        DB::beginTransaction();
        try {
            // Cari record di tabel Terminal berdasarkan tid dan mid
            $imeiRecord = Terminal::where('tid', $tid)
                ->where('merchant_id', $mid)
                ->first();

            // Cek apakah record ditemukan
            if ($imeiRecord) {
                // Update IMEI di record yang ditemukan
                $imeiRecord->imei = $newImei;
                $imeiRecord->save();

                // Hapus data dari tabel Imei berdasarkan TID yang diupdate
                Imei::where('tid', $tid)->delete();

                // Update terminal_imei di tabel TerminalBilliton
                $terminalBilliton = TerminalBilliton::where('terminal_id', $tid)->first();
                if ($terminalBilliton) {
                    $terminalBilliton->update([
                        'terminal_imei' => $newImei,
                    ]);

                    // Log sukses update
                    Log::info("TerminalBilliton for TID: $tid successfully updated with new IMEI: $newImei");
                } else {
                    // Jika data TerminalBilliton tidak ditemukan, rollback dan return error
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'TerminalBilliton not found for terminal_id: ' . $tid
                    ], 404);
                }

                // Commit transaksi
                DB::commit();

                // Return response success
                return response()->json([
                    'success' => true,
                    'message' => 'IMEI updated successfully and Imei data deleted',
                    'data' => $imeiRecord
                ], 200);
            } else {
                // Rollback jika data tidak ditemukan
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found for provided TID and MID'
                ], 404);
            }
        } catch (\Exception $e) {
            // Rollback jika terjadi kesalahan
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }



    public function create_request()
    {
        return view('apps.terminals.add-request');
    }

    public function store_request(Request $request)
    {
        // Autentikasi pengguna
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Unauthorized');
        }

        // Validasi input
        $request->validate([
            'tid' => 'required|string',
            'mid' => 'required|string',
            'imei' => 'required|string', // Unik untuk imei
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Membuat record IMEI baru dengan status 'inactive' (false)
            $imeiRecord = Imei::create([
                'tid' => $request->tid,
                'mid' => $request->mid,
                'imei' => $request->imei,
                'status' => false, // Set status otomatis ke 'false'
            ]);

            // Commit transaksi
            DB::commit();

            // Redirect kembali ke route 'imei.request' dengan pesan sukses
            return redirect()->route('imei_request')->with('status', 'IMEI created successfully');

        } catch (Exception $e) {
            // Jika terjadi kesalahan, rollback transaksi
            DB::rollBack();

            return redirect()->route('imei_request')->with('error', 'Something went wrong: ' . $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            // Menangani kesalahan query database
            DB::rollBack();

            return redirect()->route('imei_request')->with('error', 'Database error: ' . $e->getMessage());
        }
    }

}
