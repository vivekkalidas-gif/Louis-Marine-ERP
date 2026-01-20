@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Request Tool</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Browse available tools by category.</p>
        </div>
        <a href="{{ route('engineer.tools.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:border-gray-600">
            Cancel
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-8 p-4">
        <form action="{{ route('engineer.tools.create') }}" method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <input type="text" name="search" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Search tool by name, brand..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Search
            </button>
        </form>
    </div>

    @forelse($groupedTools as $type => $tools)
        <div class="mb-10">
            <div class="flex items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white border-l-4 border-blue-500 pl-3">{{ $type }}</h2>
                <span class="ml-2 px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                    {{ $tools->count() }} Available
                </span>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/4">Tool Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/4">Brand / Model</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">Notes for Request</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($tools as $tool)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tool->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $tool->code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $tool->brand }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $tool->model }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <form id="request-form-{{ $tool->id }}" action="{{ route('engineer.tools.store') }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <input type="hidden" name="equipment_item_id" value="{{ $tool->id }}">
                                        <input type="text" name="notes" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs py-1" placeholder="Optional notes...">
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button form="request-form-{{ $tool->id }}" type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Request
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @empty
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No tools found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or check back later.</p>
        </div>
    @endforelse
</div>
@endsection
