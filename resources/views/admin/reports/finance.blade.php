<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
             <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Finance Report') }}
            </h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Period: {{ $startDate }} to {{ $endDate }}
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Finance Navigation Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('admin.finance.index') }}" 
                       class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-current="page">
                        Overview
                    </a>
                    
                    <a href="{{ route('admin.finance.expenses.index') }}" 
                       class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Accounts (Income & Expense)
                    </a>

                    <a href="{{ route('admin.finance.payroll.index') }}" 
                       class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Payroll
                    </a>
                </nav>
            </div>
            
            <!-- Summary Stats -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase">Total Invoiced</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">QAR {{ number_format($invoices->sum('total_amount'), 2) }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase">VAT Collected</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">QAR {{ number_format($vatCollected, 2) }}</div>
                    </div>
                     <div>
                        <div class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase">Quotations Value</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">QAR {{ number_format($quotations->sum('total_amount'), 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Invoices Detail</h3>
                 <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($invoices as $inv)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $inv->date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">{{ $inv->invoice_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">{{ $inv->customer->company_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-300">{{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $inv->status === 'PAID' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                    {{ $inv->status === 'SENT' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                                    {{ $inv->status === 'CANCELLED' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
