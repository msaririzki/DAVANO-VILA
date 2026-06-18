<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $filter = request('filter', 'all');
        $perPage = request('per_page', 10);

        $bookingsQuery = Booking::query()->with(['room', 'payments'])->latest();

        if ($filter === 'today') {
            $bookingsQuery->whereDate('created_at', today());
        } elseif ($filter === 'week') {
            $bookingsQuery->where('created_at', '>=', now()->subWeek());
        } elseif ($filter === 'month') {
            $bookingsQuery->where('created_at', '>=', now()->subMonth());
        }

        $bookings = $bookingsQuery->paginate($perPage)->withQueryString();

        return view('dashboard', [
            'bookings' => $bookings,
            'pendingCount' => Booking::query()
                ->where('payment_status', Booking::PAYMENT_PENDING)
                ->where('booking_status', Booking::STATUS_BOOKED)
                ->where('hold_expires_at', '>', now())
                ->count(),
            'transferIssueCount' => Payment::query()
                ->where('type', Payment::TYPE_TRANSFER_ISSUE)
                ->where('resolution_status', Payment::RESOLUTION_UNRESOLVED)
                ->count(),
            'balanceDue' => Booking::query()
                ->whereIn('booking_status', [Booking::STATUS_BOOKED, Booking::STATUS_IN_HOUSE])
                ->sum('balance_due'),
            'revenueThisMonth' => Payment::query()
                ->whereBetween('validated_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->whereIn('type', [...Payment::INCOMING_TYPES, Payment::TYPE_REFUND])
                ->selectRaw('COALESCE(SUM(CASE WHEN type = ? THEN -amount ELSE amount END), 0) as total', [Payment::TYPE_REFUND])
                ->value('total'),
            'todayCheckIns' => Booking::query()
                ->whereDate('check_in_date', today())
                ->whereIn('payment_status', [Booking::PAYMENT_DP, Booking::PAYMENT_LUNAS])
                ->whereNotIn('booking_status', [Booking::STATUS_CANCELLED, Booking::STATUS_NO_SHOW])
                ->count(),
            'todayCheckOuts' => Booking::query()
                ->whereDate('check_out_date', today())
                ->whereNotIn('booking_status', [Booking::STATUS_CANCELLED, Booking::STATUS_NO_SHOW])
                ->count(),
        ]);
    }
}
