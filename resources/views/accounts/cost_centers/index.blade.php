@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Cost Centers</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage departments and operational units.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('accounts.cost-centers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm transition-colors">
                Create Cost Center
            </a>
        </div>
    </div>

    <!-- Recursive Tree View -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <ul class="space-y-2">
            @foreach($costCenters as $center)
                @include('accounts.cost_centers.partials.node', ['center' => $center, 'level' => 0])
            @endforeach
        </ul>
    </div>
</div>
@endsection
