<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Expense Management') }}
            </h2>
            <div x-data="{ open: false }">
                <button @click="open = true" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    Record New Expense
                </button>

                <!-- Create Modal -->
                <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 text-center">
                        <div class="fixed inset-0 transition-opacity bg-gray-50 dark:bg-gray-700 bg-opacity-75" @click="open = false"></div>
                        <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Record Expense</h3>
                            <form action="{{ route('admin.finance.expenses.store') }}" method="POST" class="mt-4">
                                @csrf
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                                        <input type="number" step="0.01" name="amount" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                        <select name="category_id" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Link to Job (Optional)</label>
                                        <select name="job_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- General Expense --</option>
                                            @foreach($jobs as $job)
                                                <option value="{{ $job->id }}">Job #{{ $job->id }} - {{ $job->customer->company_name ?? 'Unknown' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                     <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vendor / Supplier</label>
                                        <input type="text" name="vendor_name" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
                                        <select name="payment_method" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="cash">Cash</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="card">Card</option>
                                        </select>
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ref #</label>
                                        <input type="text" name="reference_number" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                        <textarea name="notes" rows="2" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                    </div>
                                </div>
                                <div class="mt-6 flex justify-end">
                                    <button type="button" @click="open = false" class="mr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Save Expense</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Details</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($expenses as $expense)
                                <tr x-data="{ editOpen: false }">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $expense->date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $expense->category->color ?? 'gray' }}-100 text-gray-800 dark:text-gray-200">
                                            {{ $expense->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($expense->job_id)
                                            <div class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded w-fit mb-1">
                                                Job #{{ $expense->job_id }}
                                            </div>
                                        @endif
                                        <div class="text-gray-500 dark:text-gray-400">{{ Str::limit($expense->notes, 30) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-red-600 dark:text-red-400">
                                        {{ number_format($expense->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $expense->vendor_name }}
                                        <div class="text-xs text-gray-400 dark:text-gray-500 dark:text-gray-400">{{ $expense->reference_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="editOpen = true" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mr-2">Edit</button>
                                        <form action="{{ route('admin.finance.expenses.destroy', $expense) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>

                                        <!-- Edit Modal -->
                                        <div x-show="editOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                            <div class="flex items-center justify-center min-h-screen px-4 text-center">
                                                <div class="fixed inset-0 transition-opacity bg-gray-50 dark:bg-gray-700 bg-opacity-75" @click="editOpen = false"></div>
                                                <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Edit Expense</h3>
                                                    <form action="{{ route('admin.finance.expenses.update', $expense) }}" method="POST" class="mt-4">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div class="col-span-1">
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                                                <input type="date" name="date" value="{{ $expense->date->format('Y-m-d') }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                                            </div>
                                                            <div class="col-span-1">
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                                                                <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                                            </div>
                                                            <div class="col-span-2">
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                                                <select name="category_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                                                    @foreach($categories as $cat)
                                                                        <option value="{{ $cat->id }}" {{ $expense->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-span-2">
                                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vendor</label>
                                                                <input type="text" name="vendor_name" value="{{ $expense->vendor_name }}" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                                            </div>
                                                            <div class="mt-6 col-span-2 flex justify-end">
                                                                <button type="button" @click="editOpen = false" class="mr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300">Cancel</button>
                                                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update</button>
                                                            </div>
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
                    <div class="mt-4">
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
