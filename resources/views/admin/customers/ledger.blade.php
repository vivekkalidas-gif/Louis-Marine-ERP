<x-app-layout>
    <x-slot name="header">
    <x-slot name="header">
        <div class="flex justify-between items-center print:hidden">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $customer->company_name }} - Ledger
            </h2>
            <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded font-bold hover:bg-gray-700">Print Statement</button>
        </div>
        <style>
            @media print {
                @page { margin: 0.5cm; size: A4; }
                body { background: white; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; }
                
                /* Hide Web Elements */
                nav, header, footer, .print\:\hidden, button, .web-container { display: none !important; }
                
                /* Show Print Elements */
                .print-container { display: block !important; }
                
                /* Reset Container */
                .max-w-7xl, .py-12 { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
                
                /* Typography */
                h1, h2, h3, h4, h5, h6 { color: #0f172a; margin: 0; }
                p { margin: 0; line-height: 1.5; }
                
                /* Helper Classes */
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .font-bold { font-weight: 700; }
                .uppercase { text-transform: uppercase; }
                .mb-4 { margin-bottom: 1rem; }
                .mt-4 { margin-top: 1rem; }
                .border-b { border-bottom: 1px solid #e2e8f0; }
            }
            .print-container { display: none; }
        </style>
    </x-slot>

    <!-- PRINT ONLY VIEW -->
    <div class="print-container p-8">
        <!-- Letterhead -->
        <div class="flex justify-between items-start border-b pb-6 mb-8">
            <div class="w-1/2">
                <h1 class="text-3xl font-bold text-slate-800 tracking-tight">LOUIS MARINE</h1>
                <p class="text-sm text-slate-500 font-medium tracking-widest uppercase mt-1">Equipment & Services W.L.L</p>
            </div>
            <div class="w-1/2 text-right text-sm text-slate-600">
                <p>Al Khor, Qatar</p>
                <p>Tel: +974 1234 5678</p>
                <p>Email: info@louismarine.com</p>
                <p>C.R. No: 12345</p>
            </div>
        </div>

        <!-- Statement Details -->
        <div class="flex justify-between items-start mb-8">
            <div class="w-1/2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Bill To</p>
                <h2 class="text-xl font-bold text-slate-800">{{ $customer->company_name }}</h2>
                <p class="text-sm text-slate-600 mt-1">{{ $customer->address ?? 'Address on File' }}</p>
                @if($customer->vat_number)
                <p class="text-sm text-slate-600">VAT: {{ $customer->vat_number }}</p>
                @endif
            </div>
            <div class="w-1/2 text-right">
                <h2 class="text-2xl font-bold text-slate-800 uppercase mb-4">Statement of Account</h2>
                <div class="inline-block text-left">
                    <p class="text-sm"><span class="font-bold text-slate-600 w-24 inline-block">Date:</span> {{ now()->format('d M Y') }}</p>
                    <p class="text-sm"><span class="font-bold text-slate-600 w-24 inline-block">Account No:</span> {{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        <!-- Summary Box -->
        <div class="bg-slate-50 border border-slate-200 rounded p-4 mb-8 flex justify-between text-center">
            <div class="w-1/4 border-r border-slate-200">
                <p class="text-xs font-bold text-slate-500 uppercase">Opening Balance</p>
                <p class="text-lg font-bold text-slate-700">QAR 0.00</p>
            </div>
            <div class="w-1/4 border-r border-slate-200">
                <p class="text-xs font-bold text-slate-500 uppercase">Invoiced</p>
                <p class="text-lg font-bold text-slate-700">QAR {{ number_format($ledger->where('type', 'invoice')->sum('debit'), 2) }}</p>
            </div>
            <div class="w-1/4 border-r border-slate-200">
                <p class="text-xs font-bold text-slate-500 uppercase">Received</p>
                <p class="text-lg font-bold text-slate-700">QAR {{ number_format($ledger->where('type', 'payment')->sum('credit'), 2) }}</p>
            </div>
            <div class="w-1/4">
                <p class="text-xs font-bold text-slate-500 uppercase">Amount Due</p>
                <p class="text-xl font-bold {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">QAR {{ number_format($balance, 2) }}</p>
            </div>
        </div>

        <!-- Main Table -->
        <table class="w-full text-sm mb-8">
            <thead>
                <tr class="border-b-2 border-slate-800">
                    <th class="text-left py-2 font-bold text-slate-800 w-32">DATE</th>
                    <th class="text-left py-2 font-bold text-slate-800">DESCRIPTION</th>
                    <th class="text-left py-2 font-bold text-slate-800 w-32">REFERENCE</th>
                    <th class="text-right py-2 font-bold text-slate-800 w-32">DEBIT</th>
                    <th class="text-right py-2 font-bold text-slate-800 w-32">CREDIT</th>
                    <th class="text-right py-2 font-bold text-slate-800 w-32">BALANCE</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ledger as $entry)
                <tr class="border-b border-slate-100">
                    <td class="py-3 text-slate-600">{{ $entry->date->format('d M Y') }}</td>
                    <td class="py-3 font-medium text-slate-800">
                        {{ $entry->type == 'invoice' ? 'Invoice Generated' : ($entry->type == 'payment' ? 'Payment Received' : $entry->description) }}
                    </td>
                    <td class="py-3 text-slate-600">
                        @if($entry->reference && $entry->type == 'invoice')
                            {{ $entry->reference->invoice_number }}
                        @elseif($entry->reference && $entry->type == 'payment')
                            {{ $entry->reference->payment_number }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-3 text-right text-slate-700">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                    <td class="py-3 text-right text-slate-700">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
                    <td class="py-3 text-right font-bold text-slate-900">{{ number_format($entry->running_balance, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Remittance / Footer -->
        <div class="mt-12 text-xs text-slate-500">
            <div class="flex justify-between">
                <div class="w-1/2">
                    <h4 class="font-bold text-slate-800 uppercase mb-2">Payment Details</h4>
                    <p>Bank Name: QNB</p>
                    <p>Account Name: Louis Marine Equipment & Services W.L.L</p>
                    <p>IBAN: QA00 QNBA 0000 0000 0000 0000</p>
                </div>
                <div class="w-1/2 text-right">
                    <p>This is a computer-generated statement and requires no signature.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- WEB VIEW (Original) -->
    <div class="py-12 web-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    
                    <div class="mb-6 flex justify-between items-center statement-info">
                        <div class="text-sm">
                            <h3 class="font-bold text-xl text-marine-700 mb-1">Statement of Account</h3>
                            <p class="text-gray-600">Customer: <span class="font-bold text-gray-900 dark:text-gray-100">{{ $customer->company_name }}</span></p>
                            <p class="text-gray-600">Date: <span class="text-gray-900 dark:text-gray-100">{{ now()->format('d M Y') }}</span></p>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Closing Balance</span>
                            <span class="text-2xl font-bold {{ $balance > 0 ? 'text-red-700' : 'text-green-700' }}">QAR {{ number_format($balance, 2) }}</span>
                        </div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-marine-700 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider rounded-tl-lg">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider">Debit</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider">Credit</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider rounded-tr-lg">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                            @foreach ($ledger as $entry)
                            <tr class="even:bg-marine-50 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                    {{ $entry->date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                    <div class="flex items-center">
                                        @if($entry->type == 'invoice')
                                            <span class="px-2 py-1 text-xs font-bold uppercase text-white bg-blue-600 rounded mr-3 w-16 text-center">INV</span>
                                            <span class="font-medium">Invoice #{{ $entry->reference ? $entry->reference->invoice_number : '' }}</span>
                                        @elseif($entry->type == 'payment')
                                            <span class="px-2 py-1 text-xs font-bold uppercase text-white bg-green-600 rounded mr-3 w-16 text-center">PAY</span>
                                            <span class="font-medium">Payment Received</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-bold uppercase text-gray-600 bg-gray-200 rounded mr-3 w-16 text-center">{{ substr($entry->type, 0, 3) }}</span>
                                            <span>{{ $entry->description }}</span>
                                        @endif
                                    </div>
                                    @if($entry->description && $entry->type != 'invoice' && $entry->type != 'payment')
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 pl-20">{{ $entry->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-700 dark:text-gray-300">
                                    {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-green-600">
                                    {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-gray-100">
                                    {{ number_format($entry->running_balance, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700 border-t-2 border-marine-200">
                             <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide pt-6">Outstanding Balance</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap pt-6">
                                    <span class="text-2xl font-bold {{ $balance > 0 ? 'text-red-700' : 'text-green-700' }}">
                                        QAR {{ number_format($balance, 2) }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
