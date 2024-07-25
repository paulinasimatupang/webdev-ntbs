<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Validator;
use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TransactionCreateRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Repositories\TransactionRepository;
use App\Validators\TransactionValidator;
use Ixudra\Curl\Facades\Curl;
use App\Entities\Service;
use App\Entities\Transaction;
use App\Entities\TransactionStatus;
use App\Entities\TransactionPaymentStatus;
use App\Entities\Group;
use App\Entities\UserGroup;
use App\Entities\GroupSchema;
use App\Entities\GroupSchemaShareholder;
use App\Entities\TransactionBJB;
use App\Entities\TransactionLog;
use App\Entities\TransactionSaleBJB;
use App\Exports\TransactionExport;
use App\Exports\TransactionSaleExport;
use App\Exports\TransactionFeeSaleExport;
use App\Http\Controllers\CoresController as Core;
use App\Http\Requests\TransactionBJBUpdateRequest;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

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

    /**
     * TransactionsController constructor.
     *
     * @param TransactionRepository $repository
     * @param TransactionValidator $validator
     */
    public function __construct(TransactionRepository $repository, TransactionValidator $validator)
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
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string'
        ]);

        $start_date = $validated['start_date'] ?? now()->format('Y-m-d');
        $end_date = $validated['end_date'] ?? now()->format('Y-m-d');
        $status = $validated['status'] ?? '';

        $transactions = Transaction::with('transactionPaymentStatus')
            ->when($status !== '', function($query) use ($status) {
                $query->where('status', $status);
            })
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $transactions->each(function ($item) {
            $item->status_text = $item->status == 0 ? 'Pending' : ($item->status == 1 ? 'Success' : 'Failed');
            $item->fee = $item->price - $item->vendor_price;
            $item->status_suspect = $item->is_suspect == 0 ? 'False' : 'True';
        });

        return view('apps.transactions.list', compact('transactions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TransactionCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionCreateRequest $request)
    {
        try {
            $transaction = $this->repository->create($request->all());
            return Redirect::route('apps.transactions.add')->with('message', 'Transaction successfully created.');
        } catch (ValidatorException $e) {
            return Redirect::back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TransactionUpdateRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Export transactions to Excel.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);
    
        $start_date = $validated['start_date'] ?? now()->format('Y-m-d');
        $end_date = $validated['end_date'] ?? now()->format('Y-m-d');
        $status = $validated['status'] ?? '';
    
        $transactions = Transaction::with(['transactionPaymentStatus'])
            ->when($status !== '', function($query) use ($status) {
                $query->where('status', $status);
            })
            ->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();
    
        return (new TransactionExport($transactions))->download('transaction_export.xlsx');
    }

    /**
     * Export sales transactions to Excel.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function saleExport(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);
    
        $start_date = $validated['start_date'] ?? now()->format('Y-m-d');
        $end_date = $validated['end_date'] ?? now()->format('Y-m-d');
        $status = $validated['status'] ?? '';
    
        $sales = Transaction::where('transaction_type', 'sale')
            ->when($status !== '', function($query) use ($status) {
                $query->where('status', $status);
            })
            ->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();
    
        return (new TransactionSaleExport($sales))->download('transaction_sale_export.xlsx');
    }   

    /**
     * Export transactions to CSV.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportCSV(Request $request)
    {
        if(!$request->has('transaction_status_id') && $request->get('transaction_status_id')==''){
            $request->request->add([
                'transaction_status_id'  => 'Success'
            ]);
        }
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

        return (new TransactionExport($request))->download('transaction_export_'.$request->get('start_date').'_'.$request->get('end_date').'.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv'
        ]);
    }

    public function exportPDF(Request $request)
    {
        if(!$request->has('status') && $request->get('status')==''){
            $request->request->add([
                'status'  => 'Success'
            ]);
        }
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

        return (new TransactionExport($request))->download('transaction_export_'.$request->get('start_date').'_'.$request->get('end_date').'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }

    public function feeExport(Request $request)
    {
        if(!$request->has('status') && $request->get('status')==''){
            $request->request->add([
                'status'  => 'Success'
            ]);
        }
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

        return (new TransactionFeeSaleExport($request))->download('transaction_fee_sale_export_'.$request->get('start_date').'_'.$request->get('end_date').'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function reversal(Request $request)
    {
    $validated = $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
        'stan' => 'nullable|string',
    ]);

    $start_date = $validated['start_date'] ?? now()->format('Y-m-d');
    $end_date = $validated['end_date'] ?? now()->format('Y-m-d');
    $stan = $validated['stan'] ?? null;

    $query = TransactionLog::whereNotNull('responsecode')
        ->where('tx_mti', '0200')
        ->where('proc_code', '500000')
        ->whereBetween('tx_time', [$start_date.' 00:00:00', $end_date.' 23:59:59']);

    if ($stan) {
        $query->where('stan', $stan);
    }

    $data = $query->paginate(10);

    return view('apps.transactions.reversal', compact('data'));
    }



    /**
     * Export sales transactions to CSV.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function saleExportCSV(Request $request)
    {
        $validated = $request->validate([
            'transaction_status_id' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        $transaction_status_id = $validated['transaction_status_id'] ?? 'Success';
        $start_date = $validated['start_date'] ?? date("Y-m-d");
        $end_date = $validated['end_date'] ?? date("Y-m-d");

        $sales = Transaction::where('transaction_type', 'sale')
            ->where('status', $transaction_status_id)
            ->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->get();

        return (new TransactionSaleExport($sales))->download('transaction_sale_export_' . $start_date . '_' . $end_date . '.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv'
        ]);
    }

    /**
     * Handle reversal of transactions.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postReversal(Request $request, $additional_data)
    {
        DB::beginTransaction();
        try {
            $transaction = TransactionLog::where('additional_data', $additional_data)->firstOrFail();
    
            $response = Curl::to("http://36.94.58.182:8080/ARRest/api")
                ->withData([
                    'msg' => json_encode([
                        "msg_id" => substr($additional_data, 0, 16) . now()->format('YmdHis'),
                        "msg_ui" => substr($additional_data, 0, 16),
                        "msg_si" => "R82561",
                        "msg_dt" => $transaction->stan
                    ])
                ])
                ->post();
    
            DB::commit();
    
            return Redirect::to('transaction')->with('message', 'Reversal berhasil dikirim');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return Redirect::to('transaction/reversal')->with('error', 'An error occurred.');
        }
    }

    public function edit($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            return view('apps.transactions.edit')
                ->with('transaction', $transaction);
        } else {
            return Redirect::to('transaction')
                ->with('error', 'Data not found');
        }
    }

    public function update(TransactionUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $transaction = Transaction::findOrFail($id);
            
            $reqData['status'] = $transaction->status == 0 || $transaction->status == 1 ? 2 : 1;
            $data = $this->repository->update($reqData, $id);

            if ($data) {
                DB::commit();
                return Redirect::to('transaction')->with('message', 'Status updated');
            } else {
                DB::rollBack();
                return Redirect::to('transaction')->with('error', 'Failed to update status')->withInput();
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('transaction')->with('error', $e->getMessage())->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('transaction')->with('error', $e->getMessage())->withInput();
        }
    }


    public function updateStatus($id)
    {
        try {
            $transaction = Transaction::find($id);
            if ($transaction) {
                $msg_td = base64_encode($transaction->merchant->terminal->tid);
                $msg_dt = date("YmdHms");
                $theOtherKey = $transaction->merchant->terminal->tid.$msg_dt;
                $base64key = base64_encode($theOtherKey);

                $newEncrypter = new \Illuminate\Encryption\Encrypter($base64key, 'AES-256-CBC');
                $encrypted = $newEncrypter->encrypt($transaction->code);

                // echo $msg_td."||".$msg_dt."||".$encrypted."||".$base64key; die;
                $ch = curl_init();
                // $authorization = "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8zNi45NC41OC4xODBcL2FwaVwvY29yZVwvcHVibGljXC9pbmRleC5waHBcL2FwaVwvYXV0aFwvbG9naW4iLCJpYXQiOjE2MjAyNzQ1MDMsImV4cCI6MTYyMTE0MzMwMywibmJmIjoxNjIwMjc0NTAzLCJqdGkiOiJndXRUaVprZElOb3c5RkVwIiwic3ViIjoiZTZhZTkwOWEtY2YzNC00ZDc2LWE5ZWQtMjJkOWJhNzU4ZmIwIiwicHJ2IjoiZjkzMDdlYjVmMjljNzJhOTBkYmFhZWYwZTI2ZjAyNjJlZGU4NmY1NSJ9.B8mm2IFt-TlYtvnmk8gctiBfAxnF5op0plemFJW6D_k";
                // $method_request = "transaction_code=".$transaction->code;
                // curl_setopt($ch, CURLOPT_URL, "http://36.94.58.180/api/core/public/index.php/api/transactions/checkStatus?".$method_request);
                curl_setopt($ch, CURLOPT_URL, "http://36.94.58.180/api/core/public/index.php/api/transactions/detail/".$id);
                // SSL important
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    // $authorization,
                    // 'msg-td: '.$msg_td,
                    'api-key: '.$encrypted,
                    'msg-td: '.$msg_td,
                    'msg-dt: '.$msg_dt));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

                $output = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);
                
                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    // print_r(json_decode($output));
                    return Redirect::back()->with('success','Status updated successfully');
                    // return redirect()->route('transaction')
                    //             ->with('success','Status updated successfully');
                }
            }
        } catch (Exception $e) {
            return Redirect::to('transaction')
                        ->with('error', $e)
                        ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            return Redirect::to('transaction')
                        ->with('error', $e)
                        ->withInput();
        }
    }

    public function updatebjb(TransactionBJBUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            list($stan, $date) = explode("_", $id);
            return Redirect::to('transaction')
                ->with('stan', $stan)
                ->with('date', $date);
            $transaction = TransactionLog::where('stan', '=', $stan)->first();
            foreach ($transaction as $trx) {
                if (strpos($trx->tx_time, $date) !== false) {
                    $reqData['tx_mti'] = '0400';
                    $reqData['rp_mti'] = '0410';

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
        }
    }

    private function getInfo(Request $request)
    {
        DB::beginTransaction();
        try {
            $date = $request->date;
            $group_id = $request->group_id;
            $schema_id = $request->schema_id;
            $dataCalculate = $this->calculate($group_id, $date);

            // Check 
            $groupSchema = GroupSchema::where('group_id', $group_id)
                ->where('schema_id', $schema_id)
                ->first();
            if ($groupSchema) {
                $revenue = $dataCalculate['revenue'] * $groupSchema->share / 100;
                // Check if this group is shareable
                if ($groupSchema->is_shareable == true) {
                    $shareholders = GroupSchemaShareholder::where('group_schema_id', $groupSchema->id)
                        ->with('shareholder')
                        ->get();
                    foreach ($shareholders as $sh) {
                        $sh->revenue = $sh->share / 100 * $revenue;
                    }
                } else {
                    $shareholders = [];
                }

                $response = [
                    'revenue' => $revenue,
                    'total_trx' => $dataCalculate['count'],
                    'amount_trx' => $dataCalculate['amount'],
                    'shareholder' => $shareholders
                ];
            } else {
                return response()->json([
                    'status' => false,
                    'error' => 'Group has not schema'
                ], 404);
            }

            DB::commit();
            return response()->json($response, 200);
        } catch (Exception $e) {
            // For rollback data if one data is error
            DB::rollBack();

            return response()->json([
                'status' => false,
                'error' => 'Something wrong!',
                'exception' => $e
            ], 500);
        } catch (\Illuminate\Database\QueryException $e) {
            // For rollback data if one data is error
            DB::rollBack();

            return response()->json([
                'status' => false,
                'error' => 'Something wrong!',
                'exception' => $e
            ], 500);
        }
    }

    private function calculate($group_id, $date)
    {
        $groups = Group::where('parent_id', $group_id)->get();
        $result = [];
        $result['revenue'] = 0;
        $result['count'] = 0;
        $result['amount'] = 0;
        $result['total_fee'] = 0;
        $result['total_hpp'] = 0;
        // Count
        $count = Transaction::where('status', 1)->count();
        $result['count'] = $count;

        // Sum
        $sum = Transaction::where('status', 1)->sum('price');
        $result['amount'] = $sum;

        $fee = Transaction::where('status', 1)->sum('fee');
        $result['total_fee'] = $fee;

        $result['total_hpp'] = $sum - $fee;
        return $result;
    }

    private function calculateOnly()
    {
        $result = [];
        $result['revenue'] = 0;
        $result['count'] = 0;
        $result['amount'] = 0;
        $result['total_fee'] = 0;
        $result['total_hpp'] = 0;
        // Count
        $count = Transaction::where('status', 1)->where('is_development', '!=', 1)->count();
        $result['count'] = $count;

        // Sum
        $sum = Transaction::where('status', 1)->where('is_development', '!=', 1)->sum('price');
        $result['amount'] = $sum;

        $total_hpp = Transaction::where('status', 1)->where('is_development', '!=', 1)->sum('vendor_price');
        $result['total_hpp'] = $total_hpp;

        $result['total_fee'] = $sum - $total_hpp;
        return $result;
    }
}
