<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet</title>
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
        .container {
            width: 100%;
        }
        .section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .section-header {
            background-color: #f3f4f6;
            padding: 8px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #333;
            font-weight: bold;
            background-color: #f9fafb;
        }
        .grand-total {
            margin-top: 10px;
            background-color: #333;
            color: white;
            padding: 10px;
            font-weight: bold;
            display: flex; /* Not supported in dompdf effectively, use table */
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
        <h2>Balance Sheet</h2>
        <p>As of {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
    </div>

    <!-- Assets -->
    <div class="section">
        <div class="section-header" style="color: #16a34a;">Assets</div>
        <table>
            <tbody>
                @foreach($data['Assets'] as $asset)
                <tr>
                    <td>{{ $asset->code }} - {{ $asset->name }}</td>
                    <td class="text-right">{{ number_format($asset->net_balance, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total Assets</td>
                    <td class="text-right">{{ number_format($data['Assets']->sum('net_balance'), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Liabilities -->
    <div class="section">
        <div class="section-header" style="color: #dc2626;">Liabilities</div>
        <table>
            <tbody>
                @foreach($data['Liabilities'] as $liab)
                <tr>
                    <td>{{ $liab->code }} - {{ $liab->name }}</td>
                    <td class="text-right">{{ number_format($liab->net_balance * -1, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total Liabilities</td>
                    <td class="text-right">{{ number_format($data['Liabilities']->sum('net_balance') * -1, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Equity -->
    <div class="section">
        <div class="section-header" style="color: #2563ea;">Equity</div>
        <table>
            <tbody>
                @foreach($data['Equity'] as $equity)
                <tr>
                    <td>{{ $equity->code }} - {{ $equity->name }}</td>
                    <td class="text-right">{{ number_format($equity->net_balance * -1, 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td><i>Current Year Earnings (Calculated)</i></td>
                    <td class="text-right"><i>{{ number_format($data['Equity_Adjustment'], 2) }}</i></td>
                </tr>
                <tr class="total-row">
                    <td>Total Equity</td>
                    <td class="text-right">{{ number_format(($data['Equity']->sum('net_balance') * -1) + $data['Equity_Adjustment'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Grand Totals Check -->
    <table style="width: 100%; border: 2px solid #000; margin-top: 20px;">
        <tr style="background-color: #333; color: white;">
            <td style="padding: 10px;">Total Liabilities & Equity</td>
            <td class="text-right" style="padding: 10px;">
                {{ number_format(
                    ($data['Liabilities']->sum('net_balance') * -1) + 
                    ($data['Equity']->sum('net_balance') * -1) + 
                    $data['Equity_Adjustment'], 2) 
                }}
            </td>
        </tr>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i:s') }} | Internal Document
    </div>
</body>
</html>
