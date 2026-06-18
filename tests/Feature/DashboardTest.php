<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_separates_unconfirmed_orders_from_active_receivables(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $room = Room::query()->create([
            'name' => 'Commercial Villa',
            'price' => 450000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);

        $activePending = $this->createBooking($room, 'ACTIVE', [
            'balance_due' => 900000,
            'hold_expires_at' => now()->addHour(),
        ]);
        $expiredPending = $this->createBooking($room, 'EXPIRED', [
            'balance_due' => 450000,
            'hold_expires_at' => now()->subHour(),
        ]);
        $this->createBooking($room, 'CONFIRMED', [
            'grand_total' => 1000000,
            'paid_amount' => 500000,
            'balance_due' => 500000,
            'payment_status' => Booking::PAYMENT_DP,
        ]);
        $this->createBooking($room, 'INHOUSE', [
            'grand_total' => 500000,
            'paid_amount' => 250000,
            'balance_due' => 250000,
            'payment_status' => Booking::PAYMENT_DP,
            'booking_status' => Booking::STATUS_IN_HOUSE,
        ]);
        $transferIssue = $this->createBooking($room, 'ISSUE', [
            'balance_due' => 700000,
            'hold_expires_at' => now()->subHour(),
        ]);
        $this->createBooking($room, 'CANCELLED', [
            'balance_due' => 2000000,
            'payment_status' => Booking::PAYMENT_CANCELLED,
            'booking_status' => Booking::STATUS_CANCELLED,
        ]);

        Payment::query()->create([
            'booking_id' => $transferIssue->id,
            'type' => Payment::TYPE_TRANSFER_ISSUE,
            'amount' => 700000,
            'validated_by' => $admin->id,
            'validated_at' => now(),
            'resolution_status' => Payment::RESOLUTION_UNRESOLVED,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('pendingCount', 1)
            ->assertViewHas('transferIssueCount', 1)
            ->assertViewHas('actionRequiredCount', 2)
            ->assertViewHas('balanceDue', 750000)
            ->assertSee('Ringkasan Villa')
            ->assertSee('Jadwal Hari Ini')
            ->assertSee('Perlu Diproses')
            ->assertSee('Sisa Pembayaran')
            ->assertSee('Rp 750.000')
            ->assertSee('Belum Disahkan')
            ->assertSee('Waktu Bayar Habis')
            ->assertSee('Menunggu Keputusan')
            ->assertSee('waktu bayar masih aktif dan transfer belum dicatat sebagai DP')
            ->assertSee('transfer ditemukan setelah waktu habis atau kamar sudah tidak tersedia')
            ->assertSee('Cek Mutasi Bank')
            ->assertSee('Pesanan Aktif');

        $this->actingAs($admin)
            ->get(route('dashboard', ['status_filter' => 'needs_check']))
            ->assertOk()
            ->assertViewHas('bookings', fn ($bookings): bool => $bookings->total() === 2);

        $this->actingAs($admin)
            ->get(route('dashboard', ['status_filter' => 'awaiting_dp']))
            ->assertOk()
            ->assertViewHas('bookings', fn ($bookings): bool => $bookings->total() === 1
                && $bookings->first()->is($activePending));

        $this->actingAs($admin)
            ->get(route('dashboard', ['status_filter' => 'transfer_issue']))
            ->assertOk()
            ->assertViewHas('bookings', fn ($bookings): bool => $bookings->total() === 1
                && $bookings->first()->is($transferIssue));

        $this->actingAs($admin)
            ->get(route('dashboard', ['status_filter' => 'expired']))
            ->assertOk()
            ->assertViewHas('bookings', fn ($bookings): bool => $bookings->total() === 2);

        $this->actingAs($admin)
            ->get(route('dashboard', ['status_filter' => 'active']))
            ->assertOk()
            ->assertViewHas('bookings', fn ($bookings): bool => $bookings->total() === 1);

        $this->actingAs($admin)
            ->get(route('bookings.show', $expiredPending))
            ->assertOk()
            ->assertSee('Catat Transfer Setelah Waktu Habis')
            ->assertSee('Catat Transfer untuk Diputuskan');

        $this->actingAs($admin)
            ->get(route('bookings.show', $transferIssue))
            ->assertOk()
            ->assertSee('Transfer Perlu Keputusan')
            ->assertSee('Pindah kamar/tanggal lalu terima sebagai DP')
            ->assertSee('data-date-picker', false)
            ->assertSee('resolution-check-in-', false)
            ->assertSee('resolution-check-out-', false)
            ->assertSee('Refund penuh dan batalkan booking');

        $this->assertTrue($activePending->hasActiveHold());
        $this->assertTrue($expiredPending->hasExpiredHold());
    }

    /** @param array<string, mixed> $overrides */
    private function createBooking(Room $room, string $suffix, array $overrides = []): Booking
    {
        return Booking::query()->create(array_merge([
            'booking_code' => 'VLA-DASH-'.$suffix,
            'guest_name' => 'Tamu '.$suffix,
            'guest_phone' => '6281234567890',
            'room_id' => $room->id,
            'check_in_date' => today(),
            'check_out_date' => today()->addDay(),
            'total_room_price' => 450000,
            'grand_total' => 450000,
            'paid_amount' => 0,
            'balance_due' => 450000,
            'payment_status' => Booking::PAYMENT_PENDING,
            'booking_status' => Booking::STATUS_BOOKED,
        ], $overrides));
    }
}
