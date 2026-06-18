<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $filter = in_array(request('filter'), ['today', 'week', 'month'], true)
            ? request('filter')
            : 'all';
        $statusFilter = in_array(request('status_filter'), ['needs_check', 'awaiting_dp', 'transfer_issue', 'expired', 'active'], true)
            ? request('status_filter')
            : 'all';
        $perPage = in_array((int) request('per_page'), [10, 30, 100], true)
            ? (int) request('per_page')
            : 10;

        $bookingsQuery = Booking::query()->with(['room', 'payments'])->latest();

        if ($filter === 'today') {
            $bookingsQuery->whereDate('created_at', today());
        } elseif ($filter === 'week') {
            $bookingsQuery->where('created_at', '>=', now()->subWeek());
        } elseif ($filter === 'month') {
            $bookingsQuery->where('created_at', '>=', now()->subMonth());
        }

        if ($statusFilter === 'needs_check') {
            $bookingsQuery->where(function ($query): void {
                $query
                    ->where(function ($pendingQuery): void {
                        $pendingQuery
                            ->where('payment_status', Booking::PAYMENT_PENDING)
                            ->where('booking_status', Booking::STATUS_BOOKED)
                            ->where(function ($holdQuery): void {
                                $holdQuery
                                    ->whereNull('hold_expires_at')
                                    ->orWhere('hold_expires_at', '>', now());
                            });
                    })
                    ->orWhereHas('payments', function ($paymentQuery): void {
                        $paymentQuery
                            ->where('type', Payment::TYPE_TRANSFER_ISSUE)
                            ->where('resolution_status', Payment::RESOLUTION_UNRESOLVED);
                    });
            });
        } elseif ($statusFilter === 'awaiting_dp') {
            $bookingsQuery
                ->where('payment_status', Booking::PAYMENT_PENDING)
                ->where('booking_status', Booking::STATUS_BOOKED)
                ->where(function ($query): void {
                    $query
                        ->whereNull('hold_expires_at')
                        ->orWhere('hold_expires_at', '>', now());
                });
        } elseif ($statusFilter === 'transfer_issue') {
            $bookingsQuery->whereHas('payments', function ($query): void {
                $query
                    ->where('type', Payment::TYPE_TRANSFER_ISSUE)
                    ->where('resolution_status', Payment::RESOLUTION_UNRESOLVED);
            });
        } elseif ($statusFilter === 'expired') {
            $bookingsQuery
                ->where('payment_status', Booking::PAYMENT_PENDING)
                ->where('booking_status', Booking::STATUS_BOOKED)
                ->where('hold_expires_at', '<=', now());
        } elseif ($statusFilter === 'active') {
            $bookingsQuery
                ->where('booking_status', Booking::STATUS_BOOKED)
                ->whereIn('payment_status', [Booking::PAYMENT_DP, Booking::PAYMENT_LUNAS]);
        }

        $bookings = $bookingsQuery->paginate($perPage)->withQueryString();
        $pendingPaymentCount = Booking::query()
            ->where('payment_status', Booking::PAYMENT_PENDING)
            ->where('booking_status', Booking::STATUS_BOOKED)
            ->where(function ($query): void {
                $query
                    ->whereNull('hold_expires_at')
                    ->orWhere('hold_expires_at', '>', now());
            })
            ->count();
        $transferIssueCount = Payment::query()
            ->where('type', Payment::TYPE_TRANSFER_ISSUE)
            ->where('resolution_status', Payment::RESOLUTION_UNRESOLVED)
            ->count();
        $actionRequiredCount = Booking::query()
            ->where(function ($query): void {
                $query
                    ->where(function ($pendingQuery): void {
                        $pendingQuery
                            ->where('payment_status', Booking::PAYMENT_PENDING)
                            ->where('booking_status', Booking::STATUS_BOOKED)
                            ->where(function ($holdQuery): void {
                                $holdQuery
                                    ->whereNull('hold_expires_at')
                                    ->orWhere('hold_expires_at', '>', now());
                            });
                    })
                    ->orWhereHas('payments', function ($paymentQuery): void {
                        $paymentQuery
                            ->where('type', Payment::TYPE_TRANSFER_ISSUE)
                            ->where('resolution_status', Payment::RESOLUTION_UNRESOLVED);
                    });
            })
            ->count();

        return view('dashboard', [
            'bookings' => $bookings,
            'pendingCount' => $pendingPaymentCount,
            'transferIssueCount' => $transferIssueCount,
            'actionRequiredCount' => $actionRequiredCount,
            'balanceDue' => Booking::query()
                ->whereIn('booking_status', [Booking::STATUS_BOOKED, Booking::STATUS_IN_HOUSE])
                ->where('payment_status', Booking::PAYMENT_DP)
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
