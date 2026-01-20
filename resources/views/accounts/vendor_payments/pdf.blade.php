<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payment {{ $vendorPayment->payment_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; line-height: 1.5; color: #333; margin: 0; padding: 0; }
        .header-table { width: 100%; border-bottom: 2px solid #eee; margin-bottom: 30px; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #111; }
        .company-info { font-size: 12px; color: #666; margin-top: 5px; }
        .title-cell { text-align: right; vertical-align: top; }
        .title { font-size: 24px; font-weight: bold; color: #444; text-transform: uppercase; margin: 0; }
        .meta { color: #666; font-size: 13px; }
        
        .box { background-color: #f9f9f9; padding: 20px; border: 1px solid #eee; margin-bottom: 30px; border-radius: 5px; }
        .info-table { width: 100%; }
        .info-table td { padding: 5px 0; vertical-align: top; }
        .label { font-weight: bold; color: #555; width: 140px; }
        .value { color: #000; }
        
        .section-title { font-size: 15px; font-weight: bold; margin-bottom: 10px; color: #111; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data-table th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; text-transform: uppercase; }
        table.data-table td { padding: 10px; border-bottom: 1px solid #eee; font-size: 12px; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        
        .signatures { margin-top: 60px; width: 100%; }
        .sig-box { width: 30%; float: left; margin-right: 3%; text-align: center; }
        .sig-line { border-top: 1px solid #ccc; padding-top: 8px; font-size: 12px; color: #666; }
        
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="logo">
                     @if($company['logo'])
                        <img src="{{ public_path($company['logo']) }}" alt="{{ $company['name'] }}" style="height: 50px; display: block; margin-bottom: 5px;">
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
            </td>
            <td class="title-cell">
                <div class="title">Payment Voucher</div>
                <div class="meta" style="margin-top: 10px;">Voucher #: <strong>{{ $vendorPayment->payment_number }}</strong></div>
                <div class="meta">Date: {{ $vendorPayment->payment_date->format('d M, Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="box">
        <table class="info-table">
            <tr>
                <td class="label">Paid To:</td>
                <td class="value">
                    <strong>{{ $vendorPayment->vendor->name }}</strong><br>
                    {!! nl2br(e($vendorPayment->vendor->address ?? '')) !!}
                </td>
            </tr>
            <tr>
                <td class="label">Paid From:</td>
                <td class="value">{{ $vendorPayment->bankAccount->name}}</td>
            </tr>
            <tr>
                <td class="label">Payment Method:</td>
                <td class="value" style="text-transform: capitalize;">{{ $vendorPayment->payment_method }}</td>
            </tr>
            <tr>
                <td class="label">Reference:</td>
                <td class="value">{{ $vendorPayment->reference ?? '-' }}</td>
            </tr>
             <tr>
                <td class="label" style="padding-top: 15px;">Amount Paid:</td>
                <td class="value" style="padding-top: 15px; font-size: 18px; font-weight: bold; color: #111;">
                    {{ number_format($vendorPayment->amount, 2) }} QAR
                </td>
            </tr>
        </table>
         @if($vendorPayment->notes)
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ddd;">
                <span class="label">Notes:</span> <span style="font-style: italic; color: #666;">{{ $vendorPayment->notes }}</span>
            </div>
        @endif
    </div>

    @if($vendorPayment->bills->count() > 0)
        <div class="section-title">Bill Allocation Details</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Bill Number</th>
                    <th>Bill Date</th>
                    <th class="text-right">Total Bill</th>
                    <th class="text-right">Amount Applied</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendorPayment->bills as $bill)
                    <tr>
                        <td>{{ $bill->bill_number }}</td>
                        <td>{{ $bill->bill_date->format('d M, Y') }}</td>
                         <td class="text-right">{{ number_format($bill->total_amount, 2) }}</td>
                        <td class="text-right font-mono font-bold">{{ number_format($bill->pivot->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="signatures clearfix">
        <div class="sig-box">
            <div style="height: 40px;"></div>
            <div class="sig-line">Prepared By</div>
        </div>
        <div class="sig-box">
             <div style="height: 40px;"></div>
            <div class="sig-line">Authorized Signature</div>
        </div>
        <div class="sig-box">
             <div style="height: 40px;"></div>
            <div class="sig-line">Received By</div>
        </div>
    </div>
</body>
</html>
