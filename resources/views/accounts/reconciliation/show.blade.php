<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $statement->name }}
                </h2>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $statement->journal->name }} | {{ $statement->date->format('d M Y') }}
                </div>
            </div>
            <div class="flex gap-2">
                 <form action="{{ route('accounts.reconciliation.auto-match', $statement) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Auto-Match Candidates
                    </button>
                </form>
                <a href="{{ route('accounts.reconciliation.index') }}" class="bg-white dark:bg-gray-800 border border-gray-300 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 font-bold py-2 px-4 rounded shadow-sm">
                    Close
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="reconciliation()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Messages -->
            <x-auth-session-status class="mb-4" :status="session('success')" />
            
            <!-- Balance Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500 p-6">
                    <div class="text-xs font-bold text-blue-500 uppercase mb-1">Starting Balance</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($statement->balance_start, 2) }}</div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500 p-6">
                    <div class="text-xs font-bold text-green-500 uppercase mb-1">Computed Ending Balance</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($computedEnd, 2) }}</div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500 p-6">
                    <div class="text-xs font-bold text-purple-500 uppercase mb-1">Target Ending Balance</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($statement->balance_end_real, 2) }}</div>
                    @if(abs($computedEnd - $statement->balance_end_real) > 0.01)
                        <div class="text-sm text-red-600 font-bold mt-1">Diff: {{ number_format($computedEnd - $statement->balance_end_real, 2) }}</div>
                    @else
                        <div class="text-sm text-green-600 font-bold mt-1">Balanced</div>
                    @endif
                </div>
            </div>

            <!-- Add Line Form (Collapsible?) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" x-data="{ open: false }">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">Add Missing Statement Line</h3>
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200" x-show="open" style="display: none;">
                    <form action="{{ route('accounts.reconciliation.store-line', $statement) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="w-full md:w-1/6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <input type="date" name="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ $statement->date->format('Y-m-d') }}" required>
                        </div>
                        <div class="w-full md:w-4/6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description / Label</label>
                            <input type="text" name="label" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="e.g., Bank Charge" required>
                        </div>
                        <div class="w-full md:w-1/6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
                            <input type="number" step="0.01" name="amount" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="0.00" required>
                        </div>
                        <div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sticky Match Action Bar -->
            <div class="sticky top-0 z-50 bg-yellow-100 border-b border-yellow-200 shadow-md p-4 mb-6 rounded-md" 
                 x-show="selectedLeft && selectedRight" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 style="display: none;">
                <div class="flex justify-between items-center max-w-7xl mx-auto">
                    <div class="flex items-center gap-4">
                        <div class="text-sm">
                            <span class="font-bold text-gray-700 dark:text-gray-300">Bank:</span> <span class="font-mono" x-text="formatMoney(selectedLeftAmount)"></span>
                        </div>
                        <div class="text-gray-400">vs</div>
                         <div class="text-sm">
                            <span class="font-bold text-gray-700 dark:text-gray-300">System:</span> <span class="font-mono" x-text="formatMoney(selectedRightAmount)"></span>
                        </div>
                        <div class="text-sm font-bold" :class="Math.abs(selectedLeftAmount - selectedRightAmount) < 0.01 ? 'text-green-600' : 'text-red-600'">
                            Diff: <span x-text="formatMoney(selectedLeftAmount - selectedRightAmount)"></span>
                        </div>
                    </div>
                    
                    <button @click="submitMatch()" 
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-lg transform transition hover:scale-105"
                        :disabled="loading">
                        <span x-show="!loading">Confirm Match</span>
                        <span x-show="loading">Processing...</span>
                    </button>
                </div>
            </div>

            <!-- Matching Interface -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 relative">
                
                <!-- Left: Bank Statement Lines -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg flex flex-col h-[600px]">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 flex justify-between">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200">Bank Statement Lines</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Left Side</span>
                    </div>
                    <div class="overflow-y-auto flex-1 p-2">
                        <table class="min-w-full divide-y divide-gray-200">
                             <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 shadow-sm z-10">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Label</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @foreach($statement->lines as $line)
                                    @if($line->status !== 'reconciled')
                                    <tr class="cursor-pointer transition-colors" 
                                        :class="selectedLeft === '{{ $line->id }}' ? 'bg-blue-100 ring-2 ring-inset ring-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700'"
                                        @click="selectLeft('{{ $line->id }}', {{ $line->amount }})">
                                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $line->date->format('Y-m-d') }}</td>
                                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            <div class="font-medium">{{ $line->label }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $line->ref }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-sm text-right font-mono font-bold">{{ number_format($line->amount, 2) }}</td>
                                    </tr>
                                    @else
                                    <tr class="bg-gray-50 dark:bg-gray-700 opacity-50">
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $line->date->format('Y-m-d') }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $line->label }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 text-right">{{ number_format($line->amount, 2) }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right: Ledger Entries -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg flex flex-col h-[600px]">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 dark:bg-gray-700 flex justify-between">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200">System Transactions</h3>
                         <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Right Side</span>
                    </div>
                    <div class="overflow-y-auto flex-1 p-2">
                        <table class="min-w-full divide-y divide-gray-200">
                           <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 shadow-sm z-10">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Entry</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @forelse($journalItems as $item)
                                    @php 
                                        $signedAmount = $item->debit > 0 ? $item->debit : -$item->credit;
                                    @endphp
                                    <tr class="cursor-pointer transition-colors"
                                        :class="selectedRight === '{{ $item->entry->id }}' ? 'bg-blue-100 ring-2 ring-inset ring-blue-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700'"
                                        @click="selectRight('{{ $item->entry->id }}', {{ $signedAmount }})">
                                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item->date->format('Y-m-d') }}</td>
                                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            <div class="font-bold">{{ $item->entry->name ?? $item->entry->reference }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->name }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-sm text-right font-mono font-bold {{ $signedAmount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format($signedAmount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No unreconciled transactions found for this journal.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Hidden Match Form -->
        <form id="matchForm" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="journal_entry_id" id="form_journal_entry_id">
        </form>

    </div>

    <script>
        function reconciliation() {
            return {
                selectedLeft: null,
                selectedLeftAmount: 0,
                selectedRight: null,
                selectedRightAmount: 0,
                loading: false,

                selectLeft(id, amount) {
                    // Toggle if same
                    if (this.selectedLeft === id) {
                        this.selectedLeft = null;
                        this.selectedLeftAmount = 0;
                    } else {
                        this.selectedLeft = id;
                        this.selectedLeftAmount = parseFloat(amount);
                    }
                },

                selectRight(id, amount) {
                    if (this.selectedRight === id) {
                        this.selectedRight = null;
                        this.selectedRightAmount = 0;
                    } else {
                        this.selectedRight = id;
                        this.selectedRightAmount = parseFloat(amount);
                    }
                },
                
                formatMoney(amount) {
                    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'QAR' }).format(amount);
                },

                submitMatch() {
                    const diff = Math.abs(this.selectedLeftAmount - this.selectedRightAmount);
                    if (diff > 0.01) {
                        if(!confirm('Warning: Amounts differ by ' + this.formatMoney(diff) + '.\nAre you sure you want to match these?')) {
                            return;
                        }
                    }
                    
                    this.loading = true;

                    // Set Form Action Dynamically
                    const form = document.getElementById('matchForm');
                    // Base URL is tricky in JS if not passed. Assuming relative path works or constructing safe url.
                    // Safe way: Use a placeholder or route() output in blade if possible but we have dynamic ID.
                    // We can use the current URL as base? No.
                    // Let's use a blade variable for base url
                    const baseUrl = "{{ route('accounts.reconciliation.match', ['statement' => $statement->id, 'line' => 'LINE_ID']) }}";
                    const action = baseUrl.replace('LINE_ID', this.selectedLeft);
                    
                    form.action = action;
                    document.getElementById('form_journal_entry_id').value = this.selectedRight;
                    
                    form.submit();
                }
            }
        }
    </script>
</x-app-layout>
