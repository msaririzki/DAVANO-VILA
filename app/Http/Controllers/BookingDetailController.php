<?php

namespace App\Http\Controllers;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Booking;
use Illuminate\View\View;

class BookingDetailController extends Controller
{
    public function show(Booking $booking): View
    {
        $booking->load(['room', 'addons.payments', 'payments.bankAccount', 'payments.validator']);

        return view('bookings.show', [
            'booking' => $booking,
            'addonItems' => AddonItem::query()->where('is_active', true)->orderBy('type')->orderBy('name')->get(),
            'bankAccounts' => BankAccount::query()->where('is_active', true)->orderBy('bank_name')->get(),
        ]);
    }
}
