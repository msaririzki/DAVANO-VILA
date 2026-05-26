<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Dafano Villa') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-neutral-950 antialiased">
        <main class="grid min-h-screen bg-[#f8f9fa] lg:grid-cols-[1.2fr_0.8fr] relative">
            <section class="relative hidden overflow-hidden bg-emerald-950 lg:block">
                <img src="{{ asset('dafano-media/gallery/halaman/yard-3.jpg') }}" alt="Area Villa Dafano" class="absolute inset-0 h-full w-full object-cover transition-transform duration-[20s] ease-linear hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-950/80 via-neutral-900/40 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                
                <div class="relative flex h-full flex-col justify-between p-12 z-10 animate-[fadeIn_1s_ease-out]">
                    <a href="{{ route('public.home') }}" class="inline-flex items-center gap-3 rounded-full border border-white/20 bg-white/10 py-2 pl-2 pr-5 text-white shadow-2xl backdrop-blur-md transition-all hover:bg-white/20 hover:scale-105">
                        <img src="{{ asset('dafano-media/brand/logo-dafano-villa.jpg') }}" alt="Villa Dafano" class="h-10 w-10 rounded-full object-cover ring-2 ring-emerald-400">
                        <span class="text-sm font-bold tracking-wider uppercase">Villa Dafano</span>
                    </a>
                    <div class="max-w-xl">
                        <span class="inline-block px-3 py-1 mb-4 text-[0.65rem] font-bold tracking-[0.2em] uppercase text-emerald-100 bg-emerald-600/50 backdrop-blur-md rounded-full border border-emerald-400/30 shadow-lg">Internal System</span>
                        <h1 class="mt-2 text-5xl sm:text-6xl font-black leading-[1.1] text-white drop-shadow-lg tracking-tight">Manajemen<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-100">Reservasi Cerdas.</span></h1>
                        <p class="mt-5 text-emerald-50/80 text-lg font-medium max-w-md leading-relaxed drop-shadow-md">Akses eksklusif untuk staf dalam mengelola pemesanan, ketersediaan, dan layanan tamu dengan standar terbaik.</p>
                    </div>
                </div>
            </section>

            <section class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-12 relative overflow-hidden bg-gradient-to-br from-white to-emerald-50/50">
                <!-- Decorative background elements for mobile/right side -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-emerald-100/40 blur-[60px] pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-teal-100/30 blur-[80px] pointer-events-none"></div>

                <div class="w-full max-w-md relative z-10 animate-[slideDown_0.6s_ease-out]">
                    <div class="mb-10 flex justify-center lg:hidden">
                        <a href="{{ route('public.home') }}" class="inline-flex flex-col items-center gap-3">
                            <img src="{{ asset('dafano-media/brand/logo-dafano-villa.jpg') }}" alt="Villa Dafano" class="h-14 w-14 rounded-2xl object-cover ring-4 ring-white shadow-xl">
                            <span class="text-xl font-black tracking-tight text-emerald-950 uppercase">Villa Dafano</span>
                        </a>
                    </div>

                    <div class="rounded-[2rem] border border-white/60 bg-white/70 p-8 shadow-[0_20px_60px_-15px_rgba(4,120,87,0.1)] backdrop-blur-2xl sm:p-10 relative overflow-hidden">
                        <!-- Tiny decorative gradient in corner -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/10 blur-[30px] rounded-full pointer-events-none"></div>
                        
                        {{ $slot }}
                    </div>
                    
                    <div class="mt-8 text-center">
                        <p class="text-xs font-semibold text-neutral-400 tracking-wide uppercase">&copy; {{ date('Y') }} Villa Dafano. Secure Access.</p>
                    </div>
                </div>
            </section>
        </main>
        <style>
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </body>
</html>
