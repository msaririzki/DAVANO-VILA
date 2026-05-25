<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Villa Dafano</p>
                <h2 class="mt-1 text-2xl font-semibold leading-tight text-gray-950">Dashboard Operasional</h2>
            </div>
            @if (auth()->user()->isSuperAdmin())
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('rooms.index') }}" class="inline-flex rounded-md bg-gray-950 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Master kamar</a>
                    <a href="{{ route('addon-items.index') }}" class="inline-flex rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:border-gray-950">Master add-ons</a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="bg-[#f6f4ef] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-white/70 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Booking Pending</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-950">{{ $pendingCount }}</p>
                </div>
                <div class="rounded-lg border border-white/70 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Sisa Tagihan</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-950">Rp {{ number_format($balanceDue, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-white/70 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Pendapatan Bulan Ini</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-950">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</p>
                </div>
            </section>

            @if (auth()->user()->isSuperAdmin())
                <section class="rounded-lg border border-white/70 bg-white p-5 shadow-sm">
                    <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-800">Media halaman publik</p>
                            <h3 class="mt-1 text-lg font-semibold text-gray-950">Hero awal untuk tamu</h3>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                                Mode video hanya dimuat saat koneksi pengunjung terlihat bagus dan mode hemat data tidak aktif. Jika tidak, halaman otomatis memakai carousel foto.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('settings.public-media.update') }}" class="grid gap-3 sm:grid-cols-[220px_auto]">
                            @csrf
                            @method('PATCH')
                            <select name="hero_media_mode" class="rounded-md border-gray-300 text-sm focus:border-emerald-700 focus:ring-emerald-700">
                                <option value="photos" @selected($heroMediaMode === 'photos')>Carousel foto</option>
                                <option value="video" @selected($heroMediaMode === 'video')>Video jika jaringan bagus</option>
                            </select>
                            <button class="rounded-md bg-gray-950 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Simpan</button>
                        </form>
                    </div>
                </section>
            @endif

            <section class="overflow-hidden rounded-lg border border-white/70 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-950">Booking terbaru</h3>
                        <p class="mt-1 text-sm text-gray-500">Buka detail booking untuk add-ons, checkout, dan validasi pembayaran.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3">Kode</th>
                                <th class="px-5 py-3">Tamu</th>
                                <th class="px-5 py-3">Kamar</th>
                                <th class="px-5 py-3">Tanggal</th>
                                <th class="px-5 py-3">Pembayaran</th>
                                <th class="px-5 py-3">Status tamu</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($bookings as $booking)
                                <tr class="align-middle hover:bg-gray-50/80">
                                    <td class="px-5 py-4 font-semibold text-gray-950">{{ $booking->booking_code }}</td>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-950">{{ $booking->guest_name }}</div>
                                        <div class="text-gray-500">{{ $booking->guest_phone }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div>{{ $booking->room->name }}</div>
                                        <div class="text-gray-500">{{ $booking->room->status }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        {{ $booking->check_in_date->format('d M') }} - {{ $booking->check_out_date->format('d M Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $booking->payment_status === 'Lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $booking->payment_status }}</span>
                                        <div class="mt-2 text-gray-500">Sisa Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-5 py-4">{{ $booking->booking_status }}</td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('bookings.show', $booking) }}" class="inline-flex rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-800 hover:border-gray-950 hover:text-gray-950">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-gray-500">Belum ada booking.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
