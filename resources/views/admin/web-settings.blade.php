<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">Pengaturan Web Publik</h2>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">Publik</span>
                    </div>
                    <p class="mt-0.5 text-xs font-semibold text-slate-500">Kelola tampilan, kontak, dan opsi pembayaran untuk tamu</p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all font-bold text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Ke Operasional
            </a>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-slate-50 pt-6 pb-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
            
            @if (session('status'))
                <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800 shadow-sm animate-fade-in">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-bold text-rose-800 shadow-sm animate-fade-in">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </span>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <!-- SECTION 1: PUBLIC MEDIA & BOOKING RULE -->
            <section id="website-public-settings" class="grid gap-6 lg:grid-cols-2">
                <!-- Media Publik -->
                <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md flex flex-col justify-between">
                    <div class="absolute right-0 top-0 -mr-8 -mt-8 h-32 w-32 rounded-full bg-emerald-50 blur-2xl"></div>
                    
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700 ring-1 ring-emerald-200 mb-2">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                            Tampilan Depan
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Tampilan Halaman Depan Web</h3>
                        <p class="mt-2 text-sm font-medium leading-relaxed text-slate-500 max-w-md">
                            Pilih tampilan pertama yang dilihat tamu saat membuka situs. Gunakan <b>Kumpulan Foto</b> agar halaman lebih cepat diakses.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('settings.public-media.update') }}" class="relative z-10 mt-8 flex flex-col gap-4 border-t border-slate-100 pt-6 sm:flex-row sm:items-end">
                        @csrf
                        @method('PATCH')
                        <div class="w-full flex-1 space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Pilihan Tampilan Utama</label>
                            <x-custom-select 
                                name="hero_media_mode" 
                                :options="['photos' => 'Kumpulan Foto (Direkomendasikan)', 'video' => 'Video Estetik (Lambat)']"
                                :selected="$heroMediaMode"
                                placeholder="Pilih Tampilan"
                                :required="true"
                            />
                        </div>
                        <button class="w-full sm:w-auto shrink-0 flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-emerald-700 active:scale-95">
                            Simpan Pilihan
                        </button>
                    </form>
                </div>

                <!-- Aturan Pemesanan -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md flex flex-col justify-between">
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-700 ring-1 ring-slate-200 mb-2">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                            Info Bisnis
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Ringkasan Info & Kontak</h3>
                        <p class="mt-2 text-sm text-slate-500 font-medium">Informasi utama dan pengaturan kontak operasional villa saat ini.</p>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Minimal DP</p>
                            <p class="text-3xl font-black text-emerald-600 tracking-tighter">{{ $minDpPercent }}<span class="text-xl text-emerald-600/70">%</span></p>
                            <p class="text-[10px] font-bold text-slate-400 mt-1">DARI TOTAL BIAYA</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Kamar Disewakan</p>
                            <p class="text-3xl font-black text-blue-600 tracking-tighter">{{ $rooms->where('is_active', true)->count() }} <span class="text-xl text-blue-600/70">Kamar</span></p>
                            <p class="text-[10px] font-bold text-slate-400 mt-1">AKTIF DI WEB</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: MASTER DATA SHORTCUTS -->
            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Pintasan Kamar -->
                <a href="{{ route('rooms.index') }}" class="group block rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-indigo-300 hover:shadow-md hover:-translate-y-1">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100 transition-colors group-hover:bg-indigo-500 group-hover:text-white group-hover:ring-indigo-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-black text-slate-900 tracking-tight">Kamar & Harga</h3>
                    <p class="mt-1 text-sm font-medium text-slate-500">Atur harga per malam, fasilitas, dan foto kamar.</p>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="text-xs font-bold text-slate-400">{{ $rooms->count() }} Kamar</span>
                        <span class="text-xs font-bold text-indigo-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Atur Kamar &rarr;</span>
                    </div>
                </a>

                <!-- Pintasan Add-ons -->
                <a href="{{ route('addon-items.index') }}" class="group block rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-amber-300 hover:shadow-md hover:-translate-y-1">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 transition-colors group-hover:bg-amber-500 group-hover:text-white group-hover:ring-amber-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-black text-slate-900 tracking-tight">Menu & Tambahan</h3>
                    <p class="mt-1 text-sm font-medium text-slate-500">Kelola menu makanan, kasur ekstra, dan lainnya.</p>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="text-xs font-bold text-slate-400">{{ $addonItems->count() }} Menu</span>
                        <span class="text-xs font-bold text-amber-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Lihat Menu &rarr;</span>
                    </div>
                </a>

                <!-- Pintasan Publik -->
                <a href="{{ route('admin.business-profile.edit') }}" class="group block rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-sky-300 hover:shadow-md hover:-translate-y-1">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100 transition-colors group-hover:bg-sky-500 group-hover:text-white group-hover:ring-sky-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9s2.015-9 4.5-9m0 0a9.003 9.003 0 018.716 6.747M12 3a9.003 9.003 0 00-8.716 6.747M12 9h.008v.008H12V9zm6 0h.008v.008H18V9zm-6 6h.008v.008H12v.008zm-6 0h.008v.008H6v.008z" /></svg>
                    </div>
                    <h3 class="mt-4 text-lg font-black text-slate-900 tracking-tight">Pengaturan Situs</h3>
                    <p class="mt-1 text-sm font-medium text-slate-500">Ubah tampilan web dan nomor kontak WhatsApp.</p>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                        <span class="text-xs font-bold text-slate-400">Pengaturan Publik</span>
                        <span class="text-xs font-bold text-sky-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">Buka Profil &rarr;</span>
                    </div>
                </a>
            </section>

            <!-- SECTION 3: BANK ACCOUNTS MANAGEMENT -->
            <section class="grid gap-6 lg:grid-cols-12 items-start">
                <!-- Tambah Rekening -->
                <div class="lg:col-span-4 lg:sticky lg:top-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-700 ring-1 ring-blue-200 mb-2">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Pembayaran
                    </div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Tambah Rekening</h3>
                    <p class="mt-2 text-sm font-medium text-slate-500">Nomor rekening yang digunakan tamu untuk transfer DP.</p>
                    
                    <div class="mt-5 flex gap-3 items-start rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs font-semibold text-amber-900">
                        <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <p class="leading-relaxed">Hati-hati, pastikan nomor rekening dan atas nama sudah benar agar tamu tidak salah transfer.</p>
                    </div>

                    <form method="POST" action="{{ route('bank-accounts.store') }}" class="mt-6 space-y-4 border-t border-slate-100 pt-6">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-slate-700">Nama Bank</label>
                            <input name="bank_name" value="{{ old('bank_name') }}" placeholder="Contoh: BCA / Mandiri" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-slate-700">Nomor Rekening</label>
                            <input name="account_number" value="{{ old('account_number') }}" inputmode="numeric" placeholder="Contoh: 0562603148" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-slate-700">Atas Nama</label>
                            <input name="account_name" value="{{ old('account_name') }}" placeholder="Contoh: Budi Santoso" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20" required>
                        </div>
                        <div class="pt-2">
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 cursor-pointer transition-colors hover:bg-slate-100 hover:border-slate-300">
                                <input name="is_active" type="checkbox" value="1" checked class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 transition-all">
                                <span class="text-sm font-bold text-slate-700">Aktifkan Langsung</span>
                            </label>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-slate-900 py-3.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-emerald-700 active:scale-95">
                                Simpan Rekening
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Kelola Rekening -->
                <div class="lg:col-span-8 space-y-4">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Daftar Rekening Bank</h3>
                                <p class="mt-1 text-sm font-medium text-slate-500">Pilih rekening mana saja yang aktif untuk menerima pembayaran.</p>
                            </div>
                            <span class="inline-flex items-center justify-center rounded-xl bg-blue-50 px-3 py-1.5 text-sm font-black text-blue-700 ring-1 ring-blue-200">
                                {{ $bankAccounts->count() }} Rekening
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid gap-4">
                        @foreach ($bankAccounts as $bankAccount)
                            <form method="POST" action="{{ route('bank-accounts.update', $bankAccount) }}" class="group rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-emerald-300 hover:shadow-md">
                                @csrf
                                @method('PATCH')
                                <div class="grid gap-4 sm:grid-cols-12 items-start">
                                    <div class="sm:col-span-3 space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Bank</label>
                                        <input name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 transition-colors" required>
                                    </div>
                                    <div class="sm:col-span-5 space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Nomor Rekening</label>
                                        <input name="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" inputmode="numeric" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 transition-colors" required>
                                    </div>
                                    <div class="sm:col-span-4 space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Atas Nama</label>
                                        <input name="account_name" value="{{ old('account_name', $bankAccount->account_name) }}" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 transition-colors" required>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100">
                                    <label class="inline-flex items-center gap-3 cursor-pointer">
                                        <div class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ old('is_active', $bankAccount->is_active) ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $bankAccount->is_active)) class="peer sr-only">
                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform peer-checked:translate-x-6 translate-x-1 shadow-sm"></span>
                                        </div>
                                        <span class="text-sm font-bold text-slate-700 select-none">{{ old('is_active', $bankAccount->is_active) ? 'Aktif Digunakan' : 'Tidak Aktif' }}</span>
                                    </label>

                                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 px-5 py-2 text-sm font-bold text-slate-700 shadow-sm transition-all hover:bg-slate-50 hover:text-slate-900 active:scale-95 group-hover:border-emerald-300 group-hover:text-emerald-700">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        @endforeach

                        @if ($bankAccounts->isEmpty())
                            <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
                                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </span>
                                <h4 class="mt-4 text-lg font-black text-slate-900">Belum Ada Rekening Bank</h4>
                                <p class="mt-2 text-sm font-medium text-slate-500 max-w-sm leading-relaxed">Silakan tambahkan nomor rekening melalui form di samping agar tamu bisa melakukan transfer pembayaran.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
