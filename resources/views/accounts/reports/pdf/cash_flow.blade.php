<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Flow Statement</title>
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
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        th {
            text-align: left;
            font-weight: bold;
            background-color: #f8f9fa;
            border-bottom: 2px solid #333;
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
        .group-header {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #333;
            font-weight: bold;
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
        <h2>Statement of Cash Flows</h2>
        <p>For the period {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Activities</th>
                <th class="text-right">Inflow (+)</th>
                <th class="text-right">Outflow (-)</th>
                <th class="text-right">Net Cash</th>
            </tr>
        </thead>
        <tbody>
            <!-- Operating -->
            <tr>
                <td>Cash Flows from Operating Activities</td>
                <td class="text-right">{{ number_format($activities['operating']['in'], 2) }}</td>
                <td class="text-right text-red">
                    @if($activities['operating']['out'] > 0)
                        ({{ number_format($activities['operating']['out'], 2) }})
                    @else
                        0.00
                    @endif
                </td>
                <td class="text-right font-bold {{ $summary['operating_net'] < 0 ? 'text-red' : 'text-green' }}">{{ number_format($summary['operating_net'], 2) }}</td>
            </tr>

            <!-- Investing -->
            <tr>
                <td>Cash Flows from Investing Activities</td>
                <td class="text-right">{{ number_format($activities['investing']['in'], 2) }}</td>
                <td class="text-right text-red">
                        @if($activities['investing']['out'] > 0)
                        ({{ number_format($activities['investing']['out'], 2) }})
                    @else
                        0.00
                    @endif
                </td>
                <td class="text-right font-bold {{ $summary['investing_net'] < 0 ? 'text-red' : 'text-green' }}">{{ number_format($summary['investing_net'], 2) }}</td>
            </tr>

            <!-- Financing -->
            <tr>
                <td>Cash Flows from Financing Activities</td>
                <td class="text-right">{{ number_format($activities['financing']['in'], 2) }}</td>
                <td class="text-right text-red">
                        @if($activities['financing']['out'] > 0)
                        ({{ number_format($activities['financing']['out'], 2) }})
                    @else
                        0.00
                    @endif
                </td>
                <td class="text-right font-bold {{ $summary['financing_net'] < 0 ? 'text-red' : 'text-green' }}">{{ number_format($summary['financing_net'], 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Net Increase (Decrease) in Cash</td>
                <td colspan="2"></td>
                <td class="text-right font-bold">{{ number_format($summary['net_change'], 2) }}</td>
            </tr>
            <tr>
                <td>Cash at Beginning of Period</td>
                <td colspan="2"></td>
                <td class="text-right">{{ number_format($openingBalance, 2) }}</td>
            </tr>
            <tr class="total-row" style="background-color: #ecfdf5;">
                <td>Cash at End of Period</td>
                <td colspan="2"></td>
                <td class="text-right font-bold text-green">{{ number_format($closingBalance, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i:s') }} | Internal Document
    </div>
</body>
</html>
