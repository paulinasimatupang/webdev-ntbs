<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Entities\MessageLog;
use App\Entities\ResponseLog;
use App\Entities\TerminalBilliton;
use App\Entities\ServiceBilliton;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class MessageLogController extends Controller
{
    public function index(Request $request)
    {
        $data = MessageLog::select('*');

        if($request->has('search') && $request->get('search')!=''){
            $data->where('message_id', '=', $request->get('search'));
        }

        if($request->has('start_date') && $request->get('start_date')!=''){
            $data->where('request_time', '>', $request->get('start_date'). ' 00:00:00.000');
        }

        if($request->has('end_date') && $request->get('end_date')!=''){
            $data->where('request_time', '<=', $request->get('end_date'). ' 23:59:59.999');
        }

        if ($request->has('terminal_id') && $request->get('terminal_id') != '') {
            $data->where('terminal_id', '=', $request->get('terminal_id'));
        }

        if ($request->has('service_id') && $request->get('service_id') != '') {
            $data->where('service_id', '=', $request->get('service_id'));
        }

        $orderBy = $request->input('order_by', 'request_time');
        $orderType = $request->input('order_type', 'desc');
        $data->orderBy($orderBy, $orderType);

        $data = $data->paginate(10);

        return view('apps.messagelog.list')->with('data', $data);
    }

    public function historyList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'terminal_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid input data.',
                'details' => $validator->errors(),
            ], 422);
        }

        $serviceIds = ['T00002', 'OTT001', 'OT0001', 'BPR002', 'PLN003'];

        try {
            $logs = MessageLog::select('messagelog.*', 'service.service_name as service_name')
            ->join('service', 'messagelog.service_id', '=', 'service.service_id') // Join dengan tabel service
            ->where('messagelog.terminal_id', $request->terminal_id)
            ->whereIn('messagelog.service_id', $serviceIds)
            ->whereNotNull('messagelog.response_message')
            ->where('messagelog.reply_time', '>=', Carbon::today())
            ->orderBy('messagelog.reply_time', 'desc')
            ->get();

            if ($logs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No logs found for the given terminal ID.',
                ], 404);
            }

            $processedLogs = $logs->map(function ($log) {
                $serviceName =$log->service_name;
                $serviceName = preg_replace('/\b(bayar|review|otp|bl|wa|sms)\b/i', '', $serviceName);
                $serviceName = ucwords(trim($serviceName));
                $nominal = null;
                $fee = null;
                $responseMessage = json_decode($log->response_message, true);
                if (isset($responseMessage['screen']['comps']['comp'])) {
                    foreach ($responseMessage['screen']['comps']['comp'] as $comp) {
                        if (strpos($comp['comp_lbl'], 'Nominal') !== false) {
                            $nominal = floatval($comp['comp_values']['comp_value'][0]['value']);
                        }
                        else if (strpos($comp['comp_lbl'], 'Fee') !== false) {
                            $fee = floatval($comp['comp_values']['comp_value'][0]['value']);
                        }
                    }
                }

                return [
                    'id' => $log->message_id,
                    'terminal_id' => $log->terminal_id,
                    'fitur' => $serviceName,
                    'nominal' => $nominal+$fee,
                    'status' => $log->message_status,
                    'reply_time' => $log->reply_time,
                ];
            })->filter(); 

            return response()->json([
                'status' => true,
                'data' => $processedLogs,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Failed to retrieve message logs.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function historyDetail(Request $request)
    {
        try {
            $this->validate($request, [
                'terminal_id' => 'required',
                'message_id' => 'required',
            ]);

            $responseMessage =MessageLog::where('terminal_id', $request->terminal_id)
            ->where('message_id', $request->message_id)
            ->whereNotNull('response_message')
            ->get();

            if (is_null($responseMessage)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No log found for the given terminal ID and message ID.',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $responseMessage,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error retrieving message logs: '.$e->getMessage(), [
                'terminal_id' => $request->terminal_id,
                'message_id' => $request->message_id,
            ]);

            return response()->json([
                'status' => false,
                'error' => 'An error occurred while retrieving message logs.',
            ], 500);
        }
    }
}
