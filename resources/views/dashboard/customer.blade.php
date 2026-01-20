<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Customer Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-marine-900 dark:text-marine-100">
                    {{ __("Welcome! Track your vessel service status.") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
