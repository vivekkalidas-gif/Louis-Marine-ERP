<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate Details</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; line-height: 1.3; color: #333; }
        
        @page { size: A4; margin: 15mm; }

        .pdf-page {
            min-height: 257mm;
            border: 2px solid #102a43;
            padding: 10mm;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
        }
        
        .header-compact {
            border-bottom: 2px solid #102a43;
            padding-bottom: 5px;
            margin-bottom: 15px;
            display: table;
            width: 100%;
        }
        .header-left { display: table-cell; width: 60%; vertical-align: bottom; }
        .header-right { display: table-cell; width: 40%; text-align: right; vertical-align: bottom; }
        
        .company-small { font-size: 14pt; font-weight: bold; color: #102a43; text-transform: uppercase; }
        .ref-text { font-size: 8pt; color: #666; font-weight: bold; }
        .cert-ref { font-size: 10pt; font-weight: bold; font-family: monospace; }

        .section-header {
            background-color: #102a43;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 0;
        }

        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .details-table th {
            background-color: #102a43;
            color: white;
            font-weight: bold;
            font-size: 8pt;
            padding: 6px;
            text-align: left;
            border: 1px solid #102a43;
        }
        .details-table td {
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 8pt;
        }
        .details-table tr:nth-child(even) { background-color: #f0f4f8; }

        .footer-sealed {
            position: absolute;
            bottom: 10mm;
            left: 10mm;
            right: 10mm;
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
            <div style="text-align: center; margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 5px;">
                <img src="{{ $headerPngPath }}" style="width: 100%; max-height: 120px; object-fit: contain;">
            </div>
        @else
            <!-- DEFAULT HTML HEADER -->
            <div class="header-compact">
                <div class="header-left">
                    <div class="company-small">LOUIS MARINE QATAR</div>
                    <div style="font-size: 9pt; font-weight: bold;">{{ $certificate->certificateTemplate->title ?? 'Certificate' }} - Continuation Sheet</div>
                </div>
                <div class="header-right">
                    <div class="ref-text">CERTIFICATE REF</div>
                    <div class="cert-ref">{{ $certificate->certificate_number }}</div>
                </div>
            </div>
        @endif

        @if($certificate->certificateTemplate)
            @foreach($certificate->certificateTemplate->sections as $section)
                @if($section->is_repeatable)
                    @php 
                        $instances = $certificate->fieldValues
                            ->where('definition.section_id',$section->id)
                            ->groupBy('section_instance');
                    @endphp
                    
                    @if($instances->count() > 0)
                    <div>
                        <div class="section-header">{{ $section->name }}</div>
                        
                        <table class="details-table">
                            <thead>
                                <tr>
                                    <th width="30">#</th>
                                    @foreach($section->fields as $field)
                                    <th>{{ $field->field_label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($instances as $instanceId => $fields)
                                <tr>
                                    <td align="center" style="color: #666;">{{ $instanceId }}</td>
                                    @foreach($section->fields as $field)
                                    @php
                                        $value = optional($fields->firstWhere('certificate_template_field_id',$field->id))->value;
                                        if ($field->field_type === 'date' && $value) {
                                            try {
                                                $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                                            } catch (\Exception $e) {}
                                        }
                                    @endphp
                                    <td>{{ $value ?? '-' }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                @endif
            @endforeach
        @endif

        <!-- FOOTER (CUSTOM PNG OR DEFAULT HTML) -->
        <div class="footer-sealed">
            @if($footerPngPath)
                <div style="text-align: center;">
                    <img src="{{ $footerPngPath }}" style="width: 100%; max-height: 120px; object-fit: contain;">
                </div>
            @else
                <div style="text-align: center; font-size: 7pt; color: #aaa; border-top: 1px solid #ddd; padding-top: 5px;">
                    Page 2 - Continuation Sheet - Certificate {{ $certificate->certificate_number }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
