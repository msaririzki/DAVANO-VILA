<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(): View
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $monthlyBookings = Booking::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        $dailyRevenue = Payment::query()
            ->selectRaw('DATE(validated_at) as date')
            ->selectRaw("SUM(CASE WHEN type = ? THEN -amount ELSE amount END) as total", [Payment::TYPE_REFUND])
            ->whereBetween('validated_at', [$startOfMonth, $endOfMonth])
            ->whereIn('type', [...Payment::INCOMING_TYPES, Payment::TYPE_REFUND])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn (Payment $payment): array => [
                'date' => Carbon::parse($payment->date),
                'total' => (float) $payment->total,
            ]);

        $sourceStats = Booking::query()
            ->selectRaw("COALESCE(acquisition_source, 'unknown') as source, COUNT(*) as total")
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        $roomStats = Room::query()
            ->leftJoin('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->select('rooms.id', 'rooms.name')
            ->selectRaw('COUNT(bookings.id) as booking_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN bookings.booking_status != ? THEN bookings.grand_total ELSE 0 END), 0) as gross_total", [Booking::STATUS_CANCELLED])
            ->groupBy('rooms.id', 'rooms.name')
            ->orderByDesc('booking_count')
            ->get();

        $daysInMonth = $startOfMonth->daysInMonth;
        $occupancyStats = Room::query()
            ->with(['bookings' => function ($query) use ($startOfMonth, $endOfMonth): void {
                $query
                    ->whereIn('payment_status', [Booking::PAYMENT_DP, Booking::PAYMENT_LUNAS])
                    ->whereIn('booking_status', [Booking::STATUS_BOOKED, Booking::STATUS_IN_HOUSE, Booking::STATUS_COMPLETED])
                    ->where('check_in_date', '<=', $endOfMonth->toDateString())
                    ->where('check_out_date', '>=', $startOfMonth->toDateString());
            }])
            ->orderBy('name')
            ->get()
            ->map(function (Room $room) use ($daysInMonth, $endOfMonth, $startOfMonth): array {
                $occupiedUnitNights = $room->bookings->sum(function (Booking $booking) use ($endOfMonth, $startOfMonth): int {
                    $checkIn = $booking->check_in_date->greaterThan($startOfMonth)
                        ? $booking->check_in_date->copy()
                        : $startOfMonth->copy();
                    $checkOut = $booking->check_out_date->lessThan($endOfMonth->copy()->addDay())
                        ? $booking->check_out_date->copy()
                        : $endOfMonth->copy()->addDay();

                    return (int) max(0, $checkIn->diffInDays($checkOut)) * max(1, (int) $booking->unit_count);
                });
                $unitCount = max(1, $room->units()->where('is_active', true)->count());
                $availableUnitNights = $daysInMonth * $unitCount;

                return [
                    'name' => $room->name,
                    'occupied_nights' => $occupiedUnitNights,
                    'available_nights' => $availableUnitNights,
                    'occupancy_rate' => $availableUnitNights > 0 ? round(($occupiedUnitNights / $availableUnitNights) * 100, 1) : 0,
                ];
            });

        return view('admin.reports', [
            'periodLabel' => $startOfMonth->translatedFormat('F Y'),
            'bookingCount' => (clone $monthlyBookings)->count(),
            'grossSales' => (clone $monthlyBookings)
                ->where('booking_status', '!=', Booking::STATUS_CANCELLED)
                ->sum('grand_total'),
            'revenueThisMonth' => Payment::query()
                ->whereBetween('validated_at', [$startOfMonth, $endOfMonth])
                ->whereIn('type', [...Payment::INCOMING_TYPES, Payment::TYPE_REFUND])
                ->selectRaw("COALESCE(SUM(CASE WHEN type = ? THEN -amount ELSE amount END), 0) as total", [Payment::TYPE_REFUND])
                ->value('total'),
            'balanceDue' => Booking::query()
                ->whereIn('booking_status', [Booking::STATUS_BOOKED, Booking::STATUS_IN_HOUSE])
                ->sum('balance_due'),
            'pendingPaymentCount' => Booking::query()
                ->where('payment_status', Booking::PAYMENT_PENDING)
                ->where('booking_status', Booking::STATUS_BOOKED)
                ->count(),
            'dailyRevenue' => $dailyRevenue,
            'maxDailyRevenue' => $dailyRevenue->max('total') ?: 0,
            'sourceStats' => $sourceStats,
            'sourceTotal' => $sourceStats->sum('total'),
            'roomStats' => $roomStats,
            'occupancyStats' => $occupancyStats,
            'statusStats' => Booking::query()
                ->select('booking_status', DB::raw('COUNT(*) as total'))
                ->groupBy('booking_status')
                ->orderByDesc('total')
                ->get(),
        ]);
    }
}
