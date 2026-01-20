<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Voucher - {{ $vendorPayment->payment_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; font-size: 14px; margin: 0; padding: 40px; background: #fff; }
        .container { max-width: 800px; mx-auto; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #eee; }
        .logo { font-size: 24px; font-weight: bold; color: #1a1a1a; margin-bottom: 5px; }
        .company-info { font-size: 12px; color: #666; line-height: 1.4; }
        .title-box { text-align: right; }
        .title { font-size: 28px; font-weight: bold; color: #1a1a1a; text-transform: uppercase; letter-spacing: 1px; }
        .meta { font-size: 14px; color: #666; margin-top: 5px; }
        
        .box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .row:last-child { margin-bottom: 0; }
        .label { font-weight: bold; color: #4b5563; width: 150px; }
        .value { color: #1f2937; flex: 1; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f3f4f6; padding: 12px; text-align: left; font-weight: bold; color: #374151; border-bottom: 2px solid #e5e7eb; font-size: 12px; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #eee; color: #4b5563; vertical-align: top; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        
        .footer { margin-top: 60px; padding-top: 20px; border-top: 1px solid #eee; display: flex; justify-content: space-between; }
        .signature-box { width: 200px; text-align: center; }
        .signature-line { border-top: 1px solid #ccc; margin-top: 40px; padding-top: 5px; font-size: 12px; color: #666; }
        
        @media print {
            @page { size: auto; margin: 0; }
            body { padding: 40px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
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
                <div class="title">Payment Voucher</div>
                <div class="meta"># {{ $vendorPayment->payment_number }}</div>
                <div class="meta">Date: {{ $vendorPayment->payment_date->format('d M, Y') }}</div>
            </div>
        </div>

        <div class="box">
            <div class="row">
                <div class="label">Paid To:</div>
                <div class="value">
                    <strong>{{ $vendorPayment->vendor->name }}</strong><br>
                    {{ $vendorPayment->vendor->address ?? 'No Address' }}
                </div>
            </div>
             <div class="row" style="margin-top: 15px;">
                <div class="label">Paid From:</div>
                 <div class="value">{{ $vendorPayment->bankAccount->name}}</div>
            </div>
            <div class="row">
                <div class="label">Payment Method:</div>
                <div class="value" style="text-transform: capitalize;">{{ $vendorPayment->payment_method }}</div>
            </div>
            <div class="row">
                <div class="label">Reference:</div>
                <div class="value">{{ $vendorPayment->reference ?? '-' }}</div>
            </div>
             <div class="row" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                <div class="label">Amount Paid:</div>
                <div class="value" style="font-size: 18px; font-weight: bold;">
                    {{ number_format($vendorPayment->amount, 2) }} QAR
                </div>
            </div>
        </div>

        <h3>Bill Allocation</h3>
        <table>
            <thead>
                <tr>
                    <th>Bill Number</th>
                    <th>Bill Date</th>
                    <th class="text-right">Allocated Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendorPayment->bills as $bill)
                    <tr>
                        <td>{{ $bill->bill_number }}</td>
                        <td>{{ $bill->bill_date->format('d M, Y') }}</td>
                        <td class="text-right font-mono">{{ number_format($bill->pivot->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999;">No bills were explicitly allocated to this payment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($vendorPayment->notes)
            <div style="margin-top: 20px;">
                <div class="label">Notes:</div>
                <div style="background: #fff; border: 1px solid #eee; padding: 10px; border-radius: 4px; color: #555; font-style: italic;">
                    {{ $vendorPayment->notes }}
                </div>
            </div>
        @endif

        <div class="footer">
            <div class="signature-box">
                <div class="signature-line">Prepared By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Authorized Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Receiver's Signature</div>
            </div>
        </div>
    </div>
</body>
</html>
