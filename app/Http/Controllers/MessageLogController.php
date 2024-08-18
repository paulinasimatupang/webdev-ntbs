<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entities\MessageLog;
use App\Entities\TerminalBilliton;
use App\Entities\ServiceBilliton;
use App\Http\Controllers\Controller;

class MessageLogController extends Controller
{
    public function index(Request $request)
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

        $data = MessageLog::select('*');

        if($request->has('message_id') && $request->get('message_id')!=''){
            $data->where('message_id', '=', $request->get('message_id'));
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
}
