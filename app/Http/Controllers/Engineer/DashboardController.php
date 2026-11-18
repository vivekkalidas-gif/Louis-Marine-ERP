<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceJob;
use App\Models\User;
use App\Models\EquipmentItem;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function index()
    {
        $engineerId = auth()->id();
        $today = now()->startOfDay();

        // Single query aggregations for performance
        $jobStats = ServiceJob::selectRaw("
            COUNT(*) as total_jobs,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN status = 'in-progress' THEN 1 ELSE 0 END) as in_progress_count,
            SUM(CASE WHEN DATE(scheduled_at) = ? THEN 1 ELSE 0 END) as todays_count,
            SUM(CASE WHEN scheduled_at < ? AND status NOT IN ('completed', 'cancelled') THEN 1 ELSE 0 END) as overdue_count
        ", [$today->format('Y-m-d'), $today])->first();

        $myJobsCount = $jobStats->total_jobs ?? 0;
        $completedCount = $jobStats->completed_count ?? 0;
        $inProgressCount = $jobStats->in_progress_count ?? 0;
        $todaysCount = $jobStats->todays_count ?? 0;
        $overdueCount = $jobStats->overdue_count ?? 0;

        $customersCount = Customer::count();

        // Recent Jobs - Optimized Eager Loading
        $recentJobs = ServiceJob::with([
                'customer:id,company_name,email',
                'vessel:id,name,imo_number'
            ])
            ->select(['id', 'job_number', 'title', 'customer_id', 'vessel_id', 'status', 'created_at', 'scheduled_at'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Today's Jobs Specific List
        $todaysJobs = ServiceJob::with([
                'customer:id,company_name',
                'vessel:id,name'
            ])
            ->whereDate('scheduled_at', $today)
            ->where('status', '!=', 'completed')
            ->limit(5)
            ->get();

        // Expiring Equipment Alert
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
            'todaysCount',
            'overdueCount',
            'customersCount',
            'recentJobs',
            'todaysJobs',
            'expiringEquipmentCount',
            'expiringEquipmentIds'
        ));
    }
}
