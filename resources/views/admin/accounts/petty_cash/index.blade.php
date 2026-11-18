<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Petty Cash Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                {{ session('success') }}
            </div>
            @endif

            <!-- Cash Balance Card -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg sm:rounded-lg p-8 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium opacity-90">Current Petty Cash Balance</h3>
                        <div class="text-4xl font-bold mt-2">{{ number_format($balance, 2) }}</div>
                        <div class="mt-4 text-sm opacity-75">
                            <span class="mr-4">Deposits: {{ number_format($totalDeposits, 2) }}</span>
                            <span>Expenses: {{ number_format($totalExpenses, 2) }}</span>
                        </div>
                    </div>
                    <div>
                        <button onclick="document.getElementById('entryModal').classList.remove('hidden')" class="bg-white text-indigo-600 font-bold py-3 px-6 rounded-lg shadow hover:bg-gray-50">
                            + New Entry
                        </button>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Transaction History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Received/Paid By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Approved By</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($entries as $entry)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm dark:text-gray-300">{{ $entry->date->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $entry->type === 'deposit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($entry->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm dark:text-gray-300">{{ $entry->description }}</td>
                                <td class="px-6 py-4 text-sm dark:text-gray-300">{{ $entry->received_by ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm dark:text-gray-300">{{ $entry->approved_by ?? '-' }}</td>
                                <td class="px-6 py-4 text-right font-semibold {{ $entry->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $entry->type === 'deposit' ? '+' : '-' }} {{ number_format($entry->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.accounts.petty-cash.destroy', $entry->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">No transactions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for New Entry -->
    <div id="entryModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('entryModal').classList.add('hidden')"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('admin.accounts.petty-cash.store') }}" method="POST">
                    @csrf
                    <div class="px-6 pt-6 pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Record Transaction</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                                <select name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                                    <option value="deposit">Deposit (Cash In)</option>
                                    <option value="expense">Expense (Cash Out)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                                <input type="number" name="amount" step="0.01" min="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <input type="text" name="description" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Received/Paid By</label>
                                <select name="received_by" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Select Staff --</option>
                                    @foreach($staff as $member)
                                        <option value="{{ $member->name }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved By (Accountant/Admin)</label>
                                <select name="approved_by" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                    <option value="">-- Select Approver --</option>
                                    @foreach($approvers as $approver)
                                        <option value="{{ $approver->name }}">{{ $approver->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex flex-row-reverse">
                        <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700">Save</button>
                        <button type="button" onclick="document.getElementById('entryModal').classList.add('hidden')" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>