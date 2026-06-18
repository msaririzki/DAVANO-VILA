<?php

namespace Database\Seeders;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Room;
use App\Models\RoomUnit;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdmin = User::query()->firstOrCreate(
            ['email' => 'superadmin@dafanovilla.test'],
            [
                'name' => 'Super Admin Dafano',
                'password' => env('INITIAL_SUPERADMIN_PASSWORD', Str::password(32)),
                'role' => 'super_admin',
            ],
        );
        $superAdmin->forceFill([
            'name' => 'Super Admin Dafano',
            'role' => 'super_admin',
        ])->save();

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@dafanovilla.test'],
            [
                'name' => 'Admin Resepsionis',
                'password' => env('INITIAL_ADMIN_PASSWORD', Str::password(32)),
                'role' => 'admin',
            ],
        );
        $admin->forceFill([
            'name' => 'Admin Resepsionis',
            'role' => 'admin',
        ])->save();

        $commercialVilla = Room::query()->whereIn('name', ['COMMERCIAL VILLA', 'Villa Suite 1'])->first() ?? new Room;
        $commercialVilla->fill([
            'name' => 'COMMERCIAL VILLA',
            'description' => 'Kamar nyaman untuk pasangan atau perjalanan singkat dengan sarapan untuk 2 orang.',
            'price' => 450000,
            'capacity' => 2,
            'included_capacity' => 2,
            'max_capacity' => 2,
            'allow_unit_quantity' => true,
            'extra_guest_charge_mode' => 'manual',
            'facilities' => ['FREE BREAKFAST 2 ORANG', 'TV', 'KAMAR MANDI + WATER HEATER', 'FREE WIFI'],
            'status' => Room::STATUS_AVAILABLE,
            'image_path' => '/dafano-media/rooms/commercial-villa-current.jpg',
            'is_active' => true,
        ])->save();
        foreach (range(1, 6) as $number) {
            RoomUnit::query()->updateOrCreate(
                ['room_id' => $commercialVilla->id, 'name' => sprintf('Commercial Villa %02d', $number)],
                ['status' => Room::STATUS_AVAILABLE, 'is_active' => true],
            );
        }

        $superiorVilla = Room::query()->whereIn('name', ['SUPERIOR VILLA', 'Villa Suite 2'])->first() ?? new Room;
        $superiorVilla->fill([
            'name' => 'SUPERIOR VILLA',
            'description' => 'Villa luas untuk keluarga dengan ruang tamu, dapur, mushola indoor, dan sarapan untuk 4 orang.',
            'price' => 2750000,
            'capacity' => 20,
            'included_capacity' => 15,
            'max_capacity' => 20,
            'allow_unit_quantity' => false,
            'extra_guest_charge_mode' => 'manual',
            'capacity_rule_note' => 'Harga dasar termasuk sampai 15 orang. Jika lebih, biaya tambahan dikonfirmasi admin.',
            'facilities' => ['FREE BREAKFAST 4 ORANG', 'MUSHOLA INDOOR', 'DAPUR', 'RUANG TAMU', 'TV', 'KAMAR MANDI 2 + WATER HEATER', 'FREE WIFI'],
            'status' => Room::STATUS_AVAILABLE,
            'image_path' => '/dafano-media/rooms/superior-villa-current.jpg',
            'is_active' => true,
        ])->save();
        RoomUnit::query()->updateOrCreate(
            ['room_id' => $superiorVilla->id, 'name' => 'Villa Besar 01'],
            ['status' => Room::STATUS_AVAILABLE, 'is_active' => true],
        );

        foreach ([
            ['bank_name' => 'Mandiri', 'account_number' => '1610016660446', 'account_name' => 'PT DAFFAVANORAFFASYA'],
            ['bank_name' => 'BNI', 'account_number' => '1666730227', 'account_name' => 'CV DAFANO'],
            ['bank_name' => 'BCA', 'account_number' => '0562603148', 'account_name' => 'PT DAFFAVANORAFFASYA'],
        ] as $bankAccount) {
            BankAccount::query()->updateOrCreate(
                ['bank_name' => $bankAccount['bank_name']],
                [
                    'account_number' => $bankAccount['account_number'],
                    'account_name' => $bankAccount['account_name'],
                    'is_active' => true,
                ],
            );
        }

        foreach ([
            ['name' => 'Extra Bed', 'category' => AddonItem::CATEGORY_EXTRA_BED, 'price' => 150000],
            ['name' => 'Nasi Goreng', 'category' => AddonItem::CATEGORY_MAKANAN, 'price' => 25000],
            ['name' => 'Mie Goreng', 'category' => AddonItem::CATEGORY_MAKANAN, 'price' => 25000],
            ['name' => 'Pop Mie', 'category' => AddonItem::CATEGORY_MAKANAN, 'price' => 15000],
            ['name' => 'Mie Rebus', 'category' => AddonItem::CATEGORY_MAKANAN, 'price' => 15000],
            ['name' => 'Lalapan', 'category' => AddonItem::CATEGORY_MAKANAN, 'price' => 30000],
            ['name' => 'Roti Bakar', 'category' => AddonItem::CATEGORY_CAMILAN, 'price' => 15000],
            ['name' => 'Martabak Roti', 'category' => AddonItem::CATEGORY_CAMILAN, 'price' => 15000],
            ['name' => 'Kentang Goreng', 'category' => AddonItem::CATEGORY_CAMILAN, 'price' => 20000],
            ['name' => 'Teh', 'category' => AddonItem::CATEGORY_MINUMAN, 'price' => 10000],
            ['name' => 'Kopi', 'category' => AddonItem::CATEGORY_MINUMAN, 'price' => 10000],
            ['name' => 'Mineral', 'category' => AddonItem::CATEGORY_MINUMAN, 'price' => 5000],
        ] as $item) {
            AddonItem::query()->updateOrCreate(
                ['name' => $item['name']],
                [
                    'type' => AddonItem::typeForCategory($item['category']),
                    'category' => $item['category'],
                    'price' => $item['price'],
                    'is_active' => true,
                ],
            );
        }

        foreach ([
            'min_dp_percent' => '50',
            'hero_media_mode' => 'photos',
            'villa_whatsapp_number' => '6280000000000',
            'telegram_bot_token' => '',
            'telegram_chat_id' => '',
        ] as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key_name' => $key],
                ['value' => $value],
            );
        }
    }
}
