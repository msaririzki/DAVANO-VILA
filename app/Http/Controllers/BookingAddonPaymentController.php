<?php

namespace App\Http\Controllers;

use App\Models\BookingAddon;
use App\Models\Payment;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingAddonPaymentController extends Controller
{
    public function store(Request $request, BookingAddon $bookingAddon): RedirectResponse
    {
        $reference = Str::upper(trim($request->string('transfer_reference')->toString()));
        $request->merge(['transfer_reference' => $reference !== '' ? $reference : null]);

        if ($bookingAddon->payment_status !== BookingAddon::PAYMENT_PENDING) {
            return back()->withErrors(['amount' => 'Layanan tambahan ini sudah dibayar atau dibatalkan.']);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'in:'.(float) $bookingAddon->subtotal],
            'bank_account_id' => [
                'required',
                Rule::exists('bank_accounts', 'id')->where('is_active', true),
            ],
            'transfer_reference' => ['required', 'string', 'max:120', 'unique:payments,transfer_reference'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($bookingAddon, $request, $validated): void {
            $lockedAddon = BookingAddon::query()->whereKey($bookingAddon->id)->lockForUpdate()->firstOrFail();
            $booking = $lockedAddon->booking()->lockForUpdate()->firstOrFail();

            if ($lockedAddon->payment_status !== BookingAddon::PAYMENT_PENDING) {
                throw ValidationException::withMessages(['amount' => 'Layanan ini sudah diproses.']);
            }

            if (in_array($booking->booking_status, [
                \App\Models\Booking::STATUS_COMPLETED,
                \App\Models\Booking::STATUS_CANCELLED,
                \App\Models\Booking::STATUS_NO_SHOW,
            ], true)) {
                throw ValidationException::withMessages(['amount' => 'Pemesanan sudah ditutup.']);
            }

            if (! in_array($booking->payment_status, [
                \App\Models\Booking::PAYMENT_DP,
                \App\Models\Booking::PAYMENT_LUNAS,
            ], true)) {
                throw ValidationException::withMessages([
                    'amount' => 'Pembayaran layanan hanya dapat divalidasi setelah DP booking dikonfirmasi.',
                ]);
            }

            $oldValues = [
                'addon_payment_status' => $lockedAddon->payment_status,
                ...$booking->only(['paid_amount', 'balance_due', 'payment_status']),
            ];
            $payment = Payment::query()->create([
                'booking_id' => $lockedAddon->booking_id,
                'booking_addon_id' => $lockedAddon->id,
                'type' => Payment::TYPE_ADDON,
                'amount' => $validated['amount'],
                'bank_account_id' => $validated['bank_account_id'],
                'transfer_reference' => $validated['transfer_reference'],
                'validated_by' => $request->user()->id,
                'validated_at' => now(),
                'note' => $validated['note'] ?? null,
            ]);

            $lockedAddon->update(['payment_status' => BookingAddon::PAYMENT_PAID]);
            $booking->recalculateTotals();
            $booking->save();

            AuditLogger::record(
                $request,
                'addon_payment.validated',
                'Memvalidasi transfer layanan '.$lockedAddon->item_name.' untuk pemesanan '.$booking->booking_code,
                $payment,
                $oldValues,
                [
                    'addon_payment_status' => $lockedAddon->payment_status,
                    ...$booking->only(['paid_amount', 'balance_due', 'payment_status']),
                    'payment_amount' => (float) $payment->amount,
                    'bank_account_id' => $payment->bank_account_id,
                    'transfer_reference' => $payment->transfer_reference,
                ],
            );
        });

        return back()->with('status', 'Pembayaran layanan tambahan berhasil divalidasi.');
    }
}
