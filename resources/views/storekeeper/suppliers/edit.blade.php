<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Supplier') }} - {{ $supplier->name }}
            </h2>
            <a href="{{ route('storekeeper.suppliers.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-100 font-medium">Back</a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'general', type: '{{ $supplier->type }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-alert-message />

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        <button @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                            class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm">
                            General Information
                        </button>
                        <button @click="activeTab = 'documents'"
                            :class="activeTab === 'documents' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                            class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm">
                            Documents & Compliance
                        </button>
                         <button @click="activeTab = 'performance'"
                            :class="activeTab === 'performance' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                            class="w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm">
                            Performance
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- General Tab -->
                    <div x-show="activeTab === 'general'">
                        <form action="{{ route('storekeeper.suppliers.update', $supplier) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 uppercase tracking-wide">Company Details</h3>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                                        <input type="text" name="name" value="{{ old('name', $supplier->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                                            <select name="type" x-model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                                <option value="local">Local</option>
                                                <option value="overseas">Overseas</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                            <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                                <option value="">Select Category</option>
                                                <option value="Manufacturer" {{ $supplier->category == 'Manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                                <option value="Trader" {{ $supplier->category == 'Trader' ? 'selected' : '' }}>Trader</option>
                                                <option value="Service Provider" {{ $supplier->category == 'Service Provider' ? 'selected' : '' }}>Service Provider</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="active" {{ $supplier->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $supplier->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="blacklisted" {{ $supplier->status == 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                                        <input type="url" name="website" value="{{ old('website', $supplier->website) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>

                                     <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax ID / TRN</label>
                                        <input type="text" name="tax_id" value="{{ old('tax_id', $supplier->tax_id) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 uppercase tracking-wide">Contact & Financial</h3>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Person</label>
                                        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                            <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                            <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                        <textarea name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('address', $supplier->address) }}</textarea>
                                    </div>
                                    
                                    <!-- Overseas Logic -->
                                    <div x-show="type === 'overseas'">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                                                <select name="currency_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                                    <option value="">Select Currency</option>
                                                    @foreach($currencies as $currency)
                                                        <option value="{{ $currency->id }}" {{ $supplier->currency_id == $currency->id ? 'selected' : '' }}>{{ $currency->code }} - {{ $currency->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Incoterm</label>
                                                <select name="incoterm" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                                    <option value="">Select Incoterm</option>
                                                    @foreach(['EXW', 'FOB', 'CIF', 'DDP'] as $term)
                                                        <option value="{{ $term }}" {{ $supplier->incoterm == $term ? 'selected' : '' }}>{{ $term }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Terms</label>
                                        <input type="text" name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                                    Update Details
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Documents Tab -->
                    <div x-show="activeTab === 'documents'" style="display: none;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Compliance Documents</h3>
                            <button @click="$dispatch('open-modal', 'add-document')" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm">
                                Upload Document
                            </button>
                        </div>

                        <div class="border rounded-md overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expiry</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    @forelse($supplier->documents as $doc)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $doc->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($doc->status_color === 'red')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                                            @elseif($doc->status_color === 'yellow')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Expiring Soon</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Valid</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Download</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No documents uploaded.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Modal Placeholder -->
                        <div class="mt-4 p-4 bg-yellow-50 text-yellow-700 rounded text-sm">
                            <p><strong>Note:</strong> File upload functionality will be implemented in the next step via `SupplierDocumentController`.</p>
                        </div>
                    </div>

                    <!-- Performance Tab -->
                    <div x-show="activeTab === 'performance'" style="display: none;">
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Supplier Rating</h3>
                            <div class="flex items-center space-x-2 mb-6">
                                <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $supplier->rating }}/5</span>
                                <div class="flex text-yellow-400">
                                    @for($i=1; $i<=5; $i++)
                                        <svg class="h-6 w-6 {{ $i <= $supplier->rating ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            
                            <!-- Future Placeholder for Detailed Metrics -->
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="p-4 bg-white dark:bg-gray-800 rounded shadow-sm">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Orders</div>
                                    <div class="text-xl font-semibold">0</div>
                                </div>
                                <div class="p-4 bg-white dark:bg-gray-800 rounded shadow-sm">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">On-Time Delivery</div>
                                    <div class="text-xl font-semibold text-green-600">N/A</div>
                                </div>
                                <div class="p-4 bg-white dark:bg-gray-800 rounded shadow-sm">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Quality Rejections</div>
                                    <div class="text-xl font-semibold text-red-600">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
