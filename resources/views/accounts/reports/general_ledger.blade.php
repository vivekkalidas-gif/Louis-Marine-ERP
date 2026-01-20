<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('General Ledger') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Company Header (Visible on Screen) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-blue-900 dark:text-blue-400 uppercase tracking-wide">Louis Marine Equipment</h1>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            <p>P.O. Box 12345, Doha, Qatar</p>
                            <p>Tel: +974 4444 5555 | Email: accounts@louismarine.qa</p>
                            <p class="font-semibold">TRN: 1234567890</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white uppercase">General Ledger</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Generated on {{ now()->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('accounts.reports.general_ledger') }}" class="flex flex-wrap items-end gap-4">
                    <div class="w-full sm:w-1/4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Account</label>
                        <select name="account_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">All Accounts</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->code }} - {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full sm:w-1/4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Partner / Company</label>
                        <select name="partner_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">All Companies</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                     <div class="w-full sm:w-1/4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="w-full sm:w-1/4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Filter
                        </button>
                    </div>
                     <div class="ml-auto flex gap-2">
                        <a href="{{ route('accounts.reports.ledger.print', request()->all()) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2-4h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6a2 2 0 012-2zm9-2a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                            Print
                        </a>
                         <a href="{{ route('accounts.reports.ledger.pdf', request()->all()) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export PDF
                        </a>
                    </div>
                </form>
            </div>

            <!-- Ledger Content -->
            <div class="space-y-6">
                @forelse($ledgerData as $data)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 flex justify-between items-center sticky top-0 z-10">
                        <div>
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">{{ $data['account']->code }}</span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $data['account']->name }}</h3>
                        </div>
                        <div class="text-right">
                             <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Closing Balance</span>
                             <div class="text-lg font-mono font-bold">{{ number_format($data['closing_balance'], 2) }}</div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Journal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Partner</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference / Label</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Debit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Credit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                <!-- Opening Balance Row -->
                                <tr class="bg-gray-50 dark:bg-gray-700 font-medium text-gray-700 dark:text-gray-300">
                                    <td colspan="4" class="px-6 py-3 text-right">Opening Balance</td>
                                    <td class="px-6 py-3 text-right font-mono">{{ $data['opening_balance'] > 0 ? number_format($data['opening_balance'], 2) : '' }}</td>
                                    <td class="px-6 py-3 text-right font-mono">{{ $data['opening_balance'] < 0 ? number_format(abs($data['opening_balance']), 2) : '' }}</td>
                                    <td class="px-6 py-3 text-right font-mono">{{ number_format($data['opening_balance'], 2) }}</td>
                                </tr>
                                
                                @foreach($data['lines'] as $line)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                                        <td class="px-6 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ \Carbon\Carbon::parse($line->date)->format('d M Y') }}</td>
                                        <td class="px-6 py-3 text-sm text-blue-600 font-medium whitespace-nowrap">
                                            <a href="{{ route('accounts.journals.show', $line->journal_entry_id) }}" class="hover:underline">
                                                {{ $line->entry->number ?? 'Draft' }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ optional($line->partner)->name ?? '-' }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="font-medium">{{ $line->label ?? $line->entry->reference }}</div>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-right font-mono text-gray-900 dark:text-gray-100">{{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}</td>
                                        <td class="px-6 py-3 text-sm text-right font-mono text-gray-900 dark:text-gray-100">{{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}</td>
                                        <td class="px-6 py-3 text-sm text-right font-mono font-bold text-gray-900 dark:text-gray-100">{{ number_format($line->running_balance, 2) }}</td>
                                    </tr>
                                @endforeach
                                
                                <!-- Closing Balance Row -->
                                <tr class="bg-gray-100 font-bold border-t-2 border-gray-300">
                                    <td colspan="4" class="px-6 py-3 text-right text-gray-900 dark:text-gray-100 uppercase">Ending Balance</td>
                                    <!-- Sum of Debits/Credits in period could be shown here, but request just wants Closing Balance. -->
                                    <!-- Let's show calculated Ending Balance. -->
                                    <td class="px-6 py-3 text-right font-mono"></td>
                                    <td class="px-6 py-3 text-right font-mono"></td>
                                    <td class="px-6 py-3 text-right font-mono">{{ number_format($data['closing_balance'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                        <p class="text-gray-500 dark:text-gray-400">No transactions found for the selected criteria.</p>
                    </div>
                @endforelse
            </div>
            
        </div>
    </div>
</x-app-layout>
