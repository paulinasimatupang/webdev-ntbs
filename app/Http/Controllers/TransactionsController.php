<?php
// Connect to ARDI

namespace App\Http\Controllers;

use DB;
use Redirect;
use Exception;
use Validator;
use Carbon\Carbon;
use App\Entities\Bank;

use App\Http\Requests;
<<<<<<< Updated upstream
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TransactionCreateRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Repositories\TransactionRepository;
use App\Validators\TransactionValidator;
use Ixudra\Curl\Facades\Curl;

use App\Services\TransactionService;

use App\Entities\Merchant;
use App\Entities\Service;
use App\Entities\Transaction;
use App\Entities\TransactionStatus;
use App\Entities\transactionPaymentStatus;
=======
>>>>>>> Stashed changes
use App\Entities\Group;
use App\Entities\Service;
use App\Entities\Merchant;
use App\Entities\UserGroup;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Entities\GroupSchema;

use App\Entities\Transaction;
use Ixudra\Curl\Facades\Curl;
use App\Entities\TransactionBJB;
use App\Entities\TransactionLog;
use App\Entities\TerminalBilliton;
use App\Exports\TransactionExport;
use App\Entities\TransactionStatus;
use Illuminate\Support\Facades\Log;
use App\Entities\TransactionSaleBJB;
use App\Exports\TransactionSaleExport;
use Illuminate\Support\Facades\Config;
use App\Entities\GroupSchemaShareholder;
use App\Validators\TransactionValidator;
use App\Exports\TransactionFeeSaleExport;
use App\Entities\transactionPaymentStatus;
use App\Repositories\TransactionRepository;
use App\Http\Requests\TransactionCreateRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Controllers\CoresController as Core;
use App\Http\Requests\TransactionBJBUpdateRequest;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class TransactionsController.
 *
 * @package namespace App\Http\Controllers;
 */
class TransactionsController extends Controller
{
    /**
     * @var TransactionRepository
     */
    protected $repository;

    /**
     * @var TransactionValidator
     */
    protected $validator;
    protected $transactionService;


    /**
     * TransactionsController constructor.
     *
     * @param TransactionRepository $repository
     * @param TransactionValidator $validator
     */
    public function __construct(TransactionRepository $repository, TransactionValidator $validator, TransactionService $transactionService)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->transactionService = $transactionService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct(TransactionRepository $repository, TransactionValidator $validator)
     {
         $this->repository = $repository;
         $this->validator = $validator;
     }
     public function index()
    {
<<<<<<< Updated upstream

        if (!$request->has('status') && $request->get('status') == '') {
            $request->request->add([
                'status'  => 'Success'
            ]);
        }
        if (!$request->has('start_date') && $request->get('start_date') == '') {
            $request->request->add([
                'start_date'      => date("m-d-Y")
            ]);
        }
        if (!$request->has('end_date') && $request->get('end_date') == '') {
            $request->request->add([
                'end_date'      => date("m-d-Y")
            ]);
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = Transaction::select('*');
        $data = $data->with(['terminal', 'event', 'service', 'transactionStatus', 'user']);

        // if(session()->get('user')->role_id == 2) {
        //     $data->whereHas('merchant',function($query) use ($request)
        //         {
        //             $query->where('terminal_id', '=', session()->get('user')->username);
        //         });
        // }

        if ($request->has('start_date') && $request->get('start_date') != '') {
            $data->where('transaction_time', '>', $request->get('start_date') . ' 00:00:00');
        }

        if ($request->has('end_date') && $request->get('end_date') != '') {
            $data->where('transaction_time', '<=', $request->get('end_date') . ' 23:59:59');
        }


        if ($request->has('terminal_id') && $request->get('terminal_id') != '') {
            $data->whereHas('terminal', function ($query) use ($request) {
                $query->where('terminal_id', '=', $request->get('terminal_id'));
            });
        }

        if ($request->has('merchant_id') && $request->get('merchant_id') != '') {
            $data->whereHas('terminal', function ($query) use ($request) {
                $query->where('merchant_id', '=', $request->get('merchant_id'));
            });
        }

        if ($request->has('status') && $request->get('status') != '' && $request->get('status') != 'Select Status') {
            $status = $request->get('status');
            if ($status == 'Success') {
                $data->where('transaction_status_id', '=', 0);
            } else if ($status == 'Failed') {
                $data->where('transaction_status_id', '=', 1);
            } else if ($status == 'Pending') {
                $data->where('transaction_status_id', '=', 2);
            }
        }
        // if($request->has('stan') && $request->get('stan')!=''){
        //     $data->where('stan', '=', $request->get('stan'));
        // }

        // if($request->has('limit')){
        //     $data->take($request->get('limit'));

        //     if($request->has('offset')){
        //     	$data->skip($request->get('offset'));
        //     }
        // }

        if ($request->has('order_type')) {
            if ($request->get('order_type') == 'asc') {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'));
                } else {
                    $data->orderBy('transaction_time');
                }
            } else {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'), 'desc');
                } else {
                    $data->orderBy('transaction_time', 'desc');
                }
            }
        } else {
            $data->orderBy('transaction_time', 'desc');
        }
        // $data->where('is_development','!=',1);
        // $data->where('is_marked_as_failed','!=',1);

        $dataRevenue = array();
        $dataRevenue['total_trx'] = $data->count();
        $dataRevenue['amount_trx']   = $data->sum('amount');
        $dataRevenue['total_fee']   = $data->sum('amount') - $data->sum('fee');
        $dataRevenue['total_fee_agent']   = $dataRevenue['total_fee'] * 0.6;
        $dataRevenue['total_fee_bjb']   = $dataRevenue['total_fee'] * 0.2;
        $dataRevenue['total_fee_selada']   = $dataRevenue['total_fee'] * 0.2;

        $total = $data->count();

        if ($request->has('order_type')) {
            if ($request->get('order_type') == 'asc') {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'));
                } else {
                    $data->orderBy('transaction_time');
                }
            } else {
                if ($request->has('order_by')) {
                    $data->orderBy($request->get('order_by'), 'desc');
                } else {
                    $data->orderBy('transaction_time', 'desc');
                }
            }
        } else {
            $data->orderBy('transaction_time', 'desc');
        }

        $data = $data->paginate(10);

        foreach ($data as $item) {
            if ($item->status == 0) {
                $item->status_text = 'Success';
            }

            if ($item->status == 1) {
                $item->status_text = 'Failed';
            }

            if ($item->status == 2) {
                $item->status_text = 'Pending';
            }

            $item->fee = $item->price - $item->vendor_price;

            if ($item->is_suspect == 0) {
                $item->status_suspect = 'False';
            }

            if ($item->is_suspect == 1) {
                $item->status_suspect = 'True';
            }
        }

        $user = session()->get('user');

        return view('apps.transactions.list')
            ->with('data', $data)
            ->with('dataRevenue', $dataRevenue)
            ->with('username', $user->username);
=======
        $transactions = Transaction::with('merchant', 'terminal', 'bank')->get();
        return view('apps.transaction.list', compact('transactions'));
    }
    
    public function create()
    {
        $merchants = Merchant::all();
        $terminals = TerminalBilliton::all();
        $banks = Bank::all();
        return view('apps.transactions.add', compact('merchants', 'terminals', 'banks'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric',
                'merchant_id' => 'required|exists:merchants,id',
                'terminal_id' => 'required|exists:terminals,id',
                'bank_id' => 'required|exists:banks,id',
                'status' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return Redirect::to('transaction/create')->withErrors($validator)->withInput();
            }

            $transaction = new Transaction();
            $transaction->fill($request->only(['amount', 'merchant_id', 'terminal_id', 'bank_id', 'status']));
            $transaction->save();

            DB::commit();
            return Redirect::to('transaction')->with('message', 'Transaction created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating transaction: ' . $e->getMessage());
            return Redirect::to('transaction/create')->with('error', 'Error creating transaction')->withInput();
        }
>>>>>>> Stashed changes
    }

    public function export(Request $request)
    {

        if (!$request->has('status') && $request->get('status') == '') {
            $request->request->add([
                'status'  => 'Success'
            ]);
        }
        if (!$request->has('start_date') && $request->get('start_date') == '') {
            $request->request->add([
                'start_date'      => date("Y-m-d")
            ]);
        }
        if (!$request->has('end_date') && $request->get('end_date') == '') {
            $request->request->add([
                'end_date'      => date("Y-m-d")
            ]);
        }
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return (new TransactionExport($request))->download('transaction_export_' . $request->get('start_date') . '_' . $request->get('end_date') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function saleExport(Request $request)
    {

        if (!$request->has('status') && $request->get('status') == '') {
            $request->request->add([
                'status'  => 'Success'
            ]);
        }
        if (!$request->has('start_date') && $request->get('start_date') == '') {
            $request->request->add([
                'start_date'      => date("Y-m-d")
            ]);
        }
        if (!$request->has('end_date') && $request->get('end_date') == '') {
            $request->request->add([
                'end_date'      => date("Y-m-d")
            ]);
        }
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return (new TransactionSaleExport($request))->download('transaction_sale_export_' . $request->get('start_date') . '_' . $request->get('end_date') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }


    public function exportCSV(Request $request)
    {

        if (!$request->has('status') && $request->get('status') == '') {
            $request->request->add([
                'status'  => 'Success'
            ]);
        }
        if (!$request->has('start_date') && $request->get('start_date') == '') {
            $request->request->add([
                'start_date'      => date("Y-m-d")
            ]);
        }
        if (!$request->has('end_date') && $request->get('end_date') == '') {
            $request->request->add([
                'end_date'      => date("Y-m-d")
            ]);
        }
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return (new TransactionExport($request))->download('transaction_export_' . $request->get('start_date') . '_' . $request->get('end_date') . '.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv'
        ]);
    }

    public function exportPDF(Request $request)
    {

        if (!$request->has('status') && $request->get('status') == '') {
            $request->request->add([
                'status'  => 'Success'
            ]);
        }
        if (!$request->has('start_date') && $request->get('start_date') == '') {
            $request->request->add([
                'start_date'      => date("Y-m-d")
            ]);
        }
        if (!$request->has('end_date') && $request->get('end_date') == '') {
            $request->request->add([
                'end_date'      => date("Y-m-d")
            ]);
        }
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return (new TransactionExport($request))->download('transaction_export_' . $request->get('start_date') . '_' . $request->get('end_date') . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function feeExport(Request $request)
    {

        if (!$request->has('status') && $request->get('status') == '') {
            $request->request->add([
                'status'  => 'Success'
            ]);
        }
        if (!$request->has('start_date') && $request->get('start_date') == '') {
            $request->request->add([
                'start_date'      => date("Y-m-d")
            ]);
        }
        if (!$request->has('end_date') && $request->get('end_date') == '') {
            $request->request->add([
                'end_date'      => date("Y-m-d")
            ]);
        }
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

<<<<<<< Updated upstream
        return (new TransactionFeeSaleExport($request))->download('transaction_fee_sale_export_' . $request->get('start_date') . '_' . $request->get('end_date') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
=======
	    return (new TransactionFeeSaleExport($request))->download('transaction_fee_sale_export_'.$request->get('start_date').'_'.$request->get('end_date').'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function reversal(Request $request)
    {
        if(!$request->has('start_date') && $request->get('start_date')==''){
            $request->request->add([
                'start_date'      => date("Y-m-d")
            ]);
        }
        if(!$request->has('end_date') && $request->get('end_date')==''){
            $request->request->add([
                'end_date'      => date("Y-m-d")
            ]);
        }

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $data = TransactionLog::whereNotNull('responsecode');
        $data->where('tx_mti', '=', '0200');
        $data->where('proc_code', '=', '500000');

        if($request->has('start_date') && $request->get('start_date')!=''){
            $data->where('tx_time', '>', $request->get('start_date').' 00:00:00');
        }

        if($request->has('end_date') && $request->get('end_date')!=''){
            $data->where('tx_time', '<=', $request->get('end_date').' 23:59:59');
        }

        if($request->has('stan') && $request->get('stan')!=''){
            $data->where('stan', $request->get('stan'));
        }

        $dataLog = TransactionLog::select('stan')->whereNotNull('responsecode');
        $dataLog->where('tx_mti', '=', '0200');
        $dataLog->where('proc_code', '=', '500000');

        if($request->has('start_date') && $request->get('start_date')!=''){
            $dataLog->where('tx_time', '>', $request->get('start_date').' 00:00:00');
        }

        if($request->has('end_date') && $request->get('end_date')!=''){
            $dataLog->where('tx_time', '<=', $request->get('end_date').' 23:59:59');
        }

        if($request->has('stan') && $request->get('stan')!=''){
            $dataLog->where('stan', $request->get('stan'));
        }

        $dataPpob = Transaction::select('stan');
        if($request->has('start_date') && $request->get('start_date')!=''){
            $dataPpob->where('created_at', '>', $request->get('start_date').' 00:00:00');
        }

        if($request->has('end_date') && $request->get('end_date')!=''){
            $dataPpob->where('created_at', '<=', $request->get('end_date').' 23:59:59');
        }

        if($request->has('stan') && $request->get('stan')!=''){
            $dataPpob->where('stan', $request->get('stan'));
        }

        $dataLog = $dataLog->get();
        $dataPpob = $dataPpob->get();

        $arrayLength = $dataLog->count();
        $ppobSize = $dataPpob->count();
        
        $array = array();
        $i = 0;
        $count = 0;
        for ($i = 0; $i < $arrayLength; $i++){
            $isIdentical = false;
            try {
                for ($j = 0; $j < $ppobSize; $j++){
                    if($dataLog[$i]->stan == $dataPpob[$j]->stan){
                        $isIdentical = true;
                    }
                }
            } catch (Exception $e){
                // echo $e;
            }

            if($isIdentical){
                //verified
                // echo 'isIdentical ';
            } else {
                $array[$count] = $dataLog[$i]->stan;
                $count++;
            }
        }
        
        $data->whereIn('stan', $array);

        $data = $data->paginate(10);

        return view('apps.transactions.reversal')
                ->with('data', $data);
    }

    public function postReversal(Request $request, $additional_data)
    {

        DB::beginTransaction();
        try {
            $dateTime = date("YmdHms");

            $data = TransactionLog::select('stan')->where('additional_data', $additional_data)->first();

            $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://36.94.58.182:8080/ARRest/api");
                // SSL important
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: text/plain'));

                curl_setopt($ch, CURLOPT_POSTFIELDS, [ 'msg' => '{
                                                    "msg_id": "'. substr($additional_data, 0, 16) . $dateTime . '",
                                                    "msg_ui": "'. substr($additional_data, 0, 16) .'",
                                                    "msg_si": "R82561",
                                                    "msg_dt": "'. $data->stan .'"
                                                }'] );
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt($ch, CURLOPT_POST,           1 );

                $output = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                $value = '{ msg: {
                                                    "msg_id": "'. substr($additional_data, 0, 16) . $dateTime . '",
                                                    "msg_ui": "'. substr($additional_data, 0, 16) .'",
                                                    "msg_si": "R82561",
                                                    "msg_dt": "'. $data->stan .'"
                                                } }';

                echo $output . $value;die;
                
                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    // print_r(json_decode($output));
                    return Redirect::to('transaction')
                        ->with('message', 'Reversal berhasil dikirim');
                }

        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('transaction/reversal')
                        ->with('error', $e)
                        ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('transaction/reversal')
                        ->with('error', $e)
                        ->withInput();
        }
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);
        $merchants = Merchant::all();
        $terminals = Terminal::all();
        $banks = Bank::all();
        return view('transactions.edit', compact('transaction', 'merchants', 'terminals', 'banks'));
>>>>>>> Stashed changes
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
<<<<<<< Updated upstream
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $transaction = Transaction::find($id);
            if ($transaction->status == 0 || $transaction->status == 1) {
                $reqData['status']               = 2;
            } else {
                $reqData['status']               = 1;
            }
            $data = $this->repository->update($reqData, $id);

            if ($data) {
                DB::commit();
                return Redirect::to('transaction')
                    ->with('message', 'Status updated');
            } else {
                DB::rollBack();
                return Redirect::to('transaction')
                    ->with('error', $data->error)
                    ->withInput();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('transaction')
                ->with('error', $e)
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('transaction')
                ->with('error', $e)
                ->withInput();
=======
            // Validasi request
            $validator = Validator::make($request->all(), [
                'status' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                DB::rollBack();
                return Redirect::to('transaction')->withErrors($validator)->withInput();
            }
    
            // Temukan transaksi
            $transaction = Transaction::findOrFail($id);
    
            // Tentukan status baru
            $reqData['status'] = ($transaction->status == 0 || $transaction->status == 1) ? 2 : 1;
    
            // Perbarui transaksi
            $data = $this->repository->update($reqData, $id);
    
            if ($data) {
                DB::commit();
                return Redirect::to('transaction')->with('message', 'Status updated');
            } else {
                DB::rollBack();
                return Redirect::to('transaction')->with('error', 'Update failed')->withInput();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('transaction')->with('error', $e->getMessage())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            return Redirect::to('transaction')->with('error', $e->getMessage())->withInput();
>>>>>>> Stashed changes
        }
    }

    public function updateStatus($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
    
            if ($transaction) {
                $msg_td = base64_encode($transaction->merchant->terminal->tid);
                $msg_dt = date("YmdHms");
                $theOtherKey = $transaction->merchant->terminal->tid . $msg_dt;
                $base64key = base64_encode($theOtherKey);
    
                $newEncrypter = new \Illuminate\Encryption\Encrypter($base64key, 'AES-256-CBC');
                $encrypted = $newEncrypter->encrypt($transaction->code);
<<<<<<< Updated upstream

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "http://36.94.58.180/api/core/public/index.php/api/transactions/detail/" . $id);

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(

                    'api-key: ' . $encrypted,
                    'msg-td: ' . $msg_td,
                    'msg-dt: ' . $msg_dt
                ));
=======
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://36.94.58.180/api/core/public/index.php/api/transactions/detail/" . $id);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'api-key: ' . $encrypted,
                    'msg-td: ' . $msg_td,
                    'msg-dt: ' . $msg_dt
                ]);
>>>>>>> Stashed changes
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    
                $output = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);
<<<<<<< Updated upstream

=======
    
>>>>>>> Stashed changes
                if ($err) {
                    return Redirect::to('transaction')->with('error', "cURL Error: " . $err);
                } else {
                    return Redirect::back()->with('success', 'Status updated successfully');
                }
            } else {
                return Redirect::to('transaction')->with('error', 'Transaction not found');
            }
        } catch (Exception $e) {
<<<<<<< Updated upstream
            return Redirect::to('transaction')
                ->with('error', $e)
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            return Redirect::to('transaction')
                ->with('error', $e)
                ->withInput();
=======
            return Redirect::to('transaction')->with('error', $e->getMessage())->withInput();
        } catch (QueryException $e) {
            return Redirect::to('transaction')->with('error', $e->getMessage())->withInput();
>>>>>>> Stashed changes
        }
    }
    

    public function updatebjb(Request $request, $id)
    {
<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
        DB::beginTransaction();
        try {
            // Validasi request
            $validator = Validator::make($request->all(), [
                'status' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                DB::rollBack();
                return Redirect::to('transaction')->withErrors($validator)->withInput();
            }
    
            list($stan, $date) = explode("_", $id);
<<<<<<< Updated upstream
            return Redirect::to('transaction')
                ->with('stan', $stan)
                ->with('date', $date);
            $transaction = TransactionLog::where('stan', '=', $stan)->first();

            foreach ($transaction as $trx) {
                if (strpos($trx->tx_time, $date) !== false) {
                    $reqData['tx_mti']               = '0400';
                    $reqData['rp_mti']               = '0410';

                    $data = $this->repository->update($reqData, $stan);

                    if ($data) {
                        DB::commit();
                        return Redirect::to('transaction')
                            ->with('message', 'Status updated');
                    } else {
                        DB::rollBack();
                        return Redirect::to('transaction')
                            ->with('error', $data->error)
                            ->withInput();
                    }
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('transaction')
                ->with('error', $e)
                ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('transaction')
                ->with('error', $e)
                ->withInput();
=======
    
            $transaction = TransactionLog::where('stan', $stan)
                ->where('tx_time', '>', $date . ' 00:00:00')
                ->first();
    
            if ($transaction) {
                $reqData['tx_mti'] = '0400';
                $reqData['rp_mti'] = '0410';
    
                $data = $this->repository->update($reqData, $stan);
    
                if ($data) {
                    DB::commit();
                    return Redirect::to('transaction')->with('message', 'Status updated');
                } else {
                    DB::rollBack();
                    return Redirect::to('transaction')->with('error', 'Update failed')->withInput();
                }
            } else {
                DB::rollBack();
                return Redirect::to('transaction')->with('error', 'Transaction not found');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('transaction')->with('error', $e->getMessage())->withInput();
        } catch (QueryException $e) {
            DB::rollBack();
            return Redirect::to('transaction')->with('error', $e->getMessage())->withInput();
>>>>>>> Stashed changes
        }
    }
    

    private function getInfo(Request $request)
    {
        DB::beginTransaction();
        try {
<<<<<<< Updated upstream
            $date           = $request->date;
            $group_id       = $request->group_id;
            $schema_id      = $request->schema_id;
            $dataCalculate  = $this->calculate($group_id, $date);


            $groupSchema = GroupSchema::where('group_id', $group_id)
                ->where('schema_id', $schema_id)
                ->first();
            if ($groupSchema) {
                $revenue = $dataCalculate['revenue'] * $groupSchema->share / 100;

                if ($groupSchema->is_shareable ==  true) {
                    $shareholders = GroupSchemaShareholder::where('group_schema_id', $groupSchema->id)
                        ->with('shareholder')
                        ->get();
                    foreach ($shareholders as $sh) {
                        $sh->revenue = $sh->share / 100 * $revenue;
                    }
                } else {
                    $shareholders = [];
=======
            $date = $request->date;
            $group_id = $request->group_id;
            $schema_id = $request->schema_id;
    
            $dataCalculate = $this->calculate($group_id, $date);
    
            $groupSchema = GroupSchema::where('group_id', $group_id)
                ->where('schema_id', $schema_id)
                ->first();
    
            if ($groupSchema) {
                $revenue = $dataCalculate['revenue'] * $groupSchema->share / 100;
    
                $shareholders = [];
                if ($groupSchema->is_shareable) {
                    $shareholders = GroupSchemaShareholder::where('group_schema_id', $groupSchema->id)
                        ->with('shareholder')
                        ->get();
    
                    foreach ($shareholders as $sh) {
                        $sh->revenue = $sh->share / 100 * $revenue;
                    }
>>>>>>> Stashed changes
                }
    
                $response = [
                    'revenue' => $revenue,
                    'total_trx' => $dataCalculate['count'],
                    'amount_trx' => $dataCalculate['amount'],
                    'shareholder' => $shareholders
                ];
            } else {
                return response()->json([
<<<<<<< Updated upstream
                    'status'    => false,
                    'error'     => 'Group has not schema'
=======
                    'status' => false,
                    'error' => 'Group has not schema'
>>>>>>> Stashed changes
                ], 404);
            }
    
            DB::commit();
            return response()->json($response, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
<<<<<<< Updated upstream
                'status'    => false,
                'error'     => 'Something wrong!',
                'exception' => $e
=======
                'status' => false,
                'error' => 'Something wrong!',
                'exception' => $e->getMessage()
>>>>>>> Stashed changes
            ], 500);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
<<<<<<< Updated upstream
                'status'    => false,
                'error'     => 'Something wrong!',
                'exception' => $e
=======
                'status' => false,
                'error' => 'Something wrong!',
                'exception' => $e->getMessage()
>>>>>>> Stashed changes
            ], 500);
        }
    }
    

    private function calculate($group_id, $date)
    {
        $groups = Group::where('parent_id', $group_id)->get();
<<<<<<< Updated upstream
        $result = [];
        $result['revenue']  = 0;
        $result['count']    = 0;
        $result['amount']   = 0;
        $result['total_fee']   = 0;
        $result['total_hpp']   = 0;
        // Count
        $count = Transaction::where('status', 1)
            ->count();
        $result['count'] = $count;

        // Sum
        $sum = Transaction::where('status', 1)
            ->sum('price');
        $result['amount'] = $sum;

        $fee = Transaction::where('status', 1)
            ->sum('fee');
        $result['total_fee'] = $fee;

        $result['total_hpp'] = $sum - $fee;

=======
        $result = [
            'revenue' => 0,
            'count' => 0,
            'amount' => 0,
            'total_fee' => 0,
            'total_hpp' => 0
        ];
    
        $count = Transaction::where('status', 1)->count();
        $result['count'] = $count;
    
        $sum = Transaction::where('status', 1)->sum('price');
        $result['amount'] = $sum;
    
        $fee = Transaction::where('status', 1)->sum('fee');
        $result['total_fee'] = $fee;
    
        $result['total_hpp'] = $sum - $fee;
    
>>>>>>> Stashed changes
        return $result;
    }
    
    private function calculateOnly()
    {
<<<<<<< Updated upstream
        $result = [];
        $result['revenue']  = 0;
        $result['count']    = 0;
        $result['amount']   = 0;
        $result['total_fee']   = 0;
        $result['total_hpp']   = 0;
        // Count
        $count = Transaction::where('status', 1)->where('is_development', '!=', 1)
            ->count();
        $result['count'] = $count;

        // Sum
        $sum = Transaction::where('status', 1)->where('is_development', '!=', 1)
            ->sum('price');
        $result['amount'] = $sum;

        $total_hpp = Transaction::where('status', 1)->where('is_development', '!=', 1)
            ->sum('vendor_price');
        $result['total_hpp'] = $total_hpp;

        $result['total_fee'] = $sum - $total_hpp;

=======
        $result = [
            'revenue' => 0,
            'count' => 0,
            'amount' => 0,
            'total_fee' => 0,
            'total_hpp' => 0
        ];
    
        $count = Transaction::where('status', 1)
            ->where('is_development', '!=', 1)
            ->count();
        $result['count'] = $count;
    
        $sum = Transaction::where('status', 1)
            ->where('is_development', '!=', 1)
            ->sum('price');
        $result['amount'] = $sum;
    
        $total_fee = Transaction::where('status', 1)
            ->where('is_development', '!=', 1)
            ->sum('fee');
        $result['total_fee'] = $total_fee;
    
        $result['total_hpp'] = $sum - $total_fee;
    
>>>>>>> Stashed changes
        return $result;
    }

    public function rankTransactions()
    {
        $rankedTransactions = $this->transactionService->rankTransactionsByMerchantUser();
        return view('apps.transactions.rank', compact('rankedTransactions'));
    }
}
