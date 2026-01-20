<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Service Type') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @php
                        $routePrefix = request()->routeIs('engineer.*') ? 'engineer' : 'admin';
                    @endphp
                    <form method="POST" action="{{ route($routePrefix . '.service-types.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service Name</label>
                                <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 dark:bg-gray-700 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code</label>
                                <input type="text" name="code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 dark:bg-gray-700 dark:text-white" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Quantity</label>
                                <input type="number" name="quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 dark:bg-gray-700 dark:text-white">
                            </div>
                            
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || auth()->user()->hasRole('accountant'))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Price (QAR)</label>
                                    <input type="number" name="default_price" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 dark:bg-gray-700 dark:text-white" required>
                                </div>
                            @else
                                <input type="hidden" name="default_price" value="0">
                            @endif
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 dark:bg-gray-700 dark:text-white"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Save Service Type</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
