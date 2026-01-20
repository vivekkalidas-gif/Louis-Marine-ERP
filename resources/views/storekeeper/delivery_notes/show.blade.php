@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $deliveryNote->dr_number }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium 
                    {{ $deliveryNote->status === 'invoiced' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($deliveryNote->status) }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Created on {{ $deliveryNote->date->format('M d, Y') }} by {{ $deliveryNote->creator->name ?? 'Unknown' }}
            </p>
        </div>
        <div class="flex gap-3">
            @if($deliveryNote->status === 'draft')
                <form action="{{ route('storekeeper.delivery-notes.dispatch', $deliveryNote->id) }}" method="POST" onsubmit="return confirm('Use this to deduct stock. Are you sure you want to dispatch these items?');">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm transition-colors">
                        Dispatch Items
                    </button>
                </form>
            @endif

            @if(!$deliveryNote->invoice_id && $deliveryNote->status === 'dispatched')
                <form action="{{ route('storekeeper.delivery-notes.create-invoice', $deliveryNote->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm transition-colors">
                        Create Invoice
                    </button>
                </form>
            @elseif($deliveryNote->invoice_id)
                <a href="{{ route('accounts.invoices.show', $deliveryNote->invoice_id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    View Invoice
                </a>
            @endif
            
            <a href="{{ route('storekeeper.delivery-notes.print', $deliveryNote->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                Print
            </a>

            @if($deliveryNote->status === 'draft')
                <a href="{{ route('storekeeper.delivery-notes.edit', $deliveryNote->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 px-4 py-2 text-sm font-medium">Edit</a>
            @endif
            
            <a href="{{ route('storekeeper.delivery-notes.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 px-4 py-2 text-sm">Back</a>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 grid grid-cols-2 gap-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $deliveryNote->customer->company_name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reference</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $deliveryNote->reference ?? '-' }}</dd>
            </div>
        </div>

        <div class="p-6">
            <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-4">Items</h3>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($deliveryNote->items as $item)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $item->inventoryItem->item_name ?? 'Item' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $item->description }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100 text-right">{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($deliveryNote->notes)
            <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Notes</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $deliveryNote->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
