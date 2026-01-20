<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountingPeriod;
use Illuminate\Http\Request;

class AccountingPeriodController extends Controller
{
    public function index()
    {
        $periods = AccountingPeriod::orderBy('end_date', 'desc')->paginate(10);
        return view('accounts.periods.index', compact('periods'));
    }

    public function create()
    {
        return view('accounts.periods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        AccountingPeriod::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_locked' => false,
        ]);

        return redirect()->route('accounts.periods.index')->with('success', 'Accounting Period created successfully.');
    }

    public function toggleLock(AccountingPeriod $period)
    {
        $period->update(['is_locked' => !$period->is_locked]);
        
        $status = $period->is_locked ? 'locked' : 'unlocked';
        return redirect()->back()->with('success', "Period {$status} successfully.");
    }

    public function destroy(AccountingPeriod $period)
    {
        // Prevent deleting if journal entries exist in this period?
        // For now, let's just allow it, but in a real app check for constraints.
        $period->delete();
        return redirect()->route('accounts.periods.index')->with('success', 'Period deleted.');
    }
}
