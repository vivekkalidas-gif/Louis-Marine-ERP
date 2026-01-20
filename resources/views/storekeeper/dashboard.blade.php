<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Store Keeper Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Items -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-20 text-blue-600 dark:text-blue-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Items</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalItems }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20 text-yellow-600 dark:text-yellow-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Low Stock</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lowStockItems }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Out of Stock -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-500 bg-opacity-20 text-red-600 dark:text-red-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Out of Stock</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $outOfStockItems }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-20 text-purple-600 dark:text-purple-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pending Requests</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingRequests }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            @if($lowStockAlerts->count() > 0)
            <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-600 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400 dark:text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Low Stock Alert!</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <p>The following items are running low on stock:</p>
                            <ul class="list-disc list-inside mt-2">
                                @foreach($lowStockAlerts as $item)
                                <li>
                                    <strong>{{ $item->item_name }}</strong> - 
                                    Current: {{ $item->current_stock }} {{ $item->unit }} 
                                    (Min: {{ $item->minimum_stock_level }})
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Pending Material Requests -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pending Material Requests</h3>
                            <a href="{{ route('storekeeper.material-requests.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                        </div>
                        @if($pendingMaterialRequests->count() > 0)
                        <div class="space-y-3">
                            @foreach($pendingMaterialRequests as $request)
                            <div class="border-l-4 border-yellow-400 dark:border-yellow-500 pl-4 py-2">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $request->item_name ?? $request->inventoryItem->item_name ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Qty: {{ $request->quantity }} | 
                                    By: {{ $request->requester->name }} |
                                    {{ $request->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No pending requests</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Stock Movements -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Stock Movements</h3>
                            <a href="{{ route('storekeeper.stock.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                        </div>
                        @if($recentMovements->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentMovements as $movement)
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $movement->inventoryItem->item_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ ucfirst($movement->movement_type) }} | 
                                        {{ $movement->created_at->format('d M, H:i') }}
                                    </p>
                                </div>
                                <span class="text-sm font-bold {{ $movement->quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No recent movements</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
