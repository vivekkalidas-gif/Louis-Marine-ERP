<?php

namespace App\Http\Controllers\Admin\Security;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LoginHistory;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function activity()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(20);
        return view('admin.security.activity', compact('logs'));
    }

    public function logins()
    {
        $logins = LoginHistory::with('user')->latest()->paginate(20);
        return view('admin.security.logins', compact('logins'));
    }
}
