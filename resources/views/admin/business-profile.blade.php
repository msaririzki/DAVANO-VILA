<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-emerald-700">Pengaturan Situs</p>
                <h2 class="text-2xl font-black tracking-tight text-slate-900">Profil Bisnis</h2>
                <p class="mt-1 text-sm font-medium text-slate-500">Informasi ini digunakan pada halaman publik, kontak tamu, dan dokumen reservasi.</p>
            </div>
            <a href="{{ route('admin.web-settings') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50">
                Kembali ke Pengaturan Web
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.business-profile.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <h3 class="text-lg font-black text-slate-900">Identitas Villa</h3>
                    <p class="mt-1 text-sm font-medium text-slate-500">Nama dan penjelasan utama yang dilihat calon tamu.</p>
                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="business_name" class="mb-1.5 block text-xs font-bold text-slate-700">Nama Bisnis</label>
                            <input id="business_name" name="business_name" value="{{ old('business_name', $profile['business_name']) }}" class="block w-full rounded-xl border-slate-200 text-sm font-bold focus:border-emerald-500 focus:ring-emerald-500/20" required>
                            <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                        </div>
                        <div>
                            <label for="business_tagline" class="mb-1.5 block text-xs font-bold text-slate-700">Tagline</label>
                            <input id="business_tagline" name="business_tagline" value="{{ old('business_tagline', $profile['business_tagline']) }}" class="block w-full rounded-xl border-slate-200 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500/20">
                            <x-input-error :messages="$errors->get('business_tagline')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="about_description" class="mb-1.5 block text-xs font-bold text-slate-700">Deskripsi Tentang Kami</label>
                            <textarea id="about_description" name="about_description" rows="4" class="block w-full rounded-xl border-slate-200 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500/20">{{ old('about_description', $profile['about_description'] ?: __('public.about_body')) }}</textarea>
                            <p class="mt-1 text-[11px] font-medium text-slate-400">Tampil di bagian “Tentang Kami” pada halaman utama.</p>
                            <x-input-error :messages="$errors->get('about_description')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="business_description" class="mb-1.5 block text-xs font-bold text-slate-700">Deskripsi Footer</label>
                            <textarea id="business_description" name="business_description" rows="4" class="block w-full rounded-xl border-slate-200 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500/20">{{ old('business_description', $profile['business_description']) }}</textarea>
                            <p class="mt-1 text-[11px] font-medium text-slate-400">Tampil di bagian paling bawah halaman utama.</p>
                            <x-input-error :messages="$errors->get('business_description')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <h3 class="text-lg font-black text-slate-900">Kontak dan Lokasi</h3>
                    <p class="mt-1 text-sm font-medium text-slate-500">Nomor WhatsApp digunakan oleh semua tombol bantuan dan konfirmasi tamu.</p>
                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="villa_whatsapp_number" class="mb-1.5 block text-xs font-bold text-slate-700">Nomor WhatsApp Admin</label>
                            <input id="villa_whatsapp_number" name="villa_whatsapp_number" type="tel" inputmode="numeric" value="{{ old('villa_whatsapp_number', $profile['villa_whatsapp_number']) }}" placeholder="081234567890" class="block w-full rounded-xl border-slate-200 text-sm font-bold focus:border-emerald-500 focus:ring-emerald-500/20" required>
                            <p class="mt-1 text-[11px] font-medium text-slate-400">Boleh ditulis 08... atau 62...</p>
                            <x-input-error :messages="$errors->get('villa_whatsapp_number')" class="mt-2" />
                        </div>
                        <div>
                            <label for="business_email" class="mb-1.5 block text-xs font-bold text-slate-700">Email Bisnis</label>
                            <input id="business_email" name="business_email" type="email" value="{{ old('business_email', $profile['business_email']) }}" class="block w-full rounded-xl border-slate-200 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500/20">
                            <x-input-error :messages="$errors->get('business_email')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="business_address" class="mb-1.5 block text-xs font-bold text-slate-700">Alamat Lengkap</label>
                            <textarea id="business_address" name="business_address" rows="3" class="block w-full rounded-xl border-slate-200 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500/20">{{ old('business_address', $profile['business_address']) }}</textarea>
                            <x-input-error :messages="$errors->get('business_address')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <label for="business_maps_url" class="mb-1.5 block text-xs font-bold text-slate-700">Tautan Google Maps</label>
                            <input id="business_maps_url" name="business_maps_url" type="url" value="{{ old('business_maps_url', $profile['business_maps_url']) }}" placeholder="https://maps.google.com/..." class="block w-full rounded-xl border-slate-200 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500/20">
                            <x-input-error :messages="$errors->get('business_maps_url')" class="mt-2" />
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <h3 class="text-lg font-black text-slate-900">Jam Operasional dan Media Sosial</h3>
                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="check_in_time" class="mb-1.5 block text-xs font-bold text-slate-700">Jam Check-in</label>
                            <input id="check_in_time" name="check_in_time" type="time" value="{{ old('check_in_time', $profile['check_in_time']) }}" class="block w-full rounded-xl border-slate-200 text-sm font-bold focus:border-emerald-500 focus:ring-emerald-500/20" required>
                            <x-input-error :messages="$errors->get('check_in_time')" class="mt-2" />
                        </div>
                        <div>
                            <label for="check_out_time" class="mb-1.5 block text-xs font-bold text-slate-700">Jam Check-out</label>
                            <input id="check_out_time" name="check_out_time" type="time" value="{{ old('check_out_time', $profile['check_out_time']) }}" class="block w-full rounded-xl border-slate-200 text-sm font-bold focus:border-emerald-500 focus:ring-emerald-500/20" required>
                            <x-input-error :messages="$errors->get('check_out_time')" class="mt-2" />
                        </div>
                        @foreach ([
                            'instagram_url' => 'Instagram',
                            'tiktok_url' => 'TikTok',
                            'threads_url' => 'Threads',
                            'facebook_url' => 'Facebook',
                        ] as $field => $label)
                            <div>
                                <label for="{{ $field }}" class="mb-1.5 block text-xs font-bold text-slate-700">{{ $label }}</label>
                                <input id="{{ $field }}" name="{{ $field }}" type="url" value="{{ old($field, $profile[$field]) }}" placeholder="https://..." class="block w-full rounded-xl border-slate-200 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500/20">
                                <x-input-error :messages="$errors->get($field)" class="mt-2" />
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="sticky bottom-4 z-20 flex justify-end rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-xl backdrop-blur">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-7 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95 sm:w-auto">Simpan Profil Bisnis</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
