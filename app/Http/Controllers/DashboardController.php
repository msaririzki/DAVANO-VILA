<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $filter = request('filter', 'all');
        $perPage = request('per_page', 10);

        $bookingsQuery = Booking::query()->with('room')->latest();

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
            'pendingCount' => Booking::query()->where('payment_status', Booking::PAYMENT_PENDING)->count(),
            'balanceDue' => Booking::query()->sum('balance_due'),
            'revenueThisMonth' => Booking::query()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('paid_amount'),
            'todayCheckIns' => Booking::query()->whereDate('check_in_date', today())->count(),
            'todayCheckOuts' => Booking::query()->whereDate('check_out_date', today())->count(),
        ]);
    }
}
