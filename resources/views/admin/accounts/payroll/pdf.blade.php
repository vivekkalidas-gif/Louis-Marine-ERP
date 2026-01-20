<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Report - {{ $payroll->month }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { font-size: 18px; font-weight: bold; color: #003366; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; color: #333; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { font-weight: bold; background-color: #eef; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">{{ config('app.name') }}</div>
        <h2>Salary Report for Month: {{ \Carbon\Carbon::parse($payroll->month)->format('F Y') }}</h2>
        <p>Generated on: {{ now()->format('d M, Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Designation</th>
                <th class="text-center">Days</th>
                <th class="text-center">Hours</th>
                <th class="text-right">Basic Salary</th>
                <th class="text-right">Allowances</th>
                <th class="text-right">Overtime</th>
                <th class="text-right">Deductions</th>
                <th class="text-right">Net Salary</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalBasic = 0;
                $totalAllowances = 0;
                $totalOvertime = 0;
                $totalDeductions = 0;
                $totalNet = 0;
            @endphp
            @foreach($payroll->entries as $entry)
            @php
                $totalBasic += $entry->basic_salary;
                $totalAllowances += $entry->total_allowances;
                $totalOvertime += $entry->overtime_amount;
                $totalDeductions += $entry->total_deductions;
                $totalNet += $entry->net_salary;
            @endphp
            <tr>
                <td>{{ $entry->user->name }}</td>
                <td>{{ $entry->user->designation->name ?? '-' }}</td>
                <td class="text-center">{{ $entry->days_worked }}</td>
                <td class="text-center">{{ $entry->total_hours }}</td>
                <td class="text-right">{{ number_format($entry->basic_salary, 2) }}</td>
                <td class="text-right">{{ number_format($entry->total_allowances, 2) }}</td>
                <td class="text-right">{{ number_format($entry->overtime_amount, 2) }}</td>
                <td class="text-right">{{ number_format($entry->total_deductions, 2) }}</td>
                <td class="text-right font-bold">{{ number_format($entry->net_salary, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="4" class="text-right">TOTALS</td>
                <td class="text-right">{{ number_format($totalBasic, 2) }}</td>
                <td class="text-right">{{ number_format($totalAllowances, 2) }}</td>
                <td class="text-right">{{ number_format($totalOvertime, 2) }}</td>
                <td class="text-right">{{ number_format($totalDeductions, 2) }}</td>
                <td class="text-right">{{ number_format($totalNet, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Approved By:</strong> _________________________</p>
    </div>
</body>
</html>
