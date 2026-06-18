<?php

namespace App\Http\Controllers;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Room;
use App\Models\Setting;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
                ->with('units')
                ->where('is_active', true)
                ->orderBy('price')
                ->get();
        } else {
            $rooms = Room::query()
                ->with('units')
                ->where('is_active', true)
                ->where('status', Room::STATUS_AVAILABLE)
                ->orderBy('price')
                ->get();
        }

        return view('public.bookings.index', [
            'rooms' => $rooms,
            'checkIn' => $validated['check_in_date'] ?? null,
            'checkOut' => $validated['check_out_date'] ?? null,
            'extraBedItems' => AddonItem::query()
                ->where('category', AddonItem::CATEGORY_EXTRA_BED)
                ->where('is_active', true)
                ->orderBy('price')
                ->get(),
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
            'adult_count' => ['required', 'integer', 'min:1', 'max:50'],
            'child_count' => ['nullable', 'integer', 'min:0', 'max:50'],
            'unit_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'acquisition_source' => ['nullable', 'string', 'max:100'],
            'guest_request' => ['nullable', 'string', 'max:1000'],
            'extra_bed_item_id' => [
                'nullable',
                Rule::exists('addon_items', 'id')
                    ->where('category', AddonItem::CATEGORY_EXTRA_BED)
                    ->where('is_active', true),
            ],
            'extra_bed_qty' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $unitCount = (int) ($validated['unit_count'] ?? 1);
        $adultCount = (int) $validated['adult_count'];
        $childCount = (int) ($validated['child_count'] ?? 0);
        $totalGuestCount = $adultCount + $childCount;
        $booking = DB::transaction(function () use (
            $adultCount,
            $childCount,
            $totalGuestCount,
            $unitCount,
            $validated,
        ): Booking {
            $room = Room::query()->whereKey($validated['room_id'])->lockForUpdate()->firstOrFail();
            $requestedUnitCount = $room->allow_unit_quantity ? $unitCount : 1;
            $availableUnits = $room->availableUnitCount($validated['check_in_date'], $validated['check_out_date']);

            if (! $room->is_active || $room->status !== Room::STATUS_AVAILABLE || $requestedUnitCount > $availableUnits) {
                throw ValidationException::withMessages([
                    'unit_count' => 'Unit baru saja penuh atau sedang ditahan tamu lain. Silakan pilih kamar atau tanggal lain.',
                ]);
            }

            if ($totalGuestCount > (int) $room->max_capacity * $requestedUnitCount) {
                throw ValidationException::withMessages([
                    'adult_count' => 'Jumlah penghuni melebihi kapasitas maksimal untuk pilihan kamar ini.',
                ]);
            }

            $nights = max(1, Carbon::parse($validated['check_in_date'])
                ->diffInDays(Carbon::parse($validated['check_out_date'])));
            $totalRoomPrice = $room->price * $nights * $requestedUnitCount;
            $holdMinutes = max(5, (int) Setting::value('booking_hold_minutes', 30));

            $booking = Booking::query()->create([
                'booking_code' => $this->nextBookingCode(),
                'guest_name' => $validated['guest_name'],
                'guest_phone' => $validated['guest_phone'],
                'adult_count' => $adultCount,
                'child_count' => $childCount,
                'total_guest_count' => $totalGuestCount,
                'acquisition_source' => $validated['acquisition_source'] ?? null,
                'guest_request' => $validated['guest_request'] ?? null,
                'room_id' => $room->id,
                'unit_count' => $requestedUnitCount,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'total_room_price' => $totalRoomPrice,
                'grand_total' => $totalRoomPrice,
                'balance_due' => $totalRoomPrice,
                'hold_expires_at' => now()->addMinutes($holdMinutes),
            ]);

            if (! empty($validated['extra_bed_item_id'])) {
                $extraBed = AddonItem::query()
                    ->where('category', AddonItem::CATEGORY_EXTRA_BED)
                    ->where('is_active', true)
                    ->findOrFail($validated['extra_bed_item_id']);
                $qty = (int) ($validated['extra_bed_qty'] ?? 1);
                $subtotal = $extraBed->price * $qty;

                BookingAddon::query()->create([
                    'booking_id' => $booking->id,
                    'addon_item_id' => $extraBed->id,
                    'item_name' => $extraBed->name,
                    'type' => $extraBed->type,
                    'category' => $extraBed->category,
                    'qty' => $qty,
                    'price' => $extraBed->price,
                    'subtotal' => $subtotal,
                    'payment_status' => BookingAddon::PAYMENT_PENDING,
                ]);

                $booking->recalculateTotals();
                $booking->save();
            }

            return $booking;
        });

        AuditLogger::record(
            $request,
            'booking.created_public',
            'Tamu membuat pemesanan publik '.$booking->booking_code,
            $booking,
            null,
            $booking->only([
                'booking_code',
                'room_id',
                'unit_count',
                'check_in_date',
                'check_out_date',
                'total_room_price',
                'total_addons_price',
                'grand_total',
                'balance_due',
                'payment_status',
                'hold_expires_at',
            ]),
        );

        return redirect()->to(URL::signedRoute('public.bookings.show', [
            'booking' => $booking->public_token,
        ]));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['room', 'units', 'addons']);

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
