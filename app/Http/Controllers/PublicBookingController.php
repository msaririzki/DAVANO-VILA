<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicBookingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->routeIs('public.home') && ($request->filled('check_in_date') || $request->filled('check_out_date'))) {
            return redirect()->to(route('public.rooms.index', $request->query()).'#rooms');
        }

        $validated = $request->validate([
            'check_in_date' => ['nullable', 'date', 'after_or_equal:today'],
            'check_out_date' => ['nullable', 'date', 'after:check_in_date'],
        ]);

        if (! empty($validated['check_in_date']) && ! empty($validated['check_out_date'])) {
            $rooms = Room::query()
                ->availableBetween($validated['check_in_date'], $validated['check_out_date'])
                ->orderBy('price')
                ->get();
        } else {
            $rooms = Room::query()
                ->where('is_active', true)
                ->where('status', Room::STATUS_AVAILABLE)
                ->orderBy('price')
                ->get();
        }

        return view('public.bookings.index', [
            'rooms' => $rooms,
            'checkIn' => $validated['check_in_date'] ?? null,
            'checkOut' => $validated['check_out_date'] ?? null,
            'minDpPercent' => (int) Setting::value('min_dp_percent', 50),
            'heroMediaMode' => Setting::value('hero_media_mode', 'photos'),
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

        $nights = Carbon::parse($validated['check_in_date'])
            ->diffInDays(Carbon::parse($validated['check_out_date']));
        $totalRoomPrice = $room->price * max(1, $nights);

        $booking = Booking::query()->create([
            'booking_code' => $this->nextBookingCode(),
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'acquisition_source' => $validated['acquisition_source'] ?? null,
            'room_id' => $room->id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_room_price' => $totalRoomPrice,
            'grand_total' => $totalRoomPrice,
            'balance_due' => $totalRoomPrice,
        ]);

        return redirect()->to(URL::signedRoute('public.bookings.show', [
            'booking' => $booking->public_token,
        ]));
    }

    public function show(Booking $booking): View
    {
        $booking->load('room');

        return view('public.bookings.show', [
            'booking' => $booking,
            'bankAccounts' => BankAccount::query()->where('is_active', true)->orderBy('bank_name')->get(),
            'minDpPercent' => (int) Setting::value('min_dp_percent', 50),
            'whatsappUrl' => $this->whatsappUrl($booking),
        ]);
    }

    private function nextBookingCode(): string
    {
        do {
            $code = 'VLA-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (Booking::query()->where('booking_code', $code)->exists());

        return $code;
    }

    private function whatsappUrl(Booking $booking): string
    {
        $number = Setting::value('villa_whatsapp_number', '6280000000000');
        $message = __('public.wa_message', [
            'name' => $booking->guest_name,
            'code' => $booking->booking_code,
        ]);

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }
}
