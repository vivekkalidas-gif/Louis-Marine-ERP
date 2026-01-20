<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $customer->company_name }}
            </h2>
            <div>
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded font-bold hover:bg-yellow-600">
                    Edit Profile
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'overview' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tabs Header -->
            <div class="bg-white dark:bg-marine-900 shadow sm:rounded-t-lg border-b border-gray-200 dark:border-marine-800">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    @foreach(['overview' => 'Overview', 'contacts' => 'Contacts', 'vessels' => 'Vessels', 'documents' => 'Documents', 'history' => 'History', 'notes' => 'Notes'] as $key => $label)
                        <button @click="tab = '{{ $key }}'" 
                            :class="tab === '{{ $key }}' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-300 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            {{ $label }}
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="bg-white dark:bg-marine-900 shadow sm:rounded-b-lg p-6 min-h-[400px]">
                
                <!-- OVERVIEW TAB -->
                <div x-show="tab === 'overview'">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                             <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">Company Details</h3>
                             <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Industry Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $customer->type }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $customer->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $customer->status }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $customer->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $customer->phone }}</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ $customer->address }}<br>{{ $customer->city }}, {{ $customer->country }}</dd>
                                </div>
                             </dl>
                        </div>
                        <div class="bg-gray-50 dark:bg-marine-800 p-4 rounded border dark:border-marine-700">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Internal Stats</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Vessels</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $customer->vessels->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Active Jobs</span>
                                    <span class="font-bold text-gray-900 dark:text-white">-</span> {{-- Placeholder --}}
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">Pending Invoices</span>
                                    <span class="font-bold text-gray-900 dark:text-white">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CONTACTS TAB -->
                <div x-show="tab === 'contacts'" style="display: none;">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Contact Persons</h3>
                        <button onclick="document.getElementById('addContactModal').classList.remove('hidden')" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Add Contact</button>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-marine-800">
                        <thead class="bg-gray-50 dark:bg-marine-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Contact Person</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Designation & Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Contact Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Permissions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-marine-800">
                            @foreach($customer->contacts as $contact)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $contact->name }} 
                                        @if($contact->is_primary) 
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Primary</span> 
                                        @endif
                                        <div class="text-xs text-gray-400 dark:text-gray-500 dark:text-gray-400 font-normal">{{ $contact->status }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div class="font-bold text-gray-700 dark:text-gray-300">{{ $contact->contact_type }}</div>
                                        <div class="text-xs">{{ $contact->designation ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div>{{ $contact->email }}</div>
                                        <div class="text-xs">{{ $contact->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        @if($contact->can_request_job)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Job Req</span>
                                        @endif
                                        @if($contact->can_receive_invoice)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Invoice</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- VESSELS TAB -->
                <div x-show="tab === 'vessels'" style="display: none;">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Managed Vessels</h3>
                        {{-- <button onclick="document.getElementById('addVesselModal').classList.remove('hidden')" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Link Vessel</button> --}}
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($customer->vessels as $vessel)
                            <div class="border dark:border-marine-700 rounded p-4 flex flex-col justify-between bg-white dark:bg-marine-800 shadow-sm">
                                <div>
                                    <h4 class="font-bold text-lg text-marine-900 dark:text-white">{{ $vessel->name }}</h4>
                                    <div class="text-sm text-gray-500 dark:text-gray-300">{{ $vessel->type ?? 'Unknown Type' }}</div>
                                    <div class="text-xs text-gray-400 mt-1">IMO: {{ $vessel->imo_number }}</div>
                                </div>
                                <div class="mt-4 pt-2 border-t dark:border-marine-700 flex justify-end">
                                    <a href="#" class="text-blue-600 dark:text-blue-400 text-sm hover:underline">View Details</a> 
                                    {{-- Link to Vessel Show Page in future --}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- DOCUMENTS TAB -->
                <div x-show="tab === 'documents'" style="display: none;">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Documents</h3>
                        <button onclick="document.getElementById('addDocModal').classList.remove('hidden')" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Upload</button>
                    </div>
                    <ul class="divide-y divide-gray-200 dark:divide-marine-800">
                         @foreach($customer->documents as $doc)
                            <li class="py-3 flex justify-between items-center">
                                <div class="flex items-center">
                                    <svg class="h-8 w-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $doc->title }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Expires: {{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : 'N/A' }}</div>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm">Download</a>
                            </li>
                         @endforeach
                    </ul>
                </div>

                 <!-- NOTES TAB -->
                <div x-show="tab === 'notes'" style="display: none;">
                    <div class="mb-4">
                        <form action="{{ route('admin.customers.notes.store', $customer->id) }}" method="POST">
                            @csrf
                            <textarea name="content" rows="3" class="w-full border-gray-300 dark:border-marine-700 bg-white dark:bg-marine-800 text-gray-900 dark:text-gray-100 rounded shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Add an internal note..."></textarea>
                            <div class="mt-2 text-right">
                                <button type="submit" class="bg-gray-800 dark:bg-marine-700 text-white px-4 py-2 rounded text-sm hover:bg-gray-900 dark:hover:bg-marine-600">Add Note</button>
                            </div>
                        </form>
                    </div>
                    <div class="space-y-4">
                        @foreach($customer->customerNotes->sortByDesc('created_at') as $note)
                            <div class="bg-gray-50 dark:bg-marine-800 p-4 rounded border dark:border-marine-700">
                                <p class="text-gray-800 dark:text-gray-200">{{ $note->content }}</p>
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex justify-between">
                                    <span>{{ $note->author->name ?? 'Unknown' }}</span>
                                    <span>{{ $note->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                 <!-- HISTORY TAB (Placeholder) -->
                <div x-show="tab === 'history'" style="display: none;">
                    <p class="text-gray-500 dark:text-gray-400 text-center py-10">Service and Certificate history will appear here once linked.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Modals (Hidden by default) -->
    <!-- Add Contact Modal -->
    <div id="addContactModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border dark:border-marine-700 w-96 shadow-lg rounded-md bg-white dark:bg-marine-900">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Add Contact</h3>
             <form action="{{ route('admin.customers.contacts.store', $customer->id) }}" method="POST" class="mt-4">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-2">
                         <input type="text" name="name" placeholder="Full Name" class="theme-input col-span-2" required>
                         <input type="text" name="designation" placeholder="Designation" class="theme-input">
                         <select name="contact_type" class="theme-input">
                             <option value="General">General</option>
                             <option value="Engineer">Engineer</option>
                             <option value="Accounts">Accounts</option>
                             <option value="Manager">Manager</option>
                             <option value="Purchase">Purchase</option>
                         </select>
                         <input type="email" name="email" placeholder="Email" class="theme-input col-span-2">
                         <input type="text" name="phone" placeholder="Phone" class="theme-input col-span-2">
                    </div>
                    
                    <div class="flex flex-col space-y-2 mt-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_primary" value="1" class="rounded border-gray-300 dark:border-marine-700 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-marine-800">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300 font-bold">Primary Contact</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="can_request_job" value="1" class="rounded border-gray-300 dark:border-marine-700 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-marine-800">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Can Request Jobs</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="can_receive_invoice" value="1" class="rounded border-gray-300 dark:border-marine-700 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-marine-800">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Receives Invoices</span>
                        </label>
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('addContactModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 dark:bg-marine-800 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-marine-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Vessel Modal (Disabled for Module Separation) -->
    {{-- <div id="addVesselModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Link New Vessel</h3>
             <form action="{{ route('admin.customers.vessels.store', $customer->id) }}" method="POST" class="mt-4">
                @csrf
                <div class="space-y-4">
                    <input type="text" name="name" placeholder="Vessel Name" class="w-full border rounded p-2" required>
                    <input type="text" name="imo_number" placeholder="IMO Number" class="w-full border rounded p-2">
                    <input type="text" name="type" placeholder="Type (e.g. Tanker)" class="w-full border rounded p-2">
                    <input type="text" name="flag" placeholder="Flag" class="w-full border rounded p-2">
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('addVesselModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">create & Link</button>
                </div>
            </form>
        </div>
    </div> --}}

    <!-- Add Document Modal -->
    <div id="addDocModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border dark:border-marine-700 w-96 shadow-lg rounded-md bg-white dark:bg-marine-900">
             <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Upload Document</h3>
             <form action="{{ route('admin.customers.documents.store', $customer->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="space-y-4">
                    <input type="text" name="title" placeholder="Document Title" class="theme-input" required>
                    <input type="date" name="expiry_date" class="theme-input">
                    <input type="file" name="file" class="w-full border rounded p-2 dark:bg-marine-800 dark:border-marine-700 dark:text-gray-300" required>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('addDocModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 dark:bg-marine-800 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-marine-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
