@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
             <a href="{{ route('accounts.vendor-payments.index') }}" class="p-2 rounded-full hover:bg-gray-100 text-gray-500 dark:text-gray-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $vendorPayment->payment_number }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">To: {{ $vendorPayment->vendor->name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <!-- Print & PDF -->
            <a href="{{ route('accounts.vendor-payments.print', $vendorPayment) }}" target="_blank" class="px-3 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg shadow-sm text-sm font-medium transition-colors">
                Print
            </a>
            <a href="{{ route('accounts.vendor-payments.pdf', $vendorPayment) }}" class="px-3 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg shadow-sm text-sm font-medium transition-colors">
                PDF
            </a>

            @if($vendorPayment->status === 'draft')
                <!-- Edit -->
                <a href="{{ route('accounts.vendor-payments.edit', $vendorPayment) }}" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
                    Edit
                </a>

                <!-- Delete -->
                <form action="{{ route('accounts.vendor-payments.destroy', $vendorPayment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
                        Delete
                    </button>
                </form>

                <!-- Post -->
                <form action="{{ route('accounts.vendor-payments.post', $vendorPayment) }}" method="POST" onsubmit="return confirm('Post this payment?');" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">
                        Post
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b border-gray-100 pb-2">Payment Details</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="text-gray-500 dark:text-gray-400">Date</div>
                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $vendorPayment->payment_date->format('M d, Y') }}</div>
                
                <div class="text-gray-500 dark:text-gray-400">Amount</div>
                <div class="font-bold text-indigo-600 text-lg">{{ number_format($vendorPayment->amount, 2) }}</div>
                
                <div class="text-gray-500 dark:text-gray-400">Method</div>
                <div class="font-medium text-gray-900 dark:text-gray-100 uppercase">{{ $vendorPayment->payment_method }}</div>
                
                <div class="text-gray-500 dark:text-gray-400">Reference</div>
                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $vendorPayment->reference ?? '-' }}</div>
                
                <div class="text-gray-500 dark:text-gray-400">Status</div>
                <div>
                     <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $vendorPayment->status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800 dark:text-gray-200' }}">
                        {{ ucfirst($vendorPayment->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Allocation -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b border-gray-100 pb-2 mb-4">Bill Allocation</h3>
            <div class="overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left font-medium text-gray-500 dark:text-gray-400 pb-2">Bill #</th>
                            <th class="text-right font-medium text-gray-500 dark:text-gray-400 pb-2">Amount Applied</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendorPayment->bills as $bill)
                            <tr class="border-t border-gray-50">
                                <td class="py-2 text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('accounts.vendor-bills.show', $bill) }}" class="text-indigo-600 hover:underline">
                                        {{ $bill->bill_number }}
                                    </a>
                                </td>
                                <td class="py-2 text-right font-medium">{{ number_format($bill->pivot->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-4 text-center text-gray-500 dark:text-gray-400">No bills allocated.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
     <!-- Accounting Info -->
    @if($vendorPayment->journal_entry_id)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Accounting</h3>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Journal Entry</span>
                <a href="{{ route('accounts.journals.show', $vendorPayment->journal_entry_id) }}" class="text-indigo-600 hover:underline">
                    {{ $vendorPayment->journalEntry->name }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
