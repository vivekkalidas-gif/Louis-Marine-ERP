<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// Import Models
use App\Models\ServiceJob;
use App\Models\Invoice; 
use App\Models\InventoryItem; // Corrected from Product
use App\Models\Certificate;
use App\Models\User;
use App\Models\MaterialRequest; // For Pending Approvals

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Operational KPIs
        // Active Jobs (pending, assigned, in_progress)
        $activeJobsCount = ServiceJob::whereIn('status', ['pending', 'assigned', 'in_progress'])->count();
        $completedJobsThisMonth = ServiceJob::where('status', 'completed')
            ->whereMonth('created_at', now()->month) // Assuming completed_at might be null or created_at proxy
            ->count();
        
        // 2. Financial KPIs
        $pendingInvoicesCount = Invoice::whereIn('status', ['draft', 'sent', 'overdue'])->count();
            
        $monthlyRevenue = Invoice::where('status', 'paid')
            ->whereMonth('date', now()->month)
            ->sum('total_amount'); // Corrected from total

        // 3. Inventory KPIs
        $lowStockItems = InventoryItem::whereColumn('current_stock', '<=', 'minimum_stock_level')->count(); // Corrected columns

        // 4. Compliance
        $expiringCertificates = Certificate::whereBetween('expiry_date', [now(), now()->addDays(30)])->count();

        // 5. Team Stats
        $activeEngineers = User::whereHas('roles', fn($q) => $q->where('slug', 'engineer'))->count();
        
        // 6. Pending Approvals
        $pendingApprovals = MaterialRequest::where('status', 'pending')->count();

        // 7. Chart Data: Monthly Revenue (Last 6 Months)
        $revenueData = $this->getRevenueData();
        
        // 8. Chart Data: Job Status Distribution
        $jobStatusData = $this->getJobStatusData();

        return view('manager.dashboard', compact(
            'activeJobsCount',
            'completedJobsThisMonth',
            'pendingInvoicesCount',
            'monthlyRevenue',
            'lowStockItems',
            'expiringCertificates',
            'activeEngineers',
            'pendingApprovals',
            'revenueData',
            'jobStatusData'
        ));
    }

    private function getRevenueData()
    {
        // DB-Agnostic Approach (better for SQLite/MySQL compatibility)
        $revenues = Invoice::where('status', 'paid')
            ->where('date', '>=', now()->subMonths(6)) 
            ->get();
            
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('F');
            // $key = now()->subMonths($i)->format('Y-m');
            $data[$month] = 0; // Initialize
        }

        foreach ($revenues as $invoice) {
            $month = $invoice->date->format('F'); // formatted via cast
            if (isset($data[$month])) {
                $data[$month] += $invoice->total_amount; // Corrected column
            }
        }
            
        return [
            'labels' => array_keys($data),
            'data' => array_values($data),
        ];
    }

    private function getJobStatusData()
    {
        $stats = ServiceJob::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
            
        return [
            'labels' => $stats->pluck('status')->map(fn($s) => ucfirst($s)),
            'data' => $stats->pluck('total'),
        ];
    }
}
