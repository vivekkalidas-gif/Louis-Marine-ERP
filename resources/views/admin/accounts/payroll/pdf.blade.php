<!DOCTYPE html>
<html>
<head>
    <title>Salary Slips - {{ \Carbon\Carbon::parse($payroll->month)->format('M Y') }}</title>
    <style>
        @page { margin: 20px; }
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .page-break { page-break-after: always; }
        
        .slip-container {
            border: 2px solid #333;
            padding: 30px;
            height: 900px; /* Force full page height approx */
            position: relative;
        }

        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 30px; }
        .company-name { font-size: 24px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; color: #1a202c; }
        .payslip-title { font-size: 16px; font-weight: bold; color: #555; }

        .details-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .details-row { display: table-row; }
        .details-cell { display: table-cell; padding: 5px; width: 50%; }

        .earnings-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .earnings-table th, .earnings-table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        .earnings-table th { background: #f3f4f6; font-weight: bold; }
        .text-right { text-align: right; }
        
        .net-pay-box {
            background: #f3f4f6;
            border: 1px solid #ccc;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-bottom: 50px;
        }

        .signatures {
            position: absolute;
            bottom: 50px;
            left: 30px;
            right: 30px;
            width: 100%;
        }
        .sig-block {
            float: left;
            width: 45%;
        }
        .sig-block.right { float: right; text-align: right; }
        .sig-line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
            width: 80%;
            display: inline-block;
        }
    </style>
</head>
<body>
    @foreach($payroll->entries as $entry)
    <div class="slip-container">
        <div class="header">
            <div class="company-name">{{ config('settings.company_name') ?? 'Louis Marine Services' }}</div>
            <div class="payslip-title">PAYSLIP FOR {{ \Carbon\Carbon::parse($payroll->month)->format('F Y') }}</div>
        </div>

        <div class="details-grid">
            <div class="details-row">
                <div class="details-cell">
                    <strong>Employee ID:</strong> {{ $entry->user->employeeDetail->employee_code ?? 'N/A' }}<br>
                    <strong>Name:</strong> {{ $entry->user->name }}
                </div>
                <div class="details-cell text-right">
                    <strong>Designation:</strong> {{ $entry->user->employeeDetail->designation ?? 'Staff' }}<br>
                    <strong>Date:</strong> {{ now()->format('d M Y') }}
                </div>
            </div>
        </div>

        <table class="earnings-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Earnings</th>
                    <th class="text-right">Deductions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">{{ number_format($entry->basic_salary, 2) }}</td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td>Allowances (Transport, Food, Other)</td>
                    <td class="text-right">{{ number_format($entry->allowances_total ?? 0, 2) }}</td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td>Overtime ({{ $entry->overtime_hours ?? 0 }} hrs)</td>
                    <td class="text-right">{{ number_format($entry->overtime_amount, 2) }}</td>
                    <td class="text-right">-</td>
                </tr>
                <tr>
                    <td>Deductions/Advance</td>
                    <td class="text-right">-</td>
                    <td class="text-right" style="color: red;">{{ number_format($entry->deductions_total, 2) }}</td>
                </tr>
                <tr style="font-weight: bold; background: #fafafa;">
                    <td>Total</td>
                    <td class="text-right">{{ number_format(($entry->basic_salary + ($entry->allowances_total ?? 0) + $entry->overtime_amount), 2) }}</td>
                    <td class="text-right text-red-600">{{ number_format($entry->deductions_total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="net-pay-box">
            NET PAYABLE: <span style="font-size: 18px;">{{ number_format($entry->net_salary, 2) }}</span>
        </div>

        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line"></div><br>
                Employer Signature
            </div>
            <div class="sig-block right">
                <div class="sig-line"></div><br>
                Employee Signature
            </div>
        </div>
    </div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
    @endforeach
</body>
</html>
