@if (session('error'))
    <div {{ $attributes->merge(['class' => 'mb-4 text-sm font-medium text-red-600 bg-red-100 dark:bg-red-900/20 dark:text-red-400 p-4 rounded-lg']) }}>
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-4 text-sm font-medium text-red-600 bg-red-100 dark:bg-red-900/20 dark:text-red-400 p-4 rounded-lg']) }}>
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
