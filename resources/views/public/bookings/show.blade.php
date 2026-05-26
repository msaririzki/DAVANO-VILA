<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('public.title') }} - {{ $booking->booking_code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#faf9f6] text-neutral-900 antialiased font-sans selection:bg-emerald-500 selection:text-white">
    @php
        $minDp = $booking->grand_total * ($minDpPercent / 100);
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

    <main class="min-h-screen pb-28 lg:pb-12 relative">
        <!-- Top Navigation (Overlay) -->
        <nav class="absolute top-0 left-0 right-0 z-50 px-4 sm:px-6 lg:px-8 pt-6">
            <div class="mx-auto max-w-7xl flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-3 rounded-full bg-black/40 backdrop-blur-md px-2 py-1.5 border border-white/10 shadow-lg pr-4">
                    <img src="{{ asset('dafano-media/logo/logo-df-2.png') }}" alt="{{ __('public.brand') }}" class="h-8 w-8 rounded-full object-cover border border-white/20">
                    <span class="text-sm font-bold text-white tracking-wide">{{ __('public.brand') }}</span>
                </div>
                
                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    @include('public.partials.language-switcher')
                    <a href="{{ route('public.home') }}" class="rounded-full bg-black/40 backdrop-blur-md px-4 py-2 border border-white/10 shadow-lg flex items-center gap-2 hover:bg-black/50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        <span class="text-xs font-bold text-white">{{ __('public.other_booking') }}</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-28 pb-16 sm:pt-36 sm:pb-20 lg:pt-40 lg:pb-32" x-data="{ currentSlide: 0, slides: {{ count($roomSpecificImages) }} }" x-init="setInterval(() => currentSlide = (currentSlide + 1) % slides, 5000)">
            
            @foreach($roomSpecificImages as $idx => $img)
                <img src="{{ $img }}" alt="{{ $booking->room->name }}" 
                     class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000"
                     :class="currentSlide === {{ $idx }} ? 'opacity-100' : 'opacity-0'">
            @endforeach
            
            <!-- Gradient ringan di bagian bawah saja agar teks putih tetap terbaca -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10"></div>
            
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-black/40 px-3 py-1 text-[0.65rem] font-bold uppercase tracking-widest text-white backdrop-blur-md mb-4 shadow-lg">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('public.booking_received') }}
                    </div>
                    <h1 class="text-4xl font-black text-white sm:text-5xl lg:text-[4rem] tracking-tight drop-shadow-lg">{{ $booking->booking_code }}</h1>
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-neutral-100 drop-shadow-md">
                        {{ __('public.booking_received_body') }}
                    </p>
                </div>

                <!-- Floating DP Card (Dark Glassmorphism) -->
                <aside class="w-full lg:w-96 shrink-0 rounded-[2rem] border border-white/10 bg-black/40 p-6 backdrop-blur-xl shadow-2xl">
                    <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-300 drop-shadow">{{ __('public.min_dp') }}</p>
                    <p class="mt-1 text-4xl font-black text-white drop-shadow-md">Rp {{ number_format($minDp, 0, ',', '.') }}</p>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-white/10 border border-white/5 p-3.5 backdrop-blur-md">
                            <p class="text-[0.55rem] font-bold uppercase tracking-widest text-white/70">{{ __('public.grand_total') }}</p>
                            <p class="mt-1 text-sm font-bold text-white">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-xl bg-emerald-500/20 border border-emerald-500/20 p-3.5 backdrop-blur-md">
                            <p class="text-[0.55rem] font-bold uppercase tracking-widest text-emerald-200">{{ __('public.duration') }}</p>
                            <p class="mt-1 text-sm font-bold text-white">{{ trans_choice('public.night_count', $nights, ['count' => $nights]) }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <!-- Main Content Layout -->
        <section class="relative mx-auto -mt-6 sm:-mt-12 max-w-7xl px-4 pb-12 sm:px-6 lg:px-8 z-20">
            <div class="grid gap-6 lg:grid-cols-[1.3fr_1fr] xl:gap-8 items-start">
                
                <!-- Left Column -->
                <div class="space-y-6">
                    
                    <!-- Detail Reservasi -->
                    <section class="rounded-[2rem] bg-white p-6 shadow-xl shadow-neutral-200/40">
                        <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700 mb-5">{{ __('public.reservation') }}</h2>
                        
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="rounded-xl bg-neutral-50 p-4 border border-neutral-100">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">{{ __('public.room') }}</p>
                                <p class="mt-1 text-base font-bold text-neutral-900">{{ $booking->room->name }}</p>
                            </div>
                            <div class="rounded-xl bg-neutral-50 p-4 border border-neutral-100">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">{{ __('public.guest_name') }}</p>
                                <p class="mt-1 text-base font-bold text-neutral-900">{{ $booking->guest_name }}</p>
                            </div>
                            <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-100">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-600">{{ __('public.check_in') }}</p>
                                <div class="mt-1 flex items-baseline gap-2">
                                    <p class="text-lg font-black text-emerald-950">{{ $booking->check_in_date->translatedFormat('d M') }}</p>
                                    <p class="text-xs font-bold text-emerald-700">14:00</p>
                                </div>
                            </div>
                            <div class="rounded-xl bg-amber-50 p-4 border border-amber-100">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-amber-600">{{ __('public.check_out') }}</p>
                                <div class="mt-1 flex items-baseline gap-2">
                                    <p class="text-lg font-black text-amber-950">{{ $booking->check_out_date->translatedFormat('d M') }}</p>
                                    <p class="text-xs font-bold text-amber-700">12:00</p>
                                </div>
                            </div>
                            <div class="rounded-xl bg-neutral-50 p-4 border border-neutral-100 sm:col-span-2">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-neutral-400">{{ __('public.whatsapp') }}</p>
                                <p class="mt-1 text-base font-bold text-neutral-900">{{ $booking->guest_phone }}</p>
                            </div>
                            @if ($booking->guest_request)
                                <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-100 sm:col-span-2">
                                    <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-700">{{ __('public.guest_request_label') }}</p>
                                    <p class="mt-2 text-sm font-semibold leading-6 text-emerald-950">{{ $booking->guest_request }}</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <!-- Fasilitas -->
                    @if ($booking->room->facilities)
                    <section class="rounded-[2rem] bg-white p-6 shadow-xl shadow-neutral-200/40">
                        <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-neutral-500 mb-5">{{ __('public.facilities') }}</h2>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach ($booking->room->facilities as $facility)
                                <span class="rounded-lg border border-neutral-200 bg-neutral-50 px-3 py-1.5 text-xs font-bold text-neutral-700">{{ $facility }}</span>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    <!-- Transfer Bank -->
                    <section class="rounded-[2rem] bg-white p-6 shadow-xl shadow-neutral-200/40 relative overflow-hidden border border-emerald-100">
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-emerald-500"></div>
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
                        
                        <div class="mt-6 flex flex-col gap-3">
                            @foreach ($bankAccounts as $bankAccount)
                                <div x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $bankAccount->account_number }}'); copied = true; setTimeout(() => copied = false, 2000)" class="cursor-pointer group relative flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl border border-neutral-200 bg-neutral-50 p-4 transition hover:border-emerald-400 hover:bg-emerald-50 shadow-sm">
                                    
                                    <div class="flex flex-col">
                                        <p class="text-[0.7rem] font-bold uppercase tracking-widest text-neutral-500 group-hover:text-emerald-700">{{ $bankAccount->bank_name }}</p>
                                        <p class="mt-0.5 text-xs font-medium text-neutral-500">{{ $bankAccount->account_name }}</p>
                                    </div>
                                    
                                    <div class="flex items-center gap-3 bg-white px-3 py-1.5 rounded-lg border border-neutral-200 shadow-sm group-hover:border-emerald-300">
                                        <p class="text-base font-black text-neutral-900 group-hover:text-emerald-950">{{ $bankAccount->account_number }}</p>
                                        <span class="rounded bg-neutral-100 px-2 py-1 text-[0.6rem] font-bold text-neutral-500 group-hover:bg-emerald-100 group-hover:text-emerald-700">{{ __('public.copy') }}</span>
                                    </div>
                                    
                                    <div x-cloak x-show="copied" x-transition class="absolute inset-0 flex items-center justify-center bg-emerald-500 text-white font-bold rounded-xl text-sm z-10">
                                        {{ __('public.bank_number_copied') }}
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
                <div class="lg:sticky lg:top-8">
                    <section class="rounded-[2rem] bg-white p-6 shadow-xl shadow-neutral-200/40">
                        <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700 mb-1">{{ __('public.payment_breakdown') }}</h2>
                        <p class="text-xl font-black text-neutral-900 mb-6">{{ __('public.bill_summary') }}</p>

                        <!-- Highlight DP -->
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-5 mb-5 relative">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-700">{{ __('public.transfer_minimum_now') }}</p>
                                <span class="rounded-full bg-white px-2 py-0.5 text-[0.6rem] font-bold uppercase tracking-wider text-emerald-600 border border-emerald-200">{{ __('public.payment_pending') }}</span>
                            </div>
                            <p class="text-2xl font-black text-emerald-950">Rp {{ number_format($minDp, 0, ',', '.') }}</p>
                        </div>

                        <!-- Summary rows -->
                        <div class="space-y-3 text-sm pb-5 border-b border-neutral-100">
                            <div class="flex justify-between items-center text-neutral-600 font-medium">
                                <span>{{ __('public.grand_total') }}</span>
                                <span class="font-bold text-neutral-900">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-neutral-600 font-medium">
                                <span>{{ __('public.remaining_after_dp') }}</span>
                                <span class="font-bold text-neutral-900">Rp {{ number_format($booking->grand_total - $minDp, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Accordion -->
                        <details class="group mt-5">
                            <summary class="flex cursor-pointer items-center justify-between font-bold text-sm text-emerald-700 select-none">
                                <span>{{ __('public.view_payment_details') }}</span>
                                <span class="rounded-full bg-emerald-50 p-1 group-open:bg-neutral-100 group-open:text-neutral-500 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-open:hidden" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden group-open:block" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </span>
                            </summary>
                            <div class="mt-4 space-y-3 text-sm text-neutral-600 bg-neutral-50 p-4 rounded-xl border border-neutral-100">
                                <div class="flex justify-between items-start gap-4">
                                    <span>{{ __('public.room_charge') }} ({{ trans_choice('public.night_count', $nights, ['count' => $nights]) }} x Rp {{ number_format($booking->room->price, 0, ',', '.') }})</span>
                                    <span class="font-bold text-neutral-900 shrink-0">Rp {{ number_format($booking->total_room_price, 0, ',', '.') }}</span>
                                </div>
                                @foreach ($booking->addons as $addon)
                                    <div class="flex justify-between items-start gap-4">
                                        <span>{{ $addon->item_name }} ({{ $addon->qty }} x Rp {{ number_format($addon->price, 0, ',', '.') }})</span>
                                        <span class="font-bold text-neutral-900 shrink-0">Rp {{ number_format($addon->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </details>

                        <div class="mt-5 rounded-xl bg-neutral-900 p-4 flex items-center justify-between text-white">
                            <span class="font-bold text-sm">{{ __('public.balance_due') }}</span>
                            <span class="text-lg font-black">Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</span>
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
