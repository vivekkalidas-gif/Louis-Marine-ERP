<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $certificate->type?->name ?? $certificate->certificateTemplate?->name ?? 'Certificate' }} - {{ $certificate->certificate_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        /* UTILITIES */
        .print-compact { margin-bottom: 6px !important; }
        .print-compact p { margin-bottom: 3px !important; }
        .print-compact h1, .print-compact h2, .print-compact h3 { margin-bottom: 3px !important; }
        .print-tight { padding: 4px 8px !important; font-size: 8.5pt !important; }
        .no-print { display: block; }

        /* ===== SHARED A4 LAYOUT (WYSIWYG 1:1 MATCHING) ===== */
        .a4-preview {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .a4-inner {
            margin: 8mm;
            border: 2px solid #1e3a8a;
            padding: 6mm;
            display: flex;
            flex-direction: column;
            flex: 1;
            box-sizing: border-box;
        }

        .preview-content {
            flex: 1;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .preview-footer {
            margin-top: auto;
            padding-top: 8px;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        /* ===== PREVIEW MODE (SCREEN CSS) ===== */
        @media screen {
            body {
                margin: 0;
                padding: 0;
                background: #0f172a;
                font-family: Inter, Calibri, Arial, sans-serif;
                min-height: 100vh;
            }

            .preview-wrapper {
                width: 100%;
                min-height: calc(100vh - 64px);
                margin-top: 64px;
                padding: 30px 15px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 30px;
                box-sizing: border-box;
            }

            .a4-preview {
                box-shadow: 0 20px 40px -15px rgba(0,0,0,0.5);
                border-radius: 6px;
            }
        }

        /* ===== PRINT MODE ===== */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print, nav, .btn, x-modal, .fixed { 
                display: none !important; 
            }
            body > header, body > button { 
                display: none !important; 
            }

            .preview-wrapper {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .a4-preview {
                width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                margin: 0 auto !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                page-break-after: always;
                page-break-inside: avoid;
                overflow: hidden !important;
            }
            .a4-preview:last-child { 
                page-break-after: auto; 
            }

            .a4-inner {
                margin: 6mm !important;
                padding: 5mm !important;
                height: calc(297mm - 12mm) !important;
                border: 2px solid #1e3a8a !important;
            }
        }
    </style>
</head>
<body>

    @php
        $settings = \App\Models\Setting::getAllCached();
        $customHeaderUrl = !empty($settings['certificate_header_png']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings['certificate_header_png']) : null;
        $customFooterUrl = !empty($settings['certificate_footer_png']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($settings['certificate_footer_png']) : null;
    @endphp

    <!-- Actions Bar (No Print) -->
    <div class="no-print fixed top-0 left-0 right-0 bg-slate-900 border-b border-slate-800 shadow-xl px-6 py-3 flex justify-between items-center z-50">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.certificates.index') }}" class="btn-ghost text-xs text-slate-300 hover:text-white gap-1.5 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Certificates</span>
            </a>
            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider
                {{ $certificate->status == 'DRAFT' ? 'bg-slate-800 text-slate-300 border border-slate-700' : '' }}
                {{ $certificate->status == 'PENDING' ? 'bg-amber-950/80 text-amber-300 border border-amber-800/80' : '' }}
                {{ $certificate->status == 'ISSUED' ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-800/80' : '' }}
                {{ $certificate->status == 'REJECTED' ? 'bg-rose-950/80 text-rose-300 border border-rose-800/80' : '' }}">
                {{ $certificate->status }}
            </span>
            <span class="font-mono text-xs font-bold text-brand-400 hidden sm:inline-block">{{ $certificate->certificate_number }}</span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="btn-primary text-xs font-bold py-2 px-4 gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Certificate</span>
            </button>

            <a href="{{ route('admin.certificates.pdf', $certificate->id) }}" class="btn-secondary text-xs font-bold py-2 px-4 gap-1.5">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Download PDF</span>
            </a>

            <a href="{{ route('admin.certificates.edit', $certificate->id) }}" class="btn-secondary text-xs font-bold py-2 px-4 gap-1.5">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit</span>
            </a>

            @if($certificate->status === 'DRAFT')
                <form action="{{ route('admin.certificates.submit', $certificate->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-primary text-xs font-bold py-2 px-4 bg-indigo-600 hover:bg-indigo-700">
                        Submit for Approval
                    </button>
                </form>
            @endif

            @php
                $canApprove = false;
                $user = auth()->user();
                if ($certificate->status === 'PENDING') {
                    if ($user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('pro')) {
                        $canApprove = true;
                    } elseif ($user->hasRole('engineer') && $certificate->assigned_verifier_id === $user->id && $certificate->created_by !== $user->id) {
                        $canApprove = true;
                    }
                }
            @endphp

            @if($canApprove)
                <button x-data="" @click="$dispatch('open-modal', 'confirm-approve-modal')" class="btn-primary text-xs font-bold py-2 px-4 bg-emerald-600 hover:bg-emerald-700">
                    Approve
                </button>
                <button x-data="" @click="$dispatch('open-modal', 'confirm-reject-modal')" class="btn-primary text-xs font-bold py-2 px-4 bg-rose-600 hover:bg-rose-700">
                    Reject
                </button>
            @endif
        </div>
    </div>

    <!-- Approve & Reject Modals -->
    @if($canApprove)
    <x-modal name="confirm-approve-modal" focusable>
        <form method="POST" action="{{ route('admin.certificates.approve', $certificate->id) }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Approve Certificate</h2>
            <p class="text-xs text-slate-600 dark:text-slate-400">Are you sure you want to approve this certificate? It will be marked as ISSUED and assigned a permanent certificate number.</p>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" x-on:click="$dispatch('close')" class="btn-secondary text-xs">Cancel</button>
                <button type="submit" class="btn-primary text-xs bg-emerald-600 hover:bg-emerald-700">Approve & Issue</button>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-reject-modal" focusable>
        <form method="POST" action="{{ route('admin.certificates.reject', $certificate->id) }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Reject Certificate</h2>
            <p class="text-xs text-slate-600 dark:text-slate-400">Provide a reason for rejecting this certificate request:</p>
            <textarea name="rejection_reason" rows="3" class="theme-input text-xs" required placeholder="Reason for rejection..."></textarea>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" x-on:click="$dispatch('close')" class="btn-secondary text-xs">Cancel</button>
                <button type="submit" class="btn-primary text-xs bg-rose-600 hover:bg-rose-700">Reject Certificate</button>
            </div>
        </form>
    </x-modal>
    @endif

    <!-- PREVIEW CONTAINER -->
    <div class="preview-wrapper">
        
        <!-- PAGE 1: MAIN CERTIFICATE -->
        <div class="a4-preview">
            <div class="a4-inner">
                
                <div class="preview-content">
                    
                    <!-- HEADER (CUSTOM PNG OR DEFAULT HTML) -->
                    @if($customHeaderUrl)
                        <div class="mb-2 border-b border-gray-200 pb-1">
                            <img src="{{ $customHeaderUrl }}" alt="Certificate Header" class="w-full max-h-[95px] object-contain mx-auto">
                        </div>
                    @else
                        <!-- DEFAULT HTML HEADER -->
                        <header class="flex justify-between items-start border-b-2 border-blue-900 pb-2 mb-3">
                            <div class="w-3/5">
                                <h1 class="text-lg font-bold text-blue-900 leading-tight tracking-tight">LOUIS MARINE QATAR</h1>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Marine Services & Engineering W.L.L</p>
                            </div>
                            <div class="w-2/5 text-right">
                                <span class="block text-[10px] font-bold text-gray-500 uppercase">Certificate Number</span>
                                <span class="text-sm font-mono font-bold text-slate-900">{{ $certificate->certificate_number ?? 'DRAFT' }}</span>
                            </div>
                        </header>
                    @endif

                    <!-- TITLE -->
                    <div class="text-center mb-2.5">
                        <h2 class="text-base font-bold text-blue-900 uppercase underline underline-offset-4 tracking-wider">
                            {{ $certificate->type?->name ?? $certificate->certificateTemplate?->name ?? 'Certificate of Inspection' }}
                        </h2>
                    </div>
                    
                    <div class="mb-3 text-[11px] text-gray-700 leading-normal">
                        <p>This is to certify that the undernoted vessel/equipment has been inspected in accordance with the standard marine safety requirements.</p>
                    </div>

                    <!-- VESSEL PARTICULARS -->
                    <div class="mb-3">
                        <div class="bg-blue-900 text-white text-[11px] font-bold px-3 py-1 uppercase tracking-wider rounded-t">
                            Vessel Particulars
                        </div>
                        <table class="w-full text-[11px] border-l border-r border-b border-gray-300">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="w-1/4 py-1 px-3 bg-gray-50 font-semibold text-gray-700 border-r">Vessel Name</td>
                                    <td class="w-1/4 py-1 px-3 font-bold text-slate-900 border-r">{{ $certificate->vessel->name ?? 'N/A' }}</td>
                                    <td class="w-1/4 py-1 px-3 bg-gray-50 font-semibold text-gray-700 border-r">Owner / Customer</td>
                                    <td class="w-1/4 py-1 px-3 font-bold text-slate-900">{{ $certificate->customer->company_name ?? $certificate->customer->name ?? '-' }}</td>
                                </tr>
                                @if($certificate->vessel)
                                <tr>
                                    <td class="py-1 px-3 bg-gray-50 font-semibold text-gray-700 border-r">IMO Number</td>
                                    <td class="py-1 px-3 border-r font-mono">{{ $certificate->vessel->imo_number ?? '-' }}</td>
                                    <td class="py-1 px-3 bg-gray-50 font-semibold text-gray-700 border-r">Port of Registry</td>
                                    <td class="py-1 px-3">{{ $certificate->vessel->port_of_registry ?? '-' }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="py-1 px-3 bg-gray-50 font-semibold text-gray-700 border-r">Call Sign</td>
                                    <td class="py-1 px-3 border-r font-mono">{{ $certificate->vessel->call_sign ?? '-' }}</td>
                                    <td class="py-1 px-3 bg-gray-50 font-semibold text-gray-700 border-r">Job Number</td>
                                    <td class="py-1 px-3 font-bold font-mono text-brand-700">{{ $certificate->job_number ?? $certificate->job?->job_number ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- NON-REPEATABLE SECTIONS -->
                    @if($certificate->certificateTemplate)
                        @foreach($certificate->certificateTemplate->sections->where('is_repeatable', false) as $section)
                            <div class="mb-3">
                                <h3 class="font-bold text-[11px] text-blue-900 border-b border-blue-900 mb-1.5 uppercase pb-0.5">
                                    {{ $section->name }}
                                </h3>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 mb-1">
                                    @foreach($section->fields as $field)
                                        @php
                                            $val = $certificate->fieldValues->where('certificate_template_field_id', $field->id)->first();
                                        @endphp
                                        @if($field->field_type === 'textarea' || $field->width === 'full')
                                            <div class="col-span-2 border-b border-gray-100 py-1">
                                                <span class="block text-[9px] font-bold text-gray-500 uppercase mb-0.5">{{ $field->field_label }}</span>
                                                <div class="text-[11px] text-gray-900 leading-snug whitespace-pre-wrap">{{ $val?->value ?? '-' }}</div>
                                            </div>
                                        @else
                                            <div class="flex items-center border-b border-gray-100 py-1">
                                                <span class="w-1/2 text-[9px] font-semibold text-gray-500 uppercase">{{ $field->field_label }}</span>
                                                <span class="w-1/2 text-[11px] font-bold text-gray-900 pl-1">{{ $val?->value ?? '-' }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($certificate->remarks)
                    <div class="mb-3">
                        <h3 class="font-bold text-[11px] text-blue-900 border-b border-blue-900 mb-1 uppercase pb-0.5">Inspection Report & Notes</h3>
                        <div class="text-[11px] p-2 border border-gray-200 bg-gray-50 text-gray-800 leading-snug whitespace-pre-wrap rounded">
                            {{ $certificate->remarks }}
                        </div>
                    </div>
                    @endif

                    <!-- DATES SUMMARY STRIP -->
                    <div class="grid grid-cols-2 gap-3 my-3 border-t border-b border-gray-300 py-1.5">
                        <div class="text-center border-r border-gray-200">
                            <span class="block text-[9px] font-bold text-gray-500 uppercase mb-0.5">Date of Inspection / Issue</span>
                            <span class="text-sm font-bold text-blue-900">{{ $certificate->issue_date ? $certificate->issue_date->format('d F Y') : 'Pending' }}</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-[9px] font-bold text-gray-500 uppercase mb-0.5">Date of Expiry</span>
                            <span class="text-sm font-bold text-rose-700">{{ $certificate->expiry_date ? $certificate->expiry_date->format('d F Y') : 'N/A' }}</span>
                        </div>
                    </div>

                </div>

                <!-- FOOTER WITH SIGNATURES, QR CODE & OPTIONAL CUSTOM PNG OVERLAY -->
                <div class="preview-footer">
                    <div class="flex justify-between items-end gap-4 mb-2">
                        <!-- Verified By -->
                        <div class="w-1/3 text-center">
                             <div class="h-10 border-b border-gray-400 mb-1 relative flex items-end justify-center pb-0.5">
                                 <span class="text-sm font-serif italic text-gray-800">{{ $certificate->assignedVerifier->name ?? 'Service Engineer' }}</span>
                             </div>
                             <p class="text-[11px] font-bold text-blue-900 uppercase">Verified By</p>
                             <p class="text-[9px] text-gray-500">Louis Marine Qatar</p>
                        </div>

                        <!-- QR Code Verification Box -->
                        <div class="w-1/3 text-center flex flex-col items-center justify-end">
                            <div id="qrcode" class="mb-0.5 opacity-90 mx-auto flex justify-center"></div>
                            <p class="text-[8px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Scan to Verify Online</p>
                        </div>

                        <!-- Authorized By -->
                        <div class="w-1/3 text-center">
                            <div class="h-10 border-b border-gray-400 mb-1 relative flex items-end justify-center pb-0.5">
                                <span class="text-sm font-serif italic text-gray-800">{{ $certificate->issuedBy->name ?? 'Authorized Signatory' }}</span>
                            </div>
                            <p class="text-[11px] font-bold text-blue-900 uppercase">Authorized Signature</p>
                            <p class="text-[9px] text-gray-500">Louis Marine Qatar</p>
                        </div>
                    </div>

                    @if($customFooterUrl)
                        <div class="pt-1 border-t border-gray-200 text-center">
                            <img src="{{ $customFooterUrl }}" alt="Certificate Footer" class="w-full max-h-[65px] object-contain mx-auto">
                        </div>
                    @else
                        <div class="text-center mt-1 pt-1 border-t border-gray-200">
                            <p class="text-[8px] text-gray-400">
                                This certificate is computer generated and valid without a seal. Generated on {{ now()->format('d M Y H:i') }}
                            </p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- PAGE 2: REPEATABLE EQUIPMENT IDENTIFICATION & CONTINUATION SHEET -->
        @if($certificate->certificateTemplate && $certificate->certificateTemplate->sections->where('is_repeatable', true)->count() > 0)
        <div class="a4-preview">
            <div class="a4-inner">
                
                <div class="preview-content">
                    
                    <!-- HEADER (CUSTOM PNG OR DEFAULT HTML) -->
                    @if($customHeaderUrl)
                        <div class="mb-4 border-b border-gray-200 pb-2">
                            <img src="{{ $customHeaderUrl }}" alt="Certificate Header" class="w-full max-h-[140px] object-contain mx-auto">
                        </div>
                    @else
                        <!-- DEFAULT HTML HEADER -->
                        <header class="flex justify-between items-start border-b-2 border-blue-900 pb-3 mb-5">
                            <div class="w-3/5">
                                <h1 class="text-xl font-bold text-blue-900 leading-tight tracking-tight">LOUIS MARINE QATAR</h1>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">Certificate Equipment Identification - Continuation Sheet</p>
                            </div>
                            <div class="w-2/5 text-right">
                                <span class="block text-[10px] font-bold text-gray-500 uppercase">Certificate Ref</span>
                                <span class="text-base font-mono font-bold text-slate-900">{{ $certificate->certificate_number }}</span>
                            </div>
                        </header>
                    @endif

                    @foreach($certificate->certificateTemplate->sections->where('is_repeatable', true) as $section)
                        <div class="mb-6">
                            <h3 class="font-bold text-xs text-blue-900 border-b border-blue-900 mb-3 uppercase pb-1 flex items-center justify-between">
                                <span>{{ $section->name }}</span>
                                <span class="text-[9px] font-mono text-gray-400 font-normal">EQUIPMENT ITEMS TABLE</span>
                            </h3>
                            
                            <table class="w-full text-xs border border-gray-300">
                                <thead class="bg-gray-100 text-gray-700 font-bold border-b border-gray-300">
                                    <tr>
                                        <th class="py-2 px-3 border-r text-center w-10">#</th>
                                        @foreach($section->fields as $field)
                                            <th class="py-2 px-3 border-r text-left text-[10px] uppercase tracking-wider">{{ $field->field_label }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @php
                                        $sectionFieldIds = $section->fields->pluck('id');
                                        $maxInstance = $certificate->fieldValues
                                            ->whereIn('certificate_template_field_id', $sectionFieldIds)
                                            ->max('section_instance') ?? 0;
                                    @endphp
                                    @for($i = 1; $i <= $maxInstance; $i++)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-3 border-r text-center font-bold text-gray-500 text-[10px]">{{ $i }}</td>
                                        @foreach($section->fields as $field)
                                            @php
                                                $val = $certificate->fieldValues
                                                    ->where('certificate_template_field_id', $field->id)
                                                    ->where('section_instance', $i)
                                                    ->first();
                                            @endphp
                                            <td class="py-2 px-3 border-r text-xs text-gray-900 font-medium">{{ $val?->value ?? '-' }}</td>
                                        @endforeach
                                    </tr>
                                    @endfor
                                    @if($maxInstance == 0)
                                        <tr><td colspan="{{ $section->fields->count() + 1 }}" class="py-4 text-center text-gray-400 text-xs italic">No equipment items recorded</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endforeach

                </div>

                <!-- FOOTER (CUSTOM PNG OR DEFAULT HTML) -->
                @if($customFooterUrl)
                    <div class="preview-footer">
                        <div class="pt-2 border-t border-gray-200 text-center">
                            <img src="{{ $customFooterUrl }}" alt="Certificate Footer" class="w-full max-h-[140px] object-contain mx-auto">
                        </div>
                    </div>
                @else
                    <!-- DEFAULT HTML FOOTER -->
                    <div class="preview-footer">
                        <div class="text-center pt-2 border-t border-gray-200">
                            <p class="text-[9px] text-gray-400">
                                Page 2 - Equipment Identification Sheet - Certificate {{ $certificate->certificate_number }}
                            </p>
                        </div>
                    </div>
                @endif

            </div>
        </div>
        @endif

    </div>

    <script type="text/javascript">
        if (document.getElementById("qrcode")) {
            new QRCode(document.getElementById("qrcode"), {
                text: "{{ route('certificates.verify-public', $certificate->certificate_number ?? ('PREVIEW-' . $certificate->id)) }}",
                width: 55,
                height: 55,
                colorDark : "#1e3a8a",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });
        }
    </script>
</body>
</html>
