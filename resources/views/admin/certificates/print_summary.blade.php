<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate Summary</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; line-height: 1.4; color: #102a43; }
        
        @page { size: A4; margin: 10mm; } /* Reduced page margin */
        
        .pdf-page {
            min-height: 277mm; /* 297mm - 20mm margins */
            border: 2px solid #102a43;
            padding: 10mm; /* Reduced internal padding */
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }

        /* UTILS */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .blue-text { color: #102a43; }
        .bg-gray { background-color: #f0f4f8; }
        .bg-blue { background-color: #102a43; color: white; }

        /* HEADER */
        .header-table { width: 100%; margin-bottom: 10px; border-bottom: 2px solid #102a43; padding-bottom: 5px; } /* Compact header */
        .company-name { font-size: 16pt; font-weight: bold; color: #102a43; text-transform: uppercase; letter-spacing: 1px; }
        .sub-title { font-size: 8pt; color: #666; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .cert-number { font-size: 11pt; font-weight: bold; font-family: monospace; }

        /* TITLE */
        .doc-title { 
            text-align: center; 
            font-size: 12pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin: 5px 0 5px 0; 
            color: #102a43;
            text-decoration: underline;
        }
        .certify-text {
            text-align: center;
            font-size: 9pt;
            font-style: italic;
            margin-bottom: 10px;
            color: #555;
        }

        /* DATA TABLES */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 5px; 
            font-size: 8pt;
            table-layout: fixed; 
        }
        table.data-table th, table.data-table td {
            border: 0.5pt solid #cbd5e0;
            padding: 2px 4px; /* Reduced cell padding */
            vertical-align: top;
        }
        
        /* SECTION TABLES */
        .section-title {
            background-color: #102a43;
            color: white;
            padding: 2px 5px;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            margin-bottom: 0;
            border-radius: 2px 2px 0 0;
            margin-top: 5px;
        }
        .report-box {
            border: 1px solid #cbd5e0;
            padding: 5px;
            min-height: 20px;
            font-size: 8pt;
            text-align: justify;
            background-color: #fff;
            margin-bottom: 5px;
        }
        
        /* DATES TABLE UTILS */
        .date-cell { padding: 4px !important; }
        .date-label { font-size: 7pt; font-weight: bold; color: #555; text-transform: uppercase; }
        .date-value { font-size: 9pt; font-weight: bold; color: #102a43; }

        /* FOOTER */
        .footer-sealed {
            position: absolute;
            bottom: 5mm; /* Adjusted for new padding */
            left: 0; right: 0;
            height: 120px; /* Reduced height */
            padding-top: 5px;
        }
        .signature-line {
            border-top: 1px solid #102a43;
            width: 80%;
            margin: 0 auto;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="pdf-page">
        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td width="60%">
                    <div class="company-name">LOUIS MARINE QATAR</div>
                    <div class="sub-title">Marine Services & Engineering</div>
                </td>
                <td width="40%" class="text-right">
                    <div style="font-size: 8pt; color: #555; margin-bottom: 2px;">CERTIFICATE NUMBER</div>
                    <div class="cert-number">{{ $certificate->certificate_number }}</div>
                </td>
            </tr>
        </table>

        <div class="doc-title">{{ $certificate->certificateTemplate->title ?? 'CERTIFICATE OF INSPECTION' }}</div>
        <div class="certify-text">This is to certify that the undernoted vessel/equipment has been inspected in accordance with the standard requirements.</div>

        <!-- VESSEL & JOB INFO -->
        <table class="data-table">
            <tr>
                <th>Vessel Name</th>
                <td>{{ $certificate->vessel->name ?? 'N/A' }}</td>
                <th>Owner / Customer</th>
                <td>{{ $certificate->customer->company_name ?? $certificate->customer->name }}</td>
            </tr>
            @if($certificate->vessel)
            <tr>
                <th>IMO Number</th>
                <td>{{ $certificate->vessel->imo_number ?? '-' }}</td>
                <th>Port of Registry</th>
                <td>{{ $certificate->vessel->port_of_registry ?? '-' }}</td>
            </tr>
            <tr>
                <th>Call Sign</th>
                <td>{{ $certificate->vessel->call_sign ?? '-' }}</td>
                <th>Job Number</th>
                <td>{{ $certificate->job_number ?? $certificate->job?->job_number ?? '-' }}</td>
            </tr>
            @else
            <tr>
                <th>Job Number</th>
                <td colspan="3">{{ $certificate->job_number ?? $certificate->job?->job_number ?? '-' }}</td>
            </tr>
            @endif
        </table>

        <!-- SECTIONS (Single Page Content) -->
        @if($certificate->certificateTemplate)
            @foreach($certificate->certificateTemplate->sections as $section)
                @if(!$section->is_repeatable)
                    
                    <div class="section-title">{{ $section->name }}</div>
                    
                    <!-- Report Text / Description -->
                    @if($section->description)
                    <div class="report-box">
                        {{ $section->description }}
                    </div>
                    @endif

                    <!-- Fields Table -->
                    @if($section->fields->count() > 0)
                    <table class="data-table" style="margin-top: -1px;">
                        <colgroup>
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                        </colgroup>
                        @foreach($section->fields->chunk(2) as $chunk)
                        <tr>
                            @foreach($chunk as $field)
                                @php 
                                    $val = $certificate->fieldValues->firstWhere('certificate_template_field_id', $field->id); 
                                    $displayValue = $val->value ?? '-';
                                    if ($field->field_type === 'date' && $val && $val->value) {
                                        try {
                                            $displayValue = \Carbon\Carbon::parse($val->value)->format('d-m-Y');
                                        } catch (\Exception $e) {}
                                    }
                                @endphp
                                @if($field->field_type === 'textarea' || $field->width === 'full')
                                    <th width="25%">{{ $field->field_label }}</th>
                                    <td colspan="3" width="75%">{{ $displayValue }}</td>
                                @else
                                    <th width="25%">{{ $field->field_label }}</th>
                                    <td width="25%">{{ $displayValue }}</td>
                                @endif
                            @endforeach
                            <!-- Fill empty cell if odd number of fields AND last field is not full width -->
                            @php $lastField = $chunk->last(); @endphp
                            @if($chunk->count() % 2 != 0 && !($lastField->field_type === 'textarea' || $lastField->width === 'full'))
                                <th width="25%"></th><td width="25%"></td>
                            @endif
                        </tr>
                        @endforeach
                    </table>
                    @endif
                @endif
            @endforeach
        @endif

        <!-- DATES -->
        <table class="data-table" style="border: 2px solid #102a43; margin-top: 15px;">
            <tr style="background-color: #f0f4f8;">
                <td class="text-center date-cell" width="50%">
                    <div class="date-label">Date of Issue</div>
                    <div class="date-value">{{ $certificate->issue_date->format('d F Y') }}</div>
                </td>
                <td class="text-center date-cell" width="50%" style="border-left: 2px solid #102a43;">
                    <div class="date-label">Date of Expiry</div>
                    <div class="date-value" style="color: #c53030;">{{ $certificate->expiry_date ? $certificate->expiry_date->format('d F Y') : 'N/A' }}</div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <div class="footer-sealed">
            <!-- Legacy Footer Image or Standard Layout -->
            <table width="100%">
                <tr>
                    <!-- VERIFIED BY -->
                    <td width="33%" align="center" style="vertical-align: bottom;">
                        <div class="signature-line"></div>
                        <div style="font-size: 11pt; font-weight: bold; color: #102a43; margin-bottom: 2px;">{{ $certificate->assignedVerifier->name ?? 'Verifier' }}</div>
                        <div style="font-size: 8pt; color: #666;">Verifying Engineer</div>
                        <div style="font-size: 7pt; color: #888;">LOUIS MARINE QATAR</div>
                    </td>
                    
                    <!-- QR CODE -->
                    <td width="34%" align="center" style="vertical-align: bottom;">
                        @if(isset($qrCodeBase64))
                        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" width="80" style="margin-bottom: 5px;">
                        @endif
                        <div style="font-size:7pt; color:#666;">SCAN TO VERIFY</div>
                    </td>

                    <!-- AUTHORIZED BY -->
                    <td width="33%" align="center" style="vertical-align: bottom;">
                        <div class="signature-line"></div>
                        <div style="font-size: 11pt; font-weight: bold; color: #102a43; margin-bottom: 2px;">{{ $certificate->issuedBy->name ?? 'Authorized Signatory' }}</div>
                        <div style="font-size: 8pt; color: #666;">{{ $certificate->issuedBy->role ?? 'Technical Manager' }}</div>
                        <div style="font-size: 7pt; color: #888;">LOUIS MARINE QATAR</div>
                    </td>
                </tr>
            </table>
            
            <div style="text-align: center; font-size: 7pt; color: #aaa; margin-top: 15px; border-top: 1px solid #ddd; padding-top: 5px;">
                This document is electronically generated and valid without a seal.
            </div>
        </div>
    </div>
</body>
</html>
