<x-app-layout>
<div class="container-fluid px-4">
    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6 print:hidden">
        <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Dispatch Note Details</h1>
        <div>
            <a href="{{ route('admin.dispatch.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white mr-4 transition-colors">Back</a>
            <a href="{{ route('admin.dispatch.pdf', $dispatchNote->id) }}" class="bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-red-700 flex items-center mr-2 transition-all active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Download PDF
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 flex items-center transition-all active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print
            </button>
        </div>
    </div>

    <!-- Printable Area -->
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl max-w-4xl mx-auto border border-gray-100 dark:border-gray-700 print:shadow-none print:w-full print:max-w-none print:p-0 print:border-none" id="print-area">
        
        <div class="flex-grow">
            <!-- Header Section -->
            <div class="flex justify-between items-start mb-10 border-b-2 border-gray-100 dark:border-gray-700 pb-8">
                <div class="w-1/2">
                    <div class="mb-4">
                        <h1 class="text-3xl font-black text-blue-900 dark:text-blue-400 tracking-tight">LOUIS MARINE</h1>
                        <h2 class="text-sm font-bold text-gray-400 dark:text-gray-500 tracking-widest uppercase">Equipment Repairing</h2>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                        <p class="flex items-center"><span class="w-4 h-4 mr-2 opacity-50 text-blue-500">📍</span>Street 41, Gate 42, Industrial Area</p>
                        <p class="ml-6">Doha, Qatar</p>
                        <p class="flex items-center"><span class="w-4 h-4 mr-2 opacity-50 text-blue-500">📞</span><strong>Tel:</strong> +974 44603388</p>
                        <p class="flex items-center"><span class="w-4 h-4 mr-2 opacity-50 text-blue-500">✉️</span><strong>Email:</strong> info@louismarine.qa</p>
                    </div>
                </div>
                
                <div class="w-1/2 text-right">
                    <h1 class="text-4xl font-black text-gray-800 dark:text-gray-100 uppercase tracking-tight mb-4 text-slate-800 dark:text-slate-100">Dispatch Note</h1>
                    <div class="inline-block bg-gray-50 dark:bg-gray-900 rounded-xl px-6 py-4 border border-gray-200 dark:border-gray-700 text-left min-w-[240px] shadow-sm">
                        <div class="flex justify-between mb-2">
                            <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Number:</span>
                            <span class="font-mono font-bold text-blue-900 dark:text-blue-400 text-lg">{{ $dispatchNote->dispatch_number }}</span>
                        </div>
                        <div class="flex justify-between mb-2 border-t border-gray-200 dark:border-gray-800 pt-2">
                            <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Date:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ optional($dispatchNote->date)->format('d M Y') ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 dark:border-gray-800 pt-2">
                            <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Type:</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">{{ $dispatchNote->type }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <!-- Recipient Box -->
                <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-2xl border border-gray-100 dark:border-gray-800 print:bg-transparent print:border-gray-300 shadow-sm transition-all hover:shadow-md">
                    <h3 class="text-xs font-black text-blue-900 dark:text-blue-400 uppercase tracking-widest mb-4 flex items-center">
                        <span class="bg-blue-900 dark:bg-blue-400 w-2 h-2 rounded-full mr-2"></span>
                        Dispatch To
                    </h3>
                    <div class="text-2xl font-black text-gray-800 dark:text-gray-100 mb-2">{{ $dispatchNote->receiver_name }}</div>
                    <div class="text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed text-lg">{{ $dispatchNote->receiver_address }}</div>
                    
                    @if($dispatchNote->serviceJob && $dispatchNote->serviceJob->vessel)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-tighter">Vessel</span>
                                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $dispatchNote->serviceJob->vessel->name }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-tighter">IMO Number</span>
                                <span class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $dispatchNote->serviceJob->vessel->imo_number }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Job Information -->
                <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-2xl border border-gray-100 dark:border-gray-800 print:bg-transparent print:border-gray-300 shadow-sm transition-all hover:shadow-md">
                    <h3 class="text-xs font-black text-blue-900 dark:text-blue-400 uppercase tracking-widest mb-4 flex items-center">
                        <span class="bg-blue-900 dark:bg-blue-400 w-2 h-2 rounded-full mr-2"></span>
                        Job Reference
                    </h3>
                    @if($dispatchNote->serviceJob)
                        <div class="space-y-4">
                            <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Service Job #</span>
                                <span class="font-mono font-bold text-blue-900 dark:text-blue-400">{{ $dispatchNote->serviceJob->job_number }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Process Type</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ $dispatchNote->serviceJob->job_type }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Status</span>
                                <span class="px-2 py-1 text-[10px] font-black uppercase rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">{{ $dispatchNote->status }}</span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500 italic text-sm">
                            No linked Service Job available.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-10">
                <h3 class="text-sm font-black text-gray-800 dark:text-gray-100 uppercase tracking-widest mb-4 flex items-center">
                    Items to Dispatch
                </h3>
                <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-700">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400 text-xs font-black uppercase tracking-wider print:bg-gray-200">
                                <th class="py-4 px-6 text-center w-16 border-r border-gray-100 dark:border-gray-700">#</th>
                                <th class="py-4 px-6 text-left border-r border-gray-100 dark:border-gray-700">Description</th>
                                <th class="py-4 px-6 text-left border-r border-gray-100 dark:border-gray-700">Serial No. / Asset Ref</th>
                                <th class="py-4 px-6 text-center w-24 border-r border-gray-100 dark:border-gray-700">Qty</th>
                                <th class="py-4 px-6 text-left">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($dispatchNote->items as $index => $item)
                            <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-900/10 dark:bg-gray-800">
                                <td class="py-4 px-6 text-center text-gray-400 dark:text-gray-500 font-mono border-r border-gray-100 dark:border-gray-700">{{ $index + 1 }}</td>
                                <td class="py-4 px-6 font-bold text-gray-800 dark:text-gray-200 border-r border-gray-100 dark:border-gray-700">{{ $item->description }}</td>
                                <td class="py-4 px-6 text-gray-600 dark:text-gray-400 font-mono text-xs border-r border-gray-100 dark:border-gray-700">{{ $item->serial_number ?: '-' }}</td>
                                <td class="py-4 px-6 text-center font-black text-blue-600 dark:text-blue-400 border-r border-gray-100 dark:border-gray-700">{{ $item->quantity }}</td>
                                <td class="py-4 px-6 text-gray-500 dark:text-gray-400 italic text-xs">{{ $item->remarks ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400 dark:text-gray-500 italic dark:bg-gray-800">No items listed in this dispatch note.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer / Signatures -->
        <div class="mt-16 flex justify-between items-end avoid-break">
            <div class="w-5/12 text-center">
                <div class="border-b-2 border-gray-100 dark:border-gray-700 mb-4 pb-16 relative">
                    {{-- Signature Space --}}
                </div>
                <p class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Authorized By</p>
                <p class="text-lg font-black text-gray-800 dark:text-gray-100">{{ $dispatchNote->creator->name ?? 'Louis Marine' }}</p>
                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">{{ optional($dispatchNote->created_at)->format('d M Y, H:i') }}</p>
            </div>
            
            <div class="w-5/12 text-center">
                 <div class="border-b-2 border-gray-100 dark:border-gray-700 mb-4 pb-16 relative">
                    {{-- Signature Space --}}
                </div>
                <p class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1">Received By</p>
                <p class="text-sm font-bold text-gray-400 dark:text-gray-500 italic">Seal, Signature & Date</p>
            </div>
        </div>

        <div class="mt-20 pt-8 border-t border-gray-100 dark:border-gray-700 text-center">
            <p class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest leading-loose">
                This is a computer-generated document. No manual signature is required for validity unless specified.
                <br>
                Louis Marine Equipment Repairing &copy; {{ date('Y') }} • Doha, Qatar
            </p>
        </div>
    </div>
</div>

<style>
@media print {
    /* 1. Global Reset & Settings */
    @page {
        margin: 0;
        size: A4 portrait;
    }
    
    html, body {
        height: auto !important;
        overflow: visible !important;
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* 2. Hide Web Elements */
    nav, header, footer, .no-print, button, iframe, 
    [class*="chat"], .fixed, .z-50, .bg-gray-900.bg-opacity-50, .print\:hidden {
        display: none !important;
    }

    /* 3. Print Area Container */
    #print-area {
        position: absolute !important;
        left: 0;
        top: 0;
        visibility: visible !important;
        width: 100% !important;
        min-height: 100% !important;
        margin: 0 !important;
        padding: 60px !important;
        box-shadow: none !important;
        border: none !important;
        background: white !important;
        overflow: visible !important;
        z-index: 10000;
        color: black !important;
    }
    
    #print-area * {
        visibility: visible;
        color: black !important;
        background: transparent !important;
        border-color: #eee !important;
    }

    .text-blue-900, .text-blue-400, .text-blue-600 {
        color: #1e3a8a !important;
    }

    thead tr {
        background-color: #f8fafc !important;
        border-bottom: 2px solid #1e3a8a !important;
    }
}
</style>
</x-app-layout>
