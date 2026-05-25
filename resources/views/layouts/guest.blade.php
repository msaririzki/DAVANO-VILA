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
        <main class="grid min-h-screen bg-[#f6f4ef] lg:grid-cols-[1.05fr_0.95fr]">
            <section class="relative hidden overflow-hidden bg-neutral-950 lg:block">
                <img src="https://images.unsplash.com/photo-1600566753151-384129cf4e3e?auto=format&fit=crop&w=1400&q=85" alt="Area villa modern" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-black/35 via-black/20 to-black/70"></div>
                <div class="relative flex h-full flex-col justify-between p-10">
                    <a href="{{ route('public.home') }}" class="inline-flex items-center gap-3 rounded-full border border-white/15 bg-black/20 py-1.5 pl-1.5 pr-4 text-white shadow-lg backdrop-blur-md">
                        <img src="{{ asset('dafano-media/brand/logo-dafano-villa.jpg') }}" alt="Villa Dafano" class="h-10 w-10 rounded-full object-cover ring-2 ring-white/25">
                        <span class="text-sm font-bold">Villa Dafano</span>
                    </a>
                    <div class="max-w-lg">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-200">Internal dashboard</p>
                        <h1 class="mt-4 text-5xl font-semibold leading-tight text-white">Kontrol reservasi dan pembayaran terpusat.</h1>
                    </div>
                </div>
            </section>

            <section class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="w-full max-w-md">
                    <div class="mb-8 lg:hidden">
                        <a href="{{ route('public.home') }}" class="inline-flex items-center gap-3">
                            <img src="{{ asset('dafano-media/brand/logo-dafano-villa.jpg') }}" alt="Villa Dafano" class="h-10 w-10 rounded-xl object-cover ring-1 ring-black/10">
                            <span class="text-lg font-semibold text-neutral-950">Villa Dafano</span>
                        </a>
                    </div>

                    <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
