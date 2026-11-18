<?php
// Fix View with CORRECT Route Names (admin. prefix)
// ATTEMPT 2: Ensuring the strings are actually changed this time.

$content = <<<'EOD'
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Staff Profile: 
            @php
                $routeParam = request()->route('staff');
                if ($routeParam instanceof \App\Models\User) {
                    $userObj = $routeParam;
                } elseif (is_string($routeParam)) {
                    $userObj = \App\Models\User::find($routeParam);
                } else {
                    $userObj = isset($user) && $user->id ? $user : null;
                }
                
                $empDetail = null;
                $ledger = collect([]);
                $payrolls = collect([]);
                $balance = 0;

                if ($userObj) {
                    $empDetail = \App\Models\EmployeeDetail::where('user_id', $userObj->id)->first();
                    
                    if (class_exists(\App\Models\EmployeeLedgerEntry::class)) {
                        $ledger = \App\Models\EmployeeLedgerEntry::where('user_id', $userObj->id)
                                    ->orderBy('date', 'desc')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                        $balance = $ledger->sum('debit') - $ledger->sum('credit');
                    }

                    if (class_exists(\App\Models\PayrollEntry::class)) {
                        $payrolls = \App\Models\PayrollEntry::where('user_id', $userObj->id)
                            ->with('payroll')
                            ->get()
                            ->sortByDesc(function($entry) {
                                return $entry->payroll->month ?? '';
                        });
                    }
                }
            @endphp
            {{ $userObj ? $userObj->name : 'Staff Not Found' }}
        </h2>
    </x-slot>

    @if(!$userObj)
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                     <p>Staff member not found.</p>
                </div>
            </div>
        </div>
    @else

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Card -->
                <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Employee Details</h3>
                        <a href="{{ route('admin.finance.payroll.employees.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Manage / Edit Details &rarr;</a>
                    </div>
                   
                    @if(!$empDetail)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex"><div class="ml-3"><p class="text-sm text-yellow-700">Salary details not configured. <a href="{{ route('admin.finance.payroll.employees.index') }}" class="font-bold underline">Configure in Payroll Master.</a></p></div></div>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <!-- Details -->
                        <div><span class="block text-gray-500">Designation</span><span class="font-semibold dark:text-gray-200">{{ $empDetail?->designation ?? '-' }}</span></div>
                        <div><span class="block text-gray-500">Employee Code</span><span class="font-semibold dark:text-gray-200">{{ $empDetail?->employee_code ?? '-' }}</span></div>
                        <div><span class="block text-gray-500">Joining Date</span><span class="font-semibold dark:text-gray-200">{{ $empDetail?->join_date ? $empDetail->join_date->format('d M Y') : '-' }}</span></div>
                        <div><span class="block text-gray-500">Basic Salary</span><span class="font-semibold dark:text-gray-200">{{ number_format($empDetail?->basic_salary ?? 0, 2) }}</span></div>
                    </div>
                </div>

                <!-- Advance Balance -->
                <div class="bg-indigo-50 dark:bg-gray-700 shadow sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Advance Balance</h3>
                    <div class="text-3xl font-bold {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($balance, 2) }}</div>
                    <p class="text-sm text-gray-500 mt-1">Pending to be recovered</p>
                    <div class="mt-4 pt-4 border-t border-indigo-100 dark:border-gray-600">
                        <button onclick="document.getElementById('advanceModal').classList.remove('hidden')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">Record Advance / Repayment</button>
                    </div>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Advance & Repayment History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Description</th>
                                <th class="px-4 py-2 text-right">Debit (Given)</th>
                                <th class="px-4 py-2 text-right">Credit (Recovered)</th>
                                <th class="px-4 py-2 text-center text-indigo-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($ledger as $entry)
                            <tr>
                                <td class="px-4 py-2 dark:text-gray-300">{{ $entry->date->format('d M Y') }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ in_array($entry->type, ['advance_given', 'advance']) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucwords(str_replace('_', ' ', $entry->type)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 dark:text-gray-300">{{ $entry->description }}</td>
                                <td class="px-4 py-2 text-right text-red-600 font-medium">{{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}</td>
                                <td class="px-4 py-2 text-right text-green-600 font-medium">{{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}</td>
                                <td class="px-4 py-2 text-center space-x-2">
                                    <!-- Print (USING ADMIN PREFIX) -->
                                    <a href="{{ route('admin.staff.ledger.print', $entry->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white" title="Print Receipt">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2-4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    <!-- Edit -->
                                    <button onclick="openEditModal('{{ $entry->id }}', '{{ $entry->date->format('Y-m-d') }}', '{{ $entry->amount }}', '{{ addslashes($entry->description) }}', '{{ $entry->type }}')" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <!-- Delete (USING ADMIN PREFIX) -->
                                    <form action="{{ route('admin.staff.ledger.destroy', $entry->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payroll History -->
             <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                 <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Salary History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <!-- Head -->
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="px-4 py-2 text-left">Month</th>
                                <th class="px-4 py-2 text-right">Basic</th>
                                <th class="px-4 py-2 text-right">Allowances</th>
                                <th class="px-4 py-2 text-right">Overtime</th>
                                <th class="px-4 py-2 text-right">Deductions</th>
                                <th class="px-4 py-2 text-right">Net Salary</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                             @forelse($payrolls as $entry)
                            <tr>
                                <td class="px-4 py-2 dark:text-gray-300 font-medium">{{ \Carbon\Carbon::parse($entry->payroll->month)->format('F Y') }}</td>
                                <td class="px-4 py-2 text-right dark:text-gray-300">{{ number_format($entry->basic_salary, 2) }}</td>
                                <td class="px-4 py-2 text-right dark:text-gray-300">{{ number_format($entry->allowances_total, 2) }}</td>
                                <td class="px-4 py-2 text-right dark:text-gray-300">{{ number_format($entry->overtime_amount, 2) }}</td>
                                <td class="px-4 py-2 text-right text-red-500">{{ number_format($entry->deductions_total, 2) }}</td>
                                <td class="px-4 py-2 text-right font-bold dark:text-gray-100">{{ number_format($entry->net_salary, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">No payroll history found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Modals -->
    <div id="advanceModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('advanceModal').classList.add('hidden')"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="/admin/staff/{{ $userObj->id }}/ledger" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Record Transaction</h3>
                        <div class="mt-4 space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <select name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                                <option value="advance">Give Advance (Money Out)</option>
                                <option value="repayment">Manual Repayment (Money In)</option>
                            </select></div>
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label><input type="date" name="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required></div>
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label><input type="number" name="amount" min="1" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required></div>
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label><input type="text" name="description" placeholder="Short description..." class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required></div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('advanceModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('editModal').classList.add('hidden')"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <!-- FORM ACTION UPDATE -->
                <form id="editForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Edit Transaction</h3>
                        <div class="mt-4 space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label><input type="date" name="date" id="edit_date" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required></div>
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label><input type="number" name="amount" id="edit_amount" min="1" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required></div>
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label><input type="text" name="description" id="edit_description" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required></div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Update</button>
                        <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function openEditModal(id, date, amount, description, type) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_amount').value = amount;
            document.getElementById('edit_description').value = description;
            // UPDATE: Include /admin prefix
            document.getElementById('editForm').action = "/admin/staff/ledger/" + id;
        }
    </script>
    @endif

</x-app-layout>
EOD;

file_put_contents(__DIR__ . '/../resources/views/admin/staff/show.blade.php', $content);
echo "View patched with ADMIN ROUTE PREFIX successfully (2nd Attempt).";
