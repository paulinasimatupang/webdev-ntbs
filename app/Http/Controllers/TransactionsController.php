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

use App\Services\TransactionService;

use App\Entities\Merchant;
use App\Entities\Service;
use App\Entities\Transaction;
use App\Entities\TransactionStatus;
use App\Entities\transactionPaymentStatus;
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
        // Jika status tidak ada di request, tambahkan default status "Success"
        if (!$request->has('status') || $request->get('status') == '') {
            $request->request->add(['status' => 'Success']);
        }
    
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    
        // Membuat query untuk mendapatkan data transaksi
        $data = Transaction::select('*')
            ->with(['event', 'service', 'transactionStatus', 'user', 'merchant']);
    
        // Filter berdasarkan transaction_code jika diberikan
        if ($request->has('transaction_code') && $request->get('transaction_code') != '') {
            $transactionId = $request->get('transaction_code');
            $data->where('transaction_code', $transactionId);
        } else {
            // Jika transaction_code tidak diberikan, baru filter berdasarkan start_date dan end_date
            if ($request->has('start_date') && $request->get('start_date') != '' &&
                $request->has('end_date') && $request->get('end_date') != '') {
                
                $startDate = $request->get('start_date');
                $endDate = $request->get('end_date');
                $data->whereBetween('transaction_time', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ]);
            } else {
                // Tambahkan default start_date dan end_date jika tidak ada yang diberikan
                $startDate = date("Y-m-d");
                $endDate = date("Y-m-d");
                $data->whereBetween('transaction_time', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ]);
            }
        }
    
        // Filter berdasarkan status
        if ($request->has('status') && $request->get('status') != '' && $request->get('status') != 'Select Status') {
            $status = $request->get('status');
            switch ($status) {
                case 'Success':
                    $data->where('transaction_status_id', 0);
                    break;
                case 'Failed':
                    $data->where('transaction_status_id', 1);
                    break;
                case 'Pending':
                    $data->where('transaction_status_id', 2);
                    break;
            }
        }
    
        // Order by handling
        $orderType = $request->get('order_type', 'desc');
        $orderBy = $request->get('order_by', 'transaction_time');
        $data->orderBy($orderBy, $orderType);
    
        // Hitung total amount dan total fee
        $totalAmount = $data->sum('amount');
        $totalFee = $data->sum('fee');
    
        $dataRevenue = [
            'total_trx' => $data->count(),
            'amount_trx' => $totalAmount,
            'total_fee' => $totalFee,
            'total_fee_agent' => $totalFee * 0.6,
            'total_fee_ntbs' => $totalFee * 0.2,
            'total_fee_selada' => $totalFee * 0.2,
        ];
    
        // Pagination
        $data = $data->paginate(10);
    
        foreach ($data as $item) {
            $item->status_text = $this->getStatusText($item->transaction_status_id);
            $item->status_suspect = $item->is_suspect ? 'True' : 'False';
        }
    
        $user = session()->get('user');
    
        return view('apps.transactions.list')
            ->with('data', $data)
            ->with('dataRevenue', $dataRevenue)
            ->with('username', $user->username);
    }
    
    public function getStatusText($statusId)
    {
        switch ($statusId) {
            case 0:
                return 'Success';
            case 1:
                return 'Failed';
            case 2:
                return 'Pending';
            default:
                return 'Unknown';
        }
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
}
