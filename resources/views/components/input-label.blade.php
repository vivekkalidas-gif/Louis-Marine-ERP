@props(['value'])

<label {{ $attributes->merge(['class' => 'theme-label mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
