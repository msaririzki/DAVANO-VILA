<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filter = $request->query('filter', 'month');
        $now = now();

        switch ($filter) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $periodLabel = 'Hari Ini (' . $now->translatedFormat('d F Y') . ')';
                $groupFormat = 'Y-m-d H:00';
                $labelFormat = 'H:i';
                break;
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $periodLabel = 'Minggu Ini (' . $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y') . ')';
                $groupFormat = 'Y-m-d';
                $labelFormat = 'D, d M';
                break;
            case '3_months':
                $startDate = $now->copy()->subMonths(2)->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $periodLabel = '3 Bulan Terakhir (' . $startDate->translatedFormat('M') . ' - ' . $endDate->translatedFormat('M Y') . ')';
                $groupFormat = 'Y-W'; // Group by week could be tricky with Carbon, let's group by Y-m-d but only keep weeks. Actually grouping by week is easier with raw sql but let's just group by month or week. Let's do week in php.
                $groupFormat = 'Y-W';
                $labelFormat = '\W\e\e\k W';
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                $periodLabel = 'Tahun Ini (' . $now->translatedFormat('Y') . ')';
                $groupFormat = 'Y-m';
                $labelFormat = 'M Y';
                break;
            case 'month':
            default:
                $filter = 'month';
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $periodLabel = 'Bulan Ini (' . $now->translatedFormat('F Y') . ')';
                $groupFormat = 'Y-m-d';
                $labelFormat = 'd M';
                break;
        }

        $bookingsInRange = Booking::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Get raw payments
        $payments = Payment::query()
            ->select('validated_at', 'amount', 'type')
            ->whereBetween('validated_at', [$startDate, $endDate])
            ->whereIn('type', [...Payment::INCOMING_TYPES, Payment::TYPE_REFUND])
            ->orderBy('validated_at')
            ->get();

        $groupedRevenue = [];

        // 1. Generate full range of labels so the chart is always complete
        if ($filter === 'today') {
            for ($i = 0; $i < 24; $i++) {
                $time = $startDate->copy()->addHours($i);
                $key = $time->format('Y-m-d H:00');
                $groupedRevenue[$key] = [
                    'label' => $time->format('H:00'),
                    'total' => 0,
                ];
            }
        } elseif ($filter === 'week') {
            for ($i = 0; $i < 7; $i++) {
                $day = $startDate->copy()->addDays($i);
                $key = $day->format('Y-m-d');
                $groupedRevenue[$key] = [
                    'label' => $day->translatedFormat('D, d M'),
                    'total' => 0,
                ];
            }
        } elseif ($filter === 'month') {
            $daysInMonth = $startDate->daysInMonth;
            for ($i = 0; $i < $daysInMonth; $i++) {
                $day = $startDate->copy()->addDays($i);
                $key = $day->format('Y-m-d');
                $groupedRevenue[$key] = [
                    'label' => $day->format('d M'),
                    'total' => 0,
                ];
            }
        } elseif ($filter === '3_months') {
            // Group by week for 3 months
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $weekStart = $currentDate->copy()->startOfWeek();
                $key = $weekStart->format('Y-m-d');
                if (!isset($groupedRevenue[$key])) {
                    $groupedRevenue[$key] = [
                        'label' => 'Pekan ' . $weekStart->translatedFormat('d M'),
                        'total' => 0,
                    ];
                }
                $currentDate->addDay();
            }
        } elseif ($filter === 'year') {
            for ($i = 0; $i < 12; $i++) {
                $month = $startDate->copy()->addMonths($i);
                $key = $month->format('Y-m');
                $groupedRevenue[$key] = [
                    'label' => $month->translatedFormat('M Y'),
                    'total' => 0,
                ];
            }
        }

        // 2. Fill in the actual revenue
        foreach ($payments as $payment) {
            if ($filter === '3_months') {
                $groupKey = $payment->validated_at->startOfWeek()->format('Y-m-d');
            } elseif ($filter === 'year') {
                $groupKey = $payment->validated_at->format('Y-m');
            } elseif ($filter === 'today') {
                $groupKey = $payment->validated_at->format('Y-m-d H:00');
            } else {
                $groupKey = $payment->validated_at->format('Y-m-d');
            }

            if (isset($groupedRevenue[$groupKey])) {
                $amount = $payment->type === Payment::TYPE_REFUND ? -$payment->amount : $payment->amount;
                $groupedRevenue[$groupKey]['total'] += $amount;
            }
        }

        $chartLabels = array_column(array_values($groupedRevenue), 'label');
        $chartData = array_column(array_values($groupedRevenue), 'total');

        $sourceStats = Booking::query()
            ->selectRaw("COALESCE(acquisition_source, 'unknown') as source, COUNT(*) as total")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        $roomStats = Room::query()
            ->leftJoin('bookings', function($join) use ($startDate, $endDate) {
                $join->on('rooms.id', '=', 'bookings.room_id')
                     ->whereBetween('bookings.created_at', [$startDate, $endDate]);
            })
            ->select('rooms.id', 'rooms.name')
            ->selectRaw('COUNT(bookings.id) as booking_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN bookings.booking_status != ? THEN bookings.grand_total ELSE 0 END), 0) as gross_total", [Booking::STATUS_CANCELLED])
            ->groupBy('rooms.id', 'rooms.name')
            ->orderByDesc('booking_count')
            ->get();

        $daysInPeriod = (int) max(1, ceil($startDate->floatDiffInDays($endDate)));

        $occupancyStats = \App\Models\RoomUnit::query()
            ->with(['room', 'bookings' => function ($query) use ($startDate, $endDate): void {
                $query
                    ->whereIn('payment_status', [Booking::PAYMENT_DP, Booking::PAYMENT_LUNAS])
                    ->whereIn('booking_status', [Booking::STATUS_BOOKED, Booking::STATUS_IN_HOUSE, Booking::STATUS_COMPLETED])
                    ->where('check_in_date', '<=', $endDate->toDateString())
                    ->where('check_out_date', '>=', $startDate->toDateString());
            }])
            ->where('is_active', true)
            ->get()
            ->map(function (\App\Models\RoomUnit $unit) use ($daysInPeriod, $endDate, $startDate): array {
                $occupiedNights = $unit->bookings->sum(function (Booking $booking) use ($endDate, $startDate): int {
                    $checkIn = $booking->check_in_date->greaterThan($startDate)
                        ? $booking->check_in_date->copy()
                        : $startDate->copy();
                    $checkOut = $booking->check_out_date->lessThan($endDate->copy()->addDay())
                        ? $booking->check_out_date->copy()
                        : $endDate->copy()->addDay();

                    return (int) max(0, $checkIn->diffInDays($checkOut));
                });

                $availableNights = $daysInPeriod;

                $roomName = $unit->room->name ?? '';
                $unitName = $unit->name;
                $displayName = str_starts_with($unitName, $roomName) ? $unitName : trim($roomName . ' ' . $unitName);

                return [
                    'name' => $displayName,
                    'occupied_nights' => $occupiedNights,
                    'available_nights' => $availableNights,
                    'occupancy_rate' => $availableNights > 0 ? round(($occupiedNights / $availableNights) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('occupancy_rate')
            ->values();

        return view('admin.reports', [
            'filter' => $filter,
            'periodLabel' => $periodLabel,
            'bookingCount' => (clone $bookingsInRange)->count(),
            'grossSales' => (clone $bookingsInRange)
                ->where('booking_status', '!=', Booking::STATUS_CANCELLED)
                ->sum('grand_total'),
            'revenueThisPeriod' => Payment::query()
                ->whereBetween('validated_at', [$startDate, $endDate])
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
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'sourceStats' => $sourceStats,
            'sourceTotal' => $sourceStats->sum('total'),
            'roomStats' => $roomStats,
            'occupancyStats' => $occupancyStats,
        ]);
    }
}
