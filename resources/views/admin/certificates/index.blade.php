<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                {{ __('Certificate Management') }}
            </h2>
            @php
                $createRoute = route('admin.certificates.create');
            @endphp
            <a href="{{ $createRoute }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Certificate
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: new URLSearchParams(window.location.search).get('tab') || 'drafts' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200 dark:border-gray-700 mb-6">
                        <button @click="tab = 'drafts'" 
                                :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': tab === 'drafts', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-300': tab !== 'drafts' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm w-1/2 text-center">
                            Certificate Control (Drafts/Pending)
                        </button>
                        <button @click="tab = 'issued'" 
                                :class="{ 'border-green-500 text-green-600 dark:text-green-400': tab === 'issued', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-300': tab !== 'issued' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm w-1/2 text-center">
                            Issued Certificates (Completed)
                        </button>
                    </div>

                    <!-- Drafts Tab -->
                    <div x-show="tab === 'drafts'">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ref #</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vessel / Customer</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($drafts as $cert)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-400">
                                                DRAFT
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $cert->type ? $cert->type->name : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $cert->vessel->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $cert->customer->name ?? 'Unknown Customer' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:text-gray-200 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ $cert->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @php
                                                    $editRoute = route('admin.certificates.edit', $cert->id);
                                                    $showRoute = route('admin.certificates.show', $cert->id);
                                                @endphp
                                                <a href="{{ $showRoute }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 mr-3">Preview</a>
                                                <a href="{{ $editRoute }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-500 dark:hover:text-yellow-300">Edit</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No drafts found. <a href="{{ route('admin.certificates.create') }}" class="text-blue-600 dark:text-blue-400 underline">Create one?</a></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Issued Tab -->
                    <div x-show="tab === 'issued'" x-cloak x-data="{ search: '' }">
                        <!-- Search Bar -->
                        <div class="mb-4">
                            <div class="relative max-w-sm">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input type="text" x-model="search" class="block w-full p-2 pl-10 text-sm text-gray-900 dark:text-gray-100 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search certificates..." required>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cert #</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vessel / Customer</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Issued Date</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expiry</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($issued as $cert)
                                        <tr x-show="!search || $el.textContent.toLowerCase().includes(search.toLowerCase())" class="transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-green-700 dark:text-green-400">
                                                {{ $cert->certificate_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $cert->type?->name ?? $cert->certificateTemplate?->name ?? 'Certificate' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $cert->vessel->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $cert->customer->name ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $cert->issue_date ? $cert->issue_date->format('d M Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $cert->expiry_date ? $cert->expiry_date->format('d M Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @php
                                                    $showRoute = route('admin.certificates.show', $cert->id);
                                                @endphp
                                                <div class="flex flex-col items-end gap-1">
                                                    <a href="{{ $showRoute }}" class="text-green-600 dark:text-green-400 hover:text-green-500 dark:hover:text-green-300 font-bold">View / Download</a>
                                                    
                                                    <button 
                                                        x-data=""
                                                        @click="$dispatch('open-modal', 'confirm-renew-modal'); $dispatch('set-renew-action', '{{ route('admin.certificates.renew', $cert->id) }}')"
                                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-bold text-xs uppercase">
                                                        <i class="fas fa-sync mr-1"></i> Renew
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No issued certificates yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Renew Confirmation Modal -->
    <x-modal name="confirm-renew-modal" focusable>
        <form method="POST" x-data="{ action: '' }" @set-renew-action.window="action = $event.detail" :action="action" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Renew Certificate') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Are you sure you want to renew this certificate? A new draft will be created with all details pre-filled.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Renew Certificate') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
