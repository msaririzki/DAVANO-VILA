<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $businessProfile['business_name'] }} - {{ $booking->booking_code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes bookingHeroFade {
            0%, 18% { opacity: 1; }
            24%, 100% { opacity: 0; }
        }

        .booking-hero-slide {
            opacity: 0;
            animation: bookingHeroFade calc(var(--booking-slide-count) * 5s) infinite;
            animation-delay: calc(var(--booking-slide-index) * 5s);
        }

        @media (prefers-reduced-motion: reduce) {
            .booking-hero-slide {
                animation: none;
                opacity: 0;
            }

            .booking-hero-slide[data-slide-index="0"] {
                opacity: 1;
            }
        }
    </style>
</head>
<body class="bg-[#f7f4ee] text-neutral-900 antialiased font-sans selection:bg-emerald-500 selection:text-white">
    @php
        $minDp = $booking->grand_total * ($minDpPercent / 100);
        $remainingAfterDp = max($booking->grand_total - $minDp, 0);
        $nights = max(1, $booking->check_in_date->diffInDays($booking->check_out_date));
        
        $room = $booking->room;
        $image = $room->imageUrl() ?: asset('dafano-media/hero/hero-1.jpg');
        $roomSpecificImages = [];
        if (stripos($room->name, 'commercial') !== false) {
            $roomSpecificImages = [
                asset('dafano-media/gallery/kamar/commercial/foto1.jpg'),
                asset('dafano-media/gallery/kamar/commercial/foto2.jpg'),
                asset('dafano-media/gallery/kamar/commercial/foto3.jpg'),
                asset('dafano-media/gallery/kamar/commercial/foto4.jpg'),
                asset('dafano-media/gallery/kamar/commercial/foto5.png'),
            ];
        } elseif (stripos($room->name, 'superior') !== false) {
            $roomSpecificImages = [
                asset('dafano-media/gallery/kamar/superior/foto1.jpg'),
                asset('dafano-media/gallery/kamar/superior/foto2.jpg'),
                asset('dafano-media/gallery/kamar/superior/foto3.jpg'),
                asset('dafano-media/gallery/kamar/superior/foto4.jpeg'),
            ];
        } else {
            $roomSpecificImages = [ $image ];
        }
        $logoPath = asset('dafano-media/brand/dafano-logo.png');
        $signaturePath = asset('dafano-media/brand/signature-dafano.png');
        $formatCurrency = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
        $stayUnitSummary = stripos($room->name, 'commercial') !== false || stripos($room->name, 'kamar') !== false
            ? trans_choice('public.stay_unit_room_count', $booking->unit_count, ['count' => $booking->unit_count])
            : trans_choice('public.stay_unit_villa_count', $booking->unit_count, ['count' => $booking->unit_count]);
        $legacyPendingBooking = $booking->payment_status === \App\Models\Booking::PAYMENT_PENDING && ! $booking->hold_expires_at;
        $canTransfer = $booking->hasActivePaymentWindow()
            || $legacyPendingBooking
            || ($booking->payment_status === \App\Models\Booking::PAYMENT_DP && (float) $booking->balance_due > 0);
    @endphp

    <main class="relative min-h-screen pb-28 lg:pb-12">
        <!-- Top Navigation (Overlay) -->
        <nav class="absolute left-0 right-0 top-0 z-50 px-4 pt-5 sm:px-6 lg:px-8">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-3">
                <!-- Logo -->
                <div class="flex min-w-0 items-center gap-2 rounded-full border border-white/15 bg-black/45 py-1 pl-1 pr-2.5 shadow-lg backdrop-blur-md sm:gap-3 sm:py-1.5 sm:pl-1.5 sm:pr-4">
                    <img src="{{ $logoPath }}" alt="{{ __('public.brand') }}" class="h-12 w-11 rounded-2xl border border-white/20 bg-white/95 p-1 object-contain">
                    <span class="hidden truncate text-sm font-bold tracking-wide text-white sm:inline">{{ __('public.brand') }}</span>
                </div>
                
                <!-- Right Actions -->
                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    @include('public.partials.language-switcher')
                    <a href="{{ route('public.home') }}" class="flex items-center gap-2 rounded-full border border-white/15 bg-black/45 px-3 py-2 shadow-lg backdrop-blur-md transition hover:bg-black/55 sm:px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        <span class="hidden text-xs font-bold text-white sm:inline">{{ __('public.other_booking') }}</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-emerald-950 pb-14 pt-24 sm:pb-24 sm:pt-36 lg:pb-32 lg:pt-40">
            
            @foreach($roomSpecificImages as $idx => $img)
                <img src="{{ $img }}" alt="{{ $booking->room->name }}" 
                     class="absolute inset-0 h-full w-full object-cover {{ count($roomSpecificImages) > 1 ? 'booking-hero-slide' : 'opacity-100' }}"
                     data-slide-index="{{ $idx }}"
                     style="--booking-slide-index: {{ $idx }}; --booking-slide-count: {{ max(1, count($roomSpecificImages)) }};">
            @endforeach
            
            <!-- Gradient ringan di bagian bawah saja agar teks putih tetap terbaca -->
            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(10,10,10,0.32)_0%,rgba(10,10,10,0.58)_55%,rgba(10,10,10,0.88)_100%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(184,137,67,0.18),transparent_28rem)]"></div>
            
            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-black/45 px-3 py-1 text-[0.65rem] font-bold uppercase tracking-widest text-white shadow-lg backdrop-blur-md">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('public.booking_received') }}
                    </div>
                    <h1 class="max-w-full break-words text-3xl font-black tracking-tight text-white drop-shadow-lg sm:text-5xl lg:text-[4rem]">{{ $booking->booking_code }}</h1>
                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-neutral-100 drop-shadow-md sm:mt-4">
                        {{ __('public.booking_received_body') }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2 sm:mt-6">
                        <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold text-white backdrop-blur-md">{{ $booking->room->name }}</span>
                        <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold text-white backdrop-blur-md">{{ $booking->check_in_date->translatedFormat('d M') }} - {{ $booking->check_out_date->translatedFormat('d M') }}</span>
                        <span class="inline-flex items-center rounded-full border border-emerald-300/25 bg-emerald-500/20 px-3 py-1.5 text-xs font-bold text-emerald-50 backdrop-blur-md">{{ trans_choice('public.night_count', $nights, ['count' => $nights]) }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content Layout -->
        <section class="relative z-20 mx-auto -mt-9 max-w-7xl px-3 pb-12 sm:-mt-16 sm:px-6 lg:px-8">
            <div class="grid items-start gap-6 lg:grid-cols-[1.28fr_0.92fr] xl:gap-8">
                
                <!-- Left Column -->
                <div class="space-y-5 sm:space-y-6">
                    
                    <!-- Detail Reservasi -->
                    <section class="overflow-hidden rounded-[1.35rem] border border-white/80 bg-white/95 p-3 shadow-[0_24px_70px_-42px_rgba(15,23,42,0.45)] sm:rounded-[2rem] sm:p-6">
                        <div class="mb-4 rounded-2xl border border-[#eadfce] bg-[#fbf7f0] p-3 sm:p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-center gap-3">
                                    <img src="{{ $logoPath }}" alt="{{ __('public.brand') }}" class="h-16 w-14 shrink-0 rounded-2xl border border-[#d9c19b] bg-white p-1.5 object-contain shadow-sm sm:h-20 sm:w-16">
                                    <div class="min-w-0">
                                        <p class="text-[0.65rem] font-black uppercase tracking-[0.18em] text-[#9a6a2f]">Invoice</p>
                                        <h2 class="mt-0.5 text-lg font-black leading-tight text-emerald-950 sm:text-xl">{{ __('public.brand') }}</h2>
                                        <p class="text-xs font-semibold text-neutral-600">Sembalun, Lombok Timur</p>
                                    </div>
                                </div>
                                <div class="rounded-2xl bg-white/80 p-2.5 ring-1 ring-[#eadfce] sm:p-3 sm:text-right">
                                    <div class="flex min-w-0 items-center gap-2 sm:justify-end">
                                        <p class="shrink-0 text-[0.62rem] font-black uppercase tracking-[0.14em] text-neutral-600">{{ __('public.reservation') }}</p>
                                        <p class="min-w-0 truncate font-mono text-xs font-black text-neutral-950 sm:text-sm">{{ $booking->booking_code }}</p>
                                    </div>
                                    <p class="mt-1 text-xs font-semibold text-neutral-600 sm:mt-1.5">{{ now()->translatedFormat('d M Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="mt-3 h-1 rounded-full bg-gradient-to-r from-[#b88943] via-emerald-700 to-[#b88943] sm:mt-4"></div>
                        </div>

                        <div class="mb-3 rounded-2xl border border-emerald-100 bg-emerald-50/70 p-3 sm:mb-5 sm:p-4">
                            <span class="inline-flex w-fit items-center gap-2 rounded-full bg-white px-3 py-1 text-[0.65rem] font-black uppercase tracking-[0.12em] text-emerald-700 shadow-sm ring-1 ring-emerald-100">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                {{ __('public.booking_received') }}
                            </span>
                            <p class="mt-2 text-sm font-semibold leading-6 text-emerald-950">
                                {{ $booking->room->name }} &middot; {{ $booking->check_in_date->translatedFormat('d M') }} - {{ $booking->check_out_date->translatedFormat('d M') }} &middot; {{ trans_choice('public.night_count', $nights, ['count' => $nights]) }}
                            </p>
                        </div>
                        
                        <div class="grid gap-2.5 sm:grid-cols-2 sm:gap-4">
                            <div class="rounded-xl border border-neutral-100 bg-neutral-50 p-2.5 sm:rounded-2xl sm:p-4">
                                <p class="text-[0.62rem] font-black uppercase tracking-widest text-neutral-600">{{ __('public.room') }}</p>
                                <p class="mt-0.5 text-sm font-bold text-neutral-900 sm:text-base">
                                    {{ $booking->room->name }}
                                    <span class="font-black text-emerald-700">{{ $stayUnitSummary }}</span>
                                </p>
                                @if ($booking->units->isNotEmpty())
                                    <p class="mt-1 truncate text-xs font-bold text-neutral-600">{{ $booking->units->pluck('name')->implode(', ') }}</p>
                                @endif
                            </div>
                            <div class="rounded-xl border border-neutral-100 bg-neutral-50 p-2.5 sm:rounded-2xl sm:p-4">
                                <p class="text-[0.62rem] font-black uppercase tracking-widest text-neutral-600">{{ __('public.guest_name') }}</p>
                                <p class="mt-0.5 text-sm font-bold text-neutral-900 sm:text-base">{{ $booking->guest_name }}</p>
                            </div>
                            <div class="rounded-xl border border-neutral-100 bg-neutral-50 p-2 sm:col-span-2 sm:rounded-2xl sm:p-3">
                                <div class="grid grid-cols-[0.72fr_0.72fr_minmax(0,1.56fr)] overflow-hidden rounded-xl bg-white ring-1 ring-neutral-100">
                                    <div class="min-w-0 border-r border-neutral-100 px-2 py-2">
                                        <p class="text-[0.56rem] font-black uppercase tracking-widest text-neutral-600">{{ __('public.adults') }}</p>
                                        <p class="mt-0.5 text-sm font-black text-neutral-950">{{ $booking->adult_count }}</p>
                                    </div>
                                    <div class="min-w-0 border-r border-neutral-100 px-2 py-2">
                                        <p class="text-[0.56rem] font-black uppercase tracking-widest text-neutral-600">{{ __('public.children') }}</p>
                                        <p class="mt-0.5 text-sm font-black text-neutral-950">{{ $booking->child_count }}</p>
                                    </div>
                                    <div class="min-w-0 border-r border-neutral-100 px-2 py-2">
                                        <p class="text-[0.56rem] font-black uppercase tracking-widest text-neutral-600">{{ __('public.whatsapp') }}</p>
                                        <p class="mt-0.5 truncate text-sm font-black text-neutral-950">{{ $booking->guest_phone }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-hidden rounded-xl border border-neutral-100 bg-neutral-50 p-2 sm:col-span-2 sm:rounded-2xl sm:p-3">
                                <div class="grid grid-cols-2 overflow-hidden rounded-xl ring-1 ring-neutral-100">
                                    <div class="border-r border-emerald-100 bg-emerald-50 px-2.5 py-2 sm:px-4 sm:py-3">
                                        <p class="text-[0.6rem] font-black uppercase tracking-widest text-emerald-700 sm:text-[0.62rem]">{{ __('public.check_in') }}</p>
                                        <div class="mt-0.5 flex items-baseline gap-1.5">
                                            <p class="text-sm font-black text-emerald-950 sm:text-lg">{{ $booking->check_in_date->translatedFormat('d M') }}</p>
                                            <p class="text-[0.68rem] font-bold text-emerald-700 sm:text-xs">{{ $businessProfile['check_in_time'] }}</p>
                                        </div>
                                    </div>
                                    <div class="bg-amber-50 px-2.5 py-2 text-right sm:px-4 sm:py-3">
                                        <p class="text-[0.6rem] font-black uppercase tracking-widest text-amber-700 sm:text-[0.62rem]">{{ __('public.check_out') }}</p>
                                        <div class="mt-0.5 flex items-baseline justify-end gap-1.5">
                                            <p class="text-sm font-black text-amber-950 sm:text-lg">{{ $booking->check_out_date->translatedFormat('d M') }}</p>
                                            <p class="text-[0.68rem] font-bold text-amber-700 sm:text-xs">{{ $businessProfile['check_out_time'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($booking->room->facilities)
                                <div class="rounded-xl border border-neutral-100 bg-neutral-50 p-3 sm:col-span-2 sm:rounded-2xl sm:p-4">
                                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-neutral-600">{{ __('public.facilities') }}</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($booking->room->facilities as $facility)
                                            <span class="rounded-full border border-neutral-200 bg-white px-3 py-1.5 text-xs font-bold text-neutral-700">{{ $facility }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($booking->guest_request)
                                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3 sm:col-span-2 sm:rounded-2xl sm:p-4">
                                    <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-700">{{ __('public.guest_request_label') }}</p>
                                    <p class="mt-2 text-sm font-semibold leading-6 text-emerald-950">{{ $booking->guest_request }}</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    @if ($booking->hasActivePaymentWindow())
                        <section id="booking-hold-alert" data-hold-expires="{{ ($booking->payment_deadline_at ?? $booking->hold_expires_at)->toIso8601String() }}" class="rounded-[1.35rem] border border-amber-200 bg-amber-50 p-4 shadow-sm sm:rounded-[2rem] sm:p-5">
                            <p class="text-xs font-black uppercase tracking-widest text-amber-700">Reservasi sedang ditahan</p>
                            <p class="mt-1 text-lg font-black text-amber-950">Selesaikan transfer dalam <span data-hold-countdown>--:--</span></p>
                            <p class="mt-1 text-sm font-semibold text-amber-800">Batas pembayaran untuk tamu adalah 30 menit. Setelah waktu habis, jangan melakukan transfer.</p>
                        </section>
                    @elseif ($booking->hasExpiredPaymentWindow())
                        <section class="rounded-[1.35rem] border border-rose-300 bg-rose-50 p-5 shadow-sm sm:rounded-[2rem]">
                            <p class="text-xs font-black uppercase tracking-widest text-rose-700">Waktu pembayaran berakhir</p>
                            <p class="mt-1 text-xl font-black text-rose-950">Batas 30 menit sudah habis. Jangan melakukan transfer.</p>
                            <p class="mt-2 text-sm font-semibold leading-6 text-rose-800">Silakan hubungi {{ $businessProfile['business_name'] }} atau buat reservasi baru. Jika sudah telanjur transfer, staf akan memeriksa mutasi dan menentukan tindak lanjut.</p>
                        </section>
                    @endif

                    <!-- Transfer Bank -->
                    @if ($canTransfer)
                    <section class="relative overflow-hidden rounded-[1.35rem] border border-emerald-100 bg-white/95 p-4 shadow-[0_24px_70px_-42px_rgba(15,23,42,0.45)] sm:rounded-[2rem] sm:p-6">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-emerald-500"></div>
                        <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700 mb-1">{{ __('public.bank_transfer') }}</h2>
                                <p class="text-xl font-black text-neutral-900">{{ __('public.choose_bank') }}</p>
                                <p class="text-xs font-semibold text-neutral-600 mt-1">{{ __('public.tap_bank_to_copy') }}</p>
                            </div>
                            <a href="{{ $whatsappUrl }}" class="hidden w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-[#25D366] px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-[#25D366]/30 transition hover:-translate-y-0.5 hover:shadow-[#25D366]/40 active:scale-95 sm:w-auto lg:inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                {{ __('public.confirm_whatsapp') }}
                            </a>
                        </div>
                        
                        <div class="mt-4 overflow-hidden rounded-2xl border border-emerald-100 bg-emerald-50/40">
                            @foreach ($bankAccounts as $bankAccount)
                                <div x-data="{ copied: false }" class="grid grid-cols-[minmax(0,1fr)_auto_auto] items-center gap-2.5 border-b border-emerald-100/80 bg-white/80 px-3 py-2.5 last:border-b-0 sm:gap-3 sm:px-4">
                                    <div class="min-w-0">
                                        <p class="truncate text-[0.72rem] font-black uppercase tracking-[0.12em] text-emerald-800">
                                            {{ $bankAccount->bank_name }}
                                            <span class="font-black tracking-normal text-neutral-700">- {{ $bankAccount->account_name }}</span>
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-emerald-950 px-3 py-1.5 text-right shadow-sm ring-1 ring-emerald-900/10">
                                        <p class="whitespace-nowrap font-mono text-sm font-black tracking-wide text-white sm:text-base">{{ $bankAccount->account_number }}</p>
                                    </div>

                                    <div class="relative">
                                        <button
                                            type="button"
                                            @click="navigator.clipboard.writeText('{{ $bankAccount->account_number }}'); copied = true; setTimeout(() => copied = false, 1600)"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-100 text-emerald-800 transition hover:-translate-y-0.5 hover:border-emerald-300 hover:bg-emerald-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 active:scale-95"
                                            aria-label="{{ __('public.copy') }} {{ $bankAccount->bank_name }}"
                                            title="{{ __('public.copy') }}"
                                        >
                                            <svg x-cloak x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect>
                                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path>
                                            </svg>
                                            <svg x-cloak x-show="copied" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 6 9 17l-5-5"></path>
                                            </svg>
                                        </button>
                                        <div
                                            x-cloak
                                            x-show="copied"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="translate-y-3 scale-95 opacity-0"
                                            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                                            x-transition:leave-end="translate-y-2 scale-95 opacity-0"
                                            class="fixed bottom-20 left-1/2 z-50 flex -translate-x-1/2 items-center gap-2 rounded-2xl border border-white/10 bg-neutral-950/95 px-3.5 py-2.5 text-xs font-black text-white shadow-2xl shadow-neutral-950/30 backdrop-blur-md"
                                            role="status"
                                        >
                                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-400 text-emerald-950">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 6 9 17l-5-5"></path>
                                                </svg>
                                            </span>
                                            <span class="whitespace-nowrap">{{ __('public.bank_number_copied') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <a href="{{ $whatsappUrl }}" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            {{ __('public.confirm_whatsapp') }}
                        </a>
                    </section>
                    @endif
                </div>

                <!-- Right Column (Bill) -->
                <div class="lg:sticky lg:top-6">
                    <section class="overflow-hidden rounded-[1.35rem] border border-white/80 bg-white/95 p-4 shadow-[0_24px_70px_-42px_rgba(15,23,42,0.5)] sm:rounded-[2rem] sm:p-6">
                        <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700 mb-1">{{ __('public.payment_breakdown') }}</h2>
                        <p class="text-xl font-black text-neutral-900">{{ __('public.bill_summary') }}</p>
                        <p class="mt-1 text-sm font-semibold leading-6 {{ $booking->hasExpiredPaymentWindow() ? 'text-rose-700' : 'text-neutral-600' }}">
                            {{ $booking->hasExpiredPaymentWindow() ? 'Batas pembayaran 30 menit sudah habis. Jangan transfer sebelum staf mengonfirmasi kembali.' : __('public.payment_instruction') }}
                        </p>

                        <div class="relative mt-5 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-700">{{ __('public.payment_now_title') }}</p>
                                <span class="rounded-full bg-white px-2 py-0.5 text-[0.6rem] font-bold uppercase tracking-wider text-emerald-600 border border-emerald-200">{{ __('public.payment_pending') }}</span>
                            </div>
                            <p class="text-3xl font-black text-emerald-950">{{ $formatCurrency($minDp) }}</p>
                        </div>

                        <div class="mt-5 rounded-2xl border border-neutral-100 bg-neutral-50/90 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.14em] text-neutral-600">{{ __('public.cost_detail_title') }}</p>

                            <div class="mt-4 space-y-3 text-sm text-neutral-600">
                                <div class="flex items-start justify-between gap-4">
                                    <span>{{ __('public.room_charge') }} {{ $booking->room->name }} ({{ trans_choice('public.night_count', $nights, ['count' => $nights]) }} x {{ $formatCurrency($booking->room->price) }})</span>
                                    <span class="shrink-0 font-bold text-neutral-900">{{ $formatCurrency($booking->total_room_price) }}</span>
                                </div>
                                @foreach ($booking->addons as $addon)
                                    <div class="flex items-start justify-between gap-4">
                                        <span>{{ $addon->item_name }} ({{ $addon->qty }} x {{ $formatCurrency($addon->price) }})</span>
                                        <span class="shrink-0 font-bold text-neutral-900">{{ $formatCurrency($addon->subtotal) }}</span>
                                    </div>
                                @endforeach
                                @if ((float) $booking->occupancy_adjustment_amount > 0)
                                    <div class="flex items-start justify-between gap-4">
                                        <span>{{ __('public.extra_guest_charge') }}</span>
                                        <span class="shrink-0 font-bold text-neutral-900">{{ $formatCurrency($booking->occupancy_adjustment_amount) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 space-y-3 border-t border-neutral-200 pt-4 text-sm">
                                <div class="flex items-center justify-between gap-4 font-bold text-neutral-900">
                                    <span>{{ __('public.total_bill') }}</span>
                                    <span>{{ $formatCurrency($booking->grand_total) }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-4 text-neutral-600">
                                    <span>{{ __('public.remaining_if_dp_paid') }}</span>
                                    <span class="font-bold text-neutral-900">{{ $formatCurrency($remainingAfterDp) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-between rounded-2xl bg-neutral-950 p-4 text-white shadow-lg shadow-neutral-950/10">
                            <span class="font-bold text-sm">{{ __('public.transfer_minimum_now') }}</span>
                            <span class="text-lg font-black">{{ $formatCurrency($minDp) }}</span>
                        </div>

                        <div class="mt-5 rounded-2xl border border-[#eadfce] bg-[#fbf7f0] p-4">
                            <p class="text-xs font-black uppercase tracking-[0.14em] text-[#9a6a2f]">Catatan resi</p>
                            <p class="mt-2 text-sm font-semibold leading-6 text-neutral-600">
                                Resi ini diterbitkan oleh {{ $businessProfile['business_name'] }} berdasarkan data reservasi di sistem. Kamar dikunci setelah pembayaran divalidasi.
                            </p>
                            <div class="mt-4 flex items-end justify-between gap-4 border-t border-[#eadfce] pt-3">
                                <div>
                                    <p class="text-xs font-semibold text-neutral-500">Hormat kami,</p>
                                    <p class="mt-1 text-sm font-black text-emerald-950">{{ __('public.brand') }}</p>
                                </div>
                                <img src="{{ $signaturePath }}" alt="Tanda tangan Villa Dafano" class="h-14 w-28 object-contain">
                            </div>
                        </div>
                    </section>
                </div>
                
            </div>
        </section>
    </main>

    <!-- Mobile WA Button -->
    @if ($canTransfer)
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-neutral-200 bg-white/95 p-3 shadow-[0_-18px_40px_-30px_rgba(15,23,42,0.8)] backdrop-blur lg:hidden">
        <a href="{{ $whatsappUrl }}" class="flex min-h-12 items-center justify-center rounded-xl bg-[#25D366] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-[#25D366]/30 active:scale-95 transition">
            {{ __('public.confirm_whatsapp') }}
        </a>
    </div>
    @endif
    <script>
        (() => {
            const alert = document.getElementById('booking-hold-alert');
            if (!alert) return;

            const target = new Date(alert.dataset.holdExpires).getTime();
            const output = alert.querySelector('[data-hold-countdown]');
            const tick = () => {
                const remaining = Math.max(0, target - Date.now());
                const minutes = Math.floor(remaining / 60000);
                const seconds = Math.floor((remaining % 60000) / 1000);
                output.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

                if (remaining <= 0) {
                    window.location.reload();
                }
            };

            tick();
            setInterval(tick, 1000);
        })();
    </script>
</body>
</html>
