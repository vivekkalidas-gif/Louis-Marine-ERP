<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Material Request Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Request Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Request Info</h3>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $materialRequest->status === 'approved' ? 'bg-blue-100 text-blue-800' : 
                                          ($materialRequest->status === 'issued' ? 'bg-green-100 text-green-800' : 
                                          ($materialRequest->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                                          'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst($materialRequest->status) }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requester</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $materialRequest->requester->name }} ({{ $materialRequest->requester->email }})
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Service Job</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($materialRequest->job)
                                        <a href="{{ route('admin.jobs.show', $materialRequest->job->id) }}" class="text-blue-600 hover:underline">
                                            {{ $materialRequest->job->job_number }}
                                        </a>
                                        - {{ $materialRequest->job->vessel->name ?? 'Unknown Vessel' }}
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">No Job Linked</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Requested</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $materialRequest->created_at->format('d M Y, H:i') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Urgency</dt>
                                <dd class="mt-1 text-sm font-bold {{ $materialRequest->urgency === 'critical' ? 'text-red-600' : 'text-gray-900 dark:text-gray-100' }}">
                                    {{ ucfirst($materialRequest->urgency) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Item Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Item Details</h3>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Item Name</dt>
                                <dd class="mt-1 text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ $materialRequest->inventoryItem->item_name ?? $materialRequest->item_name ?? 'N/A' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantity Requested</dt>
                                <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $materialRequest->quantity }} 
                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ $materialRequest->inventoryItem->unit ?? 'Nos' }}</span>
                                </dd>
                            </div>

                            @if($materialRequest->inventoryItem)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Current Inventory</p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-2xl font-bold {{ $materialRequest->inventoryItem->current_stock < $materialRequest->quantity ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $materialRequest->inventoryItem->current_stock }}
                                    </span>
                                    <a href="{{ route('storekeeper.inventory.show', $materialRequest->inventoryItem->id) }}" class="text-sm text-blue-600 hover:underline">View Item</a>
                                </div>
                                @if($materialRequest->inventoryItem->current_stock < $materialRequest->quantity)
                                    <p class="text-xs text-red-600 mt-1 font-bold">⚠️ Insufficient Stock!</p>
                                @endif
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Admin Actions -->
                <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Store Keeper Action</h3>

                        @if($materialRequest->status === 'pending')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Approve Form -->
                                <form method="POST" action="{{ route('storekeeper.material-requests.approve', $materialRequest->id) }}" class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-md">
                                    @csrf
                                    <h4 class="font-bold text-blue-800 dark:text-blue-300 mb-2">Approve & Issue</h4>
                                    <p class="text-sm text-blue-600 mb-4">Approving this request will <strong>immediately deduct stock</strong> and mark it as issued.</p>
                                    
                                    @if($materialRequest->inventoryItem && $materialRequest->inventoryItem->current_stock < $materialRequest->quantity)
                                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mb-4 text-sm">
                                            <p class="font-bold">Warning: Insufficient Stock</p>
                                            <p>Available: {{ $materialRequest->inventoryItem->current_stock }}</p>
                                        </div>
                                    @endif

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks (Optional)</label>
                                        <textarea name="admin_remarks" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"></textarea>
                                    </div>
                                    
                                    <button type="submit" 
                                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 {{ ($materialRequest->inventoryItem && $materialRequest->inventoryItem->current_stock < $materialRequest->quantity) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ ($materialRequest->inventoryItem && $materialRequest->inventoryItem->current_stock < $materialRequest->quantity) ? 'disabled' : '' }}>
                                        Approve & Issue Materials
                                    </button>
                                </form>

                                <!-- Reject Form -->
                                <form method="POST" action="{{ route('storekeeper.material-requests.reject', $materialRequest->id) }}" class="bg-red-50 dark:bg-red-900/20 p-4 rounded-md">
                                    @csrf
                                    <h4 class="font-bold text-red-800 dark:text-red-300 mb-2">Reject Request</h4>
                                    <p class="text-sm text-red-600 mb-4">Reject this request if materials cannot be provided.</p>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Rejection (Required)</label>
                                        <textarea name="admin_remarks" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"></textarea>
                                    </div>

                                    <button type="submit" onclick="return confirm('Are you sure you want to reject this request?')" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Reject</button>
                                </form>
                            </div>

                        @elseif($materialRequest->status === 'approved')
                            <!-- Fallback for legacy approved requests -->
                             <div class="bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-md border border-yellow-200">
                                <h4 class="text-lg font-bold text-yellow-800 dark:text-yellow-300 mb-2">Request Approved awaiting Issuance</h4>
                                <form method="POST" action="{{ route('storekeeper.material-requests.approve', $materialRequest->id) }}">
                                    @csrf
                                    <p class="text-sm text-yellow-700 mb-4">This request is in an old state. Click below to complete the issuance.</p>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Complete Issuance</button>
                                </form>
                             </div>

                        @else
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <p class="text-gray-700 dark:text-gray-300 font-medium">Remarks Log:</p>
                                <p class="mt-2 text-gray-600 dark:text-gray-400 italic">
                                    "{{ $materialRequest->admin_remarks ?: 'No remarks recorded.' }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
