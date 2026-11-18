<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate Summary</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; line-height: 1.4; color: #102a43; }
        
        @page { size: A4; margin: 10mm; }
        
        .pdf-page {
            min-height: 277mm;
            border: 2px solid #102a43;
            padding: 10mm;
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
        .header-table { width: 100%; margin-bottom: 10px; border-bottom: 2px solid #102a43; padding-bottom: 5px; }
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
            padding: 2px 4px;
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
            font-size: 8pt;
            background-color: #f8fafc;
            margin-bottom: 5px;
            min-height: 25px;
        }
        
        .footer-sealed {
            position: absolute;
            bottom: 10mm;
            left: 10mm;
            right: 10mm;
        }
        .signature-line {
            border-bottom: 1px solid #102a43;
            height: 30px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    @php
        $settings = \App\Models\Setting::getAllCached();
        $headerPngPath = !empty($settings['certificate_header_png']) && file_exists(storage_path('app/public/' . $settings['certificate_header_png'])) 
            ? storage_path('app/public/' . $settings['certificate_header_png']) 
            : null;
        $footerPngPath = !empty($settings['certificate_footer_png']) && file_exists(storage_path('app/public/' . $settings['certificate_footer_png'])) 
            ? storage_path('app/public/' . $settings['certificate_footer_png']) 
            : null;
    @endphp

    <div class="pdf-page">
        <!-- HEADER (CUSTOM PNG OR DEFAULT HTML) -->
        @if($headerPngPath)
            <div style="text-align: center; margin-bottom: 10px; border-bottom: 1px solid #cbd5e0; padding-bottom: 5px;">
                <img src="{{ $headerPngPath }}" style="width: 100%; max-height: 120px; object-fit: contain;">
            </div>
        @else
            <!-- DEFAULT HTML HEADER -->
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
        @endif

        <div class="doc-title">{{ $certificate->certificateTemplate->title ?? 'CERTIFICATE OF INSPECTION' }}</div>
        <div class="certify-text">This is to certify that the undernoted vessel/equipment has been inspected in accordance with the standard requirements.</div>

        <!-- VESSEL & JOB INFO -->
        <table class="data-table">
            <tr>
                <th width="20%" class="bg-gray font-bold">Vessel Name</th>
                <td width="30%" class="font-bold">{{ $certificate->vessel->name ?? 'N/A' }}</td>
                <th width="20%" class="bg-gray font-bold">Owner / Customer</th>
                <td width="30%">{{ $certificate->customer->company_name ?? $certificate->customer->name }}</td>
            </tr>
            @if($certificate->vessel)
            <tr>
                <th class="bg-gray font-bold">IMO Number</th>
                <td>{{ $certificate->vessel->imo_number ?? '-' }}</td>
                <th class="bg-gray font-bold">Port of Registry</th>
                <td>{{ $certificate->vessel->port_of_registry ?? '-' }}</td>
            </tr>
            <tr>
                <th class="bg-gray font-bold">Call Sign</th>
                <td>{{ $certificate->vessel->call_sign ?? '-' }}</td>
                <th class="bg-gray font-bold">Job Number</th>
                <td class="font-bold">{{ $certificate->job_number ?? $certificate->job?->job_number ?? '-' }}</td>
            </tr>
            @endif
        </table>

        <!-- DYNAMIC NON-REPEATABLE SECTIONS -->
        @if($certificate->certificateTemplate)
            @foreach($certificate->certificateTemplate->sections->where('is_repeatable', false) as $section)
                <div class="section-title">{{ $section->name }}</div>
                
                @if($section->name === 'Inspection Report')
                     @php
                        $repField = $section->fields->first();
                        $repVal = $certificate->fieldValues->where('certificate_template_field_id', $repField->id ?? 0)->first();
                     @endphp
                     <div class="report-box">
                         {{ $repVal?->value ?? ($certificate->remarks ?? 'No inspection report provided.') }}
                     </div>
                @else
                    <table class="data-table">
                        @foreach($section->fields->chunk(2) as $chunk)
                            <tr>
                                @foreach($chunk as $field)
                                    @php
                                        $val = $certificate->fieldValues->where('certificate_template_field_id', $field->id)->first();
                                    @endphp
                                    <th width="20%" class="bg-gray">{{ $field->field_label }}</th>
                                    <td width="30%">{{ $val?->value ?? '-' }}</td>
                                @endforeach
                                @if($chunk->count() == 1)
                                    <th width="20%" class="bg-gray"></th>
                                    <td width="30%"></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @endif
            @endforeach
        @endif

        <!-- DATES -->
        <table class="data-table" style="border: 2px solid #102a43; margin-top: 15px;">
            <tr style="background-color: #f0f4f8;">
                <td class="text-center date-cell" width="50%">
                    <div class="date-label" style="font-size: 8pt; font-weight: bold; color: #555;">Date of Issue</div>
                    <div class="date-value" style="font-size: 10pt; font-weight: bold; color: #102a43;">{{ $certificate->issue_date ? $certificate->issue_date->format('d F Y') : '-' }}</div>
                </td>
                <td class="text-center date-cell" width="50%" style="border-left: 2px solid #102a43;">
                    <div class="date-label" style="font-size: 8pt; font-weight: bold; color: #555;">Date of Expiry</div>
                    <div class="date-value" style="font-size: 10pt; font-weight: bold; color: #c53030;">{{ $certificate->expiry_date ? $certificate->expiry_date->format('d F Y') : 'N/A' }}</div>
                </td>
            </tr>
        </table>

        <!-- FOOTER WITH SIGNATURES, QR CODE & OPTIONAL CUSTOM PNG OVERLAY -->
        <div class="footer-sealed">
            <table width="100%" style="margin-bottom: 10px;">
                <tr>
                    <!-- VERIFIED BY -->
                    <td width="33%" align="center" style="vertical-align: bottom;">
                        <div class="signature-line"></div>
                        <div style="font-size: 10pt; font-weight: bold; color: #102a43; margin-bottom: 2px;">{{ $certificate->assignedVerifier->name ?? 'Service Engineer' }}</div>
                        <div style="font-size: 8pt; color: #666;">Verifying Engineer</div>
                        <div style="font-size: 7pt; color: #888;">LOUIS MARINE QATAR</div>
                    </td>
                    
                    <!-- QR CODE -->
                    <td width="34%" align="center" style="vertical-align: bottom;">
                        @if(isset($qrCodeBase64))
                        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" width="70" style="margin-bottom: 3px;">
                        @endif
                        <div style="font-size: 7pt; font-weight: bold; color: #666; letter-spacing: 0.5px;">SCAN TO VERIFY ONLINE</div>
                    </td>

                    <!-- AUTHORIZED BY -->
                    <td width="33%" align="center" style="vertical-align: bottom;">
                        <div class="signature-line"></div>
                        <div style="font-size: 10pt; font-weight: bold; color: #102a43; margin-bottom: 2px;">{{ $certificate->issuedBy->name ?? 'Authorized Signatory' }}</div>
                        <div style="font-size: 8pt; color: #666;">Technical Manager</div>
                        <div style="font-size: 7pt; color: #888;">LOUIS MARINE QATAR</div>
                    </td>
                </tr>
            </table>

            @if($footerPngPath)
                <div style="text-align: center; margin-top: 5px;">
                    <img src="{{ $footerPngPath }}" style="width: 100%; max-height: 90px; object-fit: contain;">
                </div>
            @else
                <div style="text-align: center; font-size: 7pt; color: #aaa; margin-top: 10px; border-top: 1px solid #ddd; padding-top: 5px;">
                    This document is electronically generated and valid without a seal.
                </div>
            @endif
        </div>
    </div>
</body>
</html>
