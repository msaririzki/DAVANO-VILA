<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8">
        <p class="text-[0.65rem] font-bold uppercase tracking-[0.2em] text-emerald-600/80 mb-1.5 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Khusus Akses Staf
        </p>
        <h1 class="text-3xl font-black tracking-tight text-neutral-900">Selamat datang kembali.</h1>
        <p class="mt-2 text-sm text-neutral-500 font-medium leading-relaxed">Silakan masuk menggunakan kredensial yang telah diberikan oleh admin untuk mengelola sistem.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5 relative z-10">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-neutral-600 uppercase tracking-wide mb-1.5">Alamat Email</label>
            <input id="email" class="block w-full rounded-xl border-neutral-200 bg-white/50 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm transition-all placeholder:text-neutral-400" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="admin@villadafano.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold text-neutral-600 uppercase tracking-wide">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>
            <input id="password" class="block w-full rounded-xl border-neutral-200 bg-white/50 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm transition-all placeholder:text-neutral-400"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <div class="relative flex items-center justify-center">
                    <input id="remember_me" type="checkbox" class="peer h-5 w-5 rounded-[0.35rem] border-neutral-300 text-emerald-600 shadow-sm focus:ring-emerald-500/30 transition-all cursor-pointer" name="remember">
                </div>
                <span class="ms-3 text-sm font-medium text-neutral-600 group-hover:text-neutral-900 transition-colors">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <div class="pt-4">
            <button type="submit" class="group relative w-full flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3.5 text-sm font-bold text-white shadow-xl shadow-emerald-900/20 hover:bg-emerald-800 hover:-translate-y-0.5 transition-all duration-300 active:scale-95 overflow-hidden">
                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-[100%] group-hover:animate-[shimmer_1.5s_infinite]"></div>
                <span>Masuk ke Sistem</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 transition-transform group-hover:translate-x-1"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
            </button>
        </div>
    </form>
</x-guest-layout>
