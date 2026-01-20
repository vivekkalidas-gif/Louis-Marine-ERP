<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
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
        
        /* Alternating row colors handled differently for print */
        /* .items-table tr:nth-child(even) { background-color: #f0f4f8; } */
        
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
        
        /* Utility for spacing */
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
                <div class="doc-title">TAX INVOICE</div>
                <table align="right" style="width: auto;">
                    <tr>
                        <td class="text-right" style="padding-right: 10px; border: none !important;"><strong>Invoice No:</strong></td>
                        <td class="text-right" style="border: none !important;">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="padding-right: 10px; border: none !important;"><strong>Date:</strong></td>
                        <td class="text-right" style="border: none !important;">{{ $invoice->date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="padding-right: 10px; border: none !important;"><strong>Due Date:</strong></td>
                        <td class="text-right" style="border: none !important;">{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Info Sections (Table Based) -->
    <table class="info-table">
        <tr>
            <td class="w-half">
                <div class="box-container">
                    <div class="box-title">Bill To</div>
                    <div class="box-content">
                        <strong>{{ $invoice->customer->company_name }}</strong><br>
                        @php
                            $primaryContact = $invoice->customer->contacts->where('is_primary', true)->first() ?? $invoice->customer->contacts->first();
                        @endphp
                        Attn: {{ $primaryContact ? $primaryContact->name : 'Manager' }}<br>
                        {{ $invoice->customer->address }}<br>
                        Phone: {{ $invoice->customer->phone }}<br>
                        Email: {{ $invoice->customer->email }}
                    </div>
                </div>
            </td>
            <td class="spacer"></td>
            <td class="w-half">
                <div class="box-container">
                    <div class="box-title">Job / Vessel Details</div>
                    <div class="box-content">
                        @if($invoice->vessel)
                        <strong>Vessel:</strong> {{ $invoice->vessel->name }}<br>
                        @endif
                        @if($invoice->serviceJob)
                        <strong>Job ID:</strong> JOB-{{ $invoice->serviceJob->job_number }}<br>
                        @endif
                        <strong>Terms:</strong> {{ $invoice->due_date ? 'Net ' . $invoice->date->diffInDays($invoice->due_date) : 'Immediate' }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Description</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Unit Price</th>
                <th width="20%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr style="background-color: {{ $index % 2 == 1 ? '#f9f9f9' : '#fff' }};">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->quantity + 0 }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount > 0)
            <tr>
                <td>Discount</td>
                <td class="text-right">-{{ number_format($invoice->discount, 2) }}</td>
            </tr>
            @endif
            @if($invoice->tax_amount > 0)
            <tr>
                <td>VAT / Tax</td>
                <td class="text-right">{{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Grand Total (QAR)</td>
                <td class="text-right">{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Notes & Footer -->
    <div class="notes-section">
        @if($invoice->notes)
        <div style="margin-bottom: 20px;">
            <strong>Notes:</strong><br>
            {{ $invoice->notes }}
        </div>
        @endif

        <table style="width: 100%; border: none;">
            <tr>
                <td width="60%" valign="top" style="border: none;">
                    <div class="terms">
                        <strong>Bank Details:</strong><br>
                        Account Name: {{ $settings['bank_account_name'] ?? 'Louis Marine Equipment' }}<br>
                        Bank: {{ $settings['bank_name'] ?? 'Qatar National Bank (QNB)' }}<br>
                        IBAN: {{ $settings['bank_iban'] ?? 'QA00 QNBA 0000 0000 1234 5678' }}<br>
                        Swift: {{ $settings['bank_swift'] ?? 'QNBAQAQA' }}
                    </div>
                </td>
                <td width="40%" valign="bottom" align="right" style="border: none;">
                    <div style="text-align: center; width: 200px; float: right;">
                        @if($invoice->createdBy && $invoice->createdBy->signature_path && file_exists(storage_path('app/public/' . $invoice->createdBy->signature_path)))
                            <img src="{{ storage_path('app/public/' . $invoice->createdBy->signature_path) }}" style="height: 60px; max-width: 150px; margin-bottom: 5px;">
                        @else
                            <div style="height: 60px;"></div> <!-- Spacer for manual sign -->
                        @endif
                        <div style="border-top: 1px solid #333; padding-top: 5px; font-weight: bold; font-size: 10px;">
                            Authorized Signature
                        </div>
                        @if($invoice->createdBy)
                        <div style="font-size: 9px; margin-top: 4px;">
                            {{ $invoice->createdBy->name }}
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
