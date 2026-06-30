<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Setting;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class BookingPaymentController extends Controller
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
            'note' => ['nullable', 'string', 'max:1000'],
            'transfer_proof' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'ocr_confidence' => ['nullable', 'integer', 'between:0,100'],
            'ocr_detected_amount' => ['nullable', 'numeric', 'min:1'],
            'ocr_detected_reference' => ['nullable', 'string', 'max:120'],
        ]);

        $proofHash = hash_file('sha256', $validated['transfer_proof']->getRealPath());

        if (Payment::query()->where('proof_sha256', $proofHash)->exists()) {
            throw ValidationException::withMessages([
                'transfer_proof' => 'Bukti transfer ini sudah pernah digunakan.',
            ]);
        }

        $proofPath = $validated['transfer_proof']->store('payment-proofs/'.$booking->id, 'local');

        if (! $proofPath) {
            throw ValidationException::withMessages([
                'transfer_proof' => 'Bukti transfer gagal disimpan. Silakan pilih gambar kembali.',
            ]);
        }

        $proofData = [
            'proof_path' => $proofPath,
            'proof_sha256' => $proofHash,
            'ocr_confidence' => $validated['ocr_confidence'] ?? null,
            'ocr_detected_amount' => $validated['ocr_detected_amount'] ?? null,
            'ocr_detected_reference' => isset($validated['ocr_detected_reference'])
                ? Str::upper(trim($validated['ocr_detected_reference']))
                : null,
        ];
        $problematicTransfer = false;

        try {
            DB::transaction(function () use ($booking, $request, $validated, $proofData, &$problematicTransfer): void {
                $lockedBooking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();
                $room = Room::query()->whereKey($lockedBooking->room_id)->lockForUpdate()->firstOrFail();

                if (in_array($lockedBooking->booking_status, [
                    Booking::STATUS_COMPLETED,
                    Booking::STATUS_CANCELLED,
                    Booking::STATUS_NO_SHOW,
                ], true)) {
                    throw ValidationException::withMessages([
                        'amount' => 'Pembayaran tidak dapat dicatat karena pemesanan sudah ditutup.',
                    ]);
                }

                $amount = (float) $validated['amount'];

                if ($amount > (float) $lockedBooking->balance_due) {
                    throw ValidationException::withMessages([
                        'amount' => 'Nominal pembayaran tidak boleh melebihi sisa tagihan.',
                    ]);
                }

                if ($lockedBooking->payment_status === Booking::PAYMENT_PENDING) {
                    $activeUnitCount = $room->activeUnitCount();
                    $reservedByOthers = $room->reservedUnitCount(
                        $lockedBooking->check_in_date->toDateString(),
                        $lockedBooking->check_out_date->toDateString(),
                        $lockedBooking->id,
                    );
                    $holdExpired = $lockedBooking->hold_expires_at && ! $lockedBooking->hasActiveHold();
                    $stockUnavailable = $reservedByOthers + (int) $lockedBooking->unit_count > $activeUnitCount;

                    if ($holdExpired || $stockUnavailable) {
                        $reason = $holdExpired
                            ? 'Transfer diterima setelah batas waktu hold berakhir.'
                            : 'Transfer diterima ketika stok pada tanggal reservasi sudah tidak tersedia.';
                        $oldValues = $lockedBooking->only(['paid_amount', 'balance_due', 'payment_status', 'hold_expires_at']);
                        $payment = Payment::query()->create([
                            'booking_id' => $lockedBooking->id,
                            'type' => Payment::TYPE_TRANSFER_ISSUE,
                            'amount' => $amount,
                            'bank_account_id' => $validated['bank_account_id'],
                            'transfer_reference' => $validated['transfer_reference'],
                            'validated_by' => $request->user()->id,
                            'validated_at' => now(),
                            'note' => trim($reason.' '.($validated['note'] ?? '')),
                            'resolution_status' => Payment::RESOLUTION_UNRESOLVED,
                            ...$proofData,
                        ]);

                        AuditLogger::record(
                            $request,
                            'payment.transfer_issue_recorded',
                            'Mencatat transfer bermasalah untuk pemesanan '.$lockedBooking->booking_code.' senilai Rp '.number_format($amount, 0, ',', '.'),
                            $payment,
                            $oldValues,
                            [
                                ...$oldValues,
                                'payment_amount' => $amount,
                                'transfer_reference' => $payment->transfer_reference,
                                'proof_sha256' => $payment->proof_sha256,
                                'ocr_confidence' => $payment->ocr_confidence,
                                'reason' => $reason,
                            ],
                        );

                        $problematicTransfer = true;

                        return;
                    }
                }

                $minimumDpAmount = (float) $lockedBooking->grand_total
                * ((int) Setting::value('min_dp_percent', 50) / 100);
                $paidAfterPayment = (float) $lockedBooking->paid_amount + $amount;

                if (
                    $lockedBooking->payment_status === Booking::PAYMENT_PENDING
                    && $paidAfterPayment < $minimumDpAmount
                    && $amount < (float) $lockedBooking->balance_due
                ) {
                    throw ValidationException::withMessages([
                        'amount' => 'Transfer pertama harus memenuhi minimal DP Rp '.number_format($minimumDpAmount, 0, ',', '.').'.',
                    ]);
                }

                $oldValues = $lockedBooking->only(['paid_amount', 'balance_due', 'payment_status']);
                $paymentType = $amount >= (float) $lockedBooking->balance_due
                    ? Payment::TYPE_BOOKING_LUNAS
                    : Payment::TYPE_BOOKING_DP;
                $payment = Payment::query()->create([
                    'booking_id' => $lockedBooking->id,
                    'type' => $paymentType,
                    'amount' => $amount,
                    'bank_account_id' => $validated['bank_account_id'],
                    'transfer_reference' => $validated['transfer_reference'],
                    'validated_by' => $request->user()->id,
                    'validated_at' => now(),
                    'note' => $validated['note'] ?? null,
                    ...$proofData,
                ]);

                $lockedBooking->recalculateTotals();
                $lockedBooking->payment_deadline_at = null;
                $lockedBooking->hold_expires_at = null;

                if ((float) $lockedBooking->balance_due <= 0) {
                    $lockedBooking->addons()
                        ->where('payment_status', BookingAddon::PAYMENT_PENDING)
                        ->update(['payment_status' => BookingAddon::PAYMENT_PAID]);
                }

                $lockedBooking->save();

                AuditLogger::record(
                    $request,
                    'payment.validated',
                    'Mencatat transfer '.$paymentType.' untuk pemesanan '.$lockedBooking->booking_code.' senilai Rp '.number_format($amount, 0, ',', '.'),
                    $payment,
                    $oldValues,
                    [
                        ...$lockedBooking->only(['paid_amount', 'balance_due', 'payment_status']),
                        'payment_type' => $payment->type,
                        'payment_amount' => (float) $payment->amount,
                        'bank_account_id' => $payment->bank_account_id,
                        'transfer_reference' => $payment->transfer_reference,
                        'proof_sha256' => $payment->proof_sha256,
                        'ocr_confidence' => $payment->ocr_confidence,
                    ],
                );
            });
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($proofPath);

            throw $exception;
        }

        if ($problematicTransfer) {
            return back()->with(
                'status',
                'Transfer tercatat sebagai bermasalah dan tidak dianggap DP. Pilih pindah kamar/tanggal atau refund.',
            );
        }

        return back()->with('status', 'Transfer berhasil divalidasi dan status booking sudah diperbarui.');
    }
}
