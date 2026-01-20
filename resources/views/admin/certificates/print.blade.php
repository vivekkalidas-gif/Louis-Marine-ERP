<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->certificateTemplate->title ?? 'Certificate' }}</title>

    <style>
        /* RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #333;
        }

        /* PAGE SETUP */
        @page {
            size: A4;
            margin: 20mm;
        }

        /* A4 USABLE HEIGHT = 297 - 40 = 257mm */
        .pdf-page {
            width: 100%;
            height: 257mm;
            position: relative;
            page-break-after: always;
            overflow: hidden;
        }

        /* PAGE 1 (SEALED) */
        .page-1 {
            padding-bottom: 130px; /* footer space */
        }

        /* PAGE 2+ (TABLE FLOW) */
        .page-2 {
            /* SAME FIXED HEIGHT — DO NOT USE AUTO */
        }

        /* HEADER */
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
        }

        .cert-title {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 5px;
        }

        /* INFO TABLE */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 4px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            width: 30%;
            color: #555;
        }

        .value {
            border-bottom: 1px dotted #ccc;
        }

        /* SECTIONS */
        .section {
            margin-bottom: 15px;
        }

        .section-header {
            background: #eee;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 11pt;
            border-left: 3px solid #2563eb;
            margin-bottom: 10px;
        }

        .field-grid {
            display: table;
            width: 100%;
        }

        .field-row {
            display: table-row;
        }

        .field-cell {
            display: table-cell;
            width: 50%;
            padding: 4px;
        }

        .field-label {
            font-weight: bold;
            font-size: 9pt;
        }

        .field-value {
            font-size: 10pt;
        }

        /* CYLINDER TABLE */
        .cylinder-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cylinder-table th,
        .cylinder-table td {
            border: 1px solid #ccc;
            padding: 6px;
            font-size: 9pt;
        }

        .cylinder-table th {
            background: #f3f4f6;
        }

        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }

        /* FOOTER (PAGE 1 ONLY) */
        .footer-sealed {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 120px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>

<!-- ================= PAGE 1 ================= -->
<div class="pdf-page page-1">

    <div class="header">
        <div class="company-name">LOUIS MARINE QATAR</div>
        <div class="cert-title">{{ $certificate->certificateTemplate->title ?? 'Certificate' }}</div>
        <div>Certificate No: {{ $certificate->certificate_number }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Vessel:</td>
            <td class="value">{{ $certificate->vessel->name ?? '-' }}</td>
            <td class="label">Issue Date:</td>
            <td class="value">{{ $certificate->issue_date->format('d-M-Y') }}</td>
        </tr>
        <tr>
            <td class="label">Customer:</td>
            <td class="value">{{ $certificate->customer->company_name }}</td>
            <td class="label">Expiry Date:</td>
            <td class="value">{{ $certificate->expiry_date ? $certificate->expiry_date->format('d-M-Y') : 'N/A' }}</td>
        </tr>
    </table>

    @foreach($certificate->certificateTemplate->sections as $section)
        @if(!$section->is_repeatable)
        <div class="section">
            <div class="section-header">{{ $section->name }}</div>
            <div class="field-grid">
                @foreach($section->fields->chunk(2) as $chunk)
                <div class="field-row">
                    @foreach($chunk as $field)
                    @php $val = $certificate->fieldValues->firstWhere('field_id', $field->id); @endphp
                    <div class="field-cell">
                        <div class="field-label">{{ $field->field_label }}</div>
                        <div class="field-value">{{ $val->field_value ?? '-' }}</div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach

    <div class="footer-sealed">
        <table width="100%">
            <tr>
                <td width="30%" align="center">
                    @if(isset($qrCodeBase64))
                    <img src="data:image/png;base64,{{ $qrCodeBase64 }}" width="80">
                    @endif
                    <div style="font-size:8pt">Scan to Verify</div>
                </td>
                <td width="40%" align="center" style="font-size:8pt;color:#777">
                    generated by LOUIS MARINE QATAR<br>www.louismarine.com
                </td>
                <td width="30%" align="center">
                    <div style="border-bottom:1px solid #000;height:50px;margin-bottom:5px"></div>
                    <strong>{{ $certificate->issuedBy->name }}</strong><br>
                    <span style="font-size:9pt">{{ $certificate->issuedBy->role }}</span>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- ================= PAGE 2+ ================= -->
<div class="pdf-page page-2">

    <div class="header">
        <div class="company-name">LOUIS MARINE QATAR</div>
        <div class="cert-title">{{ $certificate->certificateTemplate->title }}</div>
        <div>Ref: {{ $certificate->certificate_number }}</div>
    </div>

    <div class="section-header">CYLINDER DETAILS</div>

    @foreach($certificate->certificateTemplate->sections as $section)
        @if($section->is_repeatable)
        <table class="cylinder-table">
            <thead>
                <tr>
                    @foreach($section->fields as $field)
                    <th>{{ $field->field_label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach(
                  $certificate->fieldValues
                  ->where('field.section_id',$section->id)
                  ->groupBy('section_instance') as $row
                )
                <tr>
                    @foreach($section->fields as $field)
                    <td>{{ optional($row->firstWhere('field_id',$field->id))->field_value ?? '-' }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endforeach

</div>

</body>
</html>
