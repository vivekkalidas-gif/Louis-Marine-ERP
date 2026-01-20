<?php

namespace App\Services\Accounting;

use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeneralLedgerService
{
    /**
     * Generate General Ledger data for a specific account or all accounts.
     * 
     * @param string $startDate
     * @param string $endDate
     * @param int|null $accountId
     */
    public function generate($startDate, $endDate, $accountId = null, $partnerId = null)
    {
        $accountsQuery = ChartOfAccount::orderBy('code');
        
        if ($accountId) {
            $accountsQuery->where('id', $accountId);
        }

        $accounts = $accountsQuery->get();
        $reportData = [];

        foreach ($accounts as $account) {
            
            // 1. Calculate Opening Balance (Before Start Date)
            $openingStats = JournalItem::where('account_id', $account->id)
                ->where('date', '<', $startDate)
                ->whereHas('entry', fn($q) => $q->posted())
                ->when($partnerId, function($q) use ($partnerId) {
                    return $q->where('partner_id', $partnerId);
                })
                ->selectRaw('SUM(debit) as debit, SUM(credit) as credit')
                ->first();

            $openingBalance = ($openingStats->debit ?? 0) - ($openingStats->credit ?? 0);

            // 2. Fetch Transactions (Within Period)
            $transactions = JournalItem::with(['entry.journal', 'partner'])
                ->where('account_id', $account->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereHas('entry', fn($q) => $q->posted())
                ->when($partnerId, function($q) use ($partnerId) {
                    return $q->where('partner_id', $partnerId);
                })
                ->orderBy('date', 'asc') // Chronological for running balance
                ->orderBy('created_at', 'asc')
                ->get();

            // 3. Calculate Running Balance
            if ($transactions->isEmpty() && abs($openingBalance) < 0.001) {
                continue; // Skip accounts with no activity and no opening balance
            }

            $runningBalance = $openingBalance;
            $lines = [];

            foreach ($transactions as $tx) {
                $impact = $tx->debit - $tx->credit;
                $runningBalance += $impact;
                
                $tx->running_balance = $runningBalance;
                $lines[] = $tx;
            }

            $reportData[] = [
                'account' => $account,
                'opening_balance' => $openingBalance,
                'lines' => $lines,
                'closing_balance' => $runningBalance
            ];
        }

        return $reportData;
    }
}
