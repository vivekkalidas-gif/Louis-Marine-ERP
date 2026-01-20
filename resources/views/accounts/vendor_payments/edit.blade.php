@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="paymentForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Vendor Payment</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update payment details for {{ $vendorPayment->payment_number }}</p>
        </div>
        <a href="{{ route('accounts.vendor-payments.show', $vendorPayment) }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-100">
            &larr; Back to Payment
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('accounts.vendor-payments.update', $vendorPayment) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Info -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-100">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor</label>
                    <div class="text-lg font-medium text-gray-900 dark:text-white pt-2">{{ $vendorPayment->vendor->name }}</div>
                    <input type="hidden" name="vendor_id" value="{{ $vendorPayment->vendor_id }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" required value="{{ old('payment_date', $vendorPayment->payment_date->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Paid From (Bank/Cash) <span class="text-red-500">*</span></label>
                    <select name="bank_account_id" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">Select Account</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}" {{ $vendorPayment->bank_account_id == $account->id ? 'selected' : '' }}>{{ $account->name }} ({{Str::upper($account->type)}})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="amount" x-model="totalAmount" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-lg font-bold">
                 </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference</label>
                    <input type="text" name="reference" value="{{ old('reference', $vendorPayment->reference) }}" placeholder="Check No, Transaction ID" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                 </div>
                 <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">{{ old('notes', $vendorPayment->notes) }}</textarea>
                 </div>
            </div>

            <!-- Bill Allocation -->
            <div class="px-6 pb-6">
                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Allocate to Bills</h3>
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Bill #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Outstanding</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase w-32">Allocate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                             @forelse($unpaidBills as $bill)
                                @php
                                    // Check if this bill is already attached
                                    $allocated = $vendorPayment->bills->find($bill->id);
                                    $allocatedAmt = $allocated ? $allocated->pivot->amount : 0;
                                    // Outstanding for display logic: 
                                    // Make sure outstanding amount includes what we are currently paying if we are editing!
                                    // But outstanding_amount on model IS what is left. 
                                    // If we are editing, we haven't posted yet, so outstanding amount shouldn't have changed 
                                    // UNLESS the payment WAS posted and we are editing it? 
                                    // Controller says only DRAFT can be edited. 
                                    // So draft payment does NOT affect outstanding amount yet.
                                    // So outstanding_amount is correct as is.
                                @endphp
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $bill->bill_number }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $bill->bill_date->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 text-right text-sm text-gray-900 dark:text-gray-100">{{ number_format($bill->total_amount, 2) }}</td>
                                    <td class="px-4 py-2 text-right text-sm font-medium text-red-600">{{ number_format($bill->outstanding_amount, 2) }}</td>
                                    <td class="px-4 py-2">
                                        <input type="number" step="0.01" name="allocation[{{ $bill->id }}]" value="{{ $allocatedAmt > 0 ? $allocatedAmt : '' }}" max="{{ $bill->outstanding_amount }}" class="w-full border-gray-300 rounded text-right text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No unpaid bills available for this vendor.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('accounts.vendor-payments.show', $vendorPayment) }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 text-sm font-medium">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm text-sm font-medium transition-colors">Update Payment</button>
            </div>
        </div>
    </form>
</div>

<script>
    function paymentForm() {
        return {
            totalAmount: {{ $vendorPayment->amount }},
        }
    }
</script>
@endsection
