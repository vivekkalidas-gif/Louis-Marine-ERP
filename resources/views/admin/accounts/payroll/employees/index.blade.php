<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Employee Payroll Master') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Manage salary structures for Engineers, Technicians, and Staff.</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Code / Role</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Basic Salary</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">OT Rate/Hr</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Allowances</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($employees as $employee)
                                <tr x-data="{ editOpen: false }">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $employee->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $employee->employeeDetail->employee_code ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $employee->roles->pluck('name')->join(', ') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-green-600 dark:text-green-400">
                                        {{ number_format($employee->employeeDetail->basic_salary ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600 dark:text-gray-300">
                                        {{ number_format($employee->employeeDetail->hourly_rate ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600 dark:text-gray-300">
                                        @php
                                            $totalAllow = ($employee->employeeDetail->allowance_transport ?? 0) + 
                                                          ($employee->employeeDetail->allowance_food ?? 0) + 
                                                          ($employee->employeeDetail->allowance_other ?? 0);
                                        @endphp
                                        {{ number_format($totalAllow, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="editOpen = true" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Manage Salary</button>

                                        <!-- Edit Modal -->
                                        <div x-show="editOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                                                <div class="fixed inset-0 transition-opacity bg-gray-50 dark:bg-gray-7000 bg-opacity-75" @click="editOpen = false"></div>
                                                <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Payroll Details: {{ $employee->name }}</h3>
                                                    <form action="{{ route('admin.finance.payroll.employees.update', $employee) }}" method="POST" class="mt-4">
                                                        @csrf
                                                        @method('PUT')
                                                        
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee Code</label>
                                                                <input type="text" name="employee_code" value="{{ $employee->employeeDetail->employee_code ?? '' }}" required class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Designation</label>
                                                                <input type="text" name="designation" value="{{ $employee->employeeDetail->designation ?? '' }}" class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                                            </div>
                                                            
                                                            <div class="col-span-2 border-t border-gray-200 dark:border-gray-700 my-2"></div>
                                                            
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Basic Salary (Monthly)</label>
                                                                <input type="number" step="0.01" name="basic_salary" value="{{ $employee->employeeDetail->basic_salary ?? 0 }}" required class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Overtime Rate (Per Hour)</label>
                                                                <input type="number" step="0.01" name="hourly_rate" value="{{ $employee->employeeDetail->hourly_rate ?? 0 }}" required class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                                            </div>

                                                            <div class="col-span-2 border-t border-gray-200 dark:border-gray-700 my-2"></div>
                                                            <h4 class="col-span-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Monthly Allowances</h4>

                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transport</label>
                                                                <input type="number" step="0.01" name="allowance_transport" value="{{ $employee->employeeDetail->allowance_transport ?? 0 }}" class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Food</label>
                                                                <input type="number" step="0.01" name="allowance_food" value="{{ $employee->employeeDetail->allowance_food ?? 0 }}" class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Other / Site</label>
                                                                <input type="number" step="0.01" name="allowance_other" value="{{ $employee->employeeDetail->allowance_other ?? 0 }}" class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                                            </div>
                                                        </div>

                                                        <div class="mt-6 flex justify-end">
                                                            <button type="button" @click="editOpen = false" class="mr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Cancel</button>
                                                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save Details</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
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
