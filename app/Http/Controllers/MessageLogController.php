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

        // Apply sorting
        $orderBy = $request->input('order_by', 'request_time');
        $orderType = $request->input('order_type', 'desc');
        $data->orderBy($orderBy, $orderType);

        // Paginate data
        $data = $data->paginate(10);

        // Return the view with the data
        return view('apps.messagelog.list')->with('data', $data);
    }

    public function historyList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'terminal_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid input data.',
                'details' => $validator->errors(),
            ], 422);
        }

        $serviceIds = ['T00002', 'OTT001', 'OT0001'];

        try {
            $logs = MessageLog::where('terminal_id', $request->terminal_id)
                ->whereIn('service_id', $serviceIds)
                ->whereNotNull('response_message')
                ->where('reply_time', '>=', Carbon::today())
                ->orderBy('reply_time', 'desc')
                ->get();

            if ($logs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No logs found for the given terminal ID.',
                ], 404);
            }

            $processedLogs = $logs->map(function ($log) {
                $requestMessage = json_decode($log->request_message, true);
                
                if (!isset($requestMessage['msg']['msg_dt'])) {
                    return null;
                }
                
                $msgDtArray = explode('|', $requestMessage['msg']['msg_dt']);
                switch ($log->service_id) {
                    case 'T00002':
                    case 'OTT001':
                        $indices = [1, 2, 5];
                        break;
                    case 'OT0001':
                        $indices = [1, 3, 4];
                        break;
                    default:
                        $indices = [];
                }

                $responseMessage = json_decode($log->response_message, true);
                if (isset($responseMessage['screen']['comps']['comp'])) {
                    foreach ($responseMessage['screen']['comps']['comp'] as $comp) {
                        if (strpos($comp['comp_lbl'], 'Keterangan') !== false) {
                            $keterangan = $comp['comp_values']['comp_value'][0]['value'];
                        }
                        if (strpos($comp['comp_lbl'], 'Fee') !== false) {
                            $fee = floatval($comp['comp_values']['comp_value'][0]['value']);
                        }
                    }
                }
                
                $noRek = isset($msgDtArray[$indices[0]]) ? $msgDtArray[$indices[0]] : null;
                $namaRek = isset($msgDtArray[$indices[1]]) ? $msgDtArray[$indices[1]] : null;
                $nominal = isset($msgDtArray[$indices[2]]) ? floatval($msgDtArray[$indices[2]]) : null;
                // $keterangan = isset($msgDtArray[$indices[3]]) ? $msgDtArray[$indices[3]] : null;

                // Reformat the log with extracted values
                return [
                    'id' => $log->id,
                    'terminal_id' => $log->terminal_id,
                    'service_id' => $log->service_id,
                    'no_rek' => $noRek,
                    'nama_rek' => $namaRek,
                    'nominal' => $nominal + $fee,
                    'keterangan' => $keterangan,
                    'status' => $log->message_status,
                    'request_message' => $log->request_message,
                    'response_message' => $log->response_message,
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
                'terminal_id' => 'required|string',
                'message_id' => 'required|string',
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
