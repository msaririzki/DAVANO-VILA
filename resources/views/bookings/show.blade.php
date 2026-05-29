<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">{{ $booking->booking_code }}</h2>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">Booking</span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ $booking->guest_name }} &bull; {{ $booking->room->name }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200/50">{{ $booking->payment_status }}</span>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200/50">{{ $booking->booking_status }}</span>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200/50">{{ $booking->room->status }}</span>
                <a href="{{ route('dashboard') }}" class="ml-2 inline-flex items-center justify-center gap-2 px-3 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all font-bold text-xs shadow-sm">
                    Ke Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-slate-50 pt-6 pb-12">
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
                            <x-custom-select
                                name="booking_status"
                                :options="[
                                    'Booked' => 'Booked',
                                    'In-House' => 'In-House',
                                    'Completed' => 'Completed',
                                    'No-Show' => 'No-Show',
                                ]"
                                :selected="$booking->booking_status"
                                placeholder="Pilih status booking"
                                :required="true"
                            />
                            <button class="rounded-md bg-gray-950 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Simpan status</button>
                        </form>
                        <p class="mt-3 text-xs leading-5 text-gray-500">Completed hanya bisa disimpan setelah pembayaran berstatus Lunas.</p>
                    </div>

                    <div class="rounded-lg border border-white/70 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-950">Status kamar</h3>
                        <form method="POST" action="{{ route('rooms.status.update', $booking->room) }}" class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto]">
                            @csrf
                            @method('PATCH')
                            <x-custom-select
                                name="status"
                                :options="[
                                    'Available' => 'Available',
                                    'Cleaning' => 'Cleaning',
                                    'Maintenance' => 'Maintenance',
                                ]"
                                :selected="$booking->room->status"
                                placeholder="Pilih status kamar"
                                :required="true"
                            />
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
                            <x-custom-select
                                name="addon_item_id"
                                :options="$addonItems->mapWithKeys(fn ($addonItem) => [
                                    (string) $addonItem->id => $addonItem->name.' - Rp '.number_format($addonItem->price, 0, ',', '.'),
                                ])->all()"
                                placeholder="Pilih add-on"
                                :required="true"
                            />
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
                                            <x-custom-select
                                                name="bank_account_id"
                                                :options="$bankAccounts->mapWithKeys(fn ($bankAccount) => [
                                                    (string) $bankAccount->id => $bankAccount->bank_name,
                                                ])->all()"
                                                placeholder="Rekening"
                                            />
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
                                <x-custom-select
                                    name="type"
                                    :options="[
                                        'booking_dp' => 'DP',
                                        'booking_lunas' => 'Pelunasan',
                                        'adjustment' => 'Adjustment',
                                    ]"
                                    selected="booking_dp"
                                    placeholder="Pilih tipe pembayaran"
                                    :required="true"
                                />
                                <x-custom-select
                                    name="bank_account_id"
                                    :options="$bankAccounts->mapWithKeys(fn ($bankAccount) => [
                                        (string) $bankAccount->id => $bankAccount->bank_name,
                                    ])->all()"
                                    placeholder="Rekening tujuan"
                                />
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
