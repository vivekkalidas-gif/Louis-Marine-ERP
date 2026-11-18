<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Install Louis Marine CRM</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen py-10">
        <div class="w-full max-w-lg bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 py-10">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-marine-900 dark:text-white mb-2">Louis Marine CRM</h1>
                <p class="text-gray-500 dark:text-gray-400">System Setup & Database Configuration</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('system.install.run') }}" class="space-y-5">
                @csrf

                <div class="border-b pb-2">
                    <h3 class="text-md font-semibold text-gray-700 dark:text-gray-200">Database Connection Credentials</h3>
                    <p class="text-xs text-gray-500">Specify your MySQL database details below.</p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Database Host</label>
                        <input type="text" name="db_host" value="{{ old('db_host', $db_host ?? '127.0.0.1') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Port</label>
                        <input type="text" name="db_port" value="{{ old('db_port', $db_port ?? '3306') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Database Name</label>
                    <input type="text" name="db_database" value="{{ old('db_database', $db_database ?? 'louis_local') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    <span class="text-xs text-gray-400">If the database does not exist, the installer will attempt to create it automatically.</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                        <input type="text" name="db_username" value="{{ old('db_username', $db_username ?? 'root') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input type="password" name="db_password" value="{{ old('db_password', $db_password ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:border-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border" placeholder="(Leave empty for XAMPP)">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        Save Configuration & Run Install
                    </button>
                </div>
            </form>
        </div>
    </body>
</html>
