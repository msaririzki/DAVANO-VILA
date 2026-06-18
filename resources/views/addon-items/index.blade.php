<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">Layanan Tambahan</h2>
                        <p class="mt-0.5 text-xs font-semibold text-slate-500">Kelola menu dan layanan tambahan untuk tamu</p>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">Menu</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.web-settings') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all font-bold text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Ke Pengaturan Web
            </a>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-slate-50 pt-6 pb-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
            
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

            <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr] items-start">
                <!-- FORM TAMBAH ADD-ON -->
                <section class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
                    <div class="absolute right-0 top-0 -mr-6 -mt-6 h-24 w-24 rounded-full bg-emerald-500/5 blur-xl"></div>
                    
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Layanan Baru</p>
                    <h3 class="mt-1 text-2xl font-black text-neutral-950">Tambah Menu</h3>
                    <p class="mt-1.5 text-xs font-semibold text-neutral-500">Buat menu makanan, camilan, minuman, atau kasur tambahan untuk transaksi tamu.</p>
                    
                    <form method="POST" action="{{ route('addon-items.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Nama Menu / Item</label>
                            <input name="name" placeholder="Contoh: Nasi Goreng Spesial Sembalun" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-neutral-700 mb-1.5">Kategori Menu</label>
                            <x-custom-select
                                name="category"
                                :options="$categoryOptions"
                                :selected="old('category', 'makanan')"
                                placeholder="Pilih Kategori"
                                :required="true"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-neutral-700">Tarif / Harga (Rp)</label>
                            <input name="price" type="number" min="0" step="1" placeholder="Contoh: 25000" class="mt-1.5 block w-full rounded-xl border-neutral-200 text-sm font-semibold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm font-bold text-neutral-700 cursor-pointer">
                            <input name="is_active" type="checkbox" value="1" checked class="rounded border-neutral-300 text-emerald-600 focus:ring-emerald-500">
                            Aktifkan langsung di sistem pemesanan
                        </label>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-neutral-950 py-3.5 text-sm font-bold text-white shadow-md transition-all hover:bg-emerald-800 hover:-translate-y-0.5 active:scale-[0.98]">
                            Tambah Layanan
                        </button>
                    </form>
                </section>

                <!-- DAFTAR ADD-ONS -->
                <section class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
                    <div class="border-b border-neutral-100 px-6 py-5">
                        <h3 class="text-xl font-black text-neutral-950">Daftar Layanan Tersedia</h3>
                        <p class="mt-1 text-sm font-semibold text-neutral-500">Sesuaikan tarif serta keaktifan item tambahan dalam sistem transaksi.</p>
                    </div>

                    <div class="divide-y divide-neutral-100">
                        @forelse ($addonItems as $addonItem)
                            <form method="POST" action="{{ route('addon-items.update', $addonItem) }}" class="p-6 transition-colors hover:bg-neutral-50/50">
                                @csrf
                                @method('PATCH')
                                <div class="grid gap-4 sm:grid-cols-3">
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-black uppercase tracking-wider text-neutral-400">Nama Menu / Layanan</label>
                                        <input name="name" value="{{ $addonItem->name }}" class="mt-1 block w-full rounded-xl border-neutral-200 text-sm font-bold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-[10px] font-black uppercase tracking-wider text-neutral-400 mb-1">Kategori</label>
                                        <x-custom-select
                                            name="category"
                                            :options="$categoryOptions"
                                            :selected="$addonItem->category"
                                            placeholder="Pilih Kategori"
                                            :required="true"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-wider text-neutral-400">Harga (Rp)</label>
                                        <input name="price" type="number" min="0" step="1" value="{{ (int) $addonItem->price }}" class="mt-1 block w-full rounded-xl border-neutral-200 text-sm font-bold focus:border-emerald-600 focus:ring-emerald-600/20" required>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between gap-3 border-t border-neutral-100/60 pt-4">
                                    <label class="inline-flex items-center gap-2 rounded-xl bg-neutral-100 px-3 py-2 text-xs font-bold text-neutral-700 cursor-pointer transition-colors hover:bg-neutral-200/60">
                                        <input name="is_active" type="checkbox" value="1" @checked($addonItem->is_active) class="rounded border-neutral-300 text-emerald-600 focus:ring-emerald-500">
                                        Status Aktif
                                    </label>
                                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-neutral-200 bg-white px-5 py-2 text-xs font-bold text-neutral-800 shadow-sm transition-all hover:border-neutral-950 hover:bg-neutral-50 hover:-translate-y-0.5 active:scale-95">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        @empty
                            <div class="flex flex-col items-center justify-center p-12 text-center">
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-100 text-neutral-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                </span>
                                <h4 class="mt-4 text-base font-black text-neutral-900">Belum Ada Layanan Tambahan</h4>
                                <p class="mt-1 text-xs font-semibold text-neutral-500 max-w-xs leading-relaxed">Tambahkan layanan pertama melalui formulir di sebelah kiri.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>

