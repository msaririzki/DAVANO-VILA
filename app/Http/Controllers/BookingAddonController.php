<?php

namespace App\Http\Controllers;

use App\Models\AddonItem;
use App\Models\Booking;
use App\Models\BookingAddon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingAddonController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        if (in_array($booking->booking_status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED, Booking::STATUS_NO_SHOW], true)) {
            return back()->withErrors(['addon_item_id' => 'Booking ini sudah tidak bisa ditambah pesanan.']);
        }

        $validated = $request->validate([
            'addon_item_id' => ['required', 'exists:addon_items,id'],
            'qty' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $addonItem = AddonItem::query()->where('is_active', true)->findOrFail($validated['addon_item_id']);
        $subtotal = $addonItem->price * $validated['qty'];

        BookingAddon::query()->create([
            'booking_id' => $booking->id,
            'addon_item_id' => $addonItem->id,
            'item_name' => $addonItem->name,
            'type' => $addonItem->type,
            'qty' => $validated['qty'],
            'price' => $addonItem->price,
            'subtotal' => $subtotal,
            'payment_status' => BookingAddon::PAYMENT_PENDING,
        ]);

        $booking->recalculateTotals();
        $booking->save();

        return back()->with('status', 'Pesanan tambahan masuk ke running tab.');
    }
}
