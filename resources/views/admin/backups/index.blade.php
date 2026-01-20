<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('System Backups') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-alert-success />
            <x-alert-error />

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6" x-data="{ showSettings: false }">
                        <h3 class="text-lg font-medium mb-4 md:mb-0">{{ __('Existing Backups') }}</h3>
                        
                        <div class="flex space-x-3">
                            <!-- Settings Button -->
                            <button @click="showSettings = true" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Configure Drive
                            </button>

                            <!-- Backup & Download -->
                            <form action="{{ route('admin.backups.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="download" value="1">
                                <button type="submit" onclick="return confirm('Create and download a full system backup? This might take a minute.')" 
                                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring ring-indigo-300 transition">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Backup & Download
                                </button>
                            </form>

                            <!-- Standard Backup -->
                            <form action="{{ route('admin.backups.store') }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Create a new full system backup? This might take a minute.')" 
                                        class="px-4 py-2 bg-marine-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-marine-700 active:bg-marine-900 focus:outline-none focus:border-marine-900 focus:ring ring-marine-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Create Backup') }}
                                </button>
                            </form>
                        </div>

                        <!-- Settings Modal -->
                        <div x-show="showSettings" style="display: none;" 
                             class="fixed inset-0 z-50 overflow-y-auto" 
                             aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="showSettings" @click="showSettings = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                <div x-show="showSettings" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                    <form action="{{ route('admin.backups.settings') }}" method="POST">
                                        @csrf
                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                                Google Drive Configuration
                                            </h3>
                                            <div class="mt-4 space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client ID</label>
                                                    <input type="text" name="google_drive_client_id" value="{{ config('settings.google_drive_client_id') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client Secret</label>
                                                    <input type="password" name="google_drive_client_secret" value="{{ config('settings.google_drive_client_secret') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Refresh Token</label>
                                                    <input type="password" name="google_drive_refresh_token" value="{{ config('settings.google_drive_refresh_token') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Folder ID (Optional)</label>
                                                    <input type="text" name="google_drive_folder_id" value="{{ config('settings.google_drive_folder_id') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-marine-600 text-base font-medium text-white hover:bg-marine-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-marine-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Save Settings
                                            </button>
                                            <button type="button" @click="showSettings = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <h4 class="font-bold text-blue-700 dark:text-blue-200">Cloud Backup Instructions (2nd Location)</h4>
                        <p class="text-sm text-blue-600 dark:text-blue-300 mt-1">
                            To ensure safety, please install <strong>Google Drive for Desktop</strong> (or similar) on this server and configure it to sync the folder:
                        </p>
                        <code class="block mt-2 p-2 bg-gray-100 dark:bg-black rounded text-xs select-all">
                            {{ storage_path('app/backups') }}
                        </code>
                    </div>

                    @if(count($backups) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">File Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Size</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($backups as $backup)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $backup['name'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $backup['size'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $backup['date']->format('Y-m-d H:i:s') }}
                                        <span class="text-xs text-gray-400">({{ $backup['date']->diffForHumans() }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('admin.backups.download', $backup['name']) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">Download</a>
                                            
                                            <form action="{{ route('admin.backups.destroy', $backup['name']) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this backup?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 ml-2">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No backups found. Create one now.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
