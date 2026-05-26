<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InternalBookingController extends Controller
{
    public function create(Request $request): View
    {
        $validated = $request->validate([
            'check_in_date' => ['nullable', 'date', 'after_or_equal:today'],
            'check_out_date' => ['nullable', 'date', 'after:check_in_date'],
        ]);

        $checkIn = $validated['check_in_date'] ?? null;
        $checkOut = $validated['check_out_date'] ?? null;
        $rooms = collect();
        $nights = 1;

        if ($checkIn && $checkOut) {
            $nights = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));
            $rooms = Room::query()
                ->availableBetween($checkIn, $checkOut)
                ->orderBy('price')
                ->get();
        }

        return view('bookings.create', [
            'rooms' => $rooms,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'nights' => $nights,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:30'],
            'acquisition_source' => ['nullable', 'string', 'max:100'],
        ]);

        $room = Room::query()
            ->availableBetween($validated['check_in_date'], $validated['check_out_date'])
            ->findOrFail($validated['room_id']);

        $nights = max(1, Carbon::parse($validated['check_in_date'])
            ->diffInDays(Carbon::parse($validated['check_out_date'])));
        $totalRoomPrice = $room->price * $nights;

        $booking = Booking::query()->create([
            'booking_code' => $this->nextBookingCode(),
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'acquisition_source' => ($validated['acquisition_source'] ?? null) ?: 'Internal admin',
            'room_id' => $room->id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_room_price' => $totalRoomPrice,
            'grand_total' => $totalRoomPrice,
            'balance_due' => $totalRoomPrice,
        ]);

        AuditLogger::record(
            $request,
            'booking.created_internal',
            'Membuat booking internal '.$booking->booking_code.' untuk '.$booking->guest_name,
            $booking,
            null,
            $booking->only([
                'booking_code',
                'guest_name',
                'guest_phone',
                'acquisition_source',
                'room_id',
                'check_in_date',
                'check_out_date',
                'grand_total',
                'payment_status',
                'booking_status',
            ]),
        );

        return redirect()
            ->route('bookings.show', $booking)
            ->with('status', 'Booking tamu berhasil dibuat. Lanjutkan validasi DP atau tambahkan add-ons jika diperlukan.');
    }

    private function nextBookingCode(): string
    {
        do {
            $code = 'VLA-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (Booking::query()->where('booking_code', $code)->exists());

        return $code;
    }
}
