<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Payroll Details') }}: {{ \Carbon\Carbon::parse($payroll->month)->format('F Y') }}
            </h2>
            <div>
                @if($payroll->status === 'draft')
                <form action="{{ route('admin.finance.payroll.finalize', $payroll) }}" method="POST" onsubmit="return confirm('Are you sure? This will lock the payroll and post expenses to Accounts.');" class="inline-block">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Finalize & Post to Accounts
                    </button>
                </form>
                @else
                <span class="px-4 py-2 bg-green-100 text-green-800 rounded-md font-semibold text-sm inline-block">
                    Finalized on {{ $payroll->processed_date->format('d M Y') }}
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12" x-data="{ showDeductionModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-alert-success class="mb-4" />
            <x-alert-error class="mb-4" />

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-4 gap-4 text-center">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Total Basic Salary</div>
                            <div class="text-xl font-bold">{{ number_format($payroll->entries->sum('basic_salary'), 2) }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Total Allowances</div>
                            <div class="text-xl font-bold">{{ number_format($payroll->entries->sum('allowances_total'), 2) }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Total Overtime</div>
                            <div class="text-xl font-bold">{{ number_format($payroll->entries->sum('overtime_amount'), 2) }}</div>
                        </div>
                         <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-lg">
                            <div class="text-xs text-indigo-500 uppercase">Total Net Payable</div>
                            <div class="text-2xl font-bold text-indigo-700">{{ number_format($payroll->total_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Employee Breakdown</h3>
                        
                        @if($payroll->status === 'draft')
                        <button @click="showDeductionModal = true" class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm hover:bg-red-200">
                            + Add Deduction / Advance
                        </button>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Basic</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Allowances</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">OT (Hrs / Amt)</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deductions</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Salary</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($payroll->entries as $entry)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $entry->user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $entry->user->employeeDetail->designation ?? 'Staff' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($entry->basic_salary, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($entry->allowances_total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                        <div>{{ number_format($entry->overtime_amount, 2) }}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 dark:text-gray-400">({{ $entry->overtime_hours }} hrs)</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-red-500 dark:text-red-400">
                                        {{ number_format($entry->deductions_total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ number_format($entry->net_salary, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deduction Modal -->
        <div x-show="showDeductionModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeductionModal" @click="showDeductionModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showDeductionModal" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('admin.finance.payroll.deduction.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="month" value="{{ $payroll->month }}">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                Add Deduction / Advance
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee</label>
                                    <select name="user_id" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                        @foreach($payroll->entries as $entry)
                                            <option value="{{ $entry->user_id }}">{{ $entry->user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount to Deduct</label>
                                    <input type="number" name="amount" min="1" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason / Description</label>
                                    <input type="text" name="description" placeholder="e.g. Salary Advance, Penalty" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Add Deduction
                            </button>
                            <button type="button" @click="showDeductionModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
