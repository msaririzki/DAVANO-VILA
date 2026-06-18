<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingRefundController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $reference = Str::upper(trim($request->string('transfer_reference')->toString()));
        $request->merge(['transfer_reference' => $reference !== '' ? $reference : null]);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'bank_account_id' => [
                'required',
                Rule::exists('bank_accounts', 'id')->where('is_active', true),
            ],
            'transfer_reference' => ['required', 'string', 'max:120', 'unique:payments,transfer_reference'],
            'note' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($booking, $request, $validated): void {
            $lockedBooking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $amount = (float) $validated['amount'];

            if ($amount > (float) $lockedBooking->paid_amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Refund tidak boleh melebihi uang bersih yang telah diterima.',
                ]);
            }

            $oldValues = $lockedBooking->only(['paid_amount', 'balance_due', 'payment_status']);
            $payment = Payment::query()->create([
                'booking_id' => $lockedBooking->id,
                'type' => Payment::TYPE_REFUND,
                'amount' => $amount,
                'bank_account_id' => $validated['bank_account_id'],
                'transfer_reference' => $validated['transfer_reference'],
                'validated_by' => $request->user()->id,
                'validated_at' => now(),
                'note' => $validated['note'],
            ]);

            $lockedBooking->recalculateTotals();
            $lockedBooking->save();

            AuditLogger::record(
                $request,
                'payment.refunded',
                'Mencatat refund pemesanan '.$lockedBooking->booking_code.' senilai Rp '.number_format($amount, 0, ',', '.'),
                $payment,
                $oldValues,
                $lockedBooking->only(['paid_amount', 'balance_due', 'payment_status']),
            );
        });

        return back()->with('status', 'Refund berhasil dicatat.');
    }
}
