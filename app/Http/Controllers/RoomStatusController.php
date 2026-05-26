<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomStatusController extends Controller
{
    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Available,Cleaning,Maintenance'],
        ]);

        $oldValues = $room->only(['status']);

        $room->update(['status' => $validated['status']]);

        AuditLogger::record(
            $request,
            'room_status.updated',
            'Mengubah status kamar '.$room->name.' menjadi '.$room->status,
            $room,
            $oldValues,
            $room->only(['status']),
        );

        return back()->with('status', 'Status kamar berhasil diperbarui.');
    }
}
