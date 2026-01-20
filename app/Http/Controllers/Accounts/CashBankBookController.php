<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashBankBookController extends Controller
{
    public function index()
    {
        // 1. Fetch all accounts of type 'cash', 'bank', 'liquidity'
        // We assume 'liquidity' covers both or specific types exist. Adjust based on CoA structure.
        // Based on previous file reads, CoA has a 'type' field.
        
        $accounts = ChartOfAccount::whereIn('type', ['cash', 'bank', 'liquidity'])
            ->with(['journalItems' => function($q) {
                // Get simplified balance? No, aggregating in PHP or distinct query might be better for performace if many items.
                // For now, let's use a subquery or attribute if available, or just sum it here for MVP.
            }])
            ->get();

        // Calculate current balance for each
        foreach ($accounts as $account) {
            $debit = $account->journalItems()->sum('debit');
            $credit = $account->journalItems()->sum('credit');
            $account->current_balance = $debit - $credit; // Asset: Dr - Cr
        }

        return view('accounts.cash_bank_book.index', compact('accounts'));
    }

    public function show(Request $request, ChartOfAccount $account)
    {
        // validate date range
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 1. Calculate Opening Balance (Everything before Start Date)
        $openingDebit = $account->journalItems()
            ->where('date', '<', $startDate)
            ->sum('debit');
            
        $openingCredit = $account->journalItems()
            ->where('date', '<', $startDate)
            ->sum('credit');
            
        $openingBalance = $openingDebit - $openingCredit;

        // 2. Fetch Transactions in Range
        $transactions = $account->journalItems()
            ->with(['entry', 'partner']) // Load related journal entry and partner
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Calculate Running Balance
        // We will do this in the view or prepare it here.
        // It's often cleaner to prepare a collection with running balances.
        
        $ledger = [];
        $runningBalance = $openingBalance;

        // Add Opening Balance Row
        $ledger[] = (object)[
            'date' => Carbon::parse($startDate),
            'entry_number' => null,
            'description' => 'Opening Balance',
            'partner' => null,
            'debit' => 0,
            'credit' => 0,
            'balance' => $runningBalance,
            'is_opening' => true
        ];

        foreach ($transactions as $item) {
            $runningBalance += ($item->debit - $item->credit);
            
            $ledger[] = (object)[
                'id' => $item->id,
                'date' => $item->date,
                'entry_number' => $item->entry->number ?? $item->entry->name, // Fallback
                'entry_id' => $item->entry->id,
                'description' => $item->label ?? $item->entry->description,
                'partner' => $item->partner ? $item->partner->name : '-',
                'debit' => $item->debit,
                'credit' => $item->credit,
                'balance' => $runningBalance,
                'is_opening' => false
            ];
        }
        
        // Pass Ledger as Collection
        $ledger = collect($ledger);

        return view('accounts.cash_bank_book.show', compact('account', 'ledger', 'startDate', 'endDate', 'openingBalance'));
    }
}
