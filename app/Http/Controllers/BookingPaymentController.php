<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Support\AuditLogger;
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

        $oldValues = $booking->only(['paid_amount', 'balance_due', 'payment_status']);

        $payment = Payment::query()->create([
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

        AuditLogger::record(
            $request,
            'payment.validated',
            'Memvalidasi pembayaran '.$validated['type'].' untuk booking '.$booking->booking_code.' senilai Rp '.number_format((float) $validated['amount'], 0, ',', '.'),
            $payment,
            $oldValues,
            [
                ...$booking->only(['paid_amount', 'balance_due', 'payment_status']),
                'payment_type' => $payment->type,
                'payment_amount' => (float) $payment->amount,
                'bank_account_id' => $payment->bank_account_id,
            ],
        );

        return back()->with('status', 'Pembayaran berhasil divalidasi.');
    }
}
