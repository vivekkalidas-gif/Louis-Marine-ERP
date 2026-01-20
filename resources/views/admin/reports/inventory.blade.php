<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Valuation -->
             <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-bold uppercase text-gray-500 dark:text-gray-400">Total Inventory Valuation</h3>
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">QAR {{ number_format($totalValuation, 2) }}</div>
             </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Low Stock -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-red-600">Low Stock Alerts</h3>
                    @if($lowStock->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($lowStock as $item)
                            <li class="py-2 flex justify-between">
                                <span>{{ $item->name }}</span>
                                <span class="font-bold text-red-600">{{ $item->stock_quantity }} left</span>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-green-600">No items below minimum stock level.</p>
                    @endif
                </div>
                
                 <!-- Stock Summary -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Stock Levels (Bottom 50)</h3>
                    <div class="overflow-y-auto max-h-60">
                         <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Item</th>
                                    <th class="text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $prod)
                                <tr>
                                    <td class="text-sm">{{ $prod->name }}</td>
                                    <td class="text-sm text-right font-bold">{{ $prod->stock_quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
