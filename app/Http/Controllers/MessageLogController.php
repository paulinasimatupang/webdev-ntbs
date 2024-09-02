<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Entities\MessageLog;
use App\Entities\ResponseLog;
use App\Entities\TerminalBilliton;
use App\Entities\ServiceBilliton;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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
            ->select('request_message')
            ->get();

            if ($logs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No logs found for the given terminal ID.',
                ], 404);
            }
    
            return response()->json([
                'status' => true,
                'data' => $logs,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Failed to retrieve message logs.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function historyDetail($terminal_id, $message_id)
    {
        $validator = Validator::make([
            'terminal_id' => $terminal_id,
            'message_id' => $message_id,
        ], [
            'terminal_id' => 'required|integer',
            'message_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid input data.',
                'details' => $validator->errors(),
            ], 422); 
        }

        try {
            $log = MessageLog::where('terminal_id', $terminal_id)
                ->where('message_id', $message_id)
                ->select('response_message')
                ->first();

            if (!$log) {
                return response()->json([
                    'status' => false,
                    'message' => 'No log found for the given terminal ID and message ID.',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $log->response_message,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Failed to retrieve message logs.',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }
}
