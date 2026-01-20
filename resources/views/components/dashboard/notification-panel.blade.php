@props(['items'])

<div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700 font-bold text-marine-900 dark:text-marine-100 flex justify-between items-center">
        <span>Notifications</span>
        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ count($items) }}</span>
    </div>
    <div class="max-h-64 overflow-y-auto">
        @forelse($items as $item)
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-800 transition">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{{ $item['title'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['time'] }}</p>
            </div>
        @empty
            <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">No new notifications</div>
        @endforelse
    </div>
    <div class="p-2 bg-gray-50 dark:bg-marine-800 text-center">
        <a href="#" class="text-xs text-marine-600 hover:underline">View All</a>
    </div>
</div>
