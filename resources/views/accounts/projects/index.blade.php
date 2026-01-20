@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Projects</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track project profitability and status.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('accounts.projects.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm transition-colors">
                New Project
            </a>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $project)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100"><a href="{{ route('accounts.projects.show', $project) }}" class="hover:underline">{{ $project->name }}</a></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $project->code }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $project->status == 'completed' ? 'bg-green-100 text-green-800' : 
                           ($project->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 dark:text-gray-200') }}">
                        {{ str_replace('_', ' ', ucfirst($project->status)) }}
                    </span>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Profitability</span>
                        <span class="font-bold {{ $project->net_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($project->net_profit, 2) }}
                        </span>
                    </div>
                    
                     <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Manager</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $project->manager ? $project->manager->name : '-' }}</span>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                    <a href="{{ route('accounts.projects.show', $project) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">View Dashboard &rarr;</a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                 <p class="text-gray-500 dark:text-gray-400">No projects found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
