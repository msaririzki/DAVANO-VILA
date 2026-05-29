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
            /* Hari ini tidak ditandai lingkaran sesuai permintaan */
            font-weight: 700;
        }
        .calendar-day.is-in-range {
            background: rgb(209 250 229);
            color: rgb(6 78 59);
        }
        .calendar-day.is-selected,
        .calendar-day.is-checkout {
            background: rgb(4 120 87); /* emerald-700 */
            color: white;
            box-shadow: 0 16px 28px -18px rgba(4, 120, 87, 0.75);
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
        $roomDescriptionTranslations = __('public.room_descriptions');
        $roomDescriptionTranslations = is_array($roomDescriptionTranslations) ? $roomDescriptionTranslations : [];
        $facilityTranslations = __('public.facility_labels');
        $facilityTranslations = is_array($facilityTranslations) ? $facilityTranslations : [];
        $showingResults = request()->routeIs('public.rooms.index') && $checkIn && $checkOut;
        $selectedRoomId = request('selected_room');
    @endphp

    <main>
        <!-- HERO SECTION -->
        <section class="relative overflow-hidden bg-neutral-950 {{ $showingResults ? 'h-[40vh] sm:h-[50vh]' : 'min-h-[65vh] sm:min-h-[80vh] lg:min-h-[85vh]' }} flex flex-col">
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
                <header class="relative z-50 flex items-center justify-between py-6 animate-slide-down">
                    <a href="{{ route('public.home') }}" class="flex items-center gap-2 sm:gap-3 rounded-full border border-white/15 bg-black/20 py-1 sm:py-1.5 pl-1 sm:pl-1.5 pr-3 sm:pr-4 text-white shadow-lg backdrop-blur-md transition hover:bg-black/30">
                        <img src="{{ asset('dafano-media/brand/logo-dafano-villa.jpg') }}" alt="{{ __('public.brand') }}" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full object-cover ring-2 ring-white/25">
                        <span class="text-base sm:text-lg font-black tracking-tight drop-shadow">{{ __('public.brand') }}</span>
                    </a>
                    <div class="flex items-center gap-4">
                        @include('public.partials.language-switcher')
                        <a href="{{ route('login') }}" aria-label="{{ __('public.staff') }}" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 backdrop-blur-md px-2.5 py-1.5 sm:px-5 sm:py-2.5 text-sm font-semibold text-white transition hover:bg-white/20 hover:border-white/30 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <span class="hidden sm:inline">{{ __('public.staff') }}</span>
                        </a>
                    </div>
                </header>

                @unless ($showingResults)
                    <!-- Hero Content -->
                    <div class="flex-1 flex flex-col justify-center max-w-3xl pb-20 lg:pb-32">
                        <div class="animate-fade-in-up delay-100 mb-4 sm:mb-6">
                            <span class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1 sm:py-1.5 text-[0.6rem] sm:text-[0.65rem] font-bold tracking-[0.2em] uppercase text-white border border-white/40 bg-black/20 rounded-full backdrop-blur-md shadow-sm">
                                <span class="relative h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="relative">{{ __('public.hero_eyebrow') }}</span>
                            </span>
                        </div>
                        <h1 class="animate-fade-in-up delay-200 text-3xl font-black leading-[1.1] text-white sm:text-5xl md:text-6xl lg:text-7xl drop-shadow-lg mb-3 sm:mb-5 tracking-tight">
                            {{ __('public.brand') }}
                        </h1>
                        <p class="animate-fade-in-up delay-300 text-sm leading-relaxed text-neutral-200 sm:text-lg md:text-xl drop-shadow-md max-w-2xl font-medium">
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
                    
                    <div class="relative w-full flex-1 rounded-[1.4rem] border border-neutral-200 bg-neutral-50/80 sm:rounded-full grid grid-cols-2">
                        @include('public.partials.date-picker', [
                            'calendarId' => 'top-check-in-picker',
                            'name' => 'check_in_date',
                            'label' => __('public.check_in'),
                            'hint' => 'Kapan Anda akan tiba?',
                            'value' => $checkIn,
                            'collapsible' => true,
                            'panelMode' => 'modal',
                        ])
                        @include('public.partials.date-picker', [
                            'calendarId' => 'top-check-out-picker',
                            'name' => 'check_out_date',
                            'label' => __('public.check_out'),
                            'hint' => 'Kapan Anda akan pulang?',
                            'value' => $checkOut,
                            'collapsible' => true,
                            'panelMode' => 'modal',
                            'isEndNode' => true,
                        ])
                    </div>
                    
                    <button type="submit" class="flex min-h-[3.75rem] w-full shrink-0 items-center justify-center gap-2 rounded-[1.35rem] bg-emerald-700 px-8 py-3.5 text-[0.95rem] font-bold text-white shadow-[0_18px_32px_-20px_rgba(4,120,87,0.75)] transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-800 active:translate-y-0 sm:min-h-[4.35rem] sm:w-auto sm:rounded-full sm:px-10">
                        <span id="search-btn-text">{{ request('selected_room') ? __('public.continue_booking') : __('public.search_rooms') }}</span>
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
            <h2 class="text-2xl sm:text-4xl lg:text-5xl font-bold text-emerald-950 mb-8 font-serif leading-tight">{{ __('public.about_title') }}</h2>
            <p class="text-lg sm:text-xl text-neutral-600 leading-relaxed max-w-3xl mx-auto">{{ __('public.about_body') }}</p>
        </section>
        @endunless

        <!-- ROOMS SECTION -->
        <section id="rooms" class="scroll-mt-10 py-20 {{ $showingResults ? '' : 'bg-emerald-50/40' }}">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-6">
                    <div>
                        <span class="text-sm font-bold uppercase tracking-widest text-emerald-600 mb-3 block">{{ __('public.reservation') }}</span>
                        <h2 class="text-2xl sm:text-4xl font-bold text-neutral-900">{{ __('public.rooms_title') }}</h2>
                        <p class="mt-3 text-neutral-600 max-w-lg text-lg">{{ __('public.choose_available_room') }}</p>
                    </div>
                    @if ($showingResults)
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 max-w-sm text-sm text-amber-900 shadow-sm">
                            <p class="font-medium">{{ __('public.cashless_note') }}</p>
                        </div>
                    @endif
                </div>

                @if ($rooms->isEmpty())
                    <div class="text-center py-20 bg-white rounded-3xl border border-neutral-200 shadow-sm">
                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 mb-4 text-neutral-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v11"/><path d="M21 7v11"/><path d="M6 11h12"/><path d="M6 7h12a3 3 0 0 1 3 3v1H3v-1a3 3 0 0 1 3-3Z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-neutral-900 mb-2">{{ __('public.no_rooms_title') }}</h3>
                        <p class="text-neutral-500">{{ __('public.no_rooms') }}</p>
                    </div>
                @else
                    <!-- Carousel / Grid -->
                    @if ($rooms->count() <= 2)
                        <div class="grid grid-cols-1 items-start sm:grid-cols-2 gap-6 lg:gap-8 pt-4 pb-12">
                    @else
                        <div class="flex items-start overflow-x-auto snap-x snap-mandatory gap-6 pb-12 pt-4 hide-scrollbar">
                    @endif
                        @foreach ($rooms as $room)
                            @php
                                $nights = max(1, \Carbon\Carbon::parse($checkIn ?: now())->diffInDays(\Carbon\Carbon::parse($checkOut ?: now()->addDay())));
                                $grandTotal = $room->price * $nights;
                                $minDp = $grandTotal * ($minDpPercent / 100);
                                $image = $room->imageUrl() ?: $roomImages[$loop->index % count($roomImages)];

                                $roomType = null;
                                $roomSpecificImages = [];
                                if (stripos($room->name, 'commercial') !== false) {
                                    $roomType = 'commercial';
                                    $roomSpecificImages = [
                                        asset('dafano-media/gallery/kamar/commercial/foto1.jpg'),
                                        asset('dafano-media/gallery/kamar/commercial/foto2.jpg'),
                                        asset('dafano-media/gallery/kamar/commercial/foto3.jpg'),
                                        asset('dafano-media/gallery/kamar/commercial/foto4.jpg'),
                                        asset('dafano-media/gallery/kamar/commercial/foto5.png'),
                                    ];
                                } elseif (stripos($room->name, 'superior') !== false) {
                                    $roomType = 'superior';
                                    $roomSpecificImages = [
                                        asset('dafano-media/gallery/kamar/superior/foto1.jpg'),
                                        asset('dafano-media/gallery/kamar/superior/foto2.jpg'),
                                        asset('dafano-media/gallery/kamar/superior/foto3.jpg'),
                                        asset('dafano-media/gallery/kamar/superior/foto4.jpeg'),
                                    ];
                                } else {
                                    $roomSpecificImages = [ $image ];
                                }
                                $localizedRoomDescription = $roomType
                                    ? ($roomDescriptionTranslations[$roomType] ?? $room->description)
                                    : $room->description;
                            @endphp

                            @if ($rooms->count() <= 2)
                                <article id="room-card-{{ $room->id }}" class="w-full min-w-0 flex flex-col bg-white rounded-[2rem] border border-neutral-100 shadow-xl shadow-neutral-200/50 overflow-hidden group transition-all duration-700 {{ request('selected_room') && request('selected_room') != $room->id ? 'opacity-60 grayscale-[50%] scale-[0.98]' : (request('selected_room') == $room->id ? 'ring-4 ring-emerald-500/40 shadow-2xl shadow-emerald-900/20 scale-[1.01]' : 'hover:shadow-2xl hover:-translate-y-1') }}">
                            @else
                                <article id="room-card-{{ $room->id }}" class="snap-center shrink-0 w-[90vw] md:w-[400px] flex flex-col bg-white rounded-[2rem] border border-neutral-100 shadow-xl shadow-neutral-200/50 overflow-hidden group transition-all duration-700 {{ request('selected_room') && request('selected_room') != $room->id ? 'opacity-60 grayscale-[50%] scale-[0.98]' : (request('selected_room') == $room->id ? 'ring-4 ring-emerald-500/40 shadow-2xl shadow-emerald-900/20 scale-[1.01]' : 'hover:shadow-2xl hover:-translate-y-1') }}">
                            @endif
                                <!-- Image Header (Carousel) -->
                                <div class="relative h-64 sm:h-80 overflow-hidden bg-neutral-100 group room-carousel-container">
                                    <script>
                                        window.lightboxData = window.lightboxData || {};
                                        window.lightboxData['room-{{ $room->id }}'] = @json($roomSpecificImages);
                                    </script>
                                    <div class="flex h-full w-full overflow-x-auto snap-x snap-mandatory hide-scrollbar room-carousel">
                                        @foreach($roomSpecificImages as $idx => $img)
                                            <div class="w-full h-full shrink-0 snap-center relative">
                                                <img src="{{ $img }}" alt="{{ $room->name }}" class="w-full h-full object-cover transition-transform duration-700 hover:scale-105 cursor-zoom-in" onclick="openLightbox('room-{{ $room->id }}', {{ $idx }})">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-90 pointer-events-none"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Desktop Navigation arrows -->
                                    @if(count($roomSpecificImages) > 1)
                                    <button type="button" aria-label="{{ __('public.previous_image') }}" class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-neutral-800 shadow-md flex items-center justify-center opacity-0 sm:-translate-x-4 sm:group-hover:opacity-100 sm:group-hover:translate-x-0 transition-all duration-300 z-10 prev-btn hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                                    </button>
                                    <button type="button" aria-label="{{ __('public.next_image') }}" class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-neutral-800 shadow-md flex items-center justify-center opacity-0 sm:translate-x-4 sm:group-hover:opacity-100 sm:group-hover:translate-x-0 transition-all duration-300 z-10 next-btn hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                                    </button>
                                    @endif
                                    
                                    <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end pointer-events-none z-10">
                                        <div class="flex flex-col gap-1">
                                            <h3 class="text-2xl font-black tracking-tight text-white drop-shadow-md">{{ $room->name }}</h3>
                                            <span class="flex items-center gap-1.5 text-emerald-100 text-[0.75rem] font-bold tracking-wide">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-amber-400"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" /></svg>
                                                {{ __('public.recommended') }}
                                            </span>
                                        </div>
                                        <span class="flex items-center gap-1.5 bg-white/20 border border-white/30 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" /></svg>
                                            {{ __('public.guest_count', ['count' => $room->capacity]) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Body -->
                                <div class="p-6 sm:p-8 flex-1 flex flex-col">
                                    <p class="text-neutral-500 mb-5 sm:mb-6 leading-relaxed line-clamp-3 text-sm sm:text-base">{{ $localizedRoomDescription }}</p>
                                    
                                    @if ($room->facilities)
                                        <div class="mb-5 sm:mb-6 bg-emerald-50/40 p-4 rounded-2xl border border-emerald-100/50">
                                            <p class="text-[0.65rem] font-bold uppercase tracking-widest text-emerald-800/70 mb-3 flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                                                {{ __('public.facilities') }}
                                            </p>
                                            <div class="flex flex-wrap gap-1.5 sm:gap-2">
                                                @foreach ($room->facilities as $facility)
                                                    <span class="inline-flex items-center bg-white text-emerald-700 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg sm:rounded-xl text-[0.65rem] sm:text-xs font-semibold border border-emerald-100 shadow-sm transition hover:bg-emerald-50 hover:-translate-y-0.5">{{ $facilityTranslations[$facility] ?? $facility }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Price & Booking -->
                                    <div class="mt-auto">
                                        <div class="bg-gradient-to-br from-emerald-50/80 to-emerald-100/30 rounded-2xl p-4 sm:p-5 mb-5 sm:mb-6 border border-emerald-100 shadow-inner">
                                            <div class="flex justify-between items-center">
                                                <div class="flex flex-col">
                                                    <span class="text-[0.65rem] sm:text-xs text-emerald-800/70 font-bold uppercase tracking-wider mb-0.5">{{ __('public.stay_price') }}</span>
                                                    <span class="text-lg sm:text-2xl font-black text-emerald-900 tracking-tight">Rp {{ number_format($room->price, 0, ',', '.') }}</span>
                                                </div>
                                                <span class="text-[0.65rem] sm:text-xs text-emerald-700/80 font-bold uppercase tracking-wider bg-emerald-100/50 px-2.5 py-1.5 rounded-lg border border-emerald-200/50">{{ __('public.per_night') }}</span>
                                            </div>
                                            @if ($showingResults)
                                                <div class="flex justify-between items-center text-sm mt-4 pt-3 border-t border-emerald-200/50">
                                                    <span class="text-emerald-800/70">{{ __('public.duration') }}</span>
                                                    <span class="font-bold text-emerald-900">{{ trans_choice('public.night_count', $nights, ['count' => $nights]) }}</span>
                                                </div>
                                                <div class="flex justify-between items-center text-sm mt-2 pt-2 border-t border-emerald-200/50">
                                                    <span class="text-emerald-800/70">{{ __('public.min_dp') }}</span>
                                                    <span class="font-bold text-amber-600">Rp {{ number_format($minDp, 0, ',', '.') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        @unless ($showingResults)
                                            <button type="button" onclick="showRoomBookingForm('{{ $room->id }}')" data-book-room-button="{{ $room->id }}" class="w-full flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-emerald-900/20 hover:bg-emerald-800 hover:-translate-y-1 transition-all duration-300 active:scale-95 {{ $selectedRoomId == $room->id ? 'hidden' : '' }}">
                                                {{ __('public.book_now') }}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M12.97 3.97a.75.75 0 011.06 0l7.5 7.5a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 11-1.06-1.06l6.22-6.22H3a.75.75 0 010-1.5h16.19l-6.22-6.22a.75.75 0 010-1.06z" clip-rule="evenodd" /></svg>
                                            </button>
                                        @endunless

                                        <div id="booking-form-{{ $room->id }}" class="scroll-mt-32 mt-4 pt-6 border-t border-neutral-100 {{ (! $showingResults && $selectedRoomId != $room->id) ? 'hidden' : '' }} {{ $selectedRoomId == $room->id ? 'animate-[fadeIn_0.5s_ease-out]' : '' }}">
                                            <div class="bg-gradient-to-br from-emerald-50/50 to-white rounded-2xl p-5 border border-emerald-100/50 shadow-inner">
                                                <h4 class="text-sm font-bold text-emerald-900 mb-4 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-emerald-600"><path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z" clip-rule="evenodd" /></svg>
                                                    {{ __('public.complete_booking_data') }}
                                                </h4>
                                                <form method="POST" action="{{ route('public.bookings.store') }}" class="grid gap-5" data-booking-calculator data-room-price="{{ (int) $room->price }}">
                                                    @csrf
                                                    <input type="hidden" name="room_id" value="{{ $room->id }}">

                                                    @if ($showingResults)
                                                        <input type="hidden" name="check_in_date" value="{{ $checkIn }}">
                                                        <input type="hidden" name="check_out_date" value="{{ $checkOut }}">
                                                    @else
                                                        <div class="relative z-[60] grid grid-cols-2 rounded-xl border border-neutral-200 bg-white shadow-sm mb-4">
                                                            @include('public.partials.date-picker', [
                                                                'calendarId' => 'room-check-in-picker-'.$room->id,
                                                                'name' => 'check_in_date',
                                                                'label' => __('public.check_in'),
                                                                'hint' => 'Tentukan tanggal masuk',
                                                                'value' => old('check_in_date'),
                                                                'collapsible' => true,
                                                                'panelMode' => 'inline-collapse',
                                                            ])
                                                            @include('public.partials.date-picker', [
                                                                'calendarId' => 'room-check-out-picker-'.$room->id,
                                                                'name' => 'check_out_date',
                                                                'label' => __('public.check_out'),
                                                                'hint' => 'Tentukan tanggal keluar',
                                                                'value' => old('check_out_date'),
                                                                'collapsible' => true,
                                                                'panelMode' => 'inline-collapse',
                                                                'isEndNode' => true,
                                                            ])
                                                        </div>
                                                    @endif
                                                    


                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">{{ __('public.guest_name') }}</label>
                                                            <input name="guest_name" value="{{ old('guest_name') }}" required placeholder="{{ __('public.guest_name_placeholder') }}" class="w-full rounded-xl border-neutral-200 bg-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-shadow hover:shadow-md">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-neutral-500 mb-1.5">{{ __('public.guest_phone') }}</label>
                                                            <input name="guest_phone" value="{{ old('guest_phone') }}" required placeholder="0812xxx" class="w-full rounded-xl border-neutral-200 bg-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-shadow hover:shadow-md">
                                                        </div>
                                                    </div>

                                                    <div class="relative z-[55]">
                                                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">{{ __('public.source_label') }}</label>
                                                        <x-custom-select 
                                                            name="acquisition_source" 
                                                            :options="$sourceOptions" 
                                                            placeholder="{{ __('public.source_placeholder') }}"
                                                            selected="{{ old('acquisition_source') }}"
                                                        />
                                                    </div>

                                                    <details class="group rounded-2xl border border-emerald-100 bg-white/80 shadow-sm" @if (old('extra_bed_item_id') || old('guest_request')) open @endif>
                                                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 rounded-2xl px-4 py-3.5 transition hover:bg-emerald-50/70 [&::-webkit-details-marker]:hidden">
                                                            <span class="min-w-0">
                                                                <span class="block text-sm font-black text-emerald-950">{{ __('public.additional_requests_trigger') }}</span>
                                                                <span class="mt-0.5 block text-xs font-semibold leading-5 text-neutral-500">{{ __('public.additional_requests_short_hint') }}</span>
                                                            </span>
                                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 transition group-open:rotate-45 group-open:bg-emerald-700 group-open:text-white">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                                                            </span>
                                                        </summary>

                                                        <div class="grid gap-5 border-t border-emerald-100/60 px-5 py-5">
                                                            <p class="text-[0.75rem] font-medium leading-relaxed text-neutral-500">{{ __('public.additional_requests_hint') }}</p>

                                                            @if ($extraBedItems->isNotEmpty())
                                                                <div class="flex items-start gap-3 w-full">
                                                                    <div class="flex-1 min-w-0">
                                                                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">{{ __('public.extra_bed_label') }}</label>
                                                                        @php
                                                                            $extraBedOptions = $extraBedItems
                                                                                ->mapWithKeys(fn ($extraBedItem) => [
                                                                                    (string) $extraBedItem->id => $extraBedItem->name.' - Rp '.number_format($extraBedItem->price, 0, ',', '.'),
                                                                                ])
                                                                                ->all();
                                                                            $extraBedOptionAttributes = $extraBedItems
                                                                                ->mapWithKeys(fn ($extraBedItem) => [
                                                                                    (string) $extraBedItem->id => ['data-price' => (int) $extraBedItem->price],
                                                                                ])
                                                                                ->all();
                                                                        @endphp
                                                                        <x-custom-select
                                                                            name="extra_bed_item_id"
                                                                            :options="$extraBedOptions"
                                                                            :option-attributes="$extraBedOptionAttributes"
                                                                            placeholder="{{ __('public.no_extra_bed') }}"
                                                                            selected="{{ old('extra_bed_item_id') }}"
                                                                        />
                                                                    </div>
                                                                    <div class="w-32 shrink-0">
                                                                        <label class="block text-xs font-bold text-neutral-500 mb-1.5">{{ __('public.qty') }}</label>
                                                                        <div class="flex items-center rounded-xl border border-neutral-200 bg-white overflow-hidden shadow-sm focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500 transition-shadow">
                                                                            <button type="button" class="px-2.5 py-3 text-neutral-400 hover:text-emerald-700 hover:bg-emerald-50 transition-colors focus:outline-none" onclick="var input = this.nextElementSibling; if(input.value > 1) { input.value--; input.dispatchEvent(new Event('input', {bubbles: true})); }">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                                                            </button>
                                                                            <input name="extra_bed_qty" type="number" min="1" max="10" value="{{ old('extra_bed_qty', 1) }}" class="w-full text-center appearance-none border-none bg-transparent p-0 text-sm font-semibold focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none" style="-moz-appearance: textfield;">
                                                                            <button type="button" class="px-2.5 py-3 text-neutral-400 hover:text-emerald-700 hover:bg-emerald-50 transition-colors focus:outline-none" onclick="var input = this.previousElementSibling; if(input.value < 10) { input.value++; input.dispatchEvent(new Event('input', {bubbles: true})); }">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="flex items-start gap-2.5 rounded-xl bg-emerald-50/80 px-3.5 py-2.5 text-emerald-800 ring-1 ring-inset ring-emerald-100/50">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    <p class="text-[0.72rem] font-semibold leading-relaxed">{{ __('public.extra_bed_note') }}</p>
                                                                </div>
                                                            @endif

                                                            <div>
                                                                <label class="block text-xs font-bold text-neutral-500 mb-1.5">{{ __('public.guest_request_label') }}</label>
                                                                <textarea name="guest_request" rows="3" maxlength="1000" placeholder="{{ __('public.guest_request_placeholder') }}" class="w-full rounded-xl border-neutral-200 bg-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-shadow hover:shadow-md">{{ old('guest_request') }}</textarea>
                                                            </div>
                                                        </div>
                                                    </details>



                                                    <div class="relative mt-2 mb-4 rounded-2xl bg-emerald-800 p-1.5 shadow-lg shadow-emerald-900/20">
                                                        <div class="rounded-xl bg-emerald-900/40 p-4">
                                                            <div data-booking-details class="hidden animate-[fadeIn_0.4s_ease-out] mb-4 pb-4 border-b border-dashed border-emerald-500/50 space-y-2.5 text-sm">
                                                                <div class="flex items-center justify-between text-emerald-100">
                                                                    <span>{{ __('public.duration') }}</span>
                                                                    <span class="font-semibold text-white" data-booking-nights>{{ trans_choice('public.night_count', $nights, ['count' => $nights]) }}</span>
                                                                </div>
                                                                <div class="flex items-center justify-between text-emerald-100">
                                                                    <span>{{ __('public.room') }}</span>
                                                                    <span class="font-semibold text-white" data-room-subtotal>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                                                </div>
                                                                <div class="hidden items-center justify-between text-emerald-100" data-extra-bed-row>
                                                                    <span>{{ __('public.extra_bed_label') }} <span data-extra-bed-qty-label class="text-emerald-300 text-xs ml-1"></span></span>
                                                                    <span class="font-semibold text-white" data-extra-bed-subtotal>Rp 0</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="flex items-center justify-between text-white">
                                                                <div>
                                                                    <span class="text-xs font-medium text-emerald-200 uppercase tracking-wider block mb-1">{{ __('public.total_bill') }}</span>
                                                                    <strong class="text-2xl font-bold drop-shadow-sm block" data-booking-total>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                                                                </div>
                                                                <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-emerald-100"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                                                                </div>
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
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 bg-white">
            <div class="max-w-3xl mx-auto text-center mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold uppercase tracking-widest mb-6 border border-emerald-100/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                    {{ __('public.gallery_badge') }}
                </div>
                <h2 class="text-4xl sm:text-5xl font-black text-neutral-900 tracking-tight mb-6">{{ __('public.gallery_heading_prefix') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">{{ __('public.gallery_heading_highlight') }}</span> {{ __('public.gallery_heading_suffix') }}</h2>
                <p class="text-lg text-neutral-500 font-medium leading-relaxed">{{ __('public.gallery_body') }}</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 md:gap-5 auto-rows-[180px] sm:auto-rows-[220px] md:auto-rows-[260px]">
                @php
                    $galleryImages = [
                        asset('dafano-media/gallery/halaman/yard-3.jpg'), // Landscape utama
                        asset('dafano-media/gallery/halaman/yard-9.jpg'),
                        asset('dafano-media/gallery/halaman/yard-2.jpg'),
                        asset('dafano-media/gallery/halaman/yard-4.jpg'),
                        asset('dafano-media/gallery/halaman/yard-1.jpg'),
                        asset('dafano-media/gallery/halaman/yard-7.jpg'),
                        asset('dafano-media/video/vidio.mp4'), // Diganti ke video agar tidak kosong
                    ];
                    
                    // Bento box layout classes
                    $gridClasses = [
                        'col-span-2 row-span-2', // Image 1: Besar di kiri
                        'col-span-1 row-span-1', // Image 2: Kecil
                        'col-span-1 row-span-1', // Image 3: Kecil
                        'col-span-2 row-span-1', // Image 4: Persegi panjang melebar
                        'col-span-1 row-span-1', // Image 5: Kecil
                        'col-span-1 row-span-1', // Image 6: Kecil
                        'col-span-2 row-span-1', // Image 7: Persegi panjang melebar (Video)
                    ];
                @endphp
                <script>
                    window.lightboxData = window.lightboxData || {};
                    window.lightboxData['main-gallery'] = @json($galleryImages);
                </script>
                @foreach($galleryImages as $index => $media)
                    @php
                        $isVideo = str_ends_with($media, '.mp4');
                    @endphp
                    <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden group cursor-zoom-in shadow-sm hover:shadow-2xl transition-all duration-500 {{ $gridClasses[$index] ?? 'col-span-1 row-span-1' }}" onclick="openLightbox('main-gallery', {{ $index }})">
                        @if($isVideo)
                            <video src="{{ $media }}" poster="{{ asset('dafano-media/gallery/halaman/yard-3.jpg') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" muted loop playsinline preload="none" onmouseover="this.play()" onmouseout="this.pause()"></video>
                            <!-- Play Icon Overlay -->
                            <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-md rounded-full p-2 text-white shadow-lg pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd" /></svg>
                            </div>
                        @else
                            <img src="{{ $media }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @endif
                        <!-- Overlay gelap saat di-hover -->
                        <div class="absolute inset-0 bg-neutral-900/0 group-hover:bg-neutral-900/30 transition-colors duration-500 pointer-events-none"></div>
                        <!-- Ikon Expand saat di-hover -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500 scale-50 group-hover:scale-100 pointer-events-none">
                            <div class="bg-white/95 backdrop-blur-sm w-12 h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center shadow-2xl text-emerald-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" /></svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endunless

        <!-- LIGHTBOX MODAL -->
        <div id="image-lightbox" class="fixed inset-0 z-50 hidden bg-neutral-900/50 backdrop-blur-sm flex items-center justify-center transition-all duration-300 opacity-0" onclick="closeLightbox()">
            <button type="button" class="absolute top-6 right-6 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 shadow-lg rounded-full p-3 transition-all z-10" onclick="closeLightbox()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <div class="inline-flex flex-col items-center justify-center max-w-[95vw] md:max-w-[85vw] transition-transform duration-300 scale-95" id="lightbox-content-wrapper" onclick="event.stopPropagation()">
                
                <!-- Image Container with relative positioning for arrows -->
                <div class="relative inline-block max-w-full group">
                    <!-- Image & Video Elements -->
                    <img id="lightbox-img" src="" class="block max-w-full max-h-[70vh] w-auto h-auto rounded-[1.25rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)]">
                    <video id="lightbox-video" src="" class="hidden max-w-full max-h-[70vh] w-auto h-auto rounded-[1.25rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)]" controls autoplay loop playsinline></video>
                    
                    <!-- Nav Buttons (Inside Image Area) -->
                    <button type="button" class="absolute left-2 md:left-4 top-1/2 -translate-y-1/2 text-neutral-800 hover:text-emerald-600 bg-white/90 hover:bg-white rounded-full p-2.5 md:p-3 transition-all z-10 shadow-lg border border-neutral-100 opacity-90 group-hover:opacity-100" onclick="lightboxPrev(event)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>
                    <button type="button" class="absolute right-2 md:right-4 top-1/2 -translate-y-1/2 text-neutral-800 hover:text-emerald-600 bg-white/90 hover:bg-white rounded-full p-2.5 md:p-3 transition-all z-10 shadow-lg border border-neutral-100 opacity-90 group-hover:opacity-100" onclick="lightboxNext(event)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </button>
                </div>
                
                <!-- Floating Info Card (Width tied perfectly to Image via w-0) -->
                <div class="w-0 min-w-[95%] md:min-w-[96%] relative -mt-4 md:-mt-5 bg-white p-4 md:p-5 rounded-2xl shadow-xl border border-neutral-100 flex flex-wrap items-center justify-between gap-3 md:gap-4 z-20">
                    <div class="flex-1 min-w-[180px] text-left">
                        <div class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-50 text-emerald-600 rounded-full text-[0.65rem] font-bold tracking-wide mb-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-amber-400"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" /></svg>
                            {{ __('public.exclusive_pick') }}
                        </div>
                        <h4 class="text-base md:text-lg font-black text-neutral-900 mb-0.5 leading-tight line-clamp-1">{{ __('public.lightbox_title') }}</h4>
                        <p class="text-neutral-500 text-[0.7rem] md:text-xs font-medium line-clamp-2">{{ __('public.lightbox_body') }}</p>
                    </div>
                    <button type="button" onclick="closeLightboxAndScroll()" class="flex-grow md:flex-grow-0 shrink-0 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-600/30 transition-all hover:-translate-y-0.5 flex justify-center items-center gap-1.5">
                        {{ __('public.book_room') }}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- FLOATING AUDIO TOGGLE -->
        <button id="audio-toggle-btn" class="fixed bottom-6 right-6 md:bottom-8 md:right-8 z-[60] bg-black/40 hover:bg-black/70 backdrop-blur-md border border-white/20 p-3.5 rounded-full text-white shadow-2xl transition-all duration-300 hover:scale-110 flex items-center justify-center gap-0 group-hover:gap-2 group hidden cursor-pointer">
            <div class="relative flex items-center justify-center w-6 h-6">
                <!-- Muted Icon -->
                <svg id="icon-muted" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 absolute transition-all duration-300 opacity-100 scale-100">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6l4.72-4.72a.75.75 0 011.28.531V19.69a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.506-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.395C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" />
                </svg>
                <!-- Playing Icon -->
                <svg id="icon-playing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 absolute transition-all duration-300 opacity-0 scale-50 text-emerald-400">
<path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                </svg>
            </div>
            <span class="text-xs font-bold tracking-wider uppercase max-w-0 overflow-hidden opacity-0 group-hover:opacity-100 group-hover:max-w-[150px] group-hover:pr-2 transition-all duration-500 whitespace-nowrap text-emerald-50">{{ __('public.turn_on_music') }}</span>
        </button>
    </main>

    <!-- Ultra Modern Footer (Compact) -->
    <footer class="bg-neutral-950 py-10 border-t border-white/5 relative overflow-hidden flex flex-col justify-between">
        <!-- Subtle background glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[300px] bg-emerald-500/10 blur-[150px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 sm:px-8 relative z-10 w-full flex-grow">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- Brand & Description -->
                <div class="text-center lg:text-left space-y-6 lg:col-span-5">
                    <h2 class="text-2xl font-bold text-white tracking-widest uppercase">{{ __('public.brand') }}</h2>
                    <p class="text-neutral-400 text-sm md:text-base leading-relaxed max-w-md mx-auto lg:mx-0">
                        {{ __('public.footer_body') }}
                    </p>
                </div>

                <!-- Social Media Links -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-end gap-6 lg:col-span-7">
                    <!-- WhatsApp (Customer Service) -->
                    <a href="https://wa.me/6281320584705" target="_blank" class="group flex items-center gap-4 px-6 py-4 rounded-3xl bg-white/5 hover:bg-emerald-500/10 border border-white/5 hover:border-emerald-500/30 transition-all duration-500 w-full sm:w-auto shadow-2xl backdrop-blur-md hover:-translate-y-1">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500 shadow-[0_0_20px_rgba(16,185,129,0)] group-hover:shadow-[0_0_20px_rgba(16,185,129,0.4)]">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-[11px] text-neutral-400 font-semibold group-hover:text-emerald-300 transition-colors uppercase tracking-[0.2em] mb-0.5">{{ __('public.need_help') }}</p>
                            <p class="text-base text-white font-bold tracking-wide">{{ __('public.customer_service') }}</p>
                        </div>
                    </a>

                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/villadafanosembalun/" target="_blank" title="@villadafanosembalun" class="w-14 h-14 rounded-2xl bg-white/5 hover:bg-pink-500/10 border border-white/5 hover:border-pink-500/30 flex items-center justify-center text-neutral-400 hover:text-pink-400 transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-xl backdrop-blur-md">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        
                        <!-- TikTok -->
                        <a href="https://www.tiktok.com/@dafanovillasembalun" target="_blank" title="@dafanovillasembalun" class="w-14 h-14 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-white/20 flex items-center justify-center text-neutral-400 hover:text-white transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-xl backdrop-blur-md">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.12-3.44-3.17-3.8-5.46-.4-2.51.34-5.17 1.96-7.01 1.34-1.57 3.32-2.58 5.37-2.82.2-.02.41-.03.61-.03v4.04c-1.34.05-2.67.57-3.66 1.47-1.13.98-1.78 2.5-1.75 4.02.04 1.55.77 3.02 1.98 3.96.96.77 2.21 1.16 3.42 1.05 1.56-.12 2.99-1.02 3.73-2.35.48-.84.73-1.81.71-2.78-.05-5.34-.02-10.68-.03-16.02h.01z"/></svg>
                        </a>
                        
                        <!-- Threads -->
                        <a href="https://www.threads.net/@villadafanosembalun" target="_blank" title="@villadafanosembalun" class="w-14 h-14 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-white/20 flex items-center justify-center text-neutral-400 hover:text-white transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-xl backdrop-blur-md">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 30 30"><path d="M14.613 6.942a12.89 12.89 0 00-2.613-1.066C10.669 5.488 9.296 5.305 7.846 5.3c-2.316.008-4.524.81-6.262 2.27A9.096 9.096 0 00.316 9.873c-.93 1.996-1.282 4.072-1.246 6.182.046 2.66.711 5.097 1.97 7.234A12.553 12.553 0 006.182 28.5a13.313 13.313 0 007.82 2.45c3.554.02 6.84-.962 9.491-2.84 2.802-1.986 4.757-4.838 5.641-8.232a13.155 13.155 0 00.354-2.923c-.02-1.92-.321-3.79-1.178-5.556-1.12-2.308-2.905-4.137-5.143-5.263-2.128-1.065-4.5-1.385-6.86-1.144-1.22.125-2.433.422-3.565.864a9.7 9.7 0 00-2.928 1.83l.896 1.408c1.378-1.298 3.27-2.022 5.166-1.985 2.126.041 4.148.887 5.707 2.385 1.536 1.476 2.432 3.498 2.528 5.614.015.334.02.669.014 1.004-.84-1.206-2.062-2.115-3.486-2.584a8.673 8.673 0 00-2.871-.448c-1.94-.01-3.834.61-5.385 1.761a7.07 7.07 0 00-2.85 5.568c-.015 1.554.55 3.056 1.597 4.239a5.753 5.753 0 004.385 1.97c1.782.025 3.52-.613 4.904-1.8 1.488-1.275 2.422-3.138 2.637-5.244a10.82 10.82 0 01-1.34 3.79 8.647 8.647 0 01-2.992 3.037c-1.376.812-2.936 1.258-4.538 1.293-2.008.043-3.95-.658-5.46-1.97a9.206 9.206 0 01-2.972-5.457c-.503-2.316-.445-4.717.167-7.006A11.752 11.752 0 016.924 9.07c2.096-1.576 4.67-2.441 7.29-2.453 1.73-.008 3.44.331 5.025 1.016 1.25.541 2.378 1.306 3.328 2.257l1.246-1.248zM16.53 17.65c-.292 2.277-1.42 4.364-3.176 5.867a5.556 5.556 0 01-3.665 1.43c-1.312.016-2.576-.46-3.553-1.339-1.027-.923-1.616-2.228-1.656-3.67a5.03 5.03 0 012.083-4.137c1.47-1.054 3.28-1.583 5.093-1.486 1.053.057 2.086.324 3.048.788.948.455 1.8 1.082 2.502 1.848-.198.228-.415.441-.652.635l-.024.064z"/></svg>
                        </a>

                        <!-- Facebook -->
                        <a href="https://www.facebook.com/share/1EKggZ2LNC" target="_blank" title="Dafano Villa Sembalun" class="w-14 h-14 rounded-2xl bg-white/5 hover:bg-blue-500/10 border border-white/5 hover:border-blue-500/30 flex items-center justify-center text-neutral-400 hover:text-blue-400 transition-all duration-300 hover:scale-110 hover:-translate-y-1 shadow-xl backdrop-blur-md">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Divider & Copyright -->
            <div class="mt-12 pt-6 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-neutral-500 text-sm font-medium">&copy; {{ date('Y') }} {{ __('public.brand') }}. {{ __('public.all_rights_reserved') }}</p>
                <div class="flex items-center gap-6 md:gap-8 text-sm font-medium text-neutral-500">
                    <a href="#" class="hover:text-emerald-400 transition-colors">{{ __('public.terms') }}</a>
                    <a href="#" class="hover:text-emerald-400 transition-colors">{{ __('public.privacy') }}</a>
                </div>
            </div>
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

                // Audio Logic
                var audioToggleBtn = document.getElementById('audio-toggle-btn');
                var iconMuted = document.getElementById('icon-muted');
                var iconPlaying = document.getElementById('icon-playing');
                var audioText = audioToggleBtn.querySelector('span');
                var isUserInteracted = false;
                
                if (audioToggleBtn) {
                    audioToggleBtn.classList.remove('hidden');

                    window.updateAudioButtonUI = function(isPlaying) {
                        if (isPlaying) {
                            iconMuted.classList.replace('opacity-100', 'opacity-0');
                            iconMuted.classList.replace('scale-100', 'scale-50');
                            iconPlaying.classList.replace('opacity-0', 'opacity-100');
                            iconPlaying.classList.replace('scale-50', 'scale-100');
                            audioText.innerText = "Matikan Musik";
                            audioToggleBtn.classList.add('border-emerald-500/50');
                        } else {
                            iconPlaying.classList.replace('opacity-100', 'opacity-0');
                            iconPlaying.classList.replace('scale-100', 'scale-50');
                            iconMuted.classList.replace('opacity-0', 'opacity-100');
                            iconMuted.classList.replace('scale-50', 'scale-100');
                            audioText.innerText = @json(__('public.turn_on_music'));
                            audioToggleBtn.classList.remove('border-emerald-500/50');
                        }
                    };

                    window.toggleHeroAudio = function(forcePlay, forceMute) {
                        if (forceMute || (!forcePlay && !video.muted)) {
                            video.muted = true;
                            window.updateAudioButtonUI(false);
                        } else if (forcePlay || video.muted) {
                            video.muted = false;
                            video.volume = 0.5; // Set reasonable volume
                            window.updateAudioButtonUI(true);
                        }
                    };

                    audioToggleBtn.addEventListener('click', function(e) {
                        e.stopPropagation(); // Mencegah memicu klik global
                        isUserInteracted = true;
                        
                        var lightbox = document.getElementById('image-lightbox');
                        var lightboxVideo = document.getElementById('lightbox-video');
                        var isLightboxVideoPlaying = lightbox && !lightbox.classList.contains('hidden') && lightboxVideo && !lightboxVideo.classList.contains('hidden');
                        
                        if (isLightboxVideoPlaying) {
                            // Kontrol suara video di dalam galeri (lightbox)
                            if (lightboxVideo.muted) {
                                lightboxVideo.muted = false;
                                lightboxVideo.volume = 0.5;
                                window.updateAudioButtonUI(true);
                            } else {
                                lightboxVideo.muted = true;
                                window.updateAudioButtonUI(false);
                            }
                        } else {
                            // Kontrol suara video utama
                            toggleHeroAudio();
                        }
                    });

                    // Auto-unmute on first click anywhere on the document
                    document.body.addEventListener('click', function(e) {
                        if (!isUserInteracted && video.muted && e.target !== audioToggleBtn && !audioToggleBtn.contains(e.target)) {
                            isUserInteracted = true;
                            toggleHeroAudio(true);
                            video.play().catch(function(e) { console.log('Audio autoplay prevented', e); });
                        }
                    }, { once: true, capture: true });
                }
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

            initializeDatePickers();

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.method.toLowerCase() !== 'post') {
                        return;
                    }

                    var pickers = form.querySelectorAll('[data-date-picker]');
                    var hasEmpty = false;
                    pickers.forEach(function (picker) {
                        var input = picker.querySelector('[data-picker-input]');
                        if (input && !input.value) {
                            hasEmpty = true;
                            openCalendarPanel(picker);
                            picker.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            picker.classList.add('ring-4', 'ring-amber-300/70', 'rounded-[1.4rem]', 'z-50');
                            window.setTimeout(function () {
                                picker.classList.remove('ring-4', 'ring-amber-300/70', 'rounded-[1.4rem]', 'z-50');
                            }, 1400);
                        }
                    });

                    if (hasEmpty) {
                        event.preventDefault();
                    }
                });
            });
        });

        function initializeDatePickers() {
            document.querySelectorAll('[data-date-picker]').forEach(function (calendar) {
                if (calendar.dataset.ready === '1') {
                    return;
                }

                calendar.dataset.ready = '1';

                var locale = document.documentElement.lang || 'id-ID';
                var today = startOfDay(new Date());
                var minDate = parseLocalDate(calendar.dataset.minDate) || today;
                var input = calendar.querySelector('[data-picker-input]');
                var display = calendar.querySelector('[data-picker-display]');
                var monthLabel = calendar.querySelector('[data-calendar-month]');
                var weekdayGrid = calendar.querySelector('[data-calendar-weekdays]');
                var daysGrid = calendar.querySelector('[data-calendar-days]');
                var prevButton = calendar.querySelector('[data-calendar-prev]');
                var nextButton = calendar.querySelector('[data-calendar-next]');
                var toggleButton = calendar.querySelector('[data-calendar-toggle]');
                var closeButton = calendar.querySelector('[data-calendar-close]');
                var backdropButton = calendar.querySelector('[data-calendar-backdrop]');
                
                var viewDate = firstDayOfMonth(parseLocalDate(input.value) || minDate || today);

                buildWeekdays(weekdayGrid, locale);

                if (toggleButton) {
                    toggleButton.addEventListener('click', function () {
                        // Close other open calendars first
                        document.querySelectorAll('[data-date-picker]').forEach(function(c) {
                            if (c !== calendar) closeCalendarPanel(c);
                        });
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
                
                // Add listener to update its own minDate if it's a check_out_date
                if (input && input.name === 'check_out_date') {
                    document.addEventListener('check_in_date_changed', function(e) {
                        var form = calendar.closest('form') || calendar.closest('div.grid-cols-2').parentElement;
                        var sourceForm = e.target.closest('form') || e.target.closest('div.grid-cols-2').parentElement;
                        
                        if (form === sourceForm) {
                            var newCheckIn = e.detail.date;
                            if (newCheckIn) {
                                minDate = addDays(newCheckIn, 1); // Check-out must be at least 1 day after check-in
                                calendar.dataset.minDate = formatDate(minDate);
                                
                                var currentVal = parseLocalDate(input.value);
                                if (currentVal && currentVal < minDate) {
                                    input.value = ''; // Reset if invalid
                                }
                                viewDate = firstDayOfMonth(parseLocalDate(input.value) || minDate || today);
                                renderCalendar();
                            }
                        }
                    });
                }

                renderCalendar();

                function renderCalendar() {
                    var selectedDate = parseLocalDate(input.value);
                    
                    // Ambil nilai dari input kalender pasangannya (jika ada) untuk visualisasi
                    var otherInput = null;
                    var form = calendar.closest('form') || calendar.closest('div.grid-cols-2').parentElement;
                    if (form) {
                        var targetName = input.name === 'check_in_date' ? 'check_out_date' : 'check_in_date';
                        var otherPicker = form.querySelector('[data-picker-type="' + targetName + '"]');
                        if (otherPicker) {
                            otherInput = otherPicker.querySelector('[data-picker-input]');
                        }
                    }
                    var otherDate = otherInput ? parseLocalDate(otherInput.value) : null;
                    var checkInDate = input.name === 'check_in_date' ? selectedDate : otherDate;
                    var checkOutDate = input.name === 'check_out_date' ? selectedDate : otherDate;

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
                        var isStart = checkInDate && isSameDay(date, checkInDate);
                        var isEnd = checkOutDate && isSameDay(date, checkOutDate);
                        var isBetween = checkInDate && checkOutDate && date > checkInDate && date < checkOutDate;

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
                        }
                        
                        if (isEnd) {
                            button.classList.add('is-checkout');
                        }

                        button.addEventListener('click', function () {
                            selectDate(parseLocalDate(this.dataset.date));
                        });

                        daysGrid.appendChild(button);
                    }

                    display.textContent = selectedDate ? formatDisplayDate(selectedDate, locale) : calendar.dataset.emptyLabel;
                }

                function selectDate(date) {
                    input.value = formatDate(date);
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    if (input.name === 'check_in_date') {
                        input.dispatchEvent(new CustomEvent('check_in_date_changed', { 
                            detail: { date: date },
                            bubbles: true 
                        }));
                        
                        // Kita hilangkan fitur buka otomatis kalender checkout sesuai permintaan
                        // var form = calendar.closest('form') || calendar.closest('div.grid-cols-2').parentElement;
                        // if (form) {
                        //     var checkoutPicker = form.querySelector('[data-picker-type="check_out_date"]');
                        //     if (checkoutPicker) {
                        //         window.setTimeout(function () {
                        //             openCalendarPanel(checkoutPicker);
                        //         }, 250);
                        //     }
                        // }
                    }

                    window.setTimeout(function () {
                        closeCalendarPanel(calendar);
                    }, 220);

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

        function formatRupiah(amount) {
            return 'Rp ' + Math.max(0, Math.round(amount)).toLocaleString('id-ID');
        }

        function formatNightCount(nights) {
            var unit = nights === 1
                ? @json(__('public.night_unit_singular'))
                : @json(__('public.night_unit_plural'));

            return nights + ' ' + unit;
        }

        function calculateNights(checkInValue, checkOutValue) {
            var checkInDate = parseLocalDate(checkInValue);
            var checkOutDate = parseLocalDate(checkOutValue);

            if (! checkInDate || ! checkOutDate || checkOutDate <= checkInDate) {
                return 1;
            }

            return Math.max(1, Math.round((checkOutDate - checkInDate) / 86400000));
        }

        function updateBookingCalculator(form) {
            if (! form) {
                return;
            }

            var roomPrice = Number(form.dataset.roomPrice || 0);
            var checkInInput = form.querySelector('input[name="check_in_date"]');
            var checkOutInput = form.querySelector('input[name="check_out_date"]');
            var extraBedSelect = form.querySelector('select[name="extra_bed_item_id"]');
            var extraBedQtyInput = form.querySelector('input[name="extra_bed_qty"]');
            var nights = calculateNights(checkInInput ? checkInInput.value : '', checkOutInput ? checkOutInput.value : '');
            var roomSubtotal = roomPrice * nights;
            var selectedExtra = extraBedSelect ? extraBedSelect.options[extraBedSelect.selectedIndex] : null;
            var extraBedPrice = selectedExtra && selectedExtra.value ? Number(selectedExtra.dataset.price || 0) : 0;
            var extraBedQty = extraBedPrice > 0 ? Math.max(1, Number(extraBedQtyInput ? extraBedQtyInput.value : 1) || 1) : 0;
            var extraBedSubtotal = extraBedPrice * extraBedQty;
            var total = roomSubtotal + extraBedSubtotal;
            var nightsTarget = form.querySelector('[data-booking-nights]');
            var roomSubtotalTarget = form.querySelector('[data-room-subtotal]');
            var extraBedRow = form.querySelector('[data-extra-bed-row]');
            var extraBedSubtotalTarget = form.querySelector('[data-extra-bed-subtotal]');
            var totalTarget = form.querySelector('[data-booking-total]');
            var detailsContainer = form.querySelector('[data-booking-details]');

            if (detailsContainer && checkInInput && checkOutInput) {
                if (checkInInput.value && checkOutInput.value) {
                    detailsContainer.classList.remove('hidden');
                } else {
                    detailsContainer.classList.add('hidden');
                }
            }

            if (nightsTarget) {
                nightsTarget.textContent = formatNightCount(nights);
            }

            if (roomSubtotalTarget) {
                roomSubtotalTarget.textContent = formatRupiah(roomSubtotal);
            }

            if (extraBedRow && extraBedSubtotalTarget) {
                extraBedSubtotalTarget.textContent = formatRupiah(extraBedSubtotal);
                extraBedRow.classList.toggle('hidden', extraBedSubtotal <= 0);
                extraBedRow.classList.toggle('flex', extraBedSubtotal > 0);
                
                var qtyLabel = form.querySelector('[data-extra-bed-qty-label]');
                if (qtyLabel) {
                    qtyLabel.textContent = extraBedQty > 0 ? '(x' + extraBedQty + ')' : '';
                }
            }

            if (totalTarget) {
                totalTarget.textContent = formatRupiah(total);
            }
        }

        function initBookingCalculators() {
            document.querySelectorAll('[data-booking-calculator]').forEach(function(form) {
                form.addEventListener('change', function(event) {
                    if (event.target.matches('input[name="check_in_date"], input[name="check_out_date"], select[name="extra_bed_item_id"], input[name="extra_bed_qty"]')) {
                        updateBookingCalculator(form);
                    }
                });

                form.addEventListener('input', function(event) {
                    if (event.target.matches('input[name="extra_bed_qty"]')) {
                        updateBookingCalculator(form);
                    }
                });

                updateBookingCalculator(form);
            });
        }
        
        // Expose function to window for the button onclick
        window.selectRoomAndScroll = function(roomId, roomName) {
            document.getElementById('selected_room_input').value = roomId;
            var btnText = document.getElementById('search-btn-text');
            if (btnText) {
                btnText.innerText = @json(__('public.continue_booking'));
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

        // Room Carousel Logic
        document.addEventListener('DOMContentLoaded', function() {
            initBookingCalculators();

            var carousels = document.querySelectorAll('.room-carousel-container');
            carousels.forEach(function(container) {
                var scrollContainer = container.querySelector('.room-carousel');
                var prevBtn = container.querySelector('.prev-btn');
                var nextBtn = container.querySelector('.next-btn');
                
                if (!scrollContainer) return;

                // Handle Prev/Next Buttons
                if (prevBtn && nextBtn) {
                    prevBtn.addEventListener('click', function() {
                        var scrollAmount = scrollContainer.clientWidth;
                        scrollContainer.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    });

                    nextBtn.addEventListener('click', function() {
                        var scrollAmount = scrollContainer.clientWidth;
                        scrollContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                    });
                }

                // Auto Play Carousel
                var slideCount = scrollContainer.children.length;
                if (slideCount > 1) {
                    var autoPlayInterval = setInterval(function() {
                        var scrollAmount = scrollContainer.clientWidth;
                        var maxScrollLeft = scrollContainer.scrollWidth - scrollContainer.clientWidth;
                        
                        if (scrollContainer.scrollLeft >= maxScrollLeft - 10) {
                            // If reached end, scroll back to start
                            scrollContainer.scrollTo({ left: 0, behavior: 'smooth' });
                        } else {
                            scrollContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                        }
                    }, 4000); // 4 seconds

                    // Pause on hover
                    container.addEventListener('mouseenter', function() {
                        clearInterval(autoPlayInterval);
                    });
                    container.addEventListener('mouseleave', function() {
                        autoPlayInterval = setInterval(function() {
                            var scrollAmount = scrollContainer.clientWidth;
                            var maxScrollLeft = scrollContainer.scrollWidth - scrollContainer.clientWidth;
                            
                            if (scrollContainer.scrollLeft >= maxScrollLeft - 10) {
                                scrollContainer.scrollTo({ left: 0, behavior: 'smooth' });
                            } else {
                                scrollContainer.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                            }
                        }, 4000);
                    });
                }
            });
        });

        // Lightbox Logic
        window.currentGallery = [];
        window.currentIndex = 0;

        window.openLightbox = function(galleryKey, index) {
            window.currentGalleryKey = galleryKey;
            window.currentGallery = window.lightboxData[galleryKey] || [];
            window.currentIndex = index || 0;
            updateLightboxImage();

            var lightbox = document.getElementById('image-lightbox');
            var wrapper = document.getElementById('lightbox-content-wrapper');
            
            lightbox.classList.remove('hidden');

            // Request animation frame for smooth transition
            window.requestAnimationFrame(function() {
                lightbox.classList.replace('opacity-0', 'opacity-100');
                wrapper.classList.replace('scale-95', 'scale-100');
            });
            document.body.style.overflow = 'hidden';
        };

        window.closeLightbox = function() {
            var lightbox = document.getElementById('image-lightbox');
            var wrapper = document.getElementById('lightbox-content-wrapper');
            var vid = document.getElementById('lightbox-video');
            
            lightbox.classList.replace('opacity-100', 'opacity-0');
            wrapper.classList.replace('scale-100', 'scale-95');
            
            if (vid) {
                vid.pause();
            }

            // Kembalikan suara video utama jika baru saja menonton video galeri
            if (window.toggleHeroAudio && window.wasPlayingVideoInLightbox) {
                window.toggleHeroAudio(true, false);
                window.wasPlayingVideoInLightbox = false;
            }

            setTimeout(function() {
                lightbox.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        };

        window.updateLightboxImage = function() {
            var img = document.getElementById('lightbox-img');
            var vid = document.getElementById('lightbox-video');
            if (window.currentGallery.length > 0) {
                var src = window.currentGallery[window.currentIndex];
                if (src.endsWith('.mp4')) {
                    img.classList.add('hidden');
                    vid.classList.remove('hidden');
                    vid.src = src;
                    vid.muted = false; // Pastikan video galeri selalu bersuara secara default
                    vid.volume = 0.5;
                    vid.play();
                    
                    // Matikan suara video utama secara paksa saat video galeri memutar
                    if (window.toggleHeroAudio) {
                        window.wasPlayingVideoInLightbox = true;
                        window.toggleHeroAudio(false, true); // forceMute
                    }
                    
                    // Sinkronisasi tombol dengan video galeri yang sedang menyala
                    if (window.updateAudioButtonUI) window.updateAudioButtonUI(true);
                } else {
                    vid.classList.add('hidden');
                    img.classList.remove('hidden');
                    img.src = src;
                    vid.pause();
                    
                    // Kembalikan suara video utama secara otomatis
                    if (window.toggleHeroAudio && window.wasPlayingVideoInLightbox) {
                        window.toggleHeroAudio(true, false);
                        window.wasPlayingVideoInLightbox = false;
                    }
                }
            }
        };

        window.lightboxNext = function(e) {
            if (e) e.stopPropagation();
            if (window.currentGallery.length > 0) {
                window.currentIndex = (window.currentIndex + 1) % window.currentGallery.length;
                updateLightboxImage();
            }
        };

        window.lightboxPrev = function(e) {
            if (e) e.stopPropagation();
            if (window.currentGallery.length > 0) {
                window.currentIndex = (window.currentIndex - 1 + window.currentGallery.length) % window.currentGallery.length;
                updateLightboxImage();
            }
        };



        window.closeLightboxAndScroll = function() {
            window.closeLightbox();
            setTimeout(function() {
                if (window.currentGalleryKey && window.currentGalleryKey.startsWith('room-')) {
                    var roomId = window.currentGalleryKey.replace('room-', '');
                    var roomCard = document.getElementById('room-card-' + roomId);
                    if (roomCard) {
                        roomCard.scrollIntoView({behavior: 'smooth', block: 'center'});
                        if (typeof window.showRoomBookingForm === 'function') {
                            // Tunggu scroll selesai sedikit sebelum membuka form agar animasi mulus
                            setTimeout(function() {
                                window.showRoomBookingForm(roomId);
                            }, 200);
                        }
                        return;
                    }
                }

                // Fallback jika dibuka dari galeri umum
                var form = document.getElementById('search-form');
                if(form) {
                    form.scrollIntoView({behavior: 'smooth', block: 'center'});
                    var btnText = document.getElementById('search-btn-text');
                    if (btnText) btnText.innerText = @json(__('public.continue_booking'));
                }
            }, 300);
        };
    </script>
</body>
</html>


