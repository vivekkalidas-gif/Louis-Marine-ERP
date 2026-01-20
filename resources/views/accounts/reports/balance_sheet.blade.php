<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Balance Sheet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('accounts.reports.balance_sheet') }}" class="flex items-end gap-4 flex-wrap">
                    <div class="w-full sm:w-1/3">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">As of Date</label>
                        <input type="date" name="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" value="{{ $date }}">
                    </div>
                     <div class="w-full sm:w-1/3">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Compare With (Optional)</label>
                        <input type="date" name="comparison_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" value="{{ request('comparison_date') }}">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update
                        </button>
                    </div>
                    <div class="ml-auto">
                         <button type="button" onclick="window.print()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print
                        </button>
                        <a href="{{ route('accounts.reports.balance_sheet.pdf', request()->all()) }}" class="ml-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Download PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- Report Container -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-lg print:shadow-none print:p-0" id="print-area">
                
                <!-- Report Header -->
                <div class="text-center mb-10 border-b pb-6">
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 uppercase tracking-wide">Louis Marine Equipment Repairing</h2>
                    <div class="mt-2 text-xl text-blue-600 font-semibold">Balance Sheet</div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">As of <span class="font-medium text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span></p>
                    @if(request('comparison_date'))
                         <p class="mt-1 text-xs text-gray-400">Comparing with <span class="font-medium text-gray-600">{{ \Carbon\Carbon::parse(request('comparison_date'))->format('d M Y') }}</span></p>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Assets -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200">
                            <h5 class="text-gray-800 dark:text-gray-200 font-bold text-lg uppercase tracking-wider flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Assets
                            </h5>
                        </div>
                        <div class="p-6">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th class="text-right text-xs text-gray-500 dark:text-gray-400 pb-2">Current</th>
                                        @if(request('comparison_date'))
                                            <th class="text-right text-xs text-gray-500 dark:text-gray-400 pb-2">Previous</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalAssets = 0; $totalPrevAssets = 0; @endphp
                                    @foreach($data['Assets'] as $account)
                                        @php 
                                            // Get Comparison Value
                                            $prevBalance = 0;
                                            if(request('comparison_date') && isset($data['Comparison_Assets'][$account->id])) {
                                                $prevBalance = $data['Comparison_Assets'][$account->id]->net_balance;
                                            }
                                        @endphp

                                        @if(abs($account->net_balance) > 0.01 || abs($prevBalance) > 0.01)
                                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                                                <td class="py-3 text-gray-700 dark:text-gray-300">
                                                    <a href="{{ route('accounts.reports.ledger', ['account' => $account, 'date' => $date]) }}" class="hover:text-blue-600 hover:underline font-medium">
                                                        {{ $account->name }}
                                                    </a>
                                                    <span class="text-gray-400 text-xs ml-1">#{{ $account->code }}</span>
                                                </td>
                                                <td class="text-right py-3 font-mono font-medium text-gray-900 dark:text-gray-100">{{ number_format($account->net_balance, 2) }}</td>
                                                @if(request('comparison_date'))
                                                    <td class="text-right py-3 font-mono text-gray-500 dark:text-gray-400">{{ number_format($prevBalance, 2) }}</td>
                                                @endif
                                            </tr>
                                        @php $totalAssets += $account->net_balance; $totalPrevAssets += $prevBalance; @endphp
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot class="border-t-2 border-gray-800">
                                    <tr>
                                        <td class="pt-4 font-bold text-gray-900 dark:text-gray-100 text-lg">Total Assets</td>
                                        <td class="text-right pt-4 font-bold text-green-700 text-lg font-mono">{{ number_format($totalAssets, 2) }}</td>
                                        @if(request('comparison_date'))
                                            <td class="text-right pt-4 font-bold text-gray-600 text-lg font-mono">{{ number_format($totalPrevAssets, 2) }}</td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Liabilities & Equity -->
                    <div class="space-y-8">
                        <!-- Liabilities -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200">
                                <h5 class="text-gray-800 dark:text-gray-200 font-bold text-lg uppercase tracking-wider flex items-center">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    Liabilities
                                </h5>
                            </div>
                            <div class="p-6">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th class="text-right text-xs text-gray-500 dark:text-gray-400 pb-2">Current</th>
                                            @if(request('comparison_date'))
                                                <th class="text-right text-xs text-gray-500 dark:text-gray-400 pb-2">Previous</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalLiabilities = 0; $totalPrevLiabilities = 0; @endphp
                                        @foreach($data['Liabilities'] as $account)
                                            @php 
                                                $prevBalance = 0;
                                                if(request('comparison_date') && isset($data['Comparison_Liabilities'][$account->id])) {
                                                    $prevBalance = $data['Comparison_Liabilities'][$account->id]->net_balance * -1;
                                                }
                                                $currentBalance = $account->net_balance * -1;
                                            @endphp

                                            @if(abs($currentBalance) > 0.01 || abs($prevBalance) > 0.01)
                                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                                                <td class="py-3 text-gray-700 dark:text-gray-300">
                                                    <a href="{{ route('accounts.reports.ledger', ['account' => $account, 'date' => $date]) }}" class="hover:text-blue-600 hover:underline font-medium">
                                                        {{ $account->name }}
                                                    </a>
                                                    <span class="text-gray-400 text-xs ml-1">#{{ $account->code }}</span>
                                                </td>
                                                <td class="text-right py-3 font-mono font-medium text-gray-900 dark:text-gray-100">{{ number_format($currentBalance, 2) }}</td>
                                                 @if(request('comparison_date'))
                                                    <td class="text-right py-3 font-mono text-gray-500 dark:text-gray-400">{{ number_format($prevBalance, 2) }}</td>
                                                @endif
                                            </tr>
                                            @php $totalLiabilities += $currentBalance; $totalPrevLiabilities += $prevBalance; @endphp
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot class="border-t-2 border-gray-800">
                                        <tr>
                                            <td class="pt-4 font-bold text-gray-900 dark:text-gray-100">Total Liabilities</td>
                                            <td class="text-right pt-4 font-bold text-gray-900 dark:text-gray-100 font-mono">{{ number_format($totalLiabilities, 2) }}</td>
                                            @if(request('comparison_date'))
                                                <td class="text-right pt-4 font-bold text-gray-600 font-mono">{{ number_format($totalPrevLiabilities, 2) }}</td>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Equity -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                             <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200">
                                <h5 class="text-gray-800 dark:text-gray-200 font-bold text-lg uppercase tracking-wider flex items-center">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    Equity
                                </h5>
                            </div>
                            <div class="p-6">
                                 <table class="w-full text-sm">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th class="text-right text-xs text-gray-500 dark:text-gray-400 pb-2">Current</th>
                                            @if(request('comparison_date'))
                                                <th class="text-right text-xs text-gray-500 dark:text-gray-400 pb-2">Previous</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalEquity = 0; $totalPrevEquity = 0; @endphp
                                        @foreach($data['Equity'] as $account)
                                            @php 
                                                $prevBalance = 0;
                                                if(request('comparison_date') && isset($data['Comparison_Equity'][$account->id])) {
                                                    $prevBalance = $data['Comparison_Equity'][$account->id]->net_balance * -1;
                                                }
                                                $currentBalance = $account->net_balance * -1;
                                            @endphp

                                            @if(abs($currentBalance) > 0.01 || abs($prevBalance) > 0.01)
                                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                                                <td class="py-3 text-gray-700 dark:text-gray-300">
                                                    <a href="{{ route('accounts.reports.ledger', ['account' => $account, 'date' => $date]) }}" class="hover:text-blue-600 hover:underline font-medium">
                                                        {{ $account->name }}
                                                    </a>
                                                    <span class="text-gray-400 text-xs ml-1">#{{ $account->code }}</span>
                                                </td>
                                                <td class="text-right py-3 font-mono font-medium text-gray-900 dark:text-gray-100">{{ number_format($currentBalance, 2) }}</td>
                                                @if(request('comparison_date'))
                                                    <td class="text-right py-3 font-mono text-gray-500 dark:text-gray-400">{{ number_format($prevBalance, 2) }}</td>
                                                @endif
                                            </tr>
                                            @php $totalEquity += $currentBalance; $totalPrevEquity += $prevBalance; @endphp
                                            @endif
                                        @endforeach
                                        
                                        <!-- Current Year Earnings -->
                                        @php
                                            $prevAdjustment = 0;
                                            if(request('comparison_date') && isset($data['Comparison_Equity_Adjustment'])) {
                                                $prevAdjustment = $data['Comparison_Equity_Adjustment'];
                                            }
                                        @endphp
                                        <tr class="bg-blue-50 font-semibold border-b border-gray-100">
                                            <td class="py-3 pl-2 text-blue-900">Current Year Earnings</td>
                                            <td class="text-right py-3 font-mono text-blue-900">{{ number_format($data['Equity_Adjustment'], 2) }}</td>
                                            @if(request('comparison_date'))
                                                <td class="text-right py-3 font-mono text-blue-800">{{ number_format($prevAdjustment, 2) }}</td>
                                            @endif
                                        </tr>
                                        @php $totalEquity += $data['Equity_Adjustment']; $totalPrevEquity += $prevAdjustment; @endphp

                                    </tbody>
                                    <tfoot class="border-t-2 border-gray-800">
                                        <tr>
                                            <td class="pt-4 font-bold text-gray-900 dark:text-gray-100">Total Equity</td>
                                            <td class="text-right pt-4 font-bold text-gray-900 dark:text-gray-100 font-mono">{{ number_format($totalEquity, 2) }}</td>
                                            @if(request('comparison_date'))
                                                <td class="text-right pt-4 font-bold text-gray-600 font-mono">{{ number_format($totalPrevEquity, 2) }}</td>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Total Liabilities + Equity -->
                        <div class="bg-gray-800 text-white rounded-lg px-6 py-4 flex justify-between items-center text-lg shadow-md">
                            <span class="font-bold uppercase tracking-wider">Total Liab + Equity</span>
                            <div class="text-right">
                                <div class="font-mono font-bold">{{ number_format($totalLiabilities + $totalEquity, 2) }}</div>
                                @if(request('comparison_date'))
                                    <div class="text-xs text-gray-400 font-mono">{{ number_format($totalPrevLiabilities + $totalPrevEquity, 2) }} (Prev)</div>
                                @endif
                            </div>
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
