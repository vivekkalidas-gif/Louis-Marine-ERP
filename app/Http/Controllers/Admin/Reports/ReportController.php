<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Product;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default Date Range: Last 30 Days
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // KPI: Total Revenue (Paid Invoices)
            $totalRevenue = Invoice::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'PAID')
            ->sum('total_amount');

        // KPI: Outstanding Revenue
        $outstandingRevenue = Invoice::where('status', '!=', 'PAID')
            ->where('status', '!=', 'CANCELLED')
            ->where('status', '!=', 'DRAFT')
            ->sum(DB::raw('total_amount - (select coalesce(sum(amount),0) from payments where payments.invoice_id = invoices.id)'));

        // KPI: Active Jobs
        $activeJobs = ServiceJob::whereIn('status', ['Assigned', 'In Progress'])->count();

        // KPI: Certificates Issued
        $certsIssued = Certificate::whereBetween('issue_date', [$startDate, $endDate])->count();
        
        // Chart Data: Jobs by Status
        $jobStats = ServiceJob::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')->toArray();

        // Chart Data: Revenue Trend (Last 6 Months)
        $revenueTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $total = Invoice::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->where('status', 'PAID')
                ->sum('total_amount');
            $revenueTrend[$month->format('M Y')] = $total;
        }

        return view('admin.reports.index', compact(
            'startDate', 'endDate', 
            'totalRevenue', 'outstandingRevenue', 'activeJobs', 'certsIssued',
            'jobStats', 'revenueTrend'
        ));
    }

    public function operations(Request $request) {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $jobs = ServiceJob::with(['customer', 'engineer'])
            ->whereBetween('created_at', ["$startDate 00:00:00", "$endDate 23:59:59"])
            ->get();

        $engineerStats = User::where('role', 'engineer')->withCount(['assignedJobs' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', ["$startDate 00:00:00", "$endDate 23:59:59"]);
        }])->get();

        return view('admin.reports.operations', compact('jobs', 'engineerStats', 'startDate', 'endDate'));
    }

    public function finance(Request $request) {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $invoices = Invoice::with('customer')->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'desc')->get();
        $quotations = Quotation::with('customer')->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'desc')->get();

        $vatCollected = $invoices->where('status', '!=', 'CANCELLED')->sum('tax');

        return view('admin.reports.finance', compact('invoices', 'quotations', 'vatCollected', 'startDate', 'endDate'));
    }

    public function inventory(Request $request) {
        $lowStock = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')->get();
        
        // Simple valuation
        $totalValuation = Product::sum(DB::raw('stock_quantity * price'));
        
        // Recent movements would require distinct transaction query if granular
        // For summmary:
        $products = Product::orderBy('stock_quantity', 'asc')->limit(50)->get();

        return view('admin.reports.inventory', compact('lowStock', 'totalValuation', 'products'));
    }

    public function certificates(Request $request) {
        $expiringSoon = Certificate::where('expiry_date', '>', now())
            ->where('expiry_date', '<', now()->addDays(60))
            ->orderBy('expiry_date', 'asc')
            ->get();

        return view('admin.reports.certificates', compact('expiringSoon'));
    }
}
