<?php
// Connect to ARDI

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;
use Carbon\Carbon;
use Validator;
use Excel;
use App\Exports\TransactionBJBExport;
use App\Exports\TransactionFeeLakupandaiExport;
use App\Exports\TransactionFeeSaleExport;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TransactionBJBCreateRequest;
use App\Http\Requests\TransactionBJBUpdateRequest;
use App\Repositories\TransactionBJBRepository;
use App\Validators\TransactionBJBValidator;
use Ixudra\Curl\Facades\Curl;

use App\Entities\Merchant;
use App\Entities\Service;
use App\Entities\TransactionBJB;
use App\Entities\Group;
use App\Entities\UserGroup;
use App\Entities\Revenue;
use App\Entities\Transaction;
use App\Entities\GroupSchema;
use App\Entities\GroupSchemaShareholder;
use App\Entities\MessageLog;
use App\Entities\TransactionLog;
use App\Exports\TransactionExportFeeLakupandai;
use App\Entities\ReportMiniBanking;

/**
 * Class TransactionBJBsController.
 *
 * @package namespace App\Http\Controllers;
 */
class TransactionBJBsController extends Controller
{
    /**
     * @var TransactionBJBRepository
     */
    protected $repository;

    /**
     * @var TransactionBJBValidator
     */
    protected $validator;

    /**
     * TransactionBJBsController constructor.
     *
     * @param TransactionBJBRepository $repository
     * @param TransactionBJBValidator $validator
     */
    public function __construct(TransactionBJBRepository $repository, TransactionBJBValidator $validator)
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

        // if(!$request->has('limit') && $request->get('limit')==''){
        //     $request->request->add([
        //         'limit'      => '1000'
        //     ]);
        // }

        // $request->request->add([
        //     'group_id'  => 1,
        //     'date'      => date("Y-m-d"),
        //     'schema_id' => 1,
        // ]);

        // $credentials = $request->only('group_id', 'date', 'schema_id');
        // $rules = [
        //     'group_id'  => 'required',
        //     'schema_id' => 'required',
        //     'date'      => 'required',
        // ];
        
        // $validator = Validator::make($credentials, $rules);
        // if($validator->fails()) {
        //     return response()->json(['status'=> false, 'error'=> $validator->messages()],403);
        // }

        // $getInfo    = $this->getInfo($request)->original;
        
        // $dataRevenue= (object) $getInfo;
        // $data->merchants = $sorted;
        
        // $response = [   
        //     'status'    => true, 
        //     'message'   => 'Success',
        //     'data'      => $data,
        // ];

        // return response()->json($response, 200);

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $quote = "'";
        $quotes = '"';
        $quoteFee = "'fee'";
        DB::connection()->enableQueryLog();
        
        //START QUERY
        // $data = MessageLog::select(DB::raw("channel.transaction_log.stan,
        //                     messagelog.request_time,
        //                     channel.transaction_log.tx_time,
        //                     messagelog.terminal_id AS tid,
        //                     terminal.merchant_id AS mid,
        //                     users.brand AS agent_name,
        //                     messagelog.service_id AS product_name,
        //                     service.service_name AS transaction_name,
        //                         CASE
        //                             WHEN messagelog.service_id::text = ANY (ARRAY['P00031'::character varying::text, 'P00033'::character varying::text]) THEN channel.transaction_log.tx_amount / 100::double precision
        //                             ELSE
        //                             CASE
        //                                 WHEN channel.transaction_log.tx_mti::text = '0400'::text THEN channel.transaction_log.tx_amount / 100::double precision
        //                                 ELSE channel.transaction_log.tx_amount
        //                             END
        //                         END AS nominal,
        //                         CASE
        //                             WHEN d.value IS NOT NULL THEN
        //                             CASE
        //                                 WHEN d.value::text <> 'null'::text THEN d.value::double precision
        //                                 ELSE 0::double precision
        //                             END
        //                             ELSE 0::double precision
        //                         END AS fee,
        //                         CASE
        //                             WHEN d.value IS NOT NULL THEN
        //                             CASE
        //                                 WHEN d.value::text <> 'null'::text THEN d.value::double precision * 0.6::double precision
        //                                 ELSE 0::double precision
        //                             END
        //                             ELSE 0::double precision
        //                         END AS agent_fee,
        //                         CASE
        //                             WHEN d.value IS NOT NULL THEN
        //                             CASE
        //                                 WHEN d.value::text <> 'null'::text THEN d.value::double precision * 0.2::double precision
        //                                 ELSE 0::double precision
        //                             END
        //                             ELSE 0::double precision
        //                         END AS bjb_fee,
        //                         CASE
        //                             WHEN d.value IS NOT NULL THEN
        //                             CASE
        //                                 WHEN d.value::text <> 'null'::text THEN d.value::double precision * 0.2::double precision
        //                                 ELSE 0::double precision
        //                             END
        //                             ELSE 0::double precision
        //                         END AS selada_fee,
        //                         CASE
        //                             WHEN messagelog.service_id::text = ANY (ARRAY['P00031'::character varying::text, 'P00033'::character varying::text]) THEN channel.transaction_log.tx_amount / 100::double precision
        //                             ELSE
        //                             CASE
        //                                 WHEN channel.transaction_log.tx_mti::text = '0400'::text THEN channel.transaction_log.tx_amount / 100::double precision
        //                                 ELSE channel.transaction_log.tx_amount
        //                             END
        //                         END +
        //                         CASE
        //                             WHEN d.value IS NOT NULL THEN
        //                             CASE
        //                                 WHEN d.value::text <> 'null'::text THEN d.value::double precision
        //                                 ELSE 0::double precision
        //                             END
        //                             ELSE 0::double precision
        //                         END AS total,
        //                     gb.value AS billid,
        //                     channel.transaction_log.proc_code,
        //                     messagelog.message_status,
        //                     channel.transaction_log.responsecode AS rc,
        //                         CASE
        //                             WHEN reversal_view.reversal_stan IS NOT NULL THEN 'Reversed'::text
        //                             ELSE
        //                             CASE
        //                                 WHEN channel.transaction_log.responsecode::text = '00'::text THEN 'Success'::text
        //                                 ELSE 'Failed'::text
        //                             END
        //                         END AS status,
        //                     reversal_view.reversal_stan,
        //                     reversal_view.reversal_rc,
        //                     reversal_view.reversal_time,
        //                     reversal_view.reversal_service_id,
        //                     channel.transaction_log.host_ref,
        //                     channel.transaction_log.tx_pan,
        //                     channel.transaction_log.src_account,
        //                     channel.transaction_log.dst_account,
        //                     messagelog.message_id,
        //                     to_char(messagelog.request_time, 'YYYY-MM-DD hh:mm:ss'::text) AS created_at,
        //                     NULL::text AS deleted_at"))
        //                     ->leftjoin('channel.transaction_log','messagelog.message_id', '=', 'channel.transaction_log.additional_data')
        //                     ->leftjoin('users', 'messagelog.terminal_id', '=', 'users.username')
        //                     ->leftjoin('terminal', 'messagelog.terminal_id', '=', 'terminal.terminal_id')
        //                     ->leftjoin('service', 'service.service_id', '=', 'messagelog.service_id')
        //                     ->leftjoin("reversal_view", "reversal_view.reversal_stan", "=", "channel.transaction_log.stan")
        //                     ->leftjoin('service_data as d', function($join){
        //                                     $join->on('message_id', '=', 'd.message_id');
        //                                     $join->whereRaw("(d.name = 'fee' OR d.name = 'margin' OR d.name = 'dynfee')");
        //                                 })
        //                     ->leftjoin('service_data as gb', function($join){
        //                         $join->on('message_id', '=', 'gb.message_id');
        //                         $join->whereRaw("gb.name = 'billid'");
        //                     })
        //                     ->whereNotNull('message_status')
        //                     ->whereRaw("((messagelog.service_id = ANY (ARRAY['MA0021'::character varying::text, 'MA0023'::character varying::text, 'MA0031'::character varying::text, 'MA0033'::character varying::text, 'MA0041'::character varying::text, 'MA0043'::character varying::text, 'MA0010'::character varying::text, 'MA0012'::character varying::text, 'MA0081'::character varying::text, 'MA0083'::character varying::text, 'MA0091'::character varying::text, 'MA0093'::character varying::text])) OR (messagelog.service_id::text = ANY (ARRAY['MA0060'::character varying::text, 'MA0063'::character varying::text, 'MA0050'::character varying::text, 'MA0051'::character varying::text, 'MA0073'::character varying::text, 'MA0071'::character varying::text, 'P00031'::character varying::text, 'P00033'::character varying::text])))");
        //END QUERY


                        //     WHERE 1 = 1 AND m.message_status IS NOT NULL AND ((m.service_id::text = ANY (ARRAY['MA0021'::character varying::text, 'MA0023'::character varying::text, 'MA0031'::character varying::text, 'MA0033'::character varying::text, 'MA0041'::character varying::text, 'MA0043'::character varying::text, 'MA0010'::character varying::text, 'MA0012'::character varying::text, 'MA0081'::character varying::text, 'MA0083'::character varying::text, 'MA0091'::character varying::text, 'MA0093'::character varying::text])) OR (m.service_id::text = ANY (ARRAY['MA0060'::character varying::text, 'MA0063'::character varying::text, 'MA0050'::character varying::text, 'MA0051'::character varying::text, 'MA0073'::character varying::text, 'MA0071'::character varying::text, 'P00031'::character varying::text, 'P00033'::character varying::text])))
                        // ORDER BY m.request_time DESC
                        // limit 10 offset 0"))->get();
  
        // set_time_limit(1000);
        // $data = TransactionBJB::select('*');
        // $data = TransactionBJB::select('*')->skip(0)->take(10)->get();

        if($request->has('start_date') || $request->has('end_date') || $request->has('status')){
            //skip
        } else {
        //     $checkTime = ReportMiniBanking::select('tx_time')->orderBy('tx_time', 'desc')->first();
        //     $limit = 100;
        //     $offset = 0;

        //     $getMiniBankingView = TransactionBJB::select('*')
        //                             ->where('tx_time', '>', $checkTime->tx_time)
        //                             ->whereNotNull('stan')
        //                             ->orderBy('tx_time', 'asc')
        //                             ->limit($limit)
        //                             ->offset($offset)
        //                             ->get();
            

        //     if($getMiniBankingView->count() > 0){
        //         foreach($getMiniBankingView as $item) {
        //             $report = new ReportMiniBanking();
        //             $report->stan                   = $item->stan;
        //             $report->request_time           = $item->request_time;
        //             $report->tx_time                = $item->tx_time;
        //             $report->tid                    = $item->tid;
        //             $report->mid                    = $item->mid;
        //             $report->agent_name             = $item->agent_name;
        //             $report->product_name           = $item->product_name;
        //             $report->transaction_name       = $item->transaction_name;
        //             $report->nominal                = $item->nominal;
        //             $report->fee                    = $item->fee;
        //             $report->agent_fee              = $item->agent_fee;
        //             $report->bjb_fee                = $item->bjb_fee;
        //             $report->selada_fee             = $item->selada_fee;
        //             $report->total                  = $item->total;
        //             $report->billid                 = $item->billid;
        //             $report->proc_code              = $item->proc_code;
        //             $report->message_status         = $item->message_status;
        //             $report->rc                     = $item->rc;
        //             $report->status                 = $item->status;
        //             $report->reversal_stan          = $item->reversal_stan;
        //             $report->reversal_rc            = $item->reversal_rc;
        //             $report->reversal_time          = $item->reversal_time;
        //             $report->reversal_service_id    = $item->reversal_service_id;
        //             $report->host_ref               = $item->host_ref;
        //             $report->tx_pan                 = $item->tx_pan;
        //             $report->src_account            = $item->src_account;
        //             $report->dst_account            = $item->dst_account;
        //             $report->message_id             = $item->message_id;
        //             $report->save();
        //         }
        //     }
        }

        // $data = ReportMiniBanking::select('*');
        $data = TransactionBJB::select('*');
        if(($request->has('start_date') || $request->has('end_date') || $request->has('status')) && $request->get('start_date')!=''){
            $data->where('tx_time', '>', $request->get('start_date').' 00:00:00')
                    ->where('tx_time', '<=', $request->get('end_date').' 23:59:59');
        } 
        // else {
        //     $data->where('tx_time', '>', date("Y-m-d").' 00:00:00')
        //             ->where('tx_time', '<=', date("Y-m-d").' 23:59:59');
        // }

        if(session()->get('user')->role_id == 2) {
            $data->where('tid', '=', session()->get('user')->username);
        }
        
        // $data = $data->where('tx_time', '>', $request->get('start_date').' 00:00:00')
        //                 ->where('tx_time', '<=', $request->get('end_date').' 23:59:59');

        if($request->has('search') && $request->get('search')!=''){
            $data->where(function($query) use ($request)
            {
                $query->where('tid', 'like', '%' . $request->get('search') . '%')
                ->orWhere('mid', 'like', '%' . $request->get('search') . '%')
                ->orWhere('agent_name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('transaction_name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('product_name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('stan', 'like', '%' . $request->get('search') . '%')
                ->orWhere('message_id', 'like', '%'.$request->get("search").'%')
                ->orWhere('status', 'like', '%' . $request->get('search') . '%')
                ->orWhere('rc', 'like', '%' . $request->get('search') . '%')
                ->orWhere('message_status', 'like', '%' . $request->get('search') . '%')
                ->orWhere('tx_pan', 'like', '%' . $request->get('search') . '%')
                ->orWhere('src_account', 'like', '%' . $request->get('search') . '%')
                ->orWhere('dst_account', 'like', '%' . $request->get('search') . '%');
            });
        }

        if($request->has('tid') && $request->get('tid')!=''){
            $data->where('tid', '=', $request->get('tid'));
        }
        if($request->has('mid') && $request->get('mid')!=''){
            $data->where('mid', '=', $request->get('mid'));
        }
        if($request->has('agent_name') && $request->get('agent_name')!=''){
            $data->where('agent_name', '=', $request->get('agent_name'));
        }
        if($request->has('message_status') && $request->get('message_status')!=''){
            $data->where('message_status', '=', $request->get('message_status'));
        }
        if($request->has('status') && $request->get('status')!='' && $request->get('status')!='Select Status'){
	        if ($request->get('status') == 'Success'){
                $data->where('status', '=', 'Success');
                $data->whereNull('reversal_stan');
            } else {
                $data->where(function($query)
                {
                    $query->where('rc', '!=', '00')
                    ->orWhereNotNull('reversal_stan');
                });
            }
        }
        if($request->has('rc') && $request->get('rc')!=''){
            $data->where('rc', '=', $request->get('rc'));
        }

        if($request->has('stan') && $request->get('stan')!=''){
            $data->where('stan', '=', $request->get('stan'));
        }
        if($request->has('message_id') && $request->get('message_id')!=''){
            $data->where('message_id', '=', $request->get('message_id'));
        }
        if($request->has('service') && $request->get('service')!='' && $request->get('service')!='Select Service'){
            $service = $request->get('service');
            if ($service == 'Tarik Tunai'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'MA0010')
                        ->orWhere('service_id', '=', 'MA0012');
                });
            }
            else if ($service == 'Payment Transfer Antar Bank'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'MA0021')
                    ->orWhere('service_id', '=', 'MA0023');
                });
            }
            else if ($service == 'Pemindahbukuan'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'MA0031')
                ->orWhere('service_id', '=', 'MA0033');
            });
            
            }
            else if ($service == 'Setor Tunai'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'MA0041')
                ->orWhere('service_id', '=', 'MA0043');
            });
            
            }
            else if ($service == 'Mini Statement'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'MA0050')
                ->orWhere('service_id', '=', 'MA0051');
            });
            
            }
            else if ($service == 'Info Saldo'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'MA0060')
                ->orWhere('service_id', '=', 'MA0063');
            });
            
            }
            else if ($service == 'Ganti PIN'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'MA0071')
                ->orWhere('service_id', '=', 'MA0073');
            });
            
            }
            else if ($service == 'Buka Rekening'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'MA0081')
                ->orWhere('service_id', '=', 'MA0083');
            });
            
            }
            else if ($service == 'Batal Rekening'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'MA0091')
                ->orWhere('service_id', '=', 'MA0093');
            });
            }   
            else if ($service == 'PBB'){
                $data->where(function($query)
                {
                    $query->where('service_id', '=', 'P00031')
                ->orWhere('service_id', '=', 'P00033');
            });
            
            }
        }

        if($request->has('limit')){
            $data->take($request->get('limit'));
            
            if($request->has('offset')){
            	$data->skip($request->get('offset'));
            }
        }

        if($request->has('fee')){
            if($request->get('fee') == 'true'){
                $data->where('fee', '<>', '0');
            }
        }

        // if($request->has('order_type')){
        //     if($request->get('order_type') == 'asc'){
        //         if($request->has('order_by')){
        //             $data->orderBy($request->get('order_by'));
        //         }else{
        //             $data->orderBy('created_at');
        //         }
        //     }else{
        //         if($request->has('order_by')){
        //             $data->orderBy($request->get('order_by'), 'desc');
        //         }else{
        //             $data->orderBy('created_at', 'desc');
        //         }
        //     }
        // }else{
        //     $data->orderBy('created_at', 'desc');
        // }

        // $dataRevenue = Array();
        // $dataRevenue['total_trx'] = $data->get()->count();
        // $dataRevenue['amount_trx']   = $data->get()->sum("nominal");
        // $dataRevenue['total_fee']   = $data->get()->sum("fee");
        // $dataRevenue['total_fee_agent']   = $data->get()->sum("agent_fee");
        // $dataRevenue['total_fee_bjb']   = $data->get()->sum("bjb_fee");
        // $dataRevenue['total_fee_selada']   = $data->get()->sum("selada_fee");

        // $total = $data->count();

        $data->orderBy('request_time', 'desc');
        $dataRevenue = $data->get();
        // $data = $data->take('50');
        $data = $data->paginate(10);
        // ->skip(0)->take(100);


        return view('apps.transactionsBJB.list')
                ->with('data', $data)
                ->with('dataRevenue', $dataRevenue);
    }

    public function export(Request $request)
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

	    return (new TransactionBJBExport($request))->download('transaction_bjb_export_'.$request->get('start_date').'_'.$request->get('end_date').'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportCSV(Request $request)
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

        return (new TransactionBJBExport($request))->download('transaction_bjb_export_'.$request->get('start_date').'_'.$request->get('end_date').'.csv', \Maatwebsite\Excel\Excel::CSV,[
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

        return (new TransactionBJBExport($request))->download('transaction_bjb_export_'.$request->get('start_date').'_'.$request->get('end_date').'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }

    private function getInfo(Request $request)
    {
        DB::beginTransaction();
        try {
            $date           = $request->date;
            $group_id       = $request->group_id;
            $schema_id      = $request->schema_id;
            $dataCalculate  = $this->calculate($group_id, $date, $request);
            
            // Check 
            $groupSchema = GroupSchema::where('group_id', $group_id)
                                        ->where('schema_id', $schema_id)
                                        ->first();
            if($groupSchema){
                $revenue = $dataCalculate['revenue'] * $groupSchema->share/100;

                // Check if this group is shareable
                if($groupSchema->is_shareable ==  true){
                    $shareholders = GroupSchemaShareholder::where('group_schema_id', $groupSchema->id)
                                                        ->with('shareholder')
                                                        ->get();
                    foreach($shareholders as $sh){
                        $sh->revenue = $sh->share/100 * $revenue;
                    }
                }else{
                    $shareholders = [];
                }

                $response = [
                    'revenue'       => $revenue,
                    'total_trx'     => $dataCalculate['count'],
                    'amount_trx'    => $dataCalculate['amount'],
                    'total_fee'    => $dataCalculate['fee'],
                    'total_fee_agent'    => $dataCalculate['fee_agent'],
                    'total_fee_bjb'    => $dataCalculate['amount'],
                    'shareholder'   => $shareholders
                ];
            }else{
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

	    return (new TransactionExportFeeLakupandai($request))->download('transaction_fee_lakupandai_export_'.$request->get('start_date').'_'.$request->get('end_date').'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function updateBjb(TransactionBJBUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $transaction = TransactionBJB::where('stan', $id);
            foreach($transaction as $trx){
                if($trx->proc_code == '500000') {
                    $reqData['tx_mti']               = '0400';
                    $reqData['rp_mti']               = '0410';

                    $data = $this->repository->update($reqData, $id);
        
                    if($data){    
                        DB::commit();
                        return Redirect::to('transactionBJB')
                                            ->with('message', 'Status updated');
                    }else{
                        DB::rollBack();
                        return Redirect::to('transactionBJB')
                                    ->with('error', $data->error)
                                    ->withInput();
                    }
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('transactionBJB')
                        ->with('error', $e)
                        ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('transactionBJB')
                        ->with('error', $e)
                        ->withInput();
        }
    }

    public function edit($id)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $data = TransactionBJB::where('stan', '=', $id)->first();
            // $data->where('stan', '=', $id);
            if($data) {
                $item = TransactionBJB::where('message_id',$data->message_id)
                                ->orWhere('stan',$data->stan)
                                ->get();
                foreach($item as $trx){
                    return view('apps.transactionsBJB.edit')
                    ->with('data', $trx);
                }
            }                
    }

    // public function edit($id)
    // {
    //     try {
    //         list($stan, $date) = explode("_", $id);
    //         $date = substr($date, 0, 10);
    //         $dateClause = $date . " 00:00:00";
    //         $transaction = TransactionBJB::select('*');
    //         $transaction->where('stan', '=', $stan);
    //         $transaction->where('tx_time', '>', $dateClause);
    //         // $transaction = TransactionBJB::find($stan);
    //         if($transaction){
    //             $data = TransactionBJB::where('message_id', '99000867034246320210228092531')
    //                             ->get();
    //             return view('apps.transactionsBJB.edit')
    //                     ->with('data', $data);
    //             // foreach($transaction as $trx){
    //             //     echo "masuk 1";
    //             //     if(strpos($trx->tx_time, $date) !== false){
                        
    //             //     }
    //             // }
    //         } else {
    //             return Redirect::to('transactionBJB')
    //                             ->with('error', 'Data not found');
    //         }
    //     } catch (Exception $e) {
    //         return Redirect::to('transactionBJB')
    //                         ->with('error', $e);
    //     } catch (\Illuminate\Database\QueryException $e) {
    //         return Redirect::to('transactionBJB')
    //                         ->with('error', $e);
    //     }
    // }

        /**
     * Update the specified resource in storage.
     *
     * @param  TransactionBJBUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(TransactionBJBUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);
            $data = $this->repository->update($request->all(), $id);
            // $item = TransactionLog::where('additional_data',$id)
            //                     ->orWhere('stan',$request->stan)
            //                     ->get();
            //                     echo $item;die();

            $affected = DB::table('channel.transaction_log')
              ->where('additional_data', $id)
              ->update(['tx_mti' => '0400']);
                // foreach($item as $trx){
                //     $trx->tx_mti = '0400';
                //     $trx->rp_mti = '0410';
                //     $trx->save();
                // }
                DB::commit();
                return Redirect::to('transactionBJB')
                                    ->with('message', 'Reversal updated');
            // if($data){
            //     // $transaction = TransactionLog::where('additional_data',$id)
            //     // ->update([
            //     //     'tx_mti' => '0400',
            //     //     'rp_mti' => '0410'
            //     // ]);
            //     // echo $transaction;
            //     $item = TransactionLog::where('additional_data',$id)
            //                     ->orWhere('stan',$request->stan)
            //                     ->get();
            //                     echo $item;
            //     foreach($item as $trx){
            //         $trx->tx_mti = '0400';
            //         $trx->rp_mti = '0410';
            //         $trx->save();
            //     }
            //     DB::commit();
            //     return Redirect::to('transactionBJB')
            //                         ->with('message', 'Reversal updated');
            // }else{
            //     DB::rollBack();
            //     return Redirect::to('transactionBJB/'.$id.'/edit')
            //                 ->with('error', $data->error)
            //                 ->withInput();
            // }
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::to('transactionBJB/'.$id.'/edit')
                        ->with('error', $e)
                        ->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return Redirect::to('transactionBJB/'.$id.'/edit')
                        ->with('error', $e)
                        ->withInput();
        }
    }

    private function calculate($group_id, $date, $request)
    {
        $groups = Group::where('parent_id', $group_id)->get();
        $result = [];
        $result['revenue']  = 0;
        $result['count']    = 0;
        $result['amount']   = 0;

        // Sum
        $data = TransactionBJB::whereRaw("1 = 1");
        if($request->has('search') && $request->get('search')!=''){
            $data->where(function($query) use ($request)
            {
                $query->where('tid', 'like', '%' . $request->get('search') . '%')
                ->orWhere('mid', 'like', '%' . $request->get('search') . '%')
                ->orWhere('agent_name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('transaction_name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('product_name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('stan', 'like', '%' . $request->get('search') . '%')
                ->orWhere('message_id', 'like', '%'.$request->get("search").'%')
                ->orWhere('status', 'like', '%' . $request->get('search') . '%')
                ->orWhere('rc', 'like', '%' . $request->get('search') . '%')
                ->orWhere('message_status', 'like', '%' . $request->get('search') . '%')
                ->orWhere('tx_pan', 'like', '%' . $request->get('search') . '%')
                ->orWhere('src_account', 'like', '%' . $request->get('search') . '%')
                ->orWhere('dst_account', 'like', '%' . $request->get('search') . '%');
            });
        }

        if($request->has('start_date') && $request->get('start_date')!=''){
            $data->where('request_time', '>', $request->get('start_date').' 00:00:00');
        }

        if($request->has('end_date') && $request->get('end_date')!=''){
            $data->where('request_time', '<=', $request->get('end_date').' 23:59:59');
        }


        if($request->has('tid') && $request->get('tid')!=''){
            $data->where('tid', '=', $request->get('tid'));
        }
        if($request->has('mid') && $request->get('mid')!=''){
            $data->where('mid', '=', $request->get('mid'));
        }
        if($request->has('agent_name') && $request->get('agent_name')!=''){
            $data->where('agent_name', '=', $request->get('agent_name'));
        }
        if($request->has('message_status') && $request->get('message_status')!=''){
            $data->where('message_status', '=', $request->get('message_status'));
        }
        if($request->has('status') && $request->get('status')!='' && $request->get('status')!='Select Status'){
            $data->where('status', '=', $request->get('status'));
        }
        if($request->has('rc') && $request->get('rc')!=''){
            $data->where('rc', '=', $request->get('rc'));
        }

        if($request->has('stan') && $request->get('stan')!=''){
            $data->where('stan', '=', $request->get('stan'));
        }
        if($request->has('message_id') && $request->get('message_id')!=''){
            $data->where('message_id', '=', $request->get('message_id'));
        }
        if($request->has('service') && $request->get('service')!='' && $request->get('status')!='Select Service'){
            $service = $request->get('service');
            if ($service == 'Tarik Tunai'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0010')
                        ->orWhere('product_name', '=', 'MA0012');
                });
                // $data->whereIn('product_name', ['MA0010','MA0012']);
            }
            else if ($service == 'Payment Transfer Antar Bank'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0021')
                    ->orWhere('product_name', '=', 'MA0023');
                });
            }
            else if ($service == 'Pemindahbukuan'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0031')
                ->orWhere('product_name', '=', 'MA0033');
            });
            
            }
            else if ($service == 'Setor Tunai'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0041')
                ->orWhere('product_name', '=', 'MA0043');
            });
            
            }
            else if ($service == 'Mini Statement'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0050')
                ->orWhere('product_name', '=', 'MA0051');
            });
            
            }
            else if ($service == 'Info Saldo'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0060')
                ->orWhere('product_name', '=', 'MA0063');
            });
            
            }
            else if ($service == 'Ganti PIN'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0071')
                ->orWhere('product_name', '=', 'MA0073');
            });
            
            }
            else if ($service == 'Buka Rekening'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0081')
                ->orWhere('product_name', '=', 'MA0083');
            });
            
            }
            else if ($service == 'Batal Rekening'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'MA0091')
                ->orWhere('product_name', '=', 'MA0093');
            });
            }   
            else if ($service == 'PBB'){
                $data->where(function($query)
                {
                    $query->where('product_name', '=', 'P00031')
                ->orWhere('product_name', '=', 'P00033');
            });
            
            }
        }
                
        $result['count'] = $data->count();

        $result['amount']   = $data->sum('total');
        $result['fee']   = $data->sum('fee');
        $result['agent_fee']   = $data->sum('agent_fee');
        $result['bjb_fee']   = $data->sum('bjb_fee');
        $result['selada_fee']   = $data->sum('selada_fee');
        // foreach ($groups as $item){
        //     // Check if id is leaf or not
        //     $check = Group::where('parent_id', $item->id)->first();
        //     if($check){
        //         $result = $this->calculate($item->id, $date);
        //     }else{
        //         // Count amount of transactions
        //         $userGroups = UserGroup::where('group_id', $item->id)->get();
        //         foreach($userGroups as $userGroup){
        //             $merchant = Merchant::where('user_id', $userGroup->user_id)->first();
        //             if($merchant){
        //                 $revenues = TransactionBJB::where('mid', $merchant->mid)
        //                                 // ->whereDate('created_at', $date)
        //                                 ->where('status', '00')
        //                                 ->get();
        //                 // Revenue
        //                 foreach($revenues as $revenue){
        //                    $result['revenue'] = $result['revenue'] + $revenue->fee;
        //                 }

        //                 // Count
        //                 $count = TransactionBJB::where('mid', $merchant->mid)
        //                                     ->where('status','00')
        //                                     ->count();
        //                 $result['count'] = $result['count'] + $count;

        //                 // Sum
        //                 $sum = TransactionBJB::where('mid', $merchant->mid)
        //                                     ->where('status','00')
        //                                     ->sum('total');
        //                 $result['amount'] = $result['amount'] + $sum;
        //             }
        //         }
        //     }
        // }
        return $result;
    }
}
