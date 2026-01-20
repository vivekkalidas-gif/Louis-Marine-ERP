<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note - {{ $job->delivery_note_number }}</title>
    <style>
        @media print {
            @page {
                margin: 0;
                size: auto;
            }
            body {
                margin: 1.6cm;
                padding: 0;
                background: white;
            }
            .no-print { display: none !important; }
        }
        
        body {
            font-family: Arial, sans-serif;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm;
            background: white;
            color: #000;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 28px;
        }
        
        .header .company-name {
            font-size: 32px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .header .tagline {
            color: #64748b;
            font-size: 14px;
        }
        
        .delivery-note-number {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-box {
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 14px;
            text-transform: uppercase;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 5px;
        }
        
        .info-row {
            margin: 8px 0;
            display: flex;
        }
        
        .info-label {
            font-weight: bold;
            width: 140px;
            color: #475569;
        }
        
        .info-value {
            flex: 1;
        }
        
        .equipment-section {
            margin: 30px 0;
        }
        
        .equipment-section h3 {
            color: #1e40af;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        table th {
            background: #1e40af;
            color: white;
            padding: 10px;
            text-align: left;
        }
        
        table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .notes-section {
            margin: 30px 0;
            padding: 15px;
            background: #f8fafc;
            border-left: 4px solid #1e40af;
        }
        
        .signature-section {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        
        .signature-box {
            border-top: 2px solid #000;
            padding-top: 10px;
            text-align: center;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1e40af;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .print-button:hover {
            background: #1e3a8a;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Delivery Note</button>

    <div class="header">
        <div class="company-name">LOUIS MARINE QATAR</div>
        <div class="tagline">Marine Safety Equipment Services</div>
        <p style="margin: 5px 0; font-size: 12px;">
            📍 Doha, Qatar | ☎️ +974 XXXX XXXX | ✉️ info@louismarine.qa
        </p>
    </div>

    <div class="delivery-note-number">
        DELIVERY NOTE: {{ $job->delivery_note_number }}
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Job Information</h3>
            <div class="info-row">
                <span class="info-label">Job Number:</span>
                <span class="info-value">{{ $job->job_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Job Title:</span>
                <span class="info-value">{{ $job->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Service Type:</span>
                <span class="info-value">{{ $job->job_type }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Delivery Date:</span>
                <span class="info-value">{{ $job->delivery_received_at?->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A') }}</span>
            </div>
        </div>

        <div class="info-box">
            <h3>Customer Information</h3>
            <div class="info-row">
                <span class="info-label">Company:</span>
                <span class="info-value">{{ $job->customer->company_name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Vessel:</span>
                <span class="info-value">{{ $job->vessel->name ?? 'N/A' }}</span>
            </div>
            @if($job->vessel)
            <div class="info-row">
                <span class="info-label">IMO Number:</span>
                <span class="info-value">{{ $job->vessel->imo_number ?? 'N/A' }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="info-box">
        <h3>Delivery Details</h3>
        <div class="info-row">
            <span class="info-label">Delivered By:</span>
            <span class="info-value">{{ $job->delivered_by }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Contact Person:</span>
            <span class="info-value">{{ $job->delivery_contact_person }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Contact Number:</span>
            <span class="info-value">{{ $job->delivery_contact_number }}</span>
        </div>
    </div>

    @if($job->equipment()->count() > 0)
    <div class="equipment-section">
        <h3>Equipment/Items Received</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Equipment Name</th>
                    <th>Quantity</th>
                    <th>Condition Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($job->equipment as $index => $equip)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $equip->equipmentItem->name ?? 'N/A' }}</td>
                    <td>{{ $equip->quantity }}</td>
                    <td>{{ $equip->condition_notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($job->delivery_notes)
    <div class="notes-section">
        <h4 style="margin: 0 0 10px 0; color: #1e40af;">📝 Delivery Notes:</h4>
        <p style="margin: 0;">{{ $job->delivery_notes }}</p>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <strong>Delivered By</strong><br>
            <div style="margin-top: 40px;">_____________________</div>
            <div style="margin-top: 5px; font-size: 12px;">{{ $job->delivered_by }}</div>
            <div style="margin-top: 5px; font-size: 12px;">Date: {{ now()->format('d/m/Y') }}</div>
        </div>

        <div class="signature-box">
            <strong>Received By (Louis Marine)</strong><br>
            <div style="margin-top: 40px;">_____________________</div>
            <div style="margin-top: 5px; font-size: 12px;">{{ auth()->user()->name }}</div>
            <div style="margin-top: 5px; font-size: 12px;">Date: {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Louis Marine Qatar</strong> - Your Trusted Partner in Marine Safety</p>
        <p style="font-size: 10px; margin-top: 10px;">
            This is a computer-generated delivery note. No signature is required for validity.<br>
            For inquiries, please contact us at info@louismarine.qa or +974 XXXX XXXX
        </p>
    </div>
</body>
</html>
