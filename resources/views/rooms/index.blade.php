<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">Daftar Tipe Kamar</h2>
                        <p class="mt-0.5 text-xs font-semibold text-slate-500">Kelola tipe, unit fisik, dan aturan kapasitas</p>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">Kamar</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.web-settings') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all font-bold text-sm shadow-sm">
                    Ke Pengaturan Web
                </a>
                <a href="{{ route('rooms.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-neutral-950 text-white rounded-xl hover:bg-emerald-800 transition-all font-bold text-sm shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5H4.5" /></svg>
                    Tambah Tipe
                </a>
            </div>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-slate-50 pt-6 pb-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="mb-8 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 text-sm font-semibold text-emerald-800 backdrop-blur-sm animate-fade-in">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($rooms as $room)
                    <article class="group relative flex h-full flex-col overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_15px_35px_-15px_rgba(15,23,42,0.05)] transition-all duration-300 hover:border-emerald-200 hover:shadow-lg">
                        
                        <!-- Gambar Kamar -->
                        <div class="relative overflow-hidden aspect-[2.2/1] w-full bg-neutral-100">
                            @if ($room->imageUrl())
                                <img src="{{ $room->imageUrl() }}" alt="{{ $room->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="flex w-full h-full flex-col items-center justify-center bg-neutral-50 text-neutral-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                                    <span class="mt-1 text-xs font-bold uppercase tracking-wider">Belum Ada Foto</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                            
                            <!-- Status Publik -->
                            <span class="absolute right-4 top-4 shrink-0 rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-wider {{ $room->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200/50' : 'bg-neutral-100 text-neutral-600 ring-1 ring-neutral-200' }}">
                                {{ $room->is_active ? 'Tampil di Publik' : 'Disembunyikan' }}
                            </span>
                        </div>

                        <!-- Detail Kamar -->
                        <div class="flex flex-1 flex-col p-6">
                            <div class="space-y-1">
                                <h3 class="text-xl font-black text-neutral-950 tracking-tight transition-colors group-hover:text-emerald-800">{{ $room->name }}</h3>
                                <p class="text-base font-black text-neutral-800">Rp {{ number_format($room->price, 0, ',', '.') }} <span class="text-xs font-semibold text-neutral-400">/ malam</span></p>
                            </div>

                            <p class="mt-4 line-clamp-3 text-sm font-medium leading-relaxed text-neutral-500 lg:min-h-[4.5rem]">{{ $room->description ?: 'Unit kamar belum memiliki deskripsi informasi visual.' }}</p>

                            <!-- Pills Kapsul Kapasitas & Fasilitas -->
                            <div class="mt-6 flex flex-wrap gap-1.5 lg:min-h-[7rem] items-start content-start">
                                <span class="inline-flex items-center rounded-lg bg-neutral-100 px-2.5 py-1 text-[0.68rem] font-bold text-neutral-600 ring-1 ring-neutral-200">
                                    Termasuk {{ $room->included_capacity }} / Maks {{ $room->max_capacity }} tamu
                                </span>
                                <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-[0.68rem] font-bold text-blue-800 ring-1 ring-blue-200/50">
                                    {{ $room->units->where('is_active', true)->count() }} unit fisik
                                </span>
                                <span class="inline-flex items-center rounded-lg bg-violet-50 px-2.5 py-1 text-[0.68rem] font-bold text-violet-800 ring-1 ring-violet-200/50">
                                    Biaya tamu: {{ $room->extra_guest_charge_mode === 'manual' ? 'diatur admin' : 'tidak otomatis' }}
                                </span>
                                <span class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-1 text-[0.68rem] font-bold text-amber-800 ring-1 ring-amber-200/50">
                                    {{ [
                                        'Available' => 'Tersedia',
                                        'Cleaning' => 'Dibersihkan',
                                        'Maintenance' => 'Perbaikan',
                                    ][$room->status] ?? $room->status }}
                                </span>
                                @foreach ($room->facilities ?? [] as $facility)
                                    <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-[0.68rem] font-bold text-emerald-800 ring-1 ring-emerald-200/50">
                                        {{ $facility }}
                                    </span>
                                @endforeach
                            </div>

                            <a href="{{ route('rooms.edit', $room) }}" class="mt-6 inline-flex w-full justify-center items-center gap-1.5 rounded-2xl border border-neutral-200 bg-white px-5 py-3 text-sm font-bold text-neutral-800 shadow-sm transition-all hover:border-neutral-950 hover:bg-neutral-50 hover:-translate-y-0.5 active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-neutral-500"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.83 20.824a4.5 4.5 0 0 1-1.89 1.15l-3 1 1-3a4.5 4.5 0 0 1 1.15-1.9l12.893-12.893Zm0 0L19.5 7.125" /></svg>
                                Ubah Tipe & Unit
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="flex flex-col items-center justify-center p-12 text-center rounded-3xl border-2 border-dashed border-neutral-200 bg-white/50 backdrop-blur-sm md:col-span-2 lg:col-span-3">
                        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-neutral-100 text-neutral-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 21 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 21M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                        </span>
                        <h4 class="mt-4 text-lg font-black text-neutral-950">Belum Ada Kamar Terdaftar</h4>
                        <p class="mt-1 text-sm font-medium text-neutral-500 max-w-sm leading-relaxed">Tambahkan unit kamar pertama Anda sekarang untuk mulai menerima reservasi secara online dan publik.</p>
                        <a href="{{ route('rooms.create') }}" class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-neutral-950 px-6 py-3 text-sm font-bold text-white shadow-md transition-all hover:bg-emerald-800 active:scale-95">
                            Tambah Kamar Pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

