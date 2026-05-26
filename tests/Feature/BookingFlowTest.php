<?php

namespace Tests\Feature;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Room;
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
        $response->assertSee('Tidak ada kamar tersedia');
        $response->assertDontSee('Suite Test');
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
                'type' => 'booking_dp',
                'bank_account_id' => $bankAccount->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => Booking::PAYMENT_DP,
            'paid_amount' => 250000,
            'balance_due' => 250000,
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
            'balance_due' => 500000,
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

        $this->actingAs($admin)
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
            'balance_due' => 570000,
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
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('booking_addons', [
            'id' => $addon->id,
            'payment_status' => BookingAddon::PAYMENT_PAID,
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'paid_amount' => 70000,
            'balance_due' => 500000,
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
        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'status' => Room::STATUS_CLEANING,
        ]);
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
            ->assertSee('Buat Booking Tamu')
            ->assertSee('Internal Suite')
            ->assertSee('Total 2 malam');

        $response = $this->actingAs($admin)
            ->post(route('bookings.store'), [
                'room_id' => $room->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'guest_name' => 'Tamu Dibantu Admin',
                'guest_phone' => '628123456789',
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
            ->assertSee('Running tab add-ons')
            ->assertSee('Diskon dan late fee');

        $this->actingAs($superAdmin)
            ->get(route('addon-items.index'))
            ->assertOk()
            ->assertSee('Master Add-ons')
            ->assertSee('Extra Bed');
    }
}
