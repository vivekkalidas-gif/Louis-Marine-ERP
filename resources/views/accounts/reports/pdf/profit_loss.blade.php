<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Statement</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 12pt;
            color: #2563ea;
        }
        .header p {
            margin: 5px 0;
            font-size: 9pt;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
        th {
            text-align: left;
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-red {
            color: #dc2626;
        }
        .text-green {
            color: #16a34a;
        }
        .section-header {
            background-color: #f3f4f6;
            font-weight: bold;
            padding: 8px;
            margin-top: 15px;
            border-left: 4px solid #333;
        }
        .total-row td {
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 11pt;
        }
        .footer {
            margin-top: 30px;
            font-size: 8pt;
            text-align: center;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Louis Marine Equipment Repairing</h1>
        <h2>Profit & Loss Statement</h2>
        <p>For the period {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <!-- Income Section -->
    <div class="section-header" style="border-left-color: #16a34a;">Income</div>
    <table>
        <thead>
            <tr>
                <th>Account</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $income)
            <tr>
                <td>{{ $income->code }} - {{ $income->name }}</td>
                <td class="text-right">{{ number_format($income->net_balance, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Income</td>
                <td class="text-right text-green">{{ number_format($incomes->sum('net_balance'), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Expense Section -->
    <div class="section-header" style="border-left-color: #dc2626;">Expenses</div>
    <table>
        <thead>
            <tr>
                <th>Account</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->code }} - {{ $expense->name }}</td>
                <td class="text-right">{{ number_format($expense->net_balance, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Expenses</td>
                <td class="text-right text-red">{{ number_format($expenses->sum('net_balance'), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <table style="border: 2px solid #333;">
            <tr style="background-color: #f8f9fa;">
                <td class="font-bold" style="font-size: 12pt;">Net Profit / (Loss)</td>
                <td class="text-right font-bold" style="font-size: 12pt;">
                    @php 
                        $netProfit = $incomes->sum('net_balance') - $expenses->sum('net_balance');
                    @endphp
                    <span class="{{ $netProfit >= 0 ? 'text-green' : 'text-red' }}">
                        {{ number_format($netProfit, 2) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i:s') }} | Internal Document
    </div>
</body>
</html>
