<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Supplier - {{ $supplier->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #333; margin: 0; padding: 0; }
        .header { width: 100%; border-bottom: 2px solid #333; margin-bottom: 30px; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; }
        .company-details { font-size: 11px; color: #555; margin-top: 5px; }
        .title { text-align: right; float: right; font-size: 18px; font-weight: bold; text-transform: uppercase; color: #555; }
        
        .section-title { font-size: 14px; font-weight: bold; border-bottom: 1px solid #eee; margin-top: 20px; margin-bottom: 10px; padding-bottom: 5px; color: #111; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        td { padding: 6px 0; vertical-align: top; }
        .label { width: 140px; font-weight: bold; color: #555; }
        .value { color: #000; }
        
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; background: #eee; color: #333; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #f3f4f6; color: #374151; }
        
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="title">Supplier Profile</div>
        <div>
            <div class="logo">
                @if($companyData['logo'])
                    <img src="{{ public_path($companyData['logo']) }}" style="height: 40px; margin-bottom: 5px;">
                @else
                    {{ $companyData['name'] }}
                @endif
            </div>
            <div class="company-details">
                {{ $companyData['address'] }}<br>
                Tel: {{ $companyData['phone'] }} | Email: {{ $companyData['email'] }}
            </div>
        </div>
    </div>

    <div class="section-title">General Information</div>
    <table>
        <tr>
            <td class="label">Company Name:</td>
            <td class="value"><strong>{{ $supplier->name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Type:</td>
            <td class="value" style="text-transform: capitalize;">{{ $supplier->type }}</td>
        </tr>
        <tr>
            <td class="label">Status:</td>
            <td class="value">
                <span class="badge {{ $supplier->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                    {{ strtoupper($supplier->status) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="label">Category:</td>
            <td class="value">{{ $supplier->category ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Contact Information</div>
    <table>
        <tr>
            <td class="label">Contact Person:</td>
            <td class="value">{{ $supplier->contact_person ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Email:</td>
            <td class="value">{{ $supplier->email ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Phone:</td>
            <td class="value">{{ $supplier->phone ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Website:</td>
            <td class="value">{{ $supplier->website ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Address:</td>
            <td class="value">{!! nl2br(e($supplier->address ?? '-')) !!}</td>
        </tr>
    </table>

    <div class="section-title">Financial Details</div>
    <table>
        <tr>
            <td class="label">Tax ID / VAT:</td>
            <td class="value">{{ $supplier->tax_id ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Payment Terms:</td>
            <td class="value">{{ $supplier->payment_terms ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Currency:</td>
            <td class="value">{{ $supplier->currency->code ?? $supplier->currency_id ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Incoterm:</td>
            <td class="value">{{ $supplier->incoterm ?? '-' }}</td>
        </tr>
    </table>
</body>
</html>
