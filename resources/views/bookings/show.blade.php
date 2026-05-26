<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-950">Dashboard</a>
                <h2 class="mt-1 text-2xl font-semibold text-gray-950">{{ $booking->booking_code }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $booking->guest_name }} - {{ $booking->room->name }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-700">{{ $booking->payment_status }}</span>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">{{ $booking->booking_status }}</span>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700">{{ $booking->room->status }}</span>
            </div>
        </div>
    </x-slot>

    <div class="bg-[#f6f4ef] py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="grid gap-4 lg:grid-cols-4">
                <div class="rounded-lg border border-white/70 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Grand Total</p>
                    <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-white/70 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Terbayar</p>
                    <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-white/70 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Sisa Tagihan</p>
                    <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($booking->balance_due, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg border border-white/70 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Add-ons</p>
                    <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($booking->total_addons_price, 0, ',', '.') }}</p>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <div class="space-y-6">
                    <div class="rounded-lg border border-white/70 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-950">Detail tamu</h3>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Nama</dt><dd class="font-semibold">{{ $booking->guest_name }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">WhatsApp</dt><dd class="font-semibold">{{ $booking->guest_phone }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Sumber</dt><dd class="font-semibold">{{ $booking->acquisition_source ?: '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Check-in</dt><dd class="font-semibold">{{ $booking->check_in_date->format('d M Y') }}, 14:00</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Check-out</dt><dd class="font-semibold">{{ $booking->check_out_date->format('d M Y') }}, 12:00</dd></div>
                        </dl>

                        @if ($booking->guest_request)
                            <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950">
                                <p class="font-semibold">Request tamu</p>
                                <p class="mt-1 leading-6">{{ $booking->guest_request }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-lg border border-white/70 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-950">Status operasional</h3>
                        <form method="POST" action="{{ route('bookings.status.update', $booking) }}" class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto]">
                            @csrf
                            @method('PATCH')
                            <select name="booking_status" class="rounded-md border-gray-300 text-sm">
                                @foreach (['Booked', 'In-House', 'Completed', 'No-Show'] as $status)
                                    <option value="{{ $status }}" @selected($booking->booking_status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                            <button class="rounded-md bg-gray-950 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Simpan status</button>
                        </form>
                        <p class="mt-3 text-xs leading-5 text-gray-500">Completed hanya bisa disimpan setelah pembayaran berstatus Lunas.</p>
                    </div>

                    <div class="rounded-lg border border-white/70 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-950">Status kamar</h3>
                        <form method="POST" action="{{ route('rooms.status.update', $booking->room) }}" class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto]">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="rounded-md border-gray-300 text-sm">
                                @foreach (['Available', 'Cleaning', 'Maintenance'] as $status)
                                    <option value="{{ $status }}" @selected($booking->room->status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                            <button class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:border-gray-950">Update kamar</button>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-lg border border-white/70 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-950">Running tab add-ons</h3>
                                <p class="mt-1 text-sm text-gray-500">Admin boleh input, Super Admin validasi pembayarannya.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('bookings.addons.store', $booking) }}" class="mt-5 grid gap-3 sm:grid-cols-[1fr_96px_auto]">
                            @csrf
                            <select name="addon_item_id" class="rounded-md border-gray-300 text-sm" required>
                                <option value="">Pilih add-on</option>
                                @foreach ($addonItems as $addonItem)
                                    <option value="{{ $addonItem->id }}">{{ $addonItem->name }} - Rp {{ number_format($addonItem->price, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                            <input name="qty" type="number" min="1" value="1" class="rounded-md border-gray-300 text-sm" required>
                            <button class="rounded-md bg-gray-950 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Tambah</button>
                        </form>

                        <div class="mt-6 space-y-3">
                            @forelse ($booking->addons as $addon)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="font-semibold text-gray-950">{{ $addon->item_name }}</p>
                                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $addon->payment_status === 'Paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $addon->payment_status }}</span>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500">{{ $addon->qty }} x Rp {{ number_format($addon->price, 0, ',', '.') }} - {{ $addon->type }}</p>
                                        </div>
                                        <p class="font-semibold text-gray-950">Rp {{ number_format($addon->subtotal, 0, ',', '.') }}</p>
                                    </div>

                                    @if (auth()->user()->isSuperAdmin() && $addon->payment_status !== 'Paid')
                                        <form method="POST" action="{{ route('booking-addons.payments.store', $addon) }}" class="mt-4 grid gap-2 md:grid-cols-[1fr_1fr_1fr_auto]">
                                            @csrf
                                            <input name="amount" type="number" min="{{ (int) $addon->subtotal }}" step="1" value="{{ (int) $addon->subtotal }}" class="rounded-md border-gray-300 text-sm" required>
                                            <select name="bank_account_id" class="rounded-md border-gray-300 text-sm">
                                                <option value="">Rekening</option>
                                                @foreach ($bankAccounts as $bankAccount)
                                                    <option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name }}</option>
                                                @endforeach
                                            </select>
                                            <input name="note" placeholder="Catatan" class="rounded-md border-gray-300 text-sm">
                                            <button class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Validasi</button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="rounded-lg border border-dashed border-gray-300 p-5 text-sm text-gray-500">Belum ada add-on.</div>
                            @endforelse
                        </div>
                    </div>

                    @if (auth()->user()->isSuperAdmin())
                        <div class="rounded-lg border border-white/70 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-950">Validasi pembayaran booking</h3>
                            <form method="POST" action="{{ route('bookings.payments.store', $booking) }}" class="mt-5 grid gap-3 md:grid-cols-2">
                                @csrf
                                <input name="amount" type="number" min="1" step="1" placeholder="Nominal transfer" class="rounded-md border-gray-300 text-sm" required>
                                <select name="type" class="rounded-md border-gray-300 text-sm">
                                    <option value="booking_dp">DP</option>
                                    <option value="booking_lunas">Pelunasan</option>
                                    <option value="adjustment">Adjustment</option>
                                </select>
                                <select name="bank_account_id" class="rounded-md border-gray-300 text-sm">
                                    <option value="">Rekening tujuan</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name }}</option>
                                    @endforeach
                                </select>
                                <input name="note" placeholder="Catatan validasi" class="rounded-md border-gray-300 text-sm">
                                <button class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800 md:col-span-2">Validasi pembayaran</button>
                            </form>
                        </div>

                        <div class="rounded-lg border border-white/70 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-950">Diskon dan late fee</h3>
                            <form method="POST" action="{{ route('bookings.adjustments.update', $booking) }}" class="mt-5 grid gap-3">
                                @csrf
                                @method('PATCH')
                                <div class="grid gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700">Nominal diskon</label>
                                        <input name="discount_amount" type="number" min="0" step="1" value="{{ (int) $booking->discount_amount }}" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                                    </div>
                                    <div>
                                        <label class="text-sm font-semibold text-gray-700">Late fee</label>
                                        <input name="late_fee" type="number" min="0" step="1" value="{{ (int) $booking->late_fee }}" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                                    </div>
                                </div>
                                <textarea name="discount_note" rows="3" placeholder="Catatan diskon/nego" class="rounded-md border-gray-300 text-sm">{{ $booking->discount_note }}</textarea>
                                <button class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:border-gray-950">Simpan penyesuaian</button>
                            </form>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
