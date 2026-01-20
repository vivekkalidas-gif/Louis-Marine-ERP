<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Budget;
use App\Models\Accounting\BudgetItem;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with('creator')->latest()->get();
        return view('accounts.budgets.index', compact('budgets'));
    }

    public function create()
    {
        return view('accounts.budgets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'fiscal_year' => 'required',
            'period_type' => 'required|in:monthly,quarterly,yearly',
        ]);

        $budget = Budget::create([
            'name' => $request->name,
            'fiscal_year' => $request->fiscal_year,
            'period_type' => $request->period_type,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('accounts.budgets.show', $budget);
    }

    public function show(Budget $budget)
    {
        $budget->load(['items.account']);
        $accounts = ChartOfAccount::whereIn('type', ['income', 'expense', 'cost_of_revenue', 'other_income'])->orderBy('code')->get();
        
        // Calculate Actuals per Account (YTD or Period specific logic)
        // For simplicity: Load all YTD actuals for the fiscal year
        $startOfYear = Carbon::create($budget->fiscal_year, 1, 1);
        $endOfYear = Carbon::create($budget->fiscal_year, 12, 31);
        
        $actuals = JournalItem::select('account_id', DB::raw('SUM(debit - credit) as balance'))
            ->whereHas('journalEntry', function($q) { $q->posted(); })
            ->whereBetween('date', [$startOfYear, $endOfYear])
            ->groupBy('account_id')
            ->pluck('balance', 'account_id');
            
        // Map data for view
        $report = $accounts->map(function ($account) use ($budget, $actuals) {
            $budgetItem = $budget->items->where('chart_of_account_id', $account->id)->first();
            $budgetedAmount = $budgetItem ? $budgetItem->amount : 0;
            
            // For Expense: Actual > 0 is debit (spending).
            // For Income: Actual < 0 is credit (revenue).
            $actualAmount = $actuals[$account->id] ?? 0;
            
            // Normalize for display (Show positive numbers)
            if (in_array($account->type, ['income', 'other_income'])) {
               $actualAmount = -$actualAmount; 
            }
            
            $variance = $budgetedAmount - $actualAmount;
            $variancePercent = $budgetedAmount != 0 ? ($variance / $budgetedAmount) * 100 : 0;

            return [
                'account' => $account,
                'budgeted' => $budgetedAmount,
                'actual' => $actualAmount,
                'variance' => $variance,
                'variance_percent' => $variancePercent
            ];
        });

        return view('accounts.budgets.show', compact('budget', 'accounts', 'report'));
    }

    public function updateItems(Request $request, Budget $budget)
    {
        // Simple update of single item or bulk items
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.amount' => 'required|numeric',
        ]);

        foreach ($data['items'] as $item) {
            BudgetItem::updateOrCreate(
                [
                    'budget_id' => $budget->id,
                    'chart_of_account_id' => $item['account_id'],
                    'period_date' => Carbon::create($budget->fiscal_year, 1, 1) // Default to Yearly bucket for MVP
                ],
                [
                    'amount' => $item['amount']
                ]
            );
        }

        return back()->with('success', 'Budget updated successfully');
    }
}
