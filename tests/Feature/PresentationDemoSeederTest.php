<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PresentationDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresentationDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_presentation_demo_seeder_is_realistic_and_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(PresentationDemoSeeder::class);
        $this->seed(PresentationDemoSeeder::class);

        $demoCodes = collect(range(1, 12))
            ->map(fn (int $number): string => sprintf('VLA-DEMO-%03d', $number));

        $this->assertSame(12, Booking::query()->whereIn('booking_code', $demoCodes)->count());
        $this->assertSame(2, Booking::query()
            ->whereIn('booking_code', $demoCodes)
            ->where('booking_status', Booking::STATUS_IN_HOUSE)
            ->count());
        $this->assertSame(2, Booking::query()
            ->whereIn('booking_code', $demoCodes)
            ->where('booking_status', Booking::STATUS_COMPLETED)
            ->count());

        $this->assertDatabaseHas('bookings', [
            'booking_code' => 'VLA-DEMO-003',
            'booking_status' => Booking::STATUS_BOOKED,
            'payment_status' => Booking::PAYMENT_LUNAS,
            'balance_due' => 0,
        ]);
        $this->assertDatabaseHas('bookings', [
            'booking_code' => 'VLA-DEMO-012',
            'payment_status' => Booking::PAYMENT_DP,
            'paid_amount' => 500000,
        ]);
        $this->assertDatabaseHas('payments', [
            'type' => Payment::TYPE_TRANSFER_ISSUE,
            'transfer_reference' => 'DEMO-LATE-008',
            'resolution_status' => Payment::RESOLUTION_UNRESOLVED,
        ]);
    }
}
