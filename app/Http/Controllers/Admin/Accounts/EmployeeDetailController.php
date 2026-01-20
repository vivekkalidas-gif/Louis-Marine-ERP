<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDetail;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeDetailController extends Controller
{
    public function index()
    {
        $employees = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['engineer', 'technician', 'storekeeper', 'accounts']);
        })->with(['employeeDetail', 'roles'])->get();
        
        return view('admin.accounts.payroll.employees.index', compact('employees'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'employee_code' => 'required|string|unique:employee_details,employee_code,' . ($user->employeeDetail->id ?? 'NULL'),
            'basic_salary' => 'required|numeric|min:0',
            'hourly_rate' => 'required|numeric|min:0',
            'designation' => 'nullable|string',
        ]);

        $detail = $user->employeeDetail()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'employee_code', 'designation', 'join_date', 
                'basic_salary', 'hourly_rate',
                'allowance_transport', 'allowance_food', 'allowance_other',
                'bank_details'
            ])
        );

        return back()->with('success', 'Employee Payroll Details Updated');
    }
}
