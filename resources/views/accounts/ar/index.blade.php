<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Accounts Receivable') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Aging Summary -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Aging Analysis</h3>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200 text-center">
                        <span class="block text-sm font-semibold text-green-700">0 - 30 Days</span>
                        <span class="block text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($aging['0-30'], 2) }}</span>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 text-center">
                        <span class="block text-sm font-semibold text-yellow-700">31 - 60 Days</span>
                        <span class="block text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($aging['31-60'], 2) }}</span>
                    </div>
                     <div class="bg-orange-50 p-4 rounded-lg border border-orange-200 text-center">
                        <span class="block text-sm font-semibold text-orange-700">61 - 90 Days</span>
                        <span class="block text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($aging['61-90'], 2) }}</span>
                    </div>
                     <div class="bg-red-50 p-4 rounded-lg border border-red-200 text-center">
                        <span class="block text-sm font-semibold text-red-700">90+ Days</span>
                        <span class="block text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($aging['90+'], 2) }}</span>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg border border-gray-300 text-center">
                        <span class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Total Outstanding</span>
                        <span class="block text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($aging['total'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Customer Outstanding Balances</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Outstanding Amount</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($customers as $customer)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $customer->company_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 dark:text-red-400 font-bold font-mono">
                                            {{ number_format($customer->outstanding_balance, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">View Ledger</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No outstanding balances found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
