<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Quotation') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="customerManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('storekeeper.quotations.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Selection -->
                            <div class="md:col-span-2">
                                <x-input-label for="customer_id" :value="__('Customer')" />
                                <div class="flex gap-2 mt-1">
                                    <select id="customer_id" name="customer_id" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required x-model="selectedCustomer">
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('admin.customers.create') }}" target="_blank" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 whitespace-nowrap flex items-center">
                                        + New
                                    </a>
                                    <button type="button" @click="window.location.reload()" class="bg-gray-200 text-gray-700 dark:text-gray-300 px-3 py-2 rounded-md hover:bg-gray-300 whitespace-nowrap" title="Refresh Page to see new customer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="quotation_date" :value="__('Date')" />
                                <x-text-input id="quotation_date" class="block mt-1 w-full" type="date" name="quotation_date" :value="old('quotation_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('quotation_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="valid_until" :value="__('Valid Until')" />
                                <x-text-input id="valid_until" class="block mt-1 w-full" type="date" name="valid_until" :value="old('valid_until', date('Y-m-d', strtotime('+30 days')))" />
                                <x-input-error :messages="$errors->get('valid_until')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="reference_number" :value="__('Reference Number')" />
                                <x-text-input id="reference_number" class="block mt-1 w-full" type="text" name="reference_number" :value="old('reference_number')" placeholder="e.g., RFQ-123" />
                                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-4 border-t pt-4 mt-4">
                            <a href="{{ route('storekeeper.quotations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Create & Add Items') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Customer Modal Removed -->
    </div>

    <script>
        function customerManager() {
            return {
                selectedCustomer: '',
            }
        }
    </script>
</x-app-layout>
