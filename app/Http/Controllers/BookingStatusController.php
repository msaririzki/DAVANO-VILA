<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingStatusController extends Controller
{
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'booking_status' => ['required', 'in:Booked,In-House,Completed,No-Show'],
        ]);

        if ($validated['booking_status'] === Booking::STATUS_IN_HOUSE && $booking->payment_status === Booking::PAYMENT_PENDING) {
            return back()->withErrors(['booking_status' => 'Tamu belum bisa check-in sebelum DP divalidasi.']);
        }

        if ($validated['booking_status'] === Booking::STATUS_COMPLETED && $booking->payment_status !== Booking::PAYMENT_LUNAS) {
            return back()->withErrors(['booking_status' => 'Checkout hanya bisa diselesaikan setelah pembayaran Lunas.']);
        }

        $oldValues = $booking->only(['booking_status']);

        $booking->update([
            'booking_status' => $validated['booking_status'],
        ]);

        if ($validated['booking_status'] === Booking::STATUS_COMPLETED) {
            $booking->room()->update(['status' => Room::STATUS_CLEANING]);
        }

        AuditLogger::record(
            $request,
            'booking_status.updated',
            'Mengubah status tamu booking '.$booking->booking_code.' menjadi '.$booking->booking_status,
            $booking,
            $oldValues,
            $booking->only(['booking_status']),
        );

        return back()->with('status', 'Status tamu berhasil diperbarui.');
    }
}
