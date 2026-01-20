<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note - {{ $deliveryNote->dr_number }}</title>
    <style>
        body { font-family: sans-serif; padding: 40px; color: #333; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .logo { font-size: 24px; font-weight: bold; color: #4F46E5; } /* Indigo-600 */
        .title { font-size: 28px; font-weight: bold; margin-bottom: 5px; }
        .details { margin-bottom: 40px; display: flex; justify-content: space-between; }
        .box { background: #f9fafb; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; width: 48%; }
        .box h3 { margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; color: #6b7280; }
        .box p { margin: 0; font-weight: bold; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #e5e7eb; font-size: 12px; text-transform: uppercase; color: #6b7280; }
        td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 200px; }
        .line { border-top: 1px solid #333; margin-top: 40px; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <div>
            <div class="logo">Louis Marine</div>
            <p>Doha, Qatar</p>
        </div>
        <div style="text-align: right;">
            <div class="title">DELIVERY NOTE</div>
            <div>{{ $deliveryNote->dr_number }}</div>
            <div>Date: {{ $deliveryNote->date->format('d-M-Y') }}</div>
        </div>
    </div>

    <div class="details">
        <div class="box">
            <h3>Delivered To</h3>
            <p>{{ $deliveryNote->customer->company_name }}</p>
            <div style="font-weight: normal; font-size: 14px; margin-top: 5px; color: #555;">
                {{ $deliveryNote->customer->address ?? '' }}<br>
                {{ $deliveryNote->customer->phone ?? '' }}
            </div>
        </div>
        <div class="box">
            <h3>Reference</h3>
            <p>{{ $deliveryNote->reference ?? 'N/A' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Description</th>
                <th style="text-align: right;">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deliveryNote->items as $item)
            <tr>
                <td>{{ $item->inventoryItem->item_name ?? '-' }}</td>
                <td>{{ $item->description }}</td>
                <td style="text-align: right;">{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($deliveryNote->notes)
        <div style="margin-top: 20px; font-size: 14px; color: #555;">
            <strong>Notes:</strong> {{ $deliveryNote->notes }}
        </div>
    @endif

    <div class="footer">
        <div class="signature">
            <div class="line"></div>
            Received By
        </div>
        <div class="signature">
            <div class="line"></div>
            Authorized Signature
        </div>
    </div>

    <div class="no-print" style="margin-top: 40px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4F46E5; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Note</button>
        <button onclick="window.history.back()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Back</button>
    </div>

</body>
</html>
