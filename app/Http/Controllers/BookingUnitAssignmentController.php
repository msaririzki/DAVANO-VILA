<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingUnitAssignmentController extends Controller
{
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'room_unit_ids' => ['nullable', 'array', 'max:'.$booking->unit_count],
            'room_unit_ids.*' => [
                'integer',
                Rule::exists('room_units', 'id')->where('room_id', $booking->room_id),
            ],
        ]);

        $unitIds = collect($validated['room_unit_ids'] ?? [])
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        $availableIds = $booking->room
            ->availableUnitsForAssignment(
                $booking->check_in_date->toDateString(),
                $booking->check_out_date->toDateString(),
                $booking->id,
            )
            ->pluck('id');

        if ($unitIds->diff($availableIds)->isNotEmpty()) {
            return back()->withErrors(['room_unit_ids' => 'Ada unit yang tidak tersedia atau jadwalnya bertabrakan pada tanggal pemesanan ini.']);
        }

        $oldValues = [
            'unit_ids' => $booking->units()->pluck('room_units.id')->all(),
        ];

        $booking->units()->sync($unitIds->all());

        AuditLogger::record(
            $request,
            'booking.units_assigned',
            'Menetapkan unit fisik untuk pemesanan '.$booking->booking_code,
            $booking,
            $oldValues,
            ['unit_ids' => $unitIds->all()],
        );

        return back()->with('status', 'Unit fisik berhasil diperbarui.');
    }
}
