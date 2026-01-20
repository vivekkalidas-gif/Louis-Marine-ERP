<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        
        /* Theme Colors */
        :root {
            --primary-blue: #1a237e; /* Deep Blue from image */
            --border-color: #d1d5db;
        }

        .header { text-align: center; margin-bottom: 20px; }
        .company-name { font-size: 24px; font-weight: bold; color: #1a237e; text-transform: uppercase; margin-bottom: 5px; }
        .company-sub { font-size: 12px; color: #1a237e; margin-bottom: 5px; }
        .company-contact { font-size: 10px; color: #333; }
        .header-line { border-bottom: 2px solid #1a237e; margin-top: 10px; margin-bottom: 20px; }

        .doc-title { text-align: right; color: #1a237e; font-weight: bold; font-size: 14px; text-transform: uppercase; margin-bottom: 15px; }

        /* Info Boxes */
        .info-container { width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 15px 0; margin-left: -15px; } /* spacing hack for tables */
        .info-box-wrapper { width: 50%; vertical-align: top; padding: 0 10px; }
        .info-box { border: 1px solid #d1d5db; border-radius: 5px; padding: 10px; height: 120px; }
        
        .box-title { color: #1a237e; font-weight: bold; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid #1a237e; padding-bottom: 5px; margin-bottom: 10px; }
        
        .info-row { margin-bottom: 5px; font-size: 11px; }
        .info-label { font-weight: bold; color: #555; display: inline-block; width: 90px; }
        .info-val { display: inline-block; color: #000; }

        /* Sections */
        .section-header { color: #1a237e; font-size: 12px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #1a237e; padding-bottom: 5px; margin-top: 25px; margin-bottom: 10px; }

        /* Table */
        table.items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items-table th { background-color: #1a237e; color: white; padding: 8px; text-align: left; font-size: 11px; font-weight: bold; }
        table.items-table td { padding: 8px; border-bottom: 1px solid #eee; font-size: 11px; color: #333; }
        table.items-table tr:last-child td { border-bottom: 1px solid #1a237e; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Footer */
        .footer { margin-top: 50px; }
        .sig-table { width: 100%; }
        .sig-td { width: 45%; text-align: center; vertical-align: bottom; }
        .sig-line { border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px; font-weight: bold; font-size: 11px; }
        
        .page-footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #777; padding: 10px; border-top: 1px solid #eee; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ config('settings.company_name', 'LOUIS MARINE QATAR') }}</div>
        <div class="company-sub">Marine Safety Equipment Services</div>
        <div class="company-contact">
             {{ config('settings.company_address', 'P.O Box: 9864, Doha, Qatar') }} | 
             {{ config('settings.company_phone', '+974 44603388') }} | 
             {{ config('settings.company_email', 'info@louismarine.qa') }}
        </div>
        <div class="header-line"></div>
    </div>

    <!-- Doc Title -->
    <div class="doc-title">QUOTATION: {{ $quotation->quotation_number }}</div>

    <!-- Info Boxes (Using Table for layout stability in DomPDF) -->
    <table style="width: 100%; border-spacing: 10px; border-collapse: separate;">
        <tr>
            <td style="width: 48%; border: 1px solid #ddd; border-radius: 5px; padding: 10px; vertical-align: top;">
                <div class="box-title">Job Information</div>
                <div class="info-row"><span class="info-label">Quote Ref:</span> <span class="info-val">{{ $quotation->reference_number ?? 'N/A' }}</span></div>
                <div class="info-row"><span class="info-label">Quote Date:</span> <span class="info-val">{{ $quotation->date->format('d M Y') }}</span></div>
                <div class="info-row"><span class="info-label">Valid Until:</span> <span class="info-val">{{ $quotation->valid_until ? $quotation->valid_until->format('d M Y') : 'N/A' }}</span></div>
                <div class="info-row"><span class="info-label">Status:</span> <span class="info-val" style="text-transform: capitalize;">{{ $quotation->status }}</span></div>
            </td>
            <td style="width: 48%; border: 1px solid #ddd; border-radius: 5px; padding: 10px; vertical-align: top;">
                <div class="box-title">Customer Information</div>
                <div class="info-row"><span class="info-label">Company:</span> <span class="info-val font-bold">{{ $quotation->customer->company_name }}</span></div>
                <div class="info-row"><span class="info-label">Address:</span> <span class="info-val">{{ Str::limit($quotation->customer->address, 40) }}</span></div>
                <div class="info-row"><span class="info-label">Phone:</span> <span class="info-val">{{ $quotation->customer->phone }}</span></div>
                <div class="info-row"><span class="info-label">Email:</span> <span class="info-val">{{ $quotation->customer->email }}</span></div>
            </td>
        </tr>
    </table>

    <!-- Items -->
    <div class="section-header">Equipment / Items Quoted</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">#</th>
                <th width="50%">Description</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Unit Price</th>
                <th width="20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <b>{{ optional($item->product)->item_name ?? $item->description }}</b>
                    @if($item->description && $item->description !== (optional($item->product)->item_name ?? ''))
                    <br><span style="color: #555; font-size: 10px;">{{ $item->description }}</span>
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right font-bold">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
            <!-- Totals -->
            <tr>
                <td colspan="3" style="border: none;"></td>
                <td class="text-right" style="border: none; padding-top: 10px; font-weight: bold;">Total (QAR)</td>
                <td class="text-right" style="border: none; padding-top: 10px; font-weight: bold; color: #1a237e; font-size: 14px;">{{ number_format($quotation->total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Notes -->
    @if($quotation->notes)
    <div style="margin-top: 20px; border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9;">
        <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px;">Notes / Terms:</div>
        <div style="font-size: 10px; color: #555;">{{ $quotation->notes }}</div>
    </div>
    @endif

    <!-- Signatures -->
    <div class="footer">
        <table class="sig-table">
            <tr>
                <td class="sig-td">
                    <div style="height: 50px;"></div>
                    <div class="sig-line">Prepared By</div>
                </td>
                 <td style="width: 10%;"></td>
                <td class="sig-td">
                    <div style="height: 50px;"></div>
                    <div class="sig-line">Accepted By (Client)</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-footer">
        {{ config('settings.company_name') }} - Your Trusted Partner in Marine Safety<br>
        <span style="color: #999; font-size: 8px;">Generated on {{ now()->format('d M Y H:i') }}</span>
    </div>
</body>
</html>
