<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\AccountingJournal;

class AccountingSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Standard Chart of Accounts
        $accounts = [
            // Assets
            ['code' => '101000', 'name' => 'Cash', 'type' => 'liquidity', 'reconcile' => true],
            ['code' => '102000', 'name' => 'Bank', 'type' => 'liquidity', 'reconcile' => true],
            ['code' => '120000', 'name' => 'Accounts Receivable', 'type' => 'receivable', 'reconcile' => true],
            ['code' => '105000', 'name' => 'Inventory Asset', 'type' => 'current_asset', 'reconcile' => false],
            
            // Liabilities
            ['code' => '210000', 'name' => 'Accounts Payable', 'type' => 'payable', 'reconcile' => true],
            ['code' => '220000', 'name' => 'Tax Payable', 'type' => 'current_liability', 'reconcile' => true],
            
            // Equity
            ['code' => '300000', 'name' => 'Capital', 'type' => 'equity', 'reconcile' => false],
            ['code' => '999999', 'name' => 'Undistributed Profits/Losses', 'type' => 'equity', 'reconcile' => false],

            // Income
            ['code' => '400000', 'name' => 'Product Sales', 'type' => 'income', 'reconcile' => false],
            ['code' => '410000', 'name' => 'Service Income', 'type' => 'income', 'reconcile' => false],
            
            // Expenses
            ['code' => '600000', 'name' => 'Cost of Goods Sold', 'type' => 'cost_of_revenue', 'reconcile' => false],
            ['code' => '610000', 'name' => 'Office Expenses', 'type' => 'expense', 'reconcile' => false],
            ['code' => '620000', 'name' => 'Rent', 'type' => 'expense', 'reconcile' => false],
            ['code' => '630000', 'name' => 'Salaries', 'type' => 'expense', 'reconcile' => false],
        ];

        foreach ($accounts as $acc) {
            ChartOfAccount::firstOrCreate(
                ['code' => $acc['code']],
                [
                    'name' => $acc['name'],
                    'type' => $acc['type'],
                    'is_reconcilable' => $acc['reconcile']
                ]
            );
        }

        // 2. Create Default Journals
        $journals = [
            ['name' => 'Customer Invoices', 'code' => 'INV', 'type' => 'sale', 'default_account_code' => '400000'],
            ['name' => 'Vendor Bills', 'code' => 'BILL', 'type' => 'purchase', 'default_account_code' => '600000'],
            ['name' => 'Bank', 'code' => 'BNK1', 'type' => 'bank', 'default_account_code' => '102000'],
            ['name' => 'Cash', 'code' => 'CSH1', 'type' => 'cash', 'default_account_code' => '101000'],
            ['name' => 'General Operations', 'code' => 'GEN', 'type' => 'general', 'default_account_code' => null],
        ];

        foreach ($journals as $j) {
            $defaultAccount = null;
            if ($j['default_account_code']) {
                $defaultAccount = ChartOfAccount::where('code', $j['default_account_code'])->first();
            }

            AccountingJournal::firstOrCreate(
                ['code' => $j['code']],
                [
                    'name' => $j['name'],
                    'type' => $j['type'],
                    'default_account_id' => $defaultAccount?->id
                ]
            );
        }
    }
}
