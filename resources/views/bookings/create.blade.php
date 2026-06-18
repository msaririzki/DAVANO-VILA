<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">Buat Pemesanan Tamu</h2>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">Internal</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all font-bold text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Operasional
            </a>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-slate-50 pt-6 pb-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="mb-8 flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50/80 p-4 text-sm font-semibold text-red-800 backdrop-blur-sm animate-fade-in">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </span>
                    <div>
                        <p class="font-bold">Terjadi Kesalahan Validasi</p>
                        <p class="text-xs font-semibold text-red-700/90 mt-0.5">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] items-start">
                
                <!-- KOLOM KIRI: Cek Tanggal & Alur -->
                <div class="space-y-8">
                    <!-- Kartu Filter Tanggal -->
                    <div class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
                        <div class="absolute right-0 top-0 -mr-6 -mt-6 h-24 w-24 rounded-full bg-emerald-500/5 blur-xl"></div>
                        
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Periksa Ketersediaan</p>
                        <h3 class="mt-1 text-2xl font-black text-neutral-950">Pilih Tanggal Menginap</h3>
                        <p class="mt-2 text-sm font-medium leading-relaxed text-neutral-500">Masukkan periode menginap untuk menyaring kamar yang masih kosong.</p>

                        <form method="GET" action="{{ route('bookings.create') }}" class="mt-6 space-y-4">
                            <!-- Unified Search Datepicker Wrapper -->
                            <div class="relative rounded-2xl border border-neutral-200 bg-neutral-50/80 p-1.5 grid grid-cols-2 gap-1 focus-within:border-emerald-600 focus-within:ring-1 focus-within:ring-emerald-600 transition-colors">
                                @include('public.partials.date-picker', [
                                    'calendarId' => 'check-in-picker',
                                    'name' => 'check_in_date',
                                    'label' => 'Check-In',
                                    'hint' => 'Pilih tanggal tiba',
                                    'value' => old('check_in_date', $checkIn),
                                    'collapsible' => true,
                                    'panelMode' => 'modal',
                                ])
                                @include('public.partials.date-picker', [
                                    'calendarId' => 'check-out-picker',
                                    'name' => 'check_out_date',
                                    'label' => 'Check-Out',
                                    'hint' => 'Pilih tanggal pulang',
                                    'value' => old('check_out_date', $checkOut),
                                    'collapsible' => true,
                                    'panelMode' => 'modal',
                                    'isEndNode' => true,
                                ])
                            </div>
                            
                            <button type="submit" class="group w-full flex items-center justify-center gap-2 rounded-2xl bg-slate-900 py-4 text-sm font-bold text-white shadow-[0_8px_16px_-6px_rgba(15,23,42,0.5)] transition-all duration-300 hover:bg-emerald-600 hover:shadow-[0_12px_20px_-6px_rgba(16,185,129,0.4)] hover:-translate-y-0.5 active:scale-[0.98]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4 transition-transform group-hover:scale-110"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" /></svg>
                                Cek Kamar Tersedia
                            </button>
                        </form>
                    </div>

                    <!-- Kartu Panduan Alur Aman -->
                    <div class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-500/10 to-teal-500/5 p-6">
                        <div class="absolute -right-8 -bottom-8 h-32 w-32 rounded-full bg-emerald-500/10 blur-2xl"></div>
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-800">Alur Administrasi</p>
                        <h4 class="mt-1 text-lg font-black text-emerald-950">Panduan Operasional</h4>
                        
                        <div class="mt-5 space-y-4">
                            <div class="flex gap-3 items-start">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold">1</span>
                                <p class="text-xs font-bold leading-relaxed text-emerald-950/80">Pilih tanggal masuk dan keluar di atas untuk memeriksa ketersediaan kamar.</p>
                            </div>
                            <div class="flex gap-3 items-start">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold">2</span>
                                <p class="text-xs font-bold leading-relaxed text-emerald-950/80">Lengkapi data pribadi tamu dengan cermat (pastikan nomor WhatsApp aktif).</p>
                            </div>
                            <div class="flex gap-3 items-start">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold">3</span>
                                <p class="text-xs font-bold leading-relaxed text-emerald-950/80">Pilih kamar yang diinginkan, kemudian klik tombol "Buat Pemesanan Tamu".</p>
                            </div>
                            <div class="flex gap-3 items-start">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold">4</span>
                                <p class="text-xs font-bold leading-relaxed text-emerald-950/80">Validasi pembayaran DP atau pelunasan hanya dapat dilakukan melalui menu detail pesanan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN: Data Tamu & Kamar -->
                <div class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-neutral-100">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Rincian Pemesanan</p>
                            <h3 class="mt-1 text-2xl font-black text-neutral-950">Lengkapi Data Pemesanan</h3>
                            <p class="mt-1 text-sm font-semibold text-neutral-500">
                                @if ($checkIn && $checkOut)
                                    <span class="inline-flex items-center gap-1.5 text-emerald-800 bg-emerald-50 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-emerald-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" /></svg>
                                        {{ \Carbon\Carbon::parse($checkIn)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($checkOut)->translatedFormat('d M Y') }} ({{ $nights }} Malam)
                                    </span>
                                @else
                                    <span class="text-red-600 bg-red-50/80 px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                                        Silakan tentukan tanggal periode inap terlebih dahulu.
                                    </span>
                                @endif
                            </p>
                        </div>
                        @if ($checkIn && $checkOut)
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-black text-emerald-800 ring-1 ring-emerald-100">Total {{ $nights }} malam</span>
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-neutral-900 px-3.5 py-1.5 text-xs font-black uppercase tracking-wide text-white shadow-sm">{{ $rooms->count() }} Kamar Tersedia</span>
                            </div>
                        @endif
                    </div>

                    @if ($checkIn && $checkOut && $rooms->isNotEmpty())
                        <form method="POST" action="{{ route('bookings.store') }}" class="mt-6 space-y-6">
                            @csrf
                            <input type="hidden" name="check_in_date" value="{{ $checkIn }}">
                            <input type="hidden" name="check_out_date" value="{{ $checkOut }}">

                            <!-- Form Isian Data Tamu -->
                            <div class="grid gap-5 sm:grid-cols-2 bg-neutral-50/50 p-5 rounded-2xl border border-neutral-100">
                                <div class="sm:col-span-2">
                                    <h4 class="text-sm font-black text-neutral-900 uppercase tracking-wider mb-1">Informasi Personal Tamu</h4>
                                    <p class="text-xs font-semibold text-neutral-400">Pastikan informasi di bawah ini diisi dengan benar untuk keperluan notifikasi.</p>
                                </div>
                                <div class="relative">
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Nama Lengkap Tamu</label>
                                    <div class="relative mt-2">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" /></svg>
                                        </span>
                                        <input name="guest_name" value="{{ old('guest_name') }}" placeholder="Contoh: Budi Santoso" class="block w-full rounded-xl border-slate-200 bg-white py-3 pl-10 text-sm font-bold text-slate-800 shadow-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Nomor WhatsApp</label>
                                    <div class="relative mt-2">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l.547 2.19c.24.96-.183 1.961-.993 2.526L5.7 8.79a15.098 15.098 0 0 0 6.52 6.52l1.134-1.547c.565-.81 1.567-1.233 2.527-.993l2.19.547a1.75 1.75 0 0 1 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z" clip-rule="evenodd" /></svg>
                                        </span>
                                        <input name="guest_phone" value="{{ old('guest_phone') }}" inputmode="tel" placeholder="Contoh: 08123456789" class="block w-full rounded-xl border-slate-200 bg-white py-3 pl-10 text-sm font-bold text-slate-800 shadow-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" required>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Tamu Mengetahui Villa Dari</label>
                                    <div class="relative mt-2">
                                        <x-custom-select
                                            name="acquisition_source"
                                            :options="[
                                                'WhatsApp' => 'WhatsApp',
                                                'Walk-in' => 'Walk-in',
                                                'Telepon' => 'Telepon',
                                                'Instagram' => 'Instagram',
                                                'Google' => 'Google',
                                                'Traveloka' => 'Traveloka',
                                                'Lainnya' => 'Lainnya',
                                            ]"
                                            selected="{{ old('acquisition_source') }}"
                                            placeholder="Pilih sumber akuisisi"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Jumlah Dewasa</label>
                                    <input name="adult_count" type="number" min="1" max="50" value="{{ old('adult_count', 1) }}" class="mt-2 block w-full rounded-xl border-slate-200 bg-white py-3 px-4 text-sm font-bold text-slate-800 shadow-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Jumlah Anak</label>
                                    <input name="child_count" type="number" min="0" max="50" value="{{ old('child_count', 0) }}" class="mt-2 block w-full rounded-xl border-slate-200 bg-white py-3 px-4 text-sm font-bold text-slate-800 shadow-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700">Jumlah Unit/Kamar</label>
                                    <input name="unit_count" type="number" min="1" max="20" value="{{ old('unit_count', 1) }}" class="mt-2 block w-full rounded-xl border-slate-200 bg-white py-3 px-4 text-sm font-bold text-slate-800 shadow-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" required>
                                    <p class="mt-1.5 text-[11px] font-semibold text-slate-500">Untuk tipe yang tidak mengizinkan banyak unit, sistem otomatis memakai 1 unit.</p>
                                </div>
                            </div>

                            <!-- Pemilihan Kamar Dinamis -->
                            <div>
                                <h4 class="text-sm font-black text-neutral-900 uppercase tracking-wider mb-3">Pilih Unit Kamar Tersedia</h4>
                                <div class="grid gap-4">
                                    @foreach ($rooms as $room)
                                        @php
                                            $isFirst = $loop->first && !old('room_id');
                                            $isChecked = (string) old('room_id') === (string) $room->id || $isFirst;
                                            $availableUnits = $room->availableUnitCount($checkIn, $checkOut);
                                        @endphp
                                        <label data-room-card class="group relative block cursor-pointer rounded-2xl border bg-white p-5 transition-all duration-300 hover:border-emerald-500 hover:shadow-lg hover:-translate-y-1 {{ $isChecked ? 'room-card-selected' : 'border-slate-200' }}">
                                            
                                            <div class="grid gap-5 sm:grid-cols-[auto_1fr_auto] sm:items-center">
                                                <!-- Radio Button Tersembunyi -->
                                                <div class="flex items-center">
                                                    <div class="custom-radio-outer relative flex items-center justify-center h-6 w-6 rounded-full border-2 transition-colors group-hover:border-emerald-500 {{ $isChecked ? 'border-emerald-500 bg-emerald-50' : 'border-slate-300' }}">
                                                        <div class="custom-radio-inner h-2.5 w-2.5 rounded-full bg-emerald-500 transition-transform {{ $isChecked ? 'scale-100' : 'scale-0' }}"></div>
                                                    </div>
                                                    <input type="radio" name="room_id" value="{{ $room->id }}" @checked($isChecked) class="sr-only" required>
                                                </div>
                                                
                                                <!-- Informasi Kamar -->
                                                <div class="space-y-2">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="text-lg font-black text-slate-900">{{ $room->name }}</span>
                                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">Kapasitas {{ $room->included_capacity }}-{{ $room->max_capacity }} Org</span>
                                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200/50">{{ $availableUnits }} unit tersedia</span>
                                                    </div>
                                                    
                                                    @if ($room->facilities)
                                                        <div class="flex flex-wrap gap-1.5 pt-1">
                                                            @foreach (array_slice($room->facilities, 0, 4) as $facility)
                                                                <span class="inline-flex items-center rounded-lg bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-600 group-hover:bg-white group-hover:text-emerald-800 transition-colors ring-1 ring-slate-200/50">{{ $facility }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Rincian Harga Kamar -->
                                                <div class="text-left sm:text-right border-t border-slate-100 pt-4 sm:border-t-0 sm:pt-0">
                                                    <span class="block text-xs font-semibold text-slate-400">Tarif {{ $nights }} Malam</span>
                                                    <span class="block text-2xl font-black text-slate-900 mt-0.5">Rp {{ number_format($room->price * $nights, 0, ',', '.') }}</span>
                                                    <span class="block text-xs font-semibold text-slate-400 mt-1">(Rp {{ number_format($room->price, 0, ',', '.') }}/malam)</span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Tombol Aksi Akhir -->
                            <button type="submit" class="group w-full flex items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 py-4 text-sm font-black text-white shadow-[0_8px_20px_-6px_rgba(16,185,129,0.5)] transition-all duration-300 hover:from-emerald-500 hover:to-teal-500 hover:shadow-[0_12px_24px_-6px_rgba(16,185,129,0.6)] hover:-translate-y-0.5 active:scale-[0.98]">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 transition-transform group-hover:scale-110"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z" clip-rule="evenodd" /></svg>
                                Buat Pemesanan Tamu Sekarang
                            </button>
                        </form>
                    @elseif ($checkIn && $checkOut)
                        <div class="mt-6 rounded-3xl border-2 border-dashed border-neutral-200 bg-neutral-50/50 p-10 text-center">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-amber-600 ring-4 ring-amber-50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                            </span>
                            <h4 class="mt-4 text-lg font-black text-neutral-950">Tidak Ada Kamar Tersedia</h4>
                            <p class="mt-2 text-sm font-medium leading-relaxed text-neutral-500 max-w-sm mx-auto">Semua unit kamar kami telah terisi penuh untuk periode inap ini. Coba ganti tanggal lain atau periksa status perawatan kamar.</p>
                        </div>
                    @else
                        <div class="mt-6 rounded-3xl border-2 border-dashed border-neutral-200 bg-neutral-50/50 p-10 text-center">
                            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 ring-4 ring-emerald-50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                            </span>
                            <h4 class="mt-4 text-lg font-black text-neutral-950">Mulai Dengan Pilih Tanggal</h4>
                            <p class="mt-2 text-sm font-medium leading-relaxed text-neutral-500 max-w-sm mx-auto">Pilih periode menginap di menu sebelah kiri terlebih dahulu agar sistem dapat mencari kamar yang berstatus kosong dan siap dipesan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- STYLE & SCRIPT TAMBAHAN -->
    <style>
        .calendar-nav-btn {
            display: inline-flex;
            height: 2.25rem;
            width: 2.25rem;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            border: 1px solid rgb(229 231 235);
            background: rgba(255, 255, 255, 0.95);
            color: rgb(23 23 23);
            box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.08);
            transition: all 160ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .calendar-nav-btn:hover {
            transform: translateY(-1px);
            border-color: rgb(16 185 129);
            background: rgb(236 253 245);
            color: rgb(4 120 87);
        }
        .calendar-nav-btn:disabled {
            cursor: not-allowed;
            opacity: 0.36;
            transform: none;
        }
        .calendar-day {
            position: relative;
            min-height: 2.2rem;
            border-radius: 0.75rem;
            font-size: 0.8rem;
            font-weight: 800;
            color: rgb(38 38 38);
            transition: all 140ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .calendar-day:not(:disabled):hover {
            transform: translateY(-1px);
            background: rgb(236 253 245);
            color: rgb(6 95 70);
            box-shadow: 0 4px 12px -2px rgba(16, 185, 129, 0.15);
        }
        .calendar-day.is-muted {
            color: rgb(212 212 212);
        }
        .calendar-day.is-disabled {
            cursor: not-allowed;
            color: rgb(190 190 190);
            opacity: 0.4;
            text-decoration: line-through;
        }
        .calendar-day.is-empty {
            visibility: hidden;
            pointer-events: none;
        }
        .calendar-day.is-today {
            font-weight: 900;
            color: rgb(4 120 87);
            border: 1.5px solid rgb(209 250 229);
        }
        .calendar-day.is-in-range {
            background: rgb(209 250 229);
            color: rgb(6 78 59);
            border-radius: 0;
        }
        .calendar-day.is-selected,
        .calendar-day.is-checkout {
            background: rgb(4 120 87) !important; /* emerald-700 */
            color: white !important;
            box-shadow: 0 8px 20px -6px rgba(4, 120, 87, 0.6);
        }
        
        /* Premium Card States */
        .room-card-selected {
            border-color: rgb(16 185 129) !important;
            background-color: rgb(240 253 250) !important;
            box-shadow: 0 10px 25px -5px rgba(20, 184, 166, 0.1), 0 8px 10px -6px rgba(20, 184, 166, 0.05) !important;
        }
        .room-card-selected .custom-radio-outer {
            border-color: rgb(16 185 129) !important;
            background-color: rgb(236 253 245) !important;
        }
        .room-card-selected .custom-radio-inner {
            transform: scale(1) !important;
        }
        .custom-radio-inner {
            transform: scale(0);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initializeDatePickers();

            // Event listener untuk radio button pilihan kamar agar memberikan efek visual interaktif
            var roomCards = document.querySelectorAll('[data-room-card]');
            var radioInputs = document.querySelectorAll('input[name="room_id"]');

            if (radioInputs.length > 0) {
                radioInputs.forEach(function (radio) {
                    radio.addEventListener('change', function () {
                        roomCards.forEach(function (card) {
                            card.classList.remove('room-card-selected');
                            card.classList.add('border-slate-200');
                        });

                        var selectedCard = radio.closest('[data-room-card]');
                        if (selectedCard) {
                            selectedCard.classList.remove('border-slate-200');
                            selectedCard.classList.add('room-card-selected');
                        }
                    });
                });
            }

            // Validasi submit form jika ada kalender yang kosong
            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.method.toLowerCase() !== 'post' && form.method.toLowerCase() !== 'get') {
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

        // FUNGSI UTAMA INITIALIZE DATEPICKERS
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

                if (prevButton) {
                    prevButton.addEventListener('click', function () {
                        viewDate = addMonths(viewDate, -1);
                        renderCalendar();
                    });
                }

                if (nextButton) {
                    nextButton.addEventListener('click', function () {
                        viewDate = addMonths(viewDate, 1);
                        renderCalendar();
                    });
                }
                
                if (input && input.name === 'check_out_date') {
                    document.addEventListener('check_in_date_changed', function(e) {
                        var form = calendar.closest('form');
                        var sourceForm = e.target.closest('form');
                        
                        if (form === sourceForm) {
                            var newCheckIn = e.detail.date;
                            if (newCheckIn) {
                                minDate = addDays(newCheckIn, 1); 
                                calendar.dataset.minDate = formatDate(minDate);
                                
                                var currentVal = parseLocalDate(input.value);
                                if (currentVal && currentVal < minDate) {
                                    input.value = ''; 
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
                    
                    var otherInput = null;
                    var form = calendar.closest('form');
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

                    if (monthLabel) {
                        monthLabel.textContent = monthStart.toLocaleDateString(locale, { month: 'long', year: 'numeric' });
                    }
                    if (prevButton) {
                        prevButton.disabled = monthStart <= minMonth;
                    }
                    
                    if (daysGrid) {
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
                    }

                    if (display) {
                        display.textContent = selectedDate ? formatDisplayDate(selectedDate, locale) : calendar.dataset.emptyLabel;
                    }
                }

                function selectDate(date) {
                    input.value = formatDate(date);
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    if (input.name === 'check_in_date') {
                        input.dispatchEvent(new CustomEvent('check_in_date_changed', { 
                            detail: { date: date },
                            bubbles: true 
                        }));
                    }

                    window.setTimeout(function () {
                        closeCalendarPanel(calendar);
                    }, 220);

                    renderCalendar();
                }
            });
        }

        // PANEL DATEPICKER ACTIONS
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

        // DATE UTILITY FUNCTIONS
        function buildWeekdays(container, locale) {
            if (!container) return;
            var baseMonday = new Date(2026, 0, 5); // Suatu hari senin
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
    </script>
</x-app-layout>

