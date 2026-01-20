<!DOCTYPE html>
<html>
<head>
    <title>New Quote Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #0b1c3c;">New Quote Request Received</h2>
    <p><strong>Reference:</strong> {{ $quoteRequest->reference_no }}</p>
    <p><strong>Date:</strong> {{ $quoteRequest->created_at->format('d M Y H:i') }}</p>
    
    <h3>Customer Details</h3>
    <ul>
        <li><strong>Name:</strong> {{ $quoteRequest->contact_person }}</li>
        <li><strong>Company:</strong> {{ $quoteRequest->company_name ?? 'N/A' }}</li>
        <li><strong>Email:</strong> {{ $quoteRequest->email }}</li>
        <li><strong>Phone:</strong> {{ $quoteRequest->phone ?? 'N/A' }}</li>
    </ul>

    @if($quoteRequest->message)
        <p><strong>Message:</strong><br>
        <em>{{ $quoteRequest->message }}</em></p>
    @endif

    <h3>Requested Items</h3>
    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%; border-color: #ddd;">
        <thead>
            <tr style="background-color: #f8f8f8;">
                <th>Product</th>
                <th>SKU</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quoteRequest->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->sku }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 20px;">
        <a href="{{ route('admin.quote-requests.show', $quoteRequest) }}" style="background-color: #0b1c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View in Admin Panel</a>
    </p>
</body>
</html>
