<?php

namespace App\Http\Controllers;

use App\Models\BookingAddon;
use App\Models\Payment;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingAddonPaymentController extends Controller
{
    public function store(Request $request, BookingAddon $bookingAddon): RedirectResponse
    {
        if ($bookingAddon->payment_status === BookingAddon::PAYMENT_PAID) {
            return back()->withErrors(['amount' => 'Add-on ini sudah dibayar.']);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:'.$bookingAddon->subtotal],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking = $bookingAddon->booking;
        $oldValues = [
            'addon_payment_status' => $bookingAddon->payment_status,
            ...$booking->only(['paid_amount', 'balance_due', 'payment_status']),
        ];

        $payment = Payment::query()->create([
            'booking_id' => $bookingAddon->booking_id,
            'booking_addon_id' => $bookingAddon->id,
            'type' => Payment::TYPE_ADDON,
            'amount' => $validated['amount'],
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
            'note' => $validated['note'] ?? null,
        ]);

        $bookingAddon->update(['payment_status' => BookingAddon::PAYMENT_PAID]);

        $booking->recalculateTotals();
        $booking->save();

        AuditLogger::record(
            $request,
            'addon_payment.validated',
            'Memvalidasi pembayaran add-on '.$bookingAddon->item_name.' untuk booking '.$booking->booking_code,
            $payment,
            $oldValues,
            [
                'addon_payment_status' => $bookingAddon->payment_status,
                ...$booking->only(['paid_amount', 'balance_due', 'payment_status']),
                'payment_amount' => (float) $payment->amount,
                'bank_account_id' => $payment->bank_account_id,
            ],
        );

        return back()->with('status', 'Pembayaran add-on berhasil divalidasi.');
    }
}
