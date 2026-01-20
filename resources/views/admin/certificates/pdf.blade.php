<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate {{ $certificate->certificate_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.4;
        }
        
        .container {
            width: 100%;
            padding: 30px;
            border: 4px double #0a2142;
            min-height: 1000px;
            position: relative;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #0a2142;
        }
        
        .header h1 {
            color: #0a2142;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 3px;
        }
        
        .header p {
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 11px;
        }
        
        /* Certificate Title */
        .title {
            text-align: center;
            margin: 30px 0;
            padding: 20px 0;
            background: #f8f9fa;
            border-left: 4px solid #0a2142;
            border-right: 4px solid #0a2142;
        }
        
        .title h2 {
            font-size: 24px;
            font-weight: bold;
            color: #0a2142;
            margin-bottom: 8px;
        }
        
        .cert-number {
            font-size: 13px;
            color: #c00;
            font-weight: bold;
        }
        
        /* Content Section */
        .content {
            font-size: 12px;
            margin: 0 20px;
        }
        
        .intro-text {
            text-align: center;
            font-size: 13px;
            margin: 20px 0;
            font-style: italic;
            color: #555;
        }
        
        /* Details Table */
        .details-section {
            margin: 25px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .details-table td {
            padding: 10px 15px;
            vertical-align: top;
        }
        
        .label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
        }
        
        .value {
            font-size: 14px;
            font-weight: bold;
            color: #0a2142;
        }
        
        /* Equipment Table */
        .equipment-section {
            margin: 25px 0;
        }
        
        .section-title {
            font-size: 13px;
            color: #0a2142;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #0a2142;
            letter-spacing: 1px;
        }
        
        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 10px;
        }
        
        .equipment-table thead {
            background: #0a2142;
            color: white;
        }
        
        .equipment-table th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .equipment-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .equipment-table td {
            border: 1px solid #ddd;
            padding: 8px 6px;
            vertical-align: top;
        }
        
        .equipment-type {
            font-weight: bold;
            color: #0a2142;
            font-size: 11px;
        }
        
        .serial-number {
            color: #666;
            font-size: 9px;
            margin-top: 2px;
        }
        
        .condition-good {
            color: #28a745;
            font-weight: bold;
        }
        
        .condition-repair {
            color: #dc3545;
            font-weight: bold;
        }
        
        /* Certification Statement */
        .certification-text {
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: #fff9e6;
            border-left: 3px solid #ffc107;
            font-size: 12px;
            font-style: italic;
        }
        
        /* Dates Section */
        .dates-section {
            margin: 25px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .dates-container {
            display: table;
            width: 100%;
            padding: 10px;
            text-align: center;
        }
        
        .date-box {
            display: table-cell;
            width: 50%;
            padding: 10px;
            text-align: center;
        }
        
        .date-box.issue {
            border-right: 2px solid #ddd;
        }
        
        .date-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            font-weight: bold;
        }
        
        .date-value {
            font-size: 16px;
            font-weight: bold;
            color: #0a2142;
        }
        
        .date-value.expiry {
            color: #c00;
        }
        
        /* Footer Section */
        .footer {
            position: absolute;
            bottom: 30px;
            left: 30px;
            right: 30px;
            padding-top: 20px;
            border-top: 2px solid #0a2142;
        }
        
        .footer-content {
            width: 100%;
            overflow: hidden;
        }
        
        .qr-block {
            float: left;
            width: 25%;
            text-align: center;
        }
        
        .qr-block img {
            border: 2px solid #0a2142;
            padding: 5px;
            background: white;
        }
        
        .qr-label {
            font-size: 8px;
            color: #666;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .signature-block {
            float: right;
            width: 70%;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 2px solid #0a2142;
            height: 60px;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .signature-img {
            max-height: 50px;
            max-width: 150px;
            margin-top: 10px;
        }
        
        .signature-title {
            font-size: 11px;
            font-weight: bold;
            color: #0a2142;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .signature-subtitle {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>LOUIS MARINE QATAR</h1>
            <p>Marine Services & Engineering</p>
        </div>

        <!-- Certificate Title -->
        <div class="title">
            <h2>{{ $certificate->type->name }}</h2>
            <div class="cert-number">Certificate No: CERT-{{ $certificate->certificate_number }}</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="intro-text">
                This is to certify that the vessel / equipment:
            </div>

            <!-- Vessel & Customer Details -->
            <div class="details-section">
                <table class="details-table">
                    <tr>
                        <td width="50%">
                            <div class="label">Vessel Name</div>
                            <div class="value">{{ $certificate->vessel->name ?? 'N/A' }}</div>
                        </td>
                        <td width="50%">
                            <div class="label">Owner / Customer</div>
                            <div class="value">{{ $certificate->customer->name }}</div>
                        </td>
                    </tr>
                    @if($certificate->vessel)
                    <tr>
                        <td>
                            <div class="label">IMO Number</div>
                            <div class="value">{{ $certificate->vessel->imo_number ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="label">Port of Registry</div>
                            <div class="value">{{ $certificate->vessel->port_of_registry ?? 'N/A' }}</div>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Certification Statement -->
            <div class="certification-text">
                Has been inspected/serviced in accordance with the standard requirements<br>
                and found to be in satisfactory condition.
            </div>

            <!-- Template Fields -->
             @if($certificate->certificateTemplate)
                @foreach($certificate->certificateTemplate->sections->where('is_repeatable', false) as $section)
                    <div class="equipment-section">
                        <div class="section-title">{{ $section->name }}</div>
                        
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 mb-2">
                            @foreach($section->fields as $field)
                                @php
                                    $val = $certificate->fieldValues->where('certificate_template_field_id', $field->id)->first();
                                @endphp
                                
                                @if($field->field_type === 'textarea' || $field->width === 'full')
                                    <div style="margin-bottom: 10px;">
                                        <div class="label">{{ $field->field_label }}</div>
                                        <div class="value" style="background: #f8f9fa; padding: 5px; font-size: 11px;">{{ $val?->value ?? '-' }}</div>
                                    </div>
                                @else
                                    <div style="margin-bottom: 5px;">
                                        <span class="label" style="display:inline-block; width: 40%;">{{ $field->field_label }}</span>
                                        <span class="value">{{ $val?->value ?? '-' }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                @foreach($certificate->certificateTemplate->sections->where('is_repeatable', true) as $section)
                    <div class="equipment-section">
                        <div class="section-title">{{ $section->name }}</div>
                        <table class="equipment-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    @foreach($section->fields as $field)
                                        <th>{{ $field->field_label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sectionFieldIds = $section->fields->pluck('id');
                                    $maxInstance = $certificate->fieldValues
                                        ->whereIn('certificate_template_field_id', $sectionFieldIds)
                                        ->max('section_instance') ?? 0;
                                @endphp
                                @for($i = 1; $i <= $maxInstance; $i++)
                                <tr class="repeatable-row">
                                    <td style="text-align: center;">{{ $i }}</td>
                                    @foreach($section->fields as $field)
                                        @php
                                            $val = $certificate->fieldValues
                                                ->where('certificate_template_field_id', $field->id)
                                                ->where('section_instance', $i)
                                                ->first();
                                        @endphp
                                        <td>{{ $val?->value ?? '-' }}</td>
                                    @endforeach
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @endif

            <!-- Equipment List (Legacy Fallback) -->
            @if($certificate->items && $certificate->items->count() > 0)
            <div class="equipment-section">
                <div class="section-title">Equipment List</div>
                <table class="equipment-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 25%;">Type / Serial</th>
                            <th style="width: 20%;">Manufacturer</th>
                            <th style="width: 25%;">Last Service / Next Due</th>
                            <th style="width: 15%;">Condition</th>
                            <th style="width: 10%;">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificate->items as $index => $item)
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                            <td>
                                <div class="equipment-type">{{ $item->equipment_type }}</div>
                                <div class="serial-number">SN: {{ $item->serial_number }}</div>
                            </td>
                            <td>
                                <div style="font-weight: bold;">{{ $item->manufacturer ?? '-' }}</div>
                                <div style="color: #666; font-size: 9px;">MFG: {{ $item->manufacture_date ? $item->manufacture_date->format('Y') : '-' }}</div>
                            </td>
                            <td>
                                <div style="margin-bottom: 3px;">
                                    <span style="color: #666;">Last:</span> 
                                    <strong>{{ $item->last_service_date ? $item->last_service_date->format('d/m/Y') : '-' }}</strong>
                                </div>
                                <div>
                                    <span style="color: #666;">Next:</span> 
                                    <strong>{{ $item->next_due_date ? $item->next_due_date->format('d/m/Y') : '-' }}</strong>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="{{ ($item->condition ?? 'good') === 'good' ? 'condition-good' : 'condition-repair' }}">
                                    {{ strtoupper($item->condition ?? 'GOOD') }}
                                </span>
                            </td>
                            <td style="font-size: 9px;">
                                @if(!empty($item->details))
                                    @foreach($item->details as $key => $val)
                                        @if($val)
                                        <div>{{ ucwords(str_replace('_', ' ', $key)) }}: {{ $val }}</div>
                                        @endif
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Dates -->
            <div class="dates-section">
                <div class="dates-container">
                    <div class="date-box issue">
                        <div class="date-label">Issue Date</div>
                        <div class="date-value">{{ $certificate->issue_date ? $certificate->issue_date->format('d F Y') : '-' }}</div>
                    </div>
                    <div class="date-box">
                        <div class="date-label">Expiry Date</div>
                        <div class="date-value expiry">{{ $certificate->expiry_date ? $certificate->expiry_date->format('d F Y') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <div class="qr-block" style="width: 20%; float: left;">
                    @if(isset($qrCodeBase64))
                        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" width="80" height="80" alt="QR Code">
                    @else
                        <div style="width: 80px; height: 80px; border: 2px solid #0a2142; margin: 0 auto; font-size: 8px; color: #999; padding-top: 35px;">QR CODE</div>
                    @endif
                    <div class="qr-label">Scan to Verify</div>
                </div>
                
                <!-- Created By (Left/Middle) -->
                <div class="signature-block" style="width: 35%; float: left; margin-left: 2%;">
                    <div class="signature-line">
                         <!-- Use digital signature if available, otherwise placeholder or blank -->
                        @if($certificate->creator && $certificate->creator->signature_path)
                             <img src="{{ public_path('storage/' . $certificate->creator->signature_path) }}" class="signature-img" alt="Creator Signature">
                        @else
                             <!-- <div style="height: 50px;"></div> -->
                        @endif
                    </div>
                    <div class="signature-title">Created By</div>
                    <div class="signature-subtitle">{{ $certificate->creator->name ?? 'Engineer' }}</div>
                </div>

                <!-- Verified By (Right) -->
                <div class="signature-block" style="width: 35%; float: right;">
                    <div class="signature-line">
                        @if($certificate->verifier && $certificate->verifier->signature_path && $certificate->verified_at)
                            <img src="{{ public_path('storage/' . $certificate->verifier->signature_path) }}" class="signature-img" alt="Verifier Signature">
                        @elseif($certificate->issued_by) <!-- Fallback to issuer if using old logic -->
                             <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e4/Signature_sample.svg/1200px-Signature_sample.svg.png" class="signature-img" alt="Signature">
                        @endif
                    </div>
                    <div class="signature-title">Verified & Authorized By</div>
                    <div class="signature-subtitle">{{ $certificate->verifier->name ?? ($certificate->issuedBy->name ?? 'Louis Marine Qatar') }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
