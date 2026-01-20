<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Payment Receipt') }} #{{ $payment->payment_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('accounts.payments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Back to Payments
                </a>
                <a href="{{ route('accounts.invoices.show', $payment->invoice) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Back to Invoice
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    
                    <div class="mb-4 flex justify-end">
                        <a href="{{ route('accounts.payments.receipt', $payment) }}" target="_blank" class="flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download PDF
                        </a>
                    </div>

                    <div class="w-full h-screen border border-gray-300 rounded-lg">
                        <iframe src="{{ route('accounts.payments.receipt', $payment) }}" class="w-full h-full" style="min-height: 800px;"></iframe>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
