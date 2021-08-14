@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-secondary-300 text-sm font-medium leading-5 text-secondary-300 focus:outline-none focus:border-acier transition'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-secondary-400 hover:text-secondary-200 hover:border-secondary-200 focus:outline-none focus:text-secondary focus:border-secondary-300 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
