<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FinanceCategory;
use App\Models\Customer;
use App\Models\ServiceJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['category', 'customer', 'job'])
            ->latest('date')
            ->paginate(20);
            
        $categories = FinanceCategory::where('type', 'expense')->get();
        $customers = Customer::orderBy('company_name')->get();
        // Load active jobs for dropdown
        $jobs = ServiceJob::with('customer')->where('status', '!=', 'Closed')->latest()->limit(50)->get(); 
        
        return view('admin.accounts.expenses.index', compact('expenses', 'categories', 'customers', 'jobs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'category_id' => 'required|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id',
            'job_id' => 'nullable|exists:service_jobs,id',
            'vendor_name' => 'required|string',
        ]);

        Expense::create([
            'date' => $request->date,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'tax_amount' => $request->tax_amount ?? 0,
            'payment_method' => $request->payment_method,
            'customer_id' => $request->customer_id,
            'job_id' => $request->job_id,
            'vendor_name' => $request->vendor_name,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
            'recorded_by' => Auth::id(),
        ]);

        // NEW: Double-Entry Logic
        $journalCode = (strtolower($request->payment_method) === 'cash') ? 'CSH1' : 'BNK1'; // Defaulting to Cash/Bank for expenses
        $journal = \App\Models\Accounting\AccountingJournal::where('code', $journalCode)->first();
        
        // Debit Account (Expense) - ideally mapped to Category, but defaulting to Office Expense for now
        $expenseAccount = \App\Models\Accounting\ChartOfAccount::where('code', '610000')->first(); 
        
        // Credit Account (Bank/Cash)
        $paymentAccount = $journal ? $journal->defaultAccount : null;

        if ($journal && $expenseAccount && $paymentAccount) {
                $entry = \App\Models\Accounting\JournalEntry::create([
                'journal_id' => $journal->id,
                'date' => $request->date,
                'name' => 'EXP-' . $request->reference_number, // Or generated
                'reference' => 'Expense: ' . $request->vendor_name,
                'state' => 'posted',
                'entity_id' => $expense->id,
                'entity_type' => Expense::class,
                ]);

                // 1. Debit Expense (Cost increases)
                \App\Models\Accounting\JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $expenseAccount->id,
                'name' => 'Expense - ' . $request->category_id, // category name would be better
                'debit' => $request->amount,
                'credit' => 0,
                'date' => $request->date,
                ]);

                // 2. Credit Bank/Cash (Asset decreases)
                \App\Models\Accounting\JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $paymentAccount->id,
                'name' => 'Payment for Expense',
                'debit' => 0,
                'credit' => $request->amount,
                'date' => $request->date,
                ]);
        }

        return back()->with('success', 'Expense Recorded Successfully');
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'date' => 'required|date',
            'category_id' => 'required|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'vendor_name' => 'required|string',
        ]);

        $expense->update($request->only([
            'date', 'category_id', 'amount', 'tax_amount', 'payment_method', 
            'customer_id', 'job_id', 'vendor_name', 'reference_number', 'notes'
        ]));

        return back()->with('success', 'Expense Updated Successfully');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense Deleted Successfully');
    }
}
