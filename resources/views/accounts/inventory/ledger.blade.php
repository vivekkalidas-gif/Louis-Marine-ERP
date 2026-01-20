@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Stock Ledger</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Transaction history and stock movements.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('accounts.inventory.valuation') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">Back to Valuation</a>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1 max-w-sm">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Item</label>
                <select name="item_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="this.form.submit()">
                    <option value="">All Items</option>
                    @foreach($items as $i)
                        <option value="{{ $i->id }}" {{ request('item_id') == $i->id ? 'selected' : '' }}>{{ $i->item_name }} ({{ $i->item_code }})</option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('accounts.inventory.ledger') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:text-gray-100 mb-2">Reset</a>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty</th>
                         <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit Cost</th>
                         <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Cost</th>
                         <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                    @forelse($transactions as $txn)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $txn->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $txn->reference }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $txn->item ? $txn->item->item_name : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $txn->type == 'IN' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $txn->type }}
                                </span>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100 font-medium">{{ number_format($txn->quantity) }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($txn->unit_cost, 2) }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($txn->total_cost, 2) }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($txn->stock_value_after, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">No Transactions Found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
