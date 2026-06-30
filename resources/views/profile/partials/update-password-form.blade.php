<section>
    <header>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">
            Ubah Kata Sandi
        </h2>

        <p class="mt-1 text-sm font-medium text-slate-500">
            Gunakan kata sandi yang panjang dan sulit ditebak untuk menjaga keamanan akun.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div class="space-y-1">
            <label for="update_password_current_password" class="block text-sm font-bold text-slate-700">Kata Sandi Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" class="block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm sm:text-sm font-semibold text-slate-900" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="space-y-1">
            <label for="update_password_password" class="block text-sm font-bold text-slate-700">Kata Sandi Baru</label>
            <input id="update_password_password" name="password" type="password" class="block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm sm:text-sm font-semibold text-slate-900" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="space-y-1">
            <label for="update_password_password_confirmation" class="block text-sm font-bold text-slate-700">Ulangi Kata Sandi Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm sm:text-sm font-semibold text-slate-900" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-emerald-700 active:scale-95">Simpan Kata Sandi</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full ring-1 ring-emerald-200"
                >Kata sandi tersimpan.</p>
            @endif
        </div>
    </form>
</section>
