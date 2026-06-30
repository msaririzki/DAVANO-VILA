<?php

namespace Database\Seeders;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PresentationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::query()->where('role', 'super_admin')->firstOrFail();
        $rooms = Room::query()->get()->keyBy(fn (Room $room): string => Str::lower($room->name));
        $commercial = $rooms->first(fn (Room $room): bool => Str::contains(Str::lower($room->name), 'commercial'));
        $superior = $rooms->first(fn (Room $room): bool => Str::contains(Str::lower($room->name), 'superior'));

        if (! $commercial || ! $superior) {
            $this->command?->error('Room COMMERCIAL VILLA dan SUPERIOR VILLA harus tersedia.');

            return;
        }

        $banks = BankAccount::query()->where('is_active', true)->get()->keyBy('bank_name');
        $addonItems = AddonItem::query()->where('is_active', true)->get()->keyBy('name');

        $scenarios = [
            [
                'code' => 'VLA-DEMO-001',
                'guest' => 'Nadia Pratama',
                'phone' => '628000000001',
                'source' => 'Instagram',
                'request' => 'Mohon kamar dengan pemandangan terbaik untuk menikmati sunrise.',
                'room' => $commercial,
                'check_in' => today()->addDays(3),
                'check_out' => today()->addDays(5),
                'adults' => 2,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_BOOKED,
                'hold' => 'active',
                'created_at' => now()->subMinutes(8),
            ],
            [
                'code' => 'VLA-DEMO-002',
                'guest' => 'Fajar Ramadhan',
                'phone' => '628000000002',
                'source' => 'Google',
                'request' => 'Perjalanan bulan madu, check-in diperkirakan pukul 15.00 WITA.',
                'room' => $commercial,
                'check_in' => today()->addDay(),
                'check_out' => today()->addDays(3),
                'adults' => 2,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_BOOKED,
                'payments' => [[Payment::TYPE_BOOKING_DP, 450000, 'Mandiri', 'DEMO-DP-002']],
                'created_at' => now()->subHours(2),
            ],
            [
                'code' => 'VLA-DEMO-003',
                'guest' => 'Ayu Lestari',
                'phone' => '628000000003',
                'source' => 'Friend',
                'request' => 'Tiba sekitar pukul 13.30 dan membawa satu anak.',
                'room' => $commercial,
                'check_in' => today(),
                'check_out' => today()->addDay(),
                'adults' => 2,
                'children' => 1,
                'units' => 1,
                'status' => Booking::STATUS_BOOKED,
                'payments' => [[Payment::TYPE_BOOKING_LUNAS, 450000, 'BCA', 'DEMO-LUNAS-003']],
                'created_at' => now()->subHours(5),
            ],
            [
                'code' => 'VLA-DEMO-004',
                'guest' => 'Rizky Maulana',
                'phone' => '628000000004',
                'source' => 'TikTok',
                'request' => 'Sarapan diantar pukul 07.00 sebelum trekking.',
                'room' => $commercial,
                'check_in' => today()->subDay(),
                'check_out' => today()->addDay(),
                'adults' => 2,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_IN_HOUSE,
                'addons' => [['Nasi Goreng', 2], ['Kopi', 2]],
                'payments' => [[Payment::TYPE_BOOKING_DP, 500000, 'BNI', 'DEMO-DP-004']],
                'assign_unit' => true,
                'created_at' => now()->subDays(2),
            ],
            [
                'code' => 'VLA-DEMO-005',
                'guest' => 'Keluarga Bapak Hendra',
                'phone' => '628000000005',
                'source' => 'Walk-in',
                'request' => 'Rombongan keluarga, membutuhkan tambahan extra bed dan air mineral.',
                'room' => $superior,
                'check_in' => today()->subDays(2),
                'check_out' => today(),
                'adults' => 10,
                'children' => 4,
                'units' => 1,
                'status' => Booking::STATUS_IN_HOUSE,
                'addons' => [['Extra Bed', 1], ['Mineral', 6]],
                'payments' => [[Payment::TYPE_BOOKING_LUNAS, 5680000, 'Mandiri', 'DEMO-LUNAS-005']],
                'assign_unit' => true,
                'created_at' => now()->subDays(3),
            ],
            [
                'code' => 'VLA-DEMO-006',
                'guest' => 'Dewi Anggraini',
                'phone' => '628000000006',
                'source' => 'Instagram',
                'request' => 'Pesanan untuk liburan singkat bersama sahabat.',
                'room' => $commercial,
                'check_in' => today()->subDays(5),
                'check_out' => today()->subDays(3),
                'adults' => 2,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_COMPLETED,
                'addons' => [['Kentang Goreng', 1], ['Teh', 2]],
                'payments' => [[Payment::TYPE_BOOKING_LUNAS, 940000, 'BCA', 'DEMO-LUNAS-006']],
                'created_at' => now()->subDays(6),
            ],
            [
                'code' => 'VLA-DEMO-007',
                'guest' => 'Komunitas Rinjani Explore',
                'phone' => '628000000007',
                'source' => 'Google',
                'request' => 'Rombongan wisata 12 orang, membutuhkan area berkumpul.',
                'room' => $superior,
                'check_in' => today()->subDays(9),
                'check_out' => today()->subDays(8),
                'adults' => 12,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_COMPLETED,
                'addons' => [['Nasi Goreng', 8], ['Kopi', 8]],
                'payments' => [[Payment::TYPE_BOOKING_LUNAS, 3030000, 'BNI', 'DEMO-LUNAS-007']],
                'created_at' => now()->subDays(10),
            ],
            [
                'code' => 'VLA-DEMO-008',
                'guest' => 'Muhammad Iqbal',
                'phone' => '628000000008',
                'source' => 'Instagram',
                'request' => 'Transfer dilakukan menjelang batas waktu pembayaran.',
                'room' => $commercial,
                'check_in' => today()->addDays(4),
                'check_out' => today()->addDays(6),
                'adults' => 2,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_BOOKED,
                'hold' => 'expired',
                'payments' => [[Payment::TYPE_TRANSFER_ISSUE, 450000, 'Mandiri', 'DEMO-LATE-008']],
                'created_at' => now()->subHours(4),
            ],
            [
                'code' => 'VLA-DEMO-009',
                'guest' => 'Siti Nurhaliza',
                'phone' => '628000000009',
                'source' => 'TikTok',
                'request' => 'Menunggu konfirmasi jadwal perjalanan dari rombongan.',
                'room' => $commercial,
                'check_in' => today()->addDays(2),
                'check_out' => today()->addDays(3),
                'adults' => 2,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_BOOKED,
                'hold' => 'expired',
                'created_at' => now()->subHours(6),
            ],
            [
                'code' => 'VLA-DEMO-010',
                'guest' => 'Andi Saputra',
                'phone' => '628000000010',
                'source' => 'Other',
                'request' => 'Dibatalkan karena perubahan jadwal penerbangan.',
                'room' => $commercial,
                'check_in' => today()->addDays(7),
                'check_out' => today()->addDays(8),
                'adults' => 2,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_CANCELLED,
                'payments' => [
                    [Payment::TYPE_BOOKING_DP, 450000, 'BCA', 'DEMO-DP-010'],
                    [Payment::TYPE_REFUND, 450000, 'BCA', 'DEMO-REFUND-010'],
                ],
                'created_at' => now()->subDays(2),
            ],
            [
                'code' => 'VLA-DEMO-011',
                'guest' => 'Putri Maharani',
                'phone' => '628000000011',
                'source' => 'Google',
                'request' => 'Tamu tidak dapat dihubungi pada hari kedatangan.',
                'room' => $commercial,
                'check_in' => today()->subDay(),
                'check_out' => today(),
                'adults' => 2,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_NO_SHOW,
                'created_at' => now()->subDays(2),
            ],
            [
                'code' => 'VLA-DEMO-012',
                'guest' => 'Bima & Anisa',
                'phone' => '628000000012',
                'source' => 'Friend',
                'request' => 'Mohon dekorasi sederhana untuk perayaan ulang tahun.',
                'room' => $commercial,
                'check_in' => today()->addDays(6),
                'check_out' => today()->addDays(8),
                'adults' => 2,
                'children' => 0,
                'units' => 1,
                'status' => Booking::STATUS_BOOKED,
                'addons' => [['Roti Bakar', 2], ['Teh', 2]],
                'payments' => [[Payment::TYPE_BOOKING_DP, 500000, 'Mandiri', 'DEMO-DP-012']],
                'created_at' => now()->subDay(),
            ],
        ];

        DB::transaction(function () use ($addonItems, $banks, $scenarios, $superAdmin): void {
            foreach ($scenarios as $scenario) {
                $this->seedScenario($scenario, $addonItems, $banks, $superAdmin);
            }
        });

        $this->command?->info('12 booking demo presentasi berhasil disiapkan.');
    }

    /** @param array<string, mixed> $scenario */
    private function seedScenario(array $scenario, $addonItems, $banks, User $superAdmin): void
    {
        /** @var Room $room */
        $room = $scenario['room'];
        $nights = max(1, $scenario['check_in']->diffInDays($scenario['check_out']));
        $roomTotal = (float) $room->price * $nights * $scenario['units'];
        $hold = $scenario['hold'] ?? null;

        $booking = Booking::query()->updateOrCreate(
            ['booking_code' => $scenario['code']],
            [
                'guest_name' => $scenario['guest'],
                'guest_phone' => $scenario['phone'],
                'acquisition_source' => $scenario['source'],
                'guest_request' => $scenario['request'],
                'adult_count' => $scenario['adults'],
                'child_count' => $scenario['children'],
                'total_guest_count' => $scenario['adults'] + $scenario['children'],
                'room_id' => $room->id,
                'unit_count' => $scenario['units'],
                'check_in_date' => $scenario['check_in'],
                'check_out_date' => $scenario['check_out'],
                'total_room_price' => $roomTotal,
                'total_addons_price' => 0,
                'occupancy_adjustment_amount' => 0,
                'late_fee' => 0,
                'discount_amount' => 0,
                'grand_total' => $roomTotal,
                'paid_amount' => 0,
                'balance_due' => $roomTotal,
                'payment_status' => Booking::PAYMENT_PENDING,
                'booking_status' => $scenario['status'],
                'payment_deadline_at' => $hold === 'active' ? now()->addMinutes(30) : ($hold === 'expired' ? now()->subMinutes(40) : null),
                'hold_expires_at' => $hold === 'active' ? now()->addHour() : ($hold === 'expired' ? now()->subMinutes(10) : null),
                'cancelled_at' => $scenario['status'] === Booking::STATUS_CANCELLED ? now()->subDay() : null,
                'cancellation_note' => $scenario['status'] === Booking::STATUS_CANCELLED ? $scenario['request'] : null,
            ],
        );

        $booking->payments()->delete();
        $booking->addons()->delete();
        $booking->units()->detach();

        foreach ($scenario['addons'] ?? [] as [$name, $qty]) {
            /** @var AddonItem $item */
            $item = $addonItems->get($name);
            $booking->addons()->create([
                'addon_item_id' => $item->id,
                'item_name' => $item->name,
                'type' => $item->type,
                'category' => $item->category,
                'qty' => $qty,
                'price' => $item->price,
                'subtotal' => (float) $item->price * $qty,
                'payment_status' => BookingAddon::PAYMENT_PAID,
            ]);
        }

        foreach ($scenario['payments'] ?? [] as $index => [$type, $amount, $bankName, $reference]) {
            /** @var BankAccount $bank */
            $bank = $banks->get($bankName);
            $booking->payments()->create([
                'type' => $type,
                'amount' => $amount,
                'bank_account_id' => $bank->id,
                'transfer_reference' => $reference,
                'validated_by' => $superAdmin->id,
                'validated_at' => $scenario['created_at']->copy()->addHours($index + 1),
                'note' => $type === Payment::TYPE_TRANSFER_ISSUE
                    ? 'Transfer ditemukan setelah batas waktu pembayaran publik berakhir.'
                    : 'Data simulasi presentasi.',
                'resolution_status' => $type === Payment::TYPE_TRANSFER_ISSUE
                    ? Payment::RESOLUTION_UNRESOLVED
                    : null,
            ]);
        }

        $booking->recalculateTotals();
        $booking->save();

        if ($scenario['assign_unit'] ?? false) {
            $unit = $room->units()->where('is_active', true)->orderBy('id')->first();
            if ($unit) {
                $booking->units()->sync([$unit->id]);
            }
        }

        $booking->timestamps = false;
        $booking->forceFill([
            'created_at' => $scenario['created_at'],
            'updated_at' => $scenario['created_at'],
        ])->save();
    }
}
