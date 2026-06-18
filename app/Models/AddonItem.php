<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'type', 'category', 'price', 'is_active'])]
class AddonItem extends Model
{
    public const TYPE_FOOD = 'food';

    public const TYPE_EXTRA_BED = 'extrabed';

    public const CATEGORY_MAKANAN = 'makanan';

    public const CATEGORY_CAMILAN = 'camilan';

    public const CATEGORY_MINUMAN = 'minuman';

    public const CATEGORY_EXTRA_BED = 'extra_bed';

    /**
     * @return array<string, string>
     */
    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_MAKANAN => 'Makanan',
            self::CATEGORY_CAMILAN => 'Camilan',
            self::CATEGORY_MINUMAN => 'Minuman',
            self::CATEGORY_EXTRA_BED => 'Extra Bed',
        ];
    }

    public static function typeForCategory(string $category): string
    {
        return $category === self::CATEGORY_EXTRA_BED
            ? self::TYPE_EXTRA_BED
            : self::TYPE_FOOD;
    }

    public function categoryLabel(): string
    {
        return self::categoryOptions()[$this->category] ?? ucfirst((string) $this->category);
    }

    protected static function booted(): void
    {
        static::saving(function (AddonItem $addonItem): void {
            if (! $addonItem->category) {
                $addonItem->category = $addonItem->type === self::TYPE_EXTRA_BED
                    ? self::CATEGORY_EXTRA_BED
                    : self::CATEGORY_MAKANAN;
            }

            $addonItem->type = self::typeForCategory($addonItem->category);
        });
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
