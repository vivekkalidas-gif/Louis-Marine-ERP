<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Statistic') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.cms.landing-page.stats.update', $stat) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-6">
                        <div><label class="block text-sm font-medium mb-2">Label *</label><input type="text" name="label" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('label', $stat->label) }}"></div>
                        <div><label class="block text-sm font-medium mb-2">Value *</label><input type="text" name="value" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('value', $stat->value) }}"></div>
                        <div><label class="block text-sm font-medium mb-2">Icon (SVG or class)</label><input type="text" name="icon" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('icon', $stat->icon) }}"></div>
                        <div><label class="block text-sm font-medium mb-2">Sort Order</label><input type="number" name="sort_order" value="{{ old('sort_order', $stat->sort_order) }}" class="w-full rounded-md border-gray-300 shadow-sm"></div>
                    </div>
                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white px-6 py-2 rounded-lg">Update</button>
                        <a href="{{ route('admin.cms.landing-page.stats.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
