<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Edit Vessel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div x-data="vesselEditForm()">
                        <form action="{{ route('admin.vessels.update', $vessel->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Customer Selection -->
                                <div>
                                    <label class="block text-sm font-bold mb-2">Customer *</label>
                                    <select name="customer_id" x-model="customer_id" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                                        <option value="">Select Customer</option>
                                        <template x-for="c in customers" :key="c.id">
                                            <option :value="c.id" x-text="c.company_name" :selected="c.id == customer_id"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Contact Selection (Dynamic) -->
                                <div>
                                    <label class="block text-sm font-bold mb-2">Assigned Contact (Engineer-in-Charge)</label>
                                    <select name="company_contact_id" x-model="company_contact_id" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" :disabled="!customer_id">
                                        <option value="">Select Contact</option>
                                        <template x-for="contact in filteredContacts" :key="contact.id">
                                            <option :value="contact.id" x-text="contact.name + ' (' + (contact.designation || 'Staff') + ')'" :selected="contact.id == company_contact_id"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-2">Vessel Name</label>
                                    <input type="text" name="name" value="{{ $vessel->name }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold mb-2">IMO Number</label>
                                    <input type="text" name="imo_number" value="{{ $vessel->imo_number }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold mb-2">Type</label>
                                    <div class="flex gap-2">
                                        <select name="type" x-model="type" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                            <option value="">Select Type</option>
                                            <template x-for="t in vesselTypes" :key="t">
                                                <option :value="t" x-text="t" :selected="type === t"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="addNewType()" class="bg-gray-200 dark:bg-marine-700 hover:bg-gray-300 dark:hover:bg-marine-600 px-3 rounded font-bold" title="Add New Type">+</button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold mb-2">Status</label>
                                    <select name="status" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700">
                                        <option value="active" {{ $vessel->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $vessel->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="maintenance" {{ $vessel->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <a href="{{ route('admin.vessels.index') }}" class="mr-4 bg-gray-200 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded hover:bg-gray-300">Cancel</a>
                                <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                                    Update Vessel
                                </button>
                            </div>
                        </form>
                    </div>

                    <script>
                        function vesselEditForm() {
                            return {
                                customer_id: '{{ $vessel->customer_id }}',
                                company_contact_id: '{{ $vessel->company_contact_id }}',
                                customers: @json($customers),
                                vesselTypes: @json($vesselTypes ?? []),
                                type: '{{ $vessel->type }}',
                                get filteredContacts() {
                                    if (!this.customer_id) return [];
                                    const customer = this.customers.find(c => c.id == this.customer_id);
                                    return customer ? customer.contacts : [];
                                },
                                addNewType() {
                                    const newType = prompt("Enter new vessel type:");
                                    if (newType && newType.trim() !== "") {
                                        const formatted = newType.trim();
                                        if (!this.vesselTypes.includes(formatted)) {
                                            this.vesselTypes.push(formatted);
                                            this.vesselTypes.sort();
                                        }
                                        this.type = formatted;
                                    }
                                }
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
