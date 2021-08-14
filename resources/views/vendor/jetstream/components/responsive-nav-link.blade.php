@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block pl-3 pr-4 py-2 border-l-4 border-secondary-500 text-base font-medium text-secondary-500 bg-secondary-100 focus:outline-none focus:text-secondary-800 focus:bg-secondary-100 focus:border-secondary-700 transition'
            : 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-secondary-400 hover:text-secondary-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
