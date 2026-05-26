<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative">
                <div class="absolute -left-4 top-1/2 h-8 w-1 -translate-y-1/2 rounded-full bg-emerald-600"></div>
                <p class="text-xs font-black uppercase tracking-[0.2em] text-emerald-700">Web Administration</p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-neutral-950">Kontrol Halaman Publik</h2>
                <p class="mt-1 text-sm font-medium text-neutral-500">Konfigurasi materi publik, aturan pemesanan, rekening aktif, dan jalan pintas pengeditan master data.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="group inline-flex items-center gap-2 rounded-2xl border border-neutral-200 bg-white px-5 py-3 text-sm font-bold text-neutral-800 shadow-sm transition-all hover:border-neutral-950 hover:bg-neutral-50 hover:-translate-y-0.5 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4 text-neutral-500 transition-transform group-hover:-translate-x-1"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                Kembali ke Operasional
            </a>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,0.06),transparent_40rem)] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
            
            @if (session('status'))
                <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 text-sm font-semibold text-emerald-800 backdrop-blur-sm animate-fade-in">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50/80 p-4 text-sm font-semibold text-red-800 backdrop-blur-sm animate-fade-in">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </span>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <!-- SECTION 1: PUBLIC MEDIA & BOOKING RULE -->
            <section id="website-public-settings" class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                <!-- Media Publik -->
                <div class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
                    <div class="absolute right-0 top-0 -mr-6 -mt-6 h-24 w-24 rounded-full bg-emerald-500/5 blur-xl"></div>
                    
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="space-y-1">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Media Halaman Publik</p>
                            <h3 class="text-2xl font-black text-neutral-950">Hero Awal untuk Tamu</h3>
                            <p class="text-sm font-medium leading-relaxed text-neutral-500">
                                Mode video hanya akan dimuat ketika koneksi pengunjung terdeteksi cepat dan mode hemat data tidak aktif. Jika tidak memenuhi syarat, sistem secara otomatis beralih menggunakan carousel foto.
                            </p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-50 px-3.5 py-1 text-xs font-black uppercase tracking-wide text-emerald-700 ring-1 ring-emerald-100/50">
                            Mode Aktif: {{ $heroMediaMode === 'video' ? 'Video' : 'Foto' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('settings.public-media.update') }}" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                        @csrf
                        @method('PATCH')
                        <div class="w-full flex-1">
                            <x-custom-select 
                                name="hero_media_mode" 
                                :options="['photos' => 'Carousel Foto Slider (Rekomendasi Hemat Kuota)', 'video' => 'Video Sinematik (Resolusi Tinggi)']" 
                                :selected="$heroMediaMode"
                                placeholder="Pilih Mode Media"
                                :required="true"
                            />
                        </div>
                        <button class="w-full sm:w-auto shrink-0 flex items-center justify-center gap-2 rounded-xl bg-neutral-950 px-6 py-3.5 text-sm font-bold text-white shadow-md transition-all hover:bg-emerald-800 hover:-translate-y-0.5 active:scale-[0.98]">
                            Simpan Media
                        </button>
                    </form>
                </div>

                <!-- Aturan Booking -->
                <div class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Aturan Booking</p>
                    <h3 class="mt-1 text-2xl font-black text-neutral-950">Informasi Sistem</h3>
                    
                    <dl class="mt-6 space-y-4 text-sm font-medium">
                        <div class="flex justify-between gap-4 border-b border-neutral-100 pb-3.5">
                            <dt class="text-neutral-500">Minimal DP Reservasi</dt>
                            <dd class="font-black text-neutral-950 bg-emerald-50 text-emerald-800 px-2.5 py-0.5 rounded-lg text-xs">{{ $minDpPercent }}% dari Total</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-b border-neutral-100 pb-3.5">
                            <dt class="text-neutral-500">WhatsApp Pelayanan Villa</dt>
                            <dd class="font-black text-neutral-950 flex items-center gap-1">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                +{{ $villaWhatsappNumber }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-neutral-500">Unit Kamar Berstatus Aktif</dt>
                            <dd class="font-black text-neutral-950 bg-neutral-100 text-neutral-800 px-2.5 py-0.5 rounded-lg text-xs">{{ $rooms->where('is_active', true)->count() }} Kamar</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- SECTION 2: MASTER DATA SHORTCUTS -->
            <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Pintasan Kamar -->
                <a href="{{ route('rooms.index') }}" class="group relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_15px_35px_-15px_rgba(15,23,42,0.05)] transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:-translate-y-1">
                    <div class="absolute -right-4 -bottom-4 h-16 w-16 rounded-full bg-emerald-500/5 transition-transform duration-500 group-hover:scale-[3.5]"></div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                    </div>
                    <h3 class="mt-5 text-xl font-black text-neutral-950">Kamar & Fasilitas</h3>
                    <p class="mt-2 text-sm font-medium leading-relaxed text-neutral-500">Edit foto, tarif per malam, kapasitas tamu, serta deskripsi spesifikasi kamar.</p>
                    <div class="mt-6 flex items-center justify-between border-t border-neutral-100 pt-4">
                        <span class="text-xs font-black text-neutral-400">{{ $rooms->count() }} Kamar Terdaftar</span>
                        <span class="text-xs font-black text-emerald-700 group-hover:translate-x-1.5 transition-transform flex items-center gap-0.5">Kelola <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg></span>
                    </div>
                </a>

                <!-- Pintasan Add-ons -->
                <a href="{{ route('addon-items.index') }}" class="group relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_15px_35px_-15px_rgba(15,23,42,0.05)] transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:-translate-y-1">
                    <div class="absolute -right-4 -bottom-4 h-16 w-16 rounded-full bg-emerald-500/5 transition-transform duration-500 group-hover:scale-[3.5]"></div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <h3 class="mt-5 text-xl font-black text-neutral-950">Add-ons Makanan</h3>
                    <p class="mt-2 text-sm font-medium leading-relaxed text-neutral-500">Kelola menu makanan, minuman, dan ekstra kasur pelengkap transaksi tamu.</p>
                    <div class="mt-6 flex items-center justify-between border-t border-neutral-100 pt-4">
                        <span class="text-xs font-black text-neutral-400">{{ $addonItems->count() }} Layanan Tambahan</span>
                        <span class="text-xs font-black text-emerald-700 group-hover:translate-x-1.5 transition-transform flex items-center gap-0.5">Kelola <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg></span>
                    </div>
                </a>

                <!-- Pintasan Publik -->
                <a href="#website-public-settings" class="group relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_15px_35px_-15px_rgba(15,23,42,0.05)] transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:-translate-y-1">
                    <div class="absolute -right-4 -bottom-4 h-16 w-16 rounded-full bg-emerald-500/5 transition-transform duration-500 group-hover:scale-[3.5]"></div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9s2.015-9 4.5-9m0 0a9.003 9.003 0 0 1 8.716 6.747M12 3a9.003 9.003 0 0 0-8.716 6.747M12 9h.008v.008H12V9Zm6 0h.008v.008H18V9Zm-6 6h.008v.008H12V15Zm-6 0h.008v.008H6V15Z" /></svg>
                    </div>
                    <h3 class="mt-5 text-xl font-black text-neutral-950">Situs Publik</h3>
                    <p class="mt-2 text-sm font-medium leading-relaxed text-neutral-500">Atur preferensi media publik, kelola visualisasi, dan nomor rekening aktif.</p>
                    <div class="mt-6 flex items-center justify-between border-t border-neutral-100 pt-4">
                        <span class="text-xs font-black text-neutral-400">Media, Rekening & Info</span>
                        <span class="text-xs font-black text-emerald-700 group-hover:translate-x-1.5 transition-transform flex items-center gap-0.5">Kelola <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg></span>
                    </div>
                </a>
            </section>

            <!-- SECTION 3: BANK ACCOUNTS MANAGEMENT -->
            <section class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] items-start">
                <!-- Tambah Rekening -->
                <div class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Rekening Baru</p>
                    <h3 class="mt-1 text-2xl font-black text-neutral-950">Tambah Rekening</h3>
                    <p class="mt-2 text-sm font-medium leading-relaxed text-neutral-500">Hanya rekening bank aktif yang akan ditampilkan pada modul pembayaran pemesanan tamu.</p>
                    
                    <div class="mt-4 flex gap-3 items-start rounded-2xl border border-amber-100 bg-amber-50/50 p-4 text-xs font-bold leading-relaxed text-amber-900">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-9-4a1 1 0 1 1 2 0v4a1 1 0 1 1-2 0V6Zm1 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                        </span>
                        <p>Area sensitif keuangan. Penambahan atau pengeditan data rekening mewajibkan konfirmasi password admin terlebih dahulu.</p>
                    </div>

                    <form method="POST" action="{{ route('bank-accounts.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Nama Bank / Institusi</label>
                            <input name="bank_name" value="{{ old('bank_name') }}" placeholder="Contoh: BCA / BNI / Mandiri" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Nomor Rekening</label>
                            <input name="account_number" value="{{ old('account_number') }}" inputmode="numeric" placeholder="Contoh: 0562603148" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Nama Pemilik Rekening</label>
                            <input name="account_name" value="{{ old('account_name') }}" placeholder="Contoh: PT DAFANO VILLA INDONESIA" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm font-bold text-neutral-700 cursor-pointer">
                            <input name="is_active" type="checkbox" value="1" checked class="rounded border-neutral-300 text-emerald-600 focus:ring-emerald-500">
                            Aktifkan langsung untuk transaksi publik
                        </label>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-neutral-950 py-3.5 text-sm font-bold text-white shadow-md transition-all hover:bg-emerald-800 hover:-translate-y-0.5 active:scale-[0.98]">
                            Tambah Rekening
                        </button>
                    </form>
                </div>

                <!-- Kelola Rekening -->
                <div class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
                    <div class="border-b border-neutral-100 px-6 py-5">
                        <h3 class="text-xl font-black text-neutral-950">Daftar Rekening Pembayaran</h3>
                        <p class="mt-1 text-sm font-semibold text-neutral-500">Sesuaikan data nomor rekening transfer bank yang aktif dan dapat dipakai oleh tamu.</p>
                    </div>
                    
                    <div class="divide-y divide-neutral-100">
                        @foreach ($bankAccounts as $bankAccount)
                            <form method="POST" action="{{ route('bank-accounts.update', $bankAccount) }}" class="p-6 transition-colors hover:bg-neutral-50/50">
                                @csrf
                                @method('PATCH')
                                <div class="grid gap-4 sm:grid-cols-3">
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-wider text-neutral-400">Nama Bank</label>
                                        <input name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" class="mt-1 block w-full rounded-xl border-neutral-200 text-sm font-bold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-wider text-neutral-400">Nomor Rekening</label>
                                        <input name="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" inputmode="numeric" class="mt-1 block w-full rounded-xl border-neutral-200 text-sm font-bold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-wider text-neutral-400">Atas Nama Pemilik</label>
                                        <input name="account_name" value="{{ old('account_name', $bankAccount->account_name) }}" class="mt-1 block w-full rounded-xl border-neutral-200 text-sm font-bold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between gap-3 border-t border-neutral-100/60 pt-4">
                                    <label class="inline-flex items-center gap-2 rounded-xl bg-neutral-100 px-3 py-2 text-xs font-bold text-neutral-700 cursor-pointer transition-colors hover:bg-neutral-200/60">
                                        <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $bankAccount->is_active)) class="rounded border-neutral-300 text-emerald-600 focus:ring-emerald-500">
                                        Status Rekening Aktif
                                    </label>
                                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-neutral-200 bg-white px-5 py-2 text-xs font-bold text-neutral-800 shadow-sm transition-all hover:border-neutral-950 hover:bg-neutral-50 hover:-translate-y-0.5 active:scale-95">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        @endforeach

                        @if ($bankAccounts->isEmpty())
                            <div class="flex flex-col items-center justify-center p-12 text-center">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-100 text-neutral-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                </span>
                                <h4 class="mt-4 text-base font-black text-neutral-900">Belum Ada Rekening Bank</h4>
                                <p class="mt-1 text-xs font-semibold text-neutral-500 max-w-xs leading-relaxed">Silakan tambahkan nomor rekening aktif Anda melalui form di sebelah kiri.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

