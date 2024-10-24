<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use Validator;
use Illuminate\Support\Str;

use App\Mail\sendPassword;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\MerchantCreateRequest;
use App\Http\Requests\MerchantUpdateRequest;
use App\Repositories\MerchantRepository;
use App\Validators\MerchantValidator;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;

use App\Exports\MerchantExport;

use App\Entities\Merchant;
use App\Entities\Role;
use App\Entities\User;
use App\Entities\Terminal;
use App\Entities\Group;
use App\Entities\TerminalBilliton;
use App\Entities\UserGroup;
use App\Entities\Assesment;
use App\Entities\AssesmentResult;
use App\Entities\AssesmentResultDetail;
use App\Entities\CompOption;
use App\Entities\Component;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as Pdf;

/**
 * Class MerchantsController.
 *
 * @package namespace App\Http\Controllers;
 */
class MerchantsController extends Controller
{
    /**
     * @var MerchantRepository
     */
    protected $repository;

    /**
     * @var MerchantValidator
     */
    protected $validator;

    /**
     * MerchantsController constructor.
     *
     * @param MerchantRepository $repository
     * @param MerchantValidator $validator
     */
    public function __construct(MerchantRepository $repository, MerchantValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function menu() 
    {
        $jumlah_request = Merchant::where('status_agen', 0)->count();
        $jumlah_blocked = Merchant::where('status_agen', 3)->count();

        return view('apps.merchants.menu', compact('jumlah_request', 'jumlah_blocked'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = Merchant::select('*')
                ->with('user')
                ->whereIn('status_agen', [1, 2, 3]);

        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);
            
            if ($role && $role->name == 'Customer Service Cabang') {
                $branch_id = $user->branchid;
                $data->where('branchid', $branch_id);
            }
        }  

        if($request->has('search')){
            $data = $data->whereRaw('lower(name) like (?)',["%{$request->search}%"]);
        }

        $total = $data->count();
    
        if($request->has('limit')){
            $data->take($request->get('limit'));
            
            if($request->has('offset')){
            	$data->skip($request->get('offset'));
            }
        }

        if($request->has('order_type')){
            if($request->get('order_type') == 'asc'){
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'));
                }else{
                    $data->orderBy('created_at');
                }
            }else{
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'), 'desc');
                }else{
                    $data->orderBy('created_at', 'desc');
                }
            }
        }else{
            $data->orderBy('created_at', 'asc');
        }

        $data = $data->get();

        foreach($data as $merchant) {
            if ($merchant->status_agen == 1){
                $merchant->status_text = 'Aktif';
            } else if ($merchant->status_agen == 2){ 
                $merchant->status_text = 'Tidak Aktif';
            } else if ($merchant->status_agen == 3){ 
                $merchant->status_text = 'Terblokir';
            }

            if($merchant->active_at == null || $merchant->active_at == ''){
                $merchant->active_at = '-';
            }

            if ($merchant->resign_at == null || $merchant->resign_at == ''){
                $merchant->resign_at = '-';
            }
        }

        return view('apps.merchants.list')
                ->with('data', $data)
                ->with('username', $user->username);
    }

    public function list_block(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = Merchant::select('*');
        $data = $data->where('status_agen', 3);

        if($request->has('search')){
            $data = $data->whereRaw('lower(name) like (?)',["%{$request->search}%"]);
        }

        $total = $data->count();
    
        if($request->has('limit')){
            $data->take($request->get('limit'));
            
            if($request->has('offset')){
            	$data->skip($request->get('offset'));
            }
        }

        if($request->has('order_type')){
            if($request->get('order_type') == 'asc'){
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'));
                }else{
                    $data->orderBy('created_at');
                }
            }else{
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'), 'desc');
                }else{
                    $data->orderBy('created_at', 'desc');
                }
            }
        }else{
            $data->orderBy('created_at', 'asc');
        }

        $data = $data->get();

        $user = session()->get('user');

        return view('apps.merchants.list-block')
                ->with('data', $data)
                ->with('username', $user->username);
    }

    public function request_list(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = Merchant::select('*');

        $data = $data->where('status_agen', 0);

        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);
            
            if ($role && $role->name == 'Customer Service Cabang') {
                $branch_id = $user->branchid;
                $data->where('branchid', $branch_id);
            }
        } 

        if($request->has('search')){
            $data = $data->whereRaw('lower(name) like (?)', ["%{$request->search}%"]);
        }

        $total = $data->count();

        if($request->has('order_type')){
            if($request->get('order_type') == 'asc'){
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'));
                } else {
                    $data->orderBy('created_at');
                }
            } else {
                if($request->has('order_by')){
                    $data->orderBy($request->get('order_by'), 'desc');
                } else {
                    $data->orderBy('created_at', 'desc');
                }
            }
        } else {
            $data->orderBy('created_at', 'asc');
        }

        $data = $data->get();

        return view('apps.merchants.list-request')
            ->with('data', $data)
            ->with('username', $user->username);
    }
    
    public function inquiry_rek(Request $request){
        return view('apps.merchants.inquiry-rek');
    }


    public function store_inquiry_rek(Request $request)
    {
        $rek = $request->input('rek');
        $terminal = '353471045058692';
        $dateTime = date("YmdHms");

        $ch = curl_init();
                $urlArrest = config('app.url_arrest');
                curl_setopt($ch, CURLOPT_URL,$urlArrest);

        $data = json_encode([
            'msg'=>([
            'msg_id' =>  "$terminal$dateTime",
            'msg_ui' => "$terminal",
            'msg_si' => 'INF001',
            'msg_dt' => 'admin|'. $rek
            ])
        ]);
       
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/plain',
            'Accept: text/plain'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);
        $err = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        Log::info('cURL Request URL: '  . $info['url']);
        Log::info('cURL Request Data: ' . $data);
        Log::info('cURL Response: ' . $output);

        $match = false;

        $responseArray = json_decode($output, true);
        
        $cifid = null;
        $nama_rek = null;
        $alamat = null;
        $norek = null;
        $no_hp = null;
        $no_telp = null;
        $no_npwp = null;
        $no_ktp = null;
        $prod_id= null;

        if ($err) {
            Log::error('cURL Error: ' . $err);
        } else {
            if (isset($responseArray['screen']['comps']['comp'])) {
                foreach ($responseArray['screen']['comps']['comp'] as $comp) {
                    if (isset($comp['comp_values']['comp_value'][0]['value'])) {
                        $value = $comp['comp_values']['comp_value'][0]['value'];
                        if ($value !== 'null' && $value !== null) {
                            switch ($comp['comp_lbl']) {
                                case 'No CIF':
                                    $cifid = $value;
                                    break;
        
                                case 'Nama Rekening':
                                    $nama_rek = $value;
                                    break;
        
                                case 'Alamat':
                                    $alamat = $value;
                                    break;
        
                                case 'Nomor Rekening':
                                    $norek = $value;
                                    $match = Merchant::where('no', $norek)->exists();
                                    break;
        
                                case 'Nomor Handphone':
                                    $no_hp = $value;
                                    break;
                                
                                case 'NIK':
                                    $no_ktp = $value;
                                    break;
                                
                                case 'NPWP':
                                    $no_npwp = $value;
                                    break;
                                
                                case 'No Telepon':
                                    $no_telp = $value;
                                    break;
                                case 'ProdID':
                                    $prod_id = $value;
                                    break;
                            }
                        }
                    }
                }
            }
        }
        
        if ($match){
            return Redirect::to('/agen/create/inquiry')->with('error', "Agen dengan Nomor Rekening yang Anda Masukkan Sudah Terdaftar")->withInput();
        }
        else if (isset($responseArray['screen']['title']) && $responseArray['screen']['title'] === 'Gagal') {
            $errorMessage = '';
            if (isset($responseArray['screen']['comps']['comp'][0]['comp_values']['comp_value'][0]['value'])) {
                $errorMessage = $responseArray['screen']['comps']['comp'][0]['comp_values']['comp_value'][0]['value'];
            } else {
                $errorMessage = "Terjadi Kesalahan";
            }

            return Redirect::to('/agen/create/inquiry')
                            ->with('error', $errorMessage)
                            ->withInput();
        } 
        else if ($prod_id === '36')
        {
            return Redirect::to('/agen/create/inquiry')
            ->with('error', "Nomor Rekening yang Anda Masukkan Tidak Dapat Didaftarkan Sebagai Agen")
            ->withInput();
        }
        else {
            return Redirect::to('/agen/create')
            ->with('no_cif', $cifid)
            ->with('fullname', $nama_rek)
            ->with('address', $alamat)
            ->with('no', $norek)
            ->with('phone', $no_hp)
            ->with('no_ktp', $no_ktp)
            ->with('no_npwp', $no_npwp)
            ->with('no_telp', $no_telp);
        }
    }

    public function create(Request $request)
    {
        $assessments = Assesment::all();

        $min_poin_component = Component::where('comp_lbl', 'min_poin')->first();
        $min_poin = $min_poin_component ? $min_poin_component->comp_act : 0;

        $provinsi = CompOption::where('comp_id', 'CIF14')
                    ->orderBy('seq')
                    ->get();
                    
        $kota_kabupaten = CompOption::where('comp_id', 'CIF13')
                    ->orderBy('seq')
                    ->get();

        $kecamatan = CompOption::where('comp_id', 'CIF11')
        ->orderBy('seq')
        ->get();

        $kelurahan = CompOption::where('comp_id', 'CIF12')
        ->orderBy('seq')
        ->get();

        return view('apps.merchants.add', compact('assessments', 'kelurahan', 'kecamatan', 'kota_kabupaten', 'provinsi', 'min_poin'));
    }

    public function getKotaKabupaten($provinsi_id)
    {
        if (strpos($provinsi_id, 'CIF') === 0) {
            $kode_provinsi = substr($provinsi_id, 3, 2);

            $kota_kabupaten = CompOption::where('comp_id', 'CIF13')
                        ->where('opt_id', 'like', $kode_provinsi.'%')
                        ->get();
        } 
        else {
            $kota_kabupaten = CompOption::where('comp_id', 'CIF13')->get();
        }

        return response()->json($kota_kabupaten);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  MerchantCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
                $role = Role::where('name', 'Agen')->first();
                if ($role) {
                    $role_id = $role->id;
                } else {
                    return Redirect::to('agen/create')
                    ->with('error', 'Role Tidak Ditemukan')
                    ->withInput();
                }
                
                $user_login = session()->get('user');
                
                $user = User::create([
                                'role_id'           => $role_id,
                                'fullname'          => $request->fullname,
                                'email'             => $request->email,
                                'branchid'          => $user_login->branchid,
                                'status'            => 0
                            ]);

                $uploadPath = public_path('uploads/');
                $filePaths = [
                    'file_ktp' => null,
                    'file_npwp' => null,
                    'foto_lokasi_usaha' => null
                ];

                foreach ($filePaths as $fileKey => &$filePath) {
                    if ($request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);
                        Log::info("File '$fileKey' received with size: " . $file->getSize());
                
                        if ($file->isValid()) {
                            $namaFile = $file->hashName();
                
                            if ($file->move($uploadPath, $namaFile)) {
                                $filePath = $namaFile;
                            } else {
                                return back()->with('error', 'Gagal memindahkan file ' . $fileKey . '. Silakan coba lagi.');
                            }
                        } else {
                            return back()->with('error', 'File ' . $fileKey . ' yang diunggah tidak valid.');
                        }
                    } else {
                        Log::info("File '$fileKey' not found in request.");
                    }
                }


                $reqData = $request->except(['file_ktp', 'foto_lokasi_usaha', 'file_npwp']); 
                $reqData['user_id'] = $user->id;
                $reqData['name']    = $user->fullname;
                $reqData['status_agen']    = 0;
                $reqData['file_ktp'] = $filePaths['file_ktp'];
                $reqData['foto_lokasi_usaha'] = $filePaths['foto_lokasi_usaha'];
                $reqData['file_npwp'] = $filePaths['file_npwp'];
                $reqData['tgl_perjanjian'] =now();
                $data   = $this->repository->create($reqData);

                $checkGroup = Group::where('name','Agen')->first();
                if($checkGroup){
                    $checkUG = UserGroup::where('user_id',$user->id)
                                        ->where('group_id',$checkGroup->id)
                                        ->first();
                    if(!$checkUG){
                        $userGroup = UserGroup::create([
                            'user_id'   => $user->id,
                            'group_id'  => $checkGroup->id
                        ]);
                    }
                }

                $assesmentResult = AssesmentResult::create([
                    'user_id'   => $user->id,
                    'catatan'   => $request->catatan,
                    'total_poin' => 0,
                ]);

                $totalPoin = 0;

                foreach ($request->answer as $pertanyaan_id  => $answer) {
                    $assesment = Assesment::find($pertanyaan_id );

                    if ($answer === 'yes') {
                        $poin = $assesment->poin;
                        $totalPoin += $poin;
                    } else {
                        $poin = 0;
                    }

                    AssesmentResultDetail::create([
                        'assesment_id'  => $assesmentResult->id,
                        'pertanyaan_id' => $assesment->id,
                        'poin'          => $poin,
                    ]);
                }

                $assesmentResult->update(['total_poin' => $totalPoin]);

                DB::commit();
                return Redirect::to('agen')
                                ->with('success', 'Agen berhasil didaftarkan, Mohon menunggu konfirmasi dari Supervisor Cabang');
            } catch (Exception $e) {
                DB::rollBack();
                    return Redirect::to('agen/create')
                                ->with('error', $e->getMessage())
                                ->withInput();
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                    return Redirect::to('agen/create')
                                ->with('error', $e->getMessage())
                                ->withInput();
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
            'status'  => true,
            'message' => 'Success',
            'data'    => $data,
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
        $merchant = Merchant::find($id);
        if($merchant){
            $user = User::find($merchant->user_id);
            $provinsi = CompOption::where('comp_id', 'CIF14')
                    ->orderBy('opt_label')
                    ->get();
                    
            $kota_kabupaten = CompOption::where('comp_id', 'CIF13')
                        ->orderBy('opt_label')
                        ->get();

            $provinsi = $provinsi->sortBy(function($item) {
                return $item->opt_label === 'Lain-Lain' ? 'zzzz' : $item->opt_label;
            });

            $kota_kabupaten = $kota_kabupaten->sortBy(function($item) {
                return $item->opt_label === 'Lain-Lain' ? 'zzzz' : $item->opt_label;
            });
            return view('apps.merchants.edit', compact('merchant', 'kota_kabupaten', 'provinsi', 'user'));
        }else{
            return Redirect::to('agen')
                            ->with('error', 'Data not found');
        }
    }

    public function detail_request($id)
    {
        $merchant = Merchant::find($id);

        if ($merchant) {
            $user = $merchant->user;

            $assesmentResult = $merchant->assesmentResult;

            if ($assesmentResult) {
                $assesmentDetails = AssesmentResultDetail::with('assesment')
                    ->where('assesment_id', $assesmentResult->id)
                    ->get();

                $totalPoints = $assesmentDetails->sum('poin');

                return view('apps.merchants.detail-request')
                    ->with('merchant', $merchant)
                    ->with('user', $user)
                    ->with('assesmentResult', $assesmentResult)
                    ->with('assesmentDetails', $assesmentDetails)
                    ->with('totalPoints', $totalPoints);
            } else {
                return redirect()->to('agen_request')
                                ->with('error', 'Assesment result not found');
            }
        } else {
            return redirect()->to('agen_request')
                            ->with('error', 'Data not found');
        }
    }

    public function detail_blocked($id)
    {
        $merchant = Merchant::find($id);
        if($merchant){
            $user = User::find($merchant->user_id);
            return view('apps.merchants.detail-blocked')
                ->with('merchant', $merchant)
                ->with('user', $user);
        }else{
            return Redirect::to('agen_blocked')
                            ->with('error', 'Data not found');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  MerchantUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    
    public function update(MerchantUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $data = $this->repository->update($request->all(), $id);
            
            if($data){
                $merchant = Merchant::find($id);
                if($merchant){
                    if($request->has('is_user_mireta')){
                        $user = User::find($merchant->user_id);
                        if($user){
                            $user->is_user_mireta = $request->is_user_mireta;
                            $user->save();
                        }
                    }

                    $terminal = Terminal::where('tid',$merchant->terminal_id)->first();
                    
                    if($terminal){
                        $terminal->merchant_name            = $merchant->name;
                        $terminal->merchant_address         = $merchant->address;
                        $terminal->merchant_account_number  = $merchant->no;
                        $terminal->save();
                    }

                    if($request->has('status_agen')){
                        $merchant->status_agen = $request->status_agen;
                        if($request->status_agen == 1){
                            $merchant->active_at = date("Y-m-d H:m:s");
                        } else if($request->status_agen == 2){
                            $merchant->resign_at = date("Y-m-d H:m:s");
                            if($terminal){
                                $terminalBilliton = TerminalBilliton::where('terminal_imei', $terminal->imei)->first();
                                if($terminalBilliton){
                                    $date = date("YmdHms");
                                    $data = TerminalBilliton::where('terminal_imei', $terminal->imei)
                                                    ->update(['terminal_imei' => $date.$terminalBilliton->terminal_imei,
                                                              'terminal_name' => $date.$terminalBilliton->terminal_name]);
                                    
                                    $merchant->mid                       = $date.$terminalBilliton->merchant_id;
                                    $terminal->serial_number             = $date.$terminalBilliton->terminal_name;
                                    $terminal->imei                      = $date.$terminalBilliton->terminal_imei;
                                    $terminal->merchant_id               = $date.$terminalBilliton->merchant_id;
                                    $terminal->save();
                                }
                            }
                        }
                    }

                    $merchant->save();

                }
                DB::commit();
                return Redirect::to('agen/list')
                                    ->with('message', 'Agen updated');
            }else{
                DB::rollBack();
                return Redirect::to('agen/'.$id.'/edit')
                            ->with('error', $data->error)
                            ->withInput();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('agen/'.$id.'/edit')
                        ->with('error', $e)
                        ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('agen/'.$id.'/edit')
                        ->with('error', $e)
                        ->withInput();
        }
    }

    public function activateMerchant(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            
            $merchant = Merchant::where('id', $id)->first();
            
            if (!$merchant) {
                throw new \Exception("Agen not found");
            }

            $user_login = session()->get('user');

            if ($merchant->status_agen == 0) {
                $branchid = $user_login->branchid;

                $prefix = 'NTB';
                $lastMID = Merchant::where('mid', 'LIKE', $prefix . $branchid .'%')
                                ->orderBy('created_at', 'desc')
                                ->first();
                
                $newMid = $prefix . $branchid . '000001';
                
                if ($lastMID) {
                    $lastMid = $lastMID->mid;
                    $number = substr($lastMid, strlen($prefix . $branchid));
                    $newNumber = (int)$number + 1;
                    $newNumberPadded = str_pad($newNumber, 6, '0', STR_PAD_LEFT);;
                    $newMid = $prefix . $branchid . $newNumberPadded;
                }

                while (Merchant::where('mid', $newMid)->exists()) {
                    $newNumber = (int)substr($newMid, strlen($prefix . $branchid)) + 1;
                    $newMid = $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
                }

                $generatePin =  implode('', array_map(function() { return mt_rand(0, 9); }, range(1, 6)));

                $merchant->mid = $newMid;
                $merchant->pin = $generatePin;
                $merchant->status_agen = 1;
                $merchant->active_at = now();
                $merchant->tgl_pelaksanaan = now();
                $merchant->save();

                $user = User::where('id', $merchant->user_id)->first();
                
                if (!$user) {
                    throw new \Exception("User not found");
                }

                $password = implode('', array_merge(
                    array_map(function() { return chr(mt_rand(65, 90)); }, range(1, 3)),
                    array_map(function() { return chr(mt_rand(97, 122)); }, range(1, 3)), 
                    array_map(function() { return mt_rand(0, 9); }, range(1, 2))
                ));

                $password = str_shuffle($password);
                $passwordBcrypt = bcrypt($password);
                
                Log::info('PasswordGenerate: ' . $password);

                $prefix = 'NTBUS'; 
                $lastUser = User::where('username', 'LIKE', $prefix . '%')
                                ->orderBy('created_at', 'desc')
                                ->first();
                
                $newUsername = $prefix . '000001'; 
                
                if ($lastUser) {
                    $lastUsername = $lastUser->username;
                    $number = substr($lastUsername, strlen($prefix));
                    $newNumber = (int)$number + 1;
                    $newNumberPadded = str_pad($newNumber, 6, '0', STR_PAD_LEFT);
                    $newUsername = $prefix . $newNumberPadded;
                }

                while (User::where('username', $newUsername)->exists()) {
                    $newNumber = (int)substr($newUsername, strlen($prefix)) + 1;
                    $newUsername = $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
                }

                

                $user->password = $passwordBcrypt;
                $user->status = 1;
                $user->approval_by = $user_login->username;
                $user->username = $newUsername;
                $user->save();


                $pesan = '<p>Halo ' . htmlspecialchars($user->fullname) . ',</p>';
                $pesan .= '<p>Pendaftaran Anda telah kami setujui, Anda telah terdaftar sebagai Agen LAKUPANDAI.</p>';
                $pesan .= '<p>Berikut informasi Anda yang telah terdaftar sebagai Agen LAKUPANDAI:</p>';
                $pesan .= '<p>ID Agen: ' . htmlspecialchars($merchant->mid) . '</p>';
                $pesan .= '<p>Username: ' . htmlspecialchars($user->username) . '</p>';
                $pesan .= '<p>Password: ' . htmlspecialchars($password) . '</p>';
                $pesan .= '<p>Pin Transaksi: ' . htmlspecialchars($merchant->pin) . '</p>';
                $pesan .= '<p>Gunakan Username dan Pin Transaksi di atas untuk mengakses halaman Bank.</p>';
                $pesan .= '<p>Salam Hangat,</p>';
                $pesan .= '<p><b>NTBS LAKUPANDAI</b></p>';

                $detail_message = [
                    'sender' => 'administrator@selada.id',
                    'subject' => '[NTBS LAKUPANDAI] Pendaftaran Agen LAKUPANDAI Berhasil',
                    'isi' => $pesan
                ];

                Mail::to($user->email)->send(new sendPassword($detail_message));
                DB::commit();
                return redirect()->route('agen')->with('success', 'Agen Berhasil Diaktifkan');
            }

            else if ($merchant->status_agen == 2) {
                $merchant->status_agen = 1;
                $merchant->resign_at = null;
                $merchant->active_at = now();
                $merchant->save();

                $user = User::where('id', $merchant->user_id)->first();
                
                if (!$user) {
                    throw new \Exception("User not found");
                }

                $user->status = 1;
                $user->save(); 
                DB::commit();
                return redirect()->route('agen_list')->with('success', 'Agen Berhasil Diaktifkan');
            }
            else if ($merchant->status_agen == 3) {
                $merchant->status_agen = 1;
                $merchant->resign_at = null;
                $merchant->active_at = now();
                $merchant->save();

                $user = User::where('id', $merchant->user_id)->first();
                
                if (!$user) {
                    throw new \Exception("User not found");
                }

                $user->status = 1;
                $user->save(); 
                DB::commit();
                return redirect()->route('agen')->with('success', 'Agen Berhasil Diaktifkan');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Database error during agent activation', ['error' => $e->getMessage()]);
            return redirect()->route('agen')->with('failed', 'Gagal Mengaktifkan Agen:' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent activation failed', ['error' => $e->getMessage()]);
            return redirect()->route('agen')->with('failed', 'Gagal Mengaktifkan Agen:' . $e->getMessage());
        }
    }

    public function rejectAgent(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $merchant = Merchant::find($id);

            if (!$merchant) {
                return redirect()->back()->with('error', 'Agen tidak ditemukan');
            }

            $pesan = '<p>Halo ' . htmlspecialchars($merchant->name) . ',</p>';
            $pesan .= '<p>Pendaftaran Anda kami tolak karena tidak memenuhi persyaratan sebagai Agen.</p>';
            $pesan .= '<p>Anda dapat melakukan pendaftaran kembali dengan mengunjungi cabang terdekat kami.</p>';
            $pesan .= '<p>Salam Hangat,</p>';
            $pesan .= '<p><b>NTBS LAKUPANDAI</b></p>';

            $detail_message = [
                'sender' => 'administrator@selada.id',
                'subject' => '[NTBS LAKUPANDAI] Pendaftaran Agen LAKUPANDAI Gagal',
                'isi' => $pesan
            ];

            Mail::to($merchant->email)->send(new sendPassword($detail_message));

            $user = User::find($merchant->user_id);
            if ($user) {
                $user->delete();
            }

            $merchant->delete();

            DB::commit();
            return redirect()->route('agen_request')->with('success', 'Penolakan Agen berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('agen_request')->with('error', 'Gagal menghapus agen: ' . $e->getMessage());
        }
    }

    public function deactivateMerchant(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $merchant = Merchant::where('id', $id)->first();
            
            if (!$merchant) {
                throw new \Exception("Agen not found");
            }

            if ($merchant->status_agen == 1) {
                $merchant->status_agen = 2;
                $merchant->resign_at = now();
                $merchant->save();
            }

            $user = User::where('id', $merchant->user_id)->first();
            
            if ($user) {
                $user->status = 2;
                $user->save();
            }

            DB::commit();
            return redirect()->route('agen_list')->with('success', 'Agen deactivated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent deactivation failed', ['error' => $e->getMessage()]);
            return redirect()->route('agen_list')->with('failed', 'Deactivation failed: ' . $e->getMessage());
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
            $deleted = $this->repository->delete($id);

            if($deleted){
                $response = [
                    'status'  => true,
                    'message' => 'Agen deleted.'
                ];
    
                DB::commit();
                return response()->json($response, 200);
            }
            
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'    => false, 
                'error'     => 'Something wrong!',
                'exception' => $e
            ], 500);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            return response()->json([
                'status'    => false, 
                'error'     => 'Something wrong!',
                'exception' => $e
            ], 500);
        }
    }

    public function blockAgen(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $merchant = Merchant::where('id', $request->id)->first();
            
            if (!$merchant) {
                throw new \Exception("Agen tidak ditermukan");
            }

            if ($merchant->status_agen == 1) {
                $merchant->status_agen = 3;
                $merchant->save();
            }

            $user = User::where('id', $merchant->user_id)->first();
            
            if ($user) {
                $user->status = 3;
                $user->save();
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Agen Terblokir.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Blokir Agen Gagal', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Blokir Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function blockAgenLogin(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $user = User::where('username', $request->username)->first();
            
            if (!$user) {
                throw new \Exception("User tidak ditermukan");
            }

            if ($user->status == 1) {
                $user->status = 3;
                $user->save();
            }

            $merchant = Merchant::where('user_id', $user->id)->first();
            
            if ($merchant) {
                $merchant->status_agen = 3;
                $merchant->save();
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Agen Terblokir.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Blokir Agen Gagal', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Blokir Gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMerchant(Request $request){
        $data = Merchant::where('no',$request->no)->first();
        
        if($data){
            $response = [
                'status'  => true,
                'message' => 'Success',
                'data'    => $data,
            ];

            return response()->json($response, 200);
        }else{
            $response = [
                'status'  => false,
                'message' => 'Not found'
            ];

            return response()->json($response, 404);
        }
    }

    public function exportPDF(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $merchants = Merchant::where('status_agen', '!=', 0)->get();
        $pdf = Pdf::loadView('pdf.merchants', ['merchants' => $merchants])
                ->setPaper('a4', 'landscape');
        return $pdf->download('Data Agen.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new MerchantsExport(Merchant::query(), 1), 'Data Agen.xlsx');
    }    
}
