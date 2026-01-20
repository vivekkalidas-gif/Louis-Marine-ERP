@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Landed Cost #{{ $landedCost->id }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">PO: {{ $landedCost->purchaseOrder->po_number }} | Ref: {{ $landedCost->reference }}</p>
        </div>
        <div class="flex items-center gap-3">
             <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $landedCost->status == 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ ucfirst($landedCost->status) }}
            </span>
             @if($landedCost->status == 'draft')
                <form action="{{ route('accounts.landed-costs.post', $landedCost->id) }}" method="POST" onsubmit="return confirm('Are you sure? This will update inventory costs.')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white border border-transparent rounded-lg text-sm font-medium hover:bg-green-700">Post & Update Cost</button>
                </form>
            @endif
        </div>
    </div>

    <!-- PO Details -->
     <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Input Items (Purchase Order)</h3>
        <div class="overflow-x-auto">
             <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Item</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantity</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit Price</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Price</th>
                         @if($landedCost->status == 'posted' || $landedCost->lines->count() > 0)
                            <th class="px-4 py-2 text-right text-xs font-medium text-green-600 uppercase">Allocated Cost</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-indigo-600 uppercase">New Item Cost</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($landedCost->purchaseOrder->items as $item)
                        @php
                            $allocated = $landedCost->allocations->where('inventory_item_id', $item->inventory_item_id)->sum('amount');
                            $newUnitCost = $item->quantity > 0 ? ($item->total_price + $allocated) / $item->quantity : 0;
                        @endphp
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->inventoryItem->item_name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($item->quantity) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($item->total_price, 2) }}</td>
                            @if($landedCost->status == 'posted' || $landedCost->lines->count() > 0)
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right font-medium text-green-600">+ {{ number_format($allocated, 2) }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right font-bold text-indigo-600">{{ number_format($newUnitCost, 2) }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
             </table>
        </div>
     </div>

    <!-- Cost Lines -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
             <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Cost Lines (Freight, Duty, etc.)</h3>
        </div>
        
         <div class="overflow-x-auto mb-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Expense Account</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Allocation Method</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($landedCost->lines as $line)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $line->description }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $line->account ? $line->account->name : '-' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($line->allocation_method) }}</td>
                            <td class="px-4 py-2 text-right text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($line->amount, 2) }}</td>
                            <td class="px-4 py-2 text-right text-sm">
                                @if($landedCost->status == 'draft')
                                    <form action="{{ route('accounts.landed-costs.remove-line', [$landedCost->id, $line->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <td colspan="3" class="px-4 py-2 text-sm font-bold text-right text-gray-900 dark:text-gray-100">Total Landed Cost:</td>
                        <td class="px-4 py-2 text-right text-sm font-bold text-indigo-600">{{ number_format($landedCost->total_amount, 2) }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
         </div>

        <!-- Add Line Form -->
        @if($landedCost->status == 'draft')
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Add New Cost</h4>
                <form action="{{ route('accounts.landed-costs.add-line', $landedCost->id) }}" method="POST" class="grid grid-cols-5 gap-4 items-end">
                    @csrf
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Description</label>
                        <input type="text" name="description" placeholder="e.g. Shipping Charges" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Account</label>
                        <select name="account_id" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            @foreach($expenseAccounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Amount</label>
                         <input type="number" step="0.01" name="amount" placeholder="0.00" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                         <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Method</label>
                        <select name="allocation_method" class="block w-full rounded-md border-gray-300 shadow-sm text-sm mb-2">
                            <option value="value">By Value</option>
                            <option value="quantity">By Quantity</option>
                        </select>
                        <button type="submit" class="w-full bg-indigo-600 text-white px-3 py-2 rounded-md hover:bg-indigo-700 text-sm">Add Cost</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
