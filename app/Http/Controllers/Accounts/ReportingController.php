<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounting\JournalItem;
use App\Models\Accounting\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportingController extends Controller
{
    /**
     * General Ledger View
     * Lists all Journal Items, filterable by date, account, partner, journal.
     */
    /**
     * General Ledger View
     * Lists all Journal Items, filterable by date, account, partner, journal.
     */
    public function generalLedger(Request $request, \App\Services\Accounting\GeneralLedgerService $service)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $accountId = $request->get('account_id');
        $partnerId = $request->get('partner_id');

        // Check cache or heavy load? GL can be huge.
        // For MVP, service returns array.
        $ledgerData = $service->generate($startDate, $endDate, $accountId, $partnerId);
        
        $accounts = ChartOfAccount::orderBy('code')->get();
        // Fetch partners (Customers only for now, can extend to Suppliers later)
        $partners = \App\Models\Customer::orderBy('company_name')->get();

        return view('accounts.reports.general_ledger', compact('ledgerData', 'accounts', 'partners', 'startDate', 'endDate'));
    }

    /**
     * Trial Balance
     * Summary of all accounts and their current Debit/Credit balances.
     * Must sum to 0.
     */
    public function trialBalance(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $accounts = ChartOfAccount::withSum(['journalItems as total_debit' => function($q) use ($date) {
               $q->where('date', '<=', $date);
            }], 'debit')
            ->withSum(['journalItems as total_credit' => function($q) use ($date) {
               $q->where('date', '<=', $date);
            }], 'credit')
            ->orderBy('code')
            ->get();
        
        // Calculate Net Balance for each
        $accounts->transform(function($account) {
            $account->net_balance = $account->total_debit - $account->total_credit;
            return $account;
        });

        // Exclude zero balance accounts optionally? 
        // $accounts = $accounts->filter(fn($a) => $a->net_balance != 0);

        return view('accounts.reports.trial_balance', compact('accounts', 'date'));
    }

    /**
     * Balance Sheet
     * Assets = Liabilities + Equity
     */
    public function balanceSheet(Request $request, \App\Services\Accounting\BalanceSheetService $service)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $comparisonDate = $request->get('comparison_date'); // Optional
        
        $serviceData = $service->generate($date, $comparisonDate);
        
        // Map to uppercase for View compatibility
        $data = [
            'Assets' => $serviceData['assets'],
            'Liabilities' => $serviceData['liabilities'],
            'Equity' => $serviceData['equity'],
            'Equity_Adjustment' => $serviceData['equity_adjustment'],
        ];

        if ($comparisonDate) {
             $data['Comparison_Assets'] = $serviceData['comparison_assets'];
             $data['Comparison_Liabilities'] = $serviceData['comparison_liabilities'];
             $data['Comparison_Equity'] = $serviceData['comparison_equity'];
             $data['Comparison_Equity_Adjustment'] = $serviceData['comparison_equity_adjustment'];
        }

        return view('accounts.reports.balance_sheet', compact('data', 'date', 'comparisonDate'));
    }

    /**
     * Profit & Loss
     * Income - Expenses
     */
    public function profitLoss(Request $request, \App\Services\Accounting\ProfitLossService $service)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfDay()->format('Y-m-d'));
        
        // Comparisons
        $compareStartDate = $request->get('compare_start_date');
        $compareEndDate = $request->get('compare_end_date');

        $data = $service->generate($startDate, $endDate, $compareStartDate, $compareEndDate);

        return view('accounts.reports.profit_loss', compact('data', 'startDate', 'endDate', 'compareStartDate', 'compareEndDate'));
    }
    /**
     * Cash Flow Statement
     * Direct Method / Liquidity Analysis
     */
    public function cashFlow(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfDay()->format('Y-m-d'));

        // 1. Identify Liquidity Accounts (Bank/Cash)
        $liquidityAccounts = ChartOfAccount::where('type', 'liquidity')->pluck('id');

        // 2. Fetch all Journal Items for these accounts
        $transactions = JournalItem::with(['entry.journal', 'entry.items.account'])
            ->whereIn('account_id', $liquidityAccounts)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // 3. Categorize Transactions
        $activities = [
            'operating' => ['in' => 0, 'out' => 0, 'items' => []],
            'investing' => ['in' => 0, 'out' => 0, 'items' => []],
            'financing' => ['in' => 0, 'out' => 0, 'items' => []],
        ];

        foreach ($transactions as $tx) {
            // Determine direction
            // Debit on Asset (Bank) = Increase (Inflow)
            // Credit on Asset (Bank) = Decrease (Outflow)
            $amount = $tx->debit - $tx->credit; // Positive = Inflow, Negative = Outflow

            // Find counterpart account to categorize
            // This is a simplification; complex entries might split across multiple accounts.
            // We look for the "other" lines in the same entry.
            $counterpart = $tx->entry->items->first(function ($item) use ($liquidityAccounts) {
                return !in_array($item->account_id, $liquidityAccounts->toArray());
            });

            $type = 'operating'; // Default
            $categoryName = 'General Operating';

            if ($counterpart && $counterpart->account) {
                $accType = $counterpart->account->type;
                $categoryName = $counterpart->account->name;

                // Investing: Fixed Assets, Non-current Assets
                if (in_array($accType, ['fixed_asset', 'non_current_asset'])) {
                    $type = 'investing';
                }
                // Financing: Equity, Non-current Liabilities
                elseif (in_array($accType, ['equity', 'non_current_liability'])) {
                    $type = 'financing';
                }
                // Operating: Income, Expense, Receivable, Payable, Current Asset/Liab
                else {
                    $type = 'operating';
                }
            }

            if ($amount > 0) {
                $activities[$type]['in'] += $amount;
            } else {
                $activities[$type]['out'] += abs($amount);
            }
        }

        // 4. Calculate Summary
        $summary = [
            'operating_net' => $activities['operating']['in'] - $activities['operating']['out'],
            'investing_net' => $activities['investing']['in'] - $activities['investing']['out'],
            'financing_net' => $activities['financing']['in'] - $activities['financing']['out'],
        ];

        $summary['net_change'] = $summary['operating_net'] + $summary['investing_net'] + $summary['financing_net'];

        // Opening Balance
        $openingBalance = JournalItem::whereIn('account_id', $liquidityAccounts)
            ->where('date', '<', $startDate)
            ->sum(DB::raw('debit - credit'));

        $closingBalance = $openingBalance + $summary['net_change'];

        return view('accounts.reports.cash_flow', compact('activities', 'summary', 'openingBalance', 'closingBalance', 'startDate', 'endDate'));
    }
    /**
     * PDF Exports
     */
    public function generalLedgerPdf(Request $request)
    {
        $query = JournalItem::with(['account', 'partner', 'entry.journal'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->has('account_id') && $request->account_id) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->has('start_date') && $request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->has('partner_id') && $request->partner_id) {
            $query->where('partner_id', $request->partner_id);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        // For PDF, get all matching records, no pagination
        $items = $query->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounts.reports.pdf.general_ledger', compact('items'));
        return $pdf->download('general_ledger_' . now()->format('Y_m_d') . '.pdf');
    }

    public function generalLedgerPrint(Request $request, \App\Services\Accounting\GeneralLedgerService $service)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $accountId = $request->get('account_id');
        $partnerId = $request->get('partner_id');

        $ledgerData = $service->generate($startDate, $endDate, $accountId, $partnerId);
        
        // Fetch Names for Display
        $accountName = $accountId ? ChartOfAccount::find($accountId)->name : null;
        $partnerName = $partnerId ? \App\Models\Customer::find($partnerId)->company_name : null;
        
        $settings = \App\Models\Setting::getAllCached();
        $company = [
            'name' => $settings['company_name'] ?? 'Louis Marine Equipment',
            'address' => $settings['company_address'] ?? 'Doha, Qatar',
            'phone' => $settings['company_phone'] ?? '',
            'email' => $settings['company_email'] ?? '',
            'trn' => $settings['company_trn'] ?? '',
            'logo' => $settings['company_logo'] ?? null,
        ];

        return view('accounts.reports.print.general_ledger', compact('ledgerData', 'startDate', 'endDate', 'accountName', 'partnerName', 'company'));
    }

    public function trialBalancePdf(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $accounts = ChartOfAccount::withSum(['journalItems as total_debit' => function($q) use ($date) {
               $q->where('date', '<=', $date);
            }], 'debit')
            ->withSum(['journalItems as total_credit' => function($q) use ($date) {
               $q->where('date', '<=', $date);
            }], 'credit')
            ->orderBy('code')
            ->get();
        
        $accounts->transform(function($account) {
            $account->net_balance = $account->total_debit - $account->total_credit;
            return $account;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounts.reports.pdf.trial_balance', compact('accounts', 'date'));
        return $pdf->download('trial_balance_' . $date . '.pdf');
    }

    public function balanceSheetPdf(Request $request)
    {
        // ... Reusing logic ...
        // To avoid code duplication in a real app, logic should be moved to Service/Trait.
        // For now, copying strictly for speed/certainty.
        $date = $request->get('date', now()->format('Y-m-d'));
        $data = [];
        $types = [
            'Assets' => ['liquidity', 'current_asset', 'fixed_asset', 'non_current_asset', 'prepayment', 'receivable'],
            'Liabilities' => ['payable', 'current_liability', 'non_current_liability'],
            'Equity' => ['equity']
        ];

        foreach ($types as $category => $accountTypes) {
             $accounts = ChartOfAccount::shouldCache()->whereIn('type', $accountTypes)
                ->withSum(['journalItems as balance' => function($q) use ($date) {
                     $q->where('date', '<=', $date);
                }], 'debit')
                ->withSum(['journalItems as credit_sum' => function($q) use ($date) {
                     $q->where('date', '<=', $date);
                }], 'credit')
                ->get();
                
             $accounts->map(function($a) {
                 $a->net_balance = $a->balance - $a->credit_sum;
                 return $a;
             });
             $data[$category] = $accounts;
        }

        $plTypes = ['income', 'other_income', 'expense', 'depreciation', 'cost_of_revenue'];
        $plBalance = DB::table('journal_items')
            ->join('chart_of_accounts', 'journal_items.account_id', '=', 'chart_of_accounts.id')
            ->whereIn('chart_of_accounts.type', $plTypes)
            ->where('journal_items.date', '<=', $date)
            ->sum(DB::raw('credit - debit'));
        $data['Equity_Adjustment'] = $plBalance;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounts.reports.pdf.balance_sheet', compact('data', 'date'));
        return $pdf->download('balance_sheet_' . $date . '.pdf');
    }

    public function profitLossPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfDay()->format('Y-m-d'));
        $incomeTypes = ['income', 'other_income'];
        $expenseTypes = ['expense', 'depreciation', 'cost_of_revenue'];

        $incomes = ChartOfAccount::whereIn('type', $incomeTypes)
             ->withSum(['journalItems as credit_sum' => function($q) use ($startDate, $endDate) {
                 $q->whereBetween('date', [$startDate, $endDate]);
             }], 'credit')
             ->withSum(['journalItems as debit_sum' => function($q) use ($startDate, $endDate) {
                 $q->whereBetween('date', [$startDate, $endDate]);
             }], 'debit')
             ->get(); 
        $incomes->map(function($a) {
             $a->net_balance = $a->credit_sum - $a->debit_sum; 
             return $a;
        });

        $expenses = ChartOfAccount::whereIn('type', $expenseTypes)
             ->withSum(['journalItems as debit_sum' => function($q) use ($startDate, $endDate) {
                 $q->whereBetween('date', [$startDate, $endDate]);
             }], 'debit')
             ->withSum(['journalItems as credit_sum' => function($q) use ($startDate, $endDate) {
                 $q->whereBetween('date', [$startDate, $endDate]);
             }], 'credit')
             ->get();
        $expenses->map(function($a) {
             $a->net_balance = $a->debit_sum - $a->credit_sum; 
             return $a;
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounts.reports.pdf.profit_loss', compact('incomes', 'expenses', 'startDate', 'endDate'));
        return $pdf->download('profit_loss_' . $endDate . '.pdf');
    }

    public function cashFlowPdf(Request $request)
    {
        // Reusing logic
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfDay()->format('Y-m-d'));
        $liquidityAccounts = ChartOfAccount::where('type', 'liquidity')->pluck('id');

        $transactions = JournalItem::with(['entry.journal', 'entry.items.account'])
            ->whereIn('account_id', $liquidityAccounts)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $activities = [
            'operating' => ['in' => 0, 'out' => 0, 'items' => []],
            'investing' => ['in' => 0, 'out' => 0, 'items' => []],
            'financing' => ['in' => 0, 'out' => 0, 'items' => []],
        ];

        foreach ($transactions as $tx) {
            $amount = $tx->debit - $tx->credit; 
            $counterpart = $tx->entry->items->first(function ($item) use ($liquidityAccounts) {
                return !in_array($item->account_id, $liquidityAccounts->toArray());
            });

            $type = 'operating'; 
            if ($counterpart && $counterpart->account) {
                $accType = $counterpart->account->type;
                if (in_array($accType, ['fixed_asset', 'non_current_asset'])) {
                    $type = 'investing';
                }
                elseif (in_array($accType, ['equity', 'non_current_liability'])) {
                    $type = 'financing';
                }
            }

            if ($amount > 0) $activities[$type]['in'] += $amount;
            else $activities[$type]['out'] += abs($amount);
        }

        $summary = [
            'operating_net' => $activities['operating']['in'] - $activities['operating']['out'],
            'investing_net' => $activities['investing']['in'] - $activities['investing']['out'],
            'financing_net' => $activities['financing']['in'] - $activities['financing']['out'],
        ];
        $summary['net_change'] = $summary['operating_net'] + $summary['investing_net'] + $summary['financing_net'];
        $openingBalance = JournalItem::whereIn('account_id', $liquidityAccounts)->where('date', '<', $startDate)->sum(DB::raw('debit - credit'));
        $closingBalance = $openingBalance + $summary['net_change'];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounts.reports.pdf.cash_flow', compact('activities', 'summary', 'openingBalance', 'closingBalance', 'startDate', 'endDate'));
        return $pdf->download('cash_flow_statement.pdf');
    }
    /**
     * General Ledger Drill-down
     */
    public function ledger(Request $request, \App\Models\Accounting\ChartOfAccount $account)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        
        $items = \App\Models\Accounting\JournalItem::with(['entry', 'costCenter'])
            ->where('account_id', $account->id)
            ->where('date', '<=', $date)
            ->whereHas('entry', function($q) {
                $q->posted();
            })
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);
            
        // Calculate running balance implies we need ALL previous data. 
        // For pagination ease, let's just show Total Balance at top and list transactions.
        $totalDebit = \App\Models\Accounting\JournalItem::where('account_id', $account->id)
            ->where('date', '<=', $date)
            ->whereHas('entry', fn($q) => $q->posted())
            ->sum('debit');

        $totalCredit = \App\Models\Accounting\JournalItem::where('account_id', $account->id)
            ->where('date', '<=', $date)
            ->whereHas('entry', fn($q) => $q->posted())
            ->sum('credit');
            
        return view('accounts.reports.ledger', compact('account', 'items', 'date', 'totalDebit', 'totalCredit'));
    }
}
