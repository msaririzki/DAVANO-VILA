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
        $validated = $request->validate([
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'discount_note' => ['nullable', 'string', 'max:1000'],
            'late_fee' => ['required', 'numeric', 'min:0'],
        ]);

        $maximumDiscount = (float) $booking->total_room_price + (float) $booking->total_addons_price + (float) $validated['late_fee'];

        if ((float) $validated['discount_amount'] > $maximumDiscount) {
            return back()->withErrors(['discount_amount' => 'Diskon tidak boleh melebihi total tagihan.']);
        }

        $oldValues = $booking->only(['discount_amount', 'discount_note', 'late_fee', 'grand_total', 'balance_due']);

        $booking->fill([
            'discount_amount' => $validated['discount_amount'],
            'discount_note' => $validated['discount_note'] ?? null,
            'late_fee' => $validated['late_fee'],
        ]);
        $booking->recalculateTotals();
        $booking->save();

        AuditLogger::record(
            $request,
            'booking.adjusted',
            'Mengubah diskon/biaya checkout untuk booking '.$booking->booking_code,
            $booking,
            $oldValues,
            $booking->only(['discount_amount', 'discount_note', 'late_fee', 'grand_total', 'balance_due']),
        );

        return back()->with('status', 'Diskon dan biaya checkout berhasil diperbarui.');
    }
}
