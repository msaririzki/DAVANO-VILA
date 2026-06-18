@php
    $calendarId = $calendarId ?? 'date-picker-'.\Illuminate\Support\Str::uuid();
    $name = $name ?? 'date';
    $value = $value ?? '';
    $minDate = $minDate ?? now()->toDateString();
    $label = $label ?? __('public.date');
    $hint = $hint ?? 'Silakan pilih tanggal';
    $icon = $icon ?? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>';
    $isEndNode = $isEndNode ?? false;
    
    // Config panel mode inline or modal
    $collapsible = $collapsible ?? false;
    $panelMode = $panelMode ?? ($collapsible ? 'modal' : 'inline');
    $isModalCalendar = $collapsible && $panelMode === 'modal';
    
    $panelClass = match ($panelMode) {
        'modal' => 'hidden fixed left-1/2 top-1/2 z-[100] w-[calc(100vw-1.25rem)] max-w-[24rem] -translate-x-1/2 -translate-y-1/2 rounded-[1.25rem] border border-white/80 bg-white p-3 shadow-[0_24px_64px_-32px_rgba(15,23,42,0.36)] animate-modal-pop',
        'inline-collapse' => 'hidden absolute top-full mt-2 z-[60] w-[min(92vw,24rem)] rounded-[1.15rem] border border-neutral-100 bg-white p-2.5 shadow-[0_18px_42px_-28px_rgba(15,23,42,0.45)] ' . ($isEndNode ? 'right-0' : 'left-0'),
        default => 'mt-2.5',
    };
@endphp

<div
    id="{{ $calendarId }}"
    class="date-picker-calendar relative h-full w-full {{ $isEndNode ? 'before:absolute before:-left-px before:top-1/2 before:h-8 before:-translate-y-1/2 before:w-[1px] before:bg-neutral-200' : '' }}"
    data-date-picker
    data-picker-type="{{ $name }}"
    data-min-date="{{ $minDate }}"
    data-empty-label="{{ __('public.select_dates') }}"
    data-today-label="{{ __('public.today') }}"
>
    <input data-picker-input type="hidden" name="{{ $name }}" value="{{ $value }}">

    <button type="button" data-calendar-toggle class="w-full text-left group/calBtn h-full px-4 sm:px-5 py-2.5 sm:py-3 transition-colors hover:bg-emerald-50/50 flex flex-col justify-center {{ $isEndNode ? 'rounded-r-xl' : 'rounded-l-xl' }}">
        <div class="flex items-center gap-1.5 mb-1">
            {!! $icon !!}
            <p class="text-[0.65rem] font-bold uppercase tracking-[0.1em] text-neutral-500">{{ $label }}</p>
        </div>
        <p data-picker-display class="min-h-5 truncate text-[0.8rem] sm:text-[0.9rem] font-bold text-neutral-900">{{ __('public.select_dates') }}</p>
    </button>

    @if ($isModalCalendar)
        <button type="button" data-calendar-backdrop class="fixed inset-0 z-[90] hidden cursor-default bg-transparent" aria-label="{{ __('public.close_date_picker') }}"></button>
    @endif

    <div data-calendar-panel class="{{ $panelClass }}">
        @if ($isModalCalendar)
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-black text-neutral-950">{{ $label }}</p>
                    <p class="text-[0.7rem] font-semibold text-neutral-500">{{ $hint }}</p>
                </div>
                <button type="button" data-calendar-close class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-neutral-100 text-neutral-600 hover:bg-neutral-200" aria-label="{{ __('public.close_date_picker') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
        @else
            <div class="mb-3 pb-2 border-b border-neutral-100">
                <p class="text-xs font-bold uppercase tracking-wider text-neutral-800">{{ $label }}</p>
                <p class="text-[0.65rem] font-medium text-emerald-600 mt-0.5">{{ $hint }}</p>
            </div>
        @endif

        <div class="mb-2 flex items-center justify-between gap-2">
            <button type="button" data-calendar-prev class="calendar-nav-btn" aria-label="{{ __('public.prev_month') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <div class="text-center">
                <p data-calendar-month class="text-sm font-black text-neutral-950"></p>
            </div>
            <button type="button" data-calendar-next class="calendar-nav-btn" aria-label="{{ __('public.next_month') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>

        <div data-calendar-weekdays translate="no" class="notranslate grid grid-cols-7 gap-0.5 text-center text-[0.52rem] font-bold uppercase tracking-[0.02em] text-neutral-400 sm:text-[0.58rem] sm:tracking-[0.06em]"></div>
        <div data-calendar-days class="mt-1.5 grid grid-cols-7 gap-0.5"></div>

        <div class="mt-3 flex flex-wrap items-center gap-3.5 text-[0.66rem] font-bold text-neutral-500">
            <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-700 shadow-sm"></span>Terpilih</span>
        </div>
    </div>
</div>
