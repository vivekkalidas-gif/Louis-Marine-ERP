<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $quotation->quotation_number }}
            </h2>
             <div class="flex gap-2">
                @if($quotation->status != 'CONVERTED')
                    <form action="{{ route('admin.quotations.convert', $quotation) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Convert this quotation to an invoice?')">
                            Convert to Invoice
                        </button>
                    </form>
                @else
                    <span class="bg-purple-100 text-purple-800 font-bold py-2 px-4 rounded">Converted to Invoice</span>
                @endif
                <a href="{{ route('admin.quotations.print', $quotation) }}" target="_blank" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Print PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Quotation Details -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 flex justify-between">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Quotation Information</h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Prepared for {{ $quotation->customer->company_name }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-sm font-bold {{ $quotation->status == 'CONVERTED' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $quotation->status }}
                                </span>
                            </div>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Issued</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $quotation->date->format('d M Y') }}</dd>
                                </div>
                                <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Valid Until</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">{{ $quotation->valid_until->format('d M Y') }}</dd>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Related Job</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:mt-0 sm:col-span-2">
                                        @if($quotation->job)
                                            <a href="{{ route('admin.jobs.show', $quotation->job) }}" class="text-blue-600 hover:underline">{{ $quotation->job->job_number }}</a>
                                        @else
                                            <span class="text-gray-400">None</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Quoted Items</h3>
                        </div>
                        <div class="border-t border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit Price</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    @foreach($quotation->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $item->description }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 text-right">{{ $item->quantity }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 text-right">{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                     <tr>
                                        <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-500 dark:text-gray-400">Subtotal</td>
                                        <td class="px-6 py-3 text-right font-bold text-gray-900 dark:text-gray-100">{{ number_format($quotation->subtotal, 2) }}</td>
                                    </tr>
                                     <tr>
                                        <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-500 dark:text-gray-400">VAT (5%)</td>
                                        <td class="px-6 py-3 text-right font-bold text-gray-900 dark:text-gray-100">{{ number_format($quotation->tax, 2) }}</td>
                                    </tr>
                                     <tr>
                                        <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-900 dark:text-gray-100 text-lg">Total</td>
                                        <td class="px-6 py-3 text-right font-bold text-blue-600 text-lg">{{ number_format($quotation->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Terms & Conditions</h3>
                        <p class="text-gray-600 whitespace-pre-wrap">{{ $quotation->terms }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
