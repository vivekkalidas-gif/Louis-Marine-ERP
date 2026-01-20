<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource (Tree View).
     */
    public function index()
    {
        // 1. Fetch all accounts
        // To build a tree, we can fetch all and structure in PHP or fetch roots and eager load children (recursive).
        // Since Laravel doesn't support recursive eager matching easily without extra packages, and COA size is manageable (< 1000 usually),
        // we fetch all and build tree in View or use a flat list with 'level' / 'path' if using Modified Preorder Tree Traversal (MPTT).
        // For simplicity: Fetch all ordered by Code.
        
        $accounts = ChartOfAccount::with('parent')->orderBy('code')->get();
        // We can pass a flat list and let the view/JS handle the tree or group by type.
        
        $types = [
            'asset' => 'Assets',
            'liability' => 'Liabilities',
            'equity' => 'Equity',
            'income' => 'Income',
            'expense' => 'Expenses'
        ];

        return view('accounts.coa.index', compact('accounts', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:chart_of_accounts,code|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:asset,liability,equity,income,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'is_reconcilable' => 'boolean',
            'is_active' => 'boolean'
        ]);

        ChartOfAccount::create($request->all());

        return redirect()->route('accounts.coa.index')->with('success', 'Account created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        // Check if transactions exist before allowing Type change?
        if ($chartOfAccount->journalItems()->exists()) {
             // Block critical changes
             $request->validate([
                'name' => 'required|string|max:255',
                'code' => ['required', 'string', 'max:20', Rule::unique('chart_of_accounts')->ignore($chartOfAccount->id)],
                // Type cannot be changed easily if transactions exist due to strict accounting rules.
             ]);
             
             if ($request->type !== $chartOfAccount->type) {
                 return back()->with('error', 'Cannot change Account Type because transactions exist.');
             }
        } else {
             $request->validate([
                'code' => ['required', 'string', 'max:20', Rule::unique('chart_of_accounts')->ignore($chartOfAccount->id)],
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:asset,liability,equity,income,expense',
             ]);
        }

        $chartOfAccount->update($request->except(['type'])); // If type check passed above or not supplied

        if (!$chartOfAccount->journalItems()->exists()) {
             // Allow type update if safe
             if ($request->has('type')) {
                 $chartOfAccount->update(['type' => $request->type]);
             }
        }

        return redirect()->route('accounts.coa.index')->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChartOfAccount $chartOfAccount)
    {
        if ($chartOfAccount->journalItems()->exists()) {
            return back()->with('error', 'Cannot delete account with existing transactions. Deactivate it instead.');
        }
        
        if ($chartOfAccount->children()->exists()) {
             return back()->with('error', 'Cannot delete parent account. Reassign or delete children first.');
        }

        $chartOfAccount->delete();

        return redirect()->route('accounts.coa.index')->with('success', 'Account deleted successfully.');
    }
}
