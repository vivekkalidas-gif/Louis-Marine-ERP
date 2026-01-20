<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Login History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg pt-6">
                <!-- Navigation Tabs -->
                <div class="px-6 mb-4 border-b dark:border-marine-800">
                    <nav class="-mb-px flex space-x-8">
                        <a href="{{ route('admin.security.activity') }}" class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            System Activity
                        </a>
                        <a href="{{ route('admin.security.logins') }}" class="border-marine-500 text-marine-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                            Login History
                        </a>
                    </nav>
                </div>

                <div class="px-6 pb-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-marine-800">
                        <thead class="bg-gray-50 dark:bg-marine-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">IP Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">User Agent</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-marine-800">
                            @foreach($logins as $login)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $login->login_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                        {{ $login->user->name ?? 'Unknown (Guest)' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($login->status === 'success')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                SUCCESS
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                FAILED
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                        {{ $login->ip_address }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate" title="{{ $login->user_agent }}">
                                        {{ $login->user_agent }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $logins->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
