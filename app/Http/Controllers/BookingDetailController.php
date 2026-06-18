<?php

namespace App\Http\Controllers;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\BookingAddon;
use Illuminate\View\View;

class BookingDetailController extends Controller
{
    public function show(Booking $booking): View
    {
        $booking->load(['room.units', 'units', 'addons.payments', 'payments.bankAccount', 'payments.validator']);

        return view('bookings.show', [
            'booking' => $booking,
            'addonItems' => AddonItem::query()->where('is_active', true)->orderBy('category')->orderBy('name')->get(),
            'categoryLabels' => BookingAddon::categoryLabels(),
            'bankAccounts' => BankAccount::query()->where('is_active', true)->orderBy('bank_name')->get(),
            'availableUnits' => $booking->room->availableUnitsForAssignment(
                $booking->check_in_date->toDateString(),
                $booking->check_out_date->toDateString(),
                $booking->id,
            ),
        ]);
    }
}
