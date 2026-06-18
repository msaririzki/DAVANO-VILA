<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Setting;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingStatusController extends Controller
{
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'booking_status' => ['required', 'in:Booked,In-House,Completed,No-Show'],
        ]);

        DB::transaction(function () use ($booking, $request, $validated): void {
            $lockedBooking = Booking::query()
                ->with(['room', 'units'])
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();
            $allowedTransitions = [
                Booking::STATUS_BOOKED => [Booking::STATUS_IN_HOUSE, Booking::STATUS_NO_SHOW],
                Booking::STATUS_IN_HOUSE => [Booking::STATUS_COMPLETED],
                Booking::STATUS_COMPLETED => [],
                Booking::STATUS_NO_SHOW => [],
                Booking::STATUS_CANCELLED => [],
            ];

            if (! in_array($validated['booking_status'], $allowedTransitions[$lockedBooking->booking_status] ?? [], true)) {
                throw ValidationException::withMessages([
                    'booking_status' => 'Perubahan status ini tidak sesuai dengan alur pemesanan.',
                ]);
            }

            $minDpPercent = (int) Setting::value('min_dp_percent', 50);
            $minimumDpAmount = (float) $lockedBooking->grand_total * ($minDpPercent / 100);

            if ($validated['booking_status'] === Booking::STATUS_IN_HOUSE && (float) $lockedBooking->paid_amount < $minimumDpAmount) {
                throw ValidationException::withMessages([
                    'booking_status' => 'Check-in memerlukan minimal DP '.number_format($minDpPercent, 0, ',', '.').'% (Rp '.number_format($minimumDpAmount, 0, ',', '.').').',
                ]);
            }

            if ($validated['booking_status'] === Booking::STATUS_COMPLETED && $lockedBooking->payment_status !== Booking::PAYMENT_LUNAS) {
                throw ValidationException::withMessages([
                    'booking_status' => 'Proses keluar hanya dapat diselesaikan setelah pembayaran berstatus Lunas.',
                ]);
            }

            $oldValues = [
                ...$lockedBooking->only(['booking_status']),
                'unit_ids' => $lockedBooking->units()->pluck('room_units.id')->all(),
            ];

            if ($validated['booking_status'] === Booking::STATUS_IN_HOUSE) {
                $availableUnits = $lockedBooking->room->availableUnitsForAssignment(
                    $lockedBooking->check_in_date->toDateString(),
                    $lockedBooking->check_out_date->toDateString(),
                    $lockedBooking->id,
                );

                if ($availableUnits->count() < $lockedBooking->unit_count) {
                    throw ValidationException::withMessages([
                        'booking_status' => 'Unit siap pakai tidak mencukupi untuk proses check-in.',
                    ]);
                }

                $lockedBooking->units()->sync($availableUnits->take($lockedBooking->unit_count)->pluck('id')->all());
            }

            $lockedBooking->booking_status = $validated['booking_status'];
            $lockedBooking->save();

            if ($validated['booking_status'] === Booking::STATUS_COMPLETED) {
                if (! $lockedBooking->units()->exists()) {
                    $fallbackUnits = $lockedBooking->room->availableUnitsForAssignment(
                        $lockedBooking->check_in_date->toDateString(),
                        $lockedBooking->check_out_date->toDateString(),
                        $lockedBooking->id,
                    )->take($lockedBooking->unit_count);

                    $lockedBooking->units()->sync($fallbackUnits->pluck('id')->all());
                }

                $lockedBooking->units()->update(['status' => Room::STATUS_CLEANING]);
            } elseif ($validated['booking_status'] === Booking::STATUS_NO_SHOW) {
                $lockedBooking->units()->detach();
            }

            AuditLogger::record(
                $request,
                'booking_status.updated',
                'Mengubah status pemesanan '.$lockedBooking->booking_code.' menjadi '.$lockedBooking->booking_status,
                $lockedBooking,
                $oldValues,
                [
                    ...$lockedBooking->only(['booking_status']),
                    'unit_ids' => $lockedBooking->units()->pluck('room_units.id')->all(),
                ],
            );
        });

        $message = match ($validated['booking_status']) {
            Booking::STATUS_IN_HOUSE => 'Check-in berhasil. Unit kamar telah ditetapkan otomatis.',
            Booking::STATUS_COMPLETED => 'Check-out berhasil. Unit kamar otomatis masuk antrean pembersihan.',
            Booking::STATUS_NO_SHOW => 'Pemesanan ditandai tidak datang dan unit telah dilepas.',
            default => 'Status tamu berhasil diperbarui.',
        };

        return back()->with('status', $message);
    }
}
