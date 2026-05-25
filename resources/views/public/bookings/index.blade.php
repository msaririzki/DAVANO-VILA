<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('public.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .calendar-nav-btn {
            display: inline-flex;
            height: 2rem;
            width: 2rem;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            border: 1px solid rgb(229 231 235);
            background: rgba(255, 255, 255, 0.88);
            color: rgb(23 23 23);
            box-shadow: 0 10px 24px -18px rgba(15, 23, 42, 0.55);
            transition: transform 160ms ease, border-color 160ms ease, background 160ms ease;
        }
        .calendar-nav-btn:hover {
            transform: translateY(-1px);
            border-color: rgb(16 185 129);
            background: rgb(236 253 245);
        }
        .calendar-nav-btn:disabled {
            cursor: not-allowed;
            opacity: 0.36;
            transform: none;
        }
        .calendar-day {
            position: relative;
            min-height: 2.08rem;
            border-radius: 0.72rem;
            font-size: 0.78rem;
            font-weight: 800;
            color: rgb(38 38 38);
            transition: transform 140ms ease, background 140ms ease, color 140ms ease, box-shadow 140ms ease;
        }
        .calendar-day:not(:disabled):hover {
            transform: translateY(-1px);
            background: rgb(236 253 245);
            color: rgb(6 95 70);
        }
        .calendar-day.is-muted {
            color: rgb(212 212 212);
        }
        .calendar-day.is-disabled {
            cursor: not-allowed;
            color: rgb(190 190 190);
            opacity: 0.55;
            text-decoration: none;
        }
        .calendar-day.is-empty {
            visibility: hidden;
            pointer-events: none;
        }
        .calendar-day.is-today {
            box-shadow: inset 0 0 0 2px rgb(4 120 87);
            color: rgb(4 120 87);
        }
        .calendar-day.is-in-range {
            background: rgb(209 250 229);
            color: rgb(6 78 59);
        }
        .calendar-day.is-selected {
            background: rgb(23 23 23);
            color: white;
            box-shadow: 0 16px 28px -18px rgba(15, 23, 42, 0.75);
        }
        .calendar-day.is-checkout {
            background: rgb(146 64 14);
            color: white;
        }
        .calendar-day.is-selected.is-today,
        .calendar-day.is-checkout.is-today {
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.72), 0 16px 28px -18px rgba(15, 23, 42, 0.75);
        }
        .public-search-calendar,
        .room-booking-calendar {
            max-width: 27.5rem;
        }
        .public-search-calendar {
            margin-inline: auto;
        }
        .room-booking-calendar .calendar-nav-btn {
            height: 1.8rem;
            width: 1.8rem;
        }
        .room-booking-calendar .calendar-day {
            min-height: 1.82rem;
            border-radius: 0.58rem;
            font-size: 0.72rem;
        }
        @media (max-width: 640px) {
            .calendar-day {
                min-height: 2rem;
                border-radius: 0.65rem;
                font-size: 0.75rem;
            }
            .public-search-calendar,
            .room-booking-calendar {
                max-width: none;
            }
            .room-booking-calendar .calendar-day {
                min-height: 1.85rem;
            }
        }

        /* Custom Animations */
        @keyframes fade-in-up {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: none;
            }
        }
        @keyframes slide-down {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: none;
            }
        }
        @keyframes modal-pop {
            0% {
                opacity: 0;
                transform: translate(-50%, -45%) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }
        @keyframes fade-in {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .animate-slide-down {
            animation: slide-down 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .animate-modal-pop {
            animation: modal-pop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out forwards;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .delay-500 { animation-delay: 500ms; }
    </style>
</head>
<body class="bg-[#fcfbf9] text-neutral-900 antialiased font-sans selection:bg-emerald-200 selection:text-emerald-900">
    @php
        $heroImages = [
            asset('dafano-media/gallery/halaman/yard-3.jpg'),
            asset('dafano-media/gallery/halaman/yard-2.jpg'),
            asset('dafano-media/gallery/halaman/yard-4.jpg'),
            asset('dafano-media/gallery/halaman/yard-7.jpg'),
            asset('dafano-media/gallery/halaman/yard-9.jpg'),
        ];
        $heroVideo = asset('dafano-media/video/vidio.mp4');
        $roomImages = [
            asset('dafano-media/gallery/kamar/room-1.png'),
            asset('dafano-media/gallery/kamar/room-2.jpg'),
            asset('dafano-media/gallery/kamar/room-10.jpeg'),
        ];
        $sourceOptions = [
            'Instagram' => __('public.source_instagram'),
            'Google' => __('public.source_google'),
            'Friend' => __('public.source_friend'),
            'TikTok' => __('public.source_tiktok'),
            'Walk-in' => __('public.source_walkin'),
            'Other' => __('public.source_other'),
        ];
        $showingResults = request()->routeIs('public.rooms.index') && $checkIn && $checkOut;
        $selectedRoomId = request('selected_room');
    @endphp

    <main>
        <!-- HERO SECTION -->
        <section class="relative overflow-hidden bg-neutral-950 {{ $showingResults ? 'h-[50vh]' : 'min-h-[85vh]' }} flex flex-col">
            <div class="absolute inset-0">
                @foreach ($heroImages as $image)
                    <img src="{{ $image }}" alt="{{ __('public.hero_alt') }}" class="hero-slide absolute inset-0 h-full w-full object-cover transition-all duration-1000 ease-in-out {{ $loop->first ? 'opacity-100 scale-100' : 'opacity-0 scale-105' }}">
                @endforeach
                <video
                    id="hero-video"
                    class="absolute inset-0 hidden h-full w-full object-cover transition-opacity duration-1000 opacity-0"
                    data-enabled="{{ $heroMediaMode === 'video' ? '1' : '0' }}"
                    data-src="{{ $heroVideo }}"
                    poster="{{ $heroImages[0] }}"
                    muted
                    loop
                    playsinline
                    preload="none"
                ></video>
            </div>
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/20 to-black/60"></div>

            <div class="relative flex-1 flex flex-col w-full mx-auto max-w-7xl px-6 sm:px-8 lg:px-10 z-10">
                <!-- Header / Navbar -->
                <header class="flex items-center justify-between py-6 animate-slide-down">
                    <a href="{{ route('public.home') }}" class="flex items-center gap-3 rounded-full border border-white/15 bg-black/20 py-1.5 pl-1.5 pr-4 text-white shadow-lg backdrop-blur-md transition hover:bg-black/30">
                        <img src="{{ asset('dafano-media/brand/logo-dafano-villa.jpg') }}" alt="{{ __('public.brand') }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-white/25">
                        <span class="text-lg font-black tracking-tight drop-shadow">{{ __('public.brand') }}</span>
                    </a>
                    <div class="flex items-center gap-4">
                        @include('public.partials.language-switcher')
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex rounded-full border border-white/20 bg-white/10 backdrop-blur-md px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20 hover:border-white/30 shadow-sm">{{ __('public.staff') }}</a>
                    </div>
                </header>

                @unless ($showingResults)
                    <!-- Hero Content -->
                    <div class="flex-1 flex flex-col justify-center max-w-3xl pb-24 md:pb-32">
                        <div class="animate-fade-in-up delay-100 mb-6">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 text-[0.65rem] font-bold tracking-[0.2em] uppercase text-white border border-white/40 bg-black/20 rounded-full backdrop-blur-md shadow-sm">
                                <span class="relative h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="relative">{{ __('public.hero_eyebrow') }}</span>
                            </span>
                        </div>
                        <h1 class="animate-fade-in-up delay-200 text-4xl font-black leading-[1.1] text-white sm:text-5xl md:text-6xl lg:text-7xl drop-shadow-lg mb-5 sm:mb-6 tracking-tight">
                            {{ __('public.brand') }}
                        </h1>
                        <p class="animate-fade-in-up delay-300 text-base leading-relaxed text-neutral-200 sm:text-lg md:text-xl drop-shadow-md max-w-2xl font-medium">
                            {{ __('public.hero_body') }}
                        </p>
                    </div>
                @endunless
            </div>
        </section>

        <!-- SEARCH FORM (Floating) -->
        <div id="search-form" class="relative z-20 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 {{ $showingResults ? '-mt-8' : '-mt-14 sm:-mt-16' }} animate-fade-in delay-400">
            <div class="rounded-[1.75rem] border border-white/80 bg-white/95 shadow-[0_28px_70px_-35px_rgba(15,23,42,0.5)]">
                <!-- Unified Search Bar -->
                <form id="top-search-form" method="GET" action="{{ route('public.rooms.index') }}" class="relative flex flex-col items-stretch gap-3 p-2.5 sm:flex-row sm:items-center sm:p-3 lg:p-4">
                    <input type="hidden" name="selected_room" id="selected_room_input" value="{{ request('selected_room') }}">
                    
                    <div class="relative w-full flex-1 rounded-[1.4rem] border border-neutral-200 bg-neutral-50/80 sm:rounded-full">
                        @include('public.partials.date-range-calendar', [
                            'calendarId' => 'top-date-range-calendar',
                            'checkInValue' => $checkIn,
                            'checkOutValue' => $checkOut,
                            'calendarClass' => 'public-search-calendar w-full',
                            'collapsible' => true,
                            'panelMode' => 'modal',
                        ])
                    </div>
                    
                    <button type="submit" class="flex min-h-[3.75rem] w-full shrink-0 items-center justify-center gap-2 rounded-[1.35rem] bg-emerald-700 px-8 py-3.5 text-[0.95rem] font-bold text-white shadow-[0_18px_32px_-20px_rgba(4,120,87,0.75)] transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-800 active:translate-y-0 sm:min-h-[4.35rem] sm:w-auto sm:rounded-full sm:px-10">
                        <span id="search-btn-text">{{ request('selected_room') ? 'Lanjut Pesan' : __('public.search_rooms') }}</span>
                    </button>
                </form>

                @unless ($showingResults)
                <!-- Features Highlights -->
                <div class="grid grid-cols-2 gap-px overflow-hidden rounded-b-[1.75rem] border-t border-neutral-100 bg-neutral-100 sm:grid-cols-4 animate-fade-in-up delay-500">
                    <div class="flex min-w-0 items-center gap-3 bg-white px-3 py-3.5 sm:px-4 lg:px-5">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m8 3 4 8 5-5 5 15H2L8 3Z"/><path d="M8 21 12 11"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-black text-neutral-950">{{ __('public.feature_view_title') }}</p>
                            <p class="mt-0.5 truncate text-[0.68rem] font-semibold text-neutral-500">{{ __('public.feature_view_body') }}</p>
                        </div>
                    </div>
                    <div class="flex min-w-0 items-center gap-3 bg-white px-3 py-3.5 sm:px-4 lg:px-5">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-teal-50 text-teal-700 ring-1 ring-teal-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 4 13c0-6 8-10 8-10s8 4 8 10a7 7 0 0 1-7 7"/><path d="M12 3v18"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-black text-neutral-950">{{ __('public.feature_nature_title') }}</p>
                            <p class="mt-0.5 truncate text-[0.68rem] font-semibold text-neutral-500">{{ __('public.feature_nature_body') }}</p>
                        </div>
                    </div>
                    <div class="flex min-w-0 items-center gap-3 bg-white px-3 py-3.5 sm:px-4 lg:px-5">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sky-50 text-sky-700 ring-1 ring-sky-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13a10 10 0 0 1 14 0"/><path d="M8.5 16.5a5 5 0 0 1 7 0"/><path d="M12 20h.01"/><path d="M2 9a15 15 0 0 1 20 0"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-black text-neutral-950">{{ __('public.feature_wifi_title') }}</p>
                            <p class="mt-0.5 truncate text-[0.68rem] font-semibold text-neutral-500">{{ __('public.feature_wifi_body') }}</p>
                        </div>
                    </div>
                    <div class="flex min-w-0 items-center gap-3 bg-white px-3 py-3.5 sm:px-4 lg:px-5">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-700 ring-1 ring-amber-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="10" x="4" y="11" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-black text-neutral-950">{{ __('public.feature_privacy_title') }}</p>
                            <p class="mt-0.5 truncate text-[0.68rem] font-semibold text-neutral-500">{{ __('public.feature_privacy_body') }}</p>
                        </div>
                    </div>
                </div>
                @endunless
            </div>
        </div>

        @unless ($showingResults)
        <!-- ABOUT SECTION -->
        <section class="relative max-w-5xl mx-auto px-6 py-20 mt-4 sm:mt-8 text-center">
            <!-- Decorative Background Blob -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-3xl h-64 bg-emerald-100/40 blur-[80px] rounded-full pointer-events-none -z-10"></div>
            
            <span class="text-sm font-bold uppercase tracking-widest text-emerald-600 mb-4 block">{{ __('public.brand') }}</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-emerald-950 mb-8 font-serif leading-tight">{{ __('public.about_title') }}</h2>
            <p class="text-lg sm:text-xl text-neutral-600 leading-relaxed max-w-3xl mx-auto">{{ __('public.about_body') }}</p>
        </section>
        @endunless

        <!-- ROOMS SECTION -->
        <section id="rooms" class="scroll-mt-10 py-20 {{ $showingResults ? '' : 'bg-emerald-50/40' }}">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-6">
                    <div>
                        <span class="text-sm font-bold uppercase tracking-widest text-emerald-600 mb-3 block">{{ __('public.reservation') }}</span>
                        <h2 class="text-3xl sm:text-4xl font-bold text-neutral-900">{{ __('public.rooms_title') }}</h2>
                        <p class="mt-3 text-neutral-600 max-w-lg text-lg">{{ __('public.choose_available_room') }}</p>
                    </div>
                    @if ($showingResults)
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 max-w-sm text-sm text-amber-900 shadow-sm">
                            <p class="font-medium">📌 {{ __('public.cashless_note') }}</p>
                        </div>
                    @endif
                </div>

                @if ($rooms->isEmpty())
                    <div class="text-center py-20 bg-white rounded-3xl border border-neutral-200 shadow-sm">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-neutral-100 mb-4 text-2xl">🛏️</div>
                        <h3 class="text-xl font-bold text-neutral-900 mb-2">Oops!</h3>
                        <p class="text-neutral-500">{{ __('public.no_rooms') }}</p>
                    </div>
                @else
                    <!-- Carousel / Grid -->
                    @if ($rooms->count() <= 2)
                        <div class="grid grid-cols-1 items-start sm:grid-cols-2 gap-6 lg:gap-8 pt-4 pb-12">
                    @else
                        <div class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-12 pt-4 hide-scrollbar">
                    @endif
                        @foreach ($rooms as $room)
                            @php
                                $nights = max(1, \Carbon\Carbon::parse($checkIn ?: now())->diffInDays(\Carbon\Carbon::parse($checkOut ?: now()->addDay())));
                                $grandTotal = $room->price * $nights;
                                $minDp = $grandTotal * ($minDpPercent / 100);
                                $image = $room->imageUrl() ?: $roomImages[$loop->index % count($roomImages)];

                                $roomSpecificImages = [];
                                if (stripos($room->name, 'commercial') !== false) {
                                    $roomSpecificImages = [
                                        asset('dafano-media/gallery/kamar/commercial/img-2.jpg'),
                                        asset('dafano-media/gallery/kamar/commercial/img-3.jpg'),
                                        asset('dafano-media/gallery/kamar/commercial/img-4.jpg'),
                                        asset('dafano-media/gallery/kamar/commercial/img-5.jpg'),
                                        asset('dafano-media/gallery/kamar/commercial/img-6.jpg'),
                                    ];
                                } elseif (stripos($room->name, 'superior') !== false) {
                                    $roomSpecificImages = [
                                        asset('dafano-media/gallery/kamar/superior/img-1.jpg'),
                                        asset('dafano-media/gallery/kamar/superior/img-2.jpg'),
                                        asset('dafano-media/gallery/kamar/superior/img-3.jpg'),
                                    ];
                                } else {
                                    $roomSpecificImages = [ $image ];
                                }
                            @endphp

                            @if ($rooms->count() <= 2)
                                <article id="room-card-{{ $room->id }}" class="w-full min-w-0 self-start flex flex-col bg-white rounded-[2rem] border border-neutral-100 shadow-xl shadow-neutral-200/50 overflow-hidden group transition-all duration-700 {{ request('selected_room') && request('selected_room') != $room->id ? 'opacity-60 grayscale-[50%] scale-[0.98]' : (request('selected_room') == $room->id ? 'ring-4 ring-emerald-500/40 shadow-2xl shadow-emerald-900/20 scale-[1.01]' : 'hover:shadow-2xl hover:-translate-y-1') }}">
                            @else
                                <article id="room-card-{{ $room->id }}" class="snap-center shrink-0 w-[90vw] md:w-[400px] flex flex-col bg-white rounded-[2rem] border border-neutral-100 shadow-xl shadow-neutral-200/50 overflow-hidden group transition-all duration-700 {{ request('selected_room') && request('selected_room') != $room->id ? 'opacity-60 grayscale-[50%] scale-[0.98]' : (request('selected_room') == $room->id ? 'ring-4 ring-emerald-500/40 shadow-2xl shadow-emerald-900/20 scale-[1.01]' : 'hover:shadow-2xl hover:-translate-y-1') }}">
                            @endif
                                <!-- Image Header (Carousel) -->
                                <div class="relative h-64 sm:h-80 overflow-hidden bg-neutral-100 group room-carousel-container">
                                    <div class="flex h-full w-full overflow-x-auto snap-x snap-mandatory hide-scrollbar room-carousel">
                                        @foreach($roomSpecificImages as $img)
                                            <div class="w-full h-full shrink-0 snap-center relative">
                                                <img src="{{ $img }}" alt="{{ $room->name }}" class="w-full h-full object-cover transition-transform duration-700">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Desktop Navigation arrows -->
                                    @if(count($roomSpecificImages) > 1)
                                    <button type="button" aria-label="Previous image" class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/70 hover:bg-white text-neutral-900 shadow-md flex items-center justify-center sm:opacity-100 opacity-0 transition-all duration-300 z-10 prev-btn hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                                    </button>
                                    <button type="button" aria-label="Next image" class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/70 hover:bg-white text-neutral-900 shadow-md flex items-center justify-center sm:opacity-100 opacity-0 transition-all duration-300 z-10 next-btn hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                                    </button>
                                    @endif
                                    
                                    <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end pointer-events-none z-10">
                                        <h3 class="text-2xl font-bold text-white drop-shadow-md">{{ $room->name }}</h3>
                                        <span class="bg-white/20 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">{{ __('public.guest_count', ['count' => $room->capacity]) }}</span>
                                    </div>
                                </div>

                                <!-- Body -->
                                <div class="p-6 sm:p-8 flex-1 flex flex-col">
                                    <p class="text-neutral-600 mb-6 leading-relaxed line-clamp-3 lg:min-h-[4.5rem]">{{ $room->description }}</p>
                                    
                                    @if ($room->facilities)
                                        <div class="mb-8 lg:min-h-[8.5rem]">
                                            <p class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-3">{{ __('public.facilities') }}</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($room->facilities as $facility)
                                                    <span class="inline-flex items-center bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-xl text-xs font-semibold border border-emerald-100/50 shadow-sm transition hover:bg-emerald-100 hover:-translate-y-0.5">{{ $facility }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Price & Booking -->
                                    <div class="mt-auto">
                                        <div class="bg-neutral-50 rounded-2xl p-5 mb-6">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-neutral-500 font-medium">{{ __('public.per_night') }}</span>
                                                <span class="text-xl font-bold text-emerald-800">Rp {{ number_format($room->price, 0, ',', '.') }}</span>
                                            </div>
                                            @if ($showingResults)
                                                <div class="flex justify-between items-center text-sm mb-2">
                                                    <span class="text-neutral-500">{{ __('public.duration') }}</span>
                                                    <span class="font-semibold text-neutral-900">{{ trans_choice('public.night_count', $nights, ['count' => $nights]) }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-sm pt-3 border-t border-neutral-200 mt-3">
                                                    <span class="text-neutral-500">{{ __('public.min_dp') }}</span>
                                                    <span class="font-bold text-amber-600">Rp {{ number_format($minDp, 0, ',', '.') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        @unless ($showingResults)
                                            <button type="button" onclick="showRoomBookingForm('{{ $room->id }}')" data-book-room-button="{{ $room->id }}" class="w-full text-center rounded-xl bg-neutral-900 px-6 py-4 text-sm font-bold text-white shadow-lg hover:bg-emerald-800 hover:-translate-y-0.5 transition-all active:scale-95 {{ $selectedRoomId == $room->id ? 'hidden' : '' }}">
                                                {{ __('public.book_now') }}
                                            </button>
                                        @endunless

                                        <div id="booking-form-{{ $room->id }}" class="scroll-mt-32 mt-4 pt-6 border-t border-neutral-100 {{ (! $showingResults && $selectedRoomId != $room->id) ? 'hidden' : '' }} {{ $selectedRoomId == $room->id ? 'animate-[fadeIn_0.5s_ease-out]' : '' }}">
                                            <div class="bg-gradient-to-br from-emerald-50/50 to-white rounded-2xl p-5 border border-emerald-100/50 shadow-inner">
                                                <h4 class="text-sm font-bold text-emerald-900 mb-4 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-600"><path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z" clip-rule="evenodd" /></svg>
                                                    Lengkapi Data Pesanan
                                                </h4>
                                                <form method="POST" action="{{ route('public.bookings.store') }}" class="grid gap-5">
                                                    @csrf
                                                    <input type="hidden" name="room_id" value="{{ $room->id }}">

                                                    @if ($showingResults)
                                                        <input type="hidden" name="check_in_date" value="{{ $checkIn }}">
                                                        <input type="hidden" name="check_out_date" value="{{ $checkOut }}">
                                                    @else
                                                        @include('public.partials.date-range-calendar', [
                                                            'calendarId' => 'room-date-range-calendar-'.$room->id,
                                                            'checkInValue' => old('check_in_date'),
                                                            'checkOutValue' => old('check_out_date'),
                                                            'calendarClass' => 'room-booking-calendar',
                                                            'collapsible' => true,
                                                            'panelMode' => 'inline-collapse',
                                                        ])
                                                    @endif

                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">{{ __('public.guest_name') }}</label>
                                                            <input name="guest_name" value="{{ old('guest_name') }}" required placeholder="Nama Lengkap" class="w-full rounded-xl border-neutral-200 bg-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-shadow hover:shadow-md">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">{{ __('public.guest_phone') }}</label>
                                                            <input name="guest_phone" value="{{ old('guest_phone') }}" required placeholder="0812xxx" class="w-full rounded-xl border-neutral-200 bg-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-shadow hover:shadow-md">
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">{{ __('public.source_label') }}</label>
                                                        <select name="acquisition_source" class="w-full rounded-xl border-neutral-200 bg-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-shadow hover:shadow-md">
                                                            <option value="">{{ __('public.source_placeholder') }}</option>
                                                            @foreach ($sourceOptions as $sourceValue => $sourceLabel)
                                                                <option value="{{ $sourceValue }}">{{ $sourceLabel }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="relative mt-2 rounded-xl bg-emerald-800 p-1 shadow-lg shadow-emerald-900/20">
                                                        <div class="border border-dashed border-emerald-400/50 rounded-lg p-4 flex items-center justify-between bg-emerald-800 text-white">
                                                            <div>
                                                                <span class="text-xs font-medium text-emerald-200 uppercase tracking-wider block mb-1">{{ __('public.total_bill') }}</span>
                                                                <strong class="text-2xl font-bold drop-shadow-sm block">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                                                            </div>
                                                            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-emerald-100"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button type="submit" class="w-full rounded-xl bg-neutral-900 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-neutral-900/20 hover:bg-emerald-800 hover:-translate-y-0.5 transition-all active:scale-95">
                                                        {{ __('public.create_booking') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        @unless ($showingResults)
        <!-- GALLERY SECTION -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center mb-16">
                <span class="text-sm font-bold uppercase tracking-widest text-emerald-600 mb-3 block">{{ __('public.gallery_subtitle') }}</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-neutral-900 font-serif">{{ __('public.gallery_title') }}</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-5 auto-rows-[200px] sm:auto-rows-[250px]">
                @php
                    $galleryImages = [
                        asset('dafano-media/gallery/halaman/yard-3.jpg'), // Landscape utama
                        asset('dafano-media/gallery/halaman/yard-9.jpg'),
                        asset('dafano-media/gallery/halaman/yard-2.jpg'),
                        asset('dafano-media/gallery/halaman/yard-4.jpg'),
                        asset('dafano-media/gallery/halaman/yard-1.jpg'),
                        asset('dafano-media/gallery/halaman/yard-7.jpg'),
                        asset('dafano-media/gallery/kamar/room-2.jpg'),
                    ];
                @endphp
                @foreach($galleryImages as $index => $img)
                    <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden group cursor-pointer shadow-sm hover:shadow-xl transition-all duration-300 {{ $index === 0 ? 'col-span-2 row-span-2' : '' }}">
                        <img src="{{ $img }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </div>
                @endforeach
            </div>
        </section>
        @endunless
    </main>

    <!-- Footer Simple -->
    <footer class="bg-neutral-950 py-12 text-center text-white/50 text-sm">
        <div class="max-w-7xl mx-auto px-6">
            <p>&copy; {{ date('Y') }} {{ __('public.brand') }}. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var slides = Array.prototype.slice.call(document.querySelectorAll('.hero-slide'));
            var activeSlide = 0;
            var video = document.getElementById('hero-video');
            var connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            var saveData = connection && connection.saveData;
            var effectiveType = connection && connection.effectiveType;
            var hasGoodConnection = !saveData && (['4g', '5g'].indexOf(effectiveType) !== -1 || (!effectiveType && window.innerWidth >= 768));
            var canUseVideo = video && video.dataset.enabled === '1' && hasGoodConnection;

            if (canUseVideo) {
                video.src = video.dataset.src;
                video.classList.remove('hidden');
                setTimeout(function() { video.classList.replace('opacity-0', 'opacity-100'); }, 50);
                video.play().catch(function () {
                    video.classList.add('hidden');
                    startHeroSlider();
                });
            } else {
                startHeroSlider();
            }

            function startHeroSlider() {
                if (slides.length > 1) {
                    window.setInterval(function () {
                        slides[activeSlide].classList.remove('opacity-100', 'scale-100');
                        slides[activeSlide].classList.add('opacity-0', 'scale-105');
                        activeSlide = (activeSlide + 1) % slides.length;
                        slides[activeSlide].classList.remove('opacity-0', 'scale-105');
                        slides[activeSlide].classList.add('opacity-100', 'scale-100');
                    }, 5000);
                }
            }

            if (window.location.hash && window.location.hash.startsWith('#booking-form-')) {
                window.setTimeout(function () {
                    // Coba cari room-card dulu biar yang disorot keseluruhan cardnya
                    var roomId = window.location.hash.replace('#booking-form-', '');
                    var target = document.getElementById('room-card-' + roomId) || document.querySelector(window.location.hash);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 400);
            } else if (window.location.hash === '#rooms') {
                window.setTimeout(function () {
                    var target = document.getElementById('rooms');
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 300);
            }
            
            // Search form logic
            var topForm = document.getElementById('top-search-form');
            if (topForm) {
                topForm.addEventListener('submit', function(e) {
                    var roomId = document.getElementById('selected_room_input').value;
                    if (roomId) {
                        this.action = '{{ route('public.rooms.index') }}#booking-form-' + roomId;
                    } else {
                        this.action = '{{ route('public.rooms.index') }}#rooms';
                    }
                });
            }

            initializeDateRangeCalendars();

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.method.toLowerCase() !== 'post') {
                        return;
                    }

                    var calendar = form.querySelector('[data-date-range-calendar]');
                    if (! calendar) {
                        return;
                    }

                    var checkIn = calendar.querySelector('[data-check-in-input]');
                    var checkOut = calendar.querySelector('[data-check-out-input]');
                    if (! checkIn.value || ! checkOut.value) {
                        event.preventDefault();
                        openCalendarPanel(calendar);
                        calendar.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        calendar.classList.add('ring-4', 'ring-amber-300/70', 'rounded-[1.4rem]');
                        window.setTimeout(function () {
                            calendar.classList.remove('ring-4', 'ring-amber-300/70', 'rounded-[1.4rem]');
                        }, 1400);
                    }
                });
            });
        });

        function initializeDateRangeCalendars() {
            document.querySelectorAll('[data-date-range-calendar]').forEach(function (calendar) {
                if (calendar.dataset.ready === '1') {
                    return;
                }

                calendar.dataset.ready = '1';

                var locale = document.documentElement.lang || 'id-ID';
                var minDate = parseLocalDate(calendar.dataset.minDate) || new Date();
                var today = startOfDay(new Date());
                var checkInInput = calendar.querySelector('[data-check-in-input]');
                var checkOutInput = calendar.querySelector('[data-check-out-input]');
                var checkInDisplay = calendar.querySelector('[data-check-in-display]');
                var checkOutDisplay = calendar.querySelector('[data-check-out-display]');
                var monthLabel = calendar.querySelector('[data-calendar-month]');
                var nightsLabel = calendar.querySelector('[data-calendar-nights]');
                var weekdayGrid = calendar.querySelector('[data-calendar-weekdays]');
                var daysGrid = calendar.querySelector('[data-calendar-days]');
                var prevButton = calendar.querySelector('[data-calendar-prev]');
                var nextButton = calendar.querySelector('[data-calendar-next]');
                var toggleButton = calendar.querySelector('[data-calendar-toggle]');
                var closeButton = calendar.querySelector('[data-calendar-close]');
                var backdropButton = calendar.querySelector('[data-calendar-backdrop]');
                var viewDate = firstDayOfMonth(parseLocalDate(checkInInput.value) || minDate || today);

                buildWeekdays(weekdayGrid, locale);

                if (toggleButton) {
                    toggleButton.addEventListener('click', function () {
                        toggleCalendarPanel(calendar);
                    });
                }

                if (closeButton) {
                    closeButton.addEventListener('click', function () {
                        closeCalendarPanel(calendar);
                    });
                }

                if (backdropButton) {
                    backdropButton.addEventListener('click', function () {
                        closeCalendarPanel(calendar);
                    });
                }

                prevButton.addEventListener('click', function () {
                    viewDate = addMonths(viewDate, -1);
                    renderCalendar();
                });

                nextButton.addEventListener('click', function () {
                    viewDate = addMonths(viewDate, 1);
                    renderCalendar();
                });

                renderCalendar();

                function renderCalendar() {
                    var selectedStart = parseLocalDate(checkInInput.value);
                    var selectedEnd = parseLocalDate(checkOutInput.value);
                    var monthStart = firstDayOfMonth(viewDate);
                    var monthEnd = new Date(monthStart.getFullYear(), monthStart.getMonth() + 1, 0);
                    var leading = (monthStart.getDay() + 6) % 7;
                    var firstCell = addDays(monthStart, -leading);
                    var minMonth = firstDayOfMonth(minDate);
                    var totalCells = Math.ceil((leading + monthEnd.getDate()) / 7) * 7;

                    monthLabel.textContent = monthStart.toLocaleDateString(locale, { month: 'long', year: 'numeric' });
                    prevButton.disabled = monthStart <= minMonth;
                    daysGrid.innerHTML = '';

                    for (var i = 0; i < totalCells; i++) {
                        var date = addDays(firstCell, i);
                        var isCurrentMonth = date.getMonth() === monthStart.getMonth();

                        if (! isCurrentMonth) {
                            var emptyCell = document.createElement('span');
                            emptyCell.className = 'calendar-day is-empty';
                            emptyCell.setAttribute('aria-hidden', 'true');
                            daysGrid.appendChild(emptyCell);

                            continue;
                        }

                        var button = document.createElement('button');
                        var dateValue = formatDate(date);
                        var isDisabled = date < minDate;
                        var isToday = isSameDay(date, today);
                        var isStart = selectedStart && isSameDay(date, selectedStart);
                        var isEnd = selectedEnd && isSameDay(date, selectedEnd);
                        var isBetween = selectedStart && selectedEnd && date > selectedStart && date < selectedEnd;

                        button.type = 'button';
                        button.textContent = date.getDate();
                        button.dataset.date = dateValue;
                        button.className = 'calendar-day';
                        button.setAttribute('aria-label', date.toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }));

                        if (isDisabled) {
                            button.disabled = true;
                            button.classList.add('is-disabled');
                        }

                        if (isToday) {
                            button.classList.add('is-today');
                            button.setAttribute('title', calendar.dataset.todayLabel);
                        }

                        if (isBetween) {
                            button.classList.add('is-in-range');
                        }

                        if (isStart) {
                            button.classList.add('is-selected');
                            button.setAttribute('title', calendar.dataset.checkInLabel);
                        }

                        if (isEnd) {
                            button.classList.add('is-checkout');
                            button.setAttribute('title', calendar.dataset.checkOutLabel);
                        }

                        button.addEventListener('click', function () {
                            selectDate(parseLocalDate(this.dataset.date));
                        });

                        daysGrid.appendChild(button);
                    }

                    checkInDisplay.textContent = selectedStart ? formatDisplayDate(selectedStart, locale) : calendar.dataset.emptyLabel;
                    checkOutDisplay.textContent = selectedEnd ? formatDisplayDate(selectedEnd, locale) : calendar.dataset.emptyLabel;

                    if (selectedStart && selectedEnd) {
                        var nights = Math.max(1, Math.round((selectedEnd - selectedStart) / 86400000));
                        nightsLabel.textContent = nights + ' ' + (nights === 1 ? calendar.dataset.nightSingular : calendar.dataset.nightPlural);
                    } else {
                        nightsLabel.textContent = calendar.dataset.emptyLabel;
                    }
                }

                function selectDate(date) {
                    var selectedStart = parseLocalDate(checkInInput.value);
                    var selectedEnd = parseLocalDate(checkOutInput.value);

                    if (! selectedStart || selectedEnd || date <= selectedStart) {
                        checkInInput.value = formatDate(date);
                        checkOutInput.value = '';
                    } else {
                        checkOutInput.value = formatDate(date);
                        window.setTimeout(function () {
                            closeCalendarPanel(calendar);
                        }, 220);
                    }

                    checkInInput.dispatchEvent(new Event('change', { bubbles: true }));
                    checkOutInput.dispatchEvent(new Event('change', { bubbles: true }));
                    renderCalendar();
                }
            });
        }

        function toggleCalendarPanel(calendar) {
            var panel = calendar.querySelector('[data-calendar-panel]');
            if (! panel) {
                return;
            }

            if (panel.classList.contains('hidden')) {
                openCalendarPanel(calendar);
            } else {
                closeCalendarPanel(calendar);
            }
        }

        function openCalendarPanel(calendar) {
            var panel = calendar.querySelector('[data-calendar-panel]');
            var backdrop = calendar.querySelector('[data-calendar-backdrop]');
            if (panel) {
                panel.classList.remove('hidden');
            }
            if (backdrop) {
                backdrop.classList.remove('hidden');
            }
        }

        function closeCalendarPanel(calendar) {
            var panel = calendar.querySelector('[data-calendar-panel]');
            var backdrop = calendar.querySelector('[data-calendar-backdrop]');
            if (panel) {
                panel.classList.add('hidden');
            }
            if (backdrop) {
                backdrop.classList.add('hidden');
            }
        }

        function buildWeekdays(container, locale) {
            var baseMonday = new Date(2026, 0, 5);
            container.innerHTML = '';
            for (var i = 0; i < 7; i++) {
                var item = document.createElement('span');
                item.textContent = addDays(baseMonday, i).toLocaleDateString(locale, { weekday: 'short' });
                container.appendChild(item);
            }
        }

        function parseLocalDate(value) {
            if (! value) {
                return null;
            }

            var parts = value.split('-').map(Number);
            if (parts.length !== 3 || parts.some(function (part) { return Number.isNaN(part); })) {
                return null;
            }

            return startOfDay(new Date(parts[0], parts[1] - 1, parts[2]));
        }

        function formatDate(date) {
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');

            return date.getFullYear() + '-' + month + '-' + day;
        }

        function formatDisplayDate(date, locale) {
            return date.toLocaleDateString(locale, { weekday: 'short', day: 'numeric', month: 'short' });
        }

        function startOfDay(date) {
            return new Date(date.getFullYear(), date.getMonth(), date.getDate());
        }

        function firstDayOfMonth(date) {
            return new Date(date.getFullYear(), date.getMonth(), 1);
        }

        function addDays(date, amount) {
            return new Date(date.getFullYear(), date.getMonth(), date.getDate() + amount);
        }

        function addMonths(date, amount) {
            return new Date(date.getFullYear(), date.getMonth() + amount, 1);
        }

        function isSameDay(left, right) {
            return left.getFullYear() === right.getFullYear()
                && left.getMonth() === right.getMonth()
                && left.getDate() === right.getDate();
        }
        
        // Expose function to window for the button onclick
        window.selectRoomAndScroll = function(roomId, roomName) {
            document.getElementById('selected_room_input').value = roomId;
            var btnText = document.getElementById('search-btn-text');
            if (btnText) {
                btnText.innerText = 'Lanjut Pesan';
            }
            document.getElementById('search-form').scrollIntoView({behavior: 'smooth', block: 'center'});
        };

        window.showRoomBookingForm = function(roomId) {
            var form = document.getElementById('booking-form-' + roomId);
            var button = document.querySelector('[data-book-room-button="' + roomId + '"]');

            if (form) {
                form.classList.remove('hidden');
                form.scrollIntoView({behavior: 'smooth', block: 'center'});
            }

            if (button) {
                button.classList.add('hidden');
            }
        };
    </script>
</body>
</html>
