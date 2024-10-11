<?php
// Connect to ARDI

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;
use Carbon\Carbon;
use Validator;
use Redirect;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TransactionCreateRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Repositories\TransactionRepository;
use App\Validators\TransactionValidator;
use Ixudra\Curl\Facades\Curl;
use App\Exports\TransactionExport;

use App\Services\TransactionService;

use App\Entities\Merchant;
use App\Entities\PersenFee;
use App\Entities\Service;
use App\Entities\Transaction;
use App\Entities\TransactionStatus;
use App\Entities\TransactionFee;
use App\Entities\transactionPaymentStatus;
use App\Entities\Group;
use App\Entities\Role;
use App\Entities\UserGroup;
use App\Entities\GroupSchema;
use App\Entities\GroupSchemaShareholder;
use App\Entities\TransactionBJB;
use App\Entities\TransactionLog;
use App\Entities\TransactionSaleBJB;
use App\Exports\TransactionSaleExport;
use App\Exports\TransactionFeeSaleExport;
use App\Http\Controllers\CoresController as Core;
use App\Http\Requests\TransactionBJBUpdateRequest;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as Pdf;

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
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $query = Transaction::query();

        $user = session()->get('user');

        if ($user) {
            $role_user = $user->role_id;
            $role = Role::find($role_user);

            if ($role && $role->name == 'Agen') {
                $merchant = session()->get('merchant');
                if ($merchant) {
                    $kode_agen = $merchant->mid;
                    $query->where('kode_agen', $kode_agen);
                }
            }
        }

        if ($request->has('search') && $request->get('search') != '') {
            $query->where('transaction_code', '=', $request->get('search'));
        }

        if ($request->has('mid') && $request->get('mid') != '') {
            $query->where('kode_agen', '=', $request->get('mid'));
        }

        if ($request->has('start_date') && $request->get('start_date') != '') {
            $query->where('transaction_time', '>', $request->get('start_date') . ' 00:00:00.000');
        }

        if ($request->has('end_date') && $request->get('end_date') != '') {
            $query->where('transaction_time', '<=', $request->get('end_date') . ' 23:59:59.999');
        }

        if ($request->has('status') && $request->get('status') != '' && $request->get('status') != 'Select Status') {
            $status = $request->get('status');
            switch ($status) {
                case 'Success':
                    $query->where('transaction_status_id', 0);
                    break;
                case 'Failed':
                    $query->where('transaction_status_id', 1);
                    break;
                case 'Pending':
                    $query->where('transaction_status_id', 2);
                    break;
            }
        }

        $orderType = $request->get('order_type', 'desc');
        $orderBy = $request->get('order_by', 'transaction_time');
        $query->orderBy($orderBy, $orderType);

        $data = $query->with('merchant.user', 'service', 'transactionStatus')->get();

        $totalAmount = $query->sum('amount');
        $transactionCodes = $query->pluck('transaction_code')->unique();
        if ($role && $role->name == 'Agen') {
            // Jika role adalah 'Agen', ambil fee dari tabel TransactionFee dengan penerima 'Agent'
            $totalFee = TransactionFee::whereIn('transaction_code', $transactionCodes)
                                    ->where('penerima', 'Agent')
                                    ->sum('fee');
        } else {
            $totalFee = $query->sum('fee');
        }

        $dataRevenue = [
            'total_trx' => $query->count(),
            'amount_trx' => $totalAmount,
            'total_fee' => $totalFee,
        ];

        return view('apps.transactions.list')
            ->with('data', $data)
            ->with('dataRevenue', $dataRevenue)
            ->with('username', $user->username);
    }
    
    public function getStatusText($statusId)
    {
        $transaction_status = TransactionStatus::where('transaction_status_id', $statusId)->get('transaction_status_desc');
        return $transaction_status;
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
        set_time_limit(120); 
        $query = Transaction::query()->orderBy('transaction_code'); 
        $choice = 1;
        return Excel::download(new TransactionsExport($query, $choice), 'Data Transaksi.xlsx');
    }
    
    public function exportCSVFeeOnly(Request $request)
    {
        $query = Transaction::query(); 
        $choice = 2;
        return Excel::download(new TransactionsExport($query, $choice), 'transactions_fee.xlsx');
    }
    
    public function exportCSVPaymentOnly(Request $request)
    {
        $query = Transaction::query(); 
        $choice = 3; // Menampilkan nama, amount, dan status
        return Excel::download(new TransactionsExport($query, $choice), 'transactions_payment.xlsx');
    }
    
    public function exportPDF(Request $request)
    {
        set_time_limit(1000);
        
        $query = Transaction::query();
        $viewType = 1; 
        $transactionsExport = new TransactionsExport($query, $viewType);
        
        $transactions = $transactionsExport->collection(); 
        
        $pdf = Pdf::loadView('pdf.transactions', ['transactions' => $transactions])
                ->setPaper('A4', 'landscape'); 
        return $pdf->download('Data Transaksi.pdf');
    }

    public function exportTxt()
    {
        // Ambil semua transaksi dari database
        $transactions = Transaction::all();
        $txtData = '';
    
        // Loop untuk setiap transaksi
        foreach ($transactions as $transaction) {
            $txtData .= "No: " . ($transaction->No ?? 'N/A') . ", ";
            $txtData .= "Name: " . ($transaction->merchant->name ?? 'N/A') . ", ";
            $txtData .= "Transaksi Code: " . ($transaction->transaction_code ?? 'N/A') . ", ";
            $txtData .= "Amount: " . ($transaction->amount ?? 'N/A') . ", ";
            $txtData .= "Fee: " . ($transaction->fee ?? 'N/A') . ", ";
            $txtData .= "Waktu Transaksi: " . ($transaction->transaction_time);
            $txtData .= "Nomor Rekening Penerima: " . ($transaction->rekening_penerima ?? 'N/A') . ", ";
            $txtData .= "Nomor Rekening Pengirim: " . ($transaction->rekening_pengirim ?? 'N/A') . ", ";
            $txtData .= "Tipe Transaksi: " . ($transaction->transaction_type ?? 'N/A') . ", ";
            $txtData .= "Kode Agen: " . ($transaction->merchant->mid ?? 'N/A') . ", ";
            $statusId = $transaction->transaction_status_id ?? null;
            switch ($statusId) {
                case 0:
                    $statusText = 'Success';
                    break;
                case 1:
                    $statusText = 'Failed';
                    break;
                case 2:
                    $statusText = 'Pending';
                    break;
                default:
                    $statusText = 'Unknown';
            }
    
            // Append the status text to the file content
            $txtData .= "Status: " . $statusText . "\n";
        }
    
        // Nama file untuk unduhan
        $fileName = "Transaction.txt";
        
        // Headers untuk unduhan file TXT
        $headers = [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];
    
        // Mengembalikan response dengan data dan headers
        return response($txtData, 200, $headers);
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

        return (new TransactionFeeSaleExport($request))->download('transaction_fee_sale_export_' . $request->get('start_date') . '_' . $request->get('end_date') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function update(TransactionUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
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
        }
    }

    public function updateStatus($id)
    {
        try {
            $transaction = Transaction::find($id);
            if ($transaction) {
                $msg_td = base64_encode($transaction->merchant->terminal->tid);
                $msg_dt = date("YmdHms");
                $theOtherKey = $transaction->merchant->terminal->tid . $msg_dt;
                $base64key = base64_encode($theOtherKey);

                $newEncrypter = new \Illuminate\Encryption\Encrypter($base64key, 'AES-256-CBC');
                $encrypted = $newEncrypter->encrypt($transaction->code);

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "http://36.94.58.180/api/core/public/index.php/api/transactions/detail/" . $id);

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(

                    'api-key: ' . $encrypted,
                    'msg-td: ' . $msg_td,
                    'msg-dt: ' . $msg_dt
                ));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

                $output = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    return Redirect::back()->with('success', 'Status updated successfully');
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
        }
    }

    private function getInfo(Request $request)
    {
        DB::beginTransaction();
        try {
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
                }

                $response = [
                    'revenue'       => $revenue,
                    'total_trx'      => $dataCalculate['count'],
                    'amount_trx'     => $dataCalculate['amount'],
                    'shareholder'   => $shareholders
                ];
            } else {
                return response()->json([
                    'status'    => false,
                    'error'     => 'Group has not schema'
                ], 404);
            }

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

    private function calculate($group_id, $date)
    {
        $groups = Group::where('parent_id', $group_id)->get();
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

        return $result;
    }

    private function calculateOnly()
    {
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

        return $result;
    }

    public function rankTransactions()
    {
        $rankedTransactions = $this->transactionService->rankTransactionsByMerchantUser();
        return view('apps.transactions.rank', compact('rankedTransactions'));
    }

    public function reportFee()
    {
        $totalFees = TransactionFee::select('penerima', DB::raw('SUM(fee) as total_fee'))
            ->groupBy('penerima')
            ->get();

        return view('apps.transactions.list-fee', compact('totalFees'));
    }
}