<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Dispatch Note {{ $dispatchNote->dispatch_number }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        /* Branding Colors */
        .text-primary { color: #1e3a8a; } /* Blue-900 */
        .bg-primary { background-color: #1e3a8a; color: white; }
        .border-primary { border-bottom: 2px solid #1e3a8a; }

        /* Header */
        .company-header {
            text-align: center;
            margin-bottom: 5px;
        }
        .company-name {
            font-size: 26px;
            font-weight: bold;
            color: #2b459a;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .company-sub {
            font-size: 12px;
            color: #555;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 10px;
            color: #333;
            margin-bottom: 10px;
        }
        .company-info span {
            margin: 0 5px;
        }
        .header-line {
            border-bottom: 2px solid #2b459a;
            margin-bottom: 15px;
        }

        /* Doc Title */
        .doc-title-container {
            text-align: right;
            margin-bottom: 15px;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            color: #2b459a;
            text-transform: uppercase;
        }

        /* Grid Layout */
        .grid-table {
            width: 100%;
            margin-bottom: 15px;
            border-spacing: 10px 0;
            border-collapse: separate;
            margin-left: -10px;
        }
        .grid-cell {
            width: 50%;
            vertical-align: top;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 0;
            overflow: hidden;
        }
        .section-header {
            background-color: transparent;
            color: #2b459a;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 12px;
            border-bottom: 1px solid #2b459a;
        }
        .section-body {
            padding: 10px 12px;
        }
        .info-row {
            margin-bottom: 4px;
            clear: both;
        }
        .info-label {
            width: 100px;
            float: left;
            font-weight: bold;
            color: #666;
        }
        .info-value {
            margin-left: 105px;
            color: #000;
        }

        /* Items Table */
        .items-title {
            font-size: 14px;
            font-weight: bold;
            color: #2b459a;
            margin-bottom: 8px;
            border-bottom: 2px solid #2b459a;
            padding-bottom: 2px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .items-table th {
            background-color: #2b459a;
            color: white;
            text-align: left;
            font-size: 11px;
            padding: 10px;
            text-transform: capitalize;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        /* Signatures */
        .signature-section {
            margin-top: 60px;
            width: 100%;
        }
        .sig-col {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .sig-line-container {
            width: 80%;
            margin: 0 auto;
        }
        .sig-line {
            border-top: 1px solid #000;
            margin-bottom: 5px;
            width: 80%;
            margin: 0 auto;
        }
        .sig-name {
            font-weight: bold;
            font-size: 12px;
            margin-top: 5px;
        }
        .sig-title {
            font-size: 11px;
            color: #555;
        }

        /* Footer */
        .page-footer {
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="company-header">
        @if(config('settings.company_logo'))
            @php
                $logoPath = config('settings.company_logo');
                if (\Illuminate\Support\Str::startsWith($logoPath, 'storage/')) {
                    $logoPath = substr($logoPath, 8);
                }
                $fullLogoPath = storage_path('app/public/' . $logoPath);
            @endphp
            @if(file_exists($fullLogoPath))
                <img src="{{ $fullLogoPath }}" style="height: 60px; margin-bottom: 10px;">
            @else
                <div class="company-name">{{ config('settings.company_name', 'LOUIS MARINE QATAR') }}</div>
            @endif
        @else
            <div class="company-name">{{ config('settings.company_name', 'LOUIS MARINE QATAR') }}</div>
        @endif
        <div class="company-sub">Marine Safety Equipment Services</div>
        <div class="company-info">
            <span>📍 {{ config('settings.company_address', 'Doha, Qatar') }}</span> | 
            <span>📞 {{ config('settings.company_phone', '+974 44603388') }}</span> | 
            <span>✉️ {{ config('settings.company_email', 'info@louismarine.qa') }}</span>
        </div>
    </div>
    <div class="header-line"></div>

    <!-- Doc Title -->
    <div class="doc-title-container">
        <div class="doc-title">DISPATCH NOTE: {{ $dispatchNote->dispatch_number }}</div>
    </div>

    <!-- Info Grid -->
    <table class="grid-table">
        <tr>
            <td class="grid-cell">
                <div class="section-header">JOB INFORMATION</div>
                <div class="section-body">
                    <div class="info-row">
                        <div class="info-label">Note Number:</div>
                        <div class="info-value">{{ $dispatchNote->dispatch_number }}</div>
                    </div>
                    @if($dispatchNote->serviceJob)
                    <div class="info-row">
                        <div class="info-label">Job Number:</div>
                        <div class="info-value">{{ $dispatchNote->serviceJob->job_number }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Service Type:</div>
                        <div class="info-value">{{ $dispatchNote->serviceJob->job_type }}</div>
                    </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">Dispatch Type:</div>
                        <div class="info-value">{{ $dispatchNote->type }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Dispatch Date:</div>
                        <div class="info-value">{{ $dispatchNote->date ? $dispatchNote->date->format('d M Y') : 'N/A' }}</div>
                    </div>
                </div>
            </td>
            <td class="grid-cell">
                <div class="section-header">CUSTOMER INFORMATION</div>
                <div class="section-body">
                    <div class="info-row">
                        <div class="info-label">Company:</div>
                        <div class="info-value" style="font-weight: bold;">{{ $dispatchNote->receiver_name }}</div>
                    </div>
                    @if($dispatchNote->serviceJob && $dispatchNote->serviceJob->vessel)
                    <div class="info-row">
                        <div class="info-label">Vessel:</div>
                        <div class="info-value">{{ $dispatchNote->serviceJob->vessel->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">IMO Number:</div>
                        <div class="info-value">{{ $dispatchNote->serviceJob->vessel->imo_number }}</div>
                    </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Items -->
    <div class="items-title">Equipment/Items Received</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="50%">Equipment Name</th>
                <th width="15%">Quantity</th>
                <th width="30%">Remarks / Serial</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dispatchNote->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><span style="font-weight: bold;">{{ $item->description }}</span></td>
                <td>{{ $item->quantity }}</td>
                <td>
                    {{ $item->remarks }}
                    @if($item->serial_number)
                        <br><span style="color: #666; font-size: 9px;">SN: {{ $item->serial_number }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Signatures -->
    <table class="signature-section">
        <tr>
            <td class="sig-col">
                <div style="font-weight: bold; margin-bottom: 50px;">Delivered By</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $dispatchNote->creator->name ?? 'Louis Marine' }}</div>
                <div class="sig-title">Date: {{ now()->format('d/m/Y') }}</div>
            </td>
            <td class="sig-col">
                <div style="font-weight: bold; margin-bottom: 50px;">Received By (Louis Marine)</div>
                <div class="sig-line"></div>
                <div class="sig-name">&nbsp;</div>
                <div class="sig-title">Date: {{ now()->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="page-footer">
        {{ config('settings.company_name', 'Louis Marine Qatar') }} - Your Trusted Partner in Marine Safety<br>
        <div style="font-size: 8px;">This is a computer-generated delivery note. No signature is required for validity.</div>
    </div>

</body>
</html>
