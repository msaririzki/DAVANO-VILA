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

        $paidPaymentTypes = [
            Payment::TYPE_BOOKING_DP,
            Payment::TYPE_BOOKING_LUNAS,
            Payment::TYPE_ADDON,
        ];

        $dailyRevenue = Payment::query()
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('type', $paidPaymentTypes)
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
            ->selectRaw('COALESCE(SUM(bookings.grand_total), 0) as gross_total')
            ->groupBy('rooms.id', 'rooms.name')
            ->orderByDesc('booking_count')
            ->get();

        $daysInMonth = $startOfMonth->daysInMonth;
        $occupancyStats = Room::query()
            ->with(['bookings' => function ($query) use ($startOfMonth, $endOfMonth): void {
                $query
                    ->where('payment_status', '!=', Booking::PAYMENT_CANCELLED)
                    ->where('check_in_date', '<=', $endOfMonth->toDateString())
                    ->where('check_out_date', '>=', $startOfMonth->toDateString());
            }])
            ->orderBy('name')
            ->get()
            ->map(function (Room $room) use ($daysInMonth, $endOfMonth, $startOfMonth): array {
                $occupiedNights = $room->bookings->sum(function (Booking $booking) use ($endOfMonth, $startOfMonth): int {
                    $checkIn = $booking->check_in_date->greaterThan($startOfMonth)
                        ? $booking->check_in_date->copy()
                        : $startOfMonth->copy();
                    $checkOut = $booking->check_out_date->lessThan($endOfMonth->copy()->addDay())
                        ? $booking->check_out_date->copy()
                        : $endOfMonth->copy()->addDay();

                    return (int) max(0, $checkIn->diffInDays($checkOut));
                });

                return [
                    'name' => $room->name,
                    'occupied_nights' => $occupiedNights,
                    'available_nights' => $daysInMonth,
                    'occupancy_rate' => $daysInMonth > 0 ? round(($occupiedNights / $daysInMonth) * 100, 1) : 0,
                ];
            });

        return view('admin.reports', [
            'periodLabel' => $startOfMonth->translatedFormat('F Y'),
            'bookingCount' => (clone $monthlyBookings)->count(),
            'grossSales' => (clone $monthlyBookings)->sum('grand_total'),
            'revenueThisMonth' => Payment::query()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->whereIn('type', $paidPaymentTypes)
                ->sum('amount'),
            'balanceDue' => Booking::query()->sum('balance_due'),
            'pendingPaymentCount' => Booking::query()->where('payment_status', Booking::PAYMENT_PENDING)->count(),
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
