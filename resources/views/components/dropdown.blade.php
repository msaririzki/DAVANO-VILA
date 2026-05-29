@props(['align' => 'right', 'width' => '48', 'contentClasses' => ''])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<details class="group relative">
    <summary class="list-none cursor-pointer [&::-webkit-details-marker]:hidden">
        {{ $trigger }}
    </summary>

    <div class="absolute z-[90] mt-2 {{ $width }} {{ $alignmentClasses }}">
        <div class="overflow-hidden rounded-2xl border border-white/70 bg-white/95 p-1.5 shadow-2xl shadow-black/10 ring-1 ring-neutral-100 backdrop-blur-xl {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</details>
