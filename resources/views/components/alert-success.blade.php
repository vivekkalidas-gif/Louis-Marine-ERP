@if (session('success'))
    <div {{ $attributes->merge(['class' => 'mb-4 text-sm font-medium text-green-600 bg-green-100 dark:bg-green-900/20 dark:text-green-400 p-4 rounded-lg']) }}>
        {{ session('success') }}
    </div>
@endif
