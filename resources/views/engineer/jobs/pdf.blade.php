@php
    $settings = \App\Models\Setting::getAllCached();
    $companyName = $settings['company_name'] ?? 'DIya Tech Marine';
    $companyAddress = $settings['company_address'] ?? '';
    $companyPhone = $settings['company_phone'] ?? '';
    $companyEmail = $settings['company_email'] ?? '';
    $companyWebsite = $settings['company_website'] ?? '';
    
    $logoPath = storage_path('app/public/' . ($settings['company_logo'] ?? ''));
    $logoBase64 = '';
    if (!empty($settings['company_logo']) && file_exists($logoPath)) {
        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
        $data = file_get_contents($logoPath);
        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Order - {{ $job->job_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 9px;
            color: #64748b;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            text-align: right;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            text-align: right;
            font-family: monospace;
        }
        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .meta-grid td {
            padding: 6px 10px;
            vertical-align: top;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
        }
        .meta-label {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .meta-value {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .section-header {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .data-table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-priority {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-status {
            background-color: #dcfce7;
            color: #166534;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 15px;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-top: 45px;
            padding-top: 5px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #475569;
        }
        .footer {
            position: fixed;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-height: 48px; width: auto; margin-bottom: 4px;"><br>
                @endif
                <div class="company-name">{{ $companyName }}</div>
                @if($companyAddress)
                    <div class="company-sub">{!! nl2br(e($companyAddress)) !!}</div>
                @endif
                <div class="company-sub">
                    @if($companyPhone) Tel: {{ $companyPhone }} @endif
                    @if($companyEmail) | Email: {{ $companyEmail }} @endif
                    @if($companyWebsite) | Web: {{ $companyWebsite }} @endif
                </div>
            </td>
            <td style="width: 40%;">
                <div class="doc-title">Field Service Job Order</div>
                <div class="doc-number">REF #: {{ $job->job_number }}</div>
                <div style="text-align: right; font-size: 9px; color: #64748b; margin-top: 2px;">
                    Date: {{ $job->created_at ? $job->created_at->format('d M Y') : date('d M Y') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Job Summary Grid -->
    <table class="meta-grid">
        <tr>
            <td style="width: 50%;">
                <div class="meta-box">
                    <div class="meta-label">Customer Company</div>
                    <div class="meta-value">{{ $job->customer?->company_name ?? 'N/A' }}</div>
                    <div style="font-size: 9px; color: #64748b; margin-top: 4px;">
                        Contact: {{ $job->requestedBy?->name ?? 'N/A' }} {{ !empty($job->customer?->phone) ? '('.$job->customer->phone.')' : '' }}
                    </div>
                    <div style="font-size: 9px; color: #64748b;">
                        Email: {{ $job->customer?->email ?? 'N/A' }}
                    </div>
                </div>
            </td>
            <td style="width: 50%;">
                <div class="meta-box">
                    <div class="meta-label">Vessel & Location Details</div>
                    <div class="meta-value">Vessel: {{ $job->vessel?->name ?? 'N/A' }} {{ !empty($job->vessel?->imo_number) ? '(IMO: '.$job->vessel->imo_number.')' : '' }}</div>
                    <div style="font-size: 9px; color: #64748b; margin-top: 4px;">
                        Location: {{ $job->location_details ?? 'On-Site / Offshore' }}
                    </div>
                    <div style="font-size: 9px; color: #64748b;">
                        PO / Ref #: {{ $job->reference_no ?? 'N/A' }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Dispatch Parameters -->
    <table class="meta-grid" style="margin-bottom: 10px;">
        <tr>
            <td style="width: 25%;">
                <div class="meta-box">
                    <div class="meta-label">Job Type</div>
                    <div class="meta-value">{{ $job->job_type ?? 'Field Service' }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="meta-box">
                    <div class="meta-label">Priority</div>
                    <div class="meta-value"><span class="badge badge-priority">{{ $job->priority ?? 'Normal' }}</span></div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="meta-box">
                    <div class="meta-label">Scheduled Date</div>
                    <div class="meta-value">{{ $job->scheduled_at ? \Carbon\Carbon::parse($job->scheduled_at)->format('d M Y H:i') : 'As Scheduled' }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="meta-box">
                    <div class="meta-label">Job Status</div>
                    <div class="meta-value"><span class="badge badge-status">{{ ucfirst($job->status ?? 'Requested') }}</span></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Assigned Team -->
    <div class="section-header">Assigned Field Personnel</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35%;">Lead Engineer</th>
                <th style="width: 65%;">Assigned Technicians</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $job->engineer->name ?? 'Unassigned' }}</strong></td>
                <td>
                    @if($job->assignments && $job->assignments->count() > 0)
                        {{ $job->assignments->map(fn($a) => $a->user->name ?? '')->filter()->join(', ') }}
                    @else
                        <span>Standard Marine Technician Team</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Equipment Schedule / Scope of Work -->
    <div class="section-header">Equipment Inspection & Service Scope</div>
    @if($job->equipment && $job->equipment->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 52%;">Equipment Description / Type</th>
                    <th style="width: 20%;">Model / Serial No</th>
                    <th style="width: 20%; text-align: center;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($job->equipment as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $item->equipmentItem->name ?? 'Equipment Item' }}</strong></td>
                        <td>{{ $item->equipmentItem->model ?? $item->equipmentItem->code ?? '-' }}</td>
                        <td style="text-align: center;"><strong>{{ $item->quantity }}</strong> {{ $item->equipmentItem->unit ?? 'Unit' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="padding: 10px; background-color: #f8fafc; border: 1px solid #e2e8f0; font-size: 10px; color: #64748b;">
            Scope of Work: {{ $job->scope_of_work ?? 'Standard safety inspection & calibration service.' }}
        </div>
    @endif

    @if($job->special_instructions)
        <div style="margin-top: 10px; padding: 8px 10px; background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 4px; font-size: 9.5px; color: #92400e;">
            <strong>Special Instructions:</strong> {{ $job->special_instructions }}
        </div>
    @endif

    <!-- Material Requirements if any -->
    @if($job->materials && $job->materials->count() > 0)
        <div class="section-header">Required Materials & Spare Parts</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 70%;">Material Description</th>
                    <th style="width: 20%; text-align: center;">Required Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($job->materials as $mIndex => $mat)
                    <tr>
                        <td>{{ $mIndex + 1 }}</td>
                        <td>{{ $mat->item_name ?? $mat->name }}</td>
                        <td style="text-align: center;">{{ $mat->quantity ?? $mat->qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line">
                    Lead Service Engineer Signature<br>
                    <span style="font-size: 8px; font-weight: normal; color: #94a3b8;">{{ $companyName }}</span>
                </div>
            </td>
            <td>
                <div class="signature-line">
                    Customer Representative Signature & Stamp<br>
                    <span style="font-size: 8px; font-weight: normal; color: #94a3b8;">Vessel / Client Acceptance</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        {{ $companyName }} ERP | Official Field Service Job Order | Generated on {{ date('d M Y H:i') }} | Page 1 of 1
    </div>

</body>
</html>
