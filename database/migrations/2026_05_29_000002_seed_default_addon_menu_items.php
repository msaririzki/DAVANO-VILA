<?php

use App\Models\AddonItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->items() as $item) {
            DB::table('addon_items')->updateOrInsert(
                ['name' => $item['name']],
                [
                    'type' => AddonItem::typeForCategory($item['category']),
                    'category' => $item['category'],
                    'price' => $item['price'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('addon_items')
            ->whereIn('name', array_column($this->items(), 'name'))
            ->delete();
    }

    /**
     * @return array<int, array{name: string, category: string, price: int}>
     */
    private function items(): array
    {
        return [
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
        ];
    }
};
