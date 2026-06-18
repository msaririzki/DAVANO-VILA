<x-app-layout>
    <div class="bg-slate-50 min-h-screen pb-12">
        
        <!-- Compact Header -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight leading-none">Ringkasan Villa</h1>
                        <p class="text-slate-500 text-sm font-medium mt-1">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if (auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.reports') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Laporan
                        </a>
                        <a href="{{ route('admin.web-settings') }}" class="px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </a>
                    @endif
                    <a href="{{ route('bookings.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold transition-all shadow-sm shadow-emerald-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Buat Pesanan
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            
            @if (session('status'))
                <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-3 flex items-center gap-3">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <p class="text-sm font-semibold text-emerald-800">{{ session('status') }}</p>
                </div>
            @endif

            <!-- Compact Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Today's Activity (Premium Green Card) -->
                <div class="bg-gradient-to-br from-emerald-800 to-teal-900 rounded-2xl p-5 relative overflow-hidden shadow-lg shadow-emerald-900/10 col-span-1 md:col-span-2 lg:col-span-1">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-white opacity-5 rounded-full blur-2xl"></div>
                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <h3 class="text-emerald-100 text-[10px] font-bold uppercase tracking-widest mb-3">Jadwal Hari Ini</h3>
                        <div class="flex gap-3">
                            <div class="flex-1 bg-white/10 rounded-xl p-3 backdrop-blur-sm border border-white/10">
                                <p class="text-3xl font-black text-white leading-none">{{ $todayCheckIns }}</p>
                                <p class="text-[10px] font-bold text-emerald-200 mt-1 uppercase tracking-wide">Tamu Masuk</p>
                            </div>
                            <div class="flex-1 bg-white/10 rounded-xl p-3 backdrop-blur-sm border border-white/10">
                                <p class="text-3xl font-black text-white leading-none">{{ $todayCheckOuts }}</p>
                                <p class="text-[10px] font-bold text-amber-300 mt-1 uppercase tracking-wide">Tamu Keluar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Required -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-[0_2px_10px_rgb(0,0,0,0.02)] flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-2">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">{{ auth()->user()->isSuperAdmin() ? 'Perlu Diproses' : 'Belum Disahkan' }}</p>
                        <div class="w-7 h-7 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('dashboard', ['status_filter' => auth()->user()->isSuperAdmin() ? 'needs_check' : 'awaiting_dp']).'#pesanan' }}" class="inline-flex items-baseline gap-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400/40">
                            <span class="text-3xl font-black text-slate-800 tracking-tight">{{ auth()->user()->isSuperAdmin() ? $actionRequiredCount : $pendingCount }}</span>
                            <span class="text-[10px] font-bold text-slate-400">pesanan</span>
                        </a>
                        <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-[10px] font-bold">
                            <a href="{{ route('dashboard', ['status_filter' => 'awaiting_dp']).'#pesanan' }}" class="rounded-md bg-amber-50 px-2 py-1 text-amber-700 transition hover:bg-amber-100">{{ $pendingCount }} belum disahkan</a>
                            @if (auth()->user()->isSuperAdmin())
                                <a href="{{ route('dashboard', ['status_filter' => 'transfer_issue']).'#pesanan' }}" class="rounded-md bg-rose-50 px-2 py-1 text-rose-700 transition hover:bg-rose-100">{{ $transferIssueCount }} perlu keputusan</a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Balance -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-[0_2px_10px_rgb(0,0,0,0.02)] flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Sisa Pembayaran</p>
                            <p class="mt-1 text-[10px] font-semibold text-slate-400">Pesanan yang sudah bayar DP</p>
                        </div>
                        <div class="w-7 h-7 rounded-full bg-sky-50 flex items-center justify-center text-sky-500">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Rp {{ number_format($balanceDue, 0, ',', '.') }}</h3>
                </div>

                <!-- Revenue -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-[0_2px_10px_rgb(0,0,0,0.02)] flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-2">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Pendapatan {{ now()->locale('id')->isoFormat('MMMM') }}</p>
                        <div class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</h3>
                </div>

            </div>

            <!-- Table Section -->
            <div id="pesanan" class="scroll-mt-6 bg-white rounded-2xl shadow-[0_2px_10px_rgb(0,0,0,0.02)] border border-slate-200/60 overflow-hidden mt-2">
                <div class="border-b border-slate-100 px-5 py-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <h3 class="text-base font-bold text-slate-800">Pesanan Terbaru</h3>
                        @php
                            $currentFilter = request('filter', 'all');
                            $currentStatusFilter = request('status_filter', 'all');
                            $currentPerPage = request('per_page', 10);
                            $dashboardQuery = request()->except('page');
                        @endphp
                        <div class="flex items-center justify-between gap-3">
                            @if (auth()->user()->isSuperAdmin())
                                <a href="{{ route('admin.reports') }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-700">
                                    Lihat Semua Pesanan
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @endif
                            <span class="text-[11px] font-bold text-slate-500">Jumlah:</span>
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center justify-between w-24 rounded-xl border border-neutral-200 bg-white px-3 py-2 text-xs font-bold text-neutral-800 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                        <span>{{ $currentPerPage }} data</span>
                                        <svg class="ml-1 h-3.5 w-3.5 text-emerald-600 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('dashboard', array_merge($dashboardQuery, ['per_page' => 10]))" class="text-xs {{ $currentPerPage == 10 ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                        10 data
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('dashboard', array_merge($dashboardQuery, ['per_page' => 30]))" class="text-xs {{ $currentPerPage == 30 ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                        30 data
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('dashboard', array_merge($dashboardQuery, ['per_page' => 100]))" class="text-xs {{ $currentPerPage == 100 ? 'bg-emerald-50 text-emerald-700' : '' }}">
                                        100 data
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-3 xl:grid-cols-[auto_1fr] xl:items-center">
                        <div class="flex items-center gap-2 overflow-x-auto pb-1">
                            <span class="shrink-0 text-[10px] font-black uppercase tracking-wider text-slate-400">Waktu</span>
                            @foreach (['all' => 'Semua Waktu', 'today' => 'Hari Ini', 'week' => '1 Minggu', 'month' => '1 Bulan'] as $value => $label)
                                <a href="{{ route('dashboard', array_merge($dashboardQuery, ['filter' => $value])) }}#pesanan" class="shrink-0 rounded-lg px-3 py-1.5 text-[11px] font-bold transition {{ $currentFilter === $value ? 'bg-slate-800 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 xl:justify-end">
                            <span class="shrink-0 text-[10px] font-black uppercase tracking-wider text-slate-400">Status</span>
                            @php
                                $statusFilters = auth()->user()->isSuperAdmin()
                                    ? [
                                        'all' => 'Semua Pesanan',
                                        'needs_check' => 'Perlu Diproses',
                                        'awaiting_dp' => 'Belum Disahkan',
                                        'active' => 'Pesanan Aktif',
                                        'transfer_issue' => 'Perlu Keputusan',
                                        'expired' => 'Waktu Bayar Habis',
                                    ]
                                    : [
                                        'all' => 'Semua Pesanan',
                                        'awaiting_dp' => 'Belum Disahkan',
                                        'active' => 'Pesanan Aktif',
                                        'expired' => 'Waktu Bayar Habis',
                                    ];
                            @endphp
                            @foreach ($statusFilters as $value => $label)
                                <a href="{{ route('dashboard', array_merge($dashboardQuery, ['status_filter' => $value])) }}#pesanan" class="shrink-0 rounded-lg px-3 py-1.5 text-[11px] font-bold transition {{ $currentStatusFilter === $value ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-3 flex flex-col gap-1.5 rounded-xl border border-sky-100 bg-sky-50/70 px-3 py-2 text-[11px] font-semibold text-slate-600 sm:flex-row sm:gap-5">
                        <p><span class="font-black text-amber-700">Belum Disahkan:</span> waktu bayar masih aktif dan transfer belum dicatat sebagai DP.</p>
                        @if (auth()->user()->isSuperAdmin())
                            <p><span class="font-black text-rose-700">Perlu Keputusan:</span> transfer ditemukan setelah waktu habis atau kamar sudah tidak tersedia.</p>
                        @else
                            <p><span class="font-black text-slate-700">Waktu Bayar Habis:</span> pesanan tidak dihitung aktif kecuali ditemukan transfer.</p>
                        @endif
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tamu</th>
                                <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kamar & Lama Menginap</th>
                                <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pembayaran</th>
                                <th class="px-5 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-right text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($bookings as $booking)
                                @php
                                    $hasTransferIssue = $booking->payments->contains(
                                        fn ($payment) => $payment->type === \App\Models\Payment::TYPE_TRANSFER_ISSUE
                                            && $payment->resolution_status === \App\Models\Payment::RESOLUTION_UNRESOLVED
                                    );
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                                    <td class="px-5 py-3">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-800">{{ $booking->guest_name }}</span>
                                            <span class="text-xs font-semibold text-slate-500">{{ $booking->guest_phone }}</span>
                                            <span class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-wider">{{ $booking->booking_code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-800">{{ $booking->room->name }}</span>
                                            <span class="text-xs font-semibold text-slate-500">
                                                {{ $booking->check_in_date->translatedFormat('d M') }} - {{ $booking->check_out_date->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="text-[10px] font-bold text-emerald-600 mt-0.5 uppercase tracking-wider">{{ $booking->check_in_date->diffInDays($booking->check_out_date) }} Malam</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-col items-start gap-1">
                                            @if($booking->payment_status === 'Lunas')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200/60">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    Lunas
                                                </span>
                                            @elseif($booking->payment_status === 'DP')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 text-[11px] font-bold border border-sky-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                                    Sudah Bayar DP
                                                </span>
                                                <span class="text-[11px] font-bold text-slate-500 mt-0.5">Sisa Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</span>
                                            @elseif($booking->payment_status === \App\Models\Booking::PAYMENT_CANCELLED)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-bold border border-slate-200">
                                                    Dibatalkan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200/60">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    Belum Bayar
                                                </span>
                                                <span class="text-[11px] font-bold text-slate-500 mt-0.5">Total Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        @if ($hasTransferIssue)
                                            <span class="inline-flex rounded-md border border-rose-200 bg-rose-50 px-2 py-1 text-xs font-bold text-rose-700">Menunggu Keputusan</span>
                                        @elseif ($booking->isInAdminGracePeriod())
                                            <span class="inline-flex rounded-md border border-sky-200 bg-sky-50 px-2 py-1 text-xs font-bold text-sky-700">Toleransi Admin</span>
                                        @elseif ($booking->hasExpiredHold())
                                            <span class="inline-flex rounded-md border border-slate-200 bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">Waktu Bayar Habis</span>
                                        @elseif ($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING && $booking->booking_status === \App\Models\Booking::STATUS_BOOKED)
                                            <span class="inline-flex rounded-md border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-bold text-amber-700">Belum Disahkan</span>
                                        @elseif ($booking->booking_status === \App\Models\Booking::STATUS_BOOKED)
                                            <span class="inline-flex rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700">Pesanan Aktif</span>
                                        @else
                                            <span class="text-sm font-bold text-slate-700">{{ [
                                                'In-House' => 'Sedang Menginap',
                                                'Completed' => 'Selesai',
                                                'No-Show' => 'Tamu Tidak Datang',
                                                'Cancelled' => 'Dibatalkan',
                                            ][$booking->booking_status] ?? $booking->booking_status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        @php
                                            $actionAnchor = $hasTransferIssue
                                                ? '#transfer-decision'
                                                : (($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING) ? '#payment-validation' : '');
                                        @endphp
                                        <a href="{{ route('bookings.show', $booking).$actionAnchor }}" onclick="event.stopPropagation()" class="inline-flex items-center justify-center px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-emerald-700 transition-all shadow-sm">
                                            @if (auth()->user()->isSuperAdmin() && $hasTransferIssue)
                                                Ambil Keputusan
                                            @elseif (auth()->user()->isSuperAdmin() && $booking->payment_status === 'Pending' && ($booking->hasActiveHold() || ! $booking->hold_expires_at))
                                                Validasi Pembayaran
                                            @elseif ($booking->payment_status === 'Pending' && $booking->hasExpiredHold())
                                                {{ auth()->user()->isSuperAdmin() ? 'Cek Mutasi Bank' : 'Lihat Pesanan' }}
                                            @elseif ($booking->booking_status === 'Booked' && in_array($booking->payment_status, ['DP', 'Lunas'], true))
                                                Check-in Tamu
                                            @elseif ($booking->booking_status === 'In-House' && $booking->payment_status !== 'Lunas')
                                                Cek Sisa Bayar
                                            @elseif ($booking->booking_status === 'In-House')
                                                Check-out Tamu
                                            @else
                                                Lihat Detail
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-300 mb-3">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-800">Tidak ada pesanan</h3>
                                        <p class="mt-1 text-xs text-slate-500">Coba pilih waktu atau status yang lain.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($bookings->hasPages())
                    <div class="px-5 py-4 border-t border-slate-100">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</x-app-layout>
