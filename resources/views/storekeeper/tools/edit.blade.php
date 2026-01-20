@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Tool: <span class="text-blue-600 dark:text-blue-400 number-font">{{ $tool->name }}</span></h1>
            <a href="{{ route('storekeeper.tools.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:border-gray-600">
                Cancel
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <div class="p-6">
                <form action="{{ route('storekeeper.tools.update', $tool->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Category -->
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tool Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $tool->name) }}" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="e.g. Spanner, Drill">
                        </div>

                        <!-- Unit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit</label>
                            <select name="unit" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="Nos" {{ $tool->unit == 'Nos' ? 'selected' : '' }}>Nos (Number)</option>
                                <option value="Sets" {{ $tool->unit == 'Sets' ? 'selected' : '' }}>Sets</option>
                                <option value="Box" {{ $tool->unit == 'Box' ? 'selected' : '' }}>Box</option>
                            </select>
                            @error('unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Brand -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand</label>
                            <input type="text" name="brand" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('brand') ?? $tool->brand }}">
                        </div>

                        <!-- Model -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model</label>
                            <input type="text" name="model" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('model') ?? $tool->model }}">
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description / Spec</label>
                            <textarea name="description" rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description') ?? $tool->description }}</textarea>
                        </div>
                        
                        <!-- Custom Code -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Custom Code</label>
                            <input type="text" name="code" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="{{ old('code') ?? $tool->code }}">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Update Tool
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
