<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\MerchantCreateRequest;
use App\Http\Requests\MerchantUpdateRequest;
use App\Repositories\MerchantRepository;
use App\Validators\MerchantValidator;
use Ixudra\Curl\Facades\Curl;

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

        $data = Merchant::select('*');

//	$data = $data->whereHas('user', function($query){
//		$query->where('is_user_mireta', '!=', 1)->orWhereNull('is_user_mireta');
//	});

	    $data = $data->whereHas('user', function($query){
                $query->where(function($q)
                {
                    $q->where('is_user_mireta', '!=', 1)->orWhereNull('is_user_mireta');
                });
                $query->where(function($q)
                {
                    $q->where('is_development_user', '!=', 1)->orWhereNull('is_development_user');
                });
        });


//	$data = $data->with('user')->where('is_user_mireta', '!=', 't');

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
            if($merchant->status_agen == 0){
                $merchant->status_text = 'Pending';
            } else if ($merchant->status_agen == 1){
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
    
    public function create(Request $request){

        // $terminal = Terminal::where('merchant_id',null)->orderBy('id')->get();

        return view('apps.merchants.add');
                // ->with('terminal', $terminal);
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
    public function store(MerchantCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $check = User::where('username', $request->username)
                            ->orWhere('email',$request->email)
                            ->first();
            if($check){
                return response()->json([
                    'status'=> false, 
                    'error'=> 'Username already used'
                ], 403);
            }

            //check for role merchant
            $role = Role::where('name','Merchant')->first();
            if($role){
                $role_id = $role->id;
            }else{
                return response()->json([
                    'status'=> false, 
                    'error'=> 'Role not found'
                ], 404);
            }
            
            $user = User::create([
                            'role_id'           => $role_id,
                            'username'          => $request->username,
                            'fullname'          => $request->fullname,
                            'email'             => $request->email,
                            'password'          => bcrypt($request->password),
                            'is_user_mireta'    => $request->is_user_mireta,
                        ]);

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
            $reqData = $request->all();
            $reqData['user_id'] = $user->id;
            $reqData['name']    = $user->fullname;
            $reqData['status_agen']    = 0;
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
            
            if($data){
                // $terminal = Terminal::where('tid',$reqData['terminal_id'])->first();
                // if($terminal){
                //     $terminal->merchant_id              = $data->mid;
                //     $terminal->merchant_name            = $data->name;
                //     $terminal->merchant_address         = $data->address;
                //     $terminal->merchant_account_number  = $data->no;
                //     $terminal->save();
                // }

                //add to mireta
                $reqData = [
                    'username'              => $request->username,
                    'email'                 => $request->email,
                    'password'              => $request->password,
                    'fullname'              => $request->fullname,
                    'registration_email'    => $request->email,
                    'brand_name'            => $request->fullname,
                    'store_name'            => $request->fullname,
                    'store_address'         => $request->address,
                    'store_phone'           => $request->phone,
                ];
                
                $responseCurl = Curl::to(config('app.url_mireta'))
                                    ->withData($reqData)
                                    ->asJson()
                                    ->post();
                
                if($responseCurl->status == true){   
                    DB::commit();
                    return Redirect::to('merchant')
                        ->with('message', 'Merchant created');
                }else{
                    DB::rollBack();
                    return Redirect::to('merchant/create')
                        ->with('error', 'Failed to save mireta')
                        ->withInput();
                }
            }else{
                DB::rollBack();
                return Redirect::to('merchant/create')
                            ->with('error', $data->error)
                            ->withInput();
            }
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
