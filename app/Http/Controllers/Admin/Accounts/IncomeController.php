<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\FinanceCategory;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::with(['category', 'customer'])
            ->latest('date')
            ->paginate(20);
            
        $categories = FinanceCategory::where('type', 'income')->get();
        $customers = Customer::orderBy('company_name')->get();
        
        return view('admin.accounts.incomes.index', compact('incomes', 'categories', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'category_id' => 'required|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string',
        ]);

        $income = Income::create([
            'date' => $request->date,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'customer_id' => $request->customer_id,
            'reference_number' => $request->reference_number,
            'description' => $request->description,
            'notes' => $request->notes,
            'recorded_by' => Auth::id(),
            'status' => 'approved', // Manual entry is auto-approved
        ]);

        // NEW: Double-Entry Logic
        $journalCode = (strtolower($request->payment_method) === 'cash') ? 'CSH1' : 'BNK1'; 
        $journal = \App\Models\Accounting\AccountingJournal::where('code', $journalCode)->first();
        
        // Debit Account (Bank/Cash)
        $assetAccount = $journal ? $journal->defaultAccount : null;
        
        // Credit Account (Income) - Defaulting to Service Income
        $incomeAccount = \App\Models\Accounting\ChartOfAccount::where('code', '410000')->first(); 

        if ($journal && $assetAccount && $incomeAccount) {
                $entry = \App\Models\Accounting\JournalEntry::create([
                'journal_id' => $journal->id,
                'date' => $request->date,
                'name' => 'INC-' . ($request->reference_number ?? uniqid()), 
                'reference' => 'Income: ' . $request->description,
                'state' => 'posted',
                'entity_id' => $income->id,
                'entity_type' => Income::class,
                ]);

                // 1. Debit Bank/Cash (Asset increases)
                \App\Models\Accounting\JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $assetAccount->id,
                'name' => 'Income Received',
                'debit' => $request->amount,
                'credit' => 0,
                'date' => $request->date,
                ]);

                // 2. Credit Income (Revenue increases)
                \App\Models\Accounting\JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $incomeAccount->id,
                'name' => 'Income - ' . $request->category_id,
                'debit' => 0,
                'credit' => $request->amount,
                'date' => $request->date,
                ]);
        }

        return back()->with('success', 'Income Recorded Successfully');
    }

    public function update(Request $request, Income $income)
    {
        if ($income->reference_type) {
            // Prevent editing auto-generated income strictly? allow minor edits.
            // For now, allow all.
        }

        $request->validate([
            'date' => 'required|date',
            'category_id' => 'required|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $income->update($request->only([
            'date', 'category_id', 'amount', 'payment_method', 
            'customer_id', 'reference_number', 'description', 'notes'
        ]));

        return back()->with('success', 'Income Updated Successfully');
    }

    public function destroy(Income $income)
    {
        // If linked to payment, warn?
        if ($income->reference_type) {
             return back()->with('error', 'Cannot delete auto-generated income. Manage the source Payment instead.');
        }

        $income->delete();
        return back()->with('success', 'Income Deleted Successfully');
    }
}
