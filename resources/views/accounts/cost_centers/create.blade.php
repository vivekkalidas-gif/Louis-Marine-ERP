@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create Cost Center</h1>
        <a href="{{ route('accounts.cost-centers.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 font-medium">Cancel</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('accounts.cost-centers.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm uppercase">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                     <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="operational">Operational</option>
                        <option value="support">Support</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Parent Cost Center (Optional)</label>
                    <select name="parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">None (Root)</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->code }})</option>
                        @endforeach
                    </select>
                </div>
                
                 <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Manager (Optional)</label>
                    <select name="manager_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select User</option>
                         @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('accounts.cost-centers.index') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
