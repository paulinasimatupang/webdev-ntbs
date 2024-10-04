<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entities\Audit;
use App\Entities\User;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        // Get all audits
        $data = Audit::all();

        // Get users for mapping
        $users = User::pluck('fullname', 'id')->toArray(); // Fetch user fullnames indexed by user id

        return view('apps.audit.list', compact('data', 'users'));
    }
}
