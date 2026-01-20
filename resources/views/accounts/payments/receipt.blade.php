<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $payment->payment_number }}</title>
    <style>
        @page { margin: 10mm; size: A4; }
        body { 
            font-family: 'Inter', sans-serif; 
            font-size: 10px; 
            color: #333; 
            margin: 0;
            padding: 0;
        }
        
        table { width: 100%; border-collapse: collapse; }
        
        .header-table { border-bottom: 3px solid #b71c1c; margin-bottom: 20px; }
        .header-table td { padding-bottom: 10px; }
        
        .company-name { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #1a237e; margin-bottom: 5px; }
        .company-details { font-size: 9px; line-height: 1.4; color: #555; }
        
        .doc-title { font-size: 24px; font-weight: 900; color: #b71c1c; text-align: right; text-transform: uppercase; margin-bottom: 10px; }
        
        .info-table { margin-bottom: 20px; }
        .info-table td { vertical-align: top; padding: 0; }
        
        .box-container {
            border: 1px solid #1a237e;
            border-top: 4px solid #1a237e;
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            min-height: 100px;
        }
        
        .box-title { font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; color: #1a237e; border-bottom: 1px solid #ddd; padding-bottom: 3px; }
        .box-content { font-size: 10px; line-height: 1.4; }
        
        .items-table { margin-bottom: 20px; }
        .items-table th { background-color: #1a237e; color: #fff; text-transform: uppercase; font-size: 9px; padding: 8px; border: 1px solid #1a237e; text-align: left; } 
        .items-table td { border: 1px solid #ddd; padding: 8px; font-size: 10px; }
        
        .totals-section { width: 100%; }
        .totals-table { width: 40%; margin-left: auto; border-collapse: collapse; }
        .totals-table td { padding: 5px 10px; border-bottom: 1px solid #eee; }
        .totals-table .total-row td { background-color: #1a237e; color: white; font-weight: bold; font-size: 11px; border-top: none; }
        
        .notes-section { margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
        .terms { font-size: 8px; color: #777; margin-top: 10px; text-align: justify; }
        
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            width: 100%; 
            border-top: 1px solid #b71c1c; 
            padding-top: 10px;
            background: white; 
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .w-half { width: 48%; }
        .spacer { width: 4%; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td width="60%" valign="top">
                @if(isset($settings['company_logo']) && $settings['company_logo'])
                     @php
                        $logoPath = $settings['company_logo'];
                        if (\Illuminate\Support\Str::startsWith($logoPath, 'storage/')) {
                            $logoPath = substr($logoPath, 8);
                        }
                        $fullLogoPath = storage_path('app/public/' . $logoPath);
                    @endphp
                    @if(file_exists($fullLogoPath))
                        <img src="{{ $fullLogoPath }}" style="height: 50px; margin-bottom: 10px;">
                    @else
                         <div class="company-name">{{ $settings['company_name'] ?? 'Louis Marine Equipment' }}</div>
                    @endif
                @else
                    <div class="company-name">{{ $settings['company_name'] ?? 'Louis Marine Equipment' }}</div>
                @endif
                <div class="company-details">
                    {!! nl2br($settings['company_address'] ?? 'P.O. Box 12345, Doha, Qatar') !!}<br>
                    Tel: {{ $settings['company_phone'] ?? '+974 4444 5555' }} | Email: {{ $settings['company_email'] ?? 'accounts@louismarine.qa' }}<br>
                    TRN: {{ $settings['company_trn'] ?? '1234567890' }}
                </div>
            </td>
            <td width="40%" valign="top" class="text-right">
                <div class="doc-title">PAYMENT RECEIPT</div>
                <table align="right" style="width: auto;">
                    <tr>
                        <td class="text-right" style="padding-right: 10px; border: none !important;"><strong>Receipt No:</strong></td>
                        <td class="text-right" style="border: none !important;">{{ $payment->payment_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="padding-right: 10px; border: none !important;"><strong>Date:</strong></td>
                        <td class="text-right" style="border: none !important;">{{ $payment->payment_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="padding-right: 10px; border: none !important;"><strong>Method:</strong></td>
                        <td class="text-right" style="border: none !important;">{{ ucfirst($payment->payment_method) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Info Sections -->
    <table class="info-table">
        <tr>
            <td class="w-half">
                <div class="box-container">
                    <div class="box-title">Received From</div>
                    <div class="box-content">
                        <strong>{{ $payment->invoice->customer->company_name }}</strong><br>
                        {{ $payment->invoice->customer->address }}<br>
                        Phone: {{ $payment->invoice->customer->phone }}<br>
                        Email: {{ $payment->invoice->customer->email }}
                    </div>
                </div>
            </td>
            <td class="spacer"></td>
            <td class="w-half">
                <div class="box-container">
                    <div class="box-title">Payment Reference</div>
                    <div class="box-content">
                        @if($payment->reference_number)
                        <strong>Reference No:</strong> {{ $payment->reference_number }}<br>
                        @endif
                        <strong>Invoice No:</strong> {{ $payment->invoice->invoice_number }}<br>
                        <strong>Invoice Date:</strong> {{ $payment->invoice->date->format('d/m/Y') }}<br>
                        <strong>Invoice Total:</strong> {{ number_format($payment->invoice->total_amount, 2) }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="10%">#</th>
                <th width="50%">Description</th>
                <th width="20%">Invoice Ref</th>
                <th width="20%" class="text-right">Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>{{ $payment->notes ?? 'Payment Received for Invoice' }}</td>
                <td>{{ $payment->invoice->invoice_number }}</td>
                <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <table class="totals-table">
            <tr class="total-row">
                <td>Total Received (QAR)</td>
                <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Notes & Footer -->
    <div class="notes-section">
        <table style="width: 100%; border: none;">
            <tr>
                <td width="60%" valign="top" style="border: none;">
                    <div class="terms" style="font-size: 10px; color: #555;">
                        <strong>Thank you for your payment!</strong><br>
                        This receipt is an acknowledgment of the payment received against the specified invoice.<br>
                        If you have any questions, please contact our accounts department.
                    </div>
                </td>
                <td width="40%" valign="bottom" align="right" style="border: none;">
                    <div style="text-align: center; width: 200px; float: right;">
                        <!-- Signature Logic (Same as Invoice) -->
                         @php
                            // Try to get User from created_by - Payment model has createdBy relation via trait or custom?
                            // Payment model in our previous view didn't show the relation, but debug output showed created_by field.
                            // Let's assume the user relation exists or we can't show signature.
                            // The Payment.php we viewed has `created_by` in fillable but didn't explicitly show `createdBy` relation method, 
                            // BUT it uses `\App\Traits\LogsActivity` which might not include it. 
                            // However, let's try to access it via invoice->createdBy if payment->createdBy isn't available, 
                            // OR just leave blank line if null.
                            $signer = \App\Models\User::find($payment->created_by);
                        @endphp
                        
                        @if($signer && $signer->signature_path && file_exists(storage_path('app/public/' . $signer->signature_path)))
                            <img src="{{ storage_path('app/public/' . $signer->signature_path) }}" style="height: 60px; max-width: 150px; margin-bottom: 5px;">
                        @else
                            <div style="height: 60px;"></div>
                        @endif
                        
                        <div style="border-top: 1px solid #333; padding-top: 5px; font-weight: bold; font-size: 10px;">
                            Authorized Signature
                        </div>
                         @if($signer)
                        <div style="font-size: 9px; margin-top: 4px;">
                            {{ $signer->name }}
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <script type="text/php">
        if ( isset($pdf) ) {
            $font = $fontMetrics->get_font("Inter, sans-serif", "normal");
            $pdf->page_text(30, 800, "Generated on {{ now()->format('d M Y, H:i A') }} | Louis Marine ERP", $font, 6, array(0.6,0.6,0.6));
            $pdf->page_text(520, 800, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0.6,0.6,0.6));
        }
    </script>
</body>
</html>
