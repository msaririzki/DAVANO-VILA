<?php

namespace Tests\Feature;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomUnit;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_pending_booking(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Test',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);

        $response = $this->post(route('public.bookings.store'), [
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'guest_name' => 'Tamu Test',
            'guest_phone' => '628123456789',
            'adult_count' => 2,
            'child_count' => 0,
            'unit_count' => 1,
            'acquisition_source' => 'Google',
        ]);

        $booking = Booking::query()->first();

        $response->assertRedirectContains('/bookings/'.$booking->public_token);
        $response->assertRedirectContains('signature=');
        $this->assertNotSame((string) $booking->id, basename(parse_url($response->headers->get('Location'), PHP_URL_PATH)));
        $this->assertDatabaseHas('bookings', [
            'guest_name' => 'Tamu Test',
            'payment_status' => Booking::PAYMENT_PENDING,
            'booking_status' => Booking::STATUS_BOOKED,
            'grand_total' => 500000,
        ]);
        $this->assertNotNull($booking->payment_deadline_at);
        $this->assertNotNull($booking->hold_expires_at);
        $this->assertTrue($booking->payment_deadline_at->isFuture());
        $this->assertTrue($booking->hold_expires_at->isFuture());
        $this->assertEquals(30, $booking->payment_deadline_at->diffInMinutes($booking->hold_expires_at));
    }

    public function test_guest_can_request_extra_bed_and_note_during_booking(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Extra',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $extraBed = AddonItem::query()->create([
            'name' => 'Extra Bed',
            'type' => AddonItem::TYPE_EXTRA_BED,
            'price' => 150000,
            'is_active' => true,
        ]);

        $response = $this->post(route('public.bookings.store'), [
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'guest_name' => 'Tamu Extra',
            'guest_phone' => '628123456789',
            'adult_count' => 2,
            'child_count' => 0,
            'unit_count' => 1,
            'acquisition_source' => 'Google',
            'guest_request' => 'Datang malam, siapkan extra bed dekat ruang tamu.',
            'extra_bed_item_id' => $extraBed->id,
            'extra_bed_qty' => 2,
        ]);

        $booking = Booking::query()->where('guest_name', 'Tamu Extra')->firstOrFail();

        $response->assertRedirectContains('/bookings/'.$booking->public_token);
        $this->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee('Extra Bed')
            ->assertSee('Datang malam, siapkan extra bed dekat ruang tamu.');
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'guest_request' => 'Datang malam, siapkan extra bed dekat ruang tamu.',
            'total_room_price' => 500000,
            'total_addons_price' => 300000,
            'grand_total' => 800000,
            'balance_due' => 800000,
        ]);
        $this->assertDatabaseHas('booking_addons', [
            'booking_id' => $booking->id,
            'addon_item_id' => $extraBed->id,
            'item_name' => 'Extra Bed',
            'type' => AddonItem::TYPE_EXTRA_BED,
            'qty' => 2,
            'subtotal' => 300000,
            'payment_status' => BookingAddon::PAYMENT_PENDING,
        ]);
    }

    public function test_active_hold_blocks_stock_and_expired_hold_releases_it(): void
    {
        $room = Room::query()->create([
            'name' => 'Hold Suite',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $checkIn = now()->addDay()->toDateString();
        $checkOut = now()->addDays(2)->toDateString();

        $this->post(route('public.bookings.store'), [
            'room_id' => $room->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'guest_name' => 'Pemegang Hold',
            'guest_phone' => '628111111111',
            'adult_count' => 2,
            'child_count' => 0,
            'unit_count' => 1,
        ])->assertRedirect();

        $booking = Booking::query()->where('guest_name', 'Pemegang Hold')->firstOrFail();

        $this->assertSame(0, $room->fresh()->availableUnitCount($checkIn, $checkOut));
        $this->get(route('public.rooms.index', [
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
        ]))
            ->assertOk()
            ->assertSee('Sedang ditahan tamu lain');

        $booking->update([
            'payment_deadline_at' => now()->subMinute(),
            'hold_expires_at' => now()->addMinutes(29),
        ]);

        $this->assertTrue($booking->fresh()->isInAdminGracePeriod());
        $this->assertSame(0, $room->fresh()->availableUnitCount($checkIn, $checkOut));

        $booking->update(['hold_expires_at' => now()->subMinute()]);

        $this->assertSame(1, $room->fresh()->availableUnitCount($checkIn, $checkOut));
    }

    public function test_admin_can_validate_transfer_during_grace_period_after_public_deadline(): void
    {
        Setting::query()->updateOrCreate(['key_name' => 'min_dp_percent'], ['value' => '50']);
        $room = Room::query()->create([
            'name' => 'Grace Period Suite',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-GRACE-PERIOD',
            'guest_name' => 'Tamu Masa Toleransi',
            'guest_phone' => '628111111112',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
            'payment_deadline_at' => now()->subMinute(),
            'hold_expires_at' => now()->addMinutes(29),
        ]);
        $bank = BankAccount::query()->create([
            'bank_name' => 'Mandiri',
            'account_number' => '9876543210123456',
            'account_name' => 'PT Dafano Villa',
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->get(URL::signedRoute('public.bookings.show', ['booking' => $booking->public_token]))
            ->assertOk()
            ->assertSee('Batas 30 menit sudah habis')
            ->assertDontSee($bank->account_number);

        $this->actingAs($superAdmin)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSee('Masa toleransi admin aktif')
            ->assertSee('Validasi Transfer Masa Toleransi');

        $this->actingAs($superAdmin)
            ->post(route('bookings.payments.store', $booking), [
                'amount' => 250000,
                'bank_account_id' => $bank->id,
                'transfer_reference' => 'REF-GRACE-PERIOD',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'type' => Payment::TYPE_BOOKING_DP,
            'transfer_reference' => 'REF-GRACE-PERIOD',
        ]);
        $this->assertDatabaseMissing('payments', [
            'booking_id' => $booking->id,
            'type' => Payment::TYPE_TRANSFER_ISSUE,
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => Booking::PAYMENT_DP,
            'payment_deadline_at' => null,
            'hold_expires_at' => null,
        ]);
    }

    public function test_expired_hold_transfer_is_recorded_as_issue_and_can_be_refunded(): void
    {
        $room = Room::query()->create([
            'name' => 'Expired Hold Suite',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-EXPIRED-HOLD',
            'guest_name' => 'Tamu Terlambat',
            'guest_phone' => '628111111111',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
            'hold_expires_at' => now()->subMinute(),
        ]);
        $bank = BankAccount::query()->create([
            'bank_name' => 'BCA',
            'account_number' => '123',
            'account_name' => 'PT Dafano Villa',
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->post(route('bookings.payments.store', $booking), [
                'amount' => 250000,
                'bank_account_id' => $bank->id,
                'transfer_reference' => 'REF-EXPIRED-HOLD',
            ])
            ->assertRedirect();

        $issue = Payment::query()
            ->where('booking_id', $booking->id)
            ->where('type', Payment::TYPE_TRANSFER_ISSUE)
            ->firstOrFail();

        $this->actingAs($superAdmin)
            ->patch(route('bookings.transfer-issues.update', [$booking, $issue]), [
                'resolution_action' => 'refund',
                'refund_bank_account_id' => $bank->id,
                'refund_reference' => 'REFUND-EXPIRED-HOLD',
                'resolution_note' => 'Stok sudah penuh, dana dikembalikan.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'id' => $issue->id,
            'resolution_status' => Payment::RESOLUTION_REFUNDED,
        ]);
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'type' => Payment::TYPE_TRANSFER_ISSUE_REFUND,
            'amount' => 250000,
            'transfer_reference' => 'REFUND-EXPIRED-HOLD',
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => Booking::STATUS_CANCELLED,
            'payment_status' => Booking::PAYMENT_CANCELLED,
        ]);
    }

    public function test_transfer_issue_can_be_accepted_after_dates_are_verified_again(): void
    {
        Setting::query()->updateOrCreate(['key_name' => 'min_dp_percent'], ['value' => '50']);
        $room = Room::query()->create([
            'name' => 'Recovered Hold Suite',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-RECOVER-HOLD',
            'guest_name' => 'Tamu Dipindahkan',
            'guest_phone' => '628111111111',
            'room_id' => $room->id,
            'adult_count' => 2,
            'total_guest_count' => 2,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
            'hold_expires_at' => now()->subMinute(),
        ]);
        $bank = BankAccount::query()->create([
            'bank_name' => 'BNI',
            'account_number' => '456',
            'account_name' => 'CV Dafano',
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)->post(route('bookings.payments.store', $booking), [
            'amount' => 250000,
            'bank_account_id' => $bank->id,
            'transfer_reference' => 'REF-RECOVER-HOLD',
        ]);
        $issue = Payment::query()->where('type', Payment::TYPE_TRANSFER_ISSUE)->firstOrFail();

        $this->actingAs($superAdmin)
            ->patch(route('bookings.transfer-issues.update', [$booking, $issue]), [
                'resolution_action' => 'accept',
                'room_id' => $room->id,
                'unit_count' => 1,
                'check_in_date' => now()->addDays(3)->toDateString(),
                'check_out_date' => now()->addDays(4)->toDateString(),
                'resolution_note' => 'Tanggal dipindahkan dan stok tersedia.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'id' => $issue->id,
            'type' => Payment::TYPE_BOOKING_DP,
            'resolution_status' => Payment::RESOLUTION_ACCEPTED,
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => Booking::PAYMENT_DP,
            'paid_amount' => 250000,
            'balance_due' => 250000,
            'hold_expires_at' => null,
        ]);
    }

    public function test_guest_can_book_directly_from_room_card_with_dates(): void
    {
        $room = Room::query()->create([
            'name' => 'Direct Suite',
            'price' => 450000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);

        $this->withHeader('Accept-Language', 'id-ID,id;q=0.9')
            ->get(route('public.home'))
            ->assertOk()
            ->assertSee('Direct Suite')
            ->assertSee('Pesan Sekarang')
            ->assertSee('id="booking-form-'.$room->id.'" class="scroll-mt-32 mt-4 pt-6 border-t border-neutral-100 hidden', false)
            ->assertSee(__('public.complete_booking_data'))
            ->assertSee('name="check_in_date"', false)
            ->assertSee('name="check_out_date"', false);

        $response = $this->post(route('public.bookings.store'), [
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'guest_name' => 'Tamu Direct',
            'guest_phone' => '628123456789',
            'adult_count' => 2,
            'child_count' => 0,
            'unit_count' => 1,
            'acquisition_source' => 'Instagram',
        ]);

        $booking = Booking::query()->where('guest_name', 'Tamu Direct')->firstOrFail();

        $response->assertRedirectContains('/bookings/'.$booking->public_token);
        $this->assertDatabaseHas('bookings', [
            'guest_name' => 'Tamu Direct',
            'grand_total' => 900000,
        ]);
    }

    public function test_room_search_results_do_not_repeat_visible_date_fields_inside_booking_cards(): void
    {
        Room::query()->create([
            'name' => 'Date Search Suite',
            'price' => 450000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);

        $response = $this->get(route('public.rooms.index', [
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
        ]));

        $response->assertOk()
            ->assertSee('Date Search Suite')
            ->assertSee('<input type="hidden" name="check_in_date"', false)
            ->assertSee('<input type="hidden" name="check_out_date"', false)
            ->assertDontSee('onclick="showRoomBookingForm', false);
    }

    public function test_public_booking_detail_requires_signed_token_url(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Test',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);

        $booking = Booking::query()->create([
            'booking_code' => 'VLA-TEST-URL',
            'guest_name' => 'Tamu Test',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);

        $this->get('/bookings/'.$booking->id)->assertNotFound();
        $this->get('/bookings/'.$booking->public_token)->assertForbidden();
    }

    public function test_public_booking_detail_language_switch_keeps_signed_url_valid(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Locale',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);

        $booking = Booking::query()->create([
            'booking_code' => 'VLA-TEST-LANG',
            'guest_name' => 'Tamu Locale',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);

        $signedUrl = URL::signedRoute('public.bookings.show', [
            'booking' => $booking->public_token,
        ]);

        $this->get($signedUrl.'&lang=en')
            ->assertRedirect($signedUrl);

        $this->get($signedUrl)
            ->assertOk()
            ->assertSee('Reservation received');
    }

    public function test_paid_booking_blocks_room_availability(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Test',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);

        Booking::query()->create([
            'booking_code' => 'VLA-TEST-0001',
            'guest_name' => 'Tamu Test',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
            'total_room_price' => 1000000,
            'grand_total' => 1000000,
            'paid_amount' => 500000,
            'balance_due' => 500000,
            'payment_status' => Booking::PAYMENT_DP,
        ]);

        $response = $this->withHeader('Accept-Language', 'id-ID,id;q=0.9')
            ->get(route('public.rooms.index', [
                'check_in_date' => now()->addDays(2)->toDateString(),
                'check_out_date' => now()->addDays(4)->toDateString(),
            ]));

        $response->assertOk();
        $response->assertSee('Suite Test');
        $response->assertSee('Penuh pada tanggal ini');
        $response->assertSee('Kamar tidak dapat dipesan untuk tanggal ini');
    }

    public function test_only_super_admin_can_validate_payment(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Test',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-TEST-0001',
            'guest_name' => 'Tamu Test',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);
        $bankAccount = BankAccount::query()->create([
            'bank_name' => 'BCA',
            'account_number' => '123',
            'account_name' => 'PT Dafano Villa',
            'is_active' => true,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)
            ->post(route('bookings.payments.store', $booking), [
                'amount' => 250000,
                'type' => 'booking_dp',
                'bank_account_id' => $bankAccount->id,
            ])
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->post(route('bookings.payments.store', $booking), [
                'amount' => 250000,
                'bank_account_id' => $bankAccount->id,
                'transfer_reference' => 'BCA-DP-0001',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => Booking::PAYMENT_DP,
            'paid_amount' => 250000,
            'balance_due' => 250000,
        ]);
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'type' => Payment::TYPE_BOOKING_DP,
            'amount' => 250000,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $superAdmin->id,
            'action' => 'payment.validated',
            'category' => 'financial',
            'is_financial' => true,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('bookings.payments.store', $booking), [
                'amount' => 250000,
                'bank_account_id' => $bankAccount->id,
                'transfer_reference' => 'BCA-LUNAS-0001',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => Booking::PAYMENT_LUNAS,
            'paid_amount' => 500000,
            'balance_due' => 0,
        ]);
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'type' => Payment::TYPE_BOOKING_LUNAS,
            'amount' => 250000,
        ]);
    }

    public function test_addons_require_super_admin_payment_validation(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Test',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-TEST-0002',
            'guest_name' => 'Tamu Test',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'paid_amount' => 250000,
            'balance_due' => 250000,
            'payment_status' => Booking::PAYMENT_DP,
            'booking_status' => Booking::STATUS_IN_HOUSE,
        ]);
        $addonItem = AddonItem::query()->create([
            'name' => 'Nasi Goreng',
            'type' => AddonItem::TYPE_FOOD,
            'price' => 35000,
            'is_active' => true,
        ]);
        $bankAccount = BankAccount::query()->create([
            'bank_name' => 'BCA',
            'account_number' => '123',
            'account_name' => 'PT Dafano Villa',
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        Payment::query()->create([
            'booking_id' => $booking->id,
            'type' => Payment::TYPE_BOOKING_DP,
            'amount' => 250000,
            'bank_account_id' => $bankAccount->id,
            'transfer_reference' => 'BCA-BOOKING-DP-0002',
            'validated_by' => $superAdmin->id,
            'validated_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->post(route('bookings.addons.store', $booking), [
                'addon_item_id' => $addonItem->id,
                'qty' => 2,
            ])
            ->assertRedirect();

        $addon = BookingAddon::query()->first();
        $this->assertDatabaseHas('booking_addons', [
            'id' => $addon->id,
            'subtotal' => 70000,
            'payment_status' => BookingAddon::PAYMENT_PENDING,
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'grand_total' => 570000,
            'paid_amount' => 250000,
            'balance_due' => 320000,
        ]);

        $this->actingAs($admin)
            ->post(route('booking-addons.payments.store', $addon), [
                'amount' => 70000,
                'bank_account_id' => $bankAccount->id,
            ])
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->post(route('booking-addons.payments.store', $addon), [
                'amount' => 70000,
                'bank_account_id' => $bankAccount->id,
                'transfer_reference' => 'BCA-ADDON-0001',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('booking_addons', [
            'id' => $addon->id,
            'payment_status' => BookingAddon::PAYMENT_PAID,
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'paid_amount' => 320000,
            'balance_due' => 250000,
        ]);
    }

    public function test_super_admin_can_cancel_unpaid_addon_and_remove_it_from_total_bill(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Cancel Addon',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-CANCEL-ADDON',
            'guest_name' => 'Tamu Test',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'total_addons_price' => 50000,
            'grand_total' => 550000,
            'balance_due' => 550000,
            'payment_status' => Booking::PAYMENT_PENDING,
            'booking_status' => Booking::STATUS_BOOKED,
        ]);
        $addon = BookingAddon::query()->create([
            'booking_id' => $booking->id,
            'item_name' => 'Sarapan',
            'type' => BookingAddon::TYPE_FOOD,
            'qty' => 2,
            'price' => 25000,
            'subtotal' => 50000,
            'payment_status' => BookingAddon::PAYMENT_PENDING,
        ]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->patch(route('booking-addons.cancel', $addon))
            ->assertRedirect();

        $this->assertDatabaseHas('booking_addons', [
            'id' => $addon->id,
            'payment_status' => BookingAddon::PAYMENT_CANCELLED,
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'total_addons_price' => 0,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);
    }

    public function test_super_admin_can_cancel_all_unpaid_addons_at_once(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Cancel All Addons',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-CANCEL-ALL',
            'guest_name' => 'Tamu Test',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'total_addons_price' => 75000,
            'grand_total' => 575000,
            'balance_due' => 575000,
            'payment_status' => Booking::PAYMENT_PENDING,
            'booking_status' => Booking::STATUS_BOOKED,
        ]);
        foreach ([['Sarapan', 25000], ['Extra Bed', 50000]] as [$name, $subtotal]) {
            BookingAddon::query()->create([
                'booking_id' => $booking->id,
                'item_name' => $name,
                'type' => BookingAddon::TYPE_FOOD,
                'qty' => 1,
                'price' => $subtotal,
                'subtotal' => $subtotal,
                'payment_status' => BookingAddon::PAYMENT_PENDING,
            ]);
        }
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->patch(route('bookings.addons.cancel-all', $booking))
            ->assertRedirect();

        $this->assertSame(2, BookingAddon::query()->where('payment_status', BookingAddon::PAYMENT_CANCELLED)->count());
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'total_addons_price' => 0,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);
    }

    public function test_admin_can_add_guest_orders_and_totals_respect_dp(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Order',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-TEST-ORDER',
            'guest_name' => 'Tamu Order',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);
        $addonItem = AddonItem::query()->create([
            'name' => 'Nasi Goreng',
            'category' => AddonItem::CATEGORY_MAKANAN,
            'price' => 25000,
            'is_active' => true,
        ]);
        $bankAccount = BankAccount::query()->create([
            'bank_name' => 'BCA',
            'account_number' => '123',
            'account_name' => 'PT Dafano Villa',
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->post(route('bookings.payments.store', $booking), [
                'amount' => 250000,
                'bank_account_id' => $bankAccount->id,
                'transfer_reference' => 'BCA-ORDER-DP',
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('bookings.addons.store', $booking), [
                'addon_item_id' => $addonItem->id,
                'qty' => 2,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('booking_addons', [
            'booking_id' => $booking->id,
            'item_name' => 'Nasi Goreng',
            'category' => AddonItem::CATEGORY_MAKANAN,
            'qty' => 2,
            'subtotal' => 50000,
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'total_addons_price' => 50000,
            'grand_total' => 550000,
            'paid_amount' => 250000,
            'balance_due' => 300000,
            'payment_status' => Booking::PAYMENT_DP,
        ]);
    }

    public function test_admin_and_super_admin_can_download_invoice_pdf(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Invoice',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-TEST-PDF',
            'guest_name' => 'Tamu Invoice',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $adminResponse = $this->actingAs($admin)
            ->get(route('bookings.invoice', $booking))
            ->assertOk();
        $this->assertStringStartsWith('%PDF', $adminResponse->getContent());

        $response = $this->actingAs($superAdmin)
            ->get(route('bookings.invoice', $booking));

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('tagihan-dafano-villa-VLA-TEST-PDF.pdf', $response->headers->get('content-disposition'));
    }

    public function test_signed_public_receipt_can_be_opened_and_admin_can_prepare_whatsapp_message(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Receipt',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-TEST-RECEIPT',
            'guest_name' => 'Tamu Resi',
            'guest_phone' => '081234567890',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 250000,
            'paid_amount' => 250000,
            'payment_status' => Booking::PAYMENT_DP,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $signedReceiptUrl = URL::temporarySignedRoute(
            'public.bookings.receipt',
            now()->addDays(30),
            ['booking' => $booking->public_token],
        );

        $this->get(route('public.bookings.receipt', ['booking' => $booking->public_token]))
            ->assertForbidden();

        $receiptResponse = $this->get($signedReceiptUrl);
        $receiptResponse->assertOk();
        $this->assertStringStartsWith('%PDF', $receiptResponse->getContent());
        $this->assertStringContainsString('inline', $receiptResponse->headers->get('content-disposition'));

        $shareResponse = $this->actingAs($admin)
            ->post(route('bookings.receipt.send', $booking));

        $shareResponse->assertRedirect();
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $shareResponse->headers->get('Location'));
        $this->assertStringContainsString(rawurlencode('VLA-TEST-RECEIPT'), $shareResponse->headers->get('Location'));
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'booking.receipt_whatsapp_opened',
            'auditable_type' => (new Booking)->getMorphClass(),
            'auditable_id' => $booking->id,
        ]);
    }

    public function test_addon_item_category_validation_and_type_mapping(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->post(route('addon-items.store'), [
                'name' => 'Kentang Goreng',
                'category' => 'invalid',
                'price' => 20000,
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('category');

        $this->actingAs($superAdmin)
            ->post(route('addon-items.store'), [
                'name' => 'Kentang Goreng',
                'category' => AddonItem::CATEGORY_CAMILAN,
                'price' => 20000,
                'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('addon_items', [
            'name' => 'Kentang Goreng',
            'type' => AddonItem::TYPE_FOOD,
            'category' => AddonItem::CATEGORY_CAMILAN,
            'price' => 20000,
        ]);
    }

    public function test_checkout_requires_lunas_and_moves_room_to_cleaning(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Test',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-TEST-0003',
            'guest_name' => 'Tamu Test',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'paid_amount' => 250000,
            'balance_due' => 250000,
            'payment_status' => Booking::PAYMENT_DP,
            'booking_status' => Booking::STATUS_IN_HOUSE,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('bookings.status.update', $booking), [
                'booking_status' => Booking::STATUS_COMPLETED,
            ])
            ->assertSessionHasErrors('booking_status');

        $booking->update([
            'payment_status' => Booking::PAYMENT_LUNAS,
            'paid_amount' => 500000,
            'balance_due' => 0,
        ]);

        $this->actingAs($admin)
            ->patch(route('bookings.status.update', $booking), [
                'booking_status' => Booking::STATUS_COMPLETED,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => Booking::STATUS_COMPLETED,
        ]);
        $this->assertDatabaseHas('room_units', [
            'room_id' => $room->id,
            'status' => Room::STATUS_CLEANING,
        ]);
    }

    public function test_checkin_assigns_an_available_room_unit_automatically(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Auto Checkin',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-AUTO-CHECKIN',
            'guest_name' => 'Tamu Checkin',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'unit_count' => 1,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDay()->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'paid_amount' => 250000,
            'balance_due' => 250000,
            'payment_status' => Booking::PAYMENT_DP,
            'booking_status' => Booking::STATUS_BOOKED,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('bookings.status.update', $booking), [
                'booking_status' => Booking::STATUS_IN_HOUSE,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => Booking::STATUS_IN_HOUSE,
        ]);
        $this->assertDatabaseCount('booking_room_unit', 1);
    }

    public function test_checkin_requires_configured_minimum_dp_not_just_any_payment(): void
    {
        Setting::query()->updateOrCreate(['key_name' => 'min_dp_percent'], ['value' => '50']);
        $room = Room::query()->create([
            'name' => 'Suite Minimum DP',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-MIN-DP',
            'guest_name' => 'Tamu DP Kecil',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'unit_count' => 1,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDay()->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'paid_amount' => 15000,
            'balance_due' => 485000,
            'payment_status' => Booking::PAYMENT_DP,
            'booking_status' => Booking::STATUS_BOOKED,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('bookings.status.update', $booking), [
                'booking_status' => Booking::STATUS_IN_HOUSE,
            ])
            ->assertSessionHasErrors('booking_status');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => Booking::STATUS_BOOKED,
        ]);
        $this->assertDatabaseMissing('booking_room_unit', ['booking_id' => $booking->id]);
    }

    public function test_no_show_releases_assigned_unit_and_room_availability(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite No Show',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $checkIn = now()->addDay()->toDateString();
        $checkOut = now()->addDays(2)->toDateString();
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-NO-SHOW',
            'guest_name' => 'Tamu No Show',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'unit_count' => 1,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'paid_amount' => 250000,
            'balance_due' => 250000,
            'payment_status' => Booking::PAYMENT_DP,
            'booking_status' => Booking::STATUS_BOOKED,
        ]);
        $booking->units()->attach($room->units()->first()->id);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertFalse(Room::query()->availableBetween($checkIn, $checkOut)->whereKey($room->id)->exists());

        $this->actingAs($admin)
            ->patch(route('bookings.status.update', $booking), [
                'booking_status' => Booking::STATUS_NO_SHOW,
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('booking_room_unit', ['booking_id' => $booking->id]);
        $this->assertTrue(Room::query()->availableBetween($checkIn, $checkOut)->whereKey($room->id)->exists());
    }

    public function test_staff_can_create_internal_booking_for_guest(): void
    {
        $room = Room::query()->create([
            'name' => 'Internal Suite',
            'price' => 600000,
            'capacity' => 3,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $checkIn = now()->addDay()->toDateString();
        $checkOut = now()->addDays(3)->toDateString();

        $this->actingAs($admin)
            ->get(route('bookings.create', [
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
            ]))
            ->assertOk()
            ->assertSee('Buat Pemesanan Tamu')
            ->assertSee('Internal Suite')
            ->assertSee('Total 2 malam');

        $response = $this->actingAs($admin)
            ->post(route('bookings.store'), [
                'room_id' => $room->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'guest_name' => 'Tamu Dibantu Admin',
                'guest_phone' => '628123456789',
                'adult_count' => 2,
                'child_count' => 1,
                'unit_count' => 1,
                'acquisition_source' => 'Walk-in',
            ]);

        $booking = Booking::query()->where('guest_name', 'Tamu Dibantu Admin')->firstOrFail();

        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'room_id' => $room->id,
            'total_room_price' => 1200000,
            'grand_total' => 1200000,
            'balance_due' => 1200000,
            'payment_status' => Booking::PAYMENT_PENDING,
            'booking_status' => Booking::STATUS_BOOKED,
            'acquisition_source' => 'Walk-in',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'booking.created_internal',
            'auditable_type' => (new Booking)->getMorphClass(),
            'auditable_id' => $booking->id,
        ]);
    }

    public function test_multi_unit_room_stock_is_locked_only_after_dp_or_lunas(): void
    {
        $room = Room::query()->create([
            'name' => 'Commercial Test',
            'price' => 450000,
            'capacity' => 2,
            'included_capacity' => 2,
            'max_capacity' => 2,
            'allow_unit_quantity' => true,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);

        foreach (range(2, 6) as $number) {
            RoomUnit::query()->create([
                'room_id' => $room->id,
                'name' => 'Commercial Test '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'is_active' => true,
                'status' => Room::STATUS_AVAILABLE,
            ]);
        }

        $checkIn = now()->addDay()->toDateString();
        $checkOut = now()->addDays(2)->toDateString();

        Booking::query()->create([
            'booking_code' => 'VLA-PENDING-UNIT',
            'guest_name' => 'Pending Unit',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'unit_count' => 4,
            'adult_count' => 4,
            'child_count' => 0,
            'total_guest_count' => 4,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_room_price' => 1800000,
            'grand_total' => 1800000,
            'balance_due' => 1800000,
            'payment_status' => Booking::PAYMENT_PENDING,
        ]);

        $this->assertSame(6, $room->fresh()->availableUnitCount($checkIn, $checkOut));

        Booking::query()->create([
            'booking_code' => 'VLA-DP-UNIT',
            'guest_name' => 'DP Unit',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'unit_count' => 5,
            'adult_count' => 5,
            'child_count' => 0,
            'total_guest_count' => 5,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_room_price' => 2250000,
            'grand_total' => 2250000,
            'paid_amount' => 1125000,
            'balance_due' => 1125000,
            'payment_status' => Booking::PAYMENT_DP,
        ]);

        $this->assertSame(1, $room->fresh()->availableUnitCount($checkIn, $checkOut));

        $this->get(route('public.rooms.index', [
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
        ]))
            ->assertOk()
            ->assertSee('Commercial Test');
    }

    public function test_admin_can_assign_available_physical_unit_and_conflicts_are_rejected(): void
    {
        $room = Room::query()->create([
            'name' => 'Assign Unit Suite',
            'price' => 450000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $firstUnit = $room->units()->first();
        $secondUnit = RoomUnit::query()->create([
            'room_id' => $room->id,
            'name' => 'Assign Unit Suite 02',
            'is_active' => true,
            'status' => Room::STATUS_AVAILABLE,
        ]);
        $checkIn = now()->addDay()->toDateString();
        $checkOut = now()->addDays(2)->toDateString();
        $blockingBooking = Booking::query()->create([
            'booking_code' => 'VLA-BLOCK-UNIT',
            'guest_name' => 'Blocking Unit',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'unit_count' => 1,
            'adult_count' => 2,
            'child_count' => 0,
            'total_guest_count' => 2,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_room_price' => 450000,
            'grand_total' => 450000,
            'paid_amount' => 225000,
            'balance_due' => 225000,
            'payment_status' => Booking::PAYMENT_DP,
        ]);
        $blockingBooking->units()->attach($firstUnit->id);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-ASSIGN-UNIT',
            'guest_name' => 'Need Unit',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'unit_count' => 1,
            'adult_count' => 2,
            'child_count' => 0,
            'total_guest_count' => 2,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_room_price' => 450000,
            'grand_total' => 450000,
            'balance_due' => 450000,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('bookings.units.update', $booking), [
                'room_unit_ids' => [$firstUnit->id],
            ])
            ->assertSessionHasErrors('room_unit_ids');

        $this->actingAs($admin)
            ->patch(route('bookings.units.update', $booking), [
                'room_unit_ids' => [$secondUnit->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('booking_room_unit', [
            'booking_id' => $booking->id,
            'room_unit_id' => $secondUnit->id,
        ]);
    }

    public function test_super_admin_can_add_manual_occupancy_charge_after_dp(): void
    {
        $room = Room::query()->create([
            'name' => 'Villa Besar Test',
            'price' => 2750000,
            'capacity' => 20,
            'included_capacity' => 15,
            'max_capacity' => 20,
            'extra_guest_charge_mode' => 'manual',
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-OCCUPANCY',
            'guest_name' => 'Tamu Rombongan',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'unit_count' => 1,
            'adult_count' => 16,
            'child_count' => 2,
            'total_guest_count' => 18,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 2750000,
            'grand_total' => 2750000,
            'balance_due' => 1750000,
            'payment_status' => Booking::PAYMENT_DP,
        ]);
        Payment::query()->create([
            'booking_id' => $booking->id,
            'type' => Payment::TYPE_BOOKING_DP,
            'amount' => 1000000,
            'validated_by' => $superAdmin->id,
            'validated_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->patch(route('bookings.adjustments.update', $booking), [
                'discount_amount' => 0,
                'late_fee' => 0,
                'occupancy_adjustment_amount' => 300000,
                'occupancy_adjustment_note' => 'Tambahan penghuni villa besar',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'occupancy_adjustment_amount' => 300000,
            'occupancy_adjustment_note' => 'Tambahan penghuni villa besar',
            'grand_total' => 3050000,
            'paid_amount' => 1000000,
            'balance_due' => 2050000,
        ]);
    }

    public function test_internal_booking_detail_and_addon_master_pages_render(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Test',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-TEST-0004',
            'guest_name' => 'Tamu Test',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);
        AddonItem::query()->create([
            'name' => 'Extra Bed',
            'type' => AddonItem::TYPE_EXTRA_BED,
            'price' => 150000,
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->get(route('bookings.show', $booking))
            ->assertOk()
            ->assertSee('Layanan Tambahan (Add-ons)')
            ->assertSee('Penyesuaian Harga');

        $this->actingAs($superAdmin)
            ->get(route('addon-items.index'))
            ->assertOk()
            ->assertSee('Layanan Tambahan')
            ->assertSee('Extra Bed');
    }

    public function test_first_transfer_must_reach_minimum_dp_and_requires_active_bank(): void
    {
        Setting::query()->updateOrCreate(['key_name' => 'min_dp_percent'], ['value' => '50']);
        $room = Room::query()->create([
            'name' => 'Suite Cashless',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-CASHLESS',
            'guest_name' => 'Tamu Cashless',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);
        $bank = BankAccount::query()->create([
            'bank_name' => 'BCA',
            'account_number' => '123',
            'account_name' => 'PT Dafano Villa',
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->post(route('bookings.payments.store', $booking), [
                'amount' => 100000,
                'bank_account_id' => $bank->id,
                'transfer_reference' => 'DP-KECIL',
            ])
            ->assertSessionHasErrors('amount');

        $this->actingAs($superAdmin)
            ->post(route('bookings.payments.store', $booking), [
                'amount' => 250000,
                'transfer_reference' => 'TANPA-BANK',
            ])
            ->assertSessionHasErrors('bank_account_id');

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => Booking::PAYMENT_PENDING,
            'paid_amount' => 0,
        ]);
    }

    public function test_payment_validation_rejects_overbooking_and_duplicate_transfer_reference(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Conflict',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $checkIn = now()->addDay()->toDateString();
        $checkOut = now()->addDays(2)->toDateString();
        $confirmed = Booking::query()->create([
            'booking_code' => 'VLA-CONFIRMED',
            'guest_name' => 'Tamu Pertama',
            'guest_phone' => '628111111111',
            'room_id' => $room->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'paid_amount' => 250000,
            'balance_due' => 250000,
            'payment_status' => Booking::PAYMENT_DP,
        ]);
        $pending = Booking::query()->create([
            'booking_code' => 'VLA-PENDING-CONFLICT',
            'guest_name' => 'Tamu Kedua',
            'guest_phone' => '628222222222',
            'room_id' => $room->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);
        $bank = BankAccount::query()->create([
            'bank_name' => 'BCA',
            'account_number' => '123',
            'account_name' => 'PT Dafano Villa',
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        Payment::query()->create([
            'booking_id' => $confirmed->id,
            'type' => Payment::TYPE_BOOKING_DP,
            'amount' => 250000,
            'bank_account_id' => $bank->id,
            'transfer_reference' => 'REF-SUDAH-ADA',
            'validated_by' => $superAdmin->id,
            'validated_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->post(route('bookings.payments.store', $pending), [
                'amount' => 250000,
                'bank_account_id' => $bank->id,
                'transfer_reference' => 'REF-KONFLIK',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Transfer tercatat sebagai bermasalah dan tidak dianggap DP. Pilih pindah kamar/tanggal atau refund.');

        $this->assertDatabaseHas('payments', [
            'booking_id' => $pending->id,
            'type' => Payment::TYPE_TRANSFER_ISSUE,
            'amount' => 250000,
            'transfer_reference' => 'REF-KONFLIK',
            'resolution_status' => Payment::RESOLUTION_UNRESOLVED,
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $pending->id,
            'payment_status' => Booking::PAYMENT_PENDING,
            'paid_amount' => 0,
        ]);

        $otherRoom = Room::query()->create([
            'name' => 'Suite Other',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $otherBooking = Booking::query()->create([
            'booking_code' => 'VLA-DUPLICATE-REF',
            'guest_name' => 'Tamu Ketiga',
            'guest_phone' => '628333333333',
            'room_id' => $otherRoom->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'balance_due' => 500000,
        ]);

        $this->actingAs($superAdmin)
            ->post(route('bookings.payments.store', $otherBooking), [
                'amount' => 250000,
                'bank_account_id' => $bank->id,
                'transfer_reference' => 'REF-SUDAH-ADA',
            ])
            ->assertSessionHasErrors('transfer_reference');
    }

    public function test_super_admin_can_cancel_booking_with_refund_and_admin_can_mark_unit_ready(): void
    {
        $room = Room::query()->create([
            'name' => 'Suite Cancel Refund',
            'price' => 500000,
            'capacity' => 2,
            'status' => Room::STATUS_AVAILABLE,
            'is_active' => true,
        ]);
        $unit = $room->units()->first();
        $booking = Booking::query()->create([
            'booking_code' => 'VLA-CANCEL-REFUND',
            'guest_name' => 'Tamu Refund',
            'guest_phone' => '628123456789',
            'room_id' => $room->id,
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'total_room_price' => 500000,
            'grand_total' => 500000,
            'paid_amount' => 250000,
            'balance_due' => 250000,
            'payment_status' => Booking::PAYMENT_DP,
        ]);
        $booking->units()->attach($unit->id);
        $bank = BankAccount::query()->create([
            'bank_name' => 'BCA',
            'account_number' => '123',
            'account_name' => 'PT Dafano Villa',
            'is_active' => true,
        ]);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $admin = User::factory()->create(['role' => 'admin']);
        Payment::query()->create([
            'booking_id' => $booking->id,
            'type' => Payment::TYPE_BOOKING_DP,
            'amount' => 250000,
            'bank_account_id' => $bank->id,
            'transfer_reference' => 'REF-DP-CANCEL',
            'validated_by' => $superAdmin->id,
            'validated_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->post(route('bookings.cancel', $booking), [
                'cancellation_note' => 'Tamu membatalkan perjalanan.',
                'refund_amount' => 250000,
                'bank_account_id' => $bank->id,
                'transfer_reference' => 'REF-REFUND-CANCEL',
                'refund_note' => 'Dikembalikan penuh.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => Booking::STATUS_CANCELLED,
            'payment_status' => Booking::PAYMENT_CANCELLED,
            'paid_amount' => 0,
            'balance_due' => 0,
        ]);
        $this->assertDatabaseMissing('booking_room_unit', ['booking_id' => $booking->id]);
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'type' => Payment::TYPE_REFUND,
            'amount' => 250000,
            'transfer_reference' => 'REF-REFUND-CANCEL',
        ]);

        $unit->update(['status' => Room::STATUS_CLEANING]);
        $this->actingAs($admin)
            ->patch(route('room-units.status.update', $unit), ['status' => Room::STATUS_AVAILABLE])
            ->assertRedirect();
        $this->assertDatabaseHas('room_units', [
            'id' => $unit->id,
            'status' => Room::STATUS_AVAILABLE,
        ]);
    }
}
