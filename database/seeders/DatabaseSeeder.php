<?php

namespace Database\Seeders;

use App\Models\AddonItem;
use App\Models\BankAccount;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'superadmin@dafanovilla.test'],
            [
                'name' => 'Super Admin Dafano',
                'password' => 'password',
                'role' => 'super_admin',
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@dafanovilla.test'],
            [
                'name' => 'Admin Resepsionis',
                'password' => 'password',
                'role' => 'admin',
            ],
        );

        $commercialVilla = Room::query()->whereIn('name', ['COMMERCIAL VILLA', 'Villa Suite 1'])->first() ?? new Room;
        $commercialVilla->fill([
            'name' => 'COMMERCIAL VILLA',
            'description' => 'Kamar nyaman untuk pasangan atau perjalanan singkat dengan sarapan untuk 2 orang.',
            'price' => 450000,
            'capacity' => 2,
            'facilities' => ['FREE BREAKFAST 2 ORANG', 'TV', 'KAMAR MANDI + WATER HEATER', 'FREE WIFI'],
            'status' => Room::STATUS_AVAILABLE,
            'image_path' => '/dafano-media/rooms/commercial-villa-current.jpg',
            'is_active' => true,
        ])->save();

        $superiorVilla = Room::query()->whereIn('name', ['SUPERIOR VILLA', 'Villa Suite 2'])->first() ?? new Room;
        $superiorVilla->fill([
            'name' => 'SUPERIOR VILLA',
            'description' => 'Villa luas untuk keluarga dengan ruang tamu, dapur, mushola indoor, dan sarapan untuk 4 orang.',
            'price' => 2750000,
            'capacity' => 4,
            'facilities' => ['FREE BREAKFAST 4 ORANG', 'MUSHOLA INDOOR', 'DAPUR', 'RUANG TAMU', 'TV', 'KAMAR MANDI 2 + WATER HEATER', 'FREE WIFI'],
            'status' => Room::STATUS_AVAILABLE,
            'image_path' => '/dafano-media/rooms/superior-villa-current.jpg',
            'is_active' => true,
        ])->save();

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
            ['name' => 'Extra Bed', 'type' => AddonItem::TYPE_EXTRA_BED, 'price' => 150000],
            ['name' => 'Nasi Goreng Villa', 'type' => AddonItem::TYPE_FOOD, 'price' => 35000],
            ['name' => 'Ayam Lalapan', 'type' => AddonItem::TYPE_FOOD, 'price' => 45000],
            ['name' => 'Teh Hangat', 'type' => AddonItem::TYPE_FOOD, 'price' => 10000],
        ] as $item) {
            AddonItem::query()->updateOrCreate(
                ['name' => $item['name']],
                [
                    'type' => $item['type'],
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
