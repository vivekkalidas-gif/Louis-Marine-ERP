<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Journal Entry') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="journalEntryForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">

                    <!-- Error Handling -->
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('accounts.journals.store') }}">
                        @csrf

                        <!-- Header Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Journal</label>
                                <select name="journal_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" required>
                                    @foreach($journals as $journal)
                                        <option value="{{ $journal->id }}">{{ $journal->code }} - {{ $journal->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Date</label>
                                <input type="date" name="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Reference</label>
                                <input type="text" name="reference" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="mb-6 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/3">Account</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Label</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">Partner</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">Cost Center</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">Debit</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-32">Credit</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="px-2 py-2">
                                                <select :name="'items['+index+'][account_id]'" x-model="item.account_id" class="text-sm shadow-sm border-gray-300 rounded-md w-full" required>
                                                    <option value="">Select Account</option>
                                                    @foreach($accounts as $account)
                                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="text" :name="'items['+index+'][label]'" x-model="item.label" class="text-sm shadow-sm border-gray-300 rounded-md w-full">
                                            </td>
                                            <td class="px-2 py-2">
                                                 <!-- Simple Input for Partner ID for now, ideally searchable -->
                                                 <input type="text" disabled placeholder="-" class="text-sm bg-gray-100 shadow-sm border-gray-300 rounded-md w-full">
                                            </td>
                                            <td class="px-2 py-2">
                                                <select :name="'items['+index+'][cost_center_id]'" x-model="item.cost_center_id" class="text-sm shadow-sm border-gray-300 rounded-md w-full">
                                                    <option value="">-</option>
                                                    @foreach($costCenters as $cc)
                                                        <option value="{{ $cc->id }}">{{ $cc->code }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="number" step="0.01" :name="'items['+index+'][debit]'" x-model="item.debit" @input="calculateTotals()" class="text-sm shadow-sm border-gray-300 rounded-md w-full text-right" placeholder="0.00">
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="number" step="0.01" :name="'items['+index+'][credit]'" x-model="item.credit" @input="calculateTotals()" class="text-sm shadow-sm border-gray-300 rounded-md w-full text-right" placeholder="0.00">
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900" x-show="items.length > 2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700 font-bold">
                                    <tr>
                                        <td colspan="4" class="px-4 py-2 text-right">Totals:</td>
                                        <td class="px-4 py-2 text-right" :class="{'text-red-600': !isBalanced, 'text-green-600': isBalanced}" x-text="totalDebit.toFixed(2)"></td>
                                        <td class="px-4 py-2 text-right" :class="{'text-red-600': !isBalanced, 'text-green-600': isBalanced}" x-text="totalCredit.toFixed(2)"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="mt-2 text-right" x-show="!isBalanced">
                                <span class="text-red-600 text-sm font-bold">Unbalanced: <span x-text="(totalDebit - totalCredit).toFixed(2)"></span></span>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" @click="addItem()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded text-sm">
                                + Add Line
                            </button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" :disabled="!isBalanced">
                                Save Journal Entry
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function journalEntryForm() {
            return {
                items: [
                    { account_id: '', label: '', debit: 0, credit: 0, cost_center_id: '' },
                    { account_id: '', label: '', debit: 0, credit: 0, cost_center_id: '' }
                ],
                totalDebit: 0,
                totalCredit: 0,
                isBalanced: true,

                addItem() {
                    this.items.push({ account_id: '', label: '', debit: 0, credit: 0, cost_center_id: '' });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                },
                calculateTotals() {
                    this.totalDebit = this.items.reduce((sum, item) => sum + Number(item.debit || 0), 0);
                    this.totalCredit = this.items.reduce((sum, item) => sum + Number(item.credit || 0), 0);
                    // Use small epsilon for float comparison safety
                    this.isBalanced = Math.abs(this.totalDebit - this.totalCredit) < 0.01;
                }
            }
        }
    </script>
</x-app-layout>
