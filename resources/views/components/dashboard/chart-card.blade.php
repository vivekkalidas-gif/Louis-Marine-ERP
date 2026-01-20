@props(['title', 'canvasId'])

<div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg p-6">
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
            {{ $title }}
        </h3>
    </div>
    <div class="relative h-64">
        <canvas id="{{ $canvasId }}"></canvas>
    </div>
</div>
