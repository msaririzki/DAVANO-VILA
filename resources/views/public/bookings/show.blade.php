<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('public.title') }} - {{ $booking->booking_code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    @endphp

    <main class="relative min-h-screen pb-28 lg:pb-12">
        <!-- Top Navigation (Overlay) -->
        <nav class="absolute left-0 right-0 top-0 z-50 px-4 pt-5 sm:px-6 lg:px-8">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-3">
                <!-- Logo -->
                <div class="flex min-w-0 items-center gap-3 rounded-full border border-white/15 bg-black/45 px-2 py-1.5 pr-4 shadow-lg backdrop-blur-md">
                    <img src="{{ asset('dafano-media/logo/logo-df-2.png') }}" alt="{{ __('public.brand') }}" class="h-8 w-8 rounded-full object-cover border border-white/20">
                    <span class="truncate text-sm font-bold tracking-wide text-white">{{ __('public.brand') }}</span>
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
        <section class="relative overflow-hidden pb-20 pt-28 sm:pb-24 sm:pt-36 lg:pb-32 lg:pt-40" x-data="{ currentSlide: 0, slides: {{ count($roomSpecificImages) }} }" x-init="setInterval(() => currentSlide = (currentSlide + 1) % slides, 5000)">
            
            @foreach($roomSpecificImages as $idx => $img)
                <img src="{{ $img }}" alt="{{ $booking->room->name }}" 
                     class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000"
                     :class="currentSlide === {{ $idx }} ? 'opacity-100' : 'opacity-0'">
            @endforeach
            
            <!-- Gradient ringan di bagian bawah saja agar teks putih tetap terbaca -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-black/10"></div>
            
            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-black/45 px-3 py-1 text-[0.65rem] font-bold uppercase tracking-widest text-white shadow-lg backdrop-blur-md">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('public.booking_received') }}
                    </div>
                    <h1 class="max-w-full break-words text-4xl font-black tracking-tight text-white drop-shadow-lg sm:text-5xl lg:text-[4rem]">{{ $booking->booking_code }}</h1>
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-neutral-100 drop-shadow-md">
                        {{ __('public.booking_received_body') }}
                    </p>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold text-white backdrop-blur-md">{{ $booking->room->name }}</span>
                        <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold text-white backdrop-blur-md">{{ $booking->check_in_date->translatedFormat('d M') }} - {{ $booking->check_out_date->translatedFormat('d M') }}</span>
                        <span class="inline-flex items-center rounded-full border border-emerald-300/25 bg-emerald-500/20 px-3 py-1.5 text-xs font-bold text-emerald-50 backdrop-blur-md">{{ trans_choice('public.night_count', $nights, ['count' => $nights]) }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content Layout -->
        <section class="relative z-20 mx-auto -mt-12 max-w-7xl px-4 pb-12 sm:-mt-16 sm:px-6 lg:px-8">
            <div class="grid items-start gap-6 lg:grid-cols-[1.28fr_0.92fr] xl:gap-8">
                
                <!-- Left Column -->
                <div class="space-y-5 sm:space-y-6">
                    
                    <!-- Detail Reservasi -->
                    <section class="overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/95 p-5 shadow-[0_24px_70px_-42px_rgba(15,23,42,0.45)] sm:rounded-[2rem] sm:p-6">
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700">{{ __('public.reservation') }}</h2>
                                <p class="mt-1 text-sm font-semibold text-neutral-500">{{ $booking->booking_code }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-[0.65rem] font-black uppercase tracking-[0.12em] text-emerald-700 ring-1 ring-emerald-100">{{ __('public.booking_received') }}</span>
                        </div>
                        
                        <div class="grid gap-3 sm:grid-cols-2 sm:gap-4">
                            <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">{{ __('public.room') }}</p>
                                <p class="mt-1 text-base font-bold text-neutral-900">{{ $booking->room->name }}</p>
                            </div>
                            <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">{{ __('public.guest_name') }}</p>
                                <p class="mt-1 text-base font-bold text-neutral-900">{{ $booking->guest_name }}</p>
                            </div>
                            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-600">{{ __('public.check_in') }}</p>
                                <div class="mt-1 flex items-baseline gap-2">
                                    <p class="text-lg font-black text-emerald-950">{{ $booking->check_in_date->translatedFormat('d M') }}</p>
                                    <p class="text-xs font-bold text-emerald-700">14:00</p>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-amber-600">{{ __('public.check_out') }}</p>
                                <div class="mt-1 flex items-baseline gap-2">
                                    <p class="text-lg font-black text-amber-950">{{ $booking->check_out_date->translatedFormat('d M') }}</p>
                                    <p class="text-xs font-bold text-amber-700">12:00</p>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4 sm:col-span-2">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">{{ __('public.whatsapp') }}</p>
                                <p class="mt-1 text-base font-bold text-neutral-900">{{ $booking->guest_phone }}</p>

                                @if ($booking->room->facilities)
                                    <div class="mt-4 border-t border-neutral-200 pt-4">
                                        <p class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">{{ __('public.facilities') }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach ($booking->room->facilities as $facility)
                                                <span class="rounded-full border border-neutral-200 bg-white px-3 py-1.5 text-xs font-bold text-neutral-700">{{ $facility }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if ($booking->guest_request)
                                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 sm:col-span-2">
                                    <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-700">{{ __('public.guest_request_label') }}</p>
                                    <p class="mt-2 text-sm font-semibold leading-6 text-emerald-950">{{ $booking->guest_request }}</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <!-- Transfer Bank -->
                    <section class="relative overflow-hidden rounded-[1.75rem] border border-emerald-100 bg-white/95 p-5 shadow-[0_24px_70px_-42px_rgba(15,23,42,0.45)] sm:rounded-[2rem] sm:p-6">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-emerald-500"></div>
                        <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700 mb-1">{{ __('public.bank_transfer') }}</h2>
                                <p class="text-xl font-black text-neutral-900">{{ __('public.choose_bank') }}</p>
                                <p class="text-xs font-medium text-neutral-500 mt-1">{{ __('public.tap_bank_to_copy') }}</p>
                            </div>
                            <a href="{{ $whatsappUrl }}" class="hidden w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-[#25D366] px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-[#25D366]/30 transition hover:-translate-y-0.5 hover:shadow-[#25D366]/40 active:scale-95 sm:w-auto lg:inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                {{ __('public.confirm_whatsapp') }}
                            </a>
                        </div>
                        
                        <div class="mt-4 overflow-hidden rounded-2xl border border-neutral-200 bg-neutral-50/80">
                            @foreach ($bankAccounts as $bankAccount)
                                <div x-data="{ copied: false }" class="grid grid-cols-[minmax(0,1fr)_auto_auto] items-center gap-2.5 border-b border-neutral-200/70 px-3 py-2.5 last:border-b-0 sm:gap-3 sm:px-4">
                                    <div class="min-w-0">
                                        <p class="truncate text-[0.7rem] font-black uppercase tracking-[0.12em] text-neutral-600">
                                            {{ $bankAccount->bank_name }} <span class="font-semibold tracking-normal text-neutral-400">- {{ $bankAccount->account_name }}</span>
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-white px-3 py-1.5 text-right shadow-sm ring-1 ring-neutral-200">
                                        <p class="whitespace-nowrap font-mono text-sm font-black tracking-wide text-neutral-950 sm:text-base">{{ $bankAccount->account_number }}</p>
                                    </div>

                                    <div class="relative">
                                        <button
                                            type="button"
                                            @click="navigator.clipboard.writeText('{{ $bankAccount->account_number }}'); copied = true; setTimeout(() => copied = false, 1600)"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-700 transition hover:-translate-y-0.5 hover:border-emerald-300 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 active:scale-95"
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
                                        <span x-cloak x-show="copied" x-transition class="absolute right-0 top-11 whitespace-nowrap rounded-full bg-neutral-950 px-2.5 py-1 text-[0.65rem] font-black text-white shadow-lg">
                                            {{ __('public.bank_number_copied') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <a href="{{ $whatsappUrl }}" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            {{ __('public.confirm_whatsapp') }}
                        </a>
                    </section>
                </div>

                <!-- Right Column (Bill) -->
                <div class="lg:sticky lg:top-6">
                    <section class="overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/95 p-5 shadow-[0_24px_70px_-42px_rgba(15,23,42,0.5)] sm:rounded-[2rem] sm:p-6">
                        <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700 mb-1">{{ __('public.payment_breakdown') }}</h2>
                        <p class="text-xl font-black text-neutral-900">{{ __('public.bill_summary') }}</p>
                        <p class="mt-1 text-sm font-semibold leading-6 text-neutral-500">{{ __('public.payment_instruction') }}</p>

                        <div class="relative mt-5 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-700">{{ __('public.payment_now_title') }}</p>
                                <span class="rounded-full bg-white px-2 py-0.5 text-[0.6rem] font-bold uppercase tracking-wider text-emerald-600 border border-emerald-200">{{ __('public.payment_pending') }}</span>
                            </div>
                            <p class="text-3xl font-black text-emerald-950">Rp {{ number_format($minDp, 0, ',', '.') }}</p>
                        </div>

                        <div class="mt-5 rounded-2xl border border-neutral-100 bg-neutral-50/90 p-4">
                            <p class="text-xs font-black uppercase tracking-[0.14em] text-neutral-500">{{ __('public.cost_detail_title') }}</p>

                            <div class="mt-4 space-y-3 text-sm text-neutral-600">
                                <div class="flex items-start justify-between gap-4">
                                    <span>{{ __('public.room_charge') }} {{ $booking->room->name }} ({{ trans_choice('public.night_count', $nights, ['count' => $nights]) }} x Rp {{ number_format($booking->room->price, 0, ',', '.') }})</span>
                                    <span class="shrink-0 font-bold text-neutral-900">Rp {{ number_format($booking->total_room_price, 0, ',', '.') }}</span>
                                </div>
                                @foreach ($booking->addons as $addon)
                                    <div class="flex items-start justify-between gap-4">
                                        <span>{{ $addon->item_name }} ({{ $addon->qty }} x Rp {{ number_format($addon->price, 0, ',', '.') }})</span>
                                        <span class="shrink-0 font-bold text-neutral-900">Rp {{ number_format($addon->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 space-y-3 border-t border-neutral-200 pt-4 text-sm">
                                <div class="flex items-center justify-between gap-4 font-bold text-neutral-900">
                                    <span>{{ __('public.total_bill') }}</span>
                                    <span>Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-4 text-neutral-600">
                                    <span>{{ __('public.remaining_if_dp_paid') }}</span>
                                    <span class="font-bold text-neutral-900">Rp {{ number_format($remainingAfterDp, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-between rounded-2xl bg-neutral-950 p-4 text-white shadow-lg shadow-neutral-950/10">
                            <span class="font-bold text-sm">{{ __('public.transfer_minimum_now') }}</span>
                            <span class="text-lg font-black">Rp {{ number_format($minDp, 0, ',', '.') }}</span>
                        </div>
                    </section>
                </div>
                
            </div>
        </section>
    </main>

    <!-- Mobile WA Button -->
    <div class="fixed inset-x-0 bottom-0 z-40 border-t border-neutral-200 bg-white/95 p-3 shadow-[0_-18px_40px_-30px_rgba(15,23,42,0.8)] backdrop-blur lg:hidden">
        <a href="{{ $whatsappUrl }}" class="flex min-h-12 items-center justify-center rounded-xl bg-[#25D366] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-[#25D366]/30 active:scale-95 transition">
            {{ __('public.confirm_whatsapp') }}
        </a>
    </div>
</body>
</html>
