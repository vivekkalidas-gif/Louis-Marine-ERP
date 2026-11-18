<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeLedgerEntry;
use App\Models\PayrollEntry;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::whereHas('employeeDetail')
            ->with(['roles', 'employeeDetail'])
            ->get();  
        return view('admin.staff.index', compact('staff'));
    }

    public function show(User $staff)
    {
        // View handles data loading explicitly now via fix_view logic mostly,
        // but we keep this for standard behavior
        $staff->load(['employeeDetail', 'roles']);
        
        $ledger = \App\Models\EmployeeLedgerEntry::where('user_id', $staff->id)
            ->orderBy('date', 'desc')
            ->get();

        $balance = $ledger->sum('debit') - $ledger->sum('credit');

        $payrolls = \App\Models\PayrollEntry::where('user_id', $staff->id)
            ->with('payroll')
            ->get()
            ->sortByDesc(function($entry) {
                return $entry->payroll->month ?? '';
            });

        return view('admin.staff.show', compact('staff', 'ledger', 'balance', 'payrolls'));
    }

    public function storeData(Request $request, User $user)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:advance,repayment',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
        ]);

        EmployeeLedgerEntry::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'type' => $request->type === 'advance' ? 'advance_given' : 'manual_repayment',
            'debit' => $request->type === 'advance' ? $request->amount : 0,
            'credit' => $request->type === 'repayment' ? $request->amount : 0,
            'description' => $request->description,
            'amount' => $request->amount,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Entry recorded successfully.');
    }

    // NEW METHODS FOR CRUD & PRINT

    public function updateLedgerEntry(Request $request, $id)
    {
        $entry = EmployeeLedgerEntry::findOrFail($id);
        
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
        ]);

        // Recalculate debit/credit based on original type
        $isAdvance = in_array($entry->type, ['advance_given', 'advance']);
        
        $entry->update([
            'date' => $request->date,
            'amount' => $request->amount,
            'debit' => $isAdvance ? $request->amount : 0,
            'credit' => !$isAdvance ? $request->amount : 0,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Entry updated successfully.');
    }

    public function destroyLedgerEntry($id)
    {
        $entry = EmployeeLedgerEntry::findOrFail($id);
        $entry->delete();
        return back()->with('success', 'Entry deleted successfully.');
    }

    public function printLedgerEntry($id)
    {
        $entry = EmployeeLedgerEntry::with('user')->findOrFail($id);
        return view('admin.staff.ledger.receipt', compact('entry'));
    }
}