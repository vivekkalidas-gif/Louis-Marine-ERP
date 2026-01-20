<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>General Ledger</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
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
            font-size: 14pt;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 11pt;
            color: #2563ea;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 5px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        th {
            text-align: left;
            font-weight: bold;
            background-color: #f8f9fa;
            border-bottom: 1px solid #333;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }
        .footer {
            margin-top: 30px;
            font-size: 7pt;
            text-align: center;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border-bottom: none;">
            <tr>
                <td width="60%" valign="top" style="border-bottom: none; padding: 0;">
                    <div style="font-size: 18px; font-weight: bold; text-transform: uppercase; color: #1a237e; margin-bottom: 5px;">Louis Marine Equipment</div>
                    <div style="font-size: 9px; line-height: 1.4; color: #555;">
                        P.O. Box 12345, Doha, Qatar<br>
                        Tel: +974 4444 5555 | Email: accounts@louismarine.qa<br>
                        TRN: 1234567890
                    </div>
                </td>
                <td width="40%" valign="top" align="right" style="border-bottom: none; padding: 0;">
                    <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase; color: #1a237e;">General Ledger</div>
                    <div style="font-size: 9pt; color: #555; margin-top: 5px;">Generated on {{ now()->format('d M Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Date</th>
                <th style="width: 50px;">JRNL</th>
                <th>Reference</th>
                <th>Account</th>
                <th>Partner</th>
                <th>Description</th>
                <th class="text-right" style="width: 70px;">Debit</th>
                <th class="text-right" style="width: 70px;">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->date ? $item->date->format('Y-m-d') : '-' }}</td>
                <td>{{ optional($item->entry->journal)->code }}</td>
                <td>{{ $item->entry->number ?? $item->entry->name }}</td>
                <td>
                    <b>{{ $item->account->code }}</b><br>
                    <span style="font-size: 7pt;">{{ \Illuminate\Support\Str::limit($item->account->name, 20) }}</span>
                </td>
                <td>{{ optional($item->partner)->company_name ?? optional($item->partner)->name ?? '-' }}</td>
                <td>{{ $item->label }}</td>
                <td class="text-right font-mono">{{ number_format($item->debit, 2) }}</td>
                <td class="text-right font-mono">{{ number_format($item->credit, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f3f4f6; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding-right: 18px;">Grand Total:</td>
                <td class="text-right font-mono">{{ number_format($items->sum('debit'), 2) }}</td>
                <td class="text-right font-mono">{{ number_format($items->sum('credit'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

     <div class="footer">
        Generated on {{ now()->format('d M Y H:i:s') }} | Internal Document | Page 1
    </div>
</body>
</html>
