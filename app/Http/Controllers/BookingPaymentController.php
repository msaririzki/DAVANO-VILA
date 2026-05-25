<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingPaymentController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'type' => ['required', 'in:booking_dp,booking_lunas,adjustment'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        Payment::query()->create([
            'booking_id' => $booking->id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
            'note' => $validated['note'] ?? null,
        ]);

        $booking->recalculateTotals();
        $booking->save();

        return back()->with('status', 'Pembayaran berhasil divalidasi.');
    }
}
