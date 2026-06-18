<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomUnit;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomUnitStatusController extends Controller
{
    public function update(Request $request, RoomUnit $roomUnit): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Available,Cleaning,Maintenance'],
        ]);

        if (
            $validated['status'] === Room::STATUS_AVAILABLE
            && $roomUnit->bookings()
                ->where('booking_status', Booking::STATUS_IN_HOUSE)
                ->exists()
        ) {
            return back()->withErrors([
                'status' => 'Unit masih digunakan tamu yang sedang menginap.',
            ]);
        }

        $oldValues = $roomUnit->only(['status']);
        $roomUnit->update(['status' => $validated['status']]);

        AuditLogger::record(
            $request,
            'room_unit_status.updated',
            'Mengubah status unit '.$roomUnit->name.' menjadi '.$roomUnit->status,
            $roomUnit,
            $oldValues,
            $roomUnit->only(['status']),
        );

        return back()->with('status', $roomUnit->status === Room::STATUS_AVAILABLE
            ? 'Unit sudah siap menerima tamu.'
            : 'Status unit berhasil diperbarui.');
    }
}
