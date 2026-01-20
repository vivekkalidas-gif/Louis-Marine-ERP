<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('General Ledger') }}: {{ $account->name }} ({{ $account->code }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                 <form method="GET" action="{{ route('accounts.reports.ledger', $account) }}" class="flex items-end gap-4">
                    <div class="w-1/3">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">As of Date</label>
                        <input type="date" name="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" value="{{ $date }}">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update
                        </button>
                    </div>
                    <div class="ml-auto">
                        <a href="{{ route('accounts.reports.balance_sheet') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded inline-flex items-center">
                            Back to Balance Sheet
                        </a>
                    </div>
                </form>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Debit</div>
                    <div class="mt-2 text-2xl font-mono text-gray-900 dark:text-gray-100">{{ number_format($totalDebit, 2) }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Credit</div>
                    <div class="mt-2 text-2xl font-mono text-gray-900 dark:text-gray-100">{{ number_format($totalCredit, 2) }}</div>
                </div>
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center bg-blue-50 border border-blue-100">
                    <div class="text-sm font-bold text-blue-800 uppercase tracking-wide">Net Balance</div>
                    <div class="mt-2 text-2xl font-mono text-blue-900">
                        {{ number_format($totalDebit - $totalCredit, 2) }} 
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            ({{ ($totalDebit - $totalCredit) >= 0 ? 'Dr' : 'Cr' }})
                        </span>
                    </div>
                </div>
            </div>

            <!-- Transactions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ref / Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Label</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cost Center</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Debit</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Credit</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                            @forelse($items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <div class="font-medium">{{ $item->entry->number }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->entry->reference }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $item->label ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($item->costCenter)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:text-gray-200">
                                                {{ $item->costCenter->code }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right font-mono">{{ number_format($item->debit, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right font-mono">{{ number_format($item->credit, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                        <a href="{{ route('accounts.journals.show', $item->entry) }}" class="text-indigo-600 hover:text-indigo-900">View Entry</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No transactions found for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $items->appends(['date' => $date])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
