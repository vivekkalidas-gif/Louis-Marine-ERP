<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\OvertimeRecord;
use App\Models\User;
use App\Models\ServiceJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index()
    {
        $overtimeRecords = OvertimeRecord::with(['user', 'job.customer', 'approver'])
            ->latest('date')
            ->paginate(20);
            
        $employees = User::whereHas('employeeDetail')->get();
        $jobs = ServiceJob::with('customer')->where('status', '!=', 'Closed')->limit(50)->get();
        
        return view('admin.accounts.payroll.overtime.index', compact('overtimeRecords', 'employees', 'jobs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.5',
            'job_id' => 'nullable|exists:service_jobs,id',
        ]);

        OvertimeRecord::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'hours' => $request->hours,
            'job_id' => $request->job_id,
            'reason' => $request->reason,
            'status' => 'approved', // Auto-approve for now if admin adds it
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'Overtime Recorded');
    }

    public function destroy(OvertimeRecord $overtime)
    {
        $overtime->delete();
        return back()->with('success', 'Overtime Record Deleted');
    }
}
