<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('public.title') }} - {{ $booking->booking_code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f6f4ef] text-neutral-950 antialiased">
    @php
        $minDp = $booking->grand_total * ($minDpPercent / 100);
        $headerImage = $booking->room->imageUrl() ?: 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1600&q=85';
    @endphp

    <main class="min-h-screen">
        <section class="relative overflow-hidden bg-neutral-950">
            <img src="{{ $headerImage }}" alt="{{ $booking->room->name }}" class="absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/45 to-black/25"></div>

            <div class="relative mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                <header class="flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('public.home') }}" class="flex items-center gap-3 rounded-full border border-white/15 bg-black/20 py-1.5 pl-1.5 pr-4 text-white shadow-lg backdrop-blur-md transition hover:bg-black/30">
                        <img src="{{ asset('dafano-media/brand/logo-dafano-villa.jpg') }}" alt="{{ __('public.brand') }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-white/25">
                        <span class="text-sm font-bold sm:text-base">{{ __('public.brand') }}</span>
                    </a>
                    <div class="flex items-center gap-2">
                        @include('public.partials.language-switcher')
                        <a href="{{ route('public.home') }}" class="rounded-full border border-white/30 px-4 py-2 text-sm font-medium text-white/90 backdrop-blur hover:bg-white hover:text-neutral-950">{{ __('public.other_booking') }}</a>
                    </div>
                </header>

                <div class="py-20">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-200">{{ __('public.booking_received') }}</p>
                    <h1 class="mt-5 text-4xl font-semibold text-white sm:text-6xl">{{ $booking->booking_code }}</h1>
                    <p class="mt-5 max-w-2xl text-base leading-8 text-white/85">
                        {{ __('public.booking_received_body') }}
                    </p>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                <div class="space-y-6">
                    <section class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold">{{ __('public.reservation_detail') }}</h2>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div class="flex justify-between gap-4"><dt class="text-neutral-500">{{ __('public.name') }}</dt><dd class="font-semibold">{{ $booking->guest_name }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-neutral-500">{{ __('public.whatsapp') }}</dt><dd class="font-semibold">{{ $booking->guest_phone }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-neutral-500">{{ __('public.room') }}</dt><dd class="font-semibold">{{ $booking->room->name }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-neutral-500">{{ __('public.check_in') }}</dt><dd class="font-semibold">{{ $booking->check_in_date->format('d/m/Y') }}, 14:00</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-neutral-500">{{ __('public.check_out') }}</dt><dd class="font-semibold">{{ $booking->check_out_date->format('d/m/Y') }}, 12:00</dd></div>
                        </dl>
                        @if ($booking->room->facilities)
                            <div class="mt-5 border-t border-neutral-200 pt-5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">{{ __('public.facilities') }}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($booking->room->facilities as $facility)
                                        <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-700">{{ $facility }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>

                    <section class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold">{{ __('public.bill_summary') }}</h2>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div class="flex justify-between gap-4"><dt class="text-neutral-500">{{ __('public.grand_total') }}</dt><dd class="font-semibold">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-neutral-500">{{ __('public.min_dp') }}</dt><dd class="font-semibold">Rp {{ number_format($minDp, 0, ',', '.') }}</dd></div>
                            <div class="flex justify-between gap-4 border-t border-neutral-200 pt-4"><dt class="font-semibold text-neutral-950">{{ __('public.balance_due') }}</dt><dd class="text-lg font-bold">Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</dd></div>
                        </dl>
                    </section>
                </div>

                <section class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-800">{{ __('public.bank_transfer') }}</p>
                            <h2 class="mt-2 text-2xl font-semibold">{{ __('public.choose_bank') }}</h2>
                        </div>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">{{ $booking->payment_status }}</span>
                    </div>

                    <div class="mt-6 grid gap-3">
                        @foreach ($bankAccounts as $bankAccount)
                            <div class="rounded-lg border border-neutral-200 bg-[#f6f4ef] p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-neutral-500">{{ $bankAccount->bank_name }}</p>
                                        <p class="mt-1 text-2xl font-bold tracking-wide text-neutral-950">{{ $bankAccount->account_number }}</p>
                                    </div>
                                    <p class="text-right text-sm font-medium text-neutral-600">{{ $bankAccount->account_name }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-5 text-sm leading-6 text-emerald-950">
                        {{ __('public.transfer_instruction') }}
                    </div>

                    <a href="{{ $whatsappUrl }}" class="mt-5 inline-flex w-full justify-center rounded-md bg-emerald-800 px-5 py-3 text-sm font-semibold text-white hover:bg-neutral-950">
                        {{ __('public.confirm_whatsapp') }}
                    </a>
                </section>
            </div>
        </section>
    </main>
</body>
</html>
