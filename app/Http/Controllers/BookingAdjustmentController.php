<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingAdjustmentController extends Controller
{
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        if (in_array($booking->booking_status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED, Booking::STATUS_NO_SHOW], true)) {
            return back()->withErrors(['discount_amount' => 'Harga pemesanan yang sudah ditutup tidak dapat diubah.']);
        }

        $validated = $request->validate([
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'discount_note' => ['nullable', 'string', 'max:1000'],
            'late_fee' => ['required', 'numeric', 'min:0'],
            'occupancy_adjustment_amount' => ['nullable', 'numeric', 'min:0'],
            'occupancy_adjustment_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $maximumDiscount = (float) $booking->total_room_price
            + (float) $booking->total_addons_price
            + (float) ($validated['occupancy_adjustment_amount'] ?? 0)
            + (float) $validated['late_fee'];

        if ((float) $validated['discount_amount'] > $maximumDiscount) {
            return back()->withErrors(['discount_amount' => 'Diskon tidak boleh melebihi total tagihan.']);
        }

        $newGrandTotal = max(0, $maximumDiscount - (float) $validated['discount_amount']);

        if ($newGrandTotal < (float) $booking->paid_amount) {
            return back()->withErrors([
                'discount_amount' => 'Total tagihan tidak boleh lebih kecil dari uang yang sudah diterima. Catat refund terlebih dahulu.',
            ])->withInput();
        }

        if ((float) $validated['discount_amount'] > 0 && empty($validated['discount_note'])) {
            return back()->withErrors(['discount_note' => 'Catatan wajib diisi jika memberikan diskon.'])->withInput();
        }

        if ((float) ($validated['occupancy_adjustment_amount'] ?? 0) > 0 && empty($validated['occupancy_adjustment_note'])) {
            return back()->withErrors(['occupancy_adjustment_note' => 'Catatan wajib diisi untuk biaya tambahan penghuni.'])->withInput();
        }

        $oldValues = $booking->only(['discount_amount', 'discount_note', 'late_fee', 'occupancy_adjustment_amount', 'occupancy_adjustment_note', 'grand_total', 'balance_due']);

        $booking->fill([
            'discount_amount' => $validated['discount_amount'],
            'discount_note' => $validated['discount_note'] ?? null,
            'late_fee' => $validated['late_fee'],
            'occupancy_adjustment_amount' => $validated['occupancy_adjustment_amount'] ?? 0,
            'occupancy_adjustment_note' => $validated['occupancy_adjustment_note'] ?? null,
        ]);
        $booking->recalculateTotals();
        $booking->save();

        AuditLogger::record(
            $request,
            'booking.adjusted',
            'Mengubah diskon atau denda keterlambatan untuk pemesanan '.$booking->booking_code,
            $booking,
            $oldValues,
            $booking->only(['discount_amount', 'discount_note', 'late_fee', 'occupancy_adjustment_amount', 'occupancy_adjustment_note', 'grand_total', 'balance_due']),
        );

        return back()->with('status', 'Diskon, biaya penghuni, dan denda keterlambatan berhasil diperbarui.');
    }
}
