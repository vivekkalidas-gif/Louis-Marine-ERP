@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create New Asset</h1>
        <a href="{{ route('accounts.assets.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 font-medium">Cancel</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('accounts.assets.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asset Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asset Category <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-life="{{ $category->useful_life_years }}" data-method="{{ $category->depreciation_method }}">{{ $category->name }} ({{ $category->code }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Determines GL Accounts.</p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Serial Number</label>
                        <input type="text" name="serial_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                        <input type="text" name="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6"></div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Financial Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purchase Date <span class="text-red-500">*</span></label>
                        <input type="date" name="purchase_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purchase Cost <span class="text-red-500">*</span></label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="purchase_cost" step="0.01" required class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0.00">
                        </div>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Salvage Value</label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="salvage_value" step="0.01" class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0.00">
                        </div>
                    </div>
                </div>
                
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Depreciation Start Date</label>
                        <input type="date" name="capitalization_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Defaults to Purchase Date if empty.</p>
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Method</label>
                         <select name="depreciation_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                             <option value="SLM">Straight Line (SLM)</option>
                             <option value="WDV">Written Down Value (WDV)</option>
                         </select>
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Useful Life (Years) <span class="text-red-500">*</span></label>
                         <input type="number" name="useful_life_years" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('accounts.assets.index') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700">Save Asset</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Simple script to auto-fill useful life from category (if we wanted to implement that)
    const categorySelect = document.querySelector('select[name="category_id"]');
    const lifeInput = document.querySelector('input[name="useful_life_years"]');
    
    categorySelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const life = selected.getAttribute('data-life');
        if (life) lifeInput.value = life;
    });
</script>
@endsection
