<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>General Ledger - {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body { font-family: sans-serif; padding: 40px; color: #333; font-size: 14px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .logo { font-size: 24px; font-weight: bold; color: #1a237e; text-transform: uppercase; }
        .company-info { font-size: 12px; line-height: 1.4; color: #555; margin-top: 5px; }
        .title-box { text-align: right; }
        .title { font-size: 28px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; color: #1a237e; }
        .report-meta { font-size: 12px; color: #555; }
        
        .details { margin-bottom: 30px; display: flex; gap: 20px; }
        .box { background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; flex: 1; }
        .box h3 { margin: 0 0 5px 0; font-size: 12px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em; }
        .box p { margin: 0; font-weight: bold; font-size: 14px; }

        .account-section { margin-bottom: 40px; page-break-inside: avoid; }
        .account-header { margin-bottom: 15px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: baseline; }
        .account-title { font-size: 18px; font-weight: bold; color: #111; }
        .account-code { color: #6b7280; font-size: 14px; margin-right: 10px; }
        .balance-summary { font-size: 14px; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 12px; }
        th { text-align: left; padding: 10px; border-bottom: 2px solid #e5e7eb; font-size: 11px; text-transform: uppercase; color: #6b7280; background: #f9fafb; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; }
        tr:last-child td { border-bottom: none; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .font-bold { font-weight: bold; }
        .text-red { color: #dc2626; }
        .text-green { color: #16a34a; }
        .bg-gray { background-color: #f9fafb; }

        .footer { margin-top: 50px; border-top: 1px solid #e5e7eb; padding-top: 20px; text-align: center; font-size: 10px; color: #9ca3af; }
        
        @media print {
            @page {
                size: auto;
                margin: 0mm;
            }
            body { padding: 40px; }
            .no-print { display: none; }
            .box { break-inside: avoid; }
            table { break-inside: auto; }
            tr { break-inside: avoid; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div>
            <div class="logo">
                @if($company['logo'])
                    <img src="{{ asset($company['logo']) }}" alt="{{ $company['name'] }}" style="height: 60px; max-width: 200px;">
                @else
                    {{ $company['name'] }}
                @endif
            </div>
            <div class="company-info">
                {!! nl2br(e($company['address'])) !!}<br>
                @if($company['phone']) Tel: {{ $company['phone'] }} @endif
                @if($company['email']) | Email: {{ $company['email'] }} @endif <br>
                @if($company['trn']) TRN: {{ $company['trn'] }} @endif
            </div>
        </div>
        <div class="title-box">
            <div class="title">General Ledger</div>
            <div class="report-meta">Generated on {{ now()->format('d M Y') }}</div>
        </div>
    </div>

    <div class="details">
        <div class="box">
            <h3>Reporting Period</h3>
            <p>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="box">
            <h3>Filters</h3>
            <p>
                Account: {{ $accountName ?? 'All Accounts' }}<br>
                Partner: {{ $partnerName ?? 'All Partners' }}
            </p>
        </div>
    </div>

    @forelse($ledgerData as $data)
        <div class="account-section">
            <div class="account-header">
                <div>
                    <span class="account-code">{{ $data['account']->code }}</span>
                    <span class="account-title">{{ $data['account']->name }}</span>
                </div>
                <div class="balance-summary">
                    Closing Balance: <span class="font-mono">{{ number_format($data['closing_balance'], 2) }}</span>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="12%">Date</th>
                        <th width="15%">Ref / JRNL</th>
                        <th width="15%">Partner</th>
                        <th>Description / Label</th>
                        <th width="12%" class="text-right">Debit</th>
                        <th width="12%" class="text-right">Credit</th>
                        <th width="12%" class="text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Opening Balance -->
                    <tr class="bg-gray font-bold">
                        <td colspan="4" class="text-right">Opening Balance</td>
                        <td class="text-right font-mono">{{ $data['opening_balance'] > 0 ? number_format($data['opening_balance'], 2) : '-' }}</td>
                        <td class="text-right font-mono">{{ $data['opening_balance'] < 0 ? number_format(abs($data['opening_balance']), 2) : '-' }}</td>
                        <td class="text-right font-mono">{{ number_format($data['opening_balance'], 2) }}</td>
                    </tr>

                    @foreach($data['lines'] as $line)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($line->date)->format('d-M-Y') }}</td>
                            <td>
                                <div>{{ $line->entry->number ?? '-' }}</div>
                                <div style="font-size: 10px; color: #666;">{{ optional($line->entry->journal)->code }}</div>
                            </td>
                            <td>{{ optional($line->partner)->name ?? '-' }}</td>
                            <td>{{ $line->label ?? $line->entry->reference }}</td>
                            <td class="text-right font-mono">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                            <td class="text-right font-mono">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                            <td class="text-right font-mono">{{ number_format($line->running_balance, 2) }}</td>
                        </tr>
                    @endforeach

                    <!-- Closing Balance -->
                    <tr class="bg-gray font-bold" style="border-top: 2px solid #e5e7eb;">
                        <td colspan="4" class="text-right">Closing Balance</td>
                        <td class="text-right font-mono"></td> <!-- Optional: Sum Debits -->
                        <td class="text-right font-mono"></td> <!-- Optional: Sum Credits -->
                        <td class="text-right font-mono">{{ number_format($data['closing_balance'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <div style="text-align: center; padding: 40px; color: #666;">
            No records found for the selected criteria.
        </div>
    @endforelse

    <div class="footer">
        Internal Document | Generated by Louis Marine ERP
    </div>

    <div class="no-print" style="margin-top: 40px; text-align: center; position: fixed; bottom: 20px; left: 0; right: 0; background: white; padding: 20px; border-top: 1px solid #eee; box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);">
        <button onclick="window.print()" style="padding: 10px 25px; background: #1a237e; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; margin-right: 10px;">Print Report</button>
        <button onclick="window.close()" style="padding: 10px 25px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">Close</button>
    </div>

</body>
</html>
