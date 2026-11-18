<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Service Report - {{ $report->job->job_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #333; line-height: 1.4; }
        
        /* Theme Colors */
        :root {
            --primary-blue: #003366;
            --accent-red: #cc0000;
            --light-gray: #f5f5f5;
        }

        .header { width: 100%; border-bottom: 3px solid #cc0000; padding-bottom: 15px; margin-bottom: 20px; }
        .logo { font-size: 26px; font-weight: bold; color: #003366; text-transform: uppercase; }
        .report-title { font-size: 18px; color: #cc0000; font-weight: bold; text-align: right; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        
        .meta-table td { padding-bottom: 5px; }
        .label { font-weight: bold; color: #003366; font-size: 11px; text-transform: uppercase; }
        
        .section { margin-top: 25px; margin-bottom: 10px; }
        .section-header { 
            background-color: #003366; 
            color: white; 
            padding: 5px 10px; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .content-table { width: 100%; border: 1px solid #ddd; }
        .content-table th { 
            background-color: #f0f4f8; 
            color: #003366; 
            font-weight: bold; 
            text-align: left; 
            padding: 8px; 
            border-bottom: 1px solid #003366;
            font-size: 11px;
            text-transform: uppercase;
        }
        .content-table td { 
            padding: 8px; 
            border-bottom: 1px solid #eee; 
            color: #444;
        }
        .content-table tr:last-child td { border-bottom: none; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .footer { 
            position: fixed; 
            bottom: 0px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            border-top: 1px solid #cc0000;
            padding-top: 5px;
            text-align: center; 
            font-size: 10px; 
            color: #666; 
        }
    </style>
</head>
@php
    $settings = \App\Models\Setting::getAllCached();
    $companyName = $settings['company_name'] ?? 'DIya Tech Marine';
    $companyAddress = $settings['company_address'] ?? '';
    $companyPhone = $settings['company_phone'] ?? '';
    $companyEmail = $settings['company_email'] ?? '';
    
    $logoPath = storage_path('app/public/' . ($settings['company_logo'] ?? ''));
    $logoBase64 = '';
    if (!empty($settings['company_logo']) && file_exists($logoPath)) {
        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
        $data = file_get_contents($logoPath);
        $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
@endphp
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" style="max-height: 48px; width: auto; margin-bottom: 4px;"><br>
                    @endif
                    <div class="logo">{{ $companyName }}</div>
                    <div style="font-size: 11px; color: #666; margin-top: 4px;">
                        @if($companyAddress) {!! nl2br(e($companyAddress)) !!}<br> @endif
                        @if($companyPhone) Tel: {{ $companyPhone }} @endif
                        @if($companyEmail) | Email: {{ $companyEmail }} @endif
                    </div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <div class="report-title">Service Report</div>
                    <div style="margin-top: 5px; font-size: 12px;">
                        <strong>Job No:</strong> {{ $report->job->job_number }}<br>
                        <strong>Date:</strong> {{ $report->report_date->format('d M, Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Client & Vessel Details -->
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 48%; border: 1px solid #ddd; padding: 10px;">
                <div class="label" style="border-bottom: 1px solid #eee; margin-bottom: 8px; padding-bottom: 4px;">Customer Details</div>
                <strong>{{ $report->job?->customer?->company_name ?? 'N/A' }}</strong><br>
                {{ $report->job?->customer?->address ?? '' }}<br>
                {{ $report->job?->customer?->email ?? '' }}<br>
                {{ $report->job?->customer?->phone ?? '' }}
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; border: 1px solid #ddd; padding: 10px;">
                <div class="label" style="border-bottom: 1px solid #eee; margin-bottom: 8px; padding-bottom: 4px;">Vessel / Site Details</div>
                <strong>{{ $report->job?->vessel?->name ?? 'N/A' }}</strong><br>
                @if(!empty($report->job?->vessel?->imo_number))
                IMO: {{ $report->job->vessel->imo_number }}<br>
                @endif
                Location: {{ $report->job->location_details ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <!-- Services Performed -->
    <div class="section-header">Services & Activities</div>
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 70%;">Service / Activity Performed</th>
                <th style="width: 20%;" class="text-right">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->items as $item)
            <tr>
                <td>
                    <strong style="color: #003366;">{{ $item->serviceType->name }}</strong>
                    @if($item->description)
                    <div style="font-size: 12px; color: #555; margin-top: 3px;">{{ $item->description }}</div>
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Work Description -->
    <div class="section" style="margin-top: 20px;">
        <div class="section-header">Work Description</div>
        <div style="border: 1px solid #ddd; padding: 10px; background-color: #fafafa; min-height: 80px;">
            <p style="margin: 0; white-space: pre-wrap;">{{ $report->work_description }}</p>
        </div>
    </div>

    <!-- Recommendations -->
    @if($report->recommendations)
    <div class="section">
        <div class="section-header" style="background-color: #cc0000;">Recommendations / Remarks</div>
        <div style="border: 1px solid #ddd; padding: 10px; background-color: #fff5f5;">
            <p style="margin: 0; white-space: pre-wrap; color: #333;">{{ $report->recommendations }}</p>
        </div>
    </div>
    @endif

    <!-- Materials Used -->
    @if($report->charges->count() > 0)
    <div class="section-header" style="margin-top: 20px;">Materials / Spares Consumed</div>
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 80%;">Item Description</th>
                <th style="width: 20%;" class="text-center">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->charges as $charge)
            <tr>
                <td>{{ $charge->item_description }}</td>
                <td class="text-center">{{ $charge->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Summary / Signatures -->
    <table style="width: 100%; margin-top: 40px;">
        <tr>
            <td style="width: 60%; vertical-align: bottom;">
                @if($report->hours_worked > 0)
                <div style="font-size: 12px; color: #666;">
                    <strong>Work Duration:</strong> {{ $report->hours_worked }} Hours
                </div>
                @endif
            </td>
            <td style="width: 40%;">
                <div style="border-top: 1px solid #000; padding-top: 5px; text-align: center; font-weight: bold; font-size: 12px;">
                    Engineer Signature
                </div>
                <div style="text-align: center; font-size: 11px; margin-top: 3px;">
                    {{ $report->engineer->name ?? 'Engineer' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Generated by {{ config('app.name') }} System | Page <span class="page"></span>
    </div>
</body>
</html>
