<x-app-layout>
<div class="container-fluid px-4">
    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6 print:hidden">
        <h1 class="text-3xl font-bold text-slate-800">Dispatch Note Details</h1>
        <div>
            <a href="{{ route('admin.dispatch.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-100 mr-4">Back</a>
            <button onclick="window.print()" class="bg-blue-600 text-white font-bold py-2 px-4 rounded shadow hover:bg-blue-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print / Save as PDF
            </button>
        </div>
    </div>

    <!-- Printable Area -->
    <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-lg max-w-4xl mx-auto print:shadow-none print:w-full print:max-w-none print:p-0" id="print-area">
        
        <div class="flex-grow">
            <!-- Header Section -->
            <div class="flex justify-between items-start mb-8 border-b-2 border-gray-800 pb-6">
                <div class="w-1/2">
                    {{-- Placeholder Logo - Replace with actual img tag if available --}}
                    <div class="mb-3">
                        <h1 class="text-2xl font-extrabold text-blue-900 tracking-tight">LOUIS MARINE</h1>
                        <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 tracking-widest uppercase">Equipment Repairing</h2>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>Street 41, Gate 42, Industrial Area</p>
                        <p>Doha, Qatar</p>
                        <p><strong>Tel:</strong> +974 1234 5678</p>
                        <p><strong>Email:</strong> info@louismarine.com</p>
                    </div>
                </div>
                
                <div class="w-1/2 text-right">
                    <h1 class="text-4xl font-black text-gray-800 dark:text-gray-200 uppercase tracking-tight mb-2">Dispatch Note</h1>
                    <div class="inline-block bg-gray-100 rounded px-4 py-2 border border-gray-200 text-left min-w-[200px]">
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Number:</span>
                            <span class="font-mono font-bold text-blue-900">{{ $dispatchNote->dispatch_number }}</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Date:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ optional($dispatchNote->date)->format('d-M-Y') ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Type:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $dispatchNote->type }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Grid -->
            <div class="grid grid-cols-2 gap-8 mb-8">
                <!-- From (Implicitly Us) -->
                {{-- Optional: Can replicate 'From' here if needed, but usually redundant with header --}}
                
                <!-- To / Receiver -->
                <div class="col-span-2 bg-gray-50 dark:bg-gray-700 p-6 rounded border border-gray-200 print:bg-transparent print:border-gray-300">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 border-b border-gray-200 pb-1 w-full">Dispatch To</h3>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-1">{{ $dispatchNote->receiver_name }}</div>
                    <div class="text-gray-600 whitespace-pre-line leading-relaxed">{{ $dispatchNote->receiver_address }}</div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-8">
                <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider mb-2">Item Details</h3>
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 dark:text-gray-300 text-xs uppercase tracking-wider print:bg-gray-200">
                            <th class="border border-gray-300 py-3 px-4 text-center w-16">#</th>
                            <th class="border border-gray-300 py-3 px-4 text-left">Description</th>
                            <th class="border border-gray-300 py-3 px-4 text-left">Serial No. / Asset Ref</th>
                            <th class="border border-gray-300 py-3 px-4 text-center w-20">Qty</th>
                            <th class="border border-gray-300 py-3 px-4 text-left">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($dispatchNote->items as $index => $item)
                        <tr class="border-b border-gray-200 {{ $loop->even ? 'bg-gray-50 dark:bg-gray-700 print:bg-transparent' : '' }}">
                            <td class="border border-gray-300 py-3 px-4 text-center text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 py-3 px-4 font-medium text-gray-800 dark:text-gray-200">{{ $item->description }}</td>
                            <td class="border border-gray-300 py-3 px-4 text-gray-600 font-mono">{{ $item->serial_number ?: '-' }}</td>
                            <td class="border border-gray-300 py-3 px-4 text-center font-bold text-gray-800 dark:text-gray-200">{{ $item->quantity }}</td>
                            <td class="border border-gray-300 py-3 px-4 text-gray-600 italic">{{ $item->remarks ?: '' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="border border-gray-300 py-6 text-center text-gray-400 italic">No items listed.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer / Signatures -->
        <div class="mt-12 flex justify-between items-end avoid-break">
            <div class="w-5/12">
                <div class="border-b border-gray-400 mb-2 pb-12">
                    {{-- Signature Space --}}
                </div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Authorized By</p>
                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $dispatchNote->creator->name ?? 'Louis Marine' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
            </div>
            
            <div class="w-5/12">
                 <div class="border-b border-gray-400 mb-2 pb-12">
                    {{-- Signature Space --}}
                </div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Received By</p>
                <p class="text-sm text-gray-400 italic">Name, Signature & Stamp</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Date: _________________</p>
            </div>
        </div>

        <div class="mt-16 pt-4 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-400">
                This is a computer-generated document. No signature is required unless specified.
                <br>
                Louis Marine Equipment Repairing &copy; {{ date('Y') }}
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
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Professional Font */
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
        padding: 40px !important;
        box-shadow: none !important;
        border: none !important;
        background: white !important;
        overflow: visible !important;
        z-index: 10000;
        
        /* Flex Layout for Sticky Footer */
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
    }
    #print-area * {
        visibility: visible;
    }

    /* 4. Typography & Branding */
    h1, h2, h3, h4 {
        color: #1e3a8a !important; /* Deep Navy Blue (blue-900) */
    }
    
    p, span, div, td {
        color: #1f2937 !important; /* Dark Gray for text transparency */
    }

    /* Force specific text colors */
    .text-blue-900 {
        color: #1e3a8a !important;
    }
    .text-gray-500 dark:text-gray-400 {
        color: #6b7280 !important;
    }

    /* 5. Header Styling Overrides */
    /* Target the table header specifically to make it Blue with White Text */
    thead tr th {
        background-color: #1e3a8a !important; /* bg-blue-900 */
        color: #ffffff !important; /* White Text */
        border: 1px solid #1e3a8a !important; /* Match background */
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 8px 12px !important;
    }

    /* 6. Table Styling */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
        margin-bottom: 20px !important;
    }

    /* Borders for Cells - Use Light Blue instead of Black */
    td {
        border-bottom: 1px solid #bfdbfe !important; /* border-blue-200 */
        border-right: 1px solid #e5e7eb !important; /* light gray vertical */
        border-left: 1px solid #e5e7eb !important;
        padding: 10px 12px !important;
    }
    td:first-child { border-left: 1px solid #bfdbfe !important; }
    td:last-child { border-right: 1px solid #bfdbfe !important; }
    
    /* Remove row backgrounds to keep it clean white */
    tbody tr {
        background-color: transparent !important;
    }
    tbody tr:last-child td {
        border-bottom: 1px solid #1e3a8a !important; /* Strong bottom border for table */
    }

    /* 7. Specific adjustments for Layout components */
    /* Ensure the top header border is visible and blue */
    .border-gray-800 {
        border-color: #1e3a8a !important;
    }
    
    /* Dispatch Info Box styling */
    .bg-gray-100.rounded {
        background-color: #eff6ff !important; /* Very light blue bg */
        border: 1px solid #bfdbfe !important;
    }

    /* Address Box styling */
    .bg-gray-50 dark:bg-gray-700.rounded {
        background-color: transparent !important;
        border: 1px dashed #bfdbfe !important;
    }

    /* Footer / Signatures */
    .border-gray-400 {
        border-color: #93c5fd !important; /* Blue-300 for signature lines */
    }
    .avoid-break {
        page-break-inside: avoid;
    }
}
</style>
</x-app-layout>
