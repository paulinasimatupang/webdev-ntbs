<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use Validator;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\MerchantCreateRequest;
use App\Http\Requests\MerchantUpdateRequest;
use App\Repositories\MerchantRepository;
use App\Validators\MerchantValidator;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;


use App\Entities\Merchant;
use App\Entities\Role;
use App\Entities\User;
use App\Entities\Terminal;
use App\Entities\Group;
use App\Entities\TerminalBilliton;
use App\Entities\UserGroup;


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
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = Merchant::select('*')
                ->whereIn('status_agen', [1, 2]);

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
            $data->orderBy('created_at', 'desc');
        }

        $data = $data->get();

        foreach($data as $merchant) {
            if ($merchant->status_agen == 1){
                $merchant->status_text = 'Active';
            } else {
                $merchant->status_text = 'Resign';
            }

            if($merchant->active_at == null || $merchant->active_at == ''){
                $merchant->active_at = '-';
            }

            if ($merchant->resign_at == null || $merchant->resign_at == ''){
                $merchant->resign_at = '-';
            }
        }

        $user = session()->get('user');
        // echo $user->username; die;

        return view('apps.merchants.list')
                ->with('data', $data)
                ->with('username', $user->username);
    }

    public function request_list(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = Merchant::select('*');

        $data = $data->where('status_agen', 0);

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
            $data->orderBy('created_at', 'desc');
        }

        $data = $data->get();

        $user = session()->get('user');

        return view('apps.merchants.list-request')
            ->with('data', $data)
            ->with('username', $user->username);
    }
    
    public function create(Request $request){

        // $terminal = Terminal::where('merchant_id',null)->orderBy('id')->get();

        return view('apps.merchants.add');
                // ->with('terminal', $terminal);
    }

    public function inquiry_nik(Request $request){
        return view('apps.merchants.inquiry-nik');
    }

    public function store_inquiry_nik(Request $request)
    {
        // Inquiry CIF By NIK
        $nik = $request->input('nik');
        $terminal = '353471045058692';
        $dateTime = date("YmdHms");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://108.137.154.8:8080/ARRest/api/");
        $data = json_encode([
            'msg'=>([
            'msg_id' =>  "$terminal$dateTime",
            'msg_ui' => "$terminal",
            'msg_si' => 'INF002',
            'msg_dt' => 'admin|'. $nik
            ])
        ]);
       
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/plain'
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
        $email = null;
        $no_hp = null;
        $no_registrasi = null;

        if ($err) {
            Log::error('cURL Error: ' . $err);
        } else {
            if (isset($responseArray['screen']['comps']['comp'])) {
                foreach ($responseArray['screen']['comps']['comp'] as $comp) {
                    if (isset($comp['comp_values']['comp_value'][0]['value'])) {
                        $value = $comp['comp_values']['comp_value'][0]['value'];
        
                        // Assign only if value is not null or 'null'
                        if ($value !== 'null' && $value !== null) {
                            switch ($comp['comp_lbl']) {
                                case 'No CIF':
                                    $cifid = $value;
                                    $match = Merchant::where('no_cif', $cifid)->exists();
                                    break;
        
                                case 'Nama Rekening':
                                    $nama_rek = $value;
                                    break;
        
                                case 'Alamat':
                                    $alamat = $value;
                                    break;
        
                                case 'Email':
                                    $email = $value;
                                    break;
        
                                case 'Nomor Handphone':
                                    $no_hp = $value;
                                    break;
        
                                case 'Kode Registrasi':
                                    $no_registrasi = $value;
                                    break;
                            }
                        }
                    }
                }
            }
        }
        
        if ($match){
            return Redirect::to('/merchant/create/inquiry')->with('error', "Merchant Sudah Terdaftar CIF Gagal");
        }
        else if (isset($responseArray['screen']['title']) && $responseArray['screen']['title'] === 'Gagal') {
            return Redirect::to('/merchant/create/cif')
                            ->with('no_identitas', $nik);
        } else {
            return Redirect::to('/merchant/create')
                            ->with('no_cif', $cifid)
                            ->with('fullname', $nama_rek)
                            ->with('address', $alamat)
                            ->with('email', $email)
                            ->with('phone', $no_hp)
                            ->with('no_registrasi', $no_registrasi);
        }
    }

    public function create_cif(Request $request){
        return view('apps.merchants.add-cif');
    }


    public function store_cif(Request $request){
        $status_penduduk = $request->input('status_penduduk');
        $kewarganegaraan = $request->input('kewarganegaraan');
        $nama_lengkap = $request->input('nama_lengkap');
        $nama_alias = $request->input('nama_alias');
        $ibu_kandung = $request->input('ibu_kandung');
        $tempat_lahir = $request->input('tempat_lahir');
        $tgl_lahir = $request->input('tgl_lahir');
        $jenis_kelamin = $request->input('jenis_kelamin');
        $agama = $request->input('agama');
        $status_nikah = $request->input('status_nikah');
        $alamat = $request->input('alamat');
        $rt = $request->input('rt');
        $rw = $request->input('rw');
        $kecamatan = $request->input('kecamatan');
        $kelurahan = $request->input('kelurahan');
        $kab_kota = $request->input('kab_kota');
        $provinsi = $request->input('provinsi');
        $kode_pos = $request->input('kode_pos');
        $no_telp = $request->input('no_telp');
        $no_hp = $request->input('no_hp');
        $npwp = $request->input('npwp');
        $jenis_identitas = $request->input('jenis_identitas');
        $no_identitas = $request->input('no_identitas');
        $golongan_darah = $request->input('golongan_darah');
        $expired_identitas = $request->input('expired_identitas');
        $pendidikan_terakhir = $request->input('pendidikan_terakhir');
        $email = $request->input('email');
        $branchid = $request->input('branchid');

        $terminal = '353471045058692';
        $dateTime = date("YmdHms");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://108.137.154.8:8080/ARRest/api/");
        $data = json_encode([
            'msg'=>[
                'msg_id' =>  "$terminal$dateTime",
                'msg_ui' => "$terminal",
                'msg_si' => 'CC0001',
                'msg_dt' => 'admin|' . $no_identitas . '|' . $nama_lengkap . '|' . $nama_alias . '|' . $ibu_kandung . '|' . $tempat_lahir . '|' . $tgl_lahir . '|' . $jenis_kelamin . '|' 
                    . $agama . '|' . $status_nikah . '|' . $alamat . '|' . $rt . '|' . $rw . '|' . $kecamatan . '|' . $kelurahan . '|'. $kab_kota . '|' . $provinsi . '|'. $kode_pos . '|' . $status_penduduk . '|'
                    . $kewarganegaraan . '|' . $no_telp . '|' . $no_hp . '|' . $npwp . '|' . $jenis_identitas . '|' . $golongan_darah . '|' . $expired_identitas . '|' . $pendidikan_terakhir . '|'
                    . $email . '|' . $branchid
            ]
        ]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/plain'
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
        
        $responseArray = json_decode($output, true);

        if ($err) {
            Log::error('cURL Error: ' . $err);
        }else {
            $cifid = null;
            if (isset($responseArray['screen']['comps']['comp'])) {
                foreach ($responseArray['screen']['comps']['comp'] as $comp) {
                    if ($comp['comp_lbl'] === 'No CIF') {
                        $cifid = $comp['comp_values']['comp_value'][0]['value'];
                    }
                }
            }
        }

        if (isset($responseArray['screen']['title']) && $responseArray['screen']['title'] === 'Gagal') {
            return Redirect::to('/merchant/create/cif')->with('error', "Create CIF Gagal")
            ->withInput();
        } else {
            $inquiryRequest = new Request(['nik' => $no_identitas]);
            $inquiryResponse = $this->store_inquiry_nik($inquiryRequest);

            return Redirect::to('/merchant/create/rekening')
                            ->with('nama_lengkap', $nama_lengkap)
                            ->with('branchid', $branchid)
                            ->with('no_cif', $cifid)
                            ->with('inquiry_response', json_encode($inquiryResponse));
        }
        
    }

    public function create_rekening(Request $request){
        return view('apps.merchants.add-rekening');
    }

    public function store_rekening(Request $request){
        $nama_lengkap = $request->input('nama_lengkap');
        $no_cif = $request->input('no_cif');
        $kode_produk = $request->input('kode_produk');
        $no_registrasi = $request->input('no_registrasi');
        $branchid = $request->input('branchid');

        $terminal = '353471045058692';
        $dateTime = date("YmdHms");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://108.137.154.8:8080/ARRest/api/");
        $data = json_encode([
            'msg'=>[
                'msg_id' =>  "$terminal$dateTime",
                'msg_ui' => "$terminal",
                'msg_si' => 'CRS001',
                'msg_dt' => 'admin|' . $nama_lengkap . '|' . $no_cif . '|' . $kode_produk . '|' . $no_registrasi . '|' . $branchid
            ]
        ]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/plain'
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

        $responseArray = json_decode($output, true);

        if ($err) {
            Log::error('cURL Error: ' . $err);
        
        }else {
            $noRekening = null;
            $noCIF = null;
            if (isset($responseArray['screen']['comps']['comp'])) {
                foreach ($responseArray['screen']['comps']['comp'] as $comp) {
                    if ($comp['comp_lbl'] === 'No Rekening') {
                        $noRekening = $comp['comp_values']['comp_value'][0]['value'];
                    }
                    else if ($comp['comp_lbl'] === 'No CIF') {
                        $noCIF = $comp['comp_values']['comp_value'][0]['value'];
                    }
                }
            }
        }

        if (isset($responseArray['screen']['title']) && $responseArray['screen']['title'] === 'Gagal') {
            return Redirect::to('/merchant/create/rekening')->with('error', "Rekening Gagal Terdaftar")->withInput();
        } else {
            return Redirect::to('/merchant/create')
                            ->with('no', $noRekening)
                            ->with('no_cif', $noCIF)
                            ->with('no_registrasi', $no_registrasi)
                            ->with('fullname', $nama_lengkap)
                            ->with('branchid', $branchid);
        }
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

    public function store(Request $request){
        DB::beginTransaction();
            try {
                $check = User::where('username', $request->username)
                                ->orWhere('email',$request->email)
                                ->first();
                if($check){
                    return response()->json([
                        'status'=> false, 
                        'error'=> 'Username or Email already used'
                    ], 403);
                }

                if ($request->has('role_id')) {
                    $role_id = $request->role_id;
                } else {
                    $role = Role::where('name', 'Merchant')->first();
                    if ($role) {
                        $role_id = $role->id;
                    } else {
                        return response()->json([
                            'status' => false, 
                            'error' => 'Role not found'
                        ], 404);
                    }
                }
                
                $user = User::create([
                                'role_id'           => $role_id,
                                'username'          => $request->username,
                                'fullname'          => $request->fullname,
                                'email'             => $request->email,
                                'password'          => bcrypt($request->password),
                                'status'            => 0
                            ]);

                $uploadPath = public_path('uploads/');
                $filePaths = [
                    'file_ktp' => null,
                    'file_kk' => null,
                    'file_npwp' => null
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


                // $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
                $reqData = $request->except(['file_ktp', 'file_kk', 'file_npwp']); 
                $reqData['user_id'] = $user->id;
                $reqData['name']    = $user->fullname;
                $reqData['status_agen']    = 0;
                $reqData['file_ktp'] = $filePaths['file_ktp'];
                $reqData['file_kk'] = $filePaths['file_kk'];
                $reqData['file_npwp'] = $filePaths['file_npwp'];
                $reqData['mid'] = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

                $data   = $this->repository->create($reqData);

                // Create to user_groups
                $checkGroup = Group::where('name','Agent')->first();
                if($checkGroup){
                    $checkUG = UserGroup::where('user_id',$user->id)
                                        ->where('group_id',$checkGroup->id)
                                        ->first();
                    if(!$checkGroup){
                        $userGroup = UserGroup::create([
                            'user_id'   => $user->id,
                            'group_id'  => $checkGroup->id
                        ]);
                    }
                }
                DB::commit();
                return Redirect::to('merchant')
                                ->with('message', 'Merchant created');
            } catch (Exception $e) {
                DB::rollBack();
                    return Redirect::to('merchant/create')
                                ->with('error', $e)
                                ->withInput();
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                    return Redirect::to('merchant/create')
                                ->with('error', $e)
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
            return view('apps.merchants.edit')
                ->with('merchant', $merchant)
                ->with('user', $user);
        }else{
            return Redirect::to('terminal')
                            ->with('error', 'Data not found');
        }
    }

    public function detail_request($id)
    {
        $merchant = Merchant::find($id);
        if($merchant){
            $user = User::find($merchant->user_id);
            return view('apps.merchants.detail-request')
                ->with('merchant', $merchant)
                ->with('user', $user);
        }else{
            return Redirect::to('terminal')
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
                return Redirect::to('merchant')
                                    ->with('message', 'Merchant updated');
            }else{
                DB::rollBack();
                return Redirect::to('merchant/'.$id.'/edit')
                            ->with('error', $data->error)
                            ->withInput();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('merchant/'.$id.'/edit')
                        ->with('error', $e)
                        ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('merchant/'.$id.'/edit')
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
                throw new \Exception("Merchant not found");
            }

            if ($merchant->status_agen == 2 || $merchant->status_agen == 0) {
                $merchant->status_agen = 1;
                $merchant->resign_at = null; // Reset resignation date
                $merchant->save();
            } 

            $user = User::where('id', $merchant->user_id)->first();
            
            if ($user) {
                $user->status = 1;
                $user->save();
            }

            DB::commit();
            return redirect()->route('merchant')->with('success', 'Merchant activated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent activation failed', ['error' => $e->getMessage()]);
            return redirect()->route('merchant')->with('failed', 'Activation failed: ' . $e->getMessage());
        }
    }

    public function deactivateMerchant(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $merchant = Merchant::where('id', $id)->first();
            
            if (!$merchant) {
                throw new \Exception("Merchant not found");
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
            return redirect()->route('merchant')->with('success', 'Merchant deactivated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent deactivation failed', ['error' => $e->getMessage()]);
            return redirect()->route('merchant')->with('failed', 'Deactivation failed: ' . $e->getMessage());
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
                    'message' => 'Merchant deleted.'
                ];
    
                DB::commit();
                return response()->json($response, 200);
            }
            
        } catch (Exception $e) {
            // For rollback data if one data is error
            DB::rollBack();

            return response()->json([
                'status'    => false, 
                'error'     => 'Something wrong!',
                'exception' => $e
            ], 500);
        } catch (\Illuminate\Database\QueryException $e) {
            // For rollback data if one data is error
            DB::rollBack();

            return response()->json([
                'status'    => false, 
                'error'     => 'Something wrong!',
                'exception' => $e
            ], 500);
        }
    }

    /**
     * Get number from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function lastestNumber(Request $request)
    {
        DB::beginTransaction();
        try {
            $merchant = Merchant::select('no')->orderBy('no','DESC')->first();

            $response = [
                'status'  => true,
                'message' => 'Success',
                'data'    => $merchant
            ];

            DB::commit();
            return response()->json($response, 200);
        } catch (Exception $e) {
            // For rollback data if one data is error
            DB::rollBack();

            return response()->json([
                'status'    => false, 
                'error'     => 'Something wrong!',
                'exception' => $e
            ], 500);
        } catch (\Illuminate\Database\QueryException $e) {
            // For rollback data if one data is error
            DB::rollBack();

            return response()->json([
                'status'    => false, 
                'error'     => 'Something wrong!',
                'exception' => $e
            ], 500);
        }
    }

    /**
     * Set balance the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function updateBalance(Request $request)
    {
        try {
            $merchant = Merchant::where('no',$request->no)->first();
            if($merchant){
                $merchant->balance = $request->balance;
                $merchant->save();

                DB::commit();
                return response()->json([
                    'status' => true
                ], 200);
            }else{
                DB::rollBack();
                return response()->json([
                    'status' => false
                ], 200);
            }
        } catch (Exception $e) {
            // For rollback data if one data is error
            DB::rollBack();
            return response()->json([
                'status' => false
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // For rollback data if one data is error
            DB::rollBack();
            return response()->json([
                'status' => false
            ], 200);
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
}