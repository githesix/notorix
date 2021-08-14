@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-bleuis-700']) }}>
    {{ $value ?? $slot }}
</label>