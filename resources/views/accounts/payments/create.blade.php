<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Record Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('accounts.payments.store') }}" method="POST">
                        @csrf
                        
                        <!-- Invoice Selection (Locked if tracking specific invoice) -->
                        <div class="mb-6">
                            <x-input-label for="invoice_id" :value="__('Select Invoice')" />
                            @if($invoice)
                                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                <div class="mt-1 p-3 bg-gray-100 rounded border border-gray-300">
                                    <span class="font-bold">{{ $invoice->invoice_number }}</span> - 
                                    {{ $invoice->customer->company_name }}
                                    <span class="float-right text-red-600 font-bold">Due: {{ number_format($invoice->balance, 2) }}</span>
                                </div>
                            @else
                                <select id="invoice_id" name="invoice_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">-- Choose Pending Invoice --</option>
                                    @foreach($invoices as $inv)
                                        <option value="{{ $inv->id }}">
                                            {{ $inv->invoice_number }} - {{ $inv->customer->company_name }} (Bal: {{ $inv->balance }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Date -->
                            <div>
                                <x-input-label for="payment_date" :value="__('Payment Date')" />
                                <x-text-input id="payment_date" class="block mt-1 w-full" type="date" name="payment_date" value="{{ date('Y-m-d') }}" required />
                            </div>

                            <!-- Method -->
                            <div>
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <select id="payment_method" name="payment_method" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit_card">Credit Card</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Amount -->
                            <div>
                                <x-input-label for="amount" :value="__('Amount Received (QAR)')" />
                                <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" name="amount" value="{{ $invoice ? $invoice->balance : '' }}" required max="{{ $invoice ? $invoice->balance : '' }}" />
                                @if($invoice)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max: {{ $invoice->balance }}</p>
                                @endif
                            </div>

                            <!-- Reference -->
                            <div>
                                <x-input-label for="reference_number" :value="__('Reference / Cheque No.')" />
                                <x-text-input id="reference_number" class="block mt-1 w-full" type="text" name="reference_number" />
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="print_receipt" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                                <span class="ml-2 text-sm text-gray-600">Generate Receipt PDF automatically</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button type="button" onclick="history.back()" class="mr-3">
                                Cancel
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Record Payment') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
