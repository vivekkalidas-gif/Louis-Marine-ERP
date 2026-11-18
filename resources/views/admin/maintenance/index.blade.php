<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('System Maintenance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            These tools perform database operations. Only usage if you know what you are doing.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- 1. Fix Chart of Accounts -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-2">Fix "Finalize Invoice" Error</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                            This runs the <code>AccountingSeeder</code> to create missing standard accounts (Cash, Bank, Accounts Receivable, Income).
                            <br><strong>Use this if you cannot finalize invoices (Draft status stuck).</strong>
                        </p>
                        
                        <form action="{{ route('admin.maintenance.seed-accounting') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                                <i class="fas fa-database mr-2"></i> Initialize Accounting System
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 2. Clear Cache -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-2">Clear System Cache</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                            Clears Route, View, and Config caches. Run this if you deployed new code but don't see the changes.
                        </p>
                        
                        <form action="{{ route('admin.maintenance.clear-cache') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                                <i class="fas fa-broom mr-2"></i> Clear All Caches
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
