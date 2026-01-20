<?php

namespace App\Services\Accounting;

use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class BalanceSheetService
{
    /**
     * Generate Balance Sheet Logic
     * STRICT validation: Assets = Liabilities + Equity
     */
    public function generate(string $date, ?string $comparisonDate = null)
    {
        $reportData = [
            'assets' => [],
            'liabilities' => [],
            'equity' => [],
            'equity_adjustment' => 0,
            'is_balanced' => true,
            'imbalance_amount' => 0,
            // Comparison Data
            'comparison_assets' => [],
            'comparison_liabilities' => [],
            'comparison_equity' => [],
            'comparison_equity_adjustment' => 0,
        ];

        // Helper to fetch data for a date
        $fetchForDate = function($targetDate) {
             $data = ['totals' => ['assets' => 0, 'liabilities' => 0, 'equity' => 0]];
             $types = [
                'assets' => ['liquidity', 'current_asset', 'fixed_asset', 'non_current_asset', 'prepayment', 'receivable'],
                'liabilities' => ['payable', 'current_liability', 'non_current_liability', 'credit_card'],
                'equity' => ['equity']
            ];

            foreach ($types as $category => $accountTypes) {
                $accounts = ChartOfAccount::whereIn('type', $accountTypes)
                    ->withSum(['journalItems as balance' => function($q) use ($targetDate) {
                        $q->where('date', '<=', $targetDate)
                          ->whereHas('entry', fn($e) => $e->posted());
                    }], 'debit')
                    ->withSum(['journalItems as credit_sum' => function($q) use ($targetDate) {
                        $q->where('date', '<=', $targetDate)
                          ->whereHas('entry', fn($e) => $e->posted());
                    }], 'credit')
                    ->get();
                
                $accounts->transform(function($account) {
                    $account->net_balance = $account->balance - $account->credit_sum;
                    return $account;
                });

                $data[$category] = $accounts;
                $data['totals'][$category] = $accounts->sum('net_balance');
            }

            // Equity Adjustment P&L
            $plTypes = ['income', 'other_income', 'expense', 'depreciation', 'cost_of_revenue'];
            $plBalance = DB::table('journal_items')
                ->join('chart_of_accounts', 'journal_items.account_id', '=', 'chart_of_accounts.id')
                ->join('journal_entries', 'journal_items.journal_entry_id', '=', 'journal_entries.id')
                ->whereIn('chart_of_accounts.type', $plTypes)
                ->where('journal_items.date', '<=', $targetDate)
                ->where('journal_entries.state', 'posted')
                ->sum(DB::raw('journal_items.debit - journal_items.credit')); 

            $data['equity_adjustment'] = $plBalance;
            $data['totals']['equity'] += $plBalance;
            
            return $data;
        };

        // 1. Current Period
        $current = $fetchForDate($date);
        $reportData['assets'] = $current['assets'];
        $reportData['liabilities'] = $current['liabilities'];
        $reportData['equity'] = $current['equity'];
        $reportData['equity_adjustment'] = $current['equity_adjustment'];
        
        // Strict Check
        $check = round($current['totals']['assets'] + $current['totals']['liabilities'] + $current['totals']['equity'], 2);
        if (abs($check) > 0.01) {
             $reportData['is_balanced'] = false;
             $reportData['imbalance_amount'] = $check;
        }

        // 2. Comparison Period
        if ($comparisonDate) {
            $prev = $fetchForDate($comparisonDate);
            $reportData['comparison_assets'] = $prev['assets']->keyBy('id');
            $reportData['comparison_liabilities'] = $prev['liabilities']->keyBy('id');
            $reportData['comparison_equity'] = $prev['equity']->keyBy('id');
            $reportData['comparison_equity_adjustment'] = $prev['equity_adjustment'];
        }

        return $reportData;
    }
}
