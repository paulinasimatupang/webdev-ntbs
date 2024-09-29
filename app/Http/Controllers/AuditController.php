<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Audit;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $data = Audit::select('*');
        $data = $data->get();

        return view('apps.audit.list')
                ->with('data', $data);
    }
}
