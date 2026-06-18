<?php

namespace App\Http\Controllers;

use App\Models\AddonItem;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingAddonController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        if (in_array($booking->booking_status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED, Booking::STATUS_NO_SHOW], true)) {
            return back()->withErrors(['addon_item_id' => 'Pemesanan ini sudah tidak dapat ditambah layanan.']);
        }

        if ($request->has('addons')) {
            $validated = $request->validate([
                'addons' => ['required', 'array'],
                'addons.*' => ['nullable', 'integer', 'min:0', 'max:100'],
            ]);

            $oldValues = $booking->only(['total_addons_price', 'grand_total', 'balance_due']);
            $addedItems = [];

            foreach ($validated['addons'] as $addonItemId => $qty) {
                if (!$qty || $qty <= 0) continue;

                $addonItem = AddonItem::query()->where('is_active', true)->find($addonItemId);
                if (!$addonItem) continue;

                $subtotal = $addonItem->price * $qty;

                $bookingAddon = BookingAddon::query()->create([
                    'booking_id' => $booking->id,
                    'addon_item_id' => $addonItem->id,
                    'item_name' => $addonItem->name,
                    'type' => $addonItem->type,
                    'category' => $addonItem->category,
                    'qty' => $qty,
                    'price' => $addonItem->price,
                    'subtotal' => $subtotal,
                    'payment_status' => BookingAddon::PAYMENT_PENDING,
                ]);

                $addedItems[] = $bookingAddon;
            }

            if (empty($addedItems)) {
                return back()->withErrors(['addons' => 'Silakan pilih setidaknya satu layanan tambahan dengan jumlah lebih dari 0.']);
            }

            $booking->recalculateTotals();
            $booking->save();

            AuditLogger::record(
                $request,
                'booking_addon.created_bulk',
                'Menambahkan '.count($addedItems).' layanan ke pemesanan '.$booking->booking_code,
                $booking,
                $oldValues,
                [
                    ...$booking->only(['total_addons_price', 'grand_total', 'balance_due']),
                    'items_count' => count($addedItems),
                ],
            );

            return back()->with('status', 'Layanan tambahan berhasil dimasukkan ke daftar transaksi.');
        }

        // Fallback for single item (just in case)
        $validated = $request->validate([
            'addon_item_id' => ['required', 'exists:addon_items,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $addonItem = AddonItem::query()->where('is_active', true)->findOrFail($validated['addon_item_id']);
        $subtotal = $addonItem->price * $validated['qty'];

        $oldValues = $booking->only(['total_addons_price', 'grand_total', 'balance_due']);

        $bookingAddon = BookingAddon::query()->create([
            'booking_id' => $booking->id,
            'addon_item_id' => $addonItem->id,
            'item_name' => $addonItem->name,
            'type' => $addonItem->type,
            'category' => $addonItem->category,
            'qty' => $validated['qty'],
            'price' => $addonItem->price,
            'subtotal' => $subtotal,
            'payment_status' => BookingAddon::PAYMENT_PENDING,
        ]);

        $booking->recalculateTotals();
        $booking->save();

        AuditLogger::record(
            $request,
            'booking_addon.created',
            'Menambahkan layanan '.$bookingAddon->item_name.' ke pemesanan '.$booking->booking_code,
            $bookingAddon,
            $oldValues,
            [
                ...$booking->only(['total_addons_price', 'grand_total', 'balance_due']),
                'addon_item' => $bookingAddon->only(['item_name', 'type', 'category', 'qty', 'price', 'subtotal', 'payment_status']),
            ],
        );

        return back()->with('status', 'Layanan tambahan berhasil dimasukkan ke daftar transaksi.');
    }

    public function cancel(Request $request, BookingAddon $bookingAddon): RedirectResponse
    {
        if ($bookingAddon->payment_status === BookingAddon::PAYMENT_PAID) {
            return back()->withErrors(['addon' => 'Layanan yang sudah dibayar tidak dapat dibatalkan langsung. Gunakan proses pengembalian dana.']);
        }

        if ($bookingAddon->payment_status === BookingAddon::PAYMENT_CANCELLED) {
            return back()->withErrors(['addon' => 'Layanan tambahan ini sudah dibatalkan.']);
        }

        $booking = $bookingAddon->booking;

        if (in_array($booking->booking_status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED, Booking::STATUS_NO_SHOW], true)) {
            return back()->withErrors(['addon' => 'Layanan pada pemesanan yang sudah ditutup tidak dapat diubah.']);
        }

        $oldValues = [
            ...$bookingAddon->only(['payment_status']),
            ...$booking->only(['total_addons_price', 'grand_total', 'balance_due']),
        ];

        if ((float) $booking->grand_total - (float) $bookingAddon->subtotal < (float) $booking->paid_amount) {
            return back()->withErrors([
                'addon' => 'Layanan tidak dapat dibatalkan karena total baru lebih kecil dari uang yang sudah diterima. Catat refund selisih terlebih dahulu.',
            ]);
        }

        $bookingAddon->update(['payment_status' => BookingAddon::PAYMENT_CANCELLED]);
        $booking->recalculateTotals();
        $booking->save();

        AuditLogger::record(
            $request,
            'booking_addon.cancelled',
            'Membatalkan layanan '.$bookingAddon->item_name.' dari pemesanan '.$booking->booking_code,
            $bookingAddon,
            $oldValues,
            [
                ...$bookingAddon->only(['payment_status']),
                ...$booking->only(['total_addons_price', 'grand_total', 'balance_due']),
            ],
        );

        return back()->with('status', 'Layanan tambahan dibatalkan dan dikeluarkan dari total tagihan.');
    }

    public function cancelAll(Request $request, Booking $booking): RedirectResponse
    {
        if (in_array($booking->booking_status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED, Booking::STATUS_NO_SHOW], true)) {
            return back()->withErrors(['addon' => 'Layanan pada pemesanan yang sudah ditutup tidak dapat diubah.']);
        }

        $pendingAddons = $booking->addons()
            ->where('payment_status', BookingAddon::PAYMENT_PENDING)
            ->get();

        if ($pendingAddons->isEmpty()) {
            return back()->withErrors(['addon' => 'Tidak ada layanan tambahan yang dapat dibatalkan.']);
        }

        $oldValues = $booking->only(['total_addons_price', 'grand_total', 'balance_due']);
        $cancelledItems = $pendingAddons->map(fn (BookingAddon $addon) => [
            'id' => $addon->id,
            'item_name' => $addon->item_name,
            'subtotal' => (float) $addon->subtotal,
        ])->all();
        $cancelledTotal = $pendingAddons->sum('subtotal');

        if ((float) $booking->grand_total - (float) $cancelledTotal < (float) $booking->paid_amount) {
            return back()->withErrors([
                'addon' => 'Semua layanan tidak dapat dibatalkan karena total baru lebih kecil dari uang yang sudah diterima. Catat refund selisih terlebih dahulu.',
            ]);
        }

        $booking->addons()
            ->where('payment_status', BookingAddon::PAYMENT_PENDING)
            ->update(['payment_status' => BookingAddon::PAYMENT_CANCELLED]);

        $booking->recalculateTotals();
        $booking->save();

        AuditLogger::record(
            $request,
            'booking_addon.cancelled_all',
            'Membatalkan '.count($cancelledItems).' layanan dari pemesanan '.$booking->booking_code,
            $booking,
            $oldValues,
            [
                ...$booking->only(['total_addons_price', 'grand_total', 'balance_due']),
                'cancelled_items' => $cancelledItems,
            ],
        );

        return back()->with('status', count($cancelledItems).' layanan tambahan dibatalkan dan dikeluarkan dari total tagihan.');
    }
}
