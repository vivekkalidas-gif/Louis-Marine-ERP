<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <style>
        body { font-family: sans-serif; padding: 20px; text-align: center; }
        .receipt { max-width: 400px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
        .header { margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .details { text-align: left; margin-bottom: 30px; }
        .amount { font-size: 24px; font-weight: bold; margin: 20px 0; }
        .signature { margin-top: 50px; text-align: left; }
        .line { border-bottom: 1px solid #000; display: inline-block; width: 200px; margin-top: 50px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <h2>{{ config('app.name') }}</h2>
            <p>Payment Voucher / Receipt</p>
        </div>
        
        <div class="details">
            <p><strong>Date:</strong> {{ $entry->date->format('d M Y') }}</p>
            <p><strong>Staff:</strong> {{ $entry->user->name }}</p>
            <p><strong>Transaction:</strong> {{ ucwords(str_replace('_', ' ', $entry->type)) }}</p>
            <p><strong>Description:</strong> {{ $entry->description }}</p>
        </div>

        <div class="amount">
            {{ number_format($entry->amount, 2) }}
        </div>

        <div class="signature">
            <p>Received / Paid by:</p>
            <div class="line"></div>
            <p>Signature</p>
        </div>

        <button class="no-print" onclick="window.print()" style="margin-top:20px; padding:10px 20px; cursor:pointer;">Print Receipt</button>
    </div>
</body>
</html>