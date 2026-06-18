@props([
    'name',
    'value' => 0,
    'placeholder' => '0',
    'required' => false,
])

<div data-money-input class="relative">
    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-black text-slate-500">Rp</span>
    <input
        type="text"
        inputmode="numeric"
        autocomplete="off"
        data-money-display
        value="{{ number_format((float) $value, 0, ',', '.') }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full rounded-xl border-slate-200 py-2.5 pl-11 pr-4 text-sm font-black text-slate-900 focus:border-emerald-500 focus:ring-emerald-500/20']) }}
        @required($required)
    >
    <input type="hidden" data-money-value name="{{ $name }}" value="{{ (int) $value }}">
</div>
