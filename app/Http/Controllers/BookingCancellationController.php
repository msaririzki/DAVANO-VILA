<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Payment;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingCancellationController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $reference = Str::upper(trim($request->string('transfer_reference')->toString()));
        $request->merge(['transfer_reference' => $reference !== '' ? $reference : null]);

        $validated = $request->validate([
            'cancellation_note' => ['required', 'string', 'max:1000'],
            'refund_amount' => ['nullable', 'numeric', 'min:0'],
            'bank_account_id' => [
                'nullable',
                Rule::exists('bank_accounts', 'id')->where('is_active', true),
            ],
            'transfer_reference' => ['nullable', 'string', 'max:120', 'unique:payments,transfer_reference'],
            'refund_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $refundAmount = (float) ($validated['refund_amount'] ?? 0);

        if ($refundAmount > 0 && (empty($validated['bank_account_id']) || empty($validated['transfer_reference']))) {
            return back()->withErrors([
                'refund_amount' => 'Refund wajib memilih rekening perusahaan dan mengisi referensi transfer.',
            ])->withInput();
        }

        DB::transaction(function () use ($booking, $refundAmount, $request, $validated): void {
            $lockedBooking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if (in_array($lockedBooking->booking_status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED], true)) {
                throw ValidationException::withMessages([
                    'cancellation_note' => 'Pemesanan yang sudah selesai atau dibatalkan tidak dapat dibatalkan lagi.',
                ]);
            }

            $oldValues = $lockedBooking->only([
                'booking_status',
                'payment_status',
                'paid_amount',
                'balance_due',
                'cancelled_at',
                'cancellation_note',
            ]);

            if ($refundAmount > (float) $lockedBooking->paid_amount) {
                throw ValidationException::withMessages([
                    'refund_amount' => 'Refund tidak boleh melebihi uang bersih yang telah diterima.',
                ]);
            }

            if ($refundAmount > 0) {
                Payment::query()->create([
                    'booking_id' => $lockedBooking->id,
                    'type' => Payment::TYPE_REFUND,
                    'amount' => $refundAmount,
                    'bank_account_id' => $validated['bank_account_id'],
                    'transfer_reference' => $validated['transfer_reference'],
                    'validated_by' => $request->user()->id,
                    'validated_at' => now(),
                    'note' => $validated['refund_note'] ?? 'Refund saat pembatalan booking',
                ]);
            }

            $lockedBooking->addons()
                ->where('payment_status', BookingAddon::PAYMENT_PENDING)
                ->update(['payment_status' => BookingAddon::PAYMENT_CANCELLED]);
            $lockedBooking->booking_status = Booking::STATUS_CANCELLED;
            $lockedBooking->cancelled_at = now();
            $lockedBooking->cancellation_note = $validated['cancellation_note'];
            $lockedBooking->recalculateTotals();
            $lockedBooking->save();
            $lockedBooking->units()->detach();

            AuditLogger::record(
                $request,
                'booking.cancelled',
                'Membatalkan pemesanan '.$lockedBooking->booking_code,
                $lockedBooking,
                $oldValues,
                [
                    ...$lockedBooking->only([
                        'booking_status',
                        'payment_status',
                        'paid_amount',
                        'balance_due',
                        'cancelled_at',
                        'cancellation_note',
                    ]),
                    'refund_amount' => $refundAmount,
                ],
            );
        });

        return back()->with('status', 'Pemesanan dibatalkan. Refund, jika diisi, sudah tercatat.');
    }
}
