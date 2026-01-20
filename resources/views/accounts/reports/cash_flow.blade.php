<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cash Flow Statement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex items-end gap-4">
                    <div class="w-1/3">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="w-1/3">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Filter
                        </button>
                    </div>
                    <div class="ml-auto">
                        <button type="button" onclick="window.print()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print
                        </button>
                        <a href="{{ route('accounts.reports.cash_flow.pdf', request()->all()) }}" class="ml-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Download PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- Report Body -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-sm print:shadow-none print:p-0" id="print-area">
                
                <!-- Report Header -->
                <div class="text-center mb-10 border-b pb-6">
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 uppercase tracking-wide">Louis Marine Equipment Repairing</h2>
                    <div class="mt-2 text-xl text-blue-600 font-semibold">Statement of Cash Flows</div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For the period <span class="font-medium text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> to <span class="font-medium text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span></p>
                </div>

                <div class="overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-800">
                                <th class="py-3 text-lg font-bold text-gray-800 dark:text-gray-200">Activities</th>
                                <th class="py-3 text-right w-48 text-gray-600 font-semibold">Inflow (+)</th>
                                <th class="py-3 text-right w-48 text-gray-600 font-semibold">Outflow (-)</th>
                                <th class="py-3 text-right w-48 font-bold text-gray-800 dark:text-gray-200">Net Cash</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-gray-300">
                            <!-- Operating -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors border-b border-gray-100">
                                <td class="py-4 px-2 font-medium">Cash Flows from Operating Activities</td>
                                <td class="text-right px-2 font-mono">{{ number_format($activities['operating']['in'], 2) }}</td>
                                <td class="text-right px-2 font-mono text-red-500">
                                    @if($activities['operating']['out'] > 0)
                                        ({{ number_format($activities['operating']['out'], 2) }})
                                    @else
                                        0.00
                                    @endif
                                </td>
                                <td class="text-right px-2 font-bold font-mono {{ $summary['operating_net'] < 0 ? 'text-red-700' : 'text-green-700' }}">{{ number_format($summary['operating_net'], 2) }}</td>
                            </tr>

                            <!-- Investing -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors border-b border-gray-100">
                                <td class="py-4 px-2 font-medium">Cash Flows from Investing Activities</td>
                                <td class="text-right px-2 font-mono">{{ number_format($activities['investing']['in'], 2) }}</td>
                                <td class="text-right px-2 font-mono text-red-500">
                                     @if($activities['investing']['out'] > 0)
                                        ({{ number_format($activities['investing']['out'], 2) }})
                                    @else
                                        0.00
                                    @endif
                                </td>
                                <td class="text-right px-2 font-bold font-mono {{ $summary['investing_net'] < 0 ? 'text-red-700' : 'text-green-700' }}">{{ number_format($summary['investing_net'], 2) }}</td>
                            </tr>

                            <!-- Financing -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors border-b border-gray-100">
                                <td class="py-4 px-2 font-medium">Cash Flows from Financing Activities</td>
                                <td class="text-right px-2 font-mono">{{ number_format($activities['financing']['in'], 2) }}</td>
                                <td class="text-right px-2 font-mono text-red-500">
                                     @if($activities['financing']['out'] > 0)
                                        ({{ number_format($activities['financing']['out'], 2) }})
                                    @else
                                        0.00
                                    @endif
                                </td>
                                <td class="text-right px-2 font-bold font-mono {{ $summary['financing_net'] < 0 ? 'text-red-700' : 'text-green-700' }}">{{ number_format($summary['financing_net'], 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                             <!-- Net Change -->
                            <tr class="border-t-2 border-gray-800 bg-gray-50 dark:bg-gray-700">
                                <td class="py-4 px-2 font-bold text-lg text-gray-900 dark:text-gray-100">Net Increase (Decrease) in Cash</td>
                                <td colspan="2"></td>
                                <td class="text-right px-2 font-bold text-lg font-mono {{ $summary['net_change'] < 0 ? 'text-red-600' : 'text-blue-900' }}">
                                    {{ number_format($summary['net_change'], 2) }}
                                </td>
                            </tr>

                            <!-- Reconciliation -->
                            <tr>
                                <td class="py-3 px-2 text-gray-600 italic">Cash at Beginning of Period</td>
                                <td colspan="2"></td>
                                <td class="text-right px-2 font-mono text-gray-700 dark:text-gray-300">{{ number_format($openingBalance, 2) }}</td>
                            </tr>
                             <tr class="bg-green-50">
                                <td class="py-4 px-2 font-bold text-green-900 text-lg">Cash at End of Period</td>
                                <td colspan="2"></td>
                                <td class="text-right px-2 font-bold text-lg font-mono text-green-900 border-b-4 border-double border-green-600">{{ number_format($closingBalance, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-100 flex justify-between items-center text-xs text-gray-400">
                    <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
                    <p>Louis Marine Qatar - Internal Document</p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</x-app-layout>
