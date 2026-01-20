<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->certificateTemplate->title ?? 'Certificate' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }
        
        .certificate-container {
            padding: 30px;
            max-width: 100%;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2563eb;
        }
        
        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .certificate-title {
            font-size: 16pt;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        
        /* Certificate Info */
        .cert-info {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f3f4f6;
            border-left: 4px solid #2563eb;
        }
        
        .cert-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .cert-info td {
            padding: 6px 10px;
            font-size: 10pt;
        }
        
        .cert-info .label {
            font-weight: bold;
            width: 35%;
            color: #4b5563;
        }
        
        /* Sections */
        .section {
            margin-bottom: 25px;
            /* page-break-inside: avoid;  Removed to allow long tables to break */
        }

        .data-table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }
        
        tfoot {
            display: table-footer-group;
        }
        
        .section-header {
            background-color: #2563eb;
            color: white;
            padding: 10px 15px;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .section-content {
            padding: 0 15px;
        }
        
        /* Fields */
        .field-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .field-row {
            display: table-row;
        }
        
        .field-cell {
            display: table-cell;
            padding: 8px 10px;
            vertical-align: top;
            width: 50%;
        }
        
        .field-cell.full {
            width: 100%;
        }
        
        .field-label {
            font-weight: bold;
            color: #4b5563;
            font-size: 9pt;
            margin-bottom: 3px;
        }
        
        .field-value {
            color: #1f2937;
            font-size: 10pt;
            padding: 6px 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 3px;
            min-height: 25px;
        }
        
        /* Table for repeatable sections */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .data-table th {
            background-color: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            border: 1px solid #d1d5db;
        }
        
        .data-table td {
            padding: 8px;
            font-size: 9pt;
            border: 1px solid #e5e7eb;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        
        .footer-content {
            width: 100%;
        }
        
        .qr-block {
            float: left;
            width: 25%;
            text-align: center;
        }
        
        .signature-block {
            float: right;
            width: 70%;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin-top: 50px;
            padding-top: 8px;
            text-align: center;
            font-size: 10pt;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">LOUIS MARINE QATAR</div>
            <div class="certificate-title">{{ $certificate->certificateTemplate->title ?? 'Equipment Certificate' }}</div>
        </div>

        <!-- Certificate Info -->
        <div class="cert-info">
            <table>
                <tr>
                    <td class="label">Certificate No:</td>
                    <td>{{ $certificate->certificate_number }}</td>
                    <td class="label">Issue Date:</td>
                    <td>{{ $certificate->issue_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Customer:</td>
                    <td>{{ $certificate->customer->company_name }}</td>
                    <td class="label">Vessel:</td>
                    <td>{{ $certificate->vessel->name ?? 'N/A' }}</td>
                </tr>
                @if($certificate->expiry_date)
                <tr>
                    <td class="label">Expiry Date:</td>
                    <td colspan="3">{{ $certificate->expiry_date->format('d M Y') }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if($certificate->certificateTemplate)
            <!-- Template-based Sections -->
            @foreach($certificate->certificateTemplate->sections as $section)
            <div class="section">
                <div class="section-header">{{ $section->name }}</div>
                <div class="section-content">
                    @if($section->is_repeatable)
                        <!-- Repeatable Section - Table Format -->
                        @php
                            $instances = $certificate->fieldValues
                                ->where('field.section_id', $section->id)
                                ->pluck('section_instance')
                                ->unique()
                                ->sort();
                        @endphp
                        
                        @if($instances->count() > 0)
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @foreach($section->fields as $field)
                                    <th>{{ $field->field_label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($instances as $instance)
                                <tr>
                                    @foreach($section->fields as $field)
                                    @php
                                        $value = $certificate->fieldValues
                                            ->where('field_id', $field->id)
                                            ->where('section_instance', $instance)
                                            ->first();
                                    @endphp
                                    <td>{{ $value->field_value ?? '-' }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    @else
                        <!-- Regular Section - Grid Format -->
                        <div class="field-grid">
                            @foreach($section->fields->chunk(2) as $fieldChunk)
                            <div class="field-row">
                                @foreach($fieldChunk as $field)
                                @php
                                    $value = $certificate->fieldValues->where('field_id', $field->id)->first();
                                @endphp
                                <div class="field-cell {{ $field->width === 'full' ? 'full' : '' }}">
                                    <div class="field-label">{{ $field->field_label }}:</div>
                                    <div class="field-value">{{ $value->field_value ?? '-' }}</div>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content clearfix">
                <!-- QR Code -->
                <div class="qr-block">
                    @if($qrCodeBase64)
                    <img src="data:image/png;base64,{{ $qrCodeBase64 }}" style="width: 100px; height: 100px;" alt="QR Code">
                    @endif
                    <div style="font-size: 8pt; margin-top: 5px;">Scan to Verify</div>
                </div>

                <!-- Signature -->
                <div class="signature-block">
                    <div class="signature-line">
                        <strong>{{ $certificate->issuedBy->name ?? 'Authorized Signatory' }}</strong><br>
                        <span style="font-size: 9pt;">{{ $certificate->issuedBy->role ?? 'Engineer' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
