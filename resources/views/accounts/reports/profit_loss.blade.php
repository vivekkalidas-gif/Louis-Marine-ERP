<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profit & Loss Statement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('accounts.reports.profit_loss') }}" class="flex items-end gap-4 flex-wrap">
                    <div class="w-full sm:w-1/4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Start Date</label>
                        <input type="date" name="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" value="{{ $startDate }}">
                    </div>
                    <div class="w-full sm:w-1/4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">End Date</label>
                        <input type="date" name="end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" value="{{ $endDate }}">
                    </div>
                     <div class="w-full sm:w-1/4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Comparison Start (Optional)</label>
                        <input type="date" name="compare_start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" value="{{ $compareStartDate }}">
                    </div>
                     <div class="w-full sm:w-1/4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Comparison End (Optional)</label>
                        <input type="date" name="compare_end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" value="{{ $compareEndDate }}">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Generate Report
                        </button>
                    </div>
                    <div class="ml-auto flex gap-2">
                         <button type="button" onclick="window.print()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print
                        </button>
                        <a href="{{ route('accounts.reports.profit_loss.pdf', request()->all()) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- Report Container -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-lg print:shadow-none print:p-0" id="print-area">
                
                <!-- Report Header -->
                <div class="text-center mb-10 border-b pb-6">
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 uppercase tracking-wide">Louis Marine Equipment Repairing</h2>
                    <div class="mt-2 text-xl text-blue-600 font-semibold">Profit & Loss Statement</div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Period: <span class="font-medium text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> to <span class="font-medium text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span></p>
                    @if($compareStartDate)
                         <p class="mt-1 text-xs text-gray-400">Comparing with: {{ \Carbon\Carbon::parse($compareStartDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($compareEndDate)->format('d M Y') }}</p>
                    @endif
                </div>

                <div class="space-y-6">
                    
                    <!-- 1. Income -->
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 border-b pb-1">Operating Income</h4>
                        <table class="w-full text-sm">
                             <thead>
                                <tr>
                                    <th class="text-left font-medium text-gray-500 dark:text-gray-400 pb-2">Account</th>
                                     <th class="text-right font-medium text-gray-500 dark:text-gray-400 pb-2">Amount</th>
                                    @if($compareStartDate)
                                        <th class="text-right font-medium text-gray-500 dark:text-gray-400 pb-2">Previous</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['income'] as $account)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 border-b border-gray-50 last:border-0">
                                        <td class="py-2 pl-2">
                                            <a href="{{ route('accounts.reports.ledger', ['account' => $account, 'date' => $endDate]) }}" class="hover:text-blue-600 hover:underline">
                                                {{ $account->name }}
                                            </a>
                                            <span class="text-gray-400 text-xs ml-1">#{{ $account->code }}</span>
                                        </td>
                                        <td class="text-right py-2 font-mono">{{ number_format($account->net_balance, 2) }}</td>
                                        @if($compareStartDate)
                                            <td class="text-right py-2 font-mono text-gray-500 dark:text-gray-400">
                                                {{ isset($data['comparison']['income'][$account->id]) ? number_format($data['comparison']['income'][$account->id]->net_balance, 2) : '-' }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody> <!-- Fix: added closing tag -->
                             <tfoot class="border-t border-gray-300">
                                <tr class="font-bold text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700">
                                    <td class="py-3 pl-2">Total Income</td>
                                    <td class="text-right py-3 font-mono">{{ number_format($data['totals']['income'], 2) }}</td>
                                     @if($compareStartDate)
                                        <td class="text-right py-3 font-mono">{{ number_format($data['comparison']['totals']['income'], 2) }}</td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- 2. Cost of Goods Sold -->
                    @if($data['cogs']->isNotEmpty())
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 border-b pb-1">Cost of Goods Sold</h4>
                        <table class="w-full text-sm">
                            <tbody>
                                @foreach($data['cogs'] as $account)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 border-b border-gray-50 last:border-0">
                                        <td class="py-2 pl-2">
                                            <a href="{{ route('accounts.reports.ledger', ['account' => $account, 'date' => $endDate]) }}" class="hover:text-blue-600 hover:underline">
                                                {{ $account->name }}
                                            </a>
                                            <span class="text-gray-400 text-xs ml-1">#{{ $account->code }}</span>
                                        </td>
                                        <td class="text-right py-2 font-mono">{{ number_format($account->net_balance, 2) }}</td>
                                         @if($compareStartDate)
                                            <td class="text-right py-2 font-mono text-gray-500 dark:text-gray-400">
                                                {{ isset($data['comparison']['cogs'][$account->id]) ? number_format($data['comparison']['cogs'][$account->id]->net_balance, 2) : '-' }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                             <tfoot class="border-t border-gray-300">
                                <tr class="font-bold text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700">
                                    <td class="py-3 pl-2">Total COGS</td>
                                    <td class="text-right py-3 font-mono">{{ number_format($data['totals']['cogs'], 2) }}</td>
                                     @if($compareStartDate)
                                        <td class="text-right py-3 font-mono">{{ number_format($data['comparison']['totals']['cogs'], 2) }}</td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif

                    <!-- GROSS PROFIT -->
                     <div class="bg-gray-100 rounded-lg p-4 flex justify-between items-center border border-gray-200">
                        <span class="font-bold text-lg uppercase">Gross Profit</span>
                        <div class="text-right">
                             <span class="font-mono font-bold text-xl">{{ number_format($data['totals']['gross_profit'], 2) }}</span>
                             @if($compareStartDate)
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ number_format($data['comparison']['totals']['gross_profit'], 2) }} (Prev)</div>
                            @endif
                        </div>
                    </div>

                    <!-- 3. Expenses -->
                     <div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 border-b pb-1">Operating Expenses</h4>
                        <table class="w-full text-sm">
                            <tbody>
                                @foreach($data['expenses'] as $account)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 border-b border-gray-50 last:border-0">
                                        <td class="py-2 pl-2">
                                            <a href="{{ route('accounts.reports.ledger', ['account' => $account, 'date' => $endDate]) }}" class="hover:text-blue-600 hover:underline">
                                                {{ $account->name }}
                                            </a>
                                            <span class="text-gray-400 text-xs ml-1">#{{ $account->code }}</span>
                                        </td>
                                        <td class="text-right py-2 font-mono">{{ number_format($account->net_balance, 2) }}</td>
                                        @if($compareStartDate)
                                            <td class="text-right py-2 font-mono text-gray-500 dark:text-gray-400">
                                                {{ isset($data['comparison']['expenses'][$account->id]) ? number_format($data['comparison']['expenses'][$account->id]->net_balance, 2) : '-' }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                             <tfoot class="border-t border-gray-300">
                                <tr class="font-bold text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700">
                                    <td class="py-3 pl-2">Total Expenses</td>
                                    <td class="text-right py-3 font-mono">{{ number_format($data['totals']['expenses'], 2) }}</td>
                                     @if($compareStartDate)
                                        <td class="text-right py-3 font-mono">{{ number_format($data['comparison']['totals']['expenses'], 2) }}</td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- NET PROFIT -->
                    <div class="bg-gray-800 text-white rounded-lg p-6 flex justify-between items-center shadow-lg">
                        <div>
                             <h3 class="text-2xl font-bold uppercase tracking-wider">Net Profit / (Loss)</h3>
                             <p class="text-gray-400 text-sm mt-1">Gross Profit - Operating Expenses</p>
                        </div>
                        <div class="text-right">
                            <h3 class="text-4xl font-mono font-extrabold {{ $data['totals']['net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ number_format($data['totals']['net_profit'], 2) }}
                            </h3>
                             @if($compareStartDate)
                                <div class="text-sm text-gray-400 font-mono mt-1">{{ number_format($data['comparison']['totals']['net_profit'], 2) }} (Prev)</div>
                            @endif
                        </div>
                    </div>

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
