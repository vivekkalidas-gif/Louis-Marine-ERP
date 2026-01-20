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
            return redirect()->route('admin.finance.payroll.show', $payroll->id)
                ->with('success', 'Payroll Generated Successfully');
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
}
