<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Supplier') }}
            </h2>
            <a href="{{ route('storekeeper.suppliers.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-100 font-medium">Back</a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ type: 'local' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('storekeeper.suppliers.store') }}" method="POST" class="p-6">
                    @csrf
                    
                    <!-- Tabs -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <span class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                General Information
                            </span>
                            <span class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Documents (Available after Save)
                            </span>
                        </nav>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Info -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 uppercase tracking-wide">Company Details</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                                <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                                    <select name="type" x-model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="local">Local</option>
                                        <option value="overseas">Overseas</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                    <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">Select Category</option>
                                        <option value="Manufacturer">Manufacturer</option>
                                        <option value="Trader">Trader</option>
                                        <option value="Service Provider">Service Provider</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="blacklisted">Blacklisted</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                                <input type="url" name="website" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="https://">
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax ID / TRN</label>
                                <input type="text" name="tax_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <!-- Contact & Finance -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 uppercase tracking-wide">Contact & Financial</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Person</label>
                                <input type="text" name="contact_person" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                    <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                <textarea name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                            
                            <!-- Overseas Logic -->
                            <div x-show="type === 'overseas'">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                                        <select name="currency_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="">Select Currency</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Incoterm</label>
                                        <select name="incoterm" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="">Select Incoterm</option>
                                            <option value="EXW">EXW</option>
                                            <option value="FOB">FOB</option>
                                            <option value="CIF">CIF</option>
                                            <option value="DDP">DDP</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Terms</label>
                                <input type="text" name="payment_terms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g., 30 Days Net">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow-lg">
                            Save Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
