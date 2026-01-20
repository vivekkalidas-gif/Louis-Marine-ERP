<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date Logic (Default: This Year)
        $range = $request->input('range', 'year');
        if ($range == 'today') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($range == 'month') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        } elseif ($range == 'quarter') {
            $startDate = now()->startOfQuarter();
            $endDate = now()->endOfQuarter();
        } else {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        }

        // 1. Profitability (Income - Expense)
        // Adjust types based on actual Seeder/CoA structure.
        // Usually Types: income, expense, COGS
        $income = JournalItem::whereHas('account', fn($q) => $q->whereIn('type', ['income', 'revenue']))
            ->whereBetween('date', [$startDate, $endDate])
            ->sum(DB::raw('credit - debit')); // Income is Credit normal

        $expense = JournalItem::whereHas('account', fn($q) => $q->whereIn('type', ['expense', 'overhead', 'cogs']))
            ->whereBetween('date', [$startDate, $endDate])
            ->sum(DB::raw('debit - credit')); // Expense is Debit normal
            
        $netProfit = $income - $expense;

        // 2. Liquidity (Cash + Bank) - Point in Time (Current Balance)
        $liquidityAccounts = ChartOfAccount::whereIn('type', ['cash', 'bank', 'liquidity'])->get();
        $totalLiquidity = 0;
        foreach($liquidityAccounts as $acc) {
            $start = $acc->journalItems()->sum('debit');
            $end = $acc->journalItems()->sum('credit');
            $totalLiquidity += ($start - $end);
        }

        // 3. Receivables (AR) - Current Balance
        $arAccounts = ChartOfAccount::whereIn('type', ['receivable'])->get();
        $totalReceivable = 0;
        foreach($arAccounts as $acc) {
             $start = $acc->journalItems()->sum('debit');
             $end = $acc->journalItems()->sum('credit');
             $totalReceivable += ($start - $end); // Asset
        }
        
        // Include DRAFT invoices in Receivable count (since they represent expected income)
        $draftReceivables = Invoice::where('status', 'draft')->sum('total_amount');
        $totalReceivable += $draftReceivables;

        // 4. Payables (AP) - Current Balance
        $apAccounts = ChartOfAccount::whereIn('type', ['payable'])->get();
        $totalPayable = 0;
        foreach($apAccounts as $acc) {
             $start = $acc->journalItems()->sum('credit'); // Liability: Credit - Debit
             $end = $acc->journalItems()->sum('debit');
             $totalPayable += ($start - $end);
        }
        
        // 5. Monthly Trend (Revenue vs Expense) - Last 6 Months
        $labels = [];
        $revenueData = [];
        $expenseData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $labels[] = $monthStart->format('M Y');
            
            $rev = JournalItem::whereHas('account', fn($q) => $q->whereIn('type', ['income', 'revenue']))
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum(DB::raw('credit - debit'));
            $revenueData[] = $rev;
            
            $exp = JournalItem::whereHas('account', fn($q) => $q->whereIn('type', ['expense', 'overhead', 'cogs']))
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum(DB::raw('debit - credit'));
            $expenseData[] = $exp;
        }

        // Keep legacy counts for now if needed, or remove them. 
        // Keeping them for compatibility with widgets if reused.
        $pendingInvoices = Invoice::where('status', 'draft')->orWhere('status', 'sent')->count();
        $overdueInvoices = Invoice::where('status', 'overdue')->count();

        // 6. Completed Jobs Pending Invoice
        // Jobs that are completed but don't have an invoice yet
        $completedJobsPendingInvoice = \App\Models\ServiceJob::where('status', \App\Models\ServiceJob::STATUS_COMPLETED)
            ->whereDoesntHave('invoices')
            ->orderBy('completed_at', 'desc')
            ->with('customer')
            ->get();

        // 7. Dispatched Delivery Notes Pending Invoice
        $pendingDeliveryNotes = \App\Models\Storekeeper\DeliveryNote::where('status', 'dispatched')
            ->whereNull('invoice_id')
            ->orderBy('date', 'desc')
            ->with('customer')
            ->get();

        return view('accounts.dashboard', compact(
            'income', 'expense', 'netProfit', 
            'totalLiquidity', 'totalReceivable', 'totalPayable',
            'labels', 'revenueData', 'expenseData',
            'range', 'pendingInvoices', 'overdueInvoices',
            'completedJobsPendingInvoice', 'pendingDeliveryNotes'
        ));
    }
}
