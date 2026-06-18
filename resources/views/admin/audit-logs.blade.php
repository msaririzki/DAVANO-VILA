<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.977 11.977 0 0 1 12 2.714Z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">Riwayat Aktivitas Penting</h2>
                        <p class="mt-0.5 text-xs font-semibold text-slate-500">Catatan perubahan dan tindakan sensitif</p>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">Aktivitas</span>
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
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- SECURE BANNER -->
            <div class="relative overflow-hidden rounded-3xl border border-amber-100 bg-gradient-to-br from-amber-500/10 to-orange-500/5 p-5 shadow-sm">
                <div class="absolute -right-8 -bottom-8 h-32 w-32 rounded-full bg-amber-500/10 blur-2xl"></div>
                <div class="flex gap-4 items-start">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.977 11.977 0 0 1 12 2.714Z" /></svg>
                    </span>
                    <div>
                        <h4 class="text-base font-black text-amber-950">Protokol Pengawasan Keamanan</h4>
                        <p class="mt-1 text-xs font-bold leading-relaxed text-amber-900/80 max-w-4xl">Halaman ini digunakan untuk mengaudit jika terdeteksi perubahan rekening bank, validasi pembayaran reservasi, pemberian diskon, pergeseran status operasional unit, maupun manipulasi master data yang mencurigakan. Seluruh data rekam jejak ini dikunci secara permanen dan tidak dapat dihapus.</p>
                    </div>
                </div>
            </div>

            <!-- TABLE CONTAINER -->
            <section class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/90 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.08)] backdrop-blur-md">
                <div class="border-b border-neutral-100/60 px-6 py-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-xl font-black text-neutral-950">Riwayat Aktivitas</h3>
                        <p class="mt-1 text-xs font-semibold text-neutral-500 font-sans">Menampilkan daftar catatan kronologis operasional (30 data per halaman).</p>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-neutral-900 px-3.5 py-1.5 text-xs font-black uppercase tracking-wide text-white shadow-sm">Keamanan Aktif</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-100 text-sm font-sans">
                        <thead class="bg-neutral-50/70 text-left text-xs font-black uppercase tracking-wider text-neutral-500">
                            <tr>
                                <th class="px-6 py-4">Waktu Aktivitas</th>
                                <th class="px-6 py-4">Staf / Staf Ahli</th>
                                <th class="px-6 py-4">Kategori Aksi</th>
                                <th class="px-6 py-4">Ringkasan Riwayat</th>
                                <th class="px-6 py-4">Alamat IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse ($auditLogs as $auditLog)
                                @php
                                    $userName = $auditLog->user?->name ?? 'Sistem';
                                    $isSystem = !$auditLog->user;
                                    $avatarChar = Str::upper(Str::substr($userName, 0, 1));
                                    
                                    // Dinamisasi badge berdasarkan tipe aksi
                                    $action = [
                                        'room.created' => 'Kamar Ditambahkan',
                                        'room.updated' => 'Kamar Diperbarui',
                                        'room.status.updated' => 'Status Kamar Diubah',
                                        'booking.created.internal' => 'Pemesanan Dibuat',
                                        'booking.status.updated' => 'Status Pemesanan Diubah',
                                        'booking.units.updated' => 'Unit Kamar Ditetapkan',
                                        'booking.payment.validated' => 'Pembayaran Divalidasi',
                                        'booking.adjustment.updated' => 'Tagihan Disesuaikan',
                                        'booking.addon.created' => 'Layanan Ditambahkan',
                                        'booking.addon.payment.validated' => 'Pembayaran Layanan Divalidasi',
                                        'addon.created' => 'Layanan Dibuat',
                                        'addon.updated' => 'Layanan Diperbarui',
                                        'bank-account.created' => 'Rekening Ditambahkan',
                                        'bank-account.updated' => 'Rekening Diperbarui',
                                        'public-media.updated' => 'Tampilan Publik Diubah',
                                    ][$auditLog->action] ?? str($auditLog->action)->replace(['.', '_', '-'], ' ')->title();
                                    $badgeClass = 'bg-neutral-100 text-neutral-800 border-neutral-200';
                                    if (Str::contains($action, ['created', 'store', 'create'])) {
                                        $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-100/50';
                                    } elseif (Str::contains($action, ['payment', 'addons'])) {
                                        $badgeClass = 'bg-indigo-50 text-indigo-800 border-indigo-100/50';
                                    } elseif (Str::contains($action, ['update', 'status', 'adjustment'])) {
                                        $badgeClass = 'bg-amber-50 text-amber-800 border-amber-100/50';
                                    } elseif (Str::contains($action, ['destroy', 'delete'])) {
                                        $badgeClass = 'bg-rose-50 text-rose-800 border-rose-100/50';
                                    }
                                @endphp
                                <tr class="align-top hover:bg-neutral-50/40 transition-colors">
                                    <!-- Waktu -->
                                    <td class="whitespace-nowrap px-6 py-5 font-bold text-neutral-800">
                                        <div class="flex items-center gap-1.5 text-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-neutral-400"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                            {{ $auditLog->created_at->format('d M Y') }}
                                        </div>
                                        <div class="text-[10px] font-semibold text-neutral-400 mt-0.5 pl-5">Jam {{ $auditLog->created_at->format('H:i:s') }}</div>
                                    </td>
                                    
                                    <!-- User Avatar & Info -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            @if ($isSystem)
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-neutral-900 text-white font-black text-xs ring-2 ring-neutral-200">SYS</span>
                                            @else
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 font-black text-sm uppercase ring-2 ring-emerald-50">{{ $avatarChar }}</span>
                                            @endif
                                            <div>
                                                <div class="font-bold text-neutral-950 leading-tight">{{ $userName }}</div>
                                                <div class="text-xs font-semibold text-neutral-400 mt-0.5">{{ $auditLog->user?->email ?: 'Layanan Otomatis' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Kategori Aksi -->
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <span class="inline-flex rounded-lg border px-2.5 py-1 text-[10px] font-black uppercase tracking-wider {{ $badgeClass }}">{{ $action }}</span>
                                    </td>
                                    
                                    <!-- Ringkasan & JSON diff -->
                                    <td class="min-w-[24rem] px-6 py-5">
                                        <p class="font-bold leading-relaxed text-neutral-800 text-xs">{{ $auditLog->summary }}</p>
                                        
                                        @if ($auditLog->old_values || $auditLog->new_values)
                                            <details class="group mt-3 rounded-2xl border border-neutral-200/50 bg-neutral-50/50 overflow-hidden transition-all duration-300">
                                                <summary class="flex cursor-pointer items-center justify-between px-4 py-2.5 text-[10px] font-black uppercase tracking-wider text-neutral-500 hover:text-emerald-700 transition-colors select-none focus:outline-none">
                                                    <span class="flex items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                                        Rincian Data Perubahan
                                                    </span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3 transition-transform duration-300 group-open:rotate-180"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                                </summary>
                                                
                                                <div class="border-t border-neutral-200/50 p-4 bg-neutral-900 grid gap-4 md:grid-cols-2">
                                                    <!-- Data Lama -->
                                                    <div>
                                                        <div class="text-[9px] font-black text-neutral-400 uppercase tracking-widest mb-1.5 flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>Sebelum Perubahan</div>
                                                        <pre class="overflow-auto rounded-xl bg-black/40 p-3 text-[10px] font-mono text-emerald-400 max-h-48 scrollbar-thin scrollbar-thumb-neutral-800">{!! json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}</pre>
                                                    </div>
                                                    
                                                    <!-- Data Baru -->
                                                    <div>
                                                        <div class="text-[9px] font-black text-neutral-400 uppercase tracking-widest mb-1.5 flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>Setelah Perubahan</div>
                                                        <pre class="overflow-auto rounded-xl bg-black/40 p-3 text-[10px] font-mono text-emerald-400 max-h-48 scrollbar-thin scrollbar-thumb-neutral-800">{!! json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}</pre>
                                                    </div>
                                                </div>
                                            </details>
                                        @endif
                                    </td>
                                    
                                    <!-- Alamat IP -->
                                    <td class="whitespace-nowrap px-6 py-5 text-neutral-500 font-semibold text-xs">
                                        <div class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 text-neutral-400"><path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l.406.34c.15.124.325.22.515.283l.18.06a.75.75 0 0 1 .402.402l.06.18c.063.19.16.365.283.515l.34.406c.213.257.53.405.864.405h.568L20.25 9.75v.568c0 .334-.148.65-.405.864l-.406.34a.75.75 0 0 1-.515.283l-.18.06a.75.75 0 0 0-.402.402l-.06.18a.75.75 0 0 1-.283.515l-.34.406c-.213.257-.53.405-.864.405h-.568L12.75 14.25v.568c0 .334.148.65.405.864l.406.34c.15.124.325.22.515.283l.18.06a.75.75 0 0 1 .402.402l.06.18c.063.19.16.365.283.515l.34.406c.213.257.53.405.864.405h.568L20.25 15.75v.568c0 .334-.148.65-.405.864l-.406.34a.75.75 0 0 1-.515.283l-.18.06a.75.75 0 0 0-.402.402l-.06.18a.75.75 0 0 1-.283.515l-.34.406c-.213.257-.53.405-.864.405h-.568L12.75 20.25" /></svg>
                                            {{ $auditLog->ip_address }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-neutral-500 font-medium">
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-100 text-neutral-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                            </span>
                                            <h4 class="mt-4 text-base font-black text-neutral-900">Belum Ada Rekaman Audit</h4>
                                            <p class="mt-1 text-xs text-neutral-500 max-w-xs">Jejak aktivitas log sistem masih kosong. Rekaman audit akan otomatis dibuat ketika ada aktivitas sensitif.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION WRAPPER -->
                <div class="border-t border-neutral-100/60 px-6 py-5">
                    {{ $auditLogs->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

