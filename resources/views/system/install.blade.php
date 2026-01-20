<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Install Louis Marine CRM</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 py-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-marine-900 dark:text-white mb-2">Louis Marine CRM</h1>
                <p class="text-gray-500 dark:text-gray-400">System Installation Wizard</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="space-y-4 mb-8 text-sm text-gray-600 dark:text-gray-300">
                <p>This will:</p>
                <ul class="list-disc list-inside ml-2">
                    <li>Reset the Database (Migrate Fresh)</li>
                    <li>Seed Default Settings & Certificate Types</li>
                    <li>Create Admin User (admin@louis-marine.com)</li>
                    <li>Create Demo Data (Customers, Inventory)</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('system.install.run') }}">
                @csrf
                <button type="submit" class="w-full bg-marine-600 hover:bg-marine-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                    Install System
                </button>
            </form>
        </div>
    </body>
</html>
