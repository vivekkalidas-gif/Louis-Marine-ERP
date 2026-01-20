@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Inventory Valuation</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Current stock value based on Weighted Average Cost.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-100">
                <span class="text-sm text-indigo-700 font-medium">Total Inventory Value:</span>
                <span class="text-lg font-bold text-indigo-900 ml-2">{{ number_format($totalValue, 2) }}</span>
            </div>
             <a href="{{ route('accounts.inventory.ledger') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">View Stock Ledger</a>
        </div>
    </div>

    <!-- Valuation Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item Name</th>
                         <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty Hand</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Cost</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Value</th>
                         <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">GL Account</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800">
                    @forelse($items as $item)
                        @php $value = $item->current_stock * $item->purchase_price; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->item_code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->item_name }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($item->current_stock) }} {{ $item->unit }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($item->purchase_price, 2) }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-gray-100">{{ number_format($value, 2) }}</td>
                              <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                {{ $item->inventoryAccount ? $item->inventoryAccount->name : 'Unassigned' }}
                              </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">No Inventory Found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
