@if(config('app.certificate_only')) @include('admin.certificate-templates.preview-glass') @else
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                        {{ $certificateTemplate->title }}
                    </h2>
                    <span class="badge-info text-xs">Live Preview</span>
                    <span class="{{ $certificateTemplate->is_active ? 'badge-success' : 'badge-neutral' }} text-xs">
                        {{ $certificateTemplate->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Category: <strong>{{ $certificateTemplate->equipmentCategory->name ?? 'General Category' }}</strong> • Official Certificate Document & Live Data Preview
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="btn-ghost text-xs gap-1.5 shadow-sm" title="Print Certificate Sheet">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Print Sheet</span>
                </button>
                <a href="{{ route('admin.certificate-templates.edit', $certificateTemplate) }}" class="btn-primary text-xs gap-1.5 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20 4.828a2 2 0 010 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Template</span>
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #certificate-print-sheet, #certificate-print-sheet * {
                visibility: visible;
            }
            #certificate-print-sheet {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                border: none !important;
                box-shadow: none !important;
            }
            .no-print {
                display: none !important;
            }
        }
        .navy-header {
            background-color: #0a2142;
            color: #ffffff;
        }
        .navy-text {
            color: #0a2142;
        }
    </style>

    <div class="py-6 space-y-6" x-data="{ 
        previewTab: 'document', // 'document' or 'inputs'
        dataType: 'original',   // 'original' or 'placeholders'
    }">
        <div class="max-w-5xl mx-auto space-y-6">
            
            <!-- PREVIEW CONTROLLER TOOLBAR -->
            <div class="no-print theme-card p-4 flex flex-wrap items-center justify-between gap-4 border-l-4 border-brand-500">
                <div class="flex items-center gap-3">
                    <!-- Tab Switchers -->
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80">
                        <button @click="previewTab = 'document'" 
                                :class="previewTab === 'document' ? 'bg-white dark:bg-slate-900 text-brand-600 dark:text-brand-400 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900'" 
                                class="px-3.5 py-1.5 text-xs rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>📄 Live Certificate Document Sheet</span>
                        </button>
                        <button @click="previewTab = 'inputs'" 
                                :class="previewTab === 'inputs' ? 'bg-white dark:bg-slate-900 text-brand-600 dark:text-brand-400 shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900'" 
                                class="px-3.5 py-1.5 text-xs rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            <span>📝 Form Inputs Layout</span>
                        </button>
                    </div>
                </div>

                <!-- Live Data Selector Switch -->
                <div x-show="previewTab === 'document'" class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Preview Data:</span>
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700/80">
                        <button @click="dataType = 'original'" 
                                :class="dataType === 'original' ? 'bg-emerald-500 text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900'" 
                                class="px-3 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Live Preview with Original Data</span>
                        </button>
                        <button @click="dataType = 'placeholders'" 
                                :class="dataType === 'placeholders' ? 'bg-slate-700 text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900'" 
                                class="px-3 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>Template Structure Only</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- VIEW 1: OFFICIAL LIVE CERTIFICATE DOCUMENT SHEET (PAGE 1) -->
            <div x-show="previewTab === 'document'" id="certificate-print-sheet" class="w-full max-w-[210mm] min-h-[297mm] mx-auto bg-white text-slate-900 shadow-2xl p-8 sm:p-10 border border-slate-300 font-sans relative flex flex-col justify-between overflow-hidden my-4">
                <div>
                <!-- COMPANY BRANDING HEADER (CUSTOM PNG OVERLAY OR DEFAULT BRANDING) -->
                @if($customHeaderUrl && ($certificateTemplate->header_config['show_logo'] ?? true))
                    <!-- Compact Header Overlay Banner -->
                    <div class="mb-3 pb-2 bg-white overflow-hidden text-center border-b border-slate-200">
                        <img src="{{ $customHeaderUrl }}" alt="Certificate Header Overlay" class="w-full h-auto max-h-28 sm:max-h-32 object-contain mx-auto block">
                    </div>
                @else
                    <!-- Red Top Header Bar -->
                    <div class="h-1.5 bg-red-600 w-full mb-6"></div>

                    <div class="text-center mb-6 border-b border-slate-200 pb-5">
                        <div class="flex items-center justify-center gap-3 mb-2">
                            @if($companyLogoUrl && ($certificateTemplate->header_config['show_logo'] ?? true))
                                <img src="{{ $companyLogoUrl }}" alt="Company Logo" class="h-12 max-w-xs object-contain">
                            @endif
                            @if($certificateTemplate->header_config['show_company_name'] ?? true)
                                <div class="text-center">
                                    <h1 class="text-2xl font-black tracking-widest text-slate-900 uppercase font-serif">
                                        {{ $settings['company_name'] ?? 'SAFETY LINK MARINE SERVICES' }}
                                    </h1>
                                    <p class="text-xs text-slate-600 font-bold uppercase tracking-wider">
                                        {{ $settings['company_address'] ?? 'Al Jadaf, Ras Al Khor, Dubai, Dubai Municipality' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-center gap-6 text-[11px] text-slate-600 font-semibold mt-2 flex-wrap">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                                {{ $settings['company_email'] ?? 'info@safetylinkmarine.com' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-800" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                                {{ $settings['company_phone'] ?? '+971 56 606 3356' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-800" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.083 9h11.834a8 8 0 01-11.834 0zM10 2a8 8 0 100 16 8 8 0 000-16z" clip-rule="evenodd"/></svg>
                                {{ $settings['company_website'] ?? 'www.safetylinkmarine.com' }}
                            </span>
                        </div>
                        <div class="h-0.5 bg-red-600 w-3/4 mx-auto mt-3"></div>
                    </div>
                @endif

                <!-- CERTIFICATE MAIN TITLE -->
                <div class="text-center mt-2 mb-3">
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-wider underline decoration-slate-900 underline-offset-4 font-serif">
                        {{ $certificateTemplate->title }}
                    </h2>
                    <p class="text-xs italic text-slate-600 mt-1 max-w-2xl mx-auto font-medium">
                        This is to certify that the undernoted vessel/equipment has been inspected in accordance with the standard marine safety requirements.
                    </p>
                </div>

                <!-- SECTION: VESSEL PARTICULARS -->
                <div class="mb-5">
                    <div class="navy-header px-3 py-1.5 text-xs font-extrabold uppercase tracking-wider">
                        VESSEL PARTICULARS
                    </div>
                    <table class="w-full text-xs border-collapse border border-slate-300">
                        <tr>
                            <td class="bg-slate-100 font-bold px-3 py-2 border border-slate-300 w-1/6 text-slate-700">Vessel Name</td>
                            <td class="font-bold px-3 py-2 border border-slate-300 w-2/6 text-slate-900">
                                <span x-show="dataType === 'original'">{{ $originalData['vessel_name'] }}</span>
                                <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[Vessel Name]</span>
                            </td>
                            <td class="bg-slate-100 font-bold px-3 py-2 border border-slate-300 w-1/6 text-slate-700">Owner / Customer</td>
                            <td class="font-bold px-3 py-2 border border-slate-300 w-2/6 text-slate-900">
                                <span x-show="dataType === 'original'">{{ $originalData['customer_name'] }}</span>
                                <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[Customer / Owner]</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-700">IMO Number</td>
                            <td class="font-bold px-3 py-2 border border-slate-300 text-slate-900">
                                <span x-show="dataType === 'original'">{{ $originalData['imo_number'] }}</span>
                                <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[IMO Number]</span>
                            </td>
                            <td class="bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-700">Port of Registry</td>
                            <td class="font-bold px-3 py-2 border border-slate-300 text-slate-900">
                                <span x-show="dataType === 'original'">{{ $originalData['port_registry'] }}</span>
                                <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[-]</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-700">Call Sign</td>
                            <td class="font-bold px-3 py-2 border border-slate-300 text-slate-900">
                                <span x-show="dataType === 'original'">{{ $originalData['call_sign'] }}</span>
                                <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[Call Sign]</span>
                            </td>
                            <td class="bg-slate-100 font-bold px-3 py-2 border border-slate-300 text-slate-700">Job Number</td>
                            <td class="font-bold px-3 py-2 border border-slate-300 text-blue-700">
                                <span x-show="dataType === 'original'">{{ $originalData['job_number'] }}</span>
                                <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[Job #]</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- DYNAMIC TEMPLATE SECTIONS -->
                <div class="space-y-5">
                    @foreach($certificateTemplate->sections as $section)
                        @if($isMultiCylinderCategory && ($section->is_repeatable || Str::contains($section->name, ['Equipment Identification', 'Cylinder', 'Items'], true)))
                            @continue
                        @endif
                    <div>
                        <!-- Section Heading -->
                        <div class="border-b-2 border-slate-900 pb-1 mb-2 flex items-center justify-between">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">
                                {{ $section->name }}
                            </h3>
                            @if($section->is_repeatable)
                                <span class="text-[10px] font-mono text-slate-500 uppercase">Repeatable Items Table</span>
                            @endif
                        </div>

                        <!-- SECTION CONTENT: TABLE OR GRID -->
                        @if($section->is_repeatable || $section->layout_type === 'table')
                            <table class="w-full text-xs border-collapse border border-slate-300">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-800 font-bold uppercase text-[11px]">
                                        <th class="border border-slate-300 px-3 py-1.5 text-center w-12">#</th>
                                        @foreach($section->fields as $field)
                                            <th class="border border-slate-300 px-3 py-1.5 text-left">{{ strtoupper($field->field_label) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $rowCount = $isMultiCylinderCategory ? 10 : 3;
                                    @endphp
                                    @for($r = 1; $r <= $rowCount; $r++)
                                        <tr class="{{ $r % 2 === 0 ? 'bg-slate-50' : 'bg-white' }} border border-slate-300 text-slate-900 hover:bg-amber-50/40 transition">
                                            <td class="border border-slate-300 px-3 py-1.5 text-center font-bold text-slate-700">{{ $r }}</td>
                                            @foreach($section->fields as $field)
                                                <td class="border border-slate-300 px-3 py-1.5 font-medium">
                                                    <template x-if="dataType === 'original'">
                                                        <span class="{{ Str::contains(strtolower($field->field_name), ['serial', 'sn', 'number']) ? 'font-mono font-bold text-slate-900' : '' }}">
                                                            {{ $sampleRowFieldValues[$r][$field->id] ?? ($sampleFieldValues[$field->id] ?? 'Pass') }}
                                                        </span>
                                                    </template>
                                                    <template x-if="dataType === 'placeholders'">
                                                        <span class="text-slate-400 italic">[{{ $field->field_label }} #{{ $r }}]</span>
                                                    </template>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>

                        @else
                            <!-- NON-REPEATABLE SECTION (SINGLE COLUMN OR TWO COLUMN WITH FULL WIDTH SUPPORT) -->
                            @if($section->layout_type === 'single_column' || Str::contains($section->name, ['Compliance', 'Report', 'Statement', 'Findings', 'Remarks'], true))
                                <div class="p-3.5 bg-slate-50 border border-slate-200 text-xs text-slate-800 leading-relaxed font-medium">
                                    @foreach($section->fields as $field)
                                        <div class="{{ !$loop->first ? 'mt-3 pt-3 border-t border-slate-200' : '' }}">
                                            @if($field->field_label && strtolower(trim($field->field_label)) !== strtolower(trim($section->name)))
                                                <span class="block font-bold text-slate-700 text-[11px] uppercase mb-1">{{ $field->field_label }}:</span>
                                            @endif
                                            <span x-show="dataType === 'original'" class="font-semibold text-slate-900 leading-relaxed block">
                                                {{ $sampleFieldValues[$field->id] ?? ('The above ' . strtolower($certificateTemplate->equipmentCategory->name ?? 'equipment') . ' have been inspected, tested, and serviced in accordance with applicable SOLAS / IMO requirements, manufacturer instructions, and classification society standards. The equipment was found to be in satisfactory condition and fit for service, except as noted above.') }}
                                            </span>
                                            <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[{{ $field->field_label }}]</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <table class="w-full text-xs border-collapse border border-slate-200">
                                    @php
                                        $currentPair = [];
                                    @endphp
                                    @foreach($section->fields as $field)
                                        @if($field->width === 'full')
                                            @if(count($currentPair) > 0)
                                                <tr>
                                                    <td class="bg-slate-50 font-bold px-3 py-2 border border-slate-200 w-1/4 text-slate-700">
                                                        {{ strtoupper($currentPair[0]->field_label) }}
                                                    </td>
                                                    <td class="font-bold px-3 py-2 border border-slate-200 w-1/4 text-slate-900">
                                                        <span x-show="dataType === 'original'">{{ $sampleFieldValues[$currentPair[0]->id] ?? 'Pass' }}</span>
                                                        <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[{{ $currentPair[0]->field_label }}]</span>
                                                    </td>
                                                    <td class="bg-slate-50 border border-slate-200 w-1/4"></td>
                                                    <td class="border border-slate-200 w-1/4"></td>
                                                </tr>
                                                @php $currentPair = []; @endphp
                                            @endif
                                            <tr>
                                                <td class="bg-slate-50 font-bold px-3 py-2 border border-slate-200 w-1/4 text-slate-700">
                                                    {{ strtoupper($field->field_label) }}
                                                </td>
                                                <td colspan="3" class="font-bold px-3 py-2 border border-slate-200 text-slate-900 leading-relaxed">
                                                    <span x-show="dataType === 'original'">{{ $sampleFieldValues[$field->id] ?? 'Pass' }}</span>
                                                    <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[{{ $field->field_label }}]</span>
                                                </td>
                                            </tr>
                                        @else
                                            @php $currentPair[] = $field; @endphp
                                            @if(count($currentPair) === 2)
                                                <tr>
                                                    @foreach($currentPair as $pField)
                                                        <td class="bg-slate-50 font-bold px-3 py-2 border border-slate-200 w-1/4 text-slate-700">
                                                            {{ strtoupper($pField->field_label) }}
                                                        </td>
                                                        <td class="font-bold px-3 py-2 border border-slate-200 w-1/4 text-slate-900">
                                                            <span x-show="dataType === 'original'">{{ $sampleFieldValues[$pField->id] ?? 'Pass' }}</span>
                                                            <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[{{ $pField->field_label }}]</span>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                @php $currentPair = []; @endphp
                                            @endif
                                        @endif
                                    @endforeach

                                    @if(count($currentPair) === 1)
                                        <tr>
                                            <td class="bg-slate-50 font-bold px-3 py-2 border border-slate-200 w-1/4 text-slate-700">
                                                {{ strtoupper($currentPair[0]->field_label) }}
                                            </td>
                                            <td class="font-bold px-3 py-2 border border-slate-200 w-1/4 text-slate-900">
                                                <span x-show="dataType === 'original'">{{ $sampleFieldValues[$currentPair[0]->id] ?? 'Pass' }}</span>
                                                <span x-show="dataType === 'placeholders'" class="text-slate-400 italic">[{{ $currentPair[0]->field_label }}]</span>
                                            </td>
                                            <td class="bg-slate-50 border border-slate-200 w-1/4"></td>
                                            <td class="border border-slate-200 w-1/4"></td>
                                        </tr>
                                    @endif
                                </table>
                            @endif
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- DATES & SIGNATURES SECTION -->
                <div class="mt-8 pt-4 border-t border-slate-300">
                    <div class="grid grid-cols-2 gap-8 text-center text-xs mb-8">
                        <div>
                            <span class="block text-[10px] font-bold uppercase text-slate-500">DATE OF INSPECTION / ISSUE</span>
                            <span class="text-sm font-black text-slate-900 mt-1 block" x-text="dataType === 'original' ? '{{ $originalData['inspection_date'] }}' : '[Issue Date]'">07 August 2026</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase text-slate-500">DATE OF EXPIRY</span>
                            <span class="text-sm font-black text-red-600 mt-1 block" x-text="dataType === 'original' ? '{{ $originalData['expiry_date'] }}' : '[Expiry Date]'">07 August 2027</span>
                        </div>
                    </div>

                    @if($isMultiCylinderCategory)
                        <div class="my-4 p-2 bg-slate-50 border border-slate-300 rounded-sm text-[11px] text-slate-700 flex items-center justify-between font-medium">
                            @if($isLifeRaftCategory)
                                <span>* <strong>Note:</strong> SOLAS Emergency Survival Equipment Pack Checklist (12 Emergency Items & Expiry Schedule) is listed in attached <strong>Annexure (Sheet 2 of 2)</strong>.</span>
                            @elseif($isLifeboatCategory)
                                <span>* <strong>Note:</strong> SOLAS Lifeboat, Davit & Winch Annual / 5-Yearly Examination & Load Test Checklist (12 Sub-Systems & Test Parameters) is listed in attached <strong>Annexure (Sheet 2 of 2)</strong>.</span>
                            @else
                                <span>* <strong>Note:</strong> Equipment Identification & 10-Cylinder Details are listed in attached <strong>Annexure (Sheet 2 of 2)</strong>.</span>
                            @endif
                            <span class="font-bold text-slate-900 uppercase text-[10px] bg-slate-200 px-2 py-0.5 rounded">Page 1 of 2</span>
                        </div>
                    @endif

                    <!-- SIGNATURE & QR CODE FOOTER -->
                    <div class="grid grid-cols-3 items-end gap-4 text-center text-xs pt-3 border-t border-slate-200 mb-3">
                        <!-- VERIFIED BY -->
                        <div>
                            @if($certificateTemplate->footer_config['show_signature'] ?? true)
                                <div class="h-8 flex items-center justify-center italic text-slate-700 font-serif text-xs font-bold border-b border-slate-400 mb-1">
                                    Engineer 1
                                </div>
                                <span class="block font-black text-slate-900 uppercase text-[10px]">VERIFIED BY</span>
                                <span class="block text-[9px] text-slate-500">{{ $settings['company_name'] ?? 'Louis Marine Qatar' }}</span>
                            @endif
                        </div>

                        <!-- QR VERIFICATION -->
                        <div class="flex flex-col items-center justify-center">
                            @if($certificateTemplate->footer_config['show_qr_code'] ?? true)
                                <div class="w-14 h-14 border border-slate-300 p-0.5 bg-white mb-1 shadow-sm">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://louismarine.com/verify/CERT-2026-088" alt="QR Code Verification" class="w-full h-full object-contain">
                                </div>
                                <span class="text-[8px] font-bold text-slate-600 uppercase tracking-tighter">SCAN TO VERIFY ONLINE</span>
                            @endif
                        </div>

                        <!-- AUTHORIZED SIGNATURE -->
                        <div>
                            @if($certificateTemplate->footer_config['show_signature'] ?? true)
                                <div class="h-8 flex items-center justify-center italic text-slate-700 font-serif text-xs font-bold border-b border-slate-400 mb-1">
                                    Engineer 1
                                </div>
                                <span class="block font-black text-slate-900 uppercase text-[10px]">AUTHORIZED SIGNATURE</span>
                                <span class="block text-[9px] text-slate-500">{{ $settings['company_name'] ?? 'Louis Marine Qatar' }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- CLASSIFICATION LOGOS / FOOTER OVERLAY -->
                    @if($customFooterUrl)
                        <div class="pt-2 bg-white text-center border-t border-slate-200">
                            <img src="{{ $customFooterUrl }}" alt="Certificate Footer Overlay" class="w-full h-auto max-h-24 sm:max-h-28 object-contain mx-auto block">
                        </div>
                    @else
                        <div class="pt-2 border-t border-slate-200 text-center">
                            <div class="h-1 bg-red-600 w-full mb-3"></div>
                            <div class="flex justify-center items-center gap-6 opacity-80">
                                <span class="text-[10px] font-bold text-slate-600">DNV</span>
                                <span class="text-[10px] font-bold text-slate-600">ABS</span>
                                <span class="text-[10px] font-bold text-slate-600">BUREAU VERITAS</span>
                                <span class="text-[10px] font-bold text-slate-600">LLOYD'S REGISTER</span>
                            </div>
                        </div>
                    @endif
                </div>
                </div>

            </div>

            @if($isMultiCylinderCategory)
                <!-- PAGE BREAK DIVIDER FOR PREVIEW -->
                <div x-show="previewTab === 'document'" class="my-8 border-t-2 border-dashed border-slate-400 relative text-center no-print">
                    <span class="bg-slate-900 text-amber-400 text-xs font-bold px-4 py-1.5 rounded-full border border-slate-700 shadow-md">
                        📄 SHEET 2: SEPARATE ANNEXURE SHEET (UP TO 10 CYLINDERS / SERIAL NUMBERS)
                    </span>
                </div>

                <!-- SHEET 2: DEDICATED CYLINDER INSPECTION SCHEDULE ANNEXURE (PAGE 2) -->
                <div x-show="previewTab === 'document'" id="certificate-annexure-sheet" class="w-full max-w-[210mm] min-h-[297mm] mx-auto bg-white text-slate-900 shadow-2xl p-8 sm:p-10 border border-slate-300 font-sans relative flex flex-col justify-between overflow-hidden print:break-before-page my-8">
                    <div>
                    <!-- BRANDING HEADER OVERLAY OR DEFAULT -->
                    @if($customHeaderUrl && ($certificateTemplate->header_config['show_logo'] ?? true))
                        <div class="mb-3 pb-2 bg-white overflow-hidden text-center border-b border-slate-200">
                            <img src="{{ $customHeaderUrl }}" alt="Certificate Header Overlay" class="w-full h-auto max-h-28 sm:max-h-32 object-contain mx-auto block">
                        </div>
                    @else
                        <div class="h-1.5 bg-red-600 w-full mb-4"></div>
                        <div class="text-center mb-4 border-b border-slate-200 pb-3">
                            <h1 class="text-xl font-black tracking-widest text-slate-900 uppercase font-serif">
                                {{ $settings['company_name'] ?? 'SAFETY LINK MARINE SERVICES' }}
                            </h1>
                            <p class="text-xs text-slate-600 font-bold uppercase tracking-wider">
                                {{ $settings['company_address'] ?? 'Al Jadaf, Ras Al Khor, Dubai, Dubai Municipality' }}
                            </p>
                        </div>
                    @endif

                    @if($isLifeRaftCategory)
                        <!-- SOLAS LIFE RAFT EMERGENCY SURVIVAL EQUIPMENT CHECKLIST TITLE -->
                        <div class="bg-slate-900 text-white p-3 rounded-sm mb-4 text-center">
                            <h2 class="text-sm font-black uppercase tracking-widest text-amber-400">
                                ANNEXURE - SOLAS EMERGENCY SURVIVAL EQUIPMENT PACK CHECKLIST
                            </h2>
                            <p class="text-[11px] text-slate-300 font-mono mt-0.5">
                                ATTACHMENT TO CERTIFICATE NO: <span class="font-bold text-white" x-text="dataType === 'original' ? '{{ $originalData['cert_number'] }}' : '[Certificate #]'">CERT-2026-088</span> | LIFE RAFT SERIAL: <span class="font-bold text-white">LR-25P-7741</span> | SOLAS PACK A
                            </p>
                        </div>

                        <!-- 12-ITEM SOLAS EQUIPMENT CHECKLIST TABLE -->
                        <div class="mb-6 overflow-x-auto">
                            <table class="w-full text-xs border-collapse border border-slate-400">
                                <thead>
                                    <tr class="bg-slate-800 text-white font-bold uppercase text-[10px]">
                                        <th class="border border-slate-400 px-2 py-2 text-center w-8">#</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-left">EQUIPMENT / ITEM DESCRIPTION</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-center">REQUIRED QTY</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-center">INSPECTION STATUS</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-center">EXPIRY DATE</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-left">REMARKS</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-300 text-slate-900 font-medium">
                                    @foreach($lifeRaftChecklistItems as $item)
                                        <tr class="{{ $loop->iteration % 2 === 0 ? 'bg-slate-50' : 'bg-white' }} hover:bg-amber-50/50 transition">
                                            <td class="border border-slate-300 px-2 py-2 text-center font-bold text-slate-700">{{ $item['item_no'] }}</td>
                                            <td class="border border-slate-300 px-2.5 py-2 font-bold text-slate-900">{{ $item['description'] }}</td>
                                            <td class="border border-slate-300 px-2.5 py-2 text-center font-mono font-bold">{{ $item['required_qty'] }}</td>
                                            <td class="border border-slate-300 px-2.5 py-2 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    {{ $item['status'] }}
                                                </span>
                                            </td>
                                            <td class="border border-slate-300 px-2.5 py-2 text-center font-mono font-bold {{ $item['expiry_date'] !== 'N/A' ? 'text-blue-700' : 'text-slate-500' }}">
                                                {{ $item['expiry_date'] }}
                                            </td>
                                            <td class="border border-slate-300 px-2.5 py-2 text-slate-600 italic text-[11px]">{{ $item['remarks'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($isLifeboatCategory)
                        <!-- SOLAS LIFEBOAT, DAVIT & WINCH EXAMINATION CHECKLIST TITLE -->
                        <div class="bg-slate-900 text-white p-3 rounded-sm mb-4 text-center">
                            <h2 class="text-sm font-black uppercase tracking-widest text-amber-400">
                                ANNEXURE - LIFEBOAT, DAVIT & WINCH ANNUAL / 5-YEARLY EXAMINATION CHECKLIST
                            </h2>
                            <p class="text-[11px] text-slate-300 font-mono mt-0.5">
                                ATTACHMENT TO CERTIFICATE NO: <span class="font-bold text-white" x-text="dataType === 'original' ? '{{ $originalData['cert_number'] }}' : '[Certificate #]'">CERT-2026-088</span> | LIFEBOAT SERIAL: <span class="font-bold text-white">LB-RB-9901</span> | IMO MSC.402(96) COMPLIANT
                            </p>
                        </div>

                        <!-- 12-ITEM SOLAS LIFEBOAT & LAUNCHING APPLIANCE CHECKLIST TABLE -->
                        <div class="mb-6 overflow-x-auto">
                            <table class="w-full text-xs border-collapse border border-slate-400">
                                <thead>
                                    <tr class="bg-slate-800 text-white font-bold uppercase text-[10px]">
                                        <th class="border border-slate-400 px-2 py-2 text-center w-8">#</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-left">SUB-SYSTEM / COMPONENT</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-left">INSPECTION & TEST PARAMETER</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-center">SERVICE TYPE</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-center">TEST RESULT / STATUS</th>
                                        <th class="border border-slate-400 px-2.5 py-2 text-left">REMARKS</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-300 text-slate-900 font-medium">
                                    @foreach($lifeboatChecklistItems as $item)
                                        <tr class="{{ $loop->iteration % 2 === 0 ? 'bg-slate-50' : 'bg-white' }} hover:bg-amber-50/50 transition">
                                            <td class="border border-slate-300 px-2 py-2 text-center font-bold text-slate-700">{{ $item['item_no'] }}</td>
                                            <td class="border border-slate-300 px-2.5 py-2 font-bold text-slate-900">{{ $item['description'] }}</td>
                                            <td class="border border-slate-300 px-2.5 py-2 text-slate-700 text-[11px]">{{ $item['inspection_parameter'] }}</td>
                                            <td class="border border-slate-300 px-2.5 py-2 text-center font-mono font-semibold text-slate-600">{{ $item['service_type'] }}</td>
                                            <td class="border border-slate-300 px-2.5 py-2 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    {{ $item['status'] }}
                                                </span>
                                            </td>
                                            <td class="border border-slate-300 px-2.5 py-2 text-slate-600 italic text-[11px]">{{ $item['remarks'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- ANNEXURE TITLE BAR -->
                        <div class="bg-slate-900 text-white p-3 rounded-sm mb-4 text-center">
                            <h2 class="text-sm font-black uppercase tracking-widest text-amber-400">
                                ANNEXURE - CYLINDER & EQUIPMENT INSPECTION SCHEDULE
                            </h2>
                            <p class="text-[11px] text-slate-300 font-mono mt-0.5">
                                ATTACHMENT TO CERTIFICATE NO: <span class="font-bold text-white" x-text="dataType === 'original' ? '{{ $originalData['cert_number'] }}' : '[Certificate #]'">CERT-2026-088</span> | VESSEL: <span class="font-bold text-white" x-text="dataType === 'original' ? '{{ $originalData['vessel_name'] }}' : '[Vessel Name]'">MV Gulf Pioneer</span> | JOB NO: <span class="font-bold text-white" x-text="dataType === 'original' ? '{{ $originalData['job_number'] }}' : '[Job #]'">208-4582</span>
                            </p>
                        </div>

                        <!-- 10-ITEM CYLINDER INSPECTION TABLE (PAGE 2 EXCLUSIVE) -->
                        @php
                            $repeatableSection = $certificateTemplate->sections->firstWhere('is_repeatable', true) 
                                ?? $certificateTemplate->sections->first(fn($s) => Str::contains($s->name, ['Equipment Identification', 'Cylinder', 'Items'], true));
                        @endphp

                        @if($repeatableSection)
                            <div class="mb-6 overflow-x-auto">
                                <table class="w-full text-xs border-collapse border border-slate-400">
                                    <thead>
                                        <tr class="bg-slate-800 text-white font-bold uppercase text-[10px]">
                                            <th class="border border-slate-400 px-2.5 py-2 text-center w-10">#</th>
                                            @foreach($repeatableSection->fields as $field)
                                                <th class="border border-slate-400 px-2.5 py-2 text-left">{{ strtoupper($field->field_label) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-300 text-slate-900 font-medium">
                                        @for($r = 1; $r <= 10; $r++)
                                            <tr class="{{ $r % 2 === 0 ? 'bg-slate-50' : 'bg-white' }} hover:bg-amber-50/50 transition">
                                                <td class="border border-slate-300 px-2.5 py-2 text-center font-bold text-slate-700">{{ $r }}</td>
                                                @foreach($repeatableSection->fields as $field)
                                                    <td class="border border-slate-300 px-2.5 py-2 font-medium">
                                                        <template x-if="dataType === 'original'">
                                                            <span class="{{ Str::contains(strtolower($field->field_name), ['serial', 'sn', 'number']) ? 'font-mono font-bold text-slate-900' : '' }}">
                                                                {{ $sampleRowFieldValues[$r][$field->id] ?? ($sampleFieldValues[$field->id] ?? 'Pass') }}
                                                            </span>
                                                        </template>
                                                        <template x-if="dataType === 'placeholders'">
                                                            <span class="text-slate-400 italic">[{{ $field->field_label }} #{{ $r }}]</span>
                                                        </template>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- FALLBACK 10-ITEM TABLE -->
                            <div class="mb-6 overflow-x-auto">
                                <table class="w-full text-xs border-collapse border border-slate-400">
                                    <thead>
                                        <tr class="bg-slate-800 text-white font-bold uppercase text-[10px]">
                                            <th class="border border-slate-400 px-2 py-2 text-center w-8">#</th>
                                            <th class="border border-slate-400 px-2.5 py-2 text-left">SERIAL NUMBER</th>
                                            <th class="border border-slate-400 px-2.5 py-2 text-left">EQUIPMENT / CYLINDER TYPE</th>
                                            <th class="border border-slate-400 px-2.5 py-2 text-left">CAPACITY / RATING</th>
                                            <th class="border border-slate-400 px-2.5 py-2 text-left">MANUFACTURER</th>
                                            <th class="border border-slate-400 px-2.5 py-2 text-center">TEST PRESSURE</th>
                                            <th class="border border-slate-400 px-2.5 py-2 text-center">CONDITION</th>
                                            <th class="border border-slate-400 px-2.5 py-2 text-center">RESULT</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-300 text-slate-900 font-medium">
                                        <template x-if="dataType === 'original'">
                                            <template x-for="cyl in {{ json_encode($sampleCylinders) }}" :key="cyl.item_no">
                                                <tr class="hover:bg-amber-50/50 transition">
                                                    <td class="border border-slate-300 px-2 py-2 text-center font-bold text-slate-700" x-text="cyl.item_no"></td>
                                                    <td class="border border-slate-300 px-2.5 py-2 font-mono font-bold text-slate-900" x-text="cyl.serial_number"></td>
                                                    <td class="border border-slate-300 px-2.5 py-2 font-semibold" x-text="cyl.equipment_type"></td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-slate-700" x-text="cyl.capacity"></td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-slate-700" x-text="cyl.manufacturer"></td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-center font-mono" x-text="cyl.test_pressure"></td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-center text-emerald-700 font-semibold" x-text="cyl.visual_condition"></td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-center">
                                                        <span class="inline-block px-2 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-bold uppercase rounded" x-text="cyl.test_result"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                        <template x-if="dataType === 'placeholders'">
                                            <template x-for="i in 10" :key="i">
                                                <tr class="hover:bg-slate-50 transition">
                                                    <td class="border border-slate-300 px-2 py-2 text-center font-bold text-slate-400" x-text="i"></td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-slate-400 italic">[Serial #]</td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-slate-400 italic">[Equipment Type]</td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-slate-400 italic">[Capacity / Pressure]</td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-slate-400 italic">[Manufacturer]</td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-center text-slate-400 italic">[Test Pressure]</td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-center text-slate-400 italic">[Condition]</td>
                                                    <td class="border border-slate-300 px-2.5 py-2 text-center text-slate-400 italic">[Pass / Fail]</td>
                                                </tr>
                                            </template>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif

                    <!-- RECERTIFICATION STATEMENT & SIGNATURES -->
                    <div class="mt-6 pt-4 border-t border-slate-300">
                        <p class="text-[11px] text-slate-700 leading-relaxed font-medium mb-6">
                            <strong>Schedule Verification:</strong> 
                            @if($isLifeRaftCategory)
                                All 12 emergency survival equipment pack items, flares, first aid kit, water/food rations, and Hydrostatic Release Unit (HRU) listed in this Annexure have undergone visual examination, function testing, and expiry verification in accordance with SOLAS / IMO Res. A.761(18) and manufacturer servicing standards.
                            @else
                                All 10 cylinders/equipment units listed in this Annexure have undergone visual inspection, hydrostatic pressure testing, valve examination, and mass/capacity verification in accordance with SOLAS / IMO requirements and manufacturer maintenance standards.
                            @endif
                        </p>

                        <!-- SIGNATURE & QR CODE FOOTER -->
                        <div class="grid grid-cols-3 items-end gap-4 text-center text-xs pt-3 border-t border-slate-200 mb-3">
                            <div>
                                <div class="h-8 flex items-center justify-center italic text-slate-700 font-serif text-xs font-bold border-b border-slate-400 mb-1">Engineer 1</div>
                                <span class="block font-black text-slate-900 uppercase text-[10px]">INSPECTED BY</span>
                                <span class="block text-[9px] text-slate-500">{{ $settings['company_name'] ?? 'Louis Marine Qatar' }}</span>
                            </div>
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-14 h-14 border border-slate-300 p-0.5 bg-white mb-1 shadow-sm">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://louismarine.com/verify/CERT-2026-088" alt="QR Code Verification" class="w-full h-full object-contain">
                                </div>
                                <span class="text-[8px] font-bold text-slate-600 uppercase tracking-tighter">ANNEXURE VERIFICATION</span>
                            </div>
                            <div>
                                <div class="h-8 flex items-center justify-center italic text-slate-700 font-serif text-xs font-bold border-b border-slate-400 mb-1">Engineer 1</div>
                                <span class="block font-black text-slate-900 uppercase text-[10px]">AUTHORIZED SIGNATURE</span>
                                <span class="block text-[9px] text-slate-500">{{ $settings['company_name'] ?? 'Louis Marine Qatar' }}</span>
                            </div>
                        </div>

                        <!-- CLASSIFICATION LOGOS / FOOTER OVERLAY -->
                        @if($customFooterUrl)
                            <div class="pt-2 bg-white text-center border-t border-slate-200">
                                <img src="{{ $customFooterUrl }}" alt="Certificate Footer Overlay" class="w-full h-auto max-h-24 sm:max-h-28 object-contain mx-auto block">
                            </div>
                        @else
                            <div class="pt-2 border-t border-slate-200 text-center">
                                <div class="h-1 bg-red-600 w-full mb-3"></div>
                                <div class="flex justify-center items-center gap-6 opacity-80">
                                    <span class="text-[10px] font-bold text-slate-600">DNV</span>
                                    <span class="text-[10px] font-bold text-slate-600">ABS</span>
                                    <span class="text-[10px] font-bold text-slate-600">BUREAU VERITAS</span>
                                    <span class="text-[10px] font-bold text-slate-600">LLOYD'S REGISTER</span>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            @endif

            <!-- VIEW 2: FORM INPUTS ARCHITECTURE PREVIEW -->
            <div x-show="previewTab === 'inputs'" class="space-y-6">
                @forelse($certificateTemplate->sections as $section)
                <div class="theme-card p-6 space-y-5">
                    <div class="pb-4 border-b border-slate-200 dark:border-slate-700/80 flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>{{ $section->name }}</span>
                                @if($section->is_repeatable)
                                    <span class="badge-info text-[10px]">Repeatable Grid</span>
                                @endif
                            </h3>
                            @if($section->description)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $section->description }}</p>
                            @endif
                        </div>
                        <span class="badge-neutral text-[10px] font-mono">{{ count($section->fields) }} Fields</span>
                    </div>

                    @if($section->layout_type === 'table' && $section->is_repeatable)
                        <div class="table-container overflow-x-auto">
                            <table class="theme-table">
                                <thead>
                                    <tr class="theme-table-header">
                                        @foreach($section->fields as $field)
                                        <th class="px-4 py-3 text-xs font-bold text-slate-700 dark:text-slate-300">
                                            <span>{{ $field->field_label }}</span>
                                            @if($field->is_required)
                                                <span class="text-amber-500 font-bold">*</span>
                                            @endif
                                        </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="theme-table-row">
                                        @foreach($section->fields as $field)
                                        <td class="px-4 py-3 text-xs">
                                            @if($field->field_type === 'dropdown')
                                                <select class="theme-input text-xs py-1.5" disabled>
                                                    <option>Select Option...</option>
                                                    @if($field->field_options && isset($field->field_options['options']))
                                                        @foreach($field->field_options['options'] as $opt)
                                                            <option>{{ $opt }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            @elseif($field->field_type === 'checkbox')
                                                <input type="checkbox" disabled class="rounded text-brand-600">
                                            @else
                                                <input type="{{ $field->field_type === 'number' ? 'number' : ($field->field_type === 'date' ? 'date' : 'text') }}" class="theme-input text-xs py-1.5" disabled placeholder="{{ $field->placeholder ?: $field->field_label }}">
                                            @endif
                                        </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="grid grid-cols-1 {{ $section->layout_type === 'two_column' ? 'md:grid-cols-2' : '' }} gap-5">
                            @foreach($section->fields as $field)
                            <div class="{{ $field->width === 'full' ? 'md:col-span-full' : ($field->width === 'third' ? 'md:col-span-1' : '') }}">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="theme-label mb-0">{{ $field->field_label }}</label>
                                    @if($field->is_required)
                                        <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/50 px-1.5 py-0.5 rounded">Required</span>
                                    @endif
                                </div>

                                @if($field->field_type === 'textarea')
                                    <textarea rows="3" class="theme-input text-xs" disabled placeholder="{{ $field->placeholder ?: 'Enter details...' }}"></textarea>
                                
                                @elseif($field->field_type === 'dropdown')
                                    <select class="theme-input text-xs py-2.5" disabled>
                                        <option value="">Select {{ $field->field_label }}...</option>
                                        @if($field->field_options && isset($field->field_options['options']))
                                            @foreach($field->field_options['options'] as $option)
                                                <option>{{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                
                                @elseif($field->field_type === 'checkbox')
                                    <label class="flex items-center gap-2.5 cursor-pointer py-1">
                                        <input type="checkbox" disabled class="rounded border-slate-300 text-brand-600 w-4 h-4">
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $field->placeholder ?: $field->field_label }}</span>
                                    </label>
                                
                                @else
                                    <input type="{{ $field->field_type === 'number' ? 'number' : ($field->field_type === 'date' ? 'date' : 'text') }}" class="theme-input text-xs py-2.5" disabled placeholder="{{ $field->placeholder ?: 'Enter ' . strtolower($field->field_label) . '...' }}">
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @empty
                <div class="theme-card py-16 text-center">
                    <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">No Form Sections Defined</h3>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>

@endif
