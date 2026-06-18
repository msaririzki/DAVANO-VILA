<section>
    <header>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm font-medium text-slate-500">
            Perbarui nama dan alamat email yang digunakan untuk akun ini.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div class="space-y-1">
            <label for="name" class="block text-sm font-bold text-slate-700">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm sm:text-sm font-semibold text-slate-900" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-1">
            <label for="email" class="block text-sm font-bold text-slate-700">Alamat Email</label>
            <input id="email" name="email" type="email" class="block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm sm:text-sm font-semibold text-slate-900" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-xl bg-amber-50 p-4 border border-amber-200">
                    <p class="text-sm font-bold text-amber-800">
                        Alamat email Anda belum diverifikasi.
                        <button form="send-verification" class="underline text-sm font-bold text-amber-600 hover:text-amber-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-sm text-emerald-600">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-emerald-700 active:scale-95">Simpan Perubahan</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full ring-1 ring-emerald-200"
                >Perubahan tersimpan.</p>
            @endif
        </div>
    </form>
</section>
