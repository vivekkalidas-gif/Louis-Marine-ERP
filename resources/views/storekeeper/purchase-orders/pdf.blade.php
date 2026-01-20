<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ $purchaseOrder->po_number }}</title>
    <style>
        @page { size: A4; margin: 10mm; } 
        body { font-family: 'Inter', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .container { width: 100%; box-sizing: border-box; }
        
        .header-table { width: 100%; border-bottom: 3px solid #b71c1c; padding-bottom: 10px; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: bold; text-transform: uppercase; color: #1a237e; }
        .company-details { font-size: 9px; line-height: 1.4; color: #555; }
        .po-title { font-size: 24px; font-weight: 900; color: #b71c1c; text-align: right; text-transform: uppercase; }
        
        .info-section { width: 100%; margin-bottom: 30px; }
        /* Use table for layout instead of floats for better stability in dompdf */
        .info-table { width: 100%; border-collapse: separate; border-spacing: 10px 0; }
        .info-table td { width: 50%; vertical-align: top; padding: 0; }
        
        .info-box { background: #f8f9fa; border: 1px solid #1a237e; border-top: 4px solid #1a237e; padding: 15px; border-radius: 4px; min-height: 100px; }
        .box-title { font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; color: #1a237e; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .box-content { font-size: 10px; line-height: 1.5; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background-color: #1a237e; color: #fff; text-transform: uppercase; font-size: 9px; padding: 8px; border: 1px solid #1a237e; }
        .items-table td { border: 1px solid #ddd; padding: 8px; font-size: 10px; }
        .items-table tr:nth-child(even) { background-color: #f0f4f8; }
        
        .totals-table { width: 50%; float: right; border-collapse: collapse; margin-top: 10px; page-break-inside: avoid; }
        .totals-table td { padding: 5px 10px; border-bottom: 1px solid #eee; }
        .totals-table .total-row td { background-color: #1a237e; color: white; font-weight: bold; font-size: 12px; border-top: none; }
        
        .notes-section { margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px; page-break-inside: avoid; }
        .terms { font-size: 8px; color: #777; margin-top: 10px; text-align: justify; }
        
        .footer { 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            width: 100%; 
            padding: 10px 0;
            border-top: 1px solid #b71c1c; 
            background: white; 
        } 
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
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
                    <div class="po-title">Purchase Order</div>
                    <table align="right" style="margin-top: 10px; border-collapse: collapse;">
                        <tr>
                            <td class="text-right" style="padding-right: 10px;"><strong>PO Number:</strong></td>
                            <td>{{ $purchaseOrder->po_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-right" style="padding-right: 10px;"><strong>Date:</strong></td>
                            <td>{{ $purchaseOrder->po_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-right" style="padding-right: 10px;"><strong>Expected Delivery:</strong></td>
                            <td>{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

         <!-- Info Sections using Table layout -->
        <table class="info-table">
            <tr>
                <td style="padding-right: 10px;">
                    <div class="info-box">
                        <div class="box-title">Vendor / Supplier</div>
                        <div class="box-content">
                            <strong>{{ $purchaseOrder->supplier->name }}</strong><br>
                            Attn: {{ $purchaseOrder->supplier->contact_person }}<br>
                            {{ $purchaseOrder->supplier->address ?? 'Address Not Provided' }}<br>
                            Phone: {{ $purchaseOrder->supplier->phone ?? 'N/A' }}<br>
                            Email: {{ $purchaseOrder->supplier->email ?? 'N/A' }}
                        </div>
                    </div>
                </td>
                <td style="padding-left: 10px;">
                    <div class="info-box">
                        <div class="box-title">Ship To</div>
                        <div class="box-content">
                            <strong>Louis Marine Equipment Trading</strong><br>
                            Warehouse 4, Industrial Area<br>
                            Street 23, Gate 5<br>
                            Doha, Qatar<br>
                            Contact: Store Manager (+974 5555 6666)
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        <br>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Item Description</th>
                    <th width="15%" class="text-right">Quantity</th>
                    <th width="15%" class="text-right">Unit Price</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->inventoryItem->item_name }}</strong><br>
                        <span style="color: #666; font-size: 9px;">Brand: {{ $item->inventoryItem->brand ?? '-' }} | Model: {{ $item->inventoryItem->model ?? '-' }}</span>
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="clearfix">
            <table class="totals-table">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">{{ number_format($purchaseOrder->items->sum('total_price'), 2) }}</td>
                </tr>
                @if($purchaseOrder->transport_charges > 0)
                <tr>
                    <td>Transport Charges</td>
                    <td class="text-right">{{ number_format($purchaseOrder->transport_charges, 2) }}</td>
                </tr>
                @endif
                @if($purchaseOrder->customs_charges > 0)
                <tr>
                    <td>Customs Charges</td>
                    <td class="text-right">{{ number_format($purchaseOrder->customs_charges, 2) }}</td>
                </tr>
                @endif
                @if($purchaseOrder->other_charges > 0)
                <tr>
                    <td>Other Charges</td>
                    <td class="text-right">{{ number_format($purchaseOrder->other_charges, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Grand Total (QAR)</td>
                    <td class="text-right">{{ number_format($purchaseOrder->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer / Notes -->
        <div class="notes-section">
            @if($purchaseOrder->notes)
            <div style="margin-bottom: 20px;">
                <strong>Notes / Special Instructions:</strong><br>
                <div style="background: #eee; padding: 10px; border-radius: 4px; font-size: 9px; margin-top: 5px;">
                    {{ $purchaseOrder->notes }}
                </div>
            </div>
            @endif

            <div class="terms">
                <strong>Terms & Conditions:</strong>
                <ol style="padding-left: 15px; margin-top: 5px;">
                    <li>Please quote PO number on all invoices and correspondence.</li>
                    <li>Goods must be delivered to the shipping address strictly between 8:00 AM and 4:00 PM.</li>
                    <li>Payment terms: 30 Days from date of invoice unless otherwise agreed.</li>
                    <li>We reserve the right to reject goods that do not meet our quality standards.</li>
                </ol>
            </div>
        </div>
        
        <div class="footer">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>Authorized Signature</strong>
            </div>
            <div style="float: left; width: 60%; font-size: 8px; color: #999; margin-top: 20px;">
                Generated on {{ now()->format('d M Y, H:i A') }} | Louis Marine ERP
            </div>
        </div>
    </div>
</body>
</html>
