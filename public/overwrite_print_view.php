<?php

$file = __DIR__ . '/../resources/views/admin/staff/ledger_print.blade.php';

if (file_exists($file)) {
    @unlink($file);
}

$content = <<<'EOD'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Receipt</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }
        .logo-container { flex: 1; }
        .logo { max-width: 180px; max-height: 80px; }
        .company-info { text-align: right; font-size: 14px; color: #666; }
        .company-name { font-size: 20px; font-weight: bold; color: #000; margin-bottom: 5px; }
        .receipt-title {
            text-align: center; text-transform: uppercase; letter-spacing: 2px;
            font-size: 24px; font-weight: bold; color: #444;
            margin-bottom: 40px; padding: 10px; background-color: #f9f9f9; border: 1px solid #eee;
        }
        .content { margin-bottom: 50px; }
        .row { display: flex; margin-bottom: 15px; border-bottom: 1px solid #f5f5f5; padding-bottom: 5px; }
        .label { width: 150px; font-weight: bold; color: #555; }
        .value { flex: 1; font-weight: 500; }
        .amount-box {
            margin-top: 30px; text-align: right; font-size: 28px; font-weight: bold; color: #000;
            border-top: 2px solid #333; padding-top: 10px; display: inline-block; float: right;
        }
        .footer { margin-top: 80px; display: flex; justify-content: space-between; }
        .signature-box { width: 200px; text-align: center; }
        .line { border-top: 1px solid #000; margin-bottom: 5px; }
        .print-btn {
            display: block; margin: 20px auto; padding: 10px 20px; background: #333; color: #fff;
            border: none; cursor: pointer;
        }
        @media print { .print-btn { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">Print Receipt</button>

    <div class="header">
        <div class="logo-container">
            @php
                $logo = \App\Models\Setting::get('company_logo');
                $logoUrl = $logo ? asset('storage/' . $logo) : 'https://via.placeholder.com/150x50?text=Company+Logo';
            @endphp
            <img src="{{ $logoUrl }}" class="logo" alt="Logo">
        </div>
        <div class="company-info">
            <div class="company-name">{{ \App\Models\Setting::get('company_name') ?? 'Louis Marine' }}</div>
            {{ \App\Models\Setting::get('company_address') }}<br>
            Phone: {{ \App\Models\Setting::get('company_phone') }}<br>
            Email: {{ \App\Models\Setting::get('company_email') }}
        </div>
    </div>

    <div class="receipt-title">PAYMENT VOUCHER</div>

    <div class="content">
        <div class="row"><div class="label">Receipt No:</div><div class="value">VR-{{ substr($entry->id, 0, 8) }}</div></div>
        <div class="row"><div class="label">Date:</div><div class="value">{{ $entry->date->format('d F, Y') }}</div></div>
        <div class="row"><div class="label">Staff Name:</div><div class="value">{{ $user->name }} (ID: {{ $user->employeeDetail->employee_code ?? 'N/A' }})</div></div>
        <div class="row"><div class="label">Transaction Type:</div><div class="value">{{ ucwords(str_replace('_', ' ', $entry->type)) }}</div></div>
        <div class="row"><div class="label">Description:</div><div class="value">{{ $entry->description }}</div></div>
        
        <div style="clear: both;"></div>
        
        <div class="amount-box">{{ number_format($entry->amount, 2) }}</div>
        <div style="clear: both;"></div>
        
        <div style="margin-top: 10px; text-align: right; font-style: italic; color: #666;">
            Amount in words: 
            @if(class_exists('NumberFormatter'))
                {{ ucwords(\NumberFormatter::create('en', \NumberFormatter::SPELLOUT)->format($entry->amount)) }} Only
            @else
                {{ $entry->amount }}
            @endif
        </div>
    </div>

    <div class="footer">
        <div class="signature-box"><div class="line"></div>Receiver's Signature</div>
        <div class="signature-box"><div class="line"></div>Authorized Signature</div>
    </div>
</body>
</html>
EOD;

file_put_contents($file, $content);
echo "Print View overwritten successfully.";
