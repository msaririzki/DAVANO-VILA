@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-full bg-emerald-50 px-4 py-2 text-sm font-black leading-5 text-emerald-800 ring-1 ring-emerald-100 transition duration-150 ease-in-out'
            : 'inline-flex items-center rounded-full px-4 py-2 text-sm font-bold leading-5 text-neutral-600 transition duration-150 ease-in-out hover:bg-neutral-100 hover:text-neutral-950';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
