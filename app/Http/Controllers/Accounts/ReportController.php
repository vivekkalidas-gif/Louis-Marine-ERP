<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Overview Cards
        $totalRevenue = Payment::sum('amount');
        $outstandingAmount = Invoice::where('status', '!=', 'cancelled')->sum(DB::raw('total_amount - paid_amount'));
        $overdueInvoices = Invoice::where('due_date', '<', now())
            ->where('status', '!=', 'cancelled')
            ->whereRaw('total_amount > paid_amount')
            ->count();
            
        // 2. Recent Activity
        $recentPayments = Payment::with('invoice.customer')->latest()->take(5)->get();
        
        // 3. Monthly Revenue Chart Data (Last 6 Months)
        $monthlyRevenue = Payment::select(
            DB::raw('sum(amount) as total'), 
            DB::raw("strftime('%Y-%m', payment_date) as month")
        )
        ->where('payment_date', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

        // 4. Aging Report (Bucket Analysis) -> Simplified
        // 0-30 days, 31-60 days, 61-90 days, 90+ days
        // This is computationally expensive, doing simplified version
        
        return view('accounts.reports.index', compact(
            'totalRevenue', 
            'outstandingAmount', 
            'overdueInvoices',
            'recentPayments',
            'monthlyRevenue'
        ));
    }
}
