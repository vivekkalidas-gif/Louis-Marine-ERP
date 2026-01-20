<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trial Balance</title>
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
            border-bottom: 2px solid #333;
            text-transform: uppercase;
            font-size: 9pt;
        }
        .text-right {
            text-align: right;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }
        .total-row td {
            border-top: 2px solid #333;
            font-weight: bold;
            background-color: #f3f4f6;
        }
        .footer {
            margin-top: 30px;
            font-size: 8pt;
            text-align: center;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Louis Marine Equipment Repairing</h1>
        <h2>Trial Balance</h2>
        <p>As of {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Name</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Net Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $account)
                @if($account->total_debit != 0 || $account->total_credit != 0)
                <tr>
                    <td><b>{{ $account->code }}</b></td>
                    <td>{{ $account->name }}</td>
                    <td class="text-right font-mono">{{ number_format($account->total_debit, 2) }}</td>
                    <td class="text-right font-mono">{{ number_format($account->total_credit, 2) }}</td>
                    <td class="text-right font-mono">
                        {{ number_format($account->net_balance, 2) }}
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">Grand Total:</td>
                <td class="text-right font-mono">{{ number_format($accounts->sum('total_debit'), 2) }}</td>
                <td class="text-right font-mono">{{ number_format($accounts->sum('total_credit'), 2) }}</td>
                <td class="text-right font-mono" style="{{ round($accounts->sum('net_balance'), 2) != 0 ? 'color: red;' : 'color: green;' }}">
                    {{ number_format($accounts->sum('net_balance'), 2) }}
                    @if(round($accounts->sum('net_balance'), 2) != 0)
                        (UNBALANCED)
                    @else
                        (BALANCED)
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i:s') }} | Internal Document
    </div>
</body>
</html>
