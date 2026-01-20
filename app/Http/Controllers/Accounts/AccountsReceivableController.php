<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Services\Accounting\AccountsReceivableService;
use App\Models\Customer;
use Illuminate\Http\Request;

class AccountsReceivableController extends Controller
{
    protected $arService;

    public function __construct(AccountsReceivableService $arService)
    {
        $this->arService = $arService;
    }

    /**
     * AR Dashboard / Aging Report
     */
    public function index(Request $request)
    {
        // Global Aging
        $aging = $this->arService->getAgingReport();

        // Customer Summary (Top Debtors)
        // This logic could be moved to service if complex
        $customers = Customer::whereHas('invoices', function($q) {
                $q->where('status', '!=', 'paid')->where('status', '!=', 'cancelled');
            })
            ->withSum(['invoices as outstanding_balance' => function($q) {
                $q->where('status', '!=', 'paid')->where('status', '!=', 'cancelled');
                // Calculate balance: sum(total - paid). 
                // Creating a raw subquery or accessor is better, but withSum sums columns.
                // We need sum(total_amount - paid_amount).
                // Simplified: We'll fetch and calculate in PHP for MVP or use DB::raw.
            }], 'total_amount') // This is wrong, withSum only sums one column
            ->get()
            ->map(function($customer) {
                // recalculate explicitly
                 $customer->outstanding_balance = $customer->invoices()
                    ->where('status', '!=', 'paid')
                    ->where('status', '!=', 'cancelled')
                    ->get()
                    ->sum('balance');
                 return $customer;
            })
            ->where('outstanding_balance', '>', 0)
            ->sortByDesc('outstanding_balance');

        return view('accounts.ar.index', compact('aging', 'customers'));
    }
}
