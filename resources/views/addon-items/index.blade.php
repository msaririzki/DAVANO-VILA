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
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">Menu</span>
                    </div>
                    <p class="mt-0.5 text-xs font-semibold text-slate-500">Kelola menu dan layanan tambahan untuk tamu</p>
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
                <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800 shadow-sm">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    </span>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-bold text-rose-800 shadow-sm">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </span>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-12 items-start">
                <!-- FORM TAMBAH ADD-ON -->
                <section class="lg:col-span-4 lg:sticky lg:top-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 border-b border-slate-100 pb-5">
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Tambah Menu Baru</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Buat menu makanan, camilan, atau layanan tambahan untuk tamu.</p>
                    </div>
                    
                    <form method="POST" action="{{ route('addon-items.store') }}" class="space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-slate-700">Nama Menu / Layanan</label>
                            <input name="name" placeholder="Contoh: Nasi Goreng Spesial" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20" required>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-slate-700">Kategori Menu</label>
                            <x-custom-select
                                name="category"
                                :options="$categoryOptions"
                                :selected="old('category', 'makanan')"
                                placeholder="Pilih Kategori"
                                :required="true"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-sm font-bold text-slate-700">Tarif / Harga (Rp)</label>
                            <input name="price" type="number" min="0" step="1" placeholder="Contoh: 25000" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20" required>
                        </div>

                        <div class="pt-2">
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 cursor-pointer transition-colors hover:bg-slate-100 hover:border-slate-300">
                                <input name="is_active" type="checkbox" value="1" checked class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 transition-all">
                                <span class="text-sm font-bold text-slate-700">Aktifkan Langsung</span>
                            </label>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-slate-900 py-3.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-emerald-700 active:scale-[0.98]">
                                Tambah Layanan Baru
                            </button>
                        </div>
                    </form>
                </section>

                <!-- DAFTAR ADD-ONS -->
                <section class="lg:col-span-8 space-y-4">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Daftar Layanan Tersedia</h3>
                                <p class="mt-1 text-sm font-medium text-slate-500">Sesuaikan tarif serta keaktifan item tambahan dalam sistem transaksi.</p>
                            </div>
                            <span class="inline-flex items-center justify-center rounded-xl bg-emerald-50 px-3 py-1.5 text-sm font-black text-emerald-700 ring-1 ring-emerald-200">
                                {{ $addonItems->count() }} Menu
                            </span>
                        </div>
                    </div>

                    <div class="grid gap-4">
                        @forelse ($addonItems as $addonItem)
                            <form method="POST" action="{{ route('addon-items.update', $addonItem) }}" class="group rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:border-emerald-300 hover:shadow-md">
                                @csrf
                                @method('PATCH')

                                <div class="grid gap-4 sm:grid-cols-12 items-start">
                                    <div class="sm:col-span-5 space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Menu</label>
                                        <input name="name" value="{{ $addonItem->name }}" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 transition-colors" required>
                                    </div>

                                    <div class="sm:col-span-4 space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</label>
                                        <x-custom-select
                                            name="category"
                                            :options="$categoryOptions"
                                            :selected="$addonItem->category"
                                            placeholder="Pilih Kategori"
                                            :required="true"
                                        />
                                    </div>

                                    <div class="sm:col-span-3 space-y-1.5">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Harga (Rp)</label>
                                        <input name="price" type="number" min="0" step="1" value="{{ (int) $addonItem->price }}" class="block w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-900 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500/20 transition-colors" required>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100">
                                    <label class="inline-flex items-center gap-3 cursor-pointer">
                                        <div class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $addonItem->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                            <input type="checkbox" name="is_active" value="1" @checked($addonItem->is_active) class="peer sr-only">
                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform peer-checked:translate-x-6 translate-x-1 shadow-sm"></span>
                                        </div>
                                        <span class="text-sm font-bold text-slate-700 select-none">{{ $addonItem->is_active ? 'Status Aktif' : 'Tidak Aktif' }}</span>
                                    </label>

                                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border border-slate-200 px-5 py-2 text-sm font-bold text-slate-700 shadow-sm transition-all hover:bg-slate-50 hover:text-slate-900 active:scale-95 group-hover:border-emerald-300 group-hover:text-emerald-700">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        @empty
                            <div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
                                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                </span>
                                <h4 class="mt-4 text-lg font-black text-slate-900">Belum Ada Layanan Tambahan</h4>
                                <p class="mt-2 text-sm font-medium text-slate-500 max-w-sm leading-relaxed">Anda belum menambahkan menu atau layanan apapun. Gunakan formulir di samping untuk menambahkannya.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>

