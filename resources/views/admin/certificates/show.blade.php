<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $certificate->type?->name ?? $certificate->certificateTemplate?->name ?? 'Certificate' }} - {{ $certificate->certificate_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        /* UTILITIES (Compact & Hide) */
        .print-compact { margin-bottom: 2px !important; }
        .print-compact p { margin-bottom: 2px !important; }
        .print-compact h1, .print-compact h2, .print-compact h3 { margin-bottom: 2px !important; }
        .print-tight { padding: 2px 4px !important; font-size: 8pt !important; }
        .no-print { display: block; }

        /* ===== PREVIEW MODE (SCREEN CSS from User Request) ===== */
        @media screen {
            body {
                margin: 0;
                padding: 0;
                background: #f3f4f6;
                font-family: Calibri, Arial, sans-serif;
            }

            /* Outer preview scroll area */
            .preview-wrapper {
                width: 100%;
                min-height: 100vh;
                padding: 40px 0;
                display: flex;
                flex-direction: column; /* Stack pages */
                align-items: center;    /* Center pages */
                gap: 40px;              /* Gap between pages */
            }

            /* A4 wrapper */
            .a4-preview {
                width: 210mm;
                min-height: 297mm;
                background: #ffffff;
                box-shadow: 0 0 18px rgba(0,0,0,0.15);
                border-radius: 4px;

                /* IMPORTANT */
                display: flex;
                flex-direction: column;
                box-sizing: border-box;
            }

            /* Inner border container */
            .a4-inner {
                margin: 12mm;
                border: 2px solid #1e3a8a;
                padding: 8mm;

                /* FLEX MAGIC */
                display: flex;
                flex-direction: column;
                flex: 1;
                box-sizing: border-box;
            }

            /* Content area */
            .preview-content {
                flex: 1;
            }

            /* Footer stays at bottom */
            .preview-footer {
                margin-top: auto;
                padding-top: 8px;
            }

            /* Prevent accidental overflow */
            .preview-content,
            .preview-footer {
                overflow-wrap: break-word;
                word-break: break-word;
            }
        }

        /* ===== PRINT MODE (Adapted New Structure) ===== */
        @media print {
            /* Use Safe Margins that match the Preview's internal margin */
            @page {
                size: A4 portrait;
                margin: 12mm; 
            }

            body { margin: 0; padding: 0; background: white; -webkit-print-color-adjust: exact; }
            .no-print, nav, header, button, .btn { display: none !important; }

            .preview-wrapper { display: block; width: 100%; margin: 0; padding: 0; }

            .a4-preview {
                width: 100%;
                height: 100%; /* Try to fill page */
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                page-break-after: always;
                display: flex;
                flex-direction: column;
            }
            .a4-preview:last-child { page-break-after: auto; }

            .a4-inner {
                margin: 0 !important; /* Margin handled by @page */
                padding: 8mm !important;
                border: 2px solid #1e3a8a;
                display: flex;
                flex-direction: column;
                flex: 1;
            }

            .preview-content { flex: 1; }
            .preview-footer { margin-top: auto; padding-top: 8px; }
        }
    </style>
</head>
<body>

    <!-- Actions Bar (No Print) -->
    <div class="no-print fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 shadow p-3 flex justify-between items-center z-50 border-b">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back
            </a>
            <span class="px-2 py-1 rounded text-xs font-bold uppercase tracking-wider
                {{ $certificate->status == 'DRAFT' ? 'bg-gray-100 text-gray-600' : '' }}
                {{ $certificate->status == 'PENDING' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $certificate->status == 'ISSUED' ? 'bg-green-100 text-green-800' : '' }}
                {{ $certificate->status == 'REJECTED' ? 'bg-red-100 text-red-800' : '' }}">
                {{ $certificate->status }}
            </span>
        </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.certificates.edit', $certificate->id) }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white dark:bg-gray-800 py-2 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Edit Certificate
                </a>

                @if($certificate->status === 'DRAFT')
                    {{-- Submit Button (Always visible for Draft, no verification check required per new workflow) --}}
                    <form action="{{ route('admin.certificates.submit', $certificate->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Submit for Approval
                        </button>
                    </form>
                @endif
                
                {{-- Approve Button (Visible for Pending) --}}
                @php
                    $canApprove = false;
                    $user = auth()->user();
                    
                    if ($certificate->status === 'PENDING') {
                        // Admin/Manager/PRO always can
                        if ($user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('pro')) {
                            $canApprove = true;
                        }
                        // Engineer if assigned verifier AND not creator
                        elseif ($user->hasRole('engineer') && $certificate->assigned_verifier_id === $user->id && $certificate->created_by !== $user->id) {
                            $canApprove = true;
                        }
                    }
                @endphp

                @if($canApprove)

                    <button x-data="" @click="$dispatch('open-modal', 'confirm-approve-modal')" class="inline-flex justify-center rounded-md border border-transparent bg-green-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Approve Certificate
                    </button>
                    
                    <button x-data="" @click="$dispatch('open-modal', 'confirm-reject-modal')" class="inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Reject
                    </button>

                    <!-- Approve Modal -->
                    <x-modal name="confirm-approve-modal" focusable>
                        <form method="POST" action="{{ route('admin.certificates.approve', $certificate->id) }}" class="p-6">
                            @csrf
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Approve Certificate') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Are you sure you want to approve this certificate? It will be marked as ISSUED and assigned a permanent certificate number.') }}
                            </p>
                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancel') }}
                                </x-secondary-button>
                                <x-primary-button class="ml-3 bg-green-600 hover:bg-green-700">
                                    {{ __('Approve') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </x-modal>

                    <!-- Reject Modal -->
                    <x-modal name="confirm-reject-modal" focusable>
                        <form method="POST" action="{{ route('admin.certificates.reject', $certificate->id) }}" class="p-6">
                            @csrf
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Reject Certificate') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Please provide a reason for rejection. This will be visible to the creator.') }}
                            </p>
                            <div class="mt-4">
                                <x-input-label for="rejection_reason" value="{{ __('Reason') }}" />
                                <textarea id="rejection_reason" name="rejection_reason" rows="3" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required></textarea>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancel') }}
                                </x-secondary-button>
                                <x-danger-button class="ml-3">
                                    {{ __('Reject Certificate') }}
                                </x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                @endif

                @if($certificate->status === 'ISSUED')
                    <a href="{{ route('admin.certificates.pdf', $certificate) }}" target="_blank" class="inline-flex justify-center rounded-md border border-transparent bg-blue-900 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Download PDF
                    </a>
                @endif
            </div>
    </div>
    <div class="h-16 no-print"></div>

    <!-- MAIN PREVIEW WRAPPER -->
    <div class="preview-wrapper">

        <!-- PAGE 1 -->
        <div class="a4-preview">
            <div class="a4-inner">
                
                <!-- CONTENT -->
                <div class="preview-content">
                    <header class="flex justify-between items-start border-b-2 border-blue-900 pb-2 mb-3 print-compact">
                        <div class="w-2/5">
                            <h1 class="text-2xl font-bold text-blue-900 leading-none tracking-tight">LOUIS MARINE<br>QATAR</h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold mt-2 uppercase tracking-wide">Marine Services & Engineering</p>
                        </div>
                        <div class="w-1/5 text-center"></div>
                        <div class="w-2/5 text-right">
                            <div class="mb-1">
                                <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Certificate Number</span>
                                <span class="text-lg font-mono font-bold text-black">{{ $certificate->certificate_number ?? 'DRAFT' }}</span>
                            </div>
                        </div>
                    </header>

                    <div class="text-center mb-4 print-compact">
                        <h2 class="text-xl font-bold text-blue-900 uppercase underline decoration-2 underline-offset-4 tracking-wider">
                            {{ $certificate->type?->name ?? $certificate->certificateTemplate?->name ?? 'Certificate of Inspection' }}
                        </h2>
                    </div>
                    
                    <div class="mb-3 text-xs print-compact">
                        <p>This is to certify that the undernoted vessel/equipment has been inspected in accordance with the standard requirements.</p>
                    </div>

                    <div class="mb-4 print-compact">
                        <div class="bg-blue-900 text-white text-xs font-bold px-4 py-1 uppercase tracking-wider mb-0 rounded-t">
                            Vessel Particulars
                        </div>
                        <table class="w-full text-xs border-l border-r border-b border-gray-300">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="w-1/4 py-1 px-3 bg-gray-50 dark:bg-gray-700 font-semibold text-gray-700 dark:text-gray-300 border-r print-tight">Vessel Name</td>
                                    <td class="w-1/4 py-1 px-3 font-bold border-r print-tight">{{ $certificate->vessel->name ?? 'N/A' }}</td>
                                    <td class="w-1/4 py-1 px-3 bg-gray-50 dark:bg-gray-700 font-semibold text-gray-700 dark:text-gray-300 border-r print-tight">Owner / Customer</td>
                                    <td class="w-1/4 py-1 px-3 font-bold print-tight">{{ $certificate->customer->name }}</td>
                                </tr>
                                @if($certificate->vessel)
                                <tr>
                                    <td class="py-1 px-3 bg-gray-50 dark:bg-gray-700 font-semibold text-gray-700 dark:text-gray-300 border-r print-tight">IMO Number</td>
                                    <td class="py-1 px-3 border-r print-tight">{{ $certificate->vessel->imo_number ?? '-' }}</td>
                                    <td class="py-1 px-3 bg-gray-50 dark:bg-gray-700 font-semibold text-gray-700 dark:text-gray-300 border-r print-tight">Port of Registry</td>
                                    <td class="py-1 px-3 print-tight">{{ $certificate->vessel->port_of_registry ?? '-' }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="py-1 px-3 bg-gray-50 dark:bg-gray-700 font-semibold text-gray-700 dark:text-gray-300 border-r print-tight">Call Sign</td>
                                    <td class="py-1 px-3 border-r print-tight">{{ $certificate->vessel->call_sign ?? '-' }}</td>
                                    <td class="py-1 px-3 bg-gray-50 dark:bg-gray-700 font-semibold text-gray-700 dark:text-gray-300 border-r print-tight">Job Number</td>
                                    <td class="py-1 px-3 print-tight">{{ $certificate->job_number ?? $certificate->job?->job_number ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Non-Repeatable Sections (Page 1 Only) -->
                    @if($certificate->certificateTemplate)
                        @foreach($certificate->certificateTemplate->sections->where('is_repeatable', false) as $section)
                            <div class="mb-3 print-compact">
                                <h3 class="section-header font-bold text-xs text-blue-900 border-b-2 border-blue-900 mb-2 uppercase pb-1">
                                    {{ $section->name }}
                                </h3>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 mb-2">
                                    @foreach($section->fields as $field)
                                        @php
                                            $val = $certificate->fieldValues->where('certificate_template_field_id', $field->id)->first();
                                        @endphp
                                        @if($field->field_type === 'textarea' || $field->width === 'full')
                                            <div class="col-span-2 border-b border-gray-100 py-1">
                                                <span class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-0.5">{{ $field->field_label }}</span>
                                                <div class="text-xs text-gray-900 dark:text-gray-100 leading-snug whitespace-pre-wrap">{{ $val?->value ?? '-' }}</div>
                                            </div>
                                        @else
                                            <div class="flex border-b border-gray-100 py-0.5">
                                                <span class="w-1/2 text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase pt-0.5">{{ $field->field_label }}</span>
                                                <span class="w-1/2 text-xs font-bold text-gray-900 dark:text-gray-100 pl-2">{{ $val?->value ?? '-' }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @elseif($certificate->items && $certificate->items->count() > 0)
                        <div class="mb-6"><p class="text-sm text-gray-500 dark:text-gray-400 italic">Legacy data view.</p></div>
                    @endif
                    
                    @if($certificate->remarks)
                    <div class="mb-3 print-compact">
                        <h3 class="section-header font-bold text-xs text-blue-900 border-b-2 border-blue-900 mb-2 uppercase pb-1">Inspection Report</h3>
                        <div class="text-xs p-2 border border-gray-200 bg-gray-50 dark:bg-gray-700 text-justify leading-snug whitespace-pre-wrap min-h-[2rem]">
                            {{ $certificate->remarks }}
                        </div>
                    </div>
                    @endif

                    <!-- Dates -->
                    <div class="grid grid-cols-2 gap-4 my-4 border-t border-b border-gray-200 py-2 print-compact">
                        <div class="text-center border-r border-gray-200">
                            <span class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-0.5">Date of Issue</span>
                            <span class="text-base font-bold text-blue-900">{{ $certificate->issue_date ? $certificate->issue_date->format('d F Y') : 'Pending' }}</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-0.5">Date of Expiry</span>
                            <span class="text-base font-bold text-red-700">{{ $certificate->expiry_date ? $certificate->expiry_date->format('d F Y') : 'Pending' }}</span>
                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="preview-footer">
                    <div class="flex justify-between items-end gap-4">
                        <!-- Verified By -->
                        <div class="w-1/3 text-center mb-1">
                             <div class="h-16 border-b border-gray-400 mb-1 relative flex items-end justify-center pb-1">
                                 <span class="text-base font-serif italic text-gray-800 dark:text-gray-200">{{ $certificate->assignedVerifier->name ?? 'Verifier' }}</span>
                             </div>
                             <p class="text-xs font-bold text-blue-900 uppercase">Verified By</p>
                             <p class="text-[10px] text-gray-500 dark:text-gray-400">Louis Marine Qatar</p>
                        </div>

                        <!-- QR Code -->
                        <div class="w-1/3 text-center flex flex-col items-center justify-end">
                            @if($certificate->status === 'ISSUED')
                                <div id="qrcode" class="mb-2 opacity-90 mx-auto"></div>
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest mt-1">Scan to Verify</p>
                            @else
                                 <div class="h-20 w-20 border-2 border-dashed border-gray-200 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded mx-auto mb-2">
                                     <span class="text-[8px] text-gray-400 text-center">QR Code<br>Appears<br>Upon Issue</span>
                                 </div>
                            @endif
                        </div>

                        <!-- Authorized By -->
                        <div class="w-1/3 text-center mb-1">
                            <div class="h-16 border-b border-gray-400 mb-1 relative flex items-end justify-center pb-1">
                                <span class="text-base font-serif italic text-gray-800 dark:text-gray-200">{{ $certificate->time_issuedBy->name ?? $certificate->issuedBy->name ?? 'Authorized Signatory' }}</span>
                            </div>
                            <p class="text-xs font-bold text-blue-900 uppercase">Authorized Signature</p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Louis Marine Qatar</p>
                        </div>
                    </div>
                    <div class="text-center mt-3 pt-1 border-t border-gray-100">
                        <p class="text-[9px] text-gray-400">
                            This certificate is computer generated and valid without a seal. Generated on {{ now()->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <!-- CONTINUATION SHEET -->
        @if($certificate->certificateTemplate && $certificate->certificateTemplate->sections->where('is_repeatable', true)->count() > 0)
        <div class="a4-preview">
            <div class="a4-inner">
                
                <div class="preview-content">
                    <header class="flex justify-between items-end border-b-2 border-blue-900 pb-2 mb-4">
                        <div>
                            <h1 class="text-lg font-bold text-blue-900 uppercase">Louis Marine Qatar</h1>
                            <p class="text-xs font-bold text-gray-600">Certificate Details - Continuation Sheet</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Ref</p>
                            <p class="text-sm font-mono font-bold">{{ $certificate->certificate_number }}</p>
                        </div>
                    </header>

                    @foreach($certificate->certificateTemplate->sections->where('is_repeatable', true) as $section)
                        <div class="mb-4">
                            <h3 class="section-header font-bold text-xs text-blue-900 border-b-2 border-blue-900 mb-2 uppercase pb-1">
                                {{ $section->name }}
                            </h3>
                            <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Service Job</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($certificate->job)
                                    <a href="{{ route('admin.jobs.show', $certificate->job->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        {{ $certificate->job->job_number }}
                                    </a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Verification Status</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($certificate->assigned_verifier_id)
                                    @if($certificate->isVerified())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Verified by {{ $certificate->assignedVerifier->name }}
                                        </span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $certificate->verified_at->format('d M Y, h:i A') }}</div>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending Verification
                                        </span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Assigned to: {{ $certificate->assignedVerifier->name }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">Not Required</span>
                                @endif
                            </dd>
                        </div>
                            <table class="repeatable-table w-full text-[10px] border border-gray-300 mb-2">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-1 px-2 border-b border-r text-left text-[9px] font-bold text-gray-600 uppercase w-8">#</th>
                                        @foreach($section->fields as $field)
                                            <th class="py-1 px-2 border-b border-r text-left text-[9px] font-bold text-gray-600 uppercase">{{ $field->field_label }}</th>
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
                                    <tr class="repeatable-row">
                                        <td class="py-1 px-2 border-r text-gray-500 dark:text-gray-400 font-mono text-[9px]">{{ $i }}</td>
                                        @foreach($section->fields as $field)
                                            @php
                                                $val = $certificate->fieldValues
                                                    ->where('certificate_template_field_id', $field->id)
                                                    ->where('section_instance', $i)
                                                    ->first();
                                            @endphp
                                            <td class="py-1 px-2 border-r text-gray-900 dark:text-gray-100">{{ $val?->value ?? '-' }}</td>
                                        @endforeach
                                    </tr>
                                    @endfor
                                    @if($maxInstance == 0)
                                        <tr><td colspan="{{ $section->fields->count() + 1 }}" class="py-4 text-center text-gray-400 italic">No items recorded</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
                
                <div class="preview-footer">
                    <div class="text-center mt-3 pt-1 border-t border-gray-100">
                        <p class="text-[9px] text-gray-400">
                            Page 2 - Continuation Sheet - {{ $certificate->certificate_number }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
        @endif

    </div>

    @if($certificate->status === 'ISSUED')
    <script type="text/javascript">
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ route('certificates.verify-public', $certificate->certificate_number ?? 'DRAFT') }}",
            width: 70,
            height: 70,
            colorDark : "#1e3a8a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.M
        });
    </script>
    @endif
</body>
</html>
