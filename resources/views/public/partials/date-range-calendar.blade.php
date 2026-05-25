@php
    $calendarId = $calendarId ?? 'date-range-'.\Illuminate\Support\Str::uuid();
    $checkInName = $checkInName ?? 'check_in_date';
    $checkOutName = $checkOutName ?? 'check_out_date';
    $checkInValue = $checkInValue ?? '';
    $checkOutValue = $checkOutValue ?? '';
    $minDate = $minDate ?? now()->toDateString();
    $calendarClass = $calendarClass ?? '';
    $collapsible = $collapsible ?? false;
    $panelMode = $panelMode ?? ($collapsible ? 'modal' : 'inline');
    $isModalCalendar = $collapsible && $panelMode === 'modal';
    $panelClass = match ($panelMode) {
        'modal' => 'hidden fixed left-1/2 top-1/2 z-50 w-[min(92vw,24rem)] -translate-x-1/2 -translate-y-1/2 rounded-[1.25rem] border border-white/80 bg-white p-3 shadow-[0_28px_80px_-24px_rgba(15,23,42,0.5)]',
        'inline-collapse' => 'hidden mt-2.5 rounded-[1.15rem] border border-neutral-100 bg-white p-2.5 shadow-[0_18px_42px_-28px_rgba(15,23,42,0.45)]',
        default => 'mt-2.5',
    };
@endphp

<div
    id="{{ $calendarId }}"
    class="date-range-calendar {{ $calendarClass }}"
    data-date-range-calendar
    data-min-date="{{ $minDate }}"
    data-check-in-label="{{ __('public.check_in') }}"
    data-check-out-label="{{ __('public.check_out') }}"
    data-empty-label="{{ __('public.select_dates') }}"
    data-today-label="{{ __('public.today') }}"
    data-selected-label="{{ __('public.selected') }}"
    data-night-singular="{{ __('public.night_unit_singular') }}"
    data-night-plural="{{ __('public.night_unit_plural') }}"
>
    <input data-check-in-input type="hidden" name="{{ $checkInName }}" value="{{ $checkInValue }}">
    <input data-check-out-input type="hidden" name="{{ $checkOutName }}" value="{{ $checkOutValue }}">

    <div class="relative w-full rounded-full transition-all focus-within:bg-neutral-50/80">
        <button type="button" data-calendar-toggle class="grid w-full grid-cols-2 text-left group/calBtn h-full rounded-full overflow-hidden">
            <div class="px-6 py-2.5 sm:py-3 transition-colors hover:bg-neutral-100/80">
                <p class="text-[0.65rem] font-bold uppercase tracking-[0.1em] text-neutral-500">{{ __('public.check_in') }}</p>
                <p data-check-in-display class="mt-0.5 min-h-5 truncate text-[0.9rem] font-bold text-neutral-900">{{ __('public.select_dates') }}</p>
            </div>
            <div class="px-6 py-2.5 sm:py-3 transition-colors hover:bg-neutral-100/80 relative before:absolute before:-left-px before:top-1/2 before:h-8 before:-translate-y-1/2 before:w-[1px] before:bg-neutral-200">
                <p class="text-[0.65rem] font-bold uppercase tracking-[0.1em] text-neutral-500">{{ __('public.check_out') }}</p>
                <p data-check-out-display class="mt-0.5 min-h-5 truncate text-[0.9rem] font-bold text-neutral-900">{{ __('public.select_dates') }}</p>
            </div>
        </button>

        @if ($isModalCalendar)
            <button type="button" data-calendar-backdrop class="fixed inset-0 z-40 hidden cursor-default bg-neutral-950/35 backdrop-blur-[2px]" aria-label="Close date picker"></button>
        @endif

        <div data-calendar-panel class="{{ $panelClass }}">
            @if ($isModalCalendar)
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-black text-neutral-950">{{ __('public.select_dates') }}</p>
                        <p class="text-[0.7rem] font-semibold text-neutral-500">{{ __('public.date_range_hint') }}</p>
                    </div>
                    <button type="button" data-calendar-close class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-neutral-100 text-neutral-600 hover:bg-neutral-200" aria-label="Close date picker">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            @endif

            <div class="mb-2 flex items-center justify-between gap-2">
                <button type="button" data-calendar-prev class="calendar-nav-btn" aria-label="{{ __('public.prev_month') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <div class="text-center">
                    <p data-calendar-month class="text-sm font-black text-neutral-950"></p>
                    <p data-calendar-nights class="text-[0.68rem] font-semibold text-neutral-500">{{ __('public.date_range_hint') }}</p>
                </div>
                <button type="button" data-calendar-next class="calendar-nav-btn" aria-label="{{ __('public.next_month') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>

            <div data-calendar-weekdays class="grid grid-cols-7 gap-0.5 text-center text-[0.58rem] font-bold uppercase tracking-[0.06em] text-neutral-400"></div>
            <div data-calendar-days class="mt-1.5 grid grid-cols-7 gap-0.5"></div>

            <div class="mt-2.5 flex flex-wrap items-center gap-2.5 text-[0.66rem] font-semibold text-neutral-500">
                <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full border-2 border-emerald-700 bg-white"></span>{{ __('public.today') }}</span>
                <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-neutral-900"></span>{{ __('public.selected') }}</span>
            </div>
        </div>

        @error($checkInName)
            <p class="mt-3 text-sm font-semibold text-red-600">{{ $message }}</p>
        @enderror
        @error($checkOutName)
            <p class="mt-3 text-sm font-semibold text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
