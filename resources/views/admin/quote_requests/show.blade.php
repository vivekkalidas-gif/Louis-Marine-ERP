<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
                {{ __('Quote Request Details: ') . $quoteRequest->reference_no }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.quote-requests.index') }}" class="bg-gray-200 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-md hover:bg-gray-300 transition">Back to List</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Content: Items -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-bold mb-4 border-b pb-2">Requested Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left">Product</th>
                                            <th class="px-4 py-2 text-center">SKU</th>
                                            <th class="px-4 py-2 text-center">Quantity</th>
                                            <th class="px-4 py-2 text-right">Stock Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($quoteRequest->items as $item)
                                            <tr>
                                                <td class="px-4 py-4">
                                                    <div class="flex items-center">
                                                        @php
                                                            $images = $item->product->image_paths ?? [];
                                                            $thumb = !empty($images) ? $images[0] : null;
                                                        @endphp
                                                        <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 mr-3">
                                                            @if($thumb)
                                                                <img src="{{ Storage::url($thumb) }}" alt="" class="h-full w-full object-cover">
                                                            @else
                                                                <div class="h-full w-full bg-gray-100 flex items-center justify-center">?</div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="font-bold">{{ $item->product->name }}</div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->product->category->name ?? 'Uncategorized' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center text-sm">{{ $item->product->sku }}</td>
                                                <td class="px-4 py-4 text-center font-bold">{{ $item->quantity }}</td>
                                                <td class="px-4 py-4 text-right text-sm">
                                                    @if($item->product->current_stock >= $item->quantity)
                                                        <span class="text-green-600 font-bold">In Stock ({{ $item->product->current_stock }})</span>
                                                    @else
                                                        <span class="text-red-600 font-bold">Low Stock ({{ $item->product->current_stock }})</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Details & Actions -->
                <div class="space-y-6">
                    <!-- Customer Info -->
                    <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-bold mb-4 border-b pb-2">Customer Details</h3>
                            <dl class="space-y-3 text-sm">
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Contact Person</dt>
                                    <dd class="font-bold">{{ $quoteRequest->contact_person }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Company Name</dt>
                                    <dd>{{ $quoteRequest->company_name ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd><a href="mailto:{{ $quoteRequest->email }}" class="text-blue-600 hover:underline">{{ $quoteRequest->email }}</a></dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                    <dd>{{ $quoteRequest->phone ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500 dark:text-gray-400">Request Date</dt>
                                    <dd>{{ $quoteRequest->created_at->format('d M Y H:i') }}</dd>
                                </div>
                            </dl>

                            @if($quoteRequest->message)
                                <div class="mt-4 pt-4 border-t">
                                    <dt class="font-medium text-gray-500 dark:text-gray-400 mb-1">Message</dt>
                                    <dd class="bg-gray-50 dark:bg-marine-800 p-3 rounded text-sm italic">{{ $quoteRequest->message }}</dd>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-bold mb-4 border-b pb-2">Manage Request</h3>
                            
                            <!-- Update Status Form -->
                            <form action="{{ route('admin.quote-requests.update', $quoteRequest) }}" method="POST" class="mb-6">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-marine-800 dark:border-marine-700">
                                        <option value="new" {{ $quoteRequest->status == 'new' ? 'selected' : '' }}>New</option>
                                        <option value="in_review" {{ $quoteRequest->status == 'in_review' ? 'selected' : '' }}>In Review</option>
                                        <option value="quoted" {{ $quoteRequest->status == 'quoted' ? 'selected' : '' }}>Quotation Sent</option>
                                        <option value="closed" {{ $quoteRequest->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                        <option value="converted" {{ $quoteRequest->status == 'converted' ? 'selected' : '' }}>Converted to Order</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded transition">
                                    Update Status
                                </button>
                            </form>

                            <!-- Convert Action -->
                            @if($quoteRequest->status != 'closed' && $quoteRequest->status != 'converted')
                                <form action="{{ route('admin.quote-requests.convert', $quoteRequest) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition flex items-center justify-center">
                                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Create Quotation
                                    </button>
                                </form>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">Creates a draft official quotation.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
