<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="theme-text-header">
                {{ __('Customer Management') }}
            </h2>
            <div class="flex gap-2" x-data>
                <a href="{{ route('admin.customers.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow-sm flex items-center gap-2" title="Export to CSV">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3V8"></path>
                    </svg>
                    <span>Export</span>
                </a>
                
                <button @click="$dispatch('open-import-modal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow-sm flex items-center gap-2" title="Import from CSV">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span>Import</span>
                </button>

                <a href="{{ route('admin.customers.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add New</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filters -->
            <div class="theme-card mb-6">
                <!-- ... existing filters code ... -->
                <form method="GET" action="{{ route('admin.customers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Company or Email..." class="theme-input">
                    
                    <select name="type" class="theme-input">
                        <option value="">All Types</option>
                        <option value="Owner" {{ request('type') == 'Owner' ? 'selected' : '' }}>Ship Owner</option>
                        <option value="Operator" {{ request('type') == 'Operator' ? 'selected' : '' }}>Ship Operator</option>
                        <option value="Agent" {{ request('type') == 'Agent' ? 'selected' : '' }}>Agent</option>
                        <option value="Manager" {{ request('type') == 'Manager' ? 'selected' : '' }}>Manager</option>
                    </select>

                    <select name="status" class="theme-input">
                        <option value="">All Status</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <button type="submit" class="theme-button justify-center">Filter</button>
                </form>
            </div>

            <!-- Table -->
            <div class="theme-card p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contacts</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($customers as $customer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-marine-900 dark:text-white">{{ $customer->company_name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $customer->type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $customer->city ?? '-' }}, {{ $customer->country }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $customer->contacts->count() }} Contacts
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $customer->status == 'Active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ $customer->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(auth()->user()->hasRole(['admin', 'manager', 'accounts']))
                                        <a href="{{ route('admin.customers.ledger', $customer) }}" class="text-green-600 dark:text-green-400 hover:text-green-500 dark:hover:text-green-300 mr-2">Ledger</a>
                                        @endif
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mr-3">View</a>
                                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4 bg-white dark:bg-marine-900 border-t dark:border-marine-800">
                        {{ $customers->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-data="{ open: false }"
         @open-import-modal.window="open = true"
         x-show="open" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-50 dark:bg-gray-7000 bg-opacity-75 transition-opacity" @click="open = false"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <form action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Import Customers</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        Upload a CSV file to import customers. 
                                        <a href="{{ route('admin.customers.template') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Download Template</a>
                                    </p>
                                    
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Choose CSV File</label>
                                        <input type="file" name="file" accept=".csv" required class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100
                                            dark:file:bg-gray-700 dark:file:text-gray-300
                                        "/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Import</button>
                        <button type="button" @click="open = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
