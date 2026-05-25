<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $bookings = Booking::query()
            ->with('room')
            ->latest()
            ->limit(20)
            ->get();

        return view('dashboard', [
            'bookings' => $bookings,
            'bankAccounts' => BankAccount::query()->where('is_active', true)->orderBy('bank_name')->get(),
            'pendingCount' => Booking::query()->where('payment_status', Booking::PAYMENT_PENDING)->count(),
            'balanceDue' => Booking::query()->sum('balance_due'),
            'revenueThisMonth' => Booking::query()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('paid_amount'),
            'heroMediaMode' => Setting::value('hero_media_mode', 'photos'),
        ]);
    }
}
