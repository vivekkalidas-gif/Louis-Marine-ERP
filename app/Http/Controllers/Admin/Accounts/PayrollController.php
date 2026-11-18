<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index()
    {
        $payrolls = Payroll::with('processor')->latest('month')->get();
        return view('admin.accounts.payroll.index', compact('payrolls'));
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['entries.user', 'entries.user.employeeDetail']);
        return view('admin.accounts.payroll.show', compact('payroll'));
    }

    public function generate(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']); // 2024-01

        try {
            $payroll = $this->payrollService->generatePayroll($request->month);

            // Check for skipped overtime (Users with OT but no Payroll Entry)
            $startDate = \Carbon\Carbon::parse($request->month . '-01');
            $endDate = $startDate->copy()->endOfMonth();
            
            $processedUserIds = $payroll->entries()->pluck('user_id');
            
            $skipped = \App\Models\OvertimeRecord::whereBetween('date', [$startDate, $endDate])
                ->where('status', 'approved')
                ->whereNotIn('user_id', $processedUserIds)
                ->with('user')
                ->get()
                ->unique('user_id');
            
            $msg = 'Payroll Generated Successfully.';
            
            if ($skipped->count() > 0) {
                $names = $skipped->pluck('user.name')->join(', ');
                $msg .= " NOTICE: Overtime records found for employees [ $names ] but they were skipped because they are missing Salary Details in Payroll Master. Please configure them and Regenerate.";
                
                // Use error flash to make it prominent, or success with warning text
                return redirect()->route('admin.finance.payroll.show', $payroll->id)
                    ->with('success', $msg);
            }

            return redirect()->route('admin.finance.payroll.show', $payroll->id)
                ->with('success', $msg);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function finalize(Payroll $payroll)
    {
        $this->payrollService->finalizePayroll($payroll);
        return back()->with('success', 'Payroll Finalized and Expenses Posted');
    }

    public function export(Payroll $payroll)
    {
        $payroll->load(['entries.user.employeeDetail', 'entries.user.designation', 'entries.user.roles']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.accounts.payroll.pdf', compact('payroll'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('Salary-Report-' . $payroll->month . '.pdf');
    }

    public function storeDeduction(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
        ]);

        \App\Models\PayrollDeduction::create([
            'user_id' => $request->user_id,
            'month' => $request->month,
            'amount' => $request->amount,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'Deduction Added. Please Regenerate Payroll to reflect changes.');
    }
}
