<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Service Job') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.jobs.store') }}">
                        @csrf

                        <!-- Customer Selection -->
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Customer</label>
                            <select name="customer_id" id="customer_select" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Vessel Selection (Can be dynamic with AJAX, simplified for now) -->
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Vessel (Optional - Pick from Global List for now, AJAX later)</label>
                             <select name="vessel_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">No Specific Vessel</option>
                                {{-- Optimally this should be filtered by JS based on Customer --}}
                                @foreach(\App\Models\Vessel::all() as $vessel)
                                     <option value="{{ $vessel->id }}">{{ $vessel->name }} ({{ $vessel->customer->company_name ?? 'Unlinked' }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Note: In production this list filters by selected customer.</p>
                        </div>

                        <!-- Job Details -->
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Scope of Work</label>
                            <textarea name="scope_of_work" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" rows="4" required></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Priority</label>
                                <select name="priority" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="Normal">Normal</option>
                                    <option value="High">High</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Reference / PO #</label>
                                <input type="text" name="reference_no" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>

                        <!-- Assignment -->
                        <div class="mb-4 bg-gray-50 dark:bg-gray-700 p-4 rounded">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Assign Engineer (Optional)</label>
                            <select name="assigned_engineer_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Leave Unassigned</option>
                                @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Create Job Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
