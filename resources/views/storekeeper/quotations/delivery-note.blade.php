<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Note {{ $quotation->quotation_number }}</title>
    <style>
        @page { size: A4; margin: 0; }
        body { position: relative; font-family: 'Inter', sans-serif; font-size: 10px; color: #333; margin: 20px auto; padding: 0; width: 210mm; min-height: 297mm; box-sizing: border-box; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .container { width: 100%; padding: 15mm; padding-bottom: 50mm; box-sizing: border-box; } /* Padding bottom for footer space */
        
        .header-table { width: 100%; border-bottom: 3px solid #b71c1c; padding-bottom: 10px; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: bold; text-transform: uppercase; color: #1a237e; } /* Dark Blue */
        .company-details { font-size: 9px; line-height: 1.4; color: #555; }
        .po-title { font-size: 24px; font-weight: 900; color: #b71c1c; text-align: right; text-transform: uppercase; } /* Red Title */
        .po-meta { text-align: right; font-size: 10px; margin-top: 5px; }
        
        .info-section { width: 100%; margin-bottom: 30px; overflow: hidden; }
        .info-box-wrapper { width: 48%; float: left; margin-right: 2%; }
        
        .info-box { background: #f8f9fa !important; border: 1px solid #1a237e !important; border-top: 4px solid #1a237e !important; padding: 15px; border-radius: 4px; min-height: 100px; -webkit-print-color-adjust: exact; } 
        .box-title { font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; color: #1a237e !important; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .box-content { font-size: 10px; line-height: 1.5; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background-color: #1a237e !important; color: #fff !important; text-transform: uppercase; font-size: 9px; padding: 8px; border: 1px solid #1a237e; -webkit-print-color-adjust: exact; } 
        .items-table td { border: 1px solid #ddd; padding: 8px; font-size: 10px; }
        .items-table tr:nth-child(even) { background-color: #f0f4f8 !important; -webkit-print-color-adjust: exact; } 
        
        .notes-section { margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px; }
        .terms { font-size: 8px; color: #777; margin-top: 10px; text-align: justify; }
        
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 50%; 
            margin-left: -105mm; /* Half of 210mm */
            width: 210mm; 
            padding: 15mm; 
            box-sizing: border-box; 
            border-top: 1px solid #b71c1c; 
            background: white; 
        } 
        
        @media print {
            body { margin: 0; box-shadow: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .footer { position: fixed; bottom: 0; left: 0; margin-left: 0; width: 100%; } 
        }
        
        .signature-section { margin-top: 50px; }
        .signature-box { display: inline-block; width: 40%; vertical-align: top; }
        .signature-line { border-bottom: 1px solid #000; height: 40px; margin-bottom: 5px; width: 80%; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .info-section { width: 100%; margin-bottom: 30px; overflow: hidden; } /* Clearfix */
        .info-box-wrapper { width: 48%; float: left; margin-right: 2%; }
        .info-box-wrapper:last-child { margin-right: 0; }
        
        .info-box { background: #f8f9fa !important; border: 1px solid #1a237e !important; border-top: 4px solid #1a237e !important; padding: 15px; border-radius: 4px; min-height: 100px; -webkit-print-color-adjust: exact; } 
        .box-title { font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; color: #1a237e !important; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background-color: #1a237e !important; color: #fff !important; text-transform: uppercase; font-size: 9px; padding: 8px; border: 1px solid #1a237e; -webkit-print-color-adjust: exact; } 
        .items-table td { border: 1px solid #ddd; padding: 8px; font-size: 10px; }
        .items-table tr:nth-child(even) { background-color: #f0f4f8 !important; -webkit-print-color-adjust: exact; } 
        
        /* ... */
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td width="50%" valign="top">
                    <div class="company-name">Louis Marine Equipment Trading</div>
                    <div class="company-details">
                        P.O. Box 12345, Doha, Qatar<br>
                        Tel: +974 4444 5555 | Email: store@louismarine.qa<br>
                        TRN: 1234567890
                    </div>
                </td>
                <td width="50%" valign="top" class="text-right">
                    <div class="po-title">Delivery Note</div>
                    <table align="right" style="margin-top: 10px; border-collapse: collapse;">
                        <tr>
                            <td class="text-right" style="padding-right: 10px;"><strong>DN Number:</strong></td>
                            <td>DN-{{ $quotation->quotation_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-right" style="padding-right: 10px;"><strong>Date:</strong></td>
                            <td>{{ now()->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-right" style="padding-right: 10px;"><strong>Reference:</strong></td>
                            <td>{{ $quotation->reference_number ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Info Sections -->
        <div class="info-section clearfix">
            <div class="info-box-wrapper" style="margin-right: 2%;">
                <div class="info-box">
                    <div class="box-title">Deliver To (Customer)</div>
                    <div class="box-content">
                        <strong>{{ $quotation->customer->company_name }}</strong><br>
                        Attn: {{ $quotation->customer->company_name }}<br> <!-- Assuming contact person unavailable -->
                        Phone: {{ $quotation->customer->phone ?? 'N/A' }}<br>
                        Email: {{ $quotation->customer->email ?? 'N/A' }}
                    </div>
                </div>
            </div>
            <div class="info-box-wrapper">
                <div class="info-box">
                    <div class="box-title">Job Details</div>
                    <div class="box-content">
                        <strong>Job ID:</strong> {{ $quotation->service_job_id ?? 'N/A' }}<br>
                        <strong>Description:</strong> {{ $quotation->notes ?? 'Standard Delivery' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="75%">Item Description</th>
                    <th width="20%" class="text-center">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ optional($item->product)->item_name ?? $item->description }}</strong><br>
                        <span style="color: #666; font-size: 9px;">Part No: {{ optional($item->product)->part_number ?? '-' }}</span>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer / Signatures -->
        <div class="notes-section">
            <div class="terms">
                <strong>Declaration:</strong><br>
                Received the above items in good order and condition.
            </div>
        </div>
        
        <div class="footer">
            <div class="signature-section clearfix">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <strong>Delivered By</strong><br>
                    <span style="font-size: 9px; color: #666;">Louis Marine Qatar</span>
                </div>
                <div class="signature-box" style="float: right; text-align: right;">
                    <div class="signature-line" style="margin-left: auto;"></div>
                    <strong>Received By</strong><br>
                    <span style="font-size: 9px; color: #666;">Client Signature & Stamp</span>
                </div>
            </div>
            <div style="font-size: 8px; color: #999; margin-top: 10px; text-align: center;">
                Generated on {{ now()->format('d M Y, H:i A') }} | Louis Marine ERP
            </div>
        </div>
    </div>
</body>
</html>
