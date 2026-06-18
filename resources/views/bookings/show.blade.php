<x-app-layout>
    @php
        $bookingStatusLabels = [
            'Booked' => 'Sudah Dipesan',
            'In-House' => 'Sedang Menginap',
            'Completed' => 'Selesai',
            'No-Show' => 'Tamu Tidak Datang',
            'Cancelled' => 'Dibatalkan',
        ];
        $roomStatusLabels = [
            'Available' => 'Tersedia',
            'Cleaning' => 'Sedang Dibersihkan',
            'Maintenance' => 'Dalam Perbaikan',
        ];
        $sourceLabels = [
            'Instagram' => 'Instagram',
            'Google' => 'Google',
            'Friend' => 'Rekomendasi Teman',
            'TikTok' => 'TikTok',
            'Walk-in' => 'Datang Langsung',
            'Other' => 'Lainnya',
        ];
        $activeAddons = $booking->addons->where('payment_status', '!=', 'Cancelled');
        $cancelledAddons = $booking->addons->where('payment_status', 'Cancelled');
    @endphp
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">{{ $booking->booking_code }}</h2>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">Pemesanan</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $booking->guest_name }} &bull; {{ $booking->room->name }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200/50">Pembayaran: {{ $booking->payment_status }}</span>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200/50">Tamu: {{ $bookingStatusLabels[$booking->booking_status] ?? $booking->booking_status }}</span>
                <a href="{{ route('bookings.invoice', $booking) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs font-bold text-emerald-700 shadow-sm transition-all hover:bg-emerald-50">
                    Unduh Resi PDF
                </a>
                <form method="POST" action="{{ route('bookings.receipt.send', $booking) }}" target="_blank">
                    @csrf
                    <button class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#25D366] px-3 py-1.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-[#1fb958]">
                        Kirim Resi via WhatsApp
                    </button>
                </form>
                <a href="{{ route('dashboard') }}" class="ml-2 inline-flex items-center justify-center gap-2 px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all font-bold text-xs shadow-sm">
                    Kembali ke Operasional
                </a>
            </div>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-slate-50 pt-6 pb-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800 shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700 shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($booking->hasActiveHold())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-900">
                    Unit ditahan sampai {{ $booking->hold_expires_at->translatedFormat('d M Y, H:i') }}. Validasi DP sebelum batas waktu ini.
                </div>
            @elseif ($booking->hasExpiredHold() && $unresolvedTransferIssues->isEmpty())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-800">
                    Hold sudah kedaluwarsa. Jika mutasi transfer ditemukan, catat melalui formulir validasi; sistem akan menandainya sebagai transfer bermasalah.
                </div>
            @endif

            @if ($receiptLinkIsLocal)
                <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs font-semibold leading-5 text-sky-800">
                    Tombol WhatsApp sudah aktif, tetapi tautan resi masih memakai alamat lokal
                    <strong>{{ parse_url($receiptUrl, PHP_URL_HOST) }}</strong>. Tamu hanya dapat membukanya setelah aplikasi menggunakan domain publik pada <code>APP_URL</code>.
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-3">
                <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition-all hover:shadow-md hover:-translate-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Tagihan</p>
                    <p class="mt-1 text-2xl font-black text-slate-900">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</p>
                </div>
                <div class="relative overflow-hidden rounded-3xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all hover:shadow-md hover:-translate-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Sudah Dibayar</p>
                    <p class="mt-1 text-2xl font-black text-emerald-800">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</p>
                </div>
                <div class="relative overflow-hidden rounded-3xl border border-rose-200 bg-rose-50 p-5 shadow-sm transition-all hover:shadow-md hover:-translate-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Sisa Tagihan</p>
                    <p class="mt-1 text-2xl font-black text-rose-800">Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</p>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <!-- Data Tamu -->
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Data Tamu</h3>
                        <div class="mt-4 rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between gap-4"><dt class="text-slate-500 font-medium">Nama Lengkap</dt><dd class="font-bold text-slate-900">{{ $booking->guest_name }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-slate-500 font-medium">No. WhatsApp</dt><dd class="font-bold text-emerald-700">{{ $booking->guest_phone }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-slate-500 font-medium">Sumber Pemesanan</dt><dd class="font-bold text-slate-900">{{ $sourceLabels[$booking->acquisition_source] ?? ($booking->acquisition_source ?: '-') }}</dd></div>
                                <div class="flex justify-between gap-4 pt-2 border-t border-slate-200"><dt class="text-slate-500 font-medium">Jumlah Tamu</dt><dd class="font-bold text-slate-900">{{ $booking->adult_count }} Dewasa, {{ $booking->child_count }} Anak</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-slate-500 font-medium">Jumlah Kamar</dt><dd class="font-bold text-slate-900">{{ $booking->unit_count }} Unit</dd></div>
                                <div class="flex justify-between gap-4 pt-2 border-t border-slate-200"><dt class="text-slate-500 font-medium">Check-in</dt><dd class="font-bold text-slate-900">{{ $booking->check_in_date->translatedFormat('d M Y') }}, 14:00</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-slate-500 font-medium">Check-out</dt><dd class="font-bold text-slate-900">{{ $booking->check_out_date->translatedFormat('d M Y') }}, 12:00</dd></div>
                            </dl>
                        </div>

                        @if ($booking->guest_request)
                            <div class="mt-4 flex gap-3 items-start rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs font-medium text-amber-900">
                                <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <div>
                                    <p class="font-bold">Permintaan khusus tamu</p>
                                    <p class="mt-0.5">{{ $booking->guest_request }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Unit Kamar Otomatis -->
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Unit Kamar</h3>
                                @if ($booking->units->isNotEmpty())
                                    <p class="mt-1 text-sm font-medium text-slate-500">Unit ditetapkan otomatis oleh sistem.</p>
                                @else
                                    <p class="mt-1 text-sm font-medium text-slate-500">Unit siap pakai akan dipilih otomatis saat check-in.</p>
                                @endif
                            </div>
                            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-black text-sky-700 ring-1 ring-sky-200">{{ $booking->unit_count }} unit</span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @forelse ($booking->units as $unit)
                                <div class="rounded-xl border px-4 py-3 {{ $unit->status === 'Available' ? 'border-emerald-200 bg-emerald-50' : ($unit->status === 'Cleaning' ? 'border-amber-200 bg-amber-50' : 'border-rose-200 bg-rose-50') }}">
                                    <div class="flex items-center gap-2 text-sm font-black text-slate-800">
                                        <span class="h-2 w-2 rounded-full {{ $unit->status === 'Available' ? 'bg-emerald-500' : ($unit->status === 'Cleaning' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                        {{ $unit->name }}
                                    </div>
                                    <p class="mt-1 text-[10px] font-black uppercase tracking-wide text-slate-500">{{ $roomStatusLabels[$unit->status] ?? $unit->status }}</p>
                                    @if ($unit->status === 'Cleaning')
                                        <form method="POST" action="{{ route('room-units.status.update', $unit) }}" class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Available">
                                            <button class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-black text-white hover:bg-emerald-800">Unit Sudah Siap</button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm font-bold text-slate-500">Menunggu proses check-in</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Alur Tamu Otomatis -->
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.16em] text-emerald-700">Alur Tamu Otomatis</p>
                                <h3 class="mt-1 text-xl font-black tracking-tight text-slate-900">{{ $bookingStatusLabels[$booking->booking_status] ?? $booking->booking_status }}</h3>
                                <p class="mt-1 text-sm font-medium text-slate-500">Status kamar akan mengikuti proses tamu secara otomatis.</p>
                            </div>
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-3 gap-2">
                            @foreach ([['Booked', 'Dipesan'], ['In-House', 'Menginap'], ['Completed', 'Selesai']] as [$status, $label])
                                @php
                                    $steps = ['Booked' => 1, 'In-House' => 2, 'Completed' => 3];
                                    $currentStep = $steps[$booking->booking_status] ?? 0;
                                    $step = $steps[$status];
                                @endphp
                                <div class="rounded-xl border px-2 py-3 text-center {{ $currentStep >= $step ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-slate-200 bg-slate-50 text-slate-400' }}">
                                    <span class="mx-auto flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-black {{ $currentStep >= $step ? 'bg-emerald-700 text-white' : 'bg-slate-200 text-slate-500' }}">{{ $step }}</span>
                                    <p class="mt-1.5 text-[10px] font-black uppercase tracking-wide">{{ $label }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5">
                            @if ($booking->booking_status === 'Booked')
                                @if ((float) $booking->paid_amount < $minimumDpAmount)
                                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800">
                                        <p>DP belum mencukupi untuk check-in.</p>
                                        <p class="mt-1 text-xs font-semibold">Minimal {{ $minDpPercent }}%: Rp {{ number_format($minimumDpAmount, 0, ',', '.') }} · Kurang Rp {{ number_format($minimumDpRemaining, 0, ',', '.') }}</p>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('bookings.status.update', $booking) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="booking_status" value="In-House">
                                        <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-emerald-800">
                                            Proses Check-in
                                        </button>
                                    </form>
                                @endif
                                <button type="button" x-data @click="$dispatch('open-modal', 'confirm-no-show')" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700">Tandai Tidak Datang</button>
                            @elseif ($booking->booking_status === 'In-House')
                                @if ((float) $booking->paid_amount < $minimumDpAmount)
                                    <div class="mb-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-bold text-rose-800">
                                        DP booking lama ini belum memenuhi minimal {{ $minDpPercent }}%. Tambahkan pembayaran Rp {{ number_format($minimumDpRemaining, 0, ',', '.') }}.
                                    </div>
                                @endif
                                @if ($booking->payment_status === 'Lunas')
                                    <form method="POST" action="{{ route('bookings.status.update', $booking) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="booking_status" value="Completed">
                                        <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-emerald-800">Proses Check-out</button>
                                    </form>
                                @else
                                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800">
                                        Pelunasan diperlukan sebelum check-out. Sisa tagihan: Rp {{ number_format($booking->balance_due, 0, ',', '.') }}.
                                    </div>
                                @endif
                            @elseif ($booking->booking_status === 'Completed')
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">Tamu sudah check-out. Unit kamar masuk antrean pembersihan secara otomatis.</div>
                            @elseif ($booking->booking_status === 'No-Show')
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm font-bold text-slate-700">Tamu tidak datang. Unit kamar sudah dilepas dan dapat dialokasikan kembali.</div>
                            @endif
                        </div>
                    </div>

                    <x-modal name="confirm-no-show" maxWidth="md" focusable>
                        <div class="p-6 sm:p-7">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-rose-700 ring-8 ring-rose-50">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                            </div>
                            <h3 class="mt-6 text-xl font-black tracking-tight text-slate-950">Tamu Tidak Datang?</h3>
                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">Pemesanan akan ditandai sebagai tidak datang dan semua unit yang dialokasikan akan dilepas agar bisa digunakan kembali.</p>
                        </div>
                        <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-black text-slate-700 transition hover:bg-slate-100">Kembali</button>
                            <form method="POST" action="{{ route('bookings.status.update', $booking) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="booking_status" value="No-Show">
                                <button type="submit" class="w-full rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-black text-white shadow-sm transition hover:bg-rose-700 sm:w-auto">Ya, Tandai Tidak Datang</button>
                            </form>
                        </div>
                    </x-modal>

                    <!-- Rincian Biaya -->
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Rincian Biaya</h3>
                                <p class="mt-1 text-sm font-medium text-slate-500">Semua biaya telah dikalkulasi otomatis.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-700">{{ $booking->payment_status }}</span>
                        </div>
                        <div class="mt-5 space-y-3 text-sm rounded-2xl bg-slate-50 border border-slate-100 p-5">
                            <div class="flex items-center justify-between gap-4"><span class="text-slate-500 font-medium">Sewa Kamar</span><span class="font-bold text-slate-900">Rp {{ number_format($booking->total_room_price, 0, ',', '.') }}</span></div>
                            @if ((float) $booking->total_addons_price > 0)
                            <div class="flex items-center justify-between gap-4"><span class="text-slate-500 font-medium">Layanan Tambahan</span><span class="font-bold text-slate-900">Rp {{ number_format($booking->total_addons_price, 0, ',', '.') }}</span></div>
                            @endif
                            @if ((float) $booking->occupancy_adjustment_amount > 0)
                            <div class="flex items-center justify-between gap-4"><span class="text-slate-500 font-medium">Ekstra Penghuni</span><span class="font-bold text-slate-900">Rp {{ number_format($booking->occupancy_adjustment_amount, 0, ',', '.') }}</span></div>
                            @endif
                            @if ((float) $booking->late_fee > 0)
                            <div class="flex items-center justify-between gap-4"><span class="text-slate-500 font-medium">Denda Keterlambatan</span><span class="font-bold text-slate-900">Rp {{ number_format($booking->late_fee, 0, ',', '.') }}</span></div>
                            @endif
                            @if ((float) $booking->discount_amount > 0)
                            <div class="flex items-center justify-between gap-4"><span class="text-slate-500 font-medium">Diskon</span><span class="font-bold text-rose-600">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span></div>
                            @endif
                            
                            <div class="border-t border-dashed border-slate-300 pt-3 flex items-center justify-between gap-4"><span class="font-bold text-slate-900">Total Tagihan</span><span class="text-lg font-black text-slate-900">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="text-slate-500 font-medium">Sudah Dibayar</span><span class="font-bold text-emerald-700">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span></div>
                            
                            <div class="mt-3 rounded-xl bg-slate-900 p-4 flex items-center justify-between gap-4 text-white shadow-sm"><span class="font-bold">Sisa Tagihan</span><span class="text-xl font-black">Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Addons -->
                    <div
                        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                        x-data="{ cancelModal: false, cancelAction: '', cancelTitle: '', cancelMessage: '', openCancel(action, title, message) { this.cancelAction = action; this.cancelTitle = title; this.cancelMessage = message; this.cancelModal = true } }"
                        @keydown.escape.window="cancelModal = false"
                    >
                        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Layanan Tambahan (Add-ons)</h3>
                                <p class="mt-1 text-sm font-medium text-slate-500">Setiap layanan otomatis digabungkan ke total tagihan pemesanan.</p>
                            </div>
                            @if ($booking->addons->contains('payment_status', 'Pending'))
                                <button
                                    type="button"
                                    @click="openCancel(@js(route('bookings.addons.cancel-all', $booking)), 'Batalkan Semua Layanan?', 'Semua layanan yang belum dibayar akan dikeluarkan dari total tagihan. Tindakan ini tetap dicatat di log aktivitas.')"
                                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-black text-rose-700 transition hover:border-rose-300 hover:bg-rose-100"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                    Batalkan Semua
                                </button>
                            @endif
                        </div>

                        @if (! in_array($booking->booking_status, ['Completed', 'Cancelled', 'No-Show'], true))
                            <details class="group mb-5 rounded-2xl border border-slate-200 bg-slate-50">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3.5 text-sm font-black text-slate-800">
                                    <span class="flex items-center gap-2">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">+</span>
                                        Tambah Layanan
                                    </span>
                                    <svg class="h-4 w-4 text-slate-400 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" /></svg>
                                </summary>
                            <form method="POST" action="{{ route('bookings.addons.store', $booking) }}" class="border-t border-slate-200 p-4" x-data="{ addons: {} }">
                                @csrf
                                @error('addons')
                                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-600">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 max-h-[380px] overflow-y-auto pr-2 pb-2">
                                    @foreach ($addonItems as $addonItem)
                                        <div 
                                            class="relative flex flex-col justify-between rounded-2xl border p-4 transition-all cursor-pointer group"
                                            :class="addons[{{ $addonItem->id }}] ? 'border-emerald-500 bg-emerald-50 shadow-md shadow-emerald-500/10' : 'border-slate-200 bg-white hover:border-emerald-300 hover:shadow-md'"
                                            @click="if(!addons[{{ $addonItem->id }}]) addons[{{ $addonItem->id }}] = 1; else delete addons[{{ $addonItem->id }}]"
                                        >
                                            <div class="flex items-start gap-3">
                                                <div class="relative flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition-colors mt-0.5"
                                                     :class="addons[{{ $addonItem->id }}] ? 'border-emerald-500 bg-emerald-50' : 'border-slate-300 group-hover:border-emerald-400'">
                                                    <div class="h-2 w-2 rounded-full bg-emerald-500 transition-transform"
                                                         :class="addons[{{ $addonItem->id }}] ? 'scale-100' : 'scale-0'"></div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-black text-slate-900 truncate">{{ $addonItem->name }}</p>
                                                    <p class="mt-0.5 text-xs font-semibold text-slate-500 truncate">{{ $addonItem->categoryLabel() }}</p>
                                                    <p class="mt-1.5 text-sm font-black text-emerald-600">Rp {{ number_format($addonItem->price, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                            
                                            <template x-if="addons[{{ $addonItem->id }}]">
                                                <div @click.stop class="mt-4 pt-3 border-t border-emerald-200/60 flex items-center justify-between">
                                                    <span class="text-xs font-bold text-emerald-800">Porsi/Qty:</span>
                                                    <div class="flex items-center gap-1 rounded-xl bg-white p-1 ring-1 ring-emerald-100 shadow-sm">
                                                        <button type="button" 
                                                                @click="if(addons[{{ $addonItem->id }}] > 1) addons[{{ $addonItem->id }}]--; else delete addons[{{ $addonItem->id }}];" 
                                                                class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 text-slate-600 transition-colors hover:bg-rose-100 hover:text-rose-700 active:scale-95">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                                                        </button>
                                                        
                                                        <input type="number" 
                                                               name="addons[{{ $addonItem->id }}]" 
                                                               x-model.number="addons[{{ $addonItem->id }}]" 
                                                               min="1" 
                                                               style="-moz-appearance: textfield;"
                                                               class="w-10 border-0 bg-transparent p-0 text-center text-sm font-black text-slate-900 focus:ring-0 appearance-none [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                                                               required>
                                                        
                                                        <button type="button" 
                                                                @click="addons[{{ $addonItem->id }}]++" 
                                                                class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 transition-colors hover:bg-emerald-100 hover:text-emerald-800 active:scale-95">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="flex justify-end border-t border-slate-200 pt-4">
                                    <button 
                                        :disabled="Object.keys(addons).length === 0"
                                        :class="Object.keys(addons).length === 0 ? 'bg-slate-300 text-slate-500 cursor-not-allowed' : 'bg-slate-900 text-white hover:bg-emerald-700 active:scale-95 shadow-sm'"
                                        class="w-full sm:w-auto flex items-center justify-center rounded-xl px-8 py-3 text-sm font-bold transition-all">
                                        Simpan Tambahan Pesanan
                                    </button>
                                </div>
                            </form>
                            </details>
                        @endif

                        <div class="space-y-4">
                            @forelse ($activeAddons as $addon)
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="font-black text-slate-900">{{ $addon->item_name }}</p>
                                                @if ($booking->payment_status === 'Lunas')
                                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700 ring-1 ring-emerald-200">Lunas</span>
                                                @else
                                                    <span class="rounded-full bg-sky-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-sky-700 ring-1 ring-sky-200">Aktif</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-xs font-bold text-slate-500">{{ $addon->qty }} x Rp {{ number_format($addon->price, 0, ',', '.') }} <span class="mx-1">&bull;</span> {{ $addon->categoryLabel() }}</p>
                                        </div>
                                        <p class="font-black text-lg text-slate-900">Rp {{ number_format($addon->subtotal, 0, ',', '.') }}</p>
                                    </div>

                                    @if ($addon->payment_status === 'Pending')
                                        <div class="mt-4 flex justify-end border-t border-slate-100 pt-4">
                                            <button
                                                type="button"
                                                @click="openCancel(@js(route('booking-addons.cancel', $addon)), 'Batalkan Layanan?', @js($addon->item_name.' akan dikeluarkan dari total tagihan pemesanan.'))"
                                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 transition hover:border-rose-300 hover:bg-rose-100"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                                Batalkan dari Tagihan
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                                    <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                    <p class="text-sm font-bold text-slate-500">Belum ada tambahan layanan.</p>
                                </div>
                            @endforelse
                        </div>

                        @if ($cancelledAddons->isNotEmpty())
                            <details class="group mt-5 rounded-2xl border border-slate-200 bg-slate-50">
                                <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 text-xs font-black text-slate-500">
                                    <span>Riwayat dibatalkan ({{ $cancelledAddons->count() }})</span>
                                    <svg class="h-4 w-4 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" /></svg>
                                </summary>
                                <div class="space-y-2 border-t border-slate-200 p-3">
                                    @foreach ($cancelledAddons as $addon)
                                        <div class="flex items-center justify-between gap-3 rounded-xl bg-white px-3 py-2 text-xs text-slate-500">
                                            <span><strong class="text-slate-700">{{ $addon->item_name }}</strong> · {{ $addon->qty }} × Rp {{ number_format($addon->price, 0, ',', '.') }}</span>
                                            <span class="font-black line-through">Rp {{ number_format($addon->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @endif

                        <template x-teleport="body">
                            <div x-cloak x-show="cancelModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="cancel-modal-title">
                                <div x-show="cancelModal" x-transition.opacity class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" @click="cancelModal = false"></div>
                                <div
                                    x-show="cancelModal"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                                    class="relative w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-black/5"
                                >
                                    <div class="p-6 sm:p-7">
                                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-rose-700 ring-8 ring-rose-50">
                                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                                        </div>
                                        <h3 id="cancel-modal-title" class="mt-6 text-xl font-black tracking-tight text-slate-950" x-text="cancelTitle"></h3>
                                        <p class="mt-2 text-sm font-medium leading-6 text-slate-500" x-text="cancelMessage"></p>
                                        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-bold leading-5 text-amber-800">
                                            Total tagihan akan dihitung ulang secara otomatis.
                                        </div>
                                    </div>
                                    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                                        <button type="button" @click="cancelModal = false" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-black text-slate-700 transition hover:bg-slate-100">Kembali</button>
                                        <form method="POST" :action="cancelAction">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="w-full rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-black text-white shadow-sm transition hover:bg-rose-700 sm:w-auto">Ya, Batalkan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    @if (auth()->user()->isSuperAdmin())
                        @if ($unresolvedTransferIssues->isNotEmpty())
                            <div class="rounded-3xl border-2 border-rose-300 bg-rose-50 p-6 shadow-sm">
                                <h3 class="text-xl font-black text-rose-950">Transfer Bermasalah</h3>
                                <p class="mt-1 text-sm font-semibold text-rose-800">Dana sudah masuk, tetapi tidak menjadi DP karena hold kedaluwarsa atau stok tidak tersedia.</p>

                                @foreach ($unresolvedTransferIssues as $issue)
                                    <div class="mt-5 rounded-2xl border border-rose-200 bg-white p-5">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div>
                                                <p class="font-black text-slate-900">Rp {{ number_format($issue->amount, 0, ',', '.') }}</p>
                                                <p class="mt-1 text-xs font-bold text-slate-500">{{ $issue->bankAccount?->bank_name }} · {{ $issue->transfer_reference }}</p>
                                                <p class="mt-1 text-xs font-semibold text-rose-700">{{ $issue->note }}</p>
                                            </div>
                                            <span class="rounded-full bg-rose-100 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-rose-800">Belum diselesaikan</span>
                                        </div>

                                        <details class="mt-4 rounded-xl border border-slate-200">
                                            <summary class="cursor-pointer px-4 py-3 text-sm font-black text-slate-800">Pindah kamar/tanggal lalu terima sebagai DP</summary>
                                            <form method="POST" action="{{ route('bookings.transfer-issues.update', [$booking, $issue]) }}" class="grid gap-3 border-t border-slate-200 p-4 sm:grid-cols-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="resolution_action" value="accept">
                                                <select name="room_id" required class="rounded-xl border-slate-200 text-sm font-bold">
                                                    @foreach ($resolutionRooms as $room)
                                                        <option value="{{ $room->id }}" @selected($room->id === $booking->room_id)>{{ $room->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input name="unit_count" type="number" min="1" max="20" value="{{ $booking->unit_count }}" required class="rounded-xl border-slate-200 text-sm font-bold" placeholder="Jumlah unit">
                                                <input name="check_in_date" type="date" value="{{ $booking->check_in_date->toDateString() }}" required class="rounded-xl border-slate-200 text-sm font-bold">
                                                <input name="check_out_date" type="date" value="{{ $booking->check_out_date->toDateString() }}" required class="rounded-xl border-slate-200 text-sm font-bold">
                                                <textarea name="resolution_note" required rows="2" class="rounded-xl border-slate-200 text-sm sm:col-span-2" placeholder="Contoh: dipindahkan ke Commercial Villa 02 pada tanggal baru"></textarea>
                                                <button class="rounded-xl bg-emerald-700 px-4 py-3 text-sm font-black text-white hover:bg-emerald-800 sm:col-span-2">Verifikasi Stok dan Terima Transfer</button>
                                            </form>
                                        </details>

                                        <details class="mt-3 rounded-xl border border-rose-200">
                                            <summary class="cursor-pointer px-4 py-3 text-sm font-black text-rose-800">Refund penuh dan batalkan booking</summary>
                                            <form method="POST" action="{{ route('bookings.transfer-issues.update', [$booking, $issue]) }}" class="grid gap-3 border-t border-rose-200 p-4">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="resolution_action" value="refund">
                                                <select name="refund_bank_account_id" required class="rounded-xl border-slate-200 text-sm font-bold">
                                                    <option value="">Pilih rekening pengirim refund</option>
                                                    @foreach ($bankAccounts as $bankAccount)
                                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name }} — {{ $bankAccount->account_number }}</option>
                                                    @endforeach
                                                </select>
                                                <input name="refund_reference" required class="rounded-xl border-slate-200 text-sm uppercase" placeholder="Referensi transfer refund">
                                                <textarea name="resolution_note" required rows="2" class="rounded-xl border-slate-200 text-sm" placeholder="Alasan dan keterangan refund"></textarea>
                                                <button class="rounded-xl bg-rose-700 px-4 py-3 text-sm font-black text-white hover:bg-rose-800">Catat Refund dan Batalkan</button>
                                            </form>
                                        </details>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Pembayaran -->
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Validasi Transfer Bank</h3>
                            <p class="mt-1 text-sm font-medium text-slate-500">Cocokkan mutasi bank, lalu masukkan nominal dan referensi transfer. Sistem tidak menerima tunai.</p>
                            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center justify-between gap-3 text-xs font-bold">
                                    <span class="text-slate-500">Minimal DP {{ $minDpPercent }}%</span>
                                    <span class="text-slate-900">Rp {{ number_format($minimumDpAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-full rounded-full bg-emerald-600" style="width: {{ $minimumDpAmount > 0 ? min(100, ((float) $booking->paid_amount / $minimumDpAmount) * 100) : 100 }}%"></div>
                                </div>
                                <p class="mt-2 text-xs font-semibold {{ $minimumDpRemaining > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                                    {{ $minimumDpRemaining > 0 ? 'Kurang Rp '.number_format($minimumDpRemaining, 0, ',', '.').' untuk memenuhi DP.' : 'Minimal DP sudah terpenuhi.' }}
                                </p>
                            </div>
                            @if ((float) $booking->balance_due > 0 && ! in_array($booking->booking_status, ['Completed', 'Cancelled', 'No-Show'], true) && $unresolvedTransferIssues->isEmpty())
                            <form method="POST" action="{{ route('bookings.payments.store', $booking) }}" class="mt-5 space-y-5" x-data="{ setAmount(amount) { const input = $el.querySelector('[data-money-display]'); input.value = amount; input.dispatchEvent(new Event('input', { bubbles: true })); } }">
                                @csrf
                                <div>
                                    <div class="mb-2 flex items-center justify-between gap-3">
                                        <label class="text-xs font-black uppercase tracking-wide text-slate-700">Nominal Diterima</label>
                                        <span class="text-xs font-bold text-slate-500">Sisa: Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</span>
                                    </div>
                                    <x-money-input name="amount" :value="$booking->balance_due" placeholder="Nominal pembayaran" :required="true" />
                                    <p class="mt-2 text-xs font-semibold text-slate-500">Kurang dari sisa tagihan akan tercatat sebagai <strong>DP</strong>. Jika sama, otomatis <strong>Lunas</strong>.</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @if ($minimumDpRemaining > 0 && $minimumDpRemaining < (float) $booking->balance_due)
                                            <button type="button" @click="setAmount({{ (int) ceil($minimumDpRemaining) }})" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-black text-amber-800 transition hover:bg-amber-100">Penuhi DP · Rp {{ number_format($minimumDpRemaining, 0, ',', '.') }}</button>
                                        @endif
                                        <button type="button" @click="setAmount({{ (int) $booking->balance_due }})" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-black text-emerald-800 transition hover:bg-emerald-100">Lunasi · Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</button>
                                    </div>
                                </div>

                                <div>
                                    <label for="payment-bank-account" class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-700">Rekening Tujuan</label>
                                    <select id="payment-bank-account" name="bank_account_id" required class="w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 focus:border-emerald-500 focus:ring-emerald-500/20">
                                        <option value="">Pilih rekening penerima</option>
                                        @foreach ($bankAccounts as $bankAccount)
                                            <option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name }} — {{ $bankAccount->account_number }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="payment-reference" class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-700">Referensi Transfer / Mutasi</label>
                                    <input id="payment-reference" name="transfer_reference" value="{{ old('transfer_reference') }}" required placeholder="Contoh: BCA-180626-123456" class="w-full rounded-xl border-slate-200 text-sm font-medium uppercase focus:border-emerald-500 focus:ring-emerald-500/20">
                                    <p class="mt-1 text-xs font-semibold text-slate-500">Harus unik agar transfer yang sama tidak tercatat dua kali.</p>
                                </div>

                                <div>
                                    <label for="payment-note" class="mb-2 block text-xs font-black uppercase tracking-wide text-slate-700">Catatan <span class="font-semibold normal-case text-slate-400">(opsional)</span></label>
                                    <input id="payment-note" name="note" placeholder="Contoh: cocok dengan mutasi pukul 10:15" class="w-full rounded-xl border-slate-200 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500/20">
                                </div>

                                <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 py-3 text-sm font-black text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                                    Validasi Transfer
                                </button>
                            </form>
                            @else
                                <div class="mt-5 rounded-2xl border p-4 text-center text-sm font-black {{ $unresolvedTransferIssues->isNotEmpty() ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800' }}">
                                    {{ $unresolvedTransferIssues->isNotEmpty() ? 'Selesaikan transfer bermasalah sebelum mencatat pembayaran lain.' : 'Tagihan sudah lunas. Tidak ada pembayaran yang perlu dicatat.' }}
                                </div>
                            @endif

                            @if ($booking->payments->isNotEmpty())
                                <details class="group mt-5 border-t border-slate-100 pt-4">
                                    <summary class="flex cursor-pointer list-none items-center justify-between text-xs font-black text-slate-600">
                                        <span>Riwayat Pembayaran ({{ $booking->payments->count() }})</span>
                                        <svg class="h-4 w-4 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" /></svg>
                                    </summary>
                                    <div class="mt-3 space-y-2">
                                        @foreach ($booking->payments->sortByDesc('validated_at') as $payment)
                                            <div class="flex items-start justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 text-xs">
                                                <div>
                                                    <p class="font-black text-slate-800">{{ match ($payment->type) {
                                                        \App\Models\Payment::TYPE_REFUND => 'Refund',
                                                        \App\Models\Payment::TYPE_TRANSFER_ISSUE_REFUND => 'Refund Transfer Bermasalah',
                                                        \App\Models\Payment::TYPE_TRANSFER_ISSUE => 'Transfer Bermasalah',
                                                        \App\Models\Payment::TYPE_BOOKING_LUNAS => 'Pelunasan',
                                                        \App\Models\Payment::TYPE_BOOKING_DP => 'Pembayaran DP',
                                                        default => 'Pembayaran Add-on',
                                                    } }}</p>
                                                    <p class="mt-0.5 font-semibold text-slate-500">{{ $payment->bankAccount?->bank_name ?? 'Rekening tidak tersedia' }} · {{ $payment->validated_at?->translatedFormat('d M Y, H:i') }}</p>
                                                    @if ($payment->transfer_reference)
                                                        <p class="mt-0.5 font-mono text-[10px] font-bold text-slate-400">{{ $payment->transfer_reference }}</p>
                                                    @endif
                                                </div>
                                                <span class="font-black {{ in_array($payment->type, [\App\Models\Payment::TYPE_REFUND, \App\Models\Payment::TYPE_TRANSFER_ISSUE_REFUND], true) ? 'text-rose-700' : ($payment->type === \App\Models\Payment::TYPE_TRANSFER_ISSUE ? 'text-amber-700' : 'text-emerald-700') }}">{{ in_array($payment->type, [\App\Models\Payment::TYPE_REFUND, \App\Models\Payment::TYPE_TRANSFER_ISSUE_REFUND], true) ? '-' : '' }}Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        </div>

                        <!-- Penyesuaian -->
                        <details class="group rounded-3xl border border-slate-200 bg-white shadow-sm">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-6">
                                <span>
                                    <span class="block text-lg font-black tracking-tight text-slate-900">Penyesuaian Harga</span>
                                    <span class="mt-1 block text-sm font-medium text-slate-500">Diskon, denda, atau biaya ekstra jika diperlukan.</span>
                                </span>
                                <svg class="h-5 w-5 shrink-0 text-slate-400 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" /></svg>
                            </summary>
                            <div class="border-t border-slate-100 p-6">
                            <form method="POST" action="{{ route('bookings.adjustments.update', $booking) }}" class="grid gap-4">
                                @csrf
                                @method('PATCH')
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="text-xs font-bold text-slate-700 mb-1 block">Diskon (Rp)</label>
                                        <x-money-input name="discount_amount" :value="$booking->discount_amount" :required="true" />
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-slate-700 mb-1 block">Denda Telat (Rp)</label>
                                        <x-money-input name="late_fee" :value="$booking->late_fee" :required="true" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="text-xs font-bold text-slate-700 mb-1 block">Biaya Ekstra Penghuni (Rp)</label>
                                        <x-money-input name="occupancy_adjustment_amount" :value="$booking->occupancy_adjustment_amount" :required="true" />
                                    </div>
                                </div>
                                
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <textarea name="discount_note" rows="2" placeholder="Catatan Diskon..." class="rounded-xl border-slate-200 text-sm font-medium focus:ring-emerald-500/20 focus:border-emerald-500">{{ $booking->discount_note }}</textarea>
                                    <textarea name="occupancy_adjustment_note" rows="2" placeholder="Catatan Biaya Ekstra..." class="rounded-xl border-slate-200 text-sm font-medium focus:ring-emerald-500/20 focus:border-emerald-500">{{ $booking->occupancy_adjustment_note }}</textarea>
                                </div>
                                <button class="w-full flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm transition-all hover:bg-slate-50 active:scale-95">Simpan Penyesuaian</button>
                            </form>
                            </div>
                        </details>

                        @if (! in_array($booking->booking_status, ['Completed', 'Cancelled'], true))
                            <details class="group rounded-3xl border border-rose-200 bg-white shadow-sm">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-6">
                                    <span>
                                        <span class="block text-lg font-black tracking-tight text-rose-800">Batalkan Pemesanan</span>
                                        <span class="mt-1 block text-sm font-medium text-slate-500">Unit akan dilepas. Refund dapat dicatat bersamaan jika sudah ditransfer.</span>
                                    </span>
                                    <svg class="h-5 w-5 shrink-0 text-rose-400 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" /></svg>
                                </summary>
                                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="space-y-4 border-t border-rose-100 p-6">
                                    @csrf
                                    <div>
                                        <label class="mb-1 block text-xs font-black uppercase tracking-wide text-slate-700">Alasan Pembatalan</label>
                                        <textarea name="cancellation_note" rows="3" required class="w-full rounded-xl border-slate-200 text-sm" placeholder="Jelaskan alasan pembatalan">{{ old('cancellation_note') }}</textarea>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-xs font-black uppercase tracking-wide text-slate-700">Refund opsional</p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500">Isi hanya jika uang sudah benar-benar ditransfer kembali. Maksimal Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}.</p>
                                        <div class="mt-3 grid gap-3">
                                            <x-money-input name="refund_amount" :value="0" />
                                            <select name="bank_account_id" class="w-full rounded-xl border-slate-200 bg-white text-sm font-bold">
                                                <option value="">Pilih rekening pengirim refund</option>
                                                @foreach ($bankAccounts as $bankAccount)
                                                    <option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name }} — {{ $bankAccount->account_number }}</option>
                                                @endforeach
                                            </select>
                                            <input name="transfer_reference" class="w-full rounded-xl border-slate-200 text-sm uppercase" placeholder="Referensi transfer refund">
                                            <input name="refund_note" class="w-full rounded-xl border-slate-200 text-sm" placeholder="Catatan refund">
                                        </div>
                                    </div>
                                    <button class="w-full rounded-xl bg-rose-700 px-4 py-3 text-sm font-black text-white hover:bg-rose-800">Batalkan Pemesanan</button>
                                </form>
                            </details>
                        @endif

                        @if ((float) $booking->paid_amount > 0)
                            <details class="group rounded-3xl border border-slate-200 bg-white shadow-sm">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-6">
                                    <span>
                                        <span class="block text-lg font-black tracking-tight text-slate-900">Catat Refund Terpisah</span>
                                        <span class="mt-1 block text-sm font-medium text-slate-500">Gunakan jika pengembalian dana dilakukan setelah pembatalan.</span>
                                    </span>
                                    <svg class="h-5 w-5 shrink-0 text-slate-400 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" /></svg>
                                </summary>
                                <form method="POST" action="{{ route('bookings.refunds.store', $booking) }}" class="grid gap-4 border-t border-slate-100 p-6">
                                    @csrf
                                    <x-money-input name="amount" :value="$booking->paid_amount" :required="true" />
                                    <select name="bank_account_id" required class="w-full rounded-xl border-slate-200 bg-white text-sm font-bold">
                                        <option value="">Pilih rekening pengirim refund</option>
                                        @foreach ($bankAccounts as $bankAccount)
                                            <option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name }} — {{ $bankAccount->account_number }}</option>
                                        @endforeach
                                    </select>
                                    <input name="transfer_reference" required class="w-full rounded-xl border-slate-200 text-sm uppercase" placeholder="Referensi transfer refund">
                                    <textarea name="note" rows="2" required class="w-full rounded-xl border-slate-200 text-sm" placeholder="Alasan dan keterangan refund"></textarea>
                                    <button class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-black text-white hover:bg-rose-700">Simpan Refund</button>
                                </form>
                            </details>
                        @endif
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
