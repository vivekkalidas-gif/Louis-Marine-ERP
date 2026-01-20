<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Journal Entry') }} <span class="text-gray-500 dark:text-gray-400 font-normal">#{{ $journalEntry->number ?? 'Draft' }}</span>
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('accounts.journals.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                   Back
                </a>
                
                @if($journalEntry->state === 'draft')
                    <a href="{{ route('accounts.journals.edit', $journalEntry) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('accounts.journals.post', $journalEntry) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to post this entry? This action is irreversible.')">
                            Post
                        </button>
                    </form>
                @elseif($journalEntry->state === 'posted' && !$journalEntry->reversed_entry_id)
                     <button x-data @click="$dispatch('open-modal', 'reverse-modal')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Reverse Entry
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Reversal Alert -->
            @if($journalEntry->reversed_entry_id)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This entry has been reversed.
                                <a href="{{ route('accounts.journals.show', $journalEntry->reversed_entry_id) }}" class="font-medium underline text-yellow-700 hover:text-yellow-600">
                                    View Reversal Entry
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Is this a reversal itself? -->
            @if(str_contains($journalEntry->reference, 'Reversal of'))
                 <!-- Ideally we check a specific column, but using ref string for now or if we linked via reversed_entry_id on source -->
                 <!-- If we implemented bi-directional or stored ID: -->
                 @php
                    // Check if it IS a reversal (referencing another)
                    // With current simple logic, we only stored ID on the Source.
                    // But we can assume based on Ref or if we stored reversal_reason
                 @endphp
                 @if($journalEntry->reversal_reason)
                     <div class="bg-gray-50 dark:bg-gray-700 border-l-4 border-gray-400 p-4 mb-6">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong>Reversal Reason:</strong> {{ $journalEntry->reversal_reason }}
                                </p>
                            </div>
                        </div>
                    </div>
                 @endif
            @endif


            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    
                    <!-- Header Info -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 border-b pb-6">
                        <div>
                            <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Code</span>
                            <span class="block text-gray-900 dark:text-gray-100">{{ $journalEntry->number ?? 'Unposted' }}</span>
                        </div>
                         <div>
                            <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Journal</span>
                            <span class="block text-gray-900 dark:text-gray-100">{{ $journalEntry->journal->name }} ({{ $journalEntry->journal->code }})</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Date</span>
                            <span class="block text-gray-900 dark:text-gray-100">{{ $journalEntry->date->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Status</span>
                             <span class="inline-flex text-xs leading-5 font-semibold rounded-full mt-1
                                {{ $journalEntry->state === 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($journalEntry->state) }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Reference</span>
                            <span class="block text-gray-900 dark:text-gray-100">{{ $journalEntry->reference ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Created By</span>
                            <span class="block text-gray-900 dark:text-gray-100">{{ optional($journalEntry->creator)->name ?? 'System' }}</span>
                        </div>
                       
                        <div>
                            <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Posted By</span>
                            <span class="block text-gray-900 dark:text-gray-100">{{ optional($journalEntry->poster)->name ?? '-' }}</span>
                        </div>
                        
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Label</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Partner</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cost Center</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Debit</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Credit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @php $totalDebit = 0; $totalCredit = 0; @endphp
                                @foreach($journalEntry->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <span class="font-bold">{{ $item->account->code }}</span> - {{ $item->account->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->label ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ optional($item->partner)->name ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->costCenter->code ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100 font-mono">{{ number_format($item->debit, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100 font-mono">{{ number_format($item->credit, 2) }}</td>
                                    </tr>
                                    @php $totalDebit += $item->debit; $totalCredit += $item->credit; @endphp
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700 font-bold border-t-2 border-gray-200">
                                <tr>
                                    <td colspan="4" class="px-6 py-3 text-right text-gray-900 dark:text-gray-100 uppercase">Total</td>
                                    <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100 font-mono">{{ number_format($totalDebit, 2) }}</td>
                                    <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100 font-mono">{{ number_format($totalCredit, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <!-- Reversal Modal -->
    <x-modal name="reverse-modal" :show="false">
        <form method="POST" action="{{ route('journals.reverse', $journalEntry) }}" class="p-6">
            @csrf
            
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Reverse Journal Entry
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                This will create a new journal entry that reverses the debits and credits of this entry. The current entry will be marked as reversed.
            </p>

            <div class="mt-6">
                <x-input-label for="reversal_date" value="Reversal Date" />
                <x-text-input id="reversal_date" name="date" type="date" class="mt-1 block w-full" :value="now()->format('Y-m-d')" required />
                <x-input-error :messages="$errors->get('date')" class="mt-2" />
            </div>

            <div class="mt-6">
                <x-input-label for="reversal_reason" value="Reason for Reversal" />
                <textarea id="reversal_reason" name="reason" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required placeholder="e.g., Wrong account selected"></textarea>
                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancel
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    Reverse Entry
                </x-danger-button>
            </div>
        </form>
    </x-modal>

</x-app-layout>
