<!DOCTYPE html>
<html>
<head>
    <title>Quote Request Received</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #0b1c3c;">Thank you for your request</h2>
    <p>Dear {{ $quoteRequest->contact_person }},</p>
    
    <p>We have successfully received your request for quotation. Our team is reviewing your requirements and will get back to you shortly with a formal quotation.</p>

    <p><strong>Reference No:</strong> {{ $quoteRequest->reference_no }}</p>

    <h3>Request Summary</h3>
    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%; border-color: #ddd;">
        <thead>
            <tr style="background-color: #f8f8f8;">
                <th>Product</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
             @foreach($quoteRequest->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>If you have any urgent queries, please contact us directly at <a href="mailto:sales@louismarine.com">sales@louismarine.com</a>.</p>

    <p>Best Regards,<br>
    <strong>Louis Marine Equipment Team</strong></p>
</body>
</html>
