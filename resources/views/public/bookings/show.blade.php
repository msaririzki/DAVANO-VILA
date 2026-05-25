<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('public.title') }} - {{ $booking->booking_code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8f7f3] text-neutral-950 antialiased">
    @php
        $minDp = $booking->grand_total * ($minDpPercent / 100);
        $nights = max(1, $booking->check_in_date->diffInDays($booking->check_out_date));
        $headerImage = asset('dafano-media/hero/hero-1.jpg');
    @endphp

    <main class="min-h-screen pb-28 lg:pb-0">
        <section class="relative overflow-hidden bg-neutral-950">
            <img src="{{ $headerImage }}" alt="{{ $booking->room->name }}" class="absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-black/75 via-black/45 to-black/75"></div>

            <div class="relative mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <header class="flex items-center justify-between gap-3">
                    <a href="{{ route('public.home') }}" class="flex min-w-0 items-center gap-3 rounded-full border border-white/15 bg-black/25 py-1.5 pl-1.5 pr-4 text-white shadow-lg backdrop-blur-md transition hover:bg-black/35">
                        <img src="{{ asset('dafano-media/brand/logo-dafano-villa.jpg') }}" alt="{{ __('public.brand') }}" class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 ring-white/25">
                        <span class="truncate text-sm font-bold sm:text-base">{{ __('public.brand') }}</span>
                    </a>
                    <div class="flex shrink-0 items-center gap-2">
                        @include('public.partials.language-switcher')
                        <a href="{{ route('public.home') }}" class="hidden rounded-full border border-white/25 px-4 py-2 text-sm font-bold text-white/90 backdrop-blur transition hover:bg-white hover:text-neutral-950 sm:inline-flex">{{ __('public.other_booking') }}</a>
                    </div>
                </header>

                <div class="grid gap-8 py-12 sm:py-16 lg:grid-cols-[1fr_24rem] lg:items-end lg:py-20">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/35 bg-emerald-500/15 px-4 py-1.5 text-[0.68rem] font-black uppercase tracking-[0.18em] text-emerald-100 backdrop-blur">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                            {{ __('public.booking_received') }}
                        </span>
                        <h1 class="mt-5 max-w-3xl break-all text-3xl font-black leading-tight text-white drop-shadow-lg sm:text-4xl lg:text-5xl">
                            {{ $booking->booking_code }}
                        </h1>
                        <p class="mt-5 max-w-2xl text-sm font-medium leading-7 text-white/90 sm:text-base">
                            {{ __('public.booking_received_body') }}
                        </p>
                    </div>

                    <aside class="rounded-[1.5rem] border border-white/15 bg-white/95 p-4 shadow-[0_24px_70px_-32px_rgba(0,0,0,0.75)] sm:p-5">
                        <p class="text-xs font-black uppercase tracking-[0.14em] text-neutral-500">{{ __('public.min_dp') }}</p>
                        <p class="mt-2 text-3xl font-black tracking-tight text-emerald-800">Rp {{ number_format($minDp, 0, ',', '.') }}</p>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                            <div class="rounded-2xl bg-neutral-100 p-3">
                                <p class="text-[0.68rem] font-bold uppercase tracking-wide text-neutral-500">{{ __('public.grand_total') }}</p>
                                <p class="mt-1 font-black text-neutral-950">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl bg-amber-50 p-3">
                                <p class="text-[0.68rem] font-bold uppercase tracking-wide text-amber-700">{{ __('public.duration') }}</p>
                                <p class="mt-1 font-black text-neutral-950">{{ trans_choice('public.night_count', $nights, ['count' => $nights]) }}</p>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <section class="relative mx-auto -mt-6 max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[0.88fr_1.12fr] lg:items-start">
                <div class="space-y-6">
                    <section class="overflow-hidden rounded-[1.5rem] border border-neutral-200 bg-white shadow-[0_18px_50px_-38px_rgba(15,23,42,0.55)]">
                        <div class="border-b border-neutral-100 px-5 py-4 sm:px-6">
                            <p class="text-xs font-black uppercase tracking-[0.14em] text-emerald-700">{{ __('public.reservation_detail') }}</p>
                        </div>
                        <div class="grid gap-px bg-neutral-100 sm:grid-cols-2">
                            <div class="bg-white p-5">
                                <p class="text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('public.room') }}</p>
                                <p class="mt-1 text-lg font-black text-neutral-950">{{ $booking->room->name }}</p>
                            </div>
                            <div class="bg-white p-5">
                                <p class="text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('public.name') }}</p>
                                <p class="mt-1 text-lg font-black text-neutral-950">{{ $booking->guest_name }}</p>
                            </div>
                            <div class="bg-white p-5">
                                <p class="text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('public.check_in') }}</p>
                                <p class="mt-1 text-base font-black text-neutral-950">{{ $booking->check_in_date->format('d/m/Y') }}</p>
                                <p class="mt-1 text-xs font-semibold text-neutral-500">14:00</p>
                            </div>
                            <div class="bg-white p-5">
                                <p class="text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('public.check_out') }}</p>
                                <p class="mt-1 text-base font-black text-neutral-950">{{ $booking->check_out_date->format('d/m/Y') }}</p>
                                <p class="mt-1 text-xs font-semibold text-neutral-500">12:00</p>
                            </div>
                            <div class="bg-white p-5 sm:col-span-2">
                                <p class="text-xs font-bold uppercase tracking-wide text-neutral-500">{{ __('public.whatsapp') }}</p>
                                <p class="mt-1 text-base font-black text-neutral-950">{{ $booking->guest_phone }}</p>
                            </div>
                        </div>
                    </section>

                    @if ($booking->room->facilities)
                        <section class="rounded-[1.5rem] border border-neutral-200 bg-white p-5 shadow-[0_18px_50px_-38px_rgba(15,23,42,0.55)] sm:p-6">
                            <p class="text-xs font-black uppercase tracking-[0.14em] text-neutral-500">{{ __('public.facilities') }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($booking->room->facilities as $facility)
                                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800 ring-1 ring-emerald-100">{{ $facility }}</span>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="rounded-[1.5rem] border border-neutral-200 bg-white p-5 shadow-[0_18px_50px_-38px_rgba(15,23,42,0.55)] sm:p-6">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-xs font-black uppercase tracking-[0.14em] text-neutral-500">{{ __('public.bill_summary') }}</p>
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black uppercase tracking-wide text-amber-800">{{ $booking->payment_status }}</span>
                        </div>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div class="flex justify-between gap-4">
                                <dt class="text-neutral-500">{{ __('public.grand_total') }}</dt>
                                <dd class="font-black">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between gap-4">
                                <dt class="text-neutral-500">{{ __('public.min_dp') }}</dt>
                                <dd class="font-black text-emerald-800">Rp {{ number_format($minDp, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between gap-4 border-t border-neutral-200 pt-4">
                                <dt class="font-black text-neutral-950">{{ __('public.balance_due') }}</dt>
                                <dd class="text-lg font-black">Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </section>
                </div>

                <section class="rounded-[1.75rem] border border-neutral-200 bg-white p-5 shadow-[0_22px_60px_-42px_rgba(15,23,42,0.65)] sm:p-6 lg:sticky lg:top-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.14em] text-emerald-700">{{ __('public.bank_transfer') }}</p>
                            <h2 class="mt-2 text-2xl font-black tracking-tight text-neutral-950">{{ __('public.choose_bank') }}</h2>
                        </div>
                        <span class="inline-flex w-fit items-center rounded-full bg-neutral-950 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">{{ $booking->booking_code }}</span>
                    </div>

                    <div class="mt-6 grid gap-3">
                        @foreach ($bankAccounts as $bankAccount)
                            <div class="rounded-[1.25rem] border border-neutral-200 bg-[#fbfaf7] p-4 transition hover:border-emerald-200 hover:bg-emerald-50/35">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-xs font-black uppercase tracking-[0.14em] text-emerald-700">{{ $bankAccount->bank_name }}</p>
                                        <p class="mt-2 break-all text-2xl font-black tracking-wide text-neutral-950">{{ $bankAccount->account_number }}</p>
                                    </div>
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-emerald-800 shadow-sm ring-1 ring-neutral-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 18 0"/><path d="M5 21V9l7-4 7 4v12"/><path d="M9 21v-6h6v6"/><path d="M9 10h.01"/><path d="M15 10h.01"/></svg>
                                    </span>
                                </div>
                                <p class="mt-3 text-sm font-bold leading-5 text-neutral-600">{{ $bankAccount->account_name }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 rounded-[1.25rem] border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold leading-6 text-emerald-950">
                        {{ __('public.transfer_instruction') }}
                    </div>

                    <a href="{{ $whatsappUrl }}" class="mt-5 hidden w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-4 text-sm font-black text-white shadow-[0_18px_34px_-22px_rgba(4,120,87,0.85)] transition hover:-translate-y-0.5 hover:bg-emerald-800 lg:inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.08 5.18 2 2 0 0 1 5.06 3h3a2 2 0 0 1 2 1.72c.12.91.33 1.8.62 2.65a2 2 0 0 1-.45 2.11L9 10.7a16 16 0 0 0 4.3 4.3l1.22-1.22a2 2 0 0 1 2.11-.45c.85.29 1.74.5 2.65.62A2 2 0 0 1 22 16.92Z"/></svg>
                        {{ __('public.confirm_whatsapp') }}
                    </a>
                </section>
            </div>
        </section>
    </main>

    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-neutral-200 bg-white/95 p-3 shadow-[0_-18px_40px_-30px_rgba(15,23,42,0.8)] backdrop-blur lg:hidden">
        <a href="{{ $whatsappUrl }}" class="flex min-h-12 items-center justify-center rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-black text-white">
            {{ __('public.confirm_whatsapp') }}
        </a>
    </div>
</body>
</html>
