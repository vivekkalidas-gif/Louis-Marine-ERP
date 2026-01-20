@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('accounts.vendor-bills.index') }}" class="p-2 rounded-full hover:bg-gray-100 text-gray-500 dark:text-gray-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $vendorBill->bill_number }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Vendor: <a href="{{ route('admin.customers.show', $vendorBill->vendor_id) }}" class="text-indigo-600 hover:underline">{{ $vendorBill->vendor->name }}</a></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($vendorBill->status === 'draft')
                <form action="{{ route('accounts.vendor-bills.post', $vendorBill) }}" method="POST" onsubmit="return confirm('Are you sure you want to post this bill? This will create a Journal Entry.');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
                        Post Bill
                    </button>
                </form>
            @endif
            @if($vendorBill->status === 'posted' || $vendorBill->status === 'partial')
                 <a href="{{ route('accounts.vendor-payments.create', ['vendor_id' => $vendorBill->vendor_id]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
                    Make Payment
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Bill Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Bill Items</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Date: {{ $vendorBill->bill_date->format('M d, Y') }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Account</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($vendorBill->items as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $item->description }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item->account->code }} - {{ $item->account->name }}
                                        @if($item->inventoryItem)
                                            <br><span class="text-xs text-indigo-600">Stock: {{ $item->inventoryItem->item_name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-gray-100">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right font-medium text-gray-600">Subtotal</td>
                                <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-gray-100">{{ number_format($vendorBill->sub_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right font-medium text-gray-600">Tax</td>
                                <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-gray-100">{{ number_format($vendorBill->tax_total, 2) }}</td>
                            </tr>
                            <tr class="bg-indigo-50">
                                <td colspan="4" class="px-6 py-3 text-right font-bold text-indigo-900">Total</td>
                                <td class="px-6 py-3 text-right font-bold text-indigo-900">{{ number_format($vendorBill->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- Notes -->
            @if($vendorBill->notes)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Notes</h4>
                    <p class="text-sm text-gray-600">{{ $vendorBill->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Status</h3>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-gray-600">Current Status</span>
                    @php
                        $colors = [
                            'draft' => 'bg-gray-100 text-gray-800 dark:text-gray-200',
                            'posted' => 'bg-blue-100 text-blue-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'partial' => 'bg-yellow-100 text-yellow-800',
                            'void' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colors[$vendorBill->status] ?? 'bg-gray-100 text-gray-800 dark:text-gray-200' }}">
                        {{ ucfirst($vendorBill->status) }}
                    </span>
                </div>
                
                <div class="border-t border-gray-100 pt-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Due Date</span>
                        <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $vendorBill->due_date->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
                 <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Payment Info</h3>
                 <div class="space-y-3">
                     <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Total Amount</span>
                        <span class="text-gray-900 dark:text-gray-100 font-medium">{{ number_format($vendorBill->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Paid Amount</span>
                        <span class="text-green-600 font-medium">{{ number_format($vendorBill->paid_amount, 2) }}</span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 flex justify-between text-sm">
                        <span class="text-gray-900 dark:text-gray-100 font-bold">Outstanding</span>
                        <span class="text-red-600 font-bold">{{ number_format($vendorBill->outstanding_amount, 2) }}</span>
                    </div>
                 </div>
            </div>

            <!-- Accounting Info -->
            @if($vendorBill->journal_entry_id)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Accounting</h3>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Journal Entry</span>
                        <a href="{{ route('accounts.journals.show', $vendorBill->journal_entry_id) }}" class="text-indigo-600 hover:underline">
                            {{ $vendorBill->journalEntry->name }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
