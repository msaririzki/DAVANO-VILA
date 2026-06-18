<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Setting;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingTransferIssueController extends Controller
{
    public function update(Request $request, Booking $booking, Payment $payment): RedirectResponse
    {
        abort_unless(
            $payment->booking_id === $booking->id
            && $payment->type === Payment::TYPE_TRANSFER_ISSUE
            && $payment->resolution_status === Payment::RESOLUTION_UNRESOLVED,
            404,
        );

        $action = $request->string('resolution_action')->toString();

        if ($action === 'accept') {
            $this->acceptForBooking($request, $booking, $payment);

            return back()->with('status', 'Transfer bermasalah sudah diterapkan sebagai DP/Lunas setelah stok diverifikasi.');
        }

        if ($action === 'refund') {
            $this->refundAndCancel($request, $booking, $payment);

            return back()->with('status', 'Transfer bermasalah sudah direfund dan pemesanan dibatalkan.');
        }

        throw ValidationException::withMessages([
            'resolution_action' => 'Pilih penyelesaian transfer bermasalah.',
        ]);
    }

    private function acceptForBooking(Request $request, Booking $booking, Payment $payment): void
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'unit_count' => ['required', 'integer', 'min:1', 'max:20'],
            'resolution_note' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($booking, $payment, $request, $validated): void {
            $lockedBooking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $lockedPayment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            $this->ensureUnresolvedIssue($lockedPayment, $lockedBooking);

            $room = Room::query()->whereKey($validated['room_id'])->lockForUpdate()->firstOrFail();
            $unitCount = $room->allow_unit_quantity ? (int) $validated['unit_count'] : 1;
            $availableUnits = $room->activeUnitCount() - $room->reservedUnitCount(
                $validated['check_in_date'],
                $validated['check_out_date'],
                $lockedBooking->id,
            );

            if (! $room->is_active || $room->status !== Room::STATUS_AVAILABLE || $unitCount > $availableUnits) {
                throw ValidationException::withMessages([
                    'room_id' => 'Kamar/tanggal pengganti tidak memiliki unit yang cukup.',
                ]);
            }

            if ((int) $lockedBooking->total_guest_count > (int) $room->max_capacity * $unitCount) {
                throw ValidationException::withMessages([
                    'unit_count' => 'Jumlah unit pengganti tidak cukup untuk jumlah tamu.',
                ]);
            }

            $nights = max(1, Carbon::parse($validated['check_in_date'])
                ->diffInDays(Carbon::parse($validated['check_out_date'])));
            $lockedBooking->fill([
                'room_id' => $room->id,
                'unit_count' => $unitCount,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'total_room_price' => (float) $room->price * $nights * $unitCount,
                'hold_expires_at' => null,
            ]);
            $lockedBooking->recalculateTotals();

            $amount = (float) $lockedPayment->amount;
            $minimumDp = (float) $lockedBooking->grand_total
                * ((int) Setting::value('min_dp_percent', 50) / 100);

            if ($amount > (float) $lockedBooking->grand_total) {
                throw ValidationException::withMessages([
                    'room_id' => 'Nilai transfer melebihi tagihan baru. Refund selisih terlebih dahulu.',
                ]);
            }

            if ($amount < $minimumDp && $amount < (float) $lockedBooking->grand_total) {
                throw ValidationException::withMessages([
                    'room_id' => 'Transfer belum memenuhi minimal DP untuk kamar/tanggal pengganti.',
                ]);
            }

            $oldValues = $lockedBooking->getOriginal();
            $lockedPayment->fill([
                'type' => $amount >= (float) $lockedBooking->grand_total
                    ? Payment::TYPE_BOOKING_LUNAS
                    : Payment::TYPE_BOOKING_DP,
                'resolution_status' => Payment::RESOLUTION_ACCEPTED,
                'resolution_note' => $validated['resolution_note'],
                'resolved_by' => $request->user()->id,
                'resolved_at' => now(),
            ])->save();

            $lockedBooking->recalculateTotals();
            $lockedBooking->save();

            AuditLogger::record(
                $request,
                'payment.transfer_issue_accepted',
                'Menyelesaikan transfer bermasalah untuk '.$lockedBooking->booking_code.' dengan kamar/tanggal yang tersedia',
                $lockedPayment,
                $oldValues,
                [
                    ...$lockedBooking->only([
                        'room_id',
                        'unit_count',
                        'check_in_date',
                        'check_out_date',
                        'grand_total',
                        'paid_amount',
                        'balance_due',
                        'payment_status',
                    ]),
                    'resolution_note' => $validated['resolution_note'],
                ],
            );
        });
    }

    private function refundAndCancel(Request $request, Booking $booking, Payment $payment): void
    {
        $reference = Str::upper(trim($request->string('refund_reference')->toString()));
        $request->merge(['refund_reference' => $reference !== '' ? $reference : null]);
        $validated = $request->validate([
            'refund_bank_account_id' => [
                'required',
                Rule::exists('bank_accounts', 'id')->where('is_active', true),
            ],
            'refund_reference' => ['required', 'string', 'max:120', 'unique:payments,transfer_reference'],
            'resolution_note' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($booking, $payment, $request, $validated): void {
            $lockedBooking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $lockedPayment = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            $this->ensureUnresolvedIssue($lockedPayment, $lockedBooking);

            Payment::query()->create([
                'booking_id' => $lockedBooking->id,
                'type' => Payment::TYPE_TRANSFER_ISSUE_REFUND,
                'amount' => $lockedPayment->amount,
                'bank_account_id' => $validated['refund_bank_account_id'],
                'transfer_reference' => $validated['refund_reference'],
                'validated_by' => $request->user()->id,
                'validated_at' => now(),
                'note' => $validated['resolution_note'],
            ]);

            $lockedPayment->fill([
                'resolution_status' => Payment::RESOLUTION_REFUNDED,
                'resolution_note' => $validated['resolution_note'],
                'resolved_by' => $request->user()->id,
                'resolved_at' => now(),
            ])->save();

            $lockedBooking->addons()
                ->where('payment_status', BookingAddon::PAYMENT_PENDING)
                ->update(['payment_status' => BookingAddon::PAYMENT_CANCELLED]);
            $lockedBooking->booking_status = Booking::STATUS_CANCELLED;
            $lockedBooking->cancelled_at = now();
            $lockedBooking->cancellation_note = $validated['resolution_note'];
            $lockedBooking->hold_expires_at = null;
            $lockedBooking->recalculateTotals();
            $lockedBooking->save();
            $lockedBooking->units()->detach();

            AuditLogger::record(
                $request,
                'payment.transfer_issue_refunded',
                'Merefund transfer bermasalah dan membatalkan '.$lockedBooking->booking_code,
                $lockedPayment,
                null,
                [
                    'refund_amount' => (float) $lockedPayment->amount,
                    'refund_reference' => $validated['refund_reference'],
                    'resolution_note' => $validated['resolution_note'],
                ],
            );
        });
    }

    private function ensureUnresolvedIssue(Payment $payment, Booking $booking): void
    {
        if (
            $payment->booking_id !== $booking->id
            || $payment->type !== Payment::TYPE_TRANSFER_ISSUE
            || $payment->resolution_status !== Payment::RESOLUTION_UNRESOLVED
        ) {
            throw ValidationException::withMessages([
                'resolution_action' => 'Transfer bermasalah ini sudah diselesaikan atau tidak valid.',
            ]);
        }
    }
}
