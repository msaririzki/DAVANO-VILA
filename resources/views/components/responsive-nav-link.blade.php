@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl bg-emerald-50 px-4 py-3 text-start text-sm font-black text-emerald-800 ring-1 ring-emerald-100 transition duration-150 ease-in-out'
            : 'block w-full rounded-xl px-4 py-3 text-start text-sm font-bold text-neutral-600 transition duration-150 ease-in-out hover:bg-neutral-100 hover:text-neutral-950';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
