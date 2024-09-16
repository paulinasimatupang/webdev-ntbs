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

        return view('apps.merchants.menu', compact('jumlah_request'));
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
                $merchant->status_text = 'Active';
            } else if ($merchant->status_agen == 2){ 
                $merchant->status_text = 'Deactive';
            } else if ($merchant->status_agen == 3){ 
                $merchant->status_text = 'Blocked';
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

    public function cek_saldo($norek, $nama){
        $terminal = '353471045058692';
        $dateTime = date("YmdHms");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://108.137.154.8:8080/ARRest/api/");
        $data = json_encode([
            'msg'=>([
            'msg_id' =>  "$terminal$dateTime",
            'msg_ui' => "$terminal",
            'msg_si' => 'N00001',
            'msg_dt' => 'admin|'. $norek .'|'. $nama .'|null'
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
        
        $prodId = null;

        if ($err) {
            Log::error('cURL Error: ' . $err);
        } else {
            if (isset($responseArray['screen']['comps']['comp'])) {
                foreach ($responseArray['screen']['comps']['comp'] as $comp) {
                    if (isset($comp['comp_values']['comp_value'][0]['value'])) {
                        $value = $comp['comp_values']['comp_value'][0]['value'];
                        if ($value !== 'null' && $value !== null) {
                            switch ($comp['comp_lbl']) {
                                case 'ProdID':
                                    $prodId = $value;
                                    break;
                            }
                        }
                    }
                }
            }
        }
        if ($prodId === '36'){
            return "Nomor Rekening yang Anda Masukkan Tidak Dapat Didaftarkan Sebagai Agen";
        }
        else if (isset($responseArray['screen']['title']) && $responseArray['screen']['title'] === 'Gagal') {
            return  "Pengecekan Rekening Gagal";
        }
        else {
            return true;
        }
    }

    public function store_inquiry_rek(Request $request)
    {
        $rek = $request->input('rek');
        $terminal = '353471045058692';
        $dateTime = date("YmdHms");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://108.137.154.8:8080/ARRest/api/");
        $data = json_encode([
            'msg'=>([
            'msg_id' =>  "$terminal$dateTime",
            'msg_ui' => "$terminal",
            'msg_si' => 'INF001',
            'msg_dt' => 'admin|'. $rek
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
        $norek = null;
        $no_hp = null;

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
                                    // $match = Merchant::where('no', $norek)->exists();
                                    $match=null;
                                    break;
        
                                case 'Nomor Handphone':
                                    $no_hp = $value;
                                    break;
                            }
                        }
                    }
                }
            }
        }
        
        if ($match){
            return Redirect::to('/agen/create/inquiry')->with('error', "Merchant dengan Nomor Rekening yang diinputkan Sudah Terdaftar")->withInput();
        }
        else if (isset($responseArray['screen']['title']) && $responseArray['screen']['title'] === 'Gagal') {
            return Redirect::to('/agen/create/inquiry')
                            ->with('error', "No Rekening Belum Terdaftar")
                            ->withInput();
        } else {
            $cekSaldoResult = $this->cek_saldo($norek, $nama_rek);
            if ($cekSaldoResult !== true) {
                return Redirect::to('/agen/create/inquiry')->with('error', $cekSaldoResult)->withInput();
            }

            return Redirect::to('/agen/create')
            ->with('no_cif', $cifid)
            ->with('fullname', $nama_rek)
            ->with('address', $alamat)
            ->with('no', $norek)
            ->with('phone', $no_hp);
        }
    }

    public function create(Request $request)
    {
        $assessments = Assesment::all();
        
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

        return view('apps.merchants.add', compact('assessments', 'kota_kabupaten', 'provinsi'));
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
                $check = User::where('email',$request->email)
                                ->first();
                if($check){
                    return Redirect::to('agen/create')
                    ->with('error', 'Email Sudah Terdaftar')
                    ->withInput();
                }

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
                                ->with('message', 'Merchant created');
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
            return Redirect::to('agen_request')
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
                                    ->with('message', 'Merchant updated');
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
                throw new \Exception("Merchant not found");
            }

            $user = session()->get('user');

            if ($merchant->status_agen == 0) {
                $branchid = $user->branchid;

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
            }

            else if ($merchant->status_agen == 2 || $merchant->status_agen == 3) {
                $merchant->status_agen = 1;
                $merchant->resign_at = null;
                $merchant->save();

                $user = User::where('id', $merchant->user_id)->first();
                
                if (!$user) {
                    throw new \Exception("User not found");
                }

                $user->status = 1;
                $user->save();
            }

            DB::commit();
            return redirect()->route('agen')->with('success', 'Merchant activated successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Database error during agent activation', ['error' => $e->getMessage()]);
            return redirect()->route('agen')->with('failed', 'Activation failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent activation failed', ['error' => $e->getMessage()]);
            return redirect()->route('agen')->with('failed', 'Activation failed: ' . $e->getMessage());
        }
    }

    public function rejectAgent(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $merchant = Merchant::find($id);

            if (!$merchant) {
                return redirect()->back()->with('error', 'Merchant tidak ditemukan');
            }

            $user = User::find($merchant->user_id);
            if ($user) {
                $user->delete();
            }

            $merchant->delete();

            DB::commit();
            return redirect()->route('agen/request')->with('message', 'Agen berhasil di-reject dan dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('agen/request')->with('error', 'Gagal menghapus agen: ' . $e->getMessage());
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
            return redirect()->route('agen/list')->with('success', 'Merchant deactivated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent deactivation failed', ['error' => $e->getMessage()]);
            return redirect()->route('agen/list')->with('failed', 'Deactivation failed: ' . $e->getMessage());
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
            // Returning a JSON response instead of redirecting
            return response()->json([
                'status' => true,
                'message' => 'Agen Terblokir.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Blokir Agen Gagal', ['error' => $e->getMessage()]);
            // Returning a JSON response on failure
            return response()->json([
                'status' => false,
                'message' => 'Blokir Gagal: ' . $e->getMessage()
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

    public function exportPDF(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $merchants = Merchant::all();
        $pdf = Pdf::loadView('pdf.merchants', ['merchants' => $merchants]);
        return $pdf->download('merchants.pdf');
    }

    public function exportCSV()
    {
        return Excel::download(new MerchantsExport(Merchant::query(), 1), 'merchants.csv');
    }

    public function exportExcel()
    {
        return Excel::download(new MerchantsExport(Merchant::query(), 1), 'merchants.xlsx');
    }
    
    public function exportTxt()
    {
        $merchants = Merchant::all();
        $txtData = '';

        foreach ($merchants as $merchant) {
            $txtData .= "ID: {$merchant->mid}, Name: {$merchant->name}, Email: {$merchant->email}\n";
        }

        $fileName = "merchants.txt";
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response($txtData, 200, $headers);
    }
 
    // public function sendNotification($fcm_token, $title, $message) {
    //     $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    
    //     $notification = [
    //         'title' => $title,
    //         'body' => $message,
    //         'sound' => 'default'
    //     ];
    
    //     $fcmNotification = [
    //         'to' => $fcm_token, // Token agen
    //         'notification' => $notification,
    //     ];
    
    //     $headers = [
    //         'Authorization: key=YOUR_SERVER_KEY', // Server key dari Firebase
    //         'Content-Type: application/json'
    //     ];
    
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $fcmUrl);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    //     $result = curl_exec($ch);
    //     curl_close($ch);
    
    //     return $result;
    // }
    
}
