<?php

namespace App\Services\Accounting;

use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalItem;
use Illuminate\Support\Facades\DB;

class ProfitLossService
{
    /**
     * Generate Profit & Loss Report
     */
    public function generate(string $startDate, string $endDate, ?string $compareStartDate = null, ?string $compareEndDate = null)
    {
        $report = [
            'income' => [],
            'cogs' => [], // Cost of Goods Sold / Cost of Revenue
            'expenses' => [],
            'totals' => [
                'income' => 0,
                'cogs' => 0,
                'gross_profit' => 0,
                'expenses' => 0,
                'net_profit' => 0,
            ],
            // Comparison Data
            'comparison' => [
                'income' => [],
                'cogs' => [],
                'expenses' => [],
                'totals' => [
                    'income' => 0,
                    'cogs' => 0,
                    'gross_profit' => 0,
                     'expenses' => 0,
                    'net_profit' => 0,
                ]
            ],
            'has_comparison' => !empty($compareStartDate)
        ];

        // Helper to fetch data
        $fetchData = function($start, $end) {
            $data = [
                'income' => collect(),
                'cogs' => collect(),
                'expenses' => collect(),
                'totals' => [
                     'income' => 0, 'cogs' => 0, 'expenses' => 0
                ]
            ];

            // 1. Income (Credit Balance is Positive)
            $incomeTypes = ['income', 'other_income'];
            $data['income'] = ChartOfAccount::whereIn('type', $incomeTypes)
                ->withSum(['journalItems as balance' => function($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end])
                      ->whereHas('entry', fn($e) => $e->posted());
                }], 'credit')
                ->withSum(['journalItems as debit_sum' => function($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end])
                      ->whereHas('entry', fn($e) => $e->posted());
                }], 'debit')
                ->get()
                ->map(function($a) {
                    $a->net_balance = $a->balance - $a->debit_sum; // Credit - Debit
                    return $a;
                })->filter(fn($a) => abs($a->net_balance) > 0.01);

            $data['totals']['income'] = $data['income']->sum('net_balance');


            // 2. COGS (Debit Balance is Positive Expense)
            $cogsTypes = ['cost_of_revenue'];
            $data['cogs'] = ChartOfAccount::whereIn('type', $cogsTypes)
                ->withSum(['journalItems as balance' => function($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end])
                      ->whereHas('entry', fn($e) => $e->posted());
                }], 'debit')
                ->withSum(['journalItems as credit_sum' => function($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end])
                      ->whereHas('entry', fn($e) => $e->posted());
                }], 'credit')
                ->get()
                ->map(function($a) {
                    $a->net_balance = $a->balance - $a->credit_sum; // Debit - Credit
                    return $a;
                })->filter(fn($a) => abs($a->net_balance) > 0.01);
            
            $data['totals']['cogs'] = $data['cogs']->sum('net_balance');

            // 3. Expenses (Debit Balance is Positive Expense)
            $expenseTypes = ['expense', 'depreciation', 'overhead', 'administrative', 'selling'];
            $data['expenses'] = ChartOfAccount::whereIn('type', $expenseTypes)
                ->withSum(['journalItems as balance' => function($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end])
                      ->whereHas('entry', fn($e) => $e->posted());
                }], 'debit')
                ->withSum(['journalItems as credit_sum' => function($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end])
                      ->whereHas('entry', fn($e) => $e->posted());
                }], 'credit')
                 ->get()
                ->map(function($a) {
                    $a->net_balance = $a->balance - $a->credit_sum; // Debit - Credit
                    return $a;
                })->filter(fn($a) => abs($a->net_balance) > 0.01);

            $data['totals']['expenses'] = $data['expenses']->sum('net_balance');
            
            return $data;
        };

        // --- Execute for Current Period ---
        $current = $fetchData($startDate, $endDate);
        $report['income'] = $current['income'];
        $report['cogs'] = $current['cogs'];
        $report['expenses'] = $current['expenses'];
        $report['totals'] = [
            'income' => $current['totals']['income'],
            'cogs' => $current['totals']['cogs'],
            'gross_profit' => $current['totals']['income'] - $current['totals']['cogs'],
            'expenses' => $current['totals']['expenses'],
            'net_profit' => ($current['totals']['income'] - $current['totals']['cogs']) - $current['totals']['expenses']
        ];

        // --- Execute for Comparison Period ---
        if ($compareStartDate && $compareEndDate) {
            $prev = $fetchData($compareStartDate, $compareEndDate);
            // Key by ID for easy lookup in view
            $report['comparison']['income'] = $prev['income']->keyBy('id');
            $report['comparison']['cogs'] = $prev['cogs']->keyBy('id');
            $report['comparison']['expenses'] = $prev['expenses']->keyBy('id');
            $report['comparison']['totals'] = [
                'income' => $prev['totals']['income'],
                'cogs' => $prev['totals']['cogs'],
                'gross_profit' => $prev['totals']['income'] - $prev['totals']['cogs'],
                'expenses' => $prev['totals']['expenses'],
                'net_profit' => ($prev['totals']['income'] - $prev['totals']['cogs']) - $prev['totals']['expenses']
            ];
        }

        return $report;
    }
}
