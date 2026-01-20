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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

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
                    <h3 class="text-lg font-medium mb-4">Employee Breakdown</h3>
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
    </div>
</x-app-layout>
