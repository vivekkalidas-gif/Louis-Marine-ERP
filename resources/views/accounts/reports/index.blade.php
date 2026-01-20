<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Financial Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Report Direct Links -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <a href="{{ route('accounts.reports.balance_sheet') }}" class="bg-white dark:bg-gray-800 p-6 shadow-sm rounded-lg border-l-4 border-blue-500 hover:shadow-md transition">
                    <div class="text-blue-600 dark:text-blue-400 font-bold text-lg mb-1">Balance Sheet</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Assets, Liabilities, and Equity overview</div>
                </a>

                <a href="{{ route('accounts.reports.profit_loss') }}" class="bg-white dark:bg-gray-800 p-6 shadow-sm rounded-lg border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="text-green-600 dark:text-green-400 font-bold text-lg mb-1">Profit & Loss</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Income and Expenses statement</div>
                </a>

                <a href="{{ route('accounts.reports.trial_balance') }}" class="bg-white dark:bg-gray-800 p-6 shadow-sm rounded-lg border-l-4 border-indigo-500 hover:shadow-md transition">
                    <div class="text-indigo-600 dark:text-indigo-400 font-bold text-lg mb-1">Trial Balance</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">View account balances and ensure inputs match outputs</div>
                </a>

                <a href="{{ route('accounts.reports.general_ledger') }}" class="bg-white dark:bg-gray-800 p-6 shadow-sm rounded-lg border-l-4 border-purple-500 hover:shadow-md transition">
                    <div class="text-purple-600 dark:text-purple-400 font-bold text-lg mb-1">General Ledger</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Detailed transaction history</div>
                </a>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Total Revenue</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">QAR {{ number_format($totalRevenue, 2) }}</div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Outstanding Amount</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">QAR {{ number_format($outstandingAmount, 2) }}</div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Overdue Invoices</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $overdueInvoices }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Payments -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Recent Payments</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    @forelse($recentPayments as $payment)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <div class="font-medium">{{ $payment->invoice->customer->company_name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->payment_number }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-green-600">
                                            + {{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400 text-right">
                                            {{ $payment->payment_date->diffForHumans() }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">No recent payments.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue Chart (Simple HTML/CSS) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Revenue Trend (Last 6 Months)</h3>
                        
                        @if(count($monthlyRevenue) > 0)
                            <div class="flex items-end space-x-4 h-64 mt-8 pb-4 border-b border-gray-200">
                                @php $max = max($monthlyRevenue->toArray()); @endphp
                                @foreach($monthlyRevenue as $month => $amount)
                                    <div class="flex-1 flex flex-col items-center group">
                                        <div class="w-full bg-blue-500 rounded-t hover:bg-blue-600 transition-all relative" style="height: {{ ($amount / $max) * 100 }}%;">
                                            <div class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs py-1 px-2 rounded">
                                                {{ number_format($amount) }}
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 rotate-45 transform origin-top-left md:rotate-0">{{ $month }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                             <p class="text-gray-500 dark:text-gray-400">No revenue data available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
