<?php

namespace Tests\Feature;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomUnit;
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

    public function test_only_super_admin_can_add_guest_orders_and_totals_respect_dp(): void
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
                'type' => Payment::TYPE_BOOKING_DP,
                'bank_account_id' => $bankAccount->id,
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('bookings.addons.store', $booking), [
                'addon_item_id' => $addonItem->id,
                'qty' => 2,
            ])
            ->assertForbidden();

        $this->actingAs($superAdmin)
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

    public function test_invoice_pdf_requires_super_admin(): void
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

        $this->actingAs($admin)
            ->get(route('bookings.invoice', $booking))
            ->assertForbidden();

        $response = $this->actingAs($superAdmin)
            ->get(route('bookings.invoice', $booking));

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('invoice-dafano-villa-VLA-TEST-PDF.pdf', $response->headers->get('content-disposition'));
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
            ->assertSee('Daftar layanan tambahan berjalan')
            ->assertSee('Diskon dan denda keterlambatan');

        $this->actingAs($superAdmin)
            ->get(route('addon-items.index'))
            ->assertOk()
            ->assertSee('Layanan Tambahan')
            ->assertSee('Extra Bed');
    }
}
