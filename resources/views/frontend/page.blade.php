<x-layouts.frontend>
    <div class="py-12 bg-white dark:bg-marine-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-10">
                <h2 class="text-base text-marine-600 font-semibold tracking-wide uppercase">Information</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    {{ $page->title }}
                </p>
            </div>
            <div class="prose dark:prose-invert mx-auto">
                {!! nl2br(e($page->content)) !!}
            </div>
        </div>
    </div>
</x-layouts.frontend>
