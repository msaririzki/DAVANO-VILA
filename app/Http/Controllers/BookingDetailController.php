<?php

namespace App\Http\Controllers;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class BookingDetailController extends Controller
{
    public function show(Booking $booking): View
    {
        $booking->load(['room.units', 'units', 'addons.payments', 'payments.bankAccount', 'payments.validator']);
        $minDpPercent = (int) Setting::value('min_dp_percent', 50);
        $minimumDpAmount = (float) $booking->grand_total * ($minDpPercent / 100);
        $unresolvedTransferIssues = $booking->payments
            ->where('type', Payment::TYPE_TRANSFER_ISSUE)
            ->where('resolution_status', Payment::RESOLUTION_UNRESOLVED);
        $receiptUrl = URL::temporarySignedRoute(
            'public.bookings.receipt',
            now()->addDays(30),
            ['booking' => $booking->public_token],
        );

        return view('bookings.show', [
            'booking' => $booking,
            'addonItems' => AddonItem::query()->where('is_active', true)->orderBy('category')->orderBy('name')->get(),
            'categoryLabels' => BookingAddon::categoryLabels(),
            'bankAccounts' => BankAccount::query()->where('is_active', true)->orderBy('bank_name')->get(),
            'minDpPercent' => $minDpPercent,
            'minimumDpAmount' => $minimumDpAmount,
            'minimumDpRemaining' => max(0, $minimumDpAmount - (float) $booking->paid_amount),
            'unresolvedTransferIssues' => $unresolvedTransferIssues,
            'resolutionRooms' => Room::query()
                ->where('is_active', true)
                ->where('status', Room::STATUS_AVAILABLE)
                ->orderBy('name')
                ->get(),
            'receiptUrl' => $receiptUrl,
            'receiptLinkIsLocal' => in_array(parse_url($receiptUrl, PHP_URL_HOST), ['127.0.0.1', 'localhost'], true),
            'availableUnits' => $booking->room->availableUnitsForAssignment(
                $booking->check_in_date->toDateString(),
                $booking->check_out_date->toDateString(),
                $booking->id,
            ),
        ]);
    }
}
