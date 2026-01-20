<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceJob;
use App\Models\User;
use App\Models\EquipmentItem;

class DashboardController extends Controller
{
    public function index()
    {
        $engineerId = auth()->id();

        // Show ALL jobs stats (engineers can view all jobs)
        $myJobsCount = ServiceJob::count();
        $completedCount = ServiceJob::where('status', 'completed')->count();
        $inProgressCount = ServiceJob::where('status', 'in-progress')->count();
        $customersCount = \App\Models\Customer::count();

        // Recent Jobs - Show ALL recent jobs (not just assigned)
        $recentJobs = ServiceJob::with(['customer', 'vessel'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Expiring Equipment
        $expiringEquipmentQuery = EquipmentItem::where('is_active', true)
            ->where('record_type', 'asset')
            ->whereDate('calibration_expiry_date', '>', now())
            ->whereDate('calibration_expiry_date', '<=', now()->addDays(30));
            
        $expiringEquipmentCount = $expiringEquipmentQuery->count();
        $expiringEquipmentIds = $expiringEquipmentQuery->pluck('id')->implode(',');

        return view('engineer.dashboard', compact(
            'myJobsCount',
            'completedCount',
            'inProgressCount',
            'customersCount',
            'recentJobs',
            'recentJobs',
            'expiringEquipmentCount',
            'expiringEquipmentIds'
        ));
    }
}
