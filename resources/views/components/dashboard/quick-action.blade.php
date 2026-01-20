@props(['title', 'icon', 'url', 'color' => 'marine'])

<a href="{{ $url }}" class="flex flex-col items-center justify-center p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-[#0b1c3c] transition-all duration-300 group">
    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-full text-[#0b1c3c] dark:text-gray-200 group-hover:bg-[#0b1c3c] group-hover:text-white transition-colors duration-300">
        {!! $icon !!}
    </div>
    <span class="mt-3 text-sm font-bold text-gray-700 dark:text-gray-200 group-hover:text-[#0b1c3c] dark:group-hover:text-white transition-colors">{{ $title }}</span>
</a>
