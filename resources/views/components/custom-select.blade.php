@props([
    'name',
    'options' => [],
    'optionAttributes' => [],
    'selected' => '',
    'placeholder' => 'Pilih opsi',
    'required' => false,
])

@php
    $selectedValue = (string) $selected;
    $selectedLabel = $selectedValue !== '' && array_key_exists($selectedValue, $options)
        ? $options[$selectedValue]
        : $placeholder;
@endphp

<div class="group relative w-full min-w-0" data-stable-select>
    <select
        name="{{ $name }}"
        data-stable-select-native
        class="pointer-events-none absolute inset-x-0 bottom-0 h-px w-px opacity-0"
        tabindex="-1"
        {{ $required ? 'required' : '' }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $value => $label)
            <option
                value="{{ $value }}"
                @foreach ($optionAttributes[$value] ?? [] as $attribute => $attributeValue)
                    {{ $attribute }}="{{ $attributeValue }}"
                @endforeach
                @selected($selectedValue === (string) $value)
            >{{ $label }}</option>
        @endforeach
    </select>

    <button
        type="button"
        data-stable-select-trigger
        aria-haspopup="listbox"
        aria-expanded="false"
        class="flex min-h-12 w-full min-w-0 items-center justify-between gap-3 rounded-xl border border-neutral-200 bg-white/95 px-4 py-3 text-left text-sm font-bold text-neutral-800 shadow-[0_14px_32px_-26px_rgba(15,23,42,0.75)] transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:bg-white hover:shadow-[0_18px_36px_-26px_rgba(4,120,87,0.55)] focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
    >
        <span data-stable-select-label class="min-w-0 flex-1 break-words leading-snug">{{ $selectedLabel }}</span>
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 transition-all duration-200 group-hover:bg-emerald-100 group-data-[open=true]:bg-emerald-700 group-data-[open=true]:text-white group-data-[open=true]:ring-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200 group-data-[open=true]:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </span>
    </button>

    <div
        data-stable-select-menu
        role="listbox"
        class="absolute z-[90] mt-2 hidden w-full overflow-hidden rounded-2xl border border-white/70 bg-white/95 p-1.5 shadow-2xl shadow-black/10 ring-1 ring-neutral-100 backdrop-blur-xl"
    >
        <div class="max-h-64 overflow-y-auto">
            <button
                type="button"
                data-stable-select-option
                data-value=""
                role="option"
                aria-selected="{{ $selectedValue === '' ? 'true' : 'false' }}"
                class="flex w-full items-center justify-between gap-3 rounded-xl px-3.5 py-2.5 text-left text-sm transition {{ $selectedValue === '' ? 'bg-emerald-50 font-black text-emerald-800' : 'font-semibold text-neutral-600 hover:bg-neutral-50 hover:text-neutral-950' }}"
            >
                <span class="min-w-0 flex-1 break-words leading-snug">{{ $placeholder }}</span>
            </button>

            @foreach ($options as $value => $label)
                @php $isSelected = $selectedValue === (string) $value; @endphp
                <button
                    type="button"
                    data-stable-select-option
                    data-value="{{ $value }}"
                    role="option"
                    aria-selected="{{ $isSelected ? 'true' : 'false' }}"
                    class="flex w-full items-center justify-between gap-3 rounded-xl px-3.5 py-2.5 text-left text-sm transition {{ $isSelected ? 'bg-emerald-50 font-black text-emerald-800' : 'font-semibold text-neutral-700 hover:bg-emerald-50 hover:text-emerald-900' }}"
                >
                    <span class="min-w-0 flex-1 break-words leading-snug">{{ $label }}</span>
                    <svg data-stable-select-check xmlns="http://www.w3.org/2000/svg" class="{{ $isSelected ? '' : 'hidden' }} h-4 w-4 shrink-0 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endforeach
        </div>
    </div>
</div>
