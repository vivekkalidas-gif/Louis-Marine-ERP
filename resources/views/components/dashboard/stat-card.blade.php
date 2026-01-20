@props(['title', 'value', 'icon' => null, 'color' => 'marine', 'trend' => null])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 rounded-xl p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $title }}
            </div>
            <div class="mt-2 text-3xl font-extrabold text-[#0b1c3c] dark:text-white">
                {{ $value }}
            </div>
            @if($trend)
                <div class="mt-2 flex items-center text-sm {{ $trend > 0 ? 'text-green-600 bg-green-50 px-2 py-0.5 rounded-full w-fit' : 'text-red-600 bg-red-50 px-2 py-0.5 rounded-full w-fit' }}">
                    <span class="font-bold mr-1">{{ $trend > 0 ? '↑' : '↓' }}</span>
                    {{ abs($trend) }}% from last month
                </div>
            @endif
        </div>
        @if($icon)
            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-full text-[#0b1c3c] dark:text-gray-200">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
