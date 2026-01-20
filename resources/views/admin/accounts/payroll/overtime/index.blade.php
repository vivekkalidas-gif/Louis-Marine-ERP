<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Overtime Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Add Overtime Form -->
                <div class="col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">Record Overtime</h3>
                            <form action="{{ route('admin.finance.payroll.overtime.store') }}" method="POST">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Employee</label>
                                    <select name="user_id" required class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hours</label>
                                    <input type="number" step="0.5" name="hours" required class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Job (Optional)</label>
                                    <select name="job_id" class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- No Specific Job --</option>
                                        @foreach($jobs as $job)
                                            <option value="{{ $job->id }}">Job #{{ $job->id }} - {{ $job->customer->company_name ?? 'Unknown' }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Links OT cost to Service Job.</p>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason / Remarks</label>
                                    <input type="text" name="reason" class="block w-full mt-1 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm">
                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save Overtime</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Recent OT List -->
                 <div class="col-span-1 md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">Recent Overtime Entries</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Details</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hours</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($overtimeRecords as $ot)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $ot->date->format('d M') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ot->user->name ?? 'Unknown' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @if($ot->job_id)
                                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">Job #{{ $ot->job_id }}</span>
                                                @endif
                                                <div class="text-xs">{{ $ot->reason }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-gray-100">{{ $ot->hours }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <form action="{{ route('admin.finance.payroll.overtime.destroy', $ot) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this entry?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $overtimeRecords->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
