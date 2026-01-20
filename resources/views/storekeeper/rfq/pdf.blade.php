<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>RFQ {{ $rfq->reference_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #1a56db; }
        .title { text-align: right; font-size: 20px; font-weight: bold; color: #555; }
        .meta { width: 100%; margin-bottom: 20px; }
        .meta td { padding: 4px; vertical-align: top; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { background: #f3f4f6; padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .table td { padding: 8px; border-bottom: 1px solid #eee; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td class="logo">Louis Marine Services</td>
            <td class="title">REQUEST FOR QUOTATION</td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td width="50%">
                <strong>To:</strong><br>
                {{ $supplier->name }}<br>
                {{ $supplier->email }}<br>
                {{ $supplier->address }}
            </td>
            <td width="50%" style="text-align: right;">
                <strong>RFQ Ref:</strong> {{ $rfq->reference_number }}<br>
                <strong>Date:</strong> {{ $rfq->created_at->format('d M Y') }}<br>
                <strong>Deadline:</strong> {{ $rfq->deadline_date ? $rfq->deadline_date->format('d M Y') : 'ASAP' }}<br>
                <strong>Preferred Currency:</strong> {{ $rfq->currency->code ?? 'Local' }}
            </td>
        </tr>
    </table>

    <p>Dear Sir/Madam,</p>
    <p>Please provide your best quotation for the following items. Include lead time, delivery terms (Incoterms), and payment terms.</p>

    <table class="table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="55%">Description</th>
                <th width="15%">Quantity</th>
                <th width="15%">Unit</th>
                <th width="10%">Required Brand</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rfq->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    {{ $item->product->item_name ?? $item->description }}
                    @if($item->product && $item->product->model)
                        <br><small class="text-gray-500 dark:text-gray-400">Model: {{ $item->product->model }}</small>
                    @endif
                    @if($item->description && $item->description != ($item->product->item_name ?? ''))
                        <br><small>{{ $item->description }}</small>
                    @endif
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->product->brand ?? 'Any' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Notes:</strong></p>
    <ul>
        <li>Please quote with Incoterm: <strong>{{ $rfq->incoterm ?? 'Ex Works' }}</strong>.</li>
        <li>Delivery Location: {{ $rfq->delivery_location ?? 'Louis Marine Warehouse, Doha, Qatar' }}.</li>
    </ul>

    <div class="footer">
        Louis Marine Services | CR No: 12345 | Doha, Qatar | +974 1234 5678 | info@louismarine.com
    </div>
</body>
</html>
