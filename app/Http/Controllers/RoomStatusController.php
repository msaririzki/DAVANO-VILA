<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomStatusController extends Controller
{
    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Available,Cleaning,Maintenance'],
        ]);

        $room->update(['status' => $validated['status']]);

        return back()->with('status', 'Status kamar berhasil diperbarui.');
    }
}
