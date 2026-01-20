<x-app-layout>
    <x-slot name="header">
        <h2 class="theme-text-header">
            {{ isset($customer) ? 'Edit Customer' : 'Create Customer' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-card">
                <form method="POST" action="{{ isset($customer) ? route('admin.customers.update', $customer->id) : route('admin.customers.store') }}">
                    @csrf
                    @if(isset($customer))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Info -->
                        <div class="col-span-2 border-b dark:border-marine-800 pb-4 mb-4">
                            <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Company Information</h3>
                        </div>

                        <div>
                            <label class="theme-label">Company Name *</label>
                            <input type="text" name="company_name" class="theme-input" value="{{ old('company_name', $customer->company_name ?? '') }}" required>
                            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                        </div>

                        <div>
                            <label class="theme-label">Customer Type *</label>
                            <select name="type" class="theme-input">
                                @foreach(['Operator', 'Owner', 'Agent', 'Manager'] as $type)
                                    <option value="{{ $type }}" {{ old('type', $customer->type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="theme-label">General Email</label>
                            <input type="email" name="email" class="theme-input" value="{{ old('email', $customer->email ?? '') }}">
                        </div>

                        <div>
                            <label class="theme-label">General Phone</label>
                            <input type="text" name="phone" class="theme-input" value="{{ old('phone', $customer->phone ?? '') }}">
                        </div>

                         <div>
                            <label class="theme-label">Status</label>
                            <select name="status" class="theme-input">
                                <option value="Active" {{ old('status', $customer->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status', $customer->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Commercial Info -->
                         <div class="col-span-2 border-b dark:border-marine-800 pb-4 mb-4 mt-6">
                            <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Commercial Details</h3>
                        </div>

                        <div>
                            <label class="theme-label">CR Number</label>
                            <input type="text" name="cr_number" class="theme-input" value="{{ old('cr_number', $customer->cr_number ?? '') }}">
                        </div>

                        <div>
                            <label class="theme-label">VAT / Tax Number</label>
                            <input type="text" name="vat_number" class="theme-input" value="{{ old('vat_number', $customer->vat_number ?? '') }}">
                        </div>

                        <!-- Address -->
                         <div class="col-span-2 border-b dark:border-marine-800 pb-4 mb-4 mt-6">
                            <h3 class="text-lg font-medium text-marine-900 dark:text-marine-100">Location</h3>
                        </div>

                        <div class="col-span-2">
                            <label class="theme-label">Address</label>
                            <input type="text" name="address" class="theme-input" value="{{ old('address', $customer->address ?? '') }}">
                        </div>

                        <div>
                            <label class="theme-label">City</label>
                            <input type="text" name="city" class="theme-input" value="{{ old('city', $customer->city ?? '') }}">
                        </div>

                        <div>
                            <label class="theme-label">Country</label>
                            <input type="text" name="country" class="theme-input" value="{{ old('country', $customer->country ?? 'Qatar') }}">
                        </div>

                        <div class="col-span-2 mt-6 flex justify-end">
                            <a href="{{ route('admin.customers.index') }}" class="bg-gray-50 dark:bg-gray-7000 text-white px-4 py-2 rounded mr-2 hover:bg-gray-600 uppercase text-xs font-semibold tracking-widest">Cancel</a>
                            <button type="submit" class="bg-marine-700 hover:bg-marine-600 text-white px-6 py-2 rounded font-bold uppercase text-xs tracking-widest">
                                {{ isset($customer) ? 'Update Customer' : 'Create Customer' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    @if(isset($customer))
        @can('delete', $customer)
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-12">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-red-900 dark:text-red-200">
                    <h3 class="text-lg font-bold mb-2">Danger Zone</h3>
                    <p class="mb-4 text-sm">Deleting a customer is irreversible. All contacts and linked data will be deleted.</p>
                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded font-bold hover:bg-red-700 uppercase text-xs tracking-widest">
                            Delete Customer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endcan
    @endif
</x-app-layout>

