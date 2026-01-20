<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Trial Balance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('accounts.reports.trial_balance') }}" class="flex items-end gap-4">
                    <div class="w-1/3">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">As of Date</label>
                        <input type="date" name="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" value="{{ $date }}">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            <!-- Trial Balance Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-8" id="print-area">
                <div class="flex justify-between items-start mb-8 border-b pb-6">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 uppercase">Louis Marine Equipment Repairing</h2>
                        <h3 class="text-xl text-blue-600 font-semibold mt-1">Trial Balance</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">As of <span class="font-medium text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span></p>
                    </div>
                    <button onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded inline-flex items-center transition-colors no-print">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print
                    </button>
                    <a href="{{ route('accounts.reports.trial_balance.pdf', request()->all()) }}" class="ml-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center no-print">
                         <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Download PDF
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-800">
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Code</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Account Name</th>
                                <th class="px-3 py-3 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-32">Debit</th>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-32">Credit</th>
                                <th class="px-3 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-32">Net Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100">
                            @php 
                                $totalDebit = 0; 
                                $totalCredit = 0; 
                            @endphp
                            @foreach($accounts as $account)
                                @if(abs($account->total_debit) > 0 || abs($account->total_credit) > 0)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $account->code }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $account->name }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400 uppercase">
                                        {{ str_replace('_', ' ', $account->type) }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-right text-gray-600 font-mono">{{ number_format($account->total_debit, 2) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-right text-gray-600 font-mono">{{ number_format($account->total_credit, 2) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-right font-bold font-mono {{ $account->net_balance < 0 ? 'text-red-700' : 'text-green-700' }}">
                                        {{ number_format($account->net_balance, 2) }}
                                    </td>
                                </tr>
                                @php 
                                    $totalDebit += $account->total_debit; 
                                    $totalCredit += $account->total_credit; 
                                @endphp
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700 font-bold border-t-2 border-gray-800">
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-right text-sm text-gray-900 dark:text-gray-100 uppercase tracking-wide">Totals:</td>
                                <td class="px-3 py-4 text-right text-sm text-gray-900 dark:text-gray-100 font-mono">{{ number_format($totalDebit, 2) }}</td>
                                <td class="px-3 py-4 text-right text-sm text-gray-900 dark:text-gray-100 font-mono">{{ number_format($totalCredit, 2) }}</td>
                                <td class="px-3 py-4 text-right text-sm text-gray-900 dark:text-gray-100 font-mono">{{ number_format($totalDebit - $totalCredit, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if(abs($totalDebit - $totalCredit) > 0.01)
                    <div class="mt-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                        <p class="font-bold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Unbalanced Trial Balance
                        </p>
                        <p class="mt-1 text-sm">The difference is <span class="font-mono font-bold">{{ number_format($totalDebit - $totalCredit, 2) }}</span>. Please audit unposted journals.</p>
                    </div>
                @endif

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center text-xs text-gray-400">
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
